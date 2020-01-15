<template>
    <div class="protoCon peerCon">
        <div class="protoConnectContainer">
            <div class="wrapper noToolbar">
                <div class="pointer" v-el:pointer>
                    <ui-icon icon="pan_tool"></ui-icon>
                </div>

                <div class="overlay" v-if="main.loading && !invalidMeetingKey">
                    <ui-progress-circular
                        :show="main.loading" color="white" :value="main.progress"
                    ></ui-progress-circular>
                </div>

                <div class="overlay row  middle-xs center-xs blurredBg" v-if="invalidMeetingKey">
                    <div class="meetingMsg">
                        <span>Invalid Meeting Key.</span>
                    </div>
                </div>

                <div class="row blurredBg noSelect" v-el:peer-container v-show="!main.loading && !invalidMeetingKey">
                    <div class="videoFeedCon withAttachFab" v-el:vid-share :disabled="vidShare.disabled" v-show="video_src">
                        <div class="peer-connect">
                            <div class="video-connections-feed" v-el:peers-video-container>
                                <div class="video-container">
                                    <video :src="video_src" muted="true" autoplay v-el:self-video v-show="showVideoStream"></video>
                                    <canvas v-el:self-canvas width="200" height="124toda"></canvas>
                                </div>
                            </div>

                            <div class="controlPanel">
                                <ui-fab type="mini" class="screen-share" icon="screen_share" v-if="!is_screen_sharing" @click="screenShare"></ui-fab>
                                <ui-fab type="mini" class="screen-share" icon="stop_screen_share" v-if="is_screen_sharing" @click="revertShare"></ui-fab>
                            </div>
                        </div>

                        <ui-fab type="mini" icon="zoom_out_map" @click="expandVidShare"></ui-fab>
                    </div>
                    <!-- <div class="meetingNotes" v-el:note-share :disabled="noteShare.disabled">
                        <ui-textbox
                            name="notes" :multi-line="true"
                            placeholder="ノート"
                            :value.sync="negotation.notes"
                        ></ui-textbox>
                    </div> -->
                    <div class="annotation" v-el:file-annotation>
                        <h3 class="title"><ui-icon icon="assignment"></ui-icon>Annotation</h3>
                        <pre>{{ annotation }}</pre>
                    </div>
                    <staff-doc-share :staff-profile.sync="auth.user.profile" :docs-list.sync="documents" :show-more-is-visible.sync="show_more_docs_btn" view-doc-allowed :meeting-menu="meetingMenu" v-ref:doc-share></staff-doc-share>
                    <div class="col-xs-12 fileViewer" v-el:adj-container>
                        <div class="container">
                            <div class="controlPanel">
                                <ui-fab class="leftToggle" type="mini" icon="video_call" @click="toggleVidShare"></ui-fab>
                                <!-- <ui-fab class="leftToggle" type="mini" icon="note" @click="toggleNoteShare"></ui-fab> -->
                                <ui-fab class="noteToggle" type="mini" icon="assignment" @click="toggleAnnotation"></ui-fab>
                                <ui-fab class="docToggle" type="mini" icon="description" @click="toggleDocShare"></ui-fab>
                            </div>
                            <div class="viewer" v-el:file-viewer>
                                <ui-image-gallery :image-list.sync="pages" v-ref:image-gallery></ui-image-gallery>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import UiImageGallery from '../common/UiImageGallery.vue';
import StaffDocShare from './partials/StaffDocShare.vue';
import {PEERJS_API_KEY, PEERJS_HOST, APP_URL} from '../../js/config.js';
import auth from '../../js/auth.js';
import staff from '../../js/staff.js';
import helper, {getPeerKey, getPeerOptions} from '../../js/helpers.js';
import peer_fallback from '../../js/peer_fallback.js';
import {router} from '../../js/app.js';

