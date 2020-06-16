<template>
    <div class="peer-connect">
        <div class="row" v-show="showVideoCall">
            <div class="video-connections-feed" v-bind:class="[addClasses ? 'vid-max' : 'vid-min']"  v-el:peers-video-container>
                <div v-if="!mainCam.isSet">
                    <div v-for="connection in filterConnections" :key="connection.id">
                        <div v-if="togglePrimary" class="toggled_primarycam"></div>
                        <div class="col-xs-12 peers" :primaryVideo="primaryCam" v-if="connection.label == 'video-stream' && connection.open && videoEnabled && connection.id == primaryCam" v-el:peers-video-con>
                            <div v-if="emptyTrack(connection.src_object)" class="toggled_primarycam"></div>
                            <div class="video-container" v-else>
                                <ui-media :is-video="true" :src-object="{ stream: connection.src_object, videoId: connection.id}" :muted="true"></ui-media>
                            </div>
                        </div>
                        <div style="display: none;" :mainAudio="mainCam.id" v-if="connection.label == 'audio-stream' && connection.open && videoEnabled && !isUsingTemasys && connection.id == primaryCam">
                            <ui-media :is-video="false" :src-object="{ stream: connection.src_object, videoId: connection.id}" :muted="audio_mute"></ui-media>
                        </div>
                    </div>
                </div>
                <div v-else>
                        <div class="col-xs-12 peers" :mainVideo="mainCam.id" v-if="videoEnabled" v-el:peers-video-con>
                            <div v-if="togglePrimary || emptyTrack(mainCam.video)" class="toggled_primarycam"></div>
                            <div class="video-container" v-else>
                                <ui-media :is-video="true" :src-object="{ stream: mainCam.video, videoId: mainCam.id}" :muted="true"></ui-media>
                            </div>
                        </div>
                        <div style="display: none;" :mainAudio="mainCam.id" v-if="videoEnabled">  
                            <ui-media :is-video="false" :src-object="{ stream: mainCam.audio, videoId: mainCam.id}" :muted="audio_mute"></ui-media>
                        </div>
                </div>
            </div>
             <div class="col-xs-12 child-web-cams">
                <div class="video-user-feed own-feed">
                    <div v-if="toggleOwnCam || emptyTrack(video_src_object)" v-bind:style.sync="ownChildBorderStyle" class="toggled_primarycam"></div>
                    <div class="video-container"  v-bind:style.sync="ownChildBorderStyle"  v-el:self-video-con v-if="videoEnabled && (!toggleOwnCam || toggleOwnCam == 'false')">
                        <ui-media :is-video="true" :src-object="{ stream: video_src_object, videoId: 'self'}" :muted="true" v-ref:self-video></ui-media>
                    </div>
                </div>
                <div v-for="childcam in childCams" :key="childcam.id">
                    <div class="col-xs-3 video-second-childcam" id="child" v-bind:style.sync="childcam.style" :childCamID="childcam.id">
                        <div v-if="(!childcam.open || childcam.open == 'false') || emptyTrack(childcam.video)" class="toggled_primarycam"></div>
                        <div class="video-container">
                            <ui-media :is-video="true" :is-loading="!childcam.open || childcam.open == 'false'" :src-object="{ stream: childcam.video, videoId: childcam.id}" :muted="true" :put-class="true"></ui-media>
                        </div>
                        <div style="display: none;" :childAudio="childcam.id">  
                            <ui-media :is-video="false" :src-object="{ stream: childcam.audio, videoId: childcam.id}" :muted="audio_mute"></ui-media>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- new added html for camera switch -->
        <div class="col-md-12" id="MainSwitchCam" >

            
            <div class="webcam-switch">
               
                    <div class="btn-group btn-toggle" @click="toggleSelfCam"> 
                        <span class="switch-Text-label">ウェブカメラ</span>
                        <div class="switch-box">
                             <button class="btn btn-sm " v-bind:class="[isActive ? '' : 'btn-primary']">OFF</button>
                            <button class="btn btn-sm " v-bind:class="[isActive ? 'btn-primary' : '']">ON</button>
                        </div>
                    </div>
                     <!--  <a href="#" class="menu-btn" > -->
                    <span class="btn-menu" @click="footerMenu">
                        <i class="btn-icon"></i>
                    </span>
                <!-- </a> -->
             </div>
               

        </div>
        <!-- End new added html for camera switch -->
        <div class="controlPanel controlVid" v-show="showVideoCall">
            <!-- <ui-fab type="mini" class="screen-share" icon="screen_share" v-if="!is_screen_sharing" @click="screenShare"></ui-fab> -->
            <!-- <ui-fab type="mini" class="screen-share" icon="stop_screen_share" v-if="is_screen_sharing" @click="endScreenShare"></ui-fab> -->

            <!-- m2b-81 -->
            <!-- <ui-fab type="mini" class="screen-share tooltip" icon="stop_screen_share" tooltipPosition="top" tooltip="Click to close Screen Share" openTooltipOn="focus" v-if="is_screen_sharing && isScreensharedStaff" @click="screenShareStaffClose"> -->
            <!-- <ui-fab type="mini" class="screen-share screen-share-tooltip" id="staff-screenshare" icon="stop_screen_share" title="Click here to close Screen Share!" v-if="is_screen_sharing && isScreensharedStaff" @click="screenShareStaffClose"></ui-fab> -->
            <ui-fab type="mini" class="screen-share" icon="stop_screen_share" title="相手との画面共有を停止します。" v-if="is_screen_sharing && isScreensharedStaff" @click="screenShareStaffClose"></ui-fab>            
            <!-- m2b-81 -->
            <ui-fab type="mini" class="screen-share" icon="stop_screen_share" title="相手との画面共有を停止します。" v-if="is_screen_sharing && !isScreensharedStaff" @click="endScreenShare"></ui-fab>

            <ui-fab type="mini" class="audio-toggle" icon="volume_up" v-show="showAudioToggle" v-if="allow_audio && has_audio_stream && !audio_mute" @click="toggleAudioMute(true)"></ui-fab>
            <ui-fab type="mini" class="audio-toggle" icon="volume_off" v-show="showAudioToggle" v-if="allow_audio && has_audio_stream && audio_mute" @click="toggleAudioMute(false)"></ui-fab>
        </div>
    </div>

