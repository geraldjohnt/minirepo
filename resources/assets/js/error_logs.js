export default { 
    error : [
        {
            "code" : 0, 
            "status" : "False Alarm",
            "msg" : "There is a function that need to fix. "
        },
        {
            "code" : 400, 
            "status" : "Bad Request",
            "msg" : "The request could not be understood by the server due to malformed syntax.\n The client SHOULD NOT repeat the request without modifications.\nThe server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing)"
        },
        {
            "code" : 401, 
            "status" : "Unauthorized",
            "msg" : "The user does not have the necessary credentials." 
        },
        {
            "code" : 402, 
            "status" : "Payment Required",
            "msg" : "The user does not have the necessary credentials." 
        },
        {
            "code" : 403, 
            "status" : "Forbidden",
            "msg" : "The request was valid, but the server is refusing action. The user might not have the necessary permissions for a resource, or may need an account of some sort." 
        },
        {
            "code" : 404, 
            "status" : "Not Found",
            "msg" : "The server has not found anything matching the Request-URI. No indication is given of whether the condition is temporary or permanent. \nThe 410 (Gone) status code SHOULD be used if the server knows, through some internally configurable mechanism, that an old resource is permanently unavailable and has no forwarding address. This status code is commonly used when the server does not wish to reveal exactly why the request has been refused, or when no other response is applicable.\nThe requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible." 
        },
        {
            "code" : 405, 
            "status" : "MethodNotAllowed",
            "msg" : "A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource." 
        },
        {
            "code" : 406, 
            "status" : "NotAcceptable",
            "msg" : "The requested resource is capable of generating only content not acceptable according to the Accept headers sent in the request." 
        },
        {
            "code" : 407, 
            "status" : "ProxyAuthenticationRequired",
            "msg" : "The client must first authenticate itself with the proxy." 
        },
        {
            "code" : 408, 
            "status" : "RequestTimeout",
            "msg" : "The server timed out waiting for the request. According to HTTP specifications: \"The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time.\"" 
        },
        {
            "code" : 409, 
            "status" : "Conflict",
            "msg" : "Indicates that the request could not be processed because of conflict in the current state of the resource, such as an edit conflict between multiple simultaneous updates." 
        },
        {
            "code" : 410, 
            "status" : "Gone",
            "msg" : "Indicates that the resource requested is no longer available and will not be available again. This should be used when a resource has been intentionally removed and the resource should be purged. Upon receiving a 410 status code, the client should not request the resource in the future. " 
        },
        {
            "code" : 411, 
            "status" : "LengthRequired",
            "msg" : "The request did not specify the length of its content, which is required by the requested resource." 
        },
        {
            "code" : 412, 
            "status" : "PreconditionFailed",
            "msg" : "The server does not meet one of the preconditions that the requester put on the request." 
        },
        {
            "code" : 413, 
            "status" : "PayloadTooLarge",
            "msg" : "The request is larger than the server is willing or able to process. Previously called \"Request Entity Too Large\"." 
        },
        {
            "code" : 414, 
            "status" : "URITooLong",
            "msg" : "The URI provided was too long for the server to process. Often the result of too much data being encoded as a query-string of a GET request, in which case it should be converted to a POST request." 
        },
        {
            "code" : 415, 
            "status" : "Unsupported Media Type",
            "msg" : "The request entity has a media type which the server or resource does not support. For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format. And also webrtc doesn't support Microsoft Edge And Internet Explorer." 
        },
        {
            "code" : 416, 
            "status" : "RangeNotSatisfiable",
            "msg" : "The client has asked for a portion of the file (byte serving), but the server cannot supply that portion. For example, if the client asked for a part of the file that lies beyond the end of the file. Called \"Requested Range Not Satisfiable\" previously." 
        },
        {
            "code" : 417, 
            "status" : "ExpectationFailed",
            "msg" : "The server cannot meet the requirements of the Expect request-header field." 
        },
        {
            "code" : 418, 
            "status" : "ImATeapot",
            "msg" : "The RFC specifies this code should be returned by teapots requested to brew coffee. This HTTP status is used as an Easter egg in some websites, including Google.com." 
        },
        {
            "code" : 421, 
            "status" : "MisdirectedRequest",
            "msg" : "The request was directed at a server that is not able to produce a response (for example because of connection reuse)." 
        },
        {
            "code" : 422, 
            "status" : "UnprocessableEntity",
            "msg" : "The request was well-formed but was unable to be followed due to semantic errors." 
        },
        {
            "code" : 423, 
            "status" : "Locked",
            "msg" : "The resource that is being accessed is locked." 
        },
        {
            "code" : 424, 
            "status" : "FailedDependency",
            "msg" : "The request failed because it depended on another request and that request failed (e.g., a PROPPATCH)" 
        },
        {
            "code" : 425, 
            "status" : "UnorderedCollection",
            "msg" : "" 
        },
        {
            "code" : 426, 
            "status" : "UpgradeRequired",
            "msg" : "The client should switch to a different protocol such as TLS/1.0, given in the Upgrade header field." 
        },
        {
            "code" : 428, 
            "status" : "PreconditionRequired",
            "msg" : "The origin server requires the request to be conditional. Intended to prevent the 'lost update' problem, where a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict." 
        },
        {
            "code" : 429, 
            "status" : "TooManyRequests",
            "msg" : "The user has sent too many requests in a given amount of time. Intended for use with rate-limiting schemes." 
        },
        {
            "code" : 431, 
            "status" : "RequestHeaderFieldsTooLarge",
            "msg" : "The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large." 
        },
        {
            "code" : 451, 
            "status" : "UnavailableForLegalReasons",
            "msg" : "A server operator has received a legal demand to deny access to a resource or to a set of resources that includes the requested resource." 
        },


        {
            "code" : 500,
            "status" :"Internal Server Error",
            "msg" : "\tThe server encountered an unexpected condition which prevented it from fulfilling the request. \n A generic error message, given when an unexpected condition was encountered and no more specific message is suitable."
        },
        {
            "code" : 501,
            "status" :"Not Implemented",
            "msg" : "The server either does not recognize the request method, or it lacks the ability to fulfil the request. Usually this implies future availability (e.g., a new feature of a web-service API)."
        },
        
        {
            "code" : 502,
            "status" :"Bad Gateway",
            "msg" : "The server was acting as a gateway or proxy and received an invalid response from the upstream server"
        },
        
        {
            "code" : 503,
            "status" :"Service Unavailable",
            "msg" : "The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state."
        },

        {
            "code" : 504 ,
            "status" : "Gateway Timeout",
            "msg" : "The server was acting as a gateway or proxy and did not receive a timely response from the upstream server."
        },

        {
           "code" : 505 ,
           "status" : "HTTP Version Not Supported",
           "msg" : "The server does not support the HTTP protocol version used in the request"
        },

        {
            "code" : 506 ,
            "status" : "Variant Also Negotiates",
            "msg" : "Transparent content negotiation for the request results in a circular reference"
        },

        {
            "code" : 507 ,
            "status" : "Insufficient Storage",
            "msg" : "The server is unable to store the representation needed to complete the request"
        },

        {
            "code" : 508 ,
            "status" : "Loop Detected",
            "msg" : "The server detected an infinite loop while processing the request "
        },

        {
            "code" : 510 ,
            "status" : "Not Extended ",
            "msg" : "Further extensions to the request are required for the server to fulfill it."
        },

        {
            "code" : 511 ,
            "status" : "Network Authentication Required",
            "msg" : "The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to control access to the network."
        }
        
    ]
}