export default {
    data() {
        return {
            peer_api_key: PEERJS_API_KEY,
            peer_host: PEERJS_HOST,
            peer_key_prefix: 'staff',
            peer_id: null,
            peer: null,
            connected_peer_id: null,
            socket: null,
            options: null,
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            meeting_key: this.$route.params.id,
            main: {
                progress: 0,
                loading: true
            },
            documents: [],
            i_docs_page_no: 1,
            show_more_docs_btn: false,
            endMeeting: false,
            negotation: staff.data.negotation,
            meetingMenu: [ 
                {
                    id: 'end_meeting',
                    text: 'End Meeting',
                    icon: 'input'
                }
            ],
            invalidMeetingKey: false,
            timerInterval: null,
            pages: [],
            annotation: '',
            noteShare: {
                disabled: false
            },
            video_src: null,
            c_video: null,
            process_interval: null,
            showVideoStream: false,
            vidShare: {
                disabled: false,
                coordinates: {
                    top: '0px',
                    left: '0px'
                }
            },
            screen: null,
            is_screen_sharing: false,
            tmp_stream: null,
            tmp_video_src: null
        }
    },
    methods: {
        onbeforeunload(evt) {
            evt = evt || window.event
            if(evt) {
                // this.endMeetingFn()
                // evt.returnValue = 'Ending Meeting'
                setTimeout(function(){
                    //not leaving
                    // console.log(evt)
                }, 3000);
                return ''
            }
        },
        endMeetingFn() {
            if(this.connected_peer_id) {
                this.peer.send(this.connected_peer_id, {
                    data: { key: 'endMeeting' }
                })
            }
            this.endMeeting = true
            this.updateNegotation()
            clearInterval(this.timerInterval)
            clearInterval(this.process_interval)
            if(this.tmp_stream) {
                this.tmp_stream.getTracks()[0].stop()
                this.initializeScreenShare()
            }
            staff.stopMeeting(this)
        },
        updatePointer() {
            this.$els.pointer.style.opacity = 1
            this.$els.pointer.style.top = `${this.mouseCoordinates.top}px`
            this.$els.pointer.style.left = `${this.mouseCoordinates.left}px`
        },
        updateNegotation() {
            let data = new FormData();
            data.append('_method', 'PUT')
            for ( var key in this.negotation ) {
                data.append(key, this.negotation[key])
            }
            staff.updateNegotation(this, {id: this.negotation.negotation_id}, data)
        },
        relayNotes() {
            this.peer.send(this.connected_peer_id, {
                data: {key: 'notes', data: this.negotation.notes}
            })
        },
        toggleNoteShare() {
            this.toggleElement(this.$els.noteShare)
        },
        toggleAnnotation() {
            this.toggleElement(this.$els.fileAnnotation)
        },
        toggleDocShare() {
            var sidebar = this.$refs.docShare.$children[0]
            sidebar.toggle()
        },
        toggleElement(el) {
            let has_el = helper.hasClass(el, 'hide')
            if(has_el) {
                el.classList.remove('hide')
            } else {
                el.classList.add('hide')
            }
        },
        getDocsCB(response) {
            response.data.documents.forEach((val, key) => {
                this.documents.push(val)
            })
            
            let paginate = response.data.meta.pagination
            if(paginate.total_pages > paginate.current_page) {
                this.show_more_docs_btn = true
            } else {
                this.show_more_docs_btn = false
            }
        },
        initializeScreenShare() {
            this.screen = ScreenShare.create({debug: true});

        },
        openPeer() {
            let vm = this
            this.options = getPeerOptions(this.peer_host, this.peer_api_key, {})
            this.peer = peer_fallback(this.peer_id, this.options)

            this.initializeScreenShare()
            this.peer.on('open', (id) => {
                // console.log('My peer ID is: ' + id);
                vm.peer.send(vm.meeting_key, {
                    label: 'meeting'
                })
            })

            this.peer.on('receive', (msg) => {
                vm.receiveMsg(msg)
            })

            this.peer.on('error', (err) => {
                if(err.type == 'unavailable-id' || err.type == 'network') {
                    if(err.type == 'unavailable-id') {
                        this.peerKey = getPeerKey(this.peerKeyPrefix)
                        this.peer.destroy()
                    }

                    this.$nextTick( () => {
                        vm.openPeer()
                    })
                }

                if(err.type == 'peer-unavailable') {
                    vm.endMeetingFn()
                }
            })
        },
        receiveMsg(msg) {
            let data = msg.data ? JSON.parse(msg.data) : msg.data
            if(!this.connected_peer_id) {
                this.connected_peer_id = msg.connectionId
                this.main.loading = false
                this.timerInterval = setInterval(() => {
                    this.negotation.duration ++
                },1000)
                staff.addNegotation(this, null)
                this.initVideoStream()
            }

            if(data) {
                switch(data.key) {
                    case 'notes': this.negotation.notes = data.data
                                break
                    case 'nextPage': this.$refs.imageGallery.nextPage(true)
                                    break
                    case 'prevPage': this.$refs.imageGallery.prevPage(true)
                                    break
                    case 'endMeeting': this.endMeetingFn()
                                       break
                    default: this[data.key] = data.data
                            break
                }
            }
        },
        initVideoStream() {
            navigator.getUserMedia = navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia

            // Get audio/video stream
            navigator.getUserMedia({
                audio: false, video: true
            }, (stream) => {
                // Set your video displays
                this.video_src = stream
                this.process_interval = setInterval(() => {
                    this.processVideoStream()
                }, 500)
            }, () => { 

            })
        },
        processVideoStream() {
            this.c_video.drawImage(this.$els.selfVideo, 0, 0, this.$els.selfCanvas.width, this.$els.selfCanvas.height)
            // let src_data = this.c_video.getImageData(0, 0, this.$els.selfCanvas.width, this.$els.selfCanvas.height)
            let src_data = this.$els.selfCanvas.toDataURL('image/jpeg', 0.8)

            this.peer.send(this.connected_peer_id, {
                data: {key: 'processVideoStream', data: {imageData: src_data}}
            })
        },
        toggleVidShare() {
            this.toggleElement(this.$els.vidShare)
        },
        expandVidShare() {
            this.vidShare.disabled = this.vidShare.disabled ? false : true
            if(this.vidShare.disabled) {
                this.vidShare.coordinates.top = this.$els.vidShare.style.top
                this.vidShare.coordinates.left = this.$els.vidShare.style.left
                this.$els.vidShare.style.top = '50%'
                this.$els.vidShare.style.left = '50%'
                this.$els.vidShare.style.transform = 'translate(-50%, -50%)'
                this.$els.vidShare.style.width = '90%'
                this.$els.peersVideoContainer.style.minHeight = '370px'
            } else {
                this.$els.vidShare.style.top = this.vidShare.coordinates.top
                this.$els.vidShare.style.left = this.vidShare.coordinates.left
                this.$els.vidShare.style.transform = 'none'
                this.$els.vidShare.style.width = '200px'
                this.$els.peersVideoContainer.style.minHeight = '150px'
            }
        },
        screenShare() {
            if(this.screen.isScreenShareAvailable()) {
                this.screen.start({
                    frameRate: 50
                }, (stream) => {
                    this.is_screen_sharing = true
                    this.tmp_stream = stream
                    this.tmp_video_src = this.video_src
                    this.video_src = stream

                }, (error) => {
                    // console.log(error)
                }, () => {
                    this.revertShare()
                })
            } else {
                alert('ExtensionまたはAddonをインストールして下さい');
            }
        },
        revertShare() {
            this.is_screen_sharing = false
            if(this.tmp_stream) {
                this.tmp_stream.getTracks()[0].stop()
                this.initializeScreenShare()
            }
            this.video_src = this.tmp_video_src
        }
    },
    events: {
        'staffdocshare:viewdoc' (vm, doc) {
            this.$refs.imageGallery.refreshCtr()
            this.pages = []
            for ( var key in doc.pages ) {
                this.pages.push({
                    id: key,
                    path: doc.pages[key].image_url,
                    annotation: doc.pages[key].annotation
                })
            }
            this.peer.send(this.connected_peer_id, {
                data: {key: 'viewDoc', data: doc}
            })
        },
        'staffdocshare:sharedoc' (vm, docLink) {
            this.peer.send(this.connected_peer_id, {
                data: {key: 'shareDoc', data: docLink}
            })
        },
        'staffdocshare:showmore' (vm) {
            this.i_docs_page_no ++
            staff.getDocuments(this, {page: this.i_docs_page_no}, this.getDocsCB)
        },
        'staffdocshare:selected:end_meeting' (option) {
            this.endMeetingFn()

            router.go({
                name: `${this.auth.user.role}-dashboard`
            })
        },
        'keydown' (e) {
            this.relayNotes()
        },
        'changed' (e) {
            this.relayNotes()
        },
        'ui-image-gallery:loaded' (id) {
            if(typeof this.pages[id] != 'undefined') {
                this.annotation = this.pages[id].annotation
            }
        },
        'ui-image-gallery:next-page' (id) {
            this.annotation = this.pages[id].annotation
            this.peer.send(this.connected_peer_id, {
                data: {key: 'nextPage', data: true}
            })  
        },
        'ui-image-gallery:prev-page' (id) {
            this.annotation = this.pages[id].annotation
            this.peer.send(this.connected_peer_id, {
                data: {key: 'prevPage', data: true}
            })  
        },
        'resized' (el) {
            let isNotes = helper.hasClass(el, 'meetingNotes')
            if(isNotes) {
                this.noteShare.disabled = true
            }
        },
        'stop-resize' (el) {
            let isNotes = helper.hasClass(el, 'meetingNotes')
            if(isNotes) {
                this.noteShare.disabled = false
            }
        }
    },
    created() {
        this.peer_id = getPeerKey(this.peer_key_prefix)
        this.openPeer()
        staff.getDocuments(this, {page: this.i_docs_page_no}, this.getDocsCB)
        document.addEventListener('beforeunload', this.onbeforeunload)
    },
    ready() {
        let config = {
            role: 'staff',
            showToolbar: false
        }
        window.onbeforeunload = this.onbeforeunload
        this.$dispatch('component-ready', config)

        this.c_video = this.$els.selfCanvas.getContext('2d')
    },
    components: {
        StaffDocShare,
        UiImageGallery
    },
    beforeDestroy() {
        this.endMeetingFn()
    }
}
</script>

