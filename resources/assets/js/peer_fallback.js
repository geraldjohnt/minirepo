var EventEmitter = require('eventemitter3');
var util = require('peerjs/lib/util');
var Socket = require('peerjs/lib/socket');

function Peerd(id, options) {
	if (!(this instanceof Peerd)) return new Peerd(id, options)
	EventEmitter.call(this);
	
	options = util.extend({
	    debug: 3, // 1: Errors, 2: Warnings, 3: All logs
	    host: util.CLOUD_HOST,
	    port: util.CLOUD_PORT,
	    key: 'peerjs',
	    path: '/',
	    token: util.randomToken(),
	    config: util.defaultConfig
	}, options)
	this.options = options
	this.id = id
	this._initServerConnection()
	this.socket.start(this.id, this.options.token)
}

util.inherits(Peerd, EventEmitter);

Peerd.prototype._handleMessage = function(msg) {
  var payload = msg.payload;
  var peer = msg.src;
  switch (msg.type) {
    case 'OPEN':  this.emit('open', this.id)
                  this.open = true;
                  break
    case 'ERROR': // Server error.
      		        this._abort('server-error', payload.msg)
                  break
    case 'ID-TAKEN': // The selected ID is taken.
                     this._abort('unavailable-id', 'ID `' + this.id + '` is taken')
                     break
    case 'INVALID-KEY': // The given API key cannot be found.
                          this._abort('invalid-key', 'API KEY "' + this.options.key + '" is invalid')
                          break
    case 'LEAVE': // Another peer has closed its connection to this peer.
                  util.log('Received leave message from', peer)
                  break
    case 'EXPIRE': // The offer sent to a peer has expired without response.
              if(typeof peer != 'undefined') {
                if(peer.indexOf('dc_') == -1) {
                  this._abort('peer-unavailable', 'Could not connect to peer ' + peer)
                } 
              }
    					break
    case 'OFFER': var connectionId = payload.connectionId;
                  if(payload.label == 'check peer') {
                    let pd = {
                      connectionId: this.id,
                      label: 'check peer',
                      metadata: util.browser,
                      data: typeof this.options.data != 'undefined' ? JSON.stringify(this.options.data) : '',
                      type: 'data',
                      browser: util.browser
                    }
                    this.socket.send({
                     type: 'OFFER',
                     payload: pd,
                     dst: peer
                    })
                  } else {
                    if (payload.type === 'data') {
                      this.emit('receive', payload)
                    }
                  }
                  break
    default: 
            break
    }
}

Peerd.prototype._initServerConnection = function () {
	var self = this
	this.socket = new Socket(this.options.secure, this.options.host, this.options.port, this.options.path, this.options.key)
	this.socket.on('message', function(data) {
	    self._handleMessage(data);
	});
	this.socket.on('error', function(error) {
		self._abort('socket-error', error);
	});
	this.socket.on('disconnected', function() {
	    // If we haven't explicitly disconnected, emit error and disconnect.
	    if (!self.disconnected) {
	      self.emitError('network', 'Lost connection to server.');
	      self.disconnect();
	    }
	});
	this.socket.on('close', function() {
	    // If we haven't explicitly disconnected, emit error.
	    if (!self.disconnected) {
	      self._abort('socket-closed', 'Underlying socket is already closed.');
	    }
	});
}

/** Emits a typed error message. */
Peerd.prototype.emitError = function(type, err) {
  if (typeof err === 'string') {
    err = new Error(err);
  }
  err.type = type;
  this.emit('error', err);
};

/**
 * Destroys the Peer and emits an error message.
 * The Peer is not destroyed if it's in a disconnected state, in which case
 * it retains its disconnected state and its existing connections.
 */
Peerd.prototype._abort = function(type, message) {
  if (!this._lastServerId) {
    this.destroy();
  } else {
    this.disconnect();
  }
  this.emitError(type, message);
};

/**
 * Disconnects the Peer's connection to the PeerServer. Does not close any
 *  active connections.
 * Warning: The peer can no longer create or accept connections after being
 *  disconnected. It also cannot reconnect to the server.
 */
Peerd.prototype.disconnect = function() {
  var self = this;
  util.setZeroTimeout(function(){
    if (!self.disconnected) {
      self.disconnected = true;
      self.open = false;
      if (self.socket) {
        self.socket.close();
      }
      self.emit('disconnected', self.id);
      self._lastServerId = self.id;
      self.id = null;
    }
  });
};

/**
 * Returns a DataConnection to the specified peer. See documentation for a
 * complete list of options.
 */
Peerd.prototype.send = function(peer, options) {
  if (this.disconnected) {
    util.warn('You cannot connect to a new Peer because you called ' +
      '.disconnect() on this Peer and ended your connection with the ' +
      'server. You can create a new Peer to reconnect, or call reconnect ' +
      'on this peer if you believe its ID to still be available.');
    this.emitError('disconnected', 'Cannot connect to new Peer after disconnecting from server.');
    return;
  }
  let payload = {
	connectionId: this.id,
	label: options.label,
	data: typeof options.data != 'undefined' ? JSON.stringify(options.data) : '',
	type: 'data',
	browser: util.browser
  }
  this.socket.send({
  	type: 'OFFER',
    payload,
    dst: peer
  })
  return payload;
};

/**
 * Destroys the Peer: closes all active connections as well as the connection
 *  to the server.
 * Warning: The peer can no longer create or accept connections after being
 *  destroyed.
 */
Peerd.prototype.destroy = function() {
  if (!this.destroyed) {
    this.disconnect();
    this.destroyed = true;
  }
};

module.exports = Peerd;

/** Override **/
/** Start up websocket communications. */
Socket.prototype._startWebSocket = function(id) {
console.log('loaded startwebsocket')
  var self = this;

  if (this._socket) {
    return;
  }

  this._socket = new WebSocket(this._wsUrl);

  this._socket.onmessage = function(event) {
    try {
      var data = JSON.parse(event.data);
    } catch(e) {
      util.log('Invalid server message', event.data);
      return;
    }
    self.emit('message', data);
  };

  // Take care of the queue of connections if necessary and make sure Peer knows
  // socket is open.
  this._socket.onopen = function() {
    if (self._timeout) {
      clearTimeout(self._timeout);
      setTimeout(function(){
        self._http.abort();
        self._http = null;
      }, 5000);
    }
    // if (util.supportsKeepAlive) {
    //   self._setWSTimeout();
    // }
    self._sendQueuedMessages();
    util.log('Socket open');
  };

  this._socket.onerror = function(err) {
    util.error('WS error code '+err.code);
  }

  // Fall back to XHR if WS closes
  this._socket.onclose = function(msg) {
      util.log("WS closed with code "+msg.code);
      self.disconnected = true;
      self.emit('disconnected');
  }
}