<template>
    <div class="protoDashboard">

        <staff-sidebar v-ref:sidebar :api-url="notices_post.api_url"></staff-sidebar>
        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">
                <ui-alert class="compatibility-alert" v-if="errorCompatibility" @dismiss="errorCompatibility = false" type="error" v-show="errorCompatibility">
                    mee2box の全ての機能をお使いになりたい場合は Chrome の使用おすすめしております。
                </ui-alert>
                <!-- m2b-81 -->
                <ui-alert class="compatibility-alert" v-if="criticalSpaceUsage" @dismiss="criticalSpaceUsage = false" type="error" :dismissible="false" v-show="criticalSpaceUsage">
                    4~5GB: 商談動画の容量が上限の5GBに迫っています。動画をダウンロードしてからMee2box上から削除してください
                </ui-alert>
                <ui-alert class="compatibility-alert" v-if="overDiskSpaceUsage" @dismiss="overDiskSpaceUsage = false" type="error" :dismissible="false" v-show="overDiskSpaceUsage">
                    5GB~: 商談動画の容量が5GBを超えました。動画を削除し容量を確保しなければレコーディング機能は使用出来ません。
                </ui-alert>
                <!-- m2b-81 -->                
                 <div class="col-md-12 loader-wrapper" v-bind:style="{visibility: loadingVisible}">
                    <div class="custom-loader"></div>
                    <div class="loader-mask"></div>
                                    
                </div>

                <div class="row center-xs">
                    <div class="col-xs-12 col-sm-8">
                        <ui-alert class="alertBgWhite" @dismissed="error = false" type="error" :show="error">
                            <span v-html="error_message"></span>
                        </ui-alert>
                        <div class="box row">
                            <div class="col-xs-12 col-sm-8">
                                <ui-textbox
                                    name="search-meeting" placeholder="商談ナンバーを5桁入力してください。" hide-label
                                    :max-length="5" validation-rules="required|min:5|max:5" class="meeting-input"
                                    help-text="接続ナンバー入力" hide-validation-errors v-ref:meeting-textbox
                                    :value.sync="meeting_key" @changed="searchMeeting" @focussed="searchMeetingFocused" @blurred="searchMeeting"
                                ></ui-textbox>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <ui-button class="fullwidth meeting-button" disabled color="primary" @click="connectMeeting" v-ref:meeting-connect-button>接続する</ui-button>
                            </div>
                            <peer-connect :api-key="peer_api_key" :peer-host="peer_host" :peer-key-prefix="peer_key_prefix" 
                            :init-stream="false" v-ref:peer-connect :main-cam="mainCam"></peer-connect>
                            

                        </div>
                    <!-- New Added HTML -->
                    <div class="additional-settings">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="switch-label">接続時のカメラ</p>
                                </div>
                                <div class="col-md-3">
                                    <!-- <p>OFF &nbsp;&nbsp; ON</p> -->
                                    <p><span class="switch_off" v-bind:style="style_off">OFF</span><span class="switch_on" v-bind:style="style_on">ON</span></p>
                                    <ui-switch name="video_enabled" :value.sync="video_toggled"></ui-switch>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End New Added HTML -->
                            <div class="col-xs-12 doc-list-con">
                                <div class="row">
                                    <div class="col-xs-12 title-con">
                                        <h4>最近使用した資料</h4>
                                    </div>
                                    <div class="col-xs-12 content-con" v-if="!recent_docs.length">
                                        <p>ファイルがありません</p>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="doc-item row" v-for="doc in recent_docs" v-bind:key="doc.created_at">
                                            <div class="col-xs-12 col-sm-4 doc-date">
                                                <p>{{ doc.created_at }}</p>
                                            </div>
                                            <div class="col-xs-12 col-sm-8 doc-name">
                                                <p>{{ doc.title }}</p>
                                            </div>
                                            <div class="col-xs-12">
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 banners-con" v-if="false">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 banners">
                                        <div class="item">バナーなど</div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 banners">
                                        <div class="item">バナーなど</div>
                                    </div>
                                </div>
                            </div>
                       <!--  </div> -->
                    </div>
                   <!--  <div class="additional-settings">
                        <ui-switch name="video_enabled" :value.sync="video_enabled">カメラ off/on</ui-switch> -->
                        <!-- <ui-switch name="is_supported" :value.sync="is_supported">Client in PC</ui-switch> -->
                    <!-- </div> -->
                </div>
                <slot name="modal-slot"></slot>
            </div>

        </div>
    </div>