</template>
<script>
import _ from 'lodash'
import UiMedia from './UiMedia.vue';
import browser from '../../js/browser_detect.js';
import {getPeerKey, getPeerOptions, attachMediaStream_}  from '../../js/helpers.js';
import helper from '../../js/helpers.js';
import Cookie from 'js-cookie';
// m2b-81
import { UiModal, UiButton, UiTabs, UiTab, UiSnackbar, UiAlert, UiTooltip } from 'keen-ui';
// m2b-81
export default {
    name: 'peer-connect',
    data() {
        return {
            retrieveStream: 0,
            isUsingTemasys: false,
            addClasses:false,
            isActive: true,
            eventPrefix: 'peerconnect:',
            togglePrimary: false,
            toggleOwnCam: false,
            hideOwnCam: true,
            peer: null,
            dataCon: [],
            video_src_object: null,
            current_stream: null,
            screen_share_stream: null,
            connections: [],
            peer_key: null,
            screen: null,
            toggled_webcams: null,
            is_screen_sharing: false,
            existing_call: null,
            screenshare_con:{
                started: false,
                hasRemoved: false,
                id: ''
            },
            ctr: 0,
            localStream: null,
            call_closed_cb: null,
            audio_mute: false,
            allow_audio: false,
            has_audio_stream: false,
            ownChildCams : this.childCams,
            ownChildBorderStyle : {
                position: 'relative !important',
                zIndex: '10 !important',
                float:'right',
                // maxWidth: '25% !important',
                display: 'inline-block',
                border: '',
                width:''
            },
            ChildCamBorderStyle : [],
            setColor: '',
            primaryPointer: '',
            mybrowserName: '',
            screen_recording_stream: null,
            isScreensharedStaff: false,
            isRecordingStarted: false,
            isStopRecordingStaff: false,
            showScreenShareToolTip: false,
            showtooltip: true            
        }
    },
    methods: {
        
        openPeer(isUsingPlugin = false) {
            if (navigator.userAgent.indexOf('Android') > 0) {
                let body = document.getElementsByTagName('body')[0];
                body.classList.add('android-phone');
            } else if (navigator.userAgent.indexOf('iPhone') > 0) {
                let body = document.getElementsByTagName('body')[0];
                body.classList.add('iphone-phone');
            } else if (navigator.userAgent.indexOf('iPad') > 0 || 
            navigator.userAgent.indexOf('iPod') > 0) {
                let body = document.getElementsByTagName('body')[0];
                body.classList.add('tablet-mobile');
            } else {
                let body = document.getElementsByTagName('body')[0];
                body.classList.add('non-mobile');
            }
            
            try {
                // let peer_options = {}
                let peer_config = { iceTransportPolicy: "all", 'iceServers': [
                    { url: "stun:stun.webrtc.ecl.ntt.com:3478", urls: "stun:stun.webrtc.ecl.ntt.com:3478" }
                ]}
                // this.screen = ScreenShare.create({debug: false});
                // peer_options = getPeerOptions(this.peerHost, this.apiKey, peer_config)
                let peer_options = getPeerOptions(this.peerHost, this.apiKey, peer_config)
                peer_options.RtpDataChannels = true;
                this.peer = new Peer(this.peerKey, peer_options)
                this.peer.on('open', (id, connectionId) => {
                    this.isUsingTemasys = isUsingPlugin
                    this.$dispatch(this.eventPrefix+'peer:pluginChecker', isUsingPlugin)
                    //get active peer ids this.listAllPeers()
                    if(this.initStream) {
                        this.getUserMedia()
                        // this.initializeOwnAudioStream()
                        if(!isUsingPlugin) {
                            this.getUserMedia()
                            // this.initializeOwnAudioStream()
                        }
                        this.initializeOwnVideoStream(id);
                    }
                })
                // this.isActive = Cookie.get('video_enabled') == 'true' ? true : false
                this.isActive = Cookie.get('video_toggled') == 'true' ? true : false
                this.peer.on('call', (call) => {
                    // Answer the call automatically (instead of prompting user)
                    // let lstream = call.metadata == 'audio' ? window.localAudioStream : window.localStream
                    call.answer(this.video_src_object)
                    this.callToPeer(call)
                    this.$dispatch(this.eventPrefix+'peer:call', this, call)
                })

                this.peer.on('connection', (conn) => {
                    /** Stop the setup if it only needs to check peer */
                    if(conn.label == "check peer") return;
                    let label = 'meeting'
                    setTimeout(function() {
                        this.attachDataConnectionEvents(conn, label)
                    }.bind(this), 100)
                    this.$dispatch(this.eventPrefix+'peer:connection', this, conn)
                })

                this.peer.on('close', (id) => {
                    this.$dispatch(this.eventPrefix+'peer:closed', id)
                })

                this.peer.on('error', (err) => {
                    //connect again with a new peerKey
                    if(err.type == 'unavailable-id' || err.type == 'network') {
                        if(err.type == 'unavailable-id') {
                            this.peerKey = getPeerKey(this.peerKeyPrefix)
                            this.peer.destroy()
                        }
                        this.$nextTick( () => {
                            // this.openPeer(isUsingPlugin)
                        })
                    }
                    this.$dispatch(this.eventPrefix+'peer:error', this, err)
                })
            } catch(e) {
                if(e.message.search('Peer is not defined') > -1) {
                    setTimeout(function() { this.openPeer(isUsingPlugin); }.bind(this), 1000);
                    return;
                }

                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Opening a peer'}
                let functionName = 'PeerConnect:openPeer';
                helper.catchError(errorStats, functionName);
            }
        },
        getUserMedia() {
            try {
                //NOT_INCLUDED
                navigator.getUserMedia = navigator.getUserMedia || 
                        navigator.webkitGetUserMedia ||
                        navigator.mozGetUserMedia ||
                        navigator.msGetUserMedia || navigator.mediaDevices.getUserMedia
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Setting the navigator.getUserMedia'}
                let functionName = 'PeerConnect:getUserMedia';
                helper.catchError(errorStats, functionName);
            }
        },
        initializeOwnVideoAfterScreenShare(id, cb){
            try {
                let textExp = new RegExp('iPhone', 'i');
                let constraints = {
                    audio: true,
                    video:{
                        width: { ideal: 4096 },
                        height: { ideal: 2160 }
                    }
                };

                // Iphone Devices doesn't support giving video constraints
                if(!textExp.test(navigator.userAgent)) {
                    constraints = {
                        audio: true, 
                        video:{ 
                            width: 450,
                            height: 240
                            
                        }
                    };
                }
                navigator.getUserMedia(constraints, (stream) => {
                    // Set your video displays
                    // this.current_stream = stream
                    this.video_src_object = stream
                    // window.localStream = stream
                    // window.localAudioStream = stream
                    this.allow_audio = true
                    if(typeof cb === 'function') {
                        cb()
                    }
                }, () => {

                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Initializing own video stream after screen share'}
                let functionName = 'PeerConnect:initializeOwnVideoAfterScreenShare';
                helper.catchError(errorStats, functionName);
            }
        },
        initializeOwnVideoStream(id, cb) {
            try {
                var textExp = new RegExp('iPhone', 'i');
                var constraints = { audio: true, video: true };

                // Iphone Devices doesn't support giving video constraints
                if(!textExp.test(navigator.userAgent)) {
                    constraints = {
                        audio: true, 
                        video:{ 
                            width: 320,
                            height: 240
                            
                        }
                    };
                }
                navigator.getUserMedia(constraints, (stream) => {
                    // Set your video displays
                    // this.current_stream = stream
                    this.video_src_object = stream
                    // window.localStream = stream
                    // window.localAudioStream = stream
                    this.allow_audio = true
                    if(typeof cb === 'function') {
                        cb()
                    }
                    this.$dispatch(this.eventPrefix+'peer:open', this, id)
                }, () => { 
                    // if(this.retrieveStream > 2) {
                    //     this.retrieveStream++;
                    //     this.initializeOwnVideoStream(id, cb)
                    //     return;
                    // }
                    // Commented this one since the plugin automatically redirects the page if there is NO DEVICE
                    // if(AdapterJS.webrtcDetectedBrowser == 'IE') {
                    //     this.$dispatch(this.eventPrefix+'peer:nodevice');
                    //     return;
                    // }

                    let audioTrack = this.createEmptyAudioTrack();
                    let videoTrack = this.createEmptyVideoTrack({ width:10, height:10 });
                    let mediaStream = new MediaStream([audioTrack, videoTrack]);

                        /** Forced Set */
                        mediaStream.addTrack(this.createEmptyVideoTrack({ width:10, height:10 }))

                        // this.current_stream = mediaStream
                        this.video_src_object = mediaStream
                        // window.localStream = mediaStream
                        // window.localAudioStream = mediaStream
                        this.$dispatch(this.eventPrefix+'peer:open', this, id)
                    })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Initializing Own video stream'}
                let functionName = 'PeerConnect:initializeOwnVideoStream';
                helper.catchError(errorStats, functionName);
            }

            // navigator.mediaDevices.getUserMedia({video: true, audio: false})
            // .then(stream => { 
            //     /* use stream here */
            //     this.current_stream = stream
            //     this.video_src_object = stream
            //     window.localStream = stream
            //     if(typeof cb === 'function') {
            //         cb()
            //     }
            //     this.$dispatch(this.eventPrefix+'peer:open', this, id)
            // }).catch(error => {
            //      this.$dispatch(this.eventPrefix+'peer:open', this, id)
            // });
        },
        createEmptyAudioTrack() {
            try {
                let AudioContext = window.AudioContext || window.webkitAudioContext;
                const ctx = new AudioContext();
                const oscillator = ctx.createOscillator();
                const dst = oscillator.connect(ctx.createMediaStreamDestination());
                oscillator.start();
                const track = dst.stream.getAudioTracks()[0];
                return Object.assign(track, { enabled: false });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Creating empty audio track'}
                let functionName = 'PeerConnect:createEmptyAudioTrack';
                helper.catchError(errorStats, functionName);
            }
        },
        createEmptyVideoTrack({ width, height }) {
            try { 
                const canvas = Object.assign(document.createElement('canvas'), { width, height });
                canvas.getContext('2d').fillRect(0, 0, width, height);
                const stream = canvas.captureStream();
                const track = stream.getVideoTracks()[0];
                return Object.assign(track, { enabled: false });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Creating empty video track'}
                let functionName = 'PeerConnect:createEmptyVideoTrack';
                helper.catchError(errorStats, functionName);
            }
        },
        emptyTrack(stream, caller = '') {
            try {
                return stream.getVideoTracks().length > 1
            } catch(e) {
                return true
            }
        },
        toggleSelfCam(){
            try { 
                this.isActive = this.isActive ? false : true
                // let isActive = this.isActive
                this.$dispatch(this.eventPrefix+'peer:toggle:cam', this.isActive)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling self camera'}
                let functionName = 'PeerConnect:toggleSelfCam';
                helper.catchError(errorStats, functionName);
            }
        },
        footerMenu() {
            try {
                var fileViewer = document.querySelector(".fileViewer");
                this.$parent.$refs.staffDashboardFooterNav.footer = this.$parent.$refs.staffDashboardFooterNav.footer ? false : true;
                if(this.$parent.$refs.staffDashboardFooterNav.footer){
                    if(window.innerWidth <= 767){
                      if(fileViewer.hasAttribute("style")){
                        fileViewer.removeAttribute("style")
                        fileViewer.setAttribute("style","margin-bottom:47px !important;");
                      }else{
                        fileViewer.setAttribute("style","margin-bottom:47px !important;");
                      }
                        //.querySelector('.vid-min').style.minHeight = window.innerHeight - 51 + 'px';
                            document.querySelector('.webcam-switch').style.bottom = 0;
                        document.querySelector('.vid-min').style.marginBottom = '-50px';
                    if(navigator.userAgent.indexOf('Android') > 0){
                       document.querySelector('.vid-min').style.marginBottom = '-110px';
                    }
                    }
                } else {
                    if(window.innerWidth <= 767){
                      if(fileViewer.hasAttribute("style")){
                        fileViewer.removeAttribute("style")
                        fileViewer.setAttribute("style","margin-bottom:65px !important;");
                      }else{
                        fileViewer.setAttribute("style","margin-bottom:65px !important;");
                      }
                        //document.querySelector('.vid-min').style.minHeight = window.innerHeight - 120 + 'px';
                        document.querySelector('.webcam-switch').style.bottom = '69px';
                        document.querySelector('.vid-min').style.marginBottom = '-120px';
                            if(navigator.userAgent.indexOf('Android') > 0){
                                document.querySelector('.vid-min').style.marginBottom = '-180px';
                            }      
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Placement of the Footer Menu'}
                let functionName = 'PeerConnect:footerMenu';
                helper.catchError(errorStats, functionName);
            }
        },

        toggleElement(el) {
            try { 
                // let has_el = helper.hasClass(el, 'hide')
                if (helper.hasClass(el, 'hide')) {
                    el.classList.remove('hide')
                } else {
                    el.classList.add('hide')
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle of Elements'}
                let functionName = 'PeerConnect:toggleElement';
                helper.catchError(errorStats, functionName);
            }
        },
        // initializeOwnAudioStream() {
            // navigator.getUserMedia({ audio: true, video: false }, (stream) => {
            //     window.localAudioStream = stream
            //     this.allow_audio = true
            // }, () => { 
            // })
            // navigator.mediaDevices.getUserMedia({video: false, audio: true})
            // .then(stream => { 
            //     /* use stream here */
            //     window.localAudioStream = stream
            //     this.allow_audio = true
            // }).catch(error => {
            // });
        // },
        initiateCall(passed_peer_key, is_audio, cb) {
            try {
                if(this.checkIfAllowMultiple()) {
                    let peer_key = typeof passed_peer_key != 'undefined' ? passed_peer_key : this.peer_key
                    this.$nextTick( () => {
                        if(typeof this.video_src_object != 'undefined') {
                            // let lstream = is_audio ? window.localAudioStream : window.localStream
                            let call = this.peer.call(peer_key, this.video_src_object)
                            if(typeof call != 'undefined' && is_audio) {
                                call.metadata = 'audio'
                            }
                            this.callToPeer(call)
                        } else {
                            if(typeof cb == 'function') {
                                cb()
                            }
                        }
                    })
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Initiating a call'}
                let functionName = 'PeerConnect:initiateCall';
                helper.catchError(errorStats, functionName);
            }
        },
        callToPeer(call, attach) {
            try {
                if(typeof call == 'undefined') return false
                // temporary commented, for screen sharing
                // if (this.checkIsConnected(call)) {
                //     call.close();
                // }
                // Wait for stream on the call, then set peer video display
                let vm = this
                call.on('stream', (stream) => {
                    let s_label = call.metadata == 'audio' ? 'audio-stream' : 'video-stream' 
                    let connection = {
                        id: call.peer,
                        src_object: stream,
                        label: s_label,
                        type: call.type,
                        connection: call,
                        uuid: call.id,
                        open: true
                    }
                    this.$parent.$els.shareScreenCon.srcObject = null;
                    if(call.metadata == 'screen' && typeof attach =='undefined') {
                        this.screen_share_stream = call.remoteStream
                        if(typeof this.$parent.$els.shareScreenCon != 'undefined') {
                            // For IE only since video tag is replaced with object
                            if(AdapterJS.webrtcDetectedBrowser == 'IE') {
                                var elem = document.getElementById('shareScreenCon');
                                attachMediaStream_(elem,stream)
                            } else {
                                attachMediaStream_(this.$parent.$els.shareScreenCon,stream)
                            }
                        }
                        vm.handleResize();
                        vm.$dispatch('peerconnect:peer:screen:loaded', vm)
                    } else {
                        this.screen_share_stream = null;
                    }
                    // Commented because it conflicts with multiple connections
                    // if(call.metadata != 'audio') {
                    //     //change open status whenever there are new media connections
                    //     for ( var key in this.connections ) {
                    //         if(this.connections[key].type === 'media' && this.connections[key].label === 'video-stream') {
                    //             this.connections[key].open = false
                    //         }
                    //     }    
                    // } else {
                    //     this.has_audio_stream = true
                    // }
                    this.has_audio_stream = true
                    let match_conn = vm.getMatchedConn(vm.connections, { id: call.remoteId, type: call.type, label: s_label })
                    if(!match_conn && call.metadata != 'screen') {
                        this.connections.push(connection)
                        let tmpConnection = {
                            id: call.peer,
                            src_object: stream,
                            label: 'audio-stream',
                            type: call.type,
                            connection: call,
                            uuid: call.id,
                            open: true
                        }

                        this.connections.push(tmpConnection)
                    }

                    if(connection.label == 'video-stream') {
                        /** Primary Camera Setup */
                        let camStatus = 'showCamera' + Cookie.get('peerId');
                        let showCamera = (Cookie.get(camStatus) != undefined && s_label == 'video-stream') ? Cookie.get(camStatus) : true
                        if(showCamera == 'false') {
                            setTimeout(() => {
                                this.hideParentWebCam();
                            }, 3000);
                        }
                    }
                })
                call.on('close', function() {
                    let msg = 'call to peer'
                    vm.onCloseConnection(vm, this, msg)
                    // vm.$dispatch(vm.eventPrefix+'peer:call:close', vm)
                    vm.$nextTick( () => {
                        if(call.metadata == 'screen'){
                                vm.$dispatch(vm.eventPrefix+'screenshare:endclient')
                                vm.screenshare_con.started = false
                                vm.screenshare_con.hasRemoved = false
                                vm.screenshare_con.id = ''
                            }
                    })
                })
                if(call.metadata != 'audio' && call.metadata != 'screen') {
                    this.existing_call = call
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Calling to Peer'}
                let functionName = 'PeerConnect:callToPeer';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleTracks() {
            try { 
                if(this.existing_call == null) return;
                if(!!this.video_src_object) {
                    this.video_src_object.getTracks().forEach(track => track.stop())
                }
                this.video_src_object = null;

                var vm = this;
                navigator.getUserMedia({ audio: true, video: true }, (stream) => {
                    this.video_src_object = stream
                    // Updating the Child Camera
                    for(let key in this.peer.connections) {
                        let conn = this.peer.connections[key];
                        for(let connKey in conn) {
                            if(conn[connKey].type == 'media') {
                                conn[connKey].replaceStream(stream)
                            }
                        }
                    }

                    // Updating the main connection
                    for(let key in vm.connections){
                        if(vm.connections[key].type == 'media') {
                            vm.connections[key].connection.replaceStream(stream)
                        }
                    }
                }, () => {
                    /** do nothing */
                });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Tracks'}
                let functionName = 'PeerConnect:toggleTracks';
                helper.catchError(errorStats, functionName);
            }
        },
        onCloseConnection(vm, conn,msg) {
            try { 
                // Temporarily commented to really remove all the connections including data
                let b_conn = (conn.label == 'meeting' && conn.type == 'data')
                if(!b_conn) {
                    let o_conn = vm.getMatchedConn(vm.connections, { id: conn.peer, type: conn.type, uuid: conn.id })
                    if(typeof o_conn != 'undefined') {
                        let index = vm.connections.indexOf(o_conn)
                        let stopM = vm.connections[index].src_object;

                        setTimeout(function () {
                            if(typeof stopM != 'undefined' && stopM != null && stopM != '') {
                                stopM.getTracks().forEach(function(track) {
                                    track.enabled = false;
                                    track.stop();
                                });
                            }
                        }, 3000);

                        let holder = vm.connections.splice(index, 1)
                        if(conn.metadata == 'screen') {
                            this.screen_share_stream = null
                        }
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Executes a series of processes on close connection'}
                let functionName = 'PeerConnect:onCloseConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        endChildConnections(childID){
            try { 
                let vm = this
                for (let key in vm.connections){
                    let o_conn = vm.getMatchedConn(vm.connections, { id: childID })
                    if(typeof o_conn != 'undefined'){
                        let index = vm.connections.indexOf(o_conn)
                        vm.connections.splice(index, 1)
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Removing Child Camera'}
                let functionName = 'PeerConnect:endChildConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        reconnectVideoConnection(primaryid){
            try { 
                // let vm = this
                // let o_conn = vm.getMatchedConn(vm.connections, { id: primaryid, label: 'video-stream' })
                // let index = vm.connections.indexOf(o_conn)
                // vm.connections.splice(index, 1)
                // let call = this.peer.call(primaryid, this.video_src_object)
                // this.callToPeer(call)
                this.$parent.$els.shareScreenCon.srcObject = null;
                this.screen_share_stream = false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Reconnecting Video Connection after Screenshare'}
                let functionName = 'PeerConnect:reconnectVideoConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        checkMediaConnection(){
            // console.log(this.connections)
        },
        // togglePeerWebcam(){
        togglePeerWebcam(status){
            try { 
                this.togglePrimary = (status) ? false : true;
                let vm = this
                let o_conn = vm.getMatchedConn(vm.connections, {label:'meeting', type: 'data'})

                if(!!o_conn) {
                    o_conn.open = status
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling Peer Webcam'}
                let functionName = 'PeerConnect:togglePeerWebcam';
                helper.catchError(errorStats, functionName);
            }
        },
        togglePrimaryCatch(){
            try {
                let primary = this.togglePrimary
                return primary
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Returning the status of the Primary Camera'}
                let functionName = 'PeerConnect:togglePrimaryCatch';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleStaffCatch(){
            try {
                let staff = !this.isActive
                return staff
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Returning the camera status of the staff'}
                let functionName = 'PeerConnect:toggleStaffCatch';
                helper.catchError(errorStats, functionName);
            }
        },
        hideParentWebCam(){
            this.togglePrimary = true
        },
        getStaffPeerKey(){
            try {
                return this.peerKey
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Returning the peer key'}
                let functionName = 'PeerConnect:getStaffPeerKey';
                helper.catchError(errorStats, functionName);
            }
        },
        getStaffDataConnection(){
            try { 
                return this.getMatchedConn(this.connections, {label:'meeting', type: 'data'})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting the staff data connection'}
                let functionName = 'PeerConnect:getStaffDataConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleOwnWebCam(){
            try { 
                this.toggleOwnCam = (this.toggleOwnCam) ? false : true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling own web cam'}
                let functionName = 'PeerConnect:toggleOwnWebCam';
                helper.catchError(errorStats, functionName);
            }
        },
        hideOwnWebCam(){
            try { 
                this.toggleOwnCam = true;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Hiding own webcam'}
                let functionName = 'PeerConnect:hideOwnWebCam';
                helper.catchError(errorStats, functionName);
            }
        },
        checkIsConnected(conn) {
            try { 
                if(typeof conn == 'undefined') return false
                var matched_conns = this.getMatchedConn(this.connections, { id: conn.peer, type: conn.type, label: conn.label })
                var provider_matched_conns = this.getMatchedConn(conn.provider.connections, { id: conn.peer, type: conn.type, label: conn.label })
                if(!this.checkIfAllowMultiple(conn.label)) {
                    if(provider_matched_conns) {
                        return true
                    }
                }
                return typeof matched_conns == 'undefined' ? false : matched_conns
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Returning a boolean which determine if user is connected in a meeting'}
                let functionName = 'PeerConnect:checkIsConnected';
                helper.catchError(errorStats, functionName);
            }
        },
        checkIfAllowMultiple(label) {
            try {
                let matched_conns = this.getMatchedConn(this.connections, { type: 'media', label })
                matched_conns = typeof matched_conns != 'undefined' ? matched_conns.length : 0
                if(this.allowMultipleVideoCall && matched_conns || !matched_conns) {
                    return true
                }
                return false
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Returns a boolean which determines if system allows multiple connection'}
                let functionName = 'PeerConnect:checkIfAllowMultiple';
                helper.catchError(errorStats, functionName);
            }
        },
        connectToPeer(peer_key, label) {
            try {
                var self = this
                var peerReady = window.setInterval(function(){
                    try {
                        if(!!self.peer) {
                            clearInterval(peerReady);
                             let conn = self.peer.connect(peer_key, {
                                label: label,
                                serialization: 'json',
                                reliable: false
                            })
                            setTimeout(function() {
                                self.attachDataConnectionEvents(conn, label)
                            }, 100)
                        }
                    } catch(e) { /** do nothing */}
                }, 100)
                // if (this.checkIsConnected(conn)) {
                //     conn.close();
                // }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Connecting to Peer'}
                let functionName = 'PeerConnect:connectToPeer';
                helper.catchError(errorStats, functionName);
            }
        },
        getMatchedConn(connections, obj) {
            try {
                return _.find(connections, obj)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Returning matched connections'}
                let functionName = 'PeerConnect:getMatchedConn';
                helper.catchError(errorStats, functionName);
            }
        },
        getDataConn(label) {
            try { 
                return _.find(this.connections,{ label, type: 'data' })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Returns data connection'}
                let functionName = 'PeerConnect:getDataConn';
                helper.catchError(errorStats, functionName);
            }
        },
        sessionIDsetColor(data){
            try {
                const colors = ['#282828','#333333','green','red']
                const pointersColor = ['#333333','green','red']
                let sessionId = data
                // for (let key = 0; key < sessionId.length; key++) {
                for (let key in sessionId) {
                    if (this.peer.id == sessionId[key] || this.peer.id == 'staff' + sessionId[key]) {
                        this.ownChildBorderStyle.border = '2px solid '+colors[key];
                    }
                }

                this.childcamSet(data,colors,this.childCams)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Assigning border color for each users'}
                let functionName = 'PeerConnect:sessionIDsetColor';
                helper.catchError(errorStats, functionName);
            }
        },
        childcamSet(data,color,childcam){
            try {
                if (!!childcam) {
                    for (let child in childcam) {
                        if (childcam.length >= 1){
                            for (let key in data) {
                                if (data[key] == childcam[child].id){
                                    childcam[child].style.border = '2px solid '+color[key] 
                                }
                            }
                        }
                    }
                }

            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Assigning a border color for child camera'}
                let functionName = 'PeerConnect:childcamSet';
                helper.catchError(errorStats, functionName);
            }
             
        },
        addColor(conn, label) {
            try {
                const colors = ['#333333','green','red']
                let connection = {
                                id: conn.peer,
                                src: '',
                                src_object: '',
                                label,
                                type: conn.type,
                                connection: conn,
                                uuid: conn.id,
                                color: '',
                                open: true
                        }
                if(!!conn.peer){
                    let length = this.dataCon.length
                    connection.color = colors[length]
                    this.connections.push(connection)
                    this.dataCon.push(connection)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Adding a color for every connection'}
                let functionName = 'PeerConnect:addColor';
                helper.catchError(errorStats, functionName);
            }
            // for (let cnt in this.connections){
            //     for(let key in this.dataCon){
            //         if(this.dataCon[key].id == this.connections[cnt].id){
            //             this.connections[cnt].color = colors[key]
            //         }
            //     }
                
            // }
            // let colors = ['white','green','red']
            // // let childCameraId =[]
            
            // // this.connection.id = this.dataCon[key].id 
            // // this.cameraObj.color = colors[ctr]
            // this.connection[this.ctr].color = colors[this.ctr]
            // this.ctr++
            // // this.connections.forEach(function(element){
            // //     for (let cnt in this.cameraObj){
            // //         if(this.cameraObj[cnt].id)
            // //     }
            // // })
        
        },
        addDataConnection(conn, label) {
            try {
                let is_connected = this.getMatchedConn(this.connections, {id: conn.peer, label:'meeting', type: 'data'})
                if(!is_connected) {
                    this.addColor(conn, label)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Adding data connection'}
                let functionName = 'PeerConnect:addDataConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        attachDataConnectionEvents(conn, label) {
            try {
                let vm = this
                let tc = conn
                conn.on('open', function() {
                    /** Stop the setup if it only needs to check peer */
                    if(conn.label == "check peer") {
                        vm.$dispatch(vm.eventPrefix+'peer:conn:open', vm, this)
                        return;
                    }

                    vm.addDataConnection(this, label)
                    setTimeout(function() { vm.$dispatch(vm.eventPrefix+'peer:conn:open', vm, this) }.bind(this), 1000);
                    conn.on('data', function(data){
                        if(data.key == 'forceCall') {
                            vm.initiateCall(data.data)
                        } else if(data.key == 'secondClientConnect') {
                            setTimeout(function() {
                                vm.$dispatch(vm.eventPrefix+'peer:conn:data', vm, data, this)
                            }.bind(this), 100)
                        } else {
                            vm.$dispatch(vm.eventPrefix+'peer:conn:data', vm, data, this)
                        }
                    })
                })
                conn.on('close', function() {
                    if(tc.open == false) {
                        let info = {
                            key: 'endingChild',
                            data: tc.remoteId
                        }
                        vm.$dispatch(vm.eventPrefix+'peer:conn:data', vm, info, this)
                    }
                    // let msg = 'attach data connection events'
                    // vm.onCloseConnection(vm, this, msg)
                    // vm.$dispatch(vm.eventPrefix+'peer:conn:close', vm)
                })
                conn.on('error', function(err) {
                    // vm.$dispatch(vm.eventPrefix+'peer:conn:error', vm, err)
                })
                conn.on('fallbackData', function(data) {
                    let is_connected = vm.getMatchedConn(vm.connections, {id: this.peer, label:'meeting', type: 'data'})
                    if(!is_connected) {
                        vm.addDataConnection(this, label)
                        vm.$dispatch(vm.eventPrefix+'peer:conn:open', vm, this);
                    }

                    if(data.key == 'forceCall') {
                        vm.initiateCall(data.data)
                    } else if(data.key == 'secondClientConnect') {
                        setTimeout(function() {
                            vm.$dispatch(vm.eventPrefix+'peer:conn:data', vm, data, this)
                        }, 100)
                    } else {
                        vm.$dispatch(vm.eventPrefix+'peer:conn:data', vm, data, this)
                    }
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Attaching data connection'}
                let functionName = 'PeerConnect:attachDataConnectionEvents';
                helper.catchError(errorStats, functionName);
            }
        },

       // m2b-81
       screenShareStaffRecord(){
            try {
                let vm = this;
                let chromeConstraints = { 
                    video :{
                        width: this.$parent.$els.shareScreenCon.width,
                        height: this.$parent.$els.shareScreenCon.height,
                        frameRate:10
                    }
                }
                let firefoxConstraints = window.constraints = {
                    audio: false, video: { 
                        mediaSource: 'screen' ,
                        width: this.$parent.$els.shareScreenCon.width,
                        height: this.$parent.$els.shareScreenCon.height,
                        frameRate:{
                            min: "5",
                            max: "10"
                        } }
                }
                // debugger;
                this.is_screen_sharing = false;
                let constraints = this.mybrowserName == 'Chrome' ? chromeConstraints : this.mybrowserName == 'Firefox' ? firefoxConstraints : {};
                let navigate = this.mybrowserName == 'Chrome' ? navigator.mediaDevices.getDisplayMedia(constraints) : this.mybrowserName == 'Firefox' ? navigator.mediaDevices.getUserMedia(constraints) : navigator.mediaDevices.getDisplayMedia(chromeConstraints) ;
                let stream = navigate.then(stream => {   
                    // this.screen_sharing_stream = stream; 
                    this.screen_recording_stream = stream;                     
                    // this.video_src_object = this.screen_recording_stream
                    this.isScreensharedStaff = true;
                    this.isRecordingStarted = true;
                    // console.log('PASS HERE 1 ....... ', this.video_src_object);


                    if(this.screen_recording_stream) {
                        this.screen_recording_stream.getVideoTracks()[0].addEventListener('ended', () => {
                            // m2b-81
                            // console.log('screenShareStaffRecord - Recording has been stopped.');                                                
                            // alert('localStream ended - screenShareStaff')
                            this.is_screen_sharing = false;
                            this.isScreensharedStaff = false;  
                            // m2b-81
                            // this.afterScreenshare()                            
                            
                            if(this.existing_call) {
                                let peer_key = this.existing_call.peer
                                this.$dispatch(this.eventPrefix+'staffRecording:end', peer_key)                                  
                            }                            

                        });
                    }  

                })
                .catch(error => {
                    if(error.name == 'NotReadableError') {
                        this.screenshareCloseCall()
                        this.revertShare()
                        alert('Hardware Error')
                        this.$dispatch(this.eventPrefix+'screenshare:error', this, error)
                    } else if(error == 'DOMException') {
                        this.$dispatch(this.eventPrefix+'staffRecording:end', 'nostream')
                    }
                }, () => {
                    this.revertShare()
                    alert('ScreenShareを終了しました');
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Starting a screen sharing'}
                let functionName = 'PeerConnect:screenShare';
                helper.catchError(errorStats, functionName);
            }            
        },
        screenShareStaff() {
            try {
                let vm=this;
                this.screenshare_con.started = true
                vm.$dispatch(this.eventPrefix+'screenshare:start', true)
                this.is_screen_sharing = true
                this.video_src_object = this.screen_recording_stream
                let callPeer = () => {
                    for(let key in vm.dataCon){
                        let conn = vm.getMatchedConn(vm.connections, {label:'meeting', type: 'data',id:vm.dataCon[key].id})
                        if(typeof conn != 'undefined'){
                            let peer_key = conn.id
                            let options = {
                                metadata: 'screen'
                            }  
                            let call = this.peer.call(typeof conn != 'undefined' ? conn.id : peer_key, this.video_src_object, options)
                            // console.log('call: ', call);
                            this.callToPeer(call, false)
                        }
                    }
                }
                callPeer()
                if(this.video_src_object) {
                    this.video_src_object.getVideoTracks()[0].addEventListener('ended', () => {
                        // m2b-81
                        // alert('localStream ended - screenShareStaff')
                        this.is_screen_sharing = false;
                        this.isScreensharedStaff = false;                          
                        // m2b-81
                        this.afterScreenshare()
                    });
                }                

            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Starting a screen sharing'}
                let functionName = 'PeerConnect:screenShare';
                helper.catchError(errorStats, functionName);
            }
        },        
        screenShareStaffClose() {
            // console.log('Closing screen share');
            this.$parent.$els.shareScreenCon.srcObject = null;
            this.video_src_object = null;
            this.is_screen_sharing = false
            if(this.existing_call) {
                // console.log('Closing screen share -- this.existing_call');
                let peer_key = this.existing_call.peer
                this.$dispatch(this.eventPrefix+'screenshare:end', peer_key)
                this.video_src_object = this.existing_call.localStream
            }
        },        
        // m2b-81  
        screenShare() {
            try {
                // document.querySelector('.screen-share-tooltip').style.display = 'none';

                let vm=this;
                var elem = document.getElementById('shareScreenCon')
                let chromeConstraints = { 
                    video :{
                        width: elem.width, //this.$parent.$els.shareScreenCon.width,
                        height: elem.height, //this.$parent.$els.shareScreenCon.height,
                        frameRate:10
                    }
                }
                let firefoxConstraints = window.constraints = {
                    audio: false, video: { 
                        mediaSource: 'screen' ,
                        width: elem.width, //this.$parent.$els.shareScreenCon.width,
                        height: elem.height, //this.$parent.$els.shareScreenCon.height,
                        frameRate:{
                            min: "5",
                            max: "10"
                        } }
                }
                this.is_screen_sharing = false;
                let constraints = this.mybrowserName == 'Chrome' ? chromeConstraints : this.mybrowserName == 'Firefox' ? firefoxConstraints : {};
                let navigate = this.mybrowserName == 'Chrome' ? navigator.mediaDevices.getDisplayMedia(constraints) : this.mybrowserName == 'Firefox' ? navigator.mediaDevices.getUserMedia(constraints) : navigator.mediaDevices.getDisplayMedia(chromeConstraints) ;
                let stream = navigate.then(stream => {
                    this.screenshare_con.started = true
                    vm.$dispatch(this.eventPrefix+'screenshare:start', true)
                    this.is_screen_sharing = true
                    this.video_src_object = stream
                    // window.localStream = stream
                    /** Pause Existing Call Video */
                    // if(this.existing_call && typeof this.existing_call.localStream != 'undefined') {
                    //     this.existing_call.localStream.getVideoTracks().forEach(track => track.enabled = false)
                    // }
                    let callPeer = () => {
                        for(let key in vm.dataCon){
                            let conn = vm.getMatchedConn(vm.connections, {label:'meeting', type: 'data',id:vm.dataCon[key].id})
                            if(typeof conn != 'undefined'){
                            let peer_key = conn.id
                            let options = {
                                metadata: 'screen'
                            }  
                            let call = this.peer.call(typeof conn != 'undefined' ? conn.id : peer_key, stream, options)
                            this.callToPeer(call, false)
                            }
                        }
                    }
                    callPeer()
                    if(this.video_src_object) {
                        // this.screenshareCallPeer = callPeer
                        // this.call_closed_cb = callPeer
                        this.video_src_object.getVideoTracks()[0].addEventListener('ended', () => {
                            // alert('localStream ended')
                            // m2b-81
                            this.is_screen_sharing = false;
                            // m2b-81
                            this.afterScreenshare()
                            // this.revertShare()
                            // this.$dispatch(this.eventPrefix+'peer:conn:open', this, this)
                        });
                        // this.$nextTick( () => {
                        //     // console.log('calling screenshare')
                        //     this.screenshareCallPeer()
                        // })
                    }
                    // else {
                    //     callPeer()
                    // }
                    // 
                })
                .catch(error => {
                    if(error.name == 'NotReadableError') {
                        this.screenshareCloseCall()
                        this.revertShare()
                        alert('Hardware Error')
                        this.$dispatch(this.eventPrefix+'screenshare:error', this, error)
                    }
                }, () => {
                    this.revertShare()
                    alert('ScreenShareを終了しました');
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Starting a screen sharing'}
                let functionName = 'PeerConnect:screenShare';
                helper.catchError(errorStats, functionName);
            }
        },
        endScreenShare(){
            if(this.video_src_object) {
                this.video_src_object.getTracks().forEach(track => track.stop())
                this.afterScreenshare()
            }
        },
        afterScreenshare(){
            this.$parent.$els.shareScreenCon.srcObject = null;
            this.is_screen_sharing = false
            // this.video_src_object = null
            // if(this.video_src_object) {
            
            this.revertShare()

        },
        screenshareCloseCall() {
            try {
                let vm = this
                if(vm.existing_call) {
                    vm.existing_call.on('close', function() {
                        // vm.onCloseConnection(vm, this)
                        if(typeof vm.call_closed_cb === 'function') {
                            vm.call_closed_cb()
                        }
                    })
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Screenshare close call'}
                let functionName = 'PeerConnect:screenshareCloseCall';
                helper.catchError(errorStats, functionName);
            }
        },
        stopCurrentStream() {
            try {
                let camStatus = 'showCamera' + this.peer.id;
                Cookie.remove(camStatus);
                this.screenshareCloseCall()
                if(this.existing_call) {
                    if(typeof this.existing_call.localStream != 'undefined') {
                        this.existing_call.localStream.getTracks()[0].stop()
                        this.existing_call.localStream.getTracks().forEach(track => track.stop())
                    }
                    this.existing_call.close()
                    if(!this.video_src_object) {
                        this.$nextTick( () => {
                            this.existing_call = null
                        })
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Stopping Current Stream'}
                let functionName = 'PeerConnect:stopCurrentStream';
                helper.catchError(errorStats, functionName);
            }
        },
        revertShare() {
            try { 
                // let vm = this
                // 
                    //         console.log('dispatch:::screenshare:end')
                    //         this.$dispatch(this.eventPrefix+'screenshare:end', peer_key)
                    //     }
                    //     // this.initializeOwnVideoAfterScreenShare(this.peer.id, cb)
                    //     if(this.existing_call) {
                    //         if(typeof this.existing_call.localStream != 'undefined') {
                    //             // window.localStream = this.existing_call.localStream
                    //             // window.localAudioStream = this.existing_call.localStream
                    //             this.video_src_object = this.existing_call.localStream
                    //         }
                    //     }
                    // }
                    // this.$nextTick( () => {
                    //     this.stopCurrentStream()
                    // })
                // } else {
                    // let conn = this.getMatchedConn(this.connections, {label:'meeting', type: 'data'})
                    // if(typeof conn != 'undefined'){
                    //     conn.connection.send({key: 'forceCall', data: this.peer.id })
                    // }
                    // this.stopCurrentStream()
                // }
                if(this.existing_call) {
                    let peer_key = this.existing_call.peer
                    this.$dispatch(this.eventPrefix+'screenshare:end', peer_key)
                    if(typeof this.existing_call.localStream != 'undefined') {
                        // this.existing_call.localStream.getTracks().forEach(track => track.enabled = true)
                            this.video_src_object = this.existing_call.localStream
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Reverting screenshare'}
                let functionName = 'PeerConnect:revertShare';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleAudioMute(audio_mute) {
            try {
                let vm = this;
                for(var x=0; x < vm.connections.length; x++) {
                    let holder = vm.connections[x];
                    if(holder.label != 'meeting') {
                        holder.src_object.getAudioTracks().forEach(function(track) {
                            track.enabled = !audio_mute
                        })
                    }
                }

                this.$dispatch(this.eventPrefix+'audio:mute', this, !this.audio_mute)
                this.audio_mute = typeof audio_mute != 'undefined' ? audio_mute : !this.audio_mute
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling audio mute'}
                let functionName = 'PeerConnect:toggleAudioMute';
                helper.catchError(errorStats, functionName);
            }
        },
        handleResize() {
            try {
                let width = document.documentElement.clientWidth
                let height = document.documentElement.clientHeight;
                try {
                    if(AdapterJS && AdapterJS.webrtcDetectedBrowser == 'IE') { height = height - 100; }
                } catch(e) { /** do nothing */}

                var elem = document.getElementById('shareScreenCon');
                if(elem) {
                    elem.width = width;
                    elem.height = height;
                }
                // if(this.$parent) {
                //     if(typeof this.$parent.$els.shareScreenCon != 'undefined') {
                //         this.$parent.$els.shareScreenCon.width = width
                //         this.$parent.$els.shareScreenCon.height = height
                //     }
                // }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Handling of the resizing'}
                let functionName = 'PeerConnect:handleResize';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    ready() {
        window.addEventListener('resize', this.handleResize);
        this.mybrowserName = browser.specs().browser.name;
        this.handleResize()
    },
    props: {
        apiKey: {
            type: String,
            default: ''
        },
        peerHost: {
            type: String,
            default: ''
        },
        peerKeyPrefix: {
            type: String,
            default: ''
        },
        peerKey: {
            type: String,
            default: () => {
                return getPeerKey()
            }
        },
        showVideoCall: {
            type: Boolean,
            default: true
        },
        videoEnabled: {
            type: Boolean,
            default: true
        },
        allowMultipleVideoCall: {
            type: Boolean,
            default: true
        },
        initStream: {
            type: Boolean,
            default: true
        },
        showAudioToggle: {
            type: Boolean,
            default: false
        },
        isExpanded: {
            type: Boolean,
            default: false  
        },
        mainCam: {
            id: '',
            video: '',
            audio: '',
            isSet: false
        },
        videoUserFeed: {
            position: 'relative !important',
            zIndex: '10 !important',
            float:'right',
            maxWidth: '25%',
            display: 'inline-block',
            transform: 'rotateY(180deg)',
            border: '',
        },
        primaryCam: '',
        childCams: {},
        clients: [],  
    },
    computed: {
        ownchildcamera : function(){
            try { 
                if(!!this.childCams){
                    return this.childCams
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Returning child cameras'}
                let functionName = 'PeerConnect:ownchildcamera';
                helper.catchError(errorStats, functionName);
            }
        },
        filterConnections: function(){
            return this.connections
        }
    },
    watch: {
        clients: function (value) {
        },
        isActive: function (value) {
            let id = (!!this.peer.id) ? this.peer.id.match(/([A-Za-z]+)([0-9]+)/) : false;
            if (!!id){
                if (id[1] == 'staff') {
                    Cookie.set('video_toggled', value)
                } 
            }
        },
        childCams: function(value){

        },
        togglePrimary: function(value) {
            this.togglePrimary = value
        }
    },
    created() {
        if(this.peerKeyPrefix) {
            this.peerKey = getPeerKey(this.peerKeyPrefix)
        }
        let vm = this;
        var adapterReady = window.setInterval(function(){
            try {
                if(!!AdapterJS) {
                    clearInterval(adapterReady);
                    AdapterJS.webRTCReady(function(isUsingPlugin) {
                        setTimeout(function() {
                            vm.openPeer(isUsingPlugin);
                        }, 100)
                    });
                }
            } catch(e) { /** do nothing */}
        }, 100)
    },
    components: {
        UiMedia,
        UiTooltip        
    }
}
</script>
<style lang="scss">

.peer-connect .toggled_primarycam {
    width: 100%;
    height: 100%;
    z-index: 10;
    position: absolute;
    background-color: rgb(0, 0, 0);
    background-image:url('image/video.png');
    background-repeat: no-repeat;
    // background-size: auto;
    background-position: center;
}

.peer-connect .video-connections-feed {
    background-color: black;
}

.peer-connect .child-web-cams .toggled_primarycam {
    background-size: 30px;
}

.peer-connect .toggled_primarycam.zindex-notprio {
    z-index: -1;
}

.peer-connect .toggled_primarycam.borderless {
    border: 0px solid transparent!important;
}

.child-web-cams{
    position: absolute;
    bottom: 0;
    right:0;
    width:100%;
}
.minified-cams{
    width: 15% !important;
}
.own-feed{
    position: absolute !important;
}
.peer-connect {
    .video-container {
        width: 100%;
        overflow: hidden;

        video, object {
            width: 100%
        }
    } 
    // video.secondChildCont {
    // height: 90px !important;
    // object-fit: cover;
    // zoom: 100%;
    // }  
    .video-user-feed{
        position: relative !important;
        z-index: 10 !important;
        float: right;
        max-width: 25% !important;
        height: 100%;
        transform: rotateY(180deg);
        background: black;
    }
}

.webcam-switch{
   background: #333333;
   height: 4.5rem;
   padding: 10px 25px;
   z-index: 15;
   position: relative;
}
.ui-fab.color-default .ui-fab-icon {
    color: #333333;
}
.btn-default{
    background:blue;
}
.btn-primary{
    background:#333333 !important;
    border-radius: 20px!important;
    color:#fff!important;
}
.switch-Text-label{
    color: #fff;
    font-weight: 800;
    margin-right: 1rem;
    padding-top: 10px;
    font-size: 16px;
}
.switch-box{    
    background: #e4e6e9;
    border-radius: 20px;
    padding: 2px;
    height: 33.2px;
    .btn{
        background:#e4e6e9;
        border-radius: 20px;
        color:#333333 ;
        width:50px;
        height: 29px;
        padding: 3px;   
    }
}
.btn-toggle{
    display: inline-flex;
}


</style>
