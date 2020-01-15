<template>
    <div class="ui-media">
        <div v-if="isVideo">
            <div v-show="srcObject && !isConnecting">
                <video :id="uniqueId" v-el:self-video :src="src" :muted="muted" autoplay playsinline v-bind:class="[putClass ? 'secondChildCont' : '', videoPoster ? 'video_poster' : '', isConnecting ? 'video_connecting' : '']"></video>
            </div>
            <div v-if="isConnecting" class="col-md-12 loader-wrapper">
                <video src="" muted="true" autoplay="false" playsinline class="video_connecting"></video>
                <div class="custom-loader"></div>
                <div class="loader-mask"></div>
            </div>
        </div>
        <audio :id="uniqueId" v-el:self-audio v-if="!isVideo" :src="src" :muted="muted" autoplay playsinline></audio>
    </div>
</template>

<script>
export default {
    data() {
        return {
            uniqueId: null,
            videoPoster: false,
            isConnecting: true,
        }
    },
    name: 'ui-media',
    props: {
        src: {
            type: String,
            default: ''
        },
        srcObject: {
            type: Object,
            default: {
                stream: null
            }
        },
        isVideo: {
            type: Boolean,
            default: true
        },
        muted: {
            type: Boolean,
            default: true
        },
        putClass: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        setSrcObject() {
            if(!!this.srcObject) {
                if(this.isVideo && !!this.srcObject.stream) {
                    try {
                        // console.log('value of the stream::::', this.srcObject.stream)
                        if(AdapterJS.webrtcDetectedBrowser != "IE") {
                            // console.log('assigning through srcObject')
                            this.$els.selfVideo.srcObject = this.srcObject.stream
                        } else {
                            var video = document.querySelector('#' + this.uniqueId);
                            video = attachMediaStream(video, this.srcObject.stream);
                            // this.$els.selfVideo.src = window.URL.createObjectURL(this.srcObject.stream)
                        }
                    } catch(e) { /** do nothing */}

                    try {
                        // console.log('VIDEO TRACKS:::', this.srcObject.stream.getVideoTracks())
                        if(this.srcObject.stream.getVideoTracks().length == 0) {
                            this.videoPoster = true;
                        } else {
                            this.videoPoster = this.srcObject.stream.getVideoTracks().length > 1
                        }
                    } catch(e) {
                        // console.log('something went wrong:::', e)
                        this.videoPoster = true;
                    }

                    if(!this.videoPoster) {
                        let cntInterval = 0;
                        var checker = window.setInterval(function(){
                            try {
                                cntInterval++;
                                // console.log('show me the video details', this.$els.selfVideo)
                                if (!!this.$els.selfVideo && this.$els.selfVideo.duration == "Infinity") {
                                    this.isConnecting = false
                                    clearInterval(checker)
                                } else if (!!this.$els.selfVideo && typeof this.$els.selfVideo) {
                                    this.isConnecting = false
                                    clearInterval(checker)
                                }
                                // console.log('count interval:::', cntInterval)
                                /** Limit Checking of Camera status up to 20 seconds */
                                if(cntInterval > 30) {
                                    this.isConnecting = false;
                                    this.videoPoster = true;
                                    clearInterval(checker)
                                }
                            } catch(e) { /** do nothing */}
                        }.bind(this), 1000);
                    } else {
                        this.isConnecting = false
                    }
                } else if(!this.isVideo && !!this.srcObject.stream) {
                    if(AdapterJS.webrtcDetectedBrowser != "IE") {
                        this.$els.selfAudio.srcObject = this.srcObject.stream
                    } else {
                        var video = document.querySelector('#' + this.uniqueId);
                        video = attachMediaStream(video, this.srcObject.stream);
                    }
                }
            }
        },
    },
    ready() {
        this.setSrcObject()
    },
    created() {
        if(!!this.srcObject.videoId) {
            this.uniqueId = this.isVideo ? 'video_' : 'audio_';
            this.uniqueId += this.srcObject.videoId
        }
    },
    watch: {
        srcObject: function (value) {
            this.isConnecting = true
            this.setSrcObject()
        }
    }
}
</script>

<style lang="sass">
.video-connections-feed .ui-media {
    video.video_poster, object.video_poster {
        background-color: black;
        background-image: url('image/video.png');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 13.5%;
    }
}

.child-web-cams .ui-media {
    video.video_poster, object.video_poster{
        background-color: black;
        background-image: url('image/video.png');
        background-repeat: no-repeat;
        background-size: 30px;
        background-position: center;
    }
}

.peer-connect .child-web-cams .loader-wrapper .custom-loader {
    width: 25px;
    height: 25px;
    left: 40%;
    top: 38%;
}

.peer-connect .ui-media .video_connecting {
    background-color: #686868;
}

@media (min-width: 768px) and (max-width: 1200px) {
    .peer-connect .video-connections-feed .video-container {
        .loader-wrapper video, .loader-wrapper object {
            height: 450px;
        }
    }

    .peer-connect .child-web-cams .video-container .loader-wrapper {
        .custom-loader {
            top: 23%;
        }
    }
}
</style>