</template>
<script>
import StaffSidebar from './partials/StaffSidebar.vue';
import PeerConnect from '../common/PeerConnect.vue';
import {APP_URL, PEERJS_API_KEY, PEERJS_HOST} from '../../js/config.js';
import staff from '../../js/staff.js';
import browser from '../../js/browser_detect.js';
import auth from '../../js/auth.js';
import {router} from '../../js/app.js';
import helper from '../../js/helpers.js';
import Cookie from 'js-cookie';
import {STAFF_NOTICES_RESOURCE, COMPANY_USER_NOTICES_RESOURCE} from '../../js/api_routes.js';
import { UiTooltip } from 'keen-ui';
export default {
    data() {
        return {
        loadingVisible: 'hidden',
        mainCam:{
        id: '',
        video: '',
        audio: '',
        isSet: false
        },
            // style_on:Cookie.get('video_toggled') == 'true' ? 'color:black' : 'color:gray',
            // style_off:Cookie.get('video_toggled') == 'true' ? 'color:gray' : 'color:black',
            style_on:'color:black',
            style_off:'color:gray',   
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            notices_post: {
                api_url: STAFF_NOTICES_RESOURCE,
            },
            meeting_key: '',
            peer_api_key: PEERJS_API_KEY,
            peer_host: PEERJS_HOST,
            peer_key_prefix: 'staff',
            // is_supported: true,
            // video_enabled: Cookie.get('video_enabled') == 'true' ? true : false,
            video_toggled: true,
            error: false,
            error_message: null,
            in_meeting: false,
            in_meeting_interval: null,
            recent_docs: [],
            mainCam: {
                    id: '',
                    video: '',
                    audio: '',
                    isSet: false
            },
            errorCompatibility: false,
            documents:[],
            role: '',
            criticalSpaceUsage: false,
            overDiskSpaceUsage: false            
        }
    },
    methods: {
        changeButtonState(state) {
            try {
                this.$refs.meetingConnectButton.disabled = true
                this.$refs.meetingConnectButton.$children[0].icon = 'block'
                if( state == 'enable' ) {
                    this.$refs.meetingConnectButton.disabled = false
                    this.$refs.meetingConnectButton.$children[0].icon = 'forward'
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Changing the button state'}
                let functionName = 'StaffDashboard:changeButtonState';
                helper.catchError(errorStats, functionName);
            }
        },
        searchMeeting() {
            try {
                if (this.$refs.meetingTextbox.valid && this.meeting_key) {
                    this.changeButtonState('enable')
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Searching the meeting'}
                let functionName = 'StaffDashboard:searchMeeting';
                helper.catchError(errorStats, functionName);
            }
        },
        searchMeetingFocused() {
            try {
                this.changeButtonState()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Search meeting button is focused'}
                let functionName = 'StaffDashboard:searchMeetingFocused';
                helper.catchError(errorStats, functionName);
            }
        },
        // handleMessageOverride(data) {
        //     let cb = null
        //     if(data.type == 'OFFER' && data.payload.label == 'check peer') {
        //         let params = { peer_id: this.meeting_key }
        //         staff.startMeeting(this, params)
        //         router.go({ name: 'staff-connect-fallback', params: { id: this.meeting_key } })
        //     } else {
        //         this.$refs.peerConnect.peer._handleMessage(data)
        //     }
        // },
        connectMeeting() {
            try {
                this.error = false
                // this.$refs.peerConnect.peer.socket._events.message[0].fn = this.handleMessageOverride
                if(!this.in_meeting) {
                    let cb = () => {
                        this.$refs.peerConnect.connectToPeer(this.meeting_key, 'check peer')
                    }
                    staff.checkPeerId(this, this.meeting_key, cb)
                    this.loadingVisible = 'visible'
                } else {
                    this.error = true
                    this.error_message = '同じアカウントで接続中です'
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Trying to connect into the meeting'}
                let functionName = 'StaffDashboard:connectMeeting';
                helper.catchError(errorStats, functionName);
            }
        },
        // m2b-81
        bytesToSize(bytes) { //Adding Convertion from bytes to size
            try {
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                if (bytes == 0) return '0 Byte';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Converting Bytes to Size'}
                let functionName = 'Connect:bytesToSize';
                staff.checkError(errorStats, functionName);
            }
        },        
        // m2b-81
    },
    events: {
        'peerconnect:peer:conn:open' (vm, conn) {
            try {
                conn.close()
                let params = { peer_id: this.meeting_key }
                staff.startMeeting(this, params)
                clearInterval(this.in_meeting_interval)
                this.$refs.peerConnect.peer.disconnect()
                let interval = setInterval(() => {
                    if(!this.$refs.peerConnect.peer.open) {
                        // console.log(this)
                        let peer_id = typeof conn.remoteId != 'undefined' ? conn.remoteId : conn.peer
                        router.go({ name: 'staff-connect', params: { id: peer_id } })
                        clearInterval(interval)
                    }
                },1000)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Opening a peer connection'}
                let functionName = 'StaffDashboard::peerconnect:peer:conn:open';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:error' (vm, err) {
            try {
                if(err.type == 'peer-unavailable') {
                    // console.log(err)
                    this.error = true
                    this.error_message = '接続ナンバーが不正です'
                    this.loadingVisible = 'hidden';
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Encountered an error trying to connect'}
                let functionName = 'StaffDashboard::peerconnect:peer:error';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    created() {
        try {
            // m2b-81
            staff.fetchTotalDiskUsage().then((response) => {
                if(response) {
                    var total_size = this.bytesToSize(response.body.total_size);
                    // console.log('fetchTotalDiskUsage reponse: ' + total_size);
                    var brk_size = total_size.split(' ');
                    if(brk_size[1] == "GB") {
                        if(parseInt(brk_size[0]) > 5) {
                            this.overDiskSpaceUsage = true;               
                        } else if(parseInt(brk_size[0]) >= 4 && parseInt(brk_size[0]) <= 5) {
                            this.criticalSpaceUsage = true;
                        }
                    }                
                }
            });  
            // m2b-81

            // this.$refs.tooltip.$emit('open');                     
            this.in_meeting_interval = setInterval(() => {
                if(this.auth.user.role != null) {
                    this.screenshare = false;          
                    // alert("jong");      
                    staff.checkMeeting(this, this.role)
                }
            }, 2000)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created the component for the staff dashboard'}
            let functionName = 'StaffDashboard:created';
            helper.catchError(errorStats, functionName);
        }
    },
    ready() {
        try {
            let config = {
                role: 'staff',
                showToolbar: true
            }

            Cookie.set('video_enabled', true)
            if(Cookie.get('video_toggled') == undefined){
                Cookie.set('video_toggled', true)
            }

            if(Cookie.get('video_toggled') == 'true') {
                this.video_toggled = true
                this.style_on = 'color:black'
                this.style_off =  'color:gray'
            } else {
                this.video_toggled = false
                this.style_off =  'color:black'
                this.style_on = 'color:gray'
            }
            // console.log(this.video_toggled)
            this.$dispatch('component-ready', config)
            let vm=this;
            //staff.getDocuments(this, {}, (response) => {
            //    if(typeof response.data.documents != 'undefined') {
            //        response.data.documents.forEach((val, key) => {
            //            // console.log(val)
            //            if (val.pages != 0 ) {
            //                val.created_at = helper.formatDate(val.created_at.date, 'YYYY.MM.DD')
            //                this.recent_docs.push(val)
            //            } else {
            //                // IF empty pages inside the document direct delete
            //                let params = {id: val.document_id}
            //                staff.deleteDocumentFromStorage(params)
            //
            //                staff.deleteDirectDocument(vm,params, () =>{
            //                    vm.documents = response.data.documents.filter((item) => item.id !== val.document_id)
            //                })
            //            }
            //        })
            //    }
            //})
            staff.getDocuments(this, {page: 1}, (response) => {
                if(typeof response.data.documents != 'undefined') {
                    response.data.documents.forEach((val, key) => {
                        // console.log(val)
                        if (val.pages != 0 ) {
                            val.created_at = helper.formatDate(val.created_at.date, 'YYYY.MM.DD')
                            this.recent_docs.push(val)
                        }
                    })
                }
            })
            //console.log(this)
            if (browser.detectIE().trigger) {
                // let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Browser Compatibility'}
                // let functionName = 'Speech Recognition and Webrtc';
                // staff.checkError(errorStats,functionName);
                // this.errorCompatibility = true;
            }
            // vm.$refs.tooltip.$emit('close');  
            // setTimeout(function () {
            //     var bt = document.getElementById('btooltip');
            //     bt.innerHTML = "";
	        // }, 10000);
            window.onbeforeunload = null;
            document.getElementById('MainSwitchCam') ? document.getElementById('MainSwitchCam').parentNode.removeChild(document.getElementById('MainSwitchCam')) : false
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting up the staff dashboard'}
            let functionName = 'StaffDashboard:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    watch: {
        video_toggled: function(value) {
            try {
                Cookie.set('video_toggled', value)
                if(value){
                    this.style_on = 'color:#000;'
                    this.style_off = 'color:#a3a3a3;'
                } else {
                    this.style_off = 'color:#000'
                    this.style_on = 'color:#a3a3a3;'
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to video toggled'}
                let functionName = 'StaffDashboard:video_toggled';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    components: {
        StaffSidebar,
        PeerConnect,
        UiTooltip
    },
    beforeDestroy() {
        clearInterval(this.in_meeting_interval)
    }
}
</script>
<style lang="scss" scoped>
    .proto .protoDashboard .dashboardContent .additional-settings{
        position: relative;
    right: 0;
    bottom: 0;
    padding-right: 0px;
    padding-left: 0;
    margin: 2rem 0 5rem;
    }
    .onoff-label{
        position: absolute;
        top: -30px;
        .onoff-space{
            margin-left:2rem;
        }
    }
    .ui-button.fullwidth {
        height: 39px;
    }
    .proto .dashboardContent .doc-list-con {
        padding-left: 10px;
    }
    
   
</style>
<style lang="scss">
//loading area


.dashboardContent .loader-wrapper{
    padding: 0 !important;
    z-index: 99;
    width: 100%;
    height: 100%;
    position: absolute;
}

.loader-mask{
    background-color: #000000;
    opacity: .5;
    min-height: 100%;
}

.custom-loader{
   border: 5px solid #f3f3f3;
    border-radius: 50%;
    border-top: 5px solid #3498db;
    width: 50px;
    height: 50px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
    background-color: transparent;
    position: absolute;
    left: 46%;
    top: 43%;
    z-index: 9999;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

     .ui-switch-thumb {
        width: 30px;
    height: 30px;
        background-color: #3b5998!important;
    }
    .ui-switch-label-text {
        margin-left: 0;
        margin-right: 2rem;
    }
    .ui-switch-track {
    top: 7px;
    height: 15px;
    width: 60px;
}
.ui-switch-container {
    position: relative;
    width: 60px;
    height: 30px;
}
.ui-switch.checked .ui-switch-thumb {
    left: 35px;
}
.proto .protoDashboard .dashboardContent .additional-settings .ui-switch-label-text{
    font-weight:700;
}
.additional-settings{
    .col-md-4, .col-md-3{
        padding:0;
    }
    p{
        text-align:left;
            margin-bottom: .5rem;
        .switch_off{
           padding-right:1rem;     
        }
        .switch_on{
            padding-left:1rem;
        }
    }
    .switch-label{
        margin-top: 40px;
        font-weight: 700;
        font-size: 14px;
            letter-spacing: 1.5px;
    }
}
.compatibility-alert {
    .ui-alert-close-button{
        color: #fff;
    }
    .ui-alert-body {
        text-align: center;
        background: #ef5c51;            
        .ui-alert-icon {
            color: #fff;
        }
        .ui-alert-text {
            color: #fff !important;
            font-size: 15px !important;
            font-weight: 600;
        }
    }
}
@media only screen and (min-width: 1400px){
        .col-md-4{
        flex: 0 0 28.333333%;
     max-width: 28.333333%;
    }
}
</style>
