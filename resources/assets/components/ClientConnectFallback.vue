<template>
    <div class="protoCon peerCon">
        <div class="protoConnectContainer">
            <div class="wrapper noToolbar">
                <div class="overlay" v-if="main.loading">
                    <ui-progress-circular
                        :show="main.loading" color="white" :value="main.progress"
                    ></ui-progress-circular>
                </div>

                <div class="overlay row  middle-xs center-xs blurredBg" v-if="!main.loading && !connect.loaded">
                    <div class="meetingId" v-if="!connect.loaded">
                        <span>{{ peer_id }}</span>
                    </div>
                </div>

                <div class="overlay row  middle-xs center-xs blurredBg" v-if="endMeeting">
                    <div class="meetingMsg">
                        <span>Meeting has ended. <br> Thank you for your time.</span>
                    </div>
                </div>

                <div class="row blurredBg noSelect" v-el:peer-container v-show="connect.loaded && !endMeeting">
                    <div class="videoFeedCon withAttachFab" v-el:vid-share :disabled="vidShare.disabled" v-show="has_video">
                        <div class="peer-connect">
                            <div class="video-connections-feed" v-el:peers-video-container>
                                <div class="video-container">
                                    <canvas v-el:self-canvas width="200" height="124"></canvas>
                                </div>
                            </div>
                        </div>

                        <ui-fab type="mini" icon="zoom_out_map" @click="expandVidShare"></ui-fab>
                    </div>
                    <!-- <div class="meetingNotes" v-el:note-share :disabled="noteShare.disabled">
                        <ui-textbox
                            name="notes" :multi-line="true"
                            placeholder="ノート"
                            :value.sync="notes"
                        ></ui-textbox>
                    </div> -->
                    <!-- <staff-doc-share :staff-profile.sync="staffProfile" :docs-list.sync="docsList"></staff-doc-share> -->
                    <div class="col-xs-12 fileViewer" v-el:adj-container>
                        <div class="container">
                            <div class="controlPanel fixed">
                                <ui-fab class="leftToggle" type="mini" icon="video_call" @click="toggleVidShare"></ui-fab>
                                <!-- <ui-fab class="leftToggle" type="mini" icon="note" @click="toggleNoteShare"></ui-fab> -->
                            </div>
                            <div class="viewer fixed" v-el:file-viewer>
                                <ui-image-gallery :image-list.sync="pages" v-ref:image-gallery></ui-image-gallery>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ui-modal hide-footer type="small"
                :show.sync="show.share_doc_modal"
                class="downloadDoc"
                >
                <div slot="header">
                    <h1 class="ui-modal-header-text">
                        <ui-icon icon="description"></ui-icon> Shared Document
                    </h1>
                </div>
                <ui-button icon="file_download" color="success" @click="downloadDoc">Download Document</ui-button>
            </ui-modal>
        </div>
    </div>
</template>

<script>
import UiImageGallery from './common/UiImageGallery.vue';
import {PEERJS_API_KEY, PEERJS_HOST, profile} from '../js/config.js';
import helper, {getPeerKey, getPeerOptions}  from '../js/helpers.js';
import peer_fallback from '../js/peer_fallback.js';

export default {
    data() {
        return {
            peer_api_key: PEERJS_API_KEY,
            peer_host: PEERJS_HOST,
            peer_id: null,
            connected_peer_id: null,
            peer: null,
            socket: null,
            options: null,
            main: {
                progress: 0,    
                loading: true
            },
            connect: {
                loaded: false
            },
            docsList: [],
            endMeeting: false,
            notes: '',
            show: {
                share_doc_modal: false
            },
            docLink: '',
            pages: [],
            noteShare: {
                disabled: false
            },
            c_video: null,
            vidShare: {
                disabled: false,
                coordinates: {
                    top: '0px',
                    left: '0px'
                }
            },
            has_video: false
        }
    },
    methods: {
        beforeUnload() {
            
        },
        updatePointer() {
            if(!this.endMeeting) {
                this.$els.pointer.style.opacity = 1
                this.$els.pointer.style.top = `${this.mouseCoordinates.top}px`
                this.$els.pointer.style.left = `${this.mouseCoordinates.left}px`
            }
        },
        relayNotes() {
            this.peer.send(this.connected_peer_id, {
                data: {key: 'notes', data: this.notes}
            })
        },
        toggleNoteShare() {
            this.toggleElement(this.$els.noteShare)
        },
        toggleElement(el) {
            let has_el = helper.hasClass(el, 'hide')
            if(has_el) {
                el.classList.remove('hide')
            } else {
                el.classList.add('hide')
            }
        },
        downloadDoc() {
            helper.downloadFile(this.docLink)
        },
        endMeetingFn() {
            if(!this.endMeeting) {
                if(this.connected_peer_id) {
                    this.peer.send(this.connected_peer_id, {
                        data: { key: 'endMeeting' }
                    })
                }
                this.endMeeting = true
                this.peer.destroy()
            }
        },
        openPeer() {
            let vm = this
            this.options = getPeerOptions(this.peer_host, this.peer_api_key, {})
            this.peer = peer_fallback(this.peer_id, this.options)
            this.peer.on('open', (id) => {
                // console.log('My peer ID is: ' + id);
                vm.peer_id = id
                vm.main.loading = false
            })

            this.peer.on('receive', (msg) => {
                vm.receiveMsg(msg)
            })

            this.peer.on('error', (err) => {
                if(err.type == 'unavailable-id' || err.type == 'network') {
                    if(err.type == 'unavailable-id') {
                        this.peer_id = getPeerKey()
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
                this.connect.loaded = true
                this.peer.send(this.connected_peer_id, {
                    label: 'meeting'
                })
            }

            if(data) {
                switch(data.key) {
                    case 'viewDoc': let doc = data.data
                                    this.$refs.imageGallery.refreshCtr()
                                    this.pages = []
                                    for ( var key in doc.pages ) {
                                        this.pages.push({
                                            id: doc.pages[key].page_id,
                                            path: doc.pages[key].image_url
                                        })
                                    }
                                    break
                    case 'shareDoc': this.show.share_doc_modal = true
                                     this.docLink = data.data
                                    break
                    case 'nextPage': this.$refs.imageGallery.nextPage(true)
                                    break
                    case 'prevPage': this.$refs.imageGallery.prevPage(true)
                                    break
                    case 'endMeeting': this.endMeetingFn(data.data)
                                    break
                    case 'processVideoStream': this.processVideoStream(data.data)
                                               break
                    default: this[data.key] = data.data
                            break
                }
            }
        },
        processVideoStream(data) {
            this.has_video = true
            let vm = this
            let img = new Image()
            img.src = data.imageData
            img.onload = function() {
                vm.c_video.drawImage(this, 0, 0)
            }
            // this.c_video.drawImage(data, 0, 0)
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
        }
    },
    events: {
        'keydown' (e) {
            this.relayNotes()
        },
        'changed' (e) {
            this.relayNotes()
        },
        'ui-image-gallery:next-page' (id) {
            this.peer.send(this.connected_peer_id, {
                data: {key: 'nextPage', data: true}
            })
        },
        'ui-image-gallery:prev-page' (id) {
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
    components: {
        UiImageGallery,
    },
    created() {
        this.peer_id = getPeerKey()
        this.openPeer()
        document.addEventListener('beforeunload', this.beforeUnload)
    },
    ready() {
        let config = {
            role: 'guest',
            showToolbar: false
        }

        this.$dispatch('component-ready', config)
        this.c_video = this.$els.selfCanvas.getContext('2d')
    }
}
</script>