<template>
    <div class="protoCon peerCon">
        <div class="protoConnectContainer">
            <div class="wrapper noToolbar">
                <div class="pointer" v-el:pointer v-if="connect.loaded && !endMeeting">
                    <ui-icon icon="pan_tool"></ui-icon>
                </div>
                <div class="share_link" style="z-index:-1 !important;left:-60rem; display:none;">
                <ui-textbox name="name" type="text" :value.sync=shareLink.textBoxLink id="urlink" @blurred="setLink(shareLink.text)"></ui-textbox>
                <div class="share_link_btn" data-clipboard-target="#urlink">
                    <span class="mee2box-custom-icon description">description</span><br>
                </div>
                </div>
                <!-- <peer-connect :api-key="peer_api_key" :peer-host="peer_host" :peer-key-prefix="peer_key_prefix"
                :init-stream="false" v-ref:peer-connect :main-cam="mainCam" :primary-cam="primaryCam" :child-cams="childCams"
                :video-user-feed="videoUserFeed"></peer-connect> -->
                <!-- Modal for sharing file of child -->
                <ui-modal
                    :show.sync="clientupload" hide-footer header="資料共有" transition="ui-modal-fade"
                    body="Please Input Some Files">
                     <ui-alert @dismiss="show.uploaderror = false" type="error" v-show="show.uploaderror">
                                ファイルタイプ pptx、xlsx、pdf、doc
                     </ui-alert>
                     <form autocomplete="off">
                           <div class="form-group">
                               <div class="buttonCon">
                                   <ui-button type="normal" button-type="normal" icon="file_upload" class="protoButton fpreview-btn" v-ref:upload-document :value.sync="uploads" :loading="loading.file_upload" @click="uploadClicked"  text="ファイルを選択"></ui-button>
                                   <file-preview name="file" id="file" @changed="changeDocument" v-ref:upload-file hide-image-preview></file-preview>
                               </div>
                           </div>
                           <div class="fpreview-size">データサイズ ： {{ clientFileSize }} </div>
                        </form>
                           <div class="fpreview-actions" v-if="uploadSuccess">
                                <ui-button type="normal"  class=" factions-btn btn-between-space" @click="viewFileToParent"  text="見せる"></ui-button>
                                <ui-button type="normal"  class=" factions-btn" @click="shareFileToAll"  text="渡す"></ui-button>
                           </div>
                           <div class="ui-button-group" v-bind:class="[btnLoad ? 'showLoad' : 'hideLoad']" id="{{ btnLoad}} o">
                           <!-- <div class="ui-button-group"> -->
                               <ui-button
                               type="normal"
                               :loading="false"
                               color="success"
                               :disabled ="show.disable"
                               class="fpreview-upload"
                               @click="convertDocument"
                               >アップロード</ui-button>
                           </div>
                           <ui-progress-linear
                                :show="loading" type="determinate" id="progressLinear" color="darkblue" :value="progress"
                            ></ui-progress-linear>
                            <p v-if="errorConvert" class="errorConvertText {{errorConvert}}">There was an error occured. Please try again later...</p>
                   </ui-modal>
                <!-- End Modal -->
                <div class="overlay" v-if="main.loading">
                    <ui-progress-circular
                        :show="main.loading" color="white" :value="main.progress"
                    ></ui-progress-circular>
                </div>
                <div class="screen-sharing" v-show="$refs.peerConnect.screen_share_stream">
                    <video id="shareScreenCon" v-el:share-screen-con muted playsinline autoplay></video>
                </div>
                <div v-if="errorCompatibility">
                <ui-alert class="compatibility-alert" @dismiss="errorCompatibility = false" type="error" v-show="errorCompatibility">
                    <!-- mee2box の全ての機能をお使いになりたい場合は Chrome の使用おすすめしております。 -->
                    現在IEでは画面共有・レコーディング機能をご利用いただけません。
                </ui-alert>
                </div>
                <div class="overlay row  middle-xs center-xs blurredBg" v-if="!main.loading && !connect.loaded && !endMeeting">
                    <!-- <ui-alert class="compatibility-alert" v-if="errorCompatibility" @dismiss="errorCompatibility = false" type="error" v-show="errorCompatibility">
                        mee2box の全ての機能をお使いになりたい場合は Chrome の使用おすすめしております。
                    </ui-alert> -->
                    <div class="meetingId" v-if="!connect.loaded">
                        <span>接続しています...</span>
                    </div>
                </div>
                <!-- <div class="overlay row  middle-xs center-xs blurredBg" v-if="endMeeting"> -->
                <!-- <div class="" v-if="endMeeting"> -->
                    <!-- <thank-you></thank-you> -->
                    <!-- <div class="meetingMsg">
                        <span>接続を終了しました。</span>
                    </div> -->
                <!-- </div> -->
                <div class="overlay row  middle-xs center-xs blurredBg" v-if="!shareLink.default">
                    <div class="meetingMsg">
                        <span>Invalid Key</span>
                    </div>
                </div>
                <div class="row blurredBg noSelect" v-bind:class="[vidSize ? 'video-max' : 'video-min']" v-el:peer-container v-show="connect.loaded && !endMeeting">
                    <div v-draggable v-onclickfront class="videoFeedCon withAttachFab" v-el:vid-share :disabled="vidShare.disabled">
                        <peer-connect :api-key="peer_api_key" :show-audio-toggle="true" :peer-host="peer_host" :peer-key-prefix="peer_key_prefix" :video-enabled="video_enabled" :primary-cam="primaryCam" :main-cam="mainCam" :child-cams="childCams" :video-user-feed="videoUserFeed" v-ref:peer-connect :second-client="true" :second-client-childs="clients"></peer-connect>
                        <button class="ui-fab zoom_out_screen ui-fab-mini icon-color"  v-if="video_enabled" @click="expandVidShare">
                            <i class="zoom_out_screen_icon" aria-hidden="true"></i>
                            <div class="ui-ripple-ink"></div>
                           <!--  <span>最大化<span> -->
                        </button>
                    </div>
                    <div v-draggable v-onclickfront class="meetingNotes" v-bind:style="{ height: meetingNotesTrue.height, width: meetingNotesTrue.width, top: meetingNotesTrue.top, left: meetingNotesTrue.left}" v-el:note-share :disabled="noteShare.disabled">
                        <div class="meetingNotesWrapper peer-ui-connect">
                            <div class="ui-close-button">
                                <button class="ui-fab ui-fab-mini color-primary" @click="toggleNoteShare">
                                    <i class="ui-icon material-icons ui-fab-icon close">close</i>
                                    <div class="ui-ripple-ink"></div>
                                    <span></span>
                                </button>
                            </div>
                            <div class="meeting-note-head">ノート機能</div>
                            <ui-textbox
                                name="notes" :multi-line="true"
                                placeholder="ノート"
                                :value.sync="notes" id="notesID" v-on:input="notesCheck"
                            ></ui-textbox>
                            <div class="meeting-note-footer">
                            </div>
                            <ui-resizeable class="withAttachFab"></ui-resizeable>
                        </div>
                    </div>
                    <!--div v-draggable v-onclickfront v-el:sub-title class="subtitle"-->
                    <div v-draggable v-el:sub-title v-onclickfront  class="subtitle" v-bind:style="{ height: noteSubtitle.height, width: noteSubtitle.width, top: noteSubtitle.top, left: noteSubtitle.left}" :disabled="noteSubtitle.disabled" >
                        <div class="ui-close-button">
                            <button class="ui-fab ui-fab-mini color-primary" @click="toggleSubtitle">
                                <i class="ui-icon material-icons ui-fab-icon close">close</i>
                                <div class="ui-ripple-ink"></div>
                            </button>
                        </div>
                        <h3 class="subtitle-header"> 議事録 </h3>
                        <div id="subtitle-content">
                            <template v-if="!isUsingTemasys">
                                <p v-for="sub in subtitle" :key="sub">
                                    <span v-html="sub">{{sub}}</span>
                                </p>
                            </template>
                        </div>
                        <div class="subtitle-footer">
                            <!-- new added html for voice switch -->
                            <div class="btn-group btn-toggle voice-switch" @click="toggleVoiceRecord"> 
                                <span class="VoiceSwitch-label">議事録を収録</span>
                                <div class="VoiceSwitch-box">
                                     <button class="btn btn-sm " v-bind:class="[isRecord ? '' : 'btn-primary']">OFF</button>
                                    <button class="btn btn-sm " v-bind:class="[isRecord ? 'btn-primary' : '']">ON</button>
                                </div>
                                <span>
                                    <div v-bind:class="[isRecord ? 'RedLight' : 'BlackLight']"></div>
                                </span>
                            </div>
                        </div>
                         <ui-resizeable class="withAttachFab adjusted"></ui-resizeable>
                    </div>
                     
                    <!-- <staff-doc-share :staff-profile.sync="staffProfile" :docs-list.sync="docsList"></staff-doc-share> -->
                    <div class="col-xs-12 fileViewer" v-el:adj-container>
                        <div class="container">
                            <div class="controlPanel">
                                <ui-fab class="leftToggle" type="mini" icon="video_call" @click="toggleVidShare"></ui-fab>
                                <ui-fab class="leftToggle" type="mini" icon="note" @click="toggleNoteShare"></ui-fab>
                            </div>
                            <div class="viewer" id="viewerScreen" v-el:file-viewer>
                                <!-- <span class="footerCopyright">&#169; 2017 Mee2box. All right Reserved.</span> -->
                                <ui-image-gallery :image-list.sync="pages" :minimize-default-view.sync="permission" :annotation.sync="annotation" v-ref:image-gallery :client-pointer.sync="pointerFordoc" :clients.sync="clientContainer" :is-maximized="isMaximized" :box-element="boxEls" :is-staff="false"></ui-image-gallery>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ui-modal hide-footer type="small"
                :show.sync="show.share_doc_modal"
                class="downloadDoc">
                <div slot="header">
                    <h1 class="ui-modal-header-text">
                        <ui-icon icon="description"></ui-icon> 資料が届きました。
                    </h1>
                </div>
                <ui-button icon="file_download" color="success" @click="downloadDoc">資料を受け取る。</ui-button>
                <div class="file-size">データサイズ ： {{ docSize }}</div>
            </ui-modal>
            <ui-modal hide-footer type="small"
                :show.sync="show.share_clientdoc_modal"
                class="downloadDoc"
                >
                <div slot="header">
                    <h1 class="ui-modal-header-text">
                        <ui-icon icon="description"></ui-icon> 資料が届きました。
                    </h1>
                </div>
                <ui-button icon="file_download" color="success" @click="downloadClientDoc">資料を受け取る。</ui-button>
                <div class="file-size">データサイズ ： {{ docClient.size }}</div>
            </ui-modal>
            <ui-modal hide-footer type="small"
                :show.sync="show.share_notes_modal"
                class="downloadNotes"
                >
                <div slot="header">
                    <h1 class="ui-modal-header-text">
                        <ui-icon icon="description"></ui-icon> メモが届きました。
                    </h1>
                </div>
                <ui-button icon="file_download" color="success" @click="downloadNotes">メモを受け取る。</ui-button>
                <div class="file-size">データサイズ ： {{ notesSize }}</div>
            </ui-modal>
             <ui-modal hide-footer type="small"
                :show.sync="show.share_voice_report"
                class="downloadNotes"
                >
                <div slot="header">
                    <h1 class="ui-modal-header-text">
                        <ui-icon icon="description"></ui-icon><span style="font-size:27px">議事録が届きました。</span>
                    </h1>
                </div>
                <ui-button icon="file_download" color="success" @click="downloadConversation">議事録を受け取る。</ui-button>
                <div class="file-size">データサイズ ： {{voice_report_size}}</div>
            </ui-modal>
        </div>
       <!-- Toggle Url -->
        <ui-modal hide-footer type="small"
           :show.sync="showUrl">
           <div  slot="header">
               <div class="header-url">
                <img class="url-icon-color" src="../image/icon-url_blue.png" alt="icon"><span class="url-modal-header-text">チャットを招待できます。</span>
<!--                    <div class="row">
                       <div class="col-sm-2">
                           <img class="url-icon-color" src="../image/icon-url_blue.png" alt="icon">
                       </div>
                       <div class="col-sm-10">
                          <h1 class="url-modal-header-text">チャットを招待できます。</h1>
                       </div>
                   </div> -->
               </div>
           </div>
           <div class="url-link">
               <!-- <p>{{shareLink.text}}</p> -->
               <input class="url-link-text" type="text" readonly :value="shareLink.text" id="sharelinkurl"/>
              
           </div>
             <div class="url-button">
               <ui-button color="success" class="sharelinkButton" data-clipboard-target="#sharelinkurl" @click ="toggleSnackbar">URLをコピー</ui-button>
           </div>
            
       </ui-modal>
       <ui-snackbar-container :position="position" :queue-snackbars="queueSnackbars" style="z-index: 1000;"></ui-snackbar-container>
        <staff-dashboard-footer-nav
                :show-toolbar="showToolbar"
                :display-sales-memo="false"
                :display-doc-share="false"
                :display-annotation="false"
                :display-client-upload="true"
                v-on:redirect-to-dashboard="endMeetingFn"
                v-on:toggle-vid-share="toggleVidShare"
                v-on:toggle-url="toggleUrl"
                v-on:toggle-screen-share="screenShare"
                v-on:toggle-annotation="toggleAnnotation"
                v-on:toggle-note-share="toggleNoteShare"
                v-on:toggle-doc-share="toggleDocShare"
                v-on:client-upload-file="clientUpload"
                v-on:toggle-webcam="toggleWebcam"
                v-on:toggle-subtitle="toggleSubtitle"
                v-ref:staff-dashboard-footer-nav
                >
        </staff-dashboard-footer-nav>
         <!-- <ui-snackbar-container :position="center"></ui-snackbar-container> -->
    </div>
</template>
<script>
import PeerConnect from './common/PeerConnect.vue';
import UiImageGallery from './common/UiImageGallery.vue';
import UiResizeable from './common/UiResizeable.vue';
import ThankYou from './common/ThankYou.vue';
import browser from '../js/browser_detect.js';
import errorLogs from '../js/error_logs.js';
import DraggableDirective from '../directives/DraggableDirective.js';
import StaffSidebar from './staff/partials/StaffSidebar.vue';
import FilePreview from './common/FilePreview.vue';
import {UiAlert, UiSnackbar, UiProgressLinear} from 'keen-ui';
import OnclickToFrontDirective from '../directives/OnclickToFrontDirective.js';
import StaffDashboardFooterNav from './staff/partials/StaffDashboardFooterNav.vue';
// import StaffDocShare from './staff/partials/StaffDocShare.vue';
import {APP_URL, PEERJS_API_KEY, PEERJS_HOST, profile , doc} from '../js/config.js';
import helper from '../js/helpers.js';
import {CLIENT_UPLOAD, CLIENT_EMPTY_DOCUMENTS} from '../js/api_routes.js';
import Vue, {router} from '../js/app.js';
import Cookie from 'js-cookie';
import {tinymceConf} from "../js/config";
import isMobile from '../js/isMobileDetect.js';
export default {
    data() {
        return {
            isUsingTemasys: false,
            screenLoaded: false,
            isMaximized: false,
            command: false,
            isRecord: false,
            vidSize:false,
            sortedPages: [],
            checkDataConInterval: '',
            isScreensharing:{
                  active: false,
                  started: false,
                  client: null
            },
            docClient: {
                file: '',
                size: ''
            },
            shuffled_id:this.$route.params.string,
            videoUserFeed:{
                position: 'relative !important',
                zIndex: '10 !important',
                float:'right',
                maxWidth: '25%',
                display: 'inline-block',
                border: '',
            },
            pointerFordoc: {
                doctouch: false,
                top: 100,
                left: 200,
                opacity: 1,
                position: 'absolute'
            },
            mainCam: {
                id: '',
                video: '',
                audio: '',
                isSet: false
            },
            queueSnackbars: false,
            position:'center',
            subtitle:[],
            newClientQueue: [],
            primaryCam: '',
            childCams: [],
            staffChildCams: [],
            clients: [],
            APP_URL,
            shareLink: {
                isSet: false,
                text: '',
                textBoxLink: '',
                validated: false,
                default: true
            },
            sharelinkChecker: '',
            meeting_key: {
                    id: '',
                    isRemoved: false
            },
            clientFileSize:[],
            eventPrefix: 'staffdocshare:',
            file: {},
            error: false,
            error_message: null,
            loading: {
                file_upload: false,
                submit: false
            },
            peer_api_key: PEERJS_API_KEY,
            peer_host: PEERJS_HOST,
            peer_id: null,
            main: {
                progress: 0,
                loading: true
            },
            connect: {
                loaded: false
            },
            staffProfile: profile,
            docsList: [],
            endMeeting: false,
            notes: '',
            vidShareVisible: true,
            show: {
                share_doc_modal: false,
                share_clientdoc_modal: false,
                share_notes_modal: false,
                modal1: false,
                disable: true,
                uploaderror: false,
                url: false,
                share_voice_report: false,
            },
            clientupload: false,
            showUrl: false,
            docLink: '',
            docSize: '',
            notesLink: '',
            notesSize: '',
            voice_report_size: '',
            pages: [],
            mouseCoordinates: {
                top: 0,
                left: 0,
                doctouch: false,
                onScreenHeight: 0,
                onScreenWidth: 0,
                percentageHeight: 0,
                percentageWidth: 0
            },
            ownMouseCoordinates: {
                top: 0,
                left: 0,
                doctouch: false,
                percentageHeight: 0,
                percentageWidth: 0
            },
            ownDocCoord: {
                top: 0,
                left: 0,
                doctouch: false,
                onScreenHeight: 0,
                onScreenWidth: 0,
                percentageHeight: 0,
                percentageWidth: 0
            },
            vidShare: {
                disabled: false,
                coordinates: {
                    top: '0px',
                    left: '0px'
                }
            },
            meetingNotesTrue:{
                top:'',
                left:'',
                width : '',
                height:''
            },
            noteSubtitle:{
                top:'',
                left:'',
                width : '',
                height:'',
                disabled: false
            },
            parentProfile:{
                name:''
            },
            noteSubtitleWidth:0,
            noteSubtitleHeight:0,
            subtitleTopPosition:0,
            subtitleLeftPosition:0,
            meetingNotesWidth:0,
            meetingNotesHeight:0,
            notesTopPosition:0,
            notesLeftPosition:0,
            memoStyles: {
                top: (window.innerHeight - 800) > 0 ? window.innerHeight - 800 : 26,
                height: (window.innerHeight - 800) > 0 ? 300 : window.innerHeight / 4
            },
            noteShare: {
                disabled: false
            },
            video_enabled: true,
            showToolbar: false,
            notesPositionTop: 20,
            // notesPositionTop: window.innerHeight - 631,
            colorPass: [],
            convertRequestCtr: 0,
            clientDoc: [],
            clientDocUrls: {
                documents: [],
                docpages: []
            },
            uploadSuccess: false,
            getSessionId: [],
            errorCompatibility: false,
            toggleFirstChild:'',
            toggleSecondChild:'',
            reservedRequest: true,
            permission: false,
            annotation: '',
            initialMousemove : true,
            initialDocmousemove : true,
            loading: false,
            btnLoad: true,
            progress: 0,
            progressInterval: null,
            timeStatusInterval: 1000,
            convertSuccess: false,
            errorConvert: false,
            boxEls: [],
            action: '',
            duration: 5,
            actionColor: 'accent',
            message: '',
            parsedDateTime: '',
        }
    },
    methods: {
      isNoteActivePlaceholder(id, textHolder) {
        try {
            let textContent = document.getElementById(id);
            if (textContent.children.length > 0) {
                let htmlContent = textContent.children[0];
                if(htmlContent.innerText === '' ||
                    htmlContent.innerHTML === '<br>' ||
                    htmlContent.innerHTML === '<br data-mce-bogus="1">') {
                    textContent.setAttribute('placeholder', textHolder);
                } else {
                    textContent.setAttribute('placeholder', '');
                }
            } else {
                textContent.setAttribute('placeholder', textHolder);
            }
        }catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action'};
            let functionName = 'SecondClientConnect:isNoteActivePlaceholder';
            staff.checkError(errorStats, functionName);
        }
      },
      isNoteActive(active,id){
        
        try{
          let elmnts = [id+'-footer',id+'-resizeIcon'];
          let cond = ((isMobile.phone || isMobile.tablet));
          let elm = document.getElementById(id);

          if(active && elm.classList.contains('active-note')) return false;

          if(cond){
            let toolbarSize = (active) ? -40 : 40;

            for (let i= elmnts.length;i--;) {
              let style = window.getComputedStyle(document.getElementById(elmnts[i]));
              let bottom = style.getPropertyValue('bottom').replace('px','');

              document.getElementById(elmnts[i]).setAttribute( 'style' ,"bottom:"+(parseInt(bottom)+(toolbarSize))+"px !important");

              if(active) elm.classList.add('active-note');
              else elm.classList.remove('active-note');

            }
          }
          
        }catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action'};
          let functionName = 'SecondClientConnect:initTinyMCE';
          helper.catchError(errorStats, functionName);
        }
      },
      initTinyMCE(){
        try {
          let cond = ((isMobile.phone || isMobile.tablet));
          let tinymceConfig = cond ? tinymceConf.mobile : tinymceConf.desktop;
          let saveNotes = this;

          tinymceConfig.init_instance_callback = function(editor){
            editor.on('NodeChange', function (e) {
              saveNotes.notesCheck();
            });
          };

          tinymceConfig.selector = '#notesID';
          if(cond) {
            tinymceConfig.fixed_toolbar_container += "-notesID";
          }else{
            document.getElementById("custom-toolbar-mce-notesID").outerHTML = '';
          }
          
          tinymce.init(tinymceConfig).then(function(e){});

        }catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action'};
          let functionName = 'SecondClientConnect:initTinyMCE';
          helper.catchError(errorStats, functionName);
        }
      },
        sortArr(list,key){
            try {
                let comp = function compare(a, b) {
                    a = a[key];
                    b = b[key];
                    let type = (typeof(a) === 'string' || typeof(b) === 'string') ? 'string' : 'number';
                    let result;
                    if (type === 'string') result = a.localeCompare(b);
                    else result = a - b;
                    return result;
                }
                return list.sort(comp);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Sorting Array'}
                let functionName = 'SecondClientConnect:sortArr';
                helper.catchError(errorStats, functionName);
            }
        },
        thankyou() {
            try {
                router.go({ name: 'thank-you' })

                setTimeout(function () {
                    window.location.reload()
                }, 1000);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Redirecting to suggestion page'}
                let functionName = 'SecondClientConnect:thankyou';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleSnackbar(){
            try {
                this.showUrl = false
                // this.$refs.staffDashboardFooterNav.url = false
                this.$broadcast('ui-snackbar::create',{
                    message: 'URLが正常にコピーされました',
                    duration: 3000
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling Snack bar'}
                let functionName = 'SecondClientConnect:toggleSnackbar';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleUrl(){
            try {
                this.showUrl = true;
                this.show.share_notes_modal = false;
                this.show.share_doc_modal = false;
                this.show.share_clientdoc_modal = false;
                this.show.share_voice_report = false;
                this.show.modal1 = false;
                this.show.url = false;
                this.show.uploaderror = false;
                this.clientupload = false;  
                // this.$refs.staffDashboardFooterNav.url = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e,'ErrorMsgFromPage': 'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling URL'}
                let functionName = 'SecondClientConnect:toggleUrl';
                helper.catchError(errorStats, functionName);
            }
        },
        notesCheck(){
            try {
                this.notes = document.getElementById("notesID").value;
                let x = this.notes
                let id = this.peer_id
                let conn = this.getMeetingDataConnection()
                if(typeof conn != 'undefined') {
                    conn.connection.send({key: 'notes', data: x})
                    conn.connection.send({key: 'otherClientNotes', data: x, id: id})
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Checking of Notes'}
                let functionName = 'SecondClientConnect:notesCheck';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleWebcam(){
            try {
                this.$refs.peerConnect.toggleOwnWebCam()
                for ( let key in this.childCams ){
                    let index = this.getDataConnection(this.childCams[key].id)
                    index.connection.send({key: 'toggleClientWebcam', data: this.peer_id})
                }
                let staff = this.getMeetingDataConnection()
                staff.connection.send({key: 'toggleClientWebcam', data: this.peer_id})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling webcam'}
                let functionName = 'SecondClientConnect:toggleWebcam';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleVoiceRecord(){
            try {
                if(/Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) {
                    this.isRecord = this.isRecord ? false : true
                    this.command = !this.isRecord ? false : true
                } else {
                    this.$broadcast('ui-snackbar::create', {
                        message: '現在この機能はChromeでのみ使えます。',
                        action: this.action,
                        actionColor: this.actionColor,
                        duration: this.duration * 1000
                    });
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling Voice Recorder'}
                let functionName = 'SecondClientConnect:toggleVoiceRecord';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleSubtitle(){
            try {
                this.$refs.staffDashboardFooterNav.subtitle = this.isBoxHiding('.subtitle') ? false : true
                if(this.fileViewerOnTop() && this.isBoxHiding('.subtitle') && this.isAlreadyOnTop('.subtitle') && window.innerWidth > 480) {
                    this.forceOnTop('.subtitle');
                } else {
                    this.toggleElement(this.$els.subTitle)
                    if(this.isBoxHiding('.subtitle') && window.innerWidth > 480) {
                        this.forceOnTop('.subtitle');
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling Subtitle'}
                let functionName = 'SecondClientConnect:toggleSubtitle';
                helper.catchError(errorStats, functionName);
            }
        },
        setLink(id){
            try {
                if(!this.shareLink.isSet) {
                    this.shareLink.text = id
                    this.shareLink.textBoxLink = id
                    this.shareLink.isSet = (!!id) ? true : false;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Setting of Share Link'}
                let functionName = 'SecondClientConnect:setLink';
                helper.catchError(errorStats, functionName);
            }
        },
        uploadValidation(filename){  //upload validation for file sharing child
            try {
                let filext = this.file.name.split('.').pop();
                let fileTypes = ['pdf','doc','docx','pptx','xlsx','xls','ppt']
                let ctr = 0;
                for(var i = 0; i < fileTypes.length; i++) {
                    if(filext === fileTypes[i]) {
                        ctr = 1;
                    }
                }
                if(ctr == 0) {
                    this.show.alert = true;
                    filename.value = '';
                    this.show.disable = true;
                    this.show.uploaderror = true;
                } else {
                    this.show.disable = false;
                    this.show.uploaderror = false;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Validating the upload'}
                let functionName = 'SecondClientConnect:uploadValidation';
                helper.catchError(errorStats, functionName);
            }
        },
        convertDocument(){
            try {
                this.convertRequestCtr++
                // this.loading.submit = true
                this.loading = true
                // this.btnLoad = false
                let myfile = this.file
                let myId = this.peer_id
                let mydata = new FormData()
                let filext = myfile.name.split('.').pop()
                let name = myfile.name.replace('.' + filext, '')
                mydata.append('filename', name)
                mydata.append('convert_ctr', this.convertRequestCtr)
                mydata.append('file', myfile)
                mydata.append('client_id', myId)
                Vue.http.post(`${CLIENT_UPLOAD}`, mydata)
                .then(response => {
                    this.clientDoc = []
                    for(let key in response.data.image_url){
                        this.clientDoc.push({
                            id: key,
                            path: response.data.image_url[key],
                            annotation: response.data.image_title,
                            canvasImageDataUrl: '',
                            hasImage: false,
                        })
                        this.clientDocUrls.docpages.push(response.data.docpage_url[key])
                    }
                    this.clientDocUrls.documents.push(response.data.document_url)
                    // this.progress = 100
                    // this.timeStatusInterval = 10
                    this.convertSuccess = true
                    helper.progressConvertInterval(100,this,this.clientFileSize)
                }).catch(e => {
                    this.errorConvert = true
                    helper.progressConvertInterval(0,this,this.clientFileSize)
                    let determinateProgress = document.querySelector(".ui-progress-linear-determinate");
                    let progressError = document.getElementById("progressLinear");
                    progressError.style.background = "red";
                    determinateProgress.style.background = "#de2828";
                    clearInterval(this.progressInterval)
                    let errorStats = {'status':e.status,'ErrorMsgFromPage':e.body.message}
                    let functionName = 'Convert Document';
                    let catchError = helper.catchError(errorStats, functionName);
                });
                this.btnLoad = false
                helper.progressConvertInterval(this.timeStatusInterval,this,this.clientFileSize)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Converting a document'}
                let functionName = 'SecondClientConnect:convertDocument';
                helper.catchError(errorStats, functionName);
            }
        },
        shareFileToAll(){
            try {
                this.clientupload = false;
                let conn = this.getMeetingDataConnection()
                let myfile = this.file
                let myId = this.peer_id
                let blob = new Blob([myfile], {type: myfile.type});
                conn.connection.send({key: 'childFile', data: {file: myfile, blob: blob, name: myfile.name, type: myfile.type, sender: myId}})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Sharing a file to all'}
                let functionName = 'SecondClientConnect:shareFileToAll';
                helper.catchError(errorStats, functionName);
            }
        },
        viewFileToParent(){
            try {
                this.$refs.imageGallery.refreshCtr()
                this.pages = []
                this.pages = this.clientDoc
                if (this.pages.length != 0) {
                    this.show.file_view_modal = false
                    document.querySelector('.videoFeedCon').style.top = '2%';
                    document.querySelector('.videoFeedCon').style.left = '1%';
                }
                let conn = this.getMeetingDataConnection()
                conn.connection.send({key: 'childFileView', data: {data:this.pages, fromClient: false}})
                let pages = this.pages
                for ( let key in this.childCams ){
                    let index = this.getDataConnection(this.childCams[key].id)
                    index.connection.send({key: 'childFileView', data: pages})
                }
                this.clientupload = false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Viewing a file to parent'}
                let functionName = 'SecondClientConnect:viewFileToParent';
                helper.catchError(errorStats, functionName);
            }
        },
        getDocLink(doc) {
            try {
                let dateHolder = new Date();
                dateHolder.setMinutes(dateHolder.getMinutes() + 1);
                this.parsedDateTime = [Cookie.get('peerId'), dateHolder.getTime()].join('-');
                return `${APP_URL}/${this.parsedDateTime}/document/${doc.document_id}/download/${doc.file_name}`
                //return `${APP_URL}/document/${doc.document_id}/download/${doc.file_name}`
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting the document link'}
                let functionName = 'SecondClientConnect:getDocLink';
                helper.catchError(errorStats, functionName);
            }
        },
        uploadClicked(e){
            e.preventDefault()
        },
        changeDocument() {
            try {
                this.loading = false // Hide the Progress Bar
                this.clientFileSize = this.bytesToSize(parseInt(this.$refs.uploadFile.value.size))
                let filename = this.$refs.uploadFile.value.name
                this.file = this.$refs.uploadFile.value
                // filename = `${filename.replace(/.[^.]+$/,'').substring(0,10)}.${filename.replace(/^.*\./,'..')}`
                filename = `${filename.replace(/.[^.]+$/,'').substring(0,5)}.${filename.replace(/^.*\./,'..')}`
                this.$refs.uploadDocument.text = `FILE: ${filename}`
                this.uploadValidation(filename);

                // Change the settings in progress to default
                this.errorConvert = false
                this.convertSuccess = false
                this.progress = 0
                this.btnLoad = true
                let determinateProgress = document.querySelector(".ui-progress-linear-determinate").style.background = "";
                let progressError = document.getElementById("progressLinear").style.background = "";
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Changing the document'}
                let functionName = 'SecondClientConnect:changeDocument';
                helper.catchError(errorStats, functionName);
            }
        },
        clientUpload(){
            try {
                // this.show.modal1 = true;
                this.clientupload = true;
                this.show.share_doc_modal = false;
                this.show.share_notes_modal = false;
                this.show.share_clientdoc_modal = false;
                this.show.share_voice_report = false;
                this.show.modal1 = false;
                this.show.url = false;
                this.show.uploaderror = false;
                this.showUrl = false;
                // this.$refs.staffDashboardFooterNav.file = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Client Upload'}
                let functionName = 'SecondClientConnect:clientUpload';
                helper.catchError(errorStats, functionName);
            }
        },
        bytesToSize(bytes) {
            try {
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                if (bytes == 0) return '0 Byte';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
            }  catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Converting bytes to size'}
                let functionName = 'SecondClientConnect:bytesToSize';
                helper.catchError(errorStats, functionName);
            }
        },
        screenShare(){
            try {
                var isMobile = /iPhone|iPad|iPod|Android|Trident/i.test(navigator.userAgent);
                if (isMobile) {
                    this.$broadcast('ui-snackbar::create', {
                        message: 'モバイルデバイスでは画面共有機能をサポートしておりません。PCでご利用ください。',
                        duration: 3000
                    });
                } else {
                    this.$refs.peerConnect.screenShare();
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Triggering a screen share'}
                let functionName = 'SecondClientConnect:screenShare';
                helper.catchError(errorStats, functionName);
            }
        },
        beforeUnload() {
        },
        mouseMove(evt) {
            try {
                if(!this.ownDocCoord.doctouch){
                    let viewS = document.getElementById('viewerScreen');
                    this.ownMouseCoordinates.top = evt.clientY
                    this.ownMouseCoordinates.left = evt.clientX

                    if(!!viewS){
                        this.ownMouseCoordinates.onScreenHeight = viewS.clientHeight
                        this.ownMouseCoordinates.onScreenWidth  = viewS.clientWidth
                    }

                    this.ownMouseCoordinates.percentageHeight = (0 >= this.ownMouseCoordinates.top || this.ownMouseCoordinates.onScreenHeight == 0) ? 0 : parseFloat(this.ownMouseCoordinates.top / this.ownMouseCoordinates.onScreenHeight)
                    this.ownMouseCoordinates.percentageWidth  = (0 >= this.ownMouseCoordinates.left || this.ownMouseCoordinates.onScreenWidth == 0) ? 0 : parseFloat(this.ownMouseCoordinates.left / this.ownMouseCoordinates.onScreenWidth)
                }
                let ownMouse = (this.ownDocCoord.doctouch) ? this.ownDocCoord : this.ownMouseCoordinates
                let conn = this.getMeetingDataConnection()
                if(typeof conn != 'undefined') {
                    let myId = this.peer_id
                    conn.connection.send({key: 'mouseMove', data: {data:ownMouse, id: myId }})
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Tracking the mouse movement'}
                let functionName = 'SecondClientConnect:mouseMove';
                helper.catchError(errorStats, functionName);
            }
        },
        updatePointer() {
            try {
                if(this.connect.loaded && !this.endMeeting) {
                    if(this.mouseCoordinates.doctouch){
                        this.$els.pointer.style.opacity = 0
                        this.pointerFordoc = {
                            doctouch: true,
                            opacity: 1,
                            position: 'absolute',
                            top: (this.ownDocCoord.onScreenHeight * this.mouseCoordinates.percentageHeight) - 10 + 'px',
                            left: (this.ownDocCoord.onScreenWidth * this.mouseCoordinates.percentageWidth) - 15 + 'px'
                        }
                    } else {
                        this.pointerFordoc = {
                            doctouch: false,
                            opacity: 0,
                            position: 'absolute',
                            top: (this.ownDocCoord.onScreenHeight * this.mouseCoordinates.percentageHeight) - 10 + 'px',
                            left: (this.ownDocCoord.onScreenWidth * this.mouseCoordinates.percentageWidth) - 15 + 'px'
                        }
                        this.$els.pointer.style.opacity = 1
                        this.$els.pointer.style.top = `${(this.ownMouseCoordinates.onScreenHeight * this.mouseCoordinates.percentageHeight) - 10}px`
                        this.$els.pointer.style.left = `${(this.ownMouseCoordinates.onScreenWidth * this.mouseCoordinates.percentageWidth) - 15}px`
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Updating mouse pointer'}
                let functionName = 'SecondClientConnect:updatePointer';
                helper.catchError(errorStats, functionName);
            }
        },
        getMeetingDataConnection() {
            try {
                return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {label: 'meeting', type: 'data'})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting meeting data connection'}
                let functionName = 'SecondClientConnect:getMeetingDataConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        getMeetingMediaConnection(peer_key) {
            try {
                return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {id: peer_key, type: 'media'})   
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting Media connection'}
                let functionName = 'SecondClientConnect:getMeetingMediaConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        relayNotes() {
            // let conn = this.getMeetingDataConnection()
            // if(typeof conn != 'undefined') {
            //     conn.connection.send({key: 'notes', data: this.notes})
            //      conn.connection.send({key: 'otherClientNotes', data: this.notes, id:this.peer_id})
            // }
        },
        expandVidShare() {
            try {
                this.vidShare.disabled = this.vidShare.disabled ? false : true
                if (this.vidShare.disabled) {
                    this.vidShare.coordinates.top = this.$els.vidShare.style.top
                    this.vidShare.coordinates.left = this.$els.vidShare.style.left
                    this.$els.vidShare.style.top = '0'
                    this.$els.vidShare.style.left = '0'
                    this.$els.vidShare.style.maxWidth = '100%'
                    this.$els.vidShare.style.width = '100%'
                    this.videoUserFeed.maxWidth = '15%'
                    this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight + 'px';
                    document.querySelector('.protoConnectContainer').style.position = 'absolute';
                    document.querySelector('.protoConnectContainer').style.zIndex = 1;
                    document.querySelector('.zoom_out_screen').style.right = '10px';
                    document.querySelector('.zoom_out_screen').style.top = '10px';
                    document.querySelector('.videoFeedCon').style.zIndex = 110;
                    document.querySelector('.video-user-feed').style.width = '15%';
                    document.querySelector('.videoFeedCon').style.height = '100%';
                    document.querySelector('.footer').style.borderTop = '1px solid #3c9371';
                    this.childCams.forEach(function(elements){
                        elements.style.width = '15%'
                    })
                    let elem = document.querySelector('.own-feed')
                    elem.classList.add('minified-cams')
                    this.$refs.peerConnect.addClasses = true;
                    this.vidSize = true;

                    if(window.innerWidth >= 1024) {
                        this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 150 + 'px';
                        if(window.innerWidth >= 1280) {
                            this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 153 + 'px';
                        }
                    }
                } else {
                    this.videoUserFeed.maxWidth = '25%'
                    this.$els.vidShare.style.top = this.vidShare.coordinates.top
                    this.$els.vidShare.style.left = this.vidShare.coordinates.left
                    this.$els.vidShare.style.transform = 'none'
                    this.$els.vidShare.style.maxWidth = '600px'
                    this.$els.vidShare.style.width = '480px'
                    this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = '360px'
                    document.querySelector('.protoConnectContainer').style.position = 'absolute';
                    document.querySelector('.protoConnectContainer').style.zIndex = 0;
                    document.querySelector('.zoom_out_screen').style.right = '-15px';
                    document.querySelector('.zoom_out_screen').style.top = '-20px';
                    document.querySelector('.videoFeedCon').style.zIndex = 45;
                    document.querySelector('.videoFeedCon').style.height = 'auto';
                    document.querySelector('.footer').style.borderTop = '';
                    document.querySelector('.video-user-feed').style.width = '25%';
                    this.childCams.forEach(function(elements){
                        elements.style.width = '25%'
                    })
                    let elem = document.querySelector('.own-feed')
                        elem.classList.remove('minified-cams')
                    this.$refs.peerConnect.addClasses = false;
                    this.vidSize = false;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Expanding Video Sharing'}
                let functionName = 'SecondClientConnect:expandVidShare';
                helper.catchError(errorStats, functionName);
            }
        },
        fileViewerOnTop(){
            try {
                const galleryImg = document.querySelector('.ui-image-gallery .imgResponsive');
                var isOnTop = false;
                if(!!galleryImg) {
                    isOnTop = galleryImg.classList.value.includes('onTop');
                } else {
                    isOnTop = true;
                }
                return isOnTop;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Checking if file viewer is on top'}
                let functionName = 'SecondClientConnect:fileViewerOnTop';
                helper.catchError(errorStats, functionName);
            }
        },
        forceOnTop(selector){
            try {
                const selectorClass = document.querySelector(selector).classList;
                selectorClass.add('forceOnTop');
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Forcing element to be on top'}
                let functionName = 'SecondClientConnect:forceOnTop';
                helper.catchError(errorStats, functionName);
            }
        },
        isAlreadyOnTop(selector){
            try {
                return !document.querySelector(selector).classList.value.includes('forceOnTop');
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Checking of an element if it is on top'}
                let functionName = 'SecondClientConnect:isAlreadyOnTop';
                helper.catchError(errorStats, functionName);
            }
        },
        isBoxHiding(selector){
            try {
                let selectorClass = document.querySelector(selector).class
                return (!!selectorClass) ? !selectorClass.search(/hide/) : false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Checking of the element if it is hiding'}
                let functionName = 'SecondClientConnect:isBoxHiding';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleVidShare() {
            try {
                this.$refs.staffDashboardFooterNav.vidshare = this.isBoxHiding('.videoFeedCon') ? false : true

                if(this.fileViewerOnTop() && this.isBoxHiding('.videoFeedCon') && this.isAlreadyOnTop('.videoFeedCon')  && window.innerWidth > 480){
                    this.forceOnTop('.videoFeedCon');
                } else {
                    this.toggleElement(this.$els.vidShare);
                    if(this.isBoxHiding('.videoFeedCon') && window.innerWidth > 480){
                        this.forceOnTop('.videoFeedCon');
                    }
                }
            }  catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling Video Sharing'}
                let functionName = 'SecondClientConnect:toggleVidShare';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleNoteShare() {
            try {
                this.$refs.staffDashboardFooterNav.note = this.isBoxHiding('.meetingNotes') ? false : true

                if(this.fileViewerOnTop() && this.isBoxHiding('.meetingNotes') && this.isAlreadyOnTop('.meetingNotes') && window.innerWidth > 480){
                    this.forceOnTop('.meetingNotes');
                } else {
                    this.toggleElement(this.$els.noteShare)
                    if(this.isBoxHiding('.meetingNotes') && window.innerWidth > 480){
                        this.forceOnTop('.meetingNotes');
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling note share'}
                let functionName = 'SecondClientConnect:toggleNoteShare';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleElement(el) {
            try {
                let has_el = helper.hasClass(el, 'hide')
                if(has_el) {
                    el.classList.remove('hide')
                } else {
                    el.classList.add('hide')
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggling of element'}
                let functionName = 'SecondClientConnect:toggleElement';
                helper.catchError(errorStats, functionName);
            }
        },
        downloadDoc() {
            try {
                this.show.share_doc_modal = false
                helper.downloadFile(this.docLink);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Downloaing a certain document'}
                let functionName = 'SecondClientConnect:downloadDoc';
                helper.catchError(errorStats, functionName);
            }
        },
        downloadClientDoc(){
            try {
                this.show.share_clientdoc_modal = false
                const e = document.createEvent('MouseEvents'),
                a = document.createElement('a');
                a.download = this.docClient.file.name;
                a.href = window.URL.createObjectURL(this.docClient.file);
                a.dataset.downloadurl = ['text/json', a.download, a.href].join(':');
                e.initEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                a.dispatchEvent(e);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Downloading a document from the client'}
                let functionName = 'SecondClientConnect:downloadClientDoc';
                helper.catchError(errorStats, functionName);
            }
        },
        downloadNotes() {
            try {
                helper.downloadNotes(this.notesLink);
                this.show.share_notes_modal = false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Downloading notes'}
                let functionName = 'SecondClientConnect:downloadNotes';
                helper.catchError(errorStats, functionName);
            }
        },
        finalClosing(){
            try {
                this.endMeeting = true
                this.connect.loaded = false
                this.showToolbar = false
                document.querySelector('.share_link').style.display = 'none';
                if(this.$refs.peerConnect.current_stream) {
                    this.$refs.peerConnect.current_stream.getTracks()[0].stop()
                }
                this.$refs.peerConnect.screen_share_stream = false
                this.$refs.peerConnect.stopCurrentStream()
                this.$refs.peerConnect.peer.destroy()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Processing of closing the connection'}
                let functionName = 'SecondClientConnect:finalClosing';
                helper.catchError(errorStats, functionName);
            }
        },
        clearDocuments(){
            try {
                let docs = {documents: this.clientDocUrls.documents, docpages: this.clientDocUrls.docpages }
                Vue.http.post(`${CLIENT_EMPTY_DOCUMENTS}`, docs)
                .then(response => {
                    if (this.endMeeting) {
                        this.thankyou() // To Suggestion Page
                    }
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Clearing of documents'}
                let functionName = 'SecondClientConnect:clearDocuments';
                helper.catchError(errorStats, functionName);
            }
        },
        onbeforeunload(evt) {
            try {
                // evt = evt || window.event
                // alert(evt)
                if (evt) {
                    evt.preventDefault();
                    this.closeTab()
                    // this.closeTab()
                    setTimeout(function(){
                        evt.returnValue = 'Ending Meeting'
                        return ''
                    }, 10000);
                    return ''
                } else {
                    this.closeTab();
                    setTimeout(function(){
                        /** do nothing */
                    }, 10000);
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Ending the meeting on closing the tab'}
                let functionName = 'SecondClientConnect:onbeforeunload';
                helper.catchError(errorStats, functionName);
            }
        },
        closeTab(){
            try {
                let conn = this.getMeetingDataConnection()
                if(typeof conn != 'undefined'){
                    conn.connection.send({key: 'endingChild', data: this.peer_id});
                }
                for ( let key in this.childCams ){
                    let index = this.getDataConnection(this.childCams[key].id)
                    if(typeof index != 'undefined'){
                        index.connection.send({key: 'endingChild', data: this.peer_id})
                    }
                }

                document.removeEventListener('beforeunload', this.onbeforeunload)
                document.removeEventListener('mousemove', this.mouseMove)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Processing when closing the tab'}
                let functionName = 'SecondClientConnect:closeTab';
                helper.catchError(errorStats, functionName);
            }
        },
        endMeetingFn() {
            try {
                Cookie.remove('peerId');
                Cookie.remove('ParentNameHost')
                // if(!this.endMeeting) {
                    let conn = this.getMeetingDataConnection()

                    if(!!conn) {
                        console.log('sending from: ', conn)
                        console.log('sending data: ', JSON.stringify({key: 'endingChild', data: this.peer_id}))
                        conn.connection.send({key: 'endingChild', data: this.peer_id});
                    }

                    for ( let key in this.childCams ){
                        let index = this.getDataConnection(this.childCams[key].id)
                        if(typeof index != 'undefined'){
                            index.connection.send({key: 'endingChild', data: this.peer_id})
                        }
                    }
                    this.endMeeting = true
                    this.connect.loaded = false
                    this.showToolbar = false
                    document.querySelector('.share_link').style.display = 'none';
                    // commented for changed method of ending
                    // this.finalClosing()
                // }

                document.removeEventListener('beforeunload', this.onbeforeunload)
                document.removeEventListener('mousemove', this.mouseMove)
                this.clearDocuments()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Closing the connection'}
                let functionName = 'SecondClientConnect:endMeetingFn';
                helper.catchError(errorStats, functionName);
            }
        },
        endMeetingPopup(){
            try {
                this.$broadcast('ui-snackbar::create', {
                    message: this.message,
                    action: this.action,
                    actionColor: this.actionColor,
                    duration: this.duration * 1000
                });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Showing End Meeting Popup'}
                let functionName = 'SecondClientConnect:endMeetingPopup';
                helper.catchError(errorStats, functionName);
            }
        },
        downloadConversation(){ // Auto Download Conversation after ending the meeting
            try {
                let finalConvo = ''
                if (this.subtitle){
          
                    for(let key in this.subtitle){
                         let convo = this.decodeEntities(this.subtitle[key])
                         finalConvo += convo+'\r\n'
                    }
            
                    let newElement = document.createElement('a')
                    newElement.setAttribute("id","downloadedConversation")
                    newElement.setAttribute('href','data:text/plain;charset=utf-8,'+ encodeURIComponent(finalConvo))
                    newElement.setAttribute('download','Mee2box - Conversation.txt')
                    newElement.style.display='none'
                    document.body.appendChild(newElement)
                    newElement.click()
                    document.body.removeChild(newElement)
                }
                this.show.share_voice_report = false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Downloading the conversation'}
                let functionName = 'SecondClientConnect:downloadConversation';
                helper.catchError(errorStats, functionName);
            }
        },
        decodeEntities(str){ // Decode Html Values inside an array to pass to the arguments
            try {
                if(str && typeof str === 'string'){
                     str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '')
                     str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '')
                }
                return str
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Decoding Entities'}
                let functionName = 'SecondClientConnect:decodeEntities';
                helper.catchError(errorStats, functionName);
            }
        },
        callFailCB() {
            try {
                this.video_call_failed = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Video Calling Failed'}
                let functionName = 'SecondClientConnect:callFailCB';
                helper.catchError(errorStats, functionName);
            }
        },
        getVideoConn(id){
            try {
                return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {
                            id: id,
                            label: 'video-stream',
                            type: 'media'
                        })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting video connection'}
                let functionName = 'SecondClientConnect:getVideoConn';
                helper.catchError(errorStats, functionName);
            }
        },
        getAudioConn(id){
            try {
                return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {
                            id: id,
                            label: 'audio-stream',
                            type: 'media'
                        })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting audio connection'}
                let functionName = 'SecondClientConnect:getAudioConn';
                helper.catchError(errorStats, functionName);
            }
        },
        getDataConnection(id){
            try {
                return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {
                            id: id,
                            label: 'meeting',
                            type: 'data'
                        })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting data connection'}
                let functionName = 'SecondClientConnect:getDataConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        getConnectionFromID(id){
            try {
                return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {
                            id: id
                        })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting connection from ID'}
                let functionName = 'SecondClientConnect:getConnectionFromID';
                helper.catchError(errorStats, functionName);
            }
        },
        setChildCam(id,Vid,Aud){
            try {
                let added = this.childCams.findIndex(cont => cont.id === id)
                if(added < 0){
                    // let testCon = this.getVideoConn(id)
                    // let testAud = this.getAudioConn(id)
                    // testCon.open = true
                    // Adding new Child Cams
                    let childstyle = {
                            position: 'relative !important',
                            zIndex: '10 !important',
                            float:'right',
                            maxWidth: '25%',
                            display: 'inline-block',
                            border: '',
                            width: '',
                            // background: 'black'
                        }
                    let temp2 = {id: id, video: Vid.src_object, audio: Aud.src_object, open:true, style:childstyle }
                    let vm=this;
                    // setTimeout(function(){
                        vm.childCams.push(temp2)
                    // },2000)
                    // this.childCams.push(temp2)
                } else {
                    this.childCams[added].video = Vid.src_object;
                    this.childCams[added].audio = Aud.src_object;
                    this.childCams[added].open = true;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Setting the child camera'}
                let functionName = 'SecondClientConnect:setChildCam';
                helper.catchError(errorStats, functionName);
            }
        },
        setchildCamBorder(id){
            try {
                let colorConn = this.getDataConnection(id)
                for(let key in this.childCams){
                    if(id == this.childCams[key].id){
                        this.childCams[key].style.border = '2px solid '+colorConn.color
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Setting child camera border'}
                let functionName = 'SecondClientConnect:setChildCamBorder';
                helper.catchError(errorStats, functionName);
            }
        },
        settingMainCam(){
            try {
                let id = this.meeting_key.id
                if(!this.mainCam.isSet){
                    let mainVid = this.getVideoConn(id)
                    let mainAud = this.getAudioConn(id)
                    this.mainCam.id = id
                    this.mainCam.video = mainVid.src_object ? mainVid.src_object : ''
                    this.mainCam.audio = mainAud.src_object ? mainAud.src_object : ''
                    this.mainCam.isSet = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Setting Main Camera'}
                let functionName = 'SecondClientConnect:settingMainCam';
                helper.catchError(errorStats, functionName);
            }
        },
        callClient(id){
            try {
                // alert('callingClient ')
                this.$refs.peerConnect.connectToPeer(id, 'meeting')
                this.$refs.peerConnect.initiateCall(id, false, this.callFailCB)
                // this.$refs.peerConnect.initiateCall(id, true)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Calling client'}
                let functionName = 'SecondClientConnect:callClient';
                helper.catchError(errorStats, functionName);
            }
        },
        addClient(index){
            try {
                let id = this.newClientQueue[index]
                let testVidcon = this.getVideoConn(id)
                let testAud = this.getAudioConn(id)
                let datacon = this.getDataConnection(id)
                console.log('addClient::video', testVidcon)
                console.log('addClient::audio', testVidcon)
                // if(testVidcon != null && testAud != null){
                if(!!testVidcon && !!testAud){
                    this.settingMainCam()

                    this.setChildCam(id,testVidcon,testAud)
                    // Add to Clients
                    this.clients.push(id)
                    // Remove ID from queue
                    let pos = this.newClientQueue.indexOf(id)
                    this.newClientQueue.splice(pos,1)
                    this.checkDataConInterval = setInterval(this.checkDataConConnection(id), 1000)
                }else{
                    this.callClient(id)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Adding a client'}
                let functionName = 'SecondClientConnect:addClient';
                helper.catchError(errorStats, functionName);
            }
        },
        checkDataConConnection(id){
            try {
                let datacon = this.getDataConnection(id)
                if(datacon){
                    this.setchildCamBorder(id)
                    clearInterval(this.checkDataConInterval)
                    // datacon.connection.send({key: 'ChildConnect', data: this.peer_id})
                    this.$refs.peerConnect.addColor(id)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Checking of data connection'}
                let functionName = 'SecondClientConnect:checkDataConConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        setPrimaryCam(){
            try {
                this.primaryCam = this.meeting_key.id
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Setting Primary Camera'}
                let functionName = 'SecondClientConnect:setPrimaryCam';
                helper.catchError(errorStats, functionName);
            }
        },
        addClientQueue(id){
            try {
                let isAdded = this.clients.findIndex(cont => cont.id === id)
                if(isAdded < 0){
                    this.callClient(id)
                    this.newClientQueue.push(id)
                    this.newClientAdded = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding client queue'}
                let functionName = 'SecondClientConnect:addClientQueue';
                helper.catchError(errorStats, functionName);
            }
        },
        getShareLink(){
            try {
                let parentcon = this.getMeetingDataConnection()
                parentcon.connection.send({key: 'sendShareLinktoClient', data: this.peer_id})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Getting the share link'}
                let functionName = 'SecondClientConnect:getShareLink';
                helper.catchError(errorStats, functionName);
            }
        },
        removeClient(id){
            try {
                // Update Client Array
                let oldClient = this.clients.filter((item) => item.id !== id)
                this.clients = oldClient
                // Update ChildCams Array
                let oldChildCams = this.childCams.filter((item) => item.id !== id)
                this.childCams = oldChildCams
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Removing client'}
                let functionName = 'SecondClientConnect:removeClient';
                helper.catchError(errorStats, functionName);
            }
        },
        refreshCam(){
            try {
                let client_id = this.isScreensharing.client
                let vid = this.getVideoConn(client_id)
                if(vid != null){
                    for(let key in this.childCams){
                        if(this.childCams[key].id == client_id){
                            this.childCams[key].video = vid.src_object
                            this.isScreensharing.client = null
                        }
                    }
                } else {
                    let con = this.getDataConnection(this.isScreensharing.client)
                    con.connection.send({key: 'clientRequestSreenshareReconnect', data: this.peer_id})
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Refreshing the camera'}
                let functionName = 'SecondClientConnect:refreshCam';
                helper.catchError(errorStats, functionName);
            }
        },
        speechRecognitionStart(){
            try {
                window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition // Initialize Speech Recognition
                const recognition = new SpeechRecognition()
                recognition.interimResults = true
                recognition.lang = 'ja'
                    
                recognition.addEventListener('result',e => {
                const transcript = Array.from(e.results)
                        .map(result=>result[0])
                        .map(result=>result.transcript)
                        .join('')

                        const poopScript = transcript.replace(/poop|poo|shit|dump/gi, '💩')
                        if (e.results[0].isFinal) {
                            for(let key in this.staffChildCams){
                                
                                if(this.peer_id == this.staffChildCams[key]){
                                    let con = this.getMeetingDataConnection()
                                    let ownContent = [poopScript,this.peer_id]
                                    this.pushSpeechToArray(ownContent)
                                }
                            }
                        }
                })
                recognition.addEventListener('end',recognition.start)
                recognition.start()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Speech Recognition Start'}
                let functionName = 'SecondClientConnect:speechRecognitionStart';
                helper.catchError(errorStats, functionName);
            }
        },
        pushSpeechToArray(ownContent){
            try {
                if(this.command){
                    let con = this.getMeetingDataConnection()
                    if (typeof con != 'undefined'){
                    con.connection.send({key:'sendAudioTranscription', data: ownContent})
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Pushing speech to array'}
                let functionName = 'SecondClientConnect:pushSpeechToArray';
                helper.catchError(errorStats, functionName);
            }
        },
        checkShareLink(urlShareLink,vm,sendLink){
            try {
                if (urlShareLink) {
                    urlShareLink = urlShareLink
                    vm.setLink(urlShareLink)
                    clearInterval(vm.sharelinkChecker);
                } else {
                    let id = vm.peer_id
                    sendLink.connection.send({key: 'sendShareLinktoClient', data: id });
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking the share link'}
                let functionName = 'SecondClientConnect:checkShareLink';
                helper.catchError(errorStats, functionName);
            }
        },
        handleResize(){
            try {
                if(window.innerWidth >= 1024){
                    this.$refs.peerConnect.$els.peersVideoContainer == null ? null : this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 150 + 'px';
                    if(window.innerWidth >= 1280){
                        this.$refs.peerConnect.$els.peersVideoContainer == null ? null : this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 153 + 'px';
                    }
                  }

               // // if(window.innerWidth <= 767){
               //      document.querySelector('.vid-min').style.minHeight = !this.$refs.staffDashboardFooterNav.footer ? window.innerHeight - 120 + 'px' : window.innerHeight - 51 + 'px';
               //  }
                if(this.$refs.staffDashboardFooterNav.footer){
                    if(window.innerWidth <= 767){
                        //this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 51 + 'px';
                        !!document.querySelector('.webcam-switch') ? document.querySelector('.webcam-switch').style.bottom = 0 : null;
                       !!document.querySelector('.vid-min') ? document.querySelector('.vid-min').style.marginBottom = '-50px' :null;
                        if(navigator.userAgent.indexOf('Android') > 0){ 
                            document.querySelector('.vid-min').style.marginBottom = null;
                        }
                    }
                } else {
                    if(window.innerWidth <= 767){
                        //this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 120 + 'px';
                        !!document.querySelector('.webcam-switch') ? document.querySelector('.webcam-switch').style.bottom = '69px' : null;
                        !!document.querySelector('.vid-min') ? document.querySelector('.vid-min').style.marginBottom = '-120px' : null;
                    }
                }

                // if (window.innerWidth<=321){
                //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 14%;';
                // } else if (window.innerWidth<=414){
                //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
                // } else if (window.innerWidth<=482){
                //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 15%;';
                // } else if (window.innerWidth<=641){
                //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 16%;';
                // }  else if (window.innerWidth<=732){
                //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 16.5%;';
                // } else if (window.innerWidth<=768){
                //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
                // }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Handling the resizing'}
                let functionName = 'SecondClientConnect:handleResize';
                helper.catchError(errorStats, functionName);
            }
        },
        relayToOpenPage(data) {
            console.log("RELAY PREEEEE")
            let conn = this.getMeetingDataConnection()
            if(typeof conn != 'undefined') {
                conn.connection.send({key: 'onLoadedPage', data: data})
            }
            for ( let key in this.childCams ){
                let index = this.getDataConnection(this.childCams[key].id)
                index.connection.send({key: 'onLoadedPage', data: data})
            }
        }
    },
    watch: {
        childCams: function(value){
            try {
                for(let key in this.childCams) {
                    if (this.toggleFirstChild.id == this.childCams[key].id) {
                        this.childCams[key].open = (this.toggleFirstChild.status) ? false : true
                    }
                    if (this.toggleSecondChild.id == this.childCams[key].id) {
                        this.childCams[key].open = (this.toggleSecondChild.status) ? false : true
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Watching child camera changes'}
                let functionName = 'SecondClientConnect:childCams';
                helper.catchError(errorStats, functionName);
            }
        },
        subtitleTopPosition: function(value){
            this.noteSubtitle.top = value + "px !important";
        },
        subtitleLeftPosition: function(value){
            this.noteSubtitle.left = value + "px !important";
        },
        noteSubtitleWidth: function(value){
            if (value < window.innerWidth){
                if (value > '250'){
                this.noteSubtitle.width = value + "px !important";
                }else if(value <= '250'){
                this.noteSubtitle.width = "251px !important";
                }
             }
        },
        noteSubtitleHeight: function(value){
                if (value > '101'){
                this.noteSubtitle.height = value + "px !important";
                }else if (value <= '101'){
                this.noteSubtitle.height = "102px !important";
                }
        },
        notesTopPosition: function(value){
            this.meetingNotesTrue.top = value + "px !important";
        },
        notesLeftPosition: function(value){
            this.meetingNotesTrue.left = value + "px !important";
        },
        meetingNotesWidth: function(value){
            if (value < window.innerWidth){
                if (value > '281'){
                this.meetingNotesTrue.width = value + "px !important";
                }else if(value <= '281'){
                this.meetingNotesTrue.width = "282px !important";
                }
             }
        },
        meetingNotesHeight: function(value){
                if (value > '101'){
                this.meetingNotesTrue.height = value + "px !important";
                }else if (value <= '101'){
                this.meetingNotesTrue.height = "102px !important";
                }
        }
    },
    events: {
        'peerconnect:peer:open' (vm, id) {
            try {
                this.peer_id = id
                // Cookie.set('peerId', id)
                this.main.loading = false
                let newid =  this.$route.params.id
                this.meeting_key.id = 'staff'+ newid
                this.$refs.peerConnect.connectToPeer(this.meeting_key.id, 'meeting')
                this.$refs.peerConnect.initiateCall(this.meeting_key.id, false, this.callFailCB)
                // this.$refs.peerConnect.initiateCall(this.meeting_key.id, true)
                this.$refs.peerConnect.isActive = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peer connect open'}
                let functionName = 'SecondClientConnect::peerconnect:peer:open';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:toggle:cam'(isActive) {
            try {
                this.reservedRequest = isActive // For reserved toggle
                this.$refs.peerConnect.toggleOwnWebCam()
                for ( let key in this.childCams ){
                    let index = this.getDataConnection(this.childCams[key].id)
                    index.connection.send({key: 'toggleClientWebcam', data: {data:this.peer_id, status: isActive}})
                }
                let staff = this.getMeetingDataConnection()
                staff.connection.send({key: 'toggleClientWebcam', data: {data:this.peer_id, status: isActive}})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer toggle camera'}
                let functionName = 'SecondClientConnect::peerconnect:peer:toggle:cam';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:nodevice'() {
            try {
                this.$broadcast('ui-snackbar::create',{
                    message: 'IE needs a camera device in order to engage in a meeting.',
                    duration: 3000
                });
                setTimeout(function() {
                    this.endMeetingFn();
                }.bind(this), 5000)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Alerting for no device'}
                let functionName = 'SecondClientConnect::peerconnect:peer:nodevice';
                staff.checkError(errorStats, functionName);
            }
        },
        'peerconnect:peer:pluginChecker'(isPlugin){
            try {
                this.isUsingTemasys = isPlugin;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect updating temasys plugin variable'}
                let functionName = 'SecondClientconnect::peerconnect:peer:pluginChecker';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:updateChildCamera'(id) {
            try {
                // this.removeClient(id);
                let testVidcon = this.getVideoConn(id)
                let testAud = this.getAudioConn(id)
                let datacon = this.getDataConnection(id)
                if(!!testVidcon && !!testAud){
                    this.setChildCam(id,testVidcon,testAud)
                } else {
                    console.log('callClient')
                    // this.callClient(id)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Updating Child Camera'}
                let functionName = 'SecondClientConnect::peerconnect:peer:updateChildCamera';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:call' (vm, id) {
        },
        'peerconnect:peer:error' (vm, err) {
            // if(err.type == 'browser-incompatible') {
            //     router.go({ name: 'connect-fallback' })
            // }
        },
        'peerconnect:peer:connection' (vm, conn) {
            try {
                this.$refs.peerConnect.showVideoCall = true;
                this.showToolbar = true;
                //if(document.querySelector('.footer_menu')) {
                    //document.querySelector('.footer_menu').style.margin= '0% -13%';
                //}
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connection'}
                let functionName = 'SecondClientConnect::peerconnect:peer:connection';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:conn:finalclose'(vm) {
            // if(!this.isScreensharing){
            //     let sample = this.getVideoConn(this.meeting_key.id)
            //         if(sample == undefined){
            //                 this.endMeeting = true
            //                 this.connect.loaded = false
            //                 if(this.$refs.peerConnect.current_stream) {
            //                     this.$refs.peerConnect.current_stream.getTracks()[0].stop()
            //                 }
            //                 this.$refs.peerConnect.screen_share_stream = false
            //                 this.$refs.peerConnect.stopCurrentStream()
            //                 this.$refs.peerConnect.peer.destroy()
            //         }
            // }
        },
        'peerconnect:peer:closed' (vm) {
            // this.endMeetingFn()
        },
        'peerconnect:peer:conn:open' (vm , conn) {
            try {
                var self = this;
                setTimeout(function() {
                    console.log('yazzzum', self, conn)
                    self.$refs.peerConnect.getStaffPeerKey()
                    if(!self.shareLink.validated){
                        conn.send({key: 'secondClientConnect', data: {data: self.peer_id, link: self.$route.params.id + self.$route.params.string}});
                    }
                    if(self.isScreensharing.client != null){
                        self.refreshCam()
                    }
                    if(self.newClientQueue.length > 0){
                        for(let x = 0; x < self.newClientQueue.length; x++){
                            self.addClient(x)
                        }
                    }
                    self.$refs.peerConnect.showVideoCall = true
                    let vb = self
                    let shareData = vb.shareLink.isSet
                    if (shareData) {
                    } else {
                        let id = vb.peer_id
                        conn.send({key: 'sendShareLinktoClient', data: id });
                    }
                    self.setPrimaryCam()
                    // self.getShareLink() // redundant response from sendShareLinkToClient
                    document.querySelector('.ui-modal-mask').style.zIndex = 53;
                }, 1000)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peer connection open'}
                let functionName = 'SecondClientConnect::peerconnect:peer:conn:open';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:conn:data' (vm , data, conn) {
            try {
                switch(data.key) {
                    case 'viewDoc':
                        data = data.data
                        let doc = data.data
                        this.permission = data.permission
                        this.$refs.imageGallery.refreshCtr()
                        this.pages = []
                        this.sortedPages = []
                        this.sortedPages = this.sortArr(doc.pages,'page_id')

                        for ( var key in this.sortedPages ) {
                            this.pages.push({
                                id: key,
                                path: this.sortedPages[key].image_url,
                                annotation: this.sortedPages[key].annotation,
                                canvasImageDataUrl: '',
                                hasImage: false,
                            })
                        }
                        if (this.pages.length != 0) {
                            this.show.file_view_modal = false
                            document.querySelector('.videoFeedCon').style.top = '2%';
                            document.querySelector('.videoFeedCon').style.left = '1%';
                        }
                    break
                    case 'shareDoc':
                        data = data.data
                        this.show.share_doc_modal = true;
                        this.show.share_notes_modal = false;
                        this.show.share_clientdoc_modal = false;
                        this.show.share_voice_report = false;
                        this.show.modal1 = false;
                        this.show.url = false;
                        this.show.uploaderror = false;
                        this.clientupload = false;
                        this.showUrl = false;
                        this.docLink = data.data;
                        this.docSize = this.bytesToSize(parseInt(data.size));
                    break
                    case 'clientShareDoc':
                        data = data.data
                        this.show.share_clientdoc_modal = true;
                        this.show.share_doc_modal = false;
                        this.show.share_notes_modal = false;
                        this.show.share_voice_report = false;
                        this.show.modal1 = false;
                        this.show.url = false;
                        this.show.uploaderror = false;
                        this.clientupload = false;
                        this.showUrl = false;
                        let newf = new File([data.blob],data.name, {type:data.type})
                        this.docClient.file = newf
                        this.docClient.size = this.bytesToSize(parseInt(newf.size))
                    break
                    case 'childFileView':
                        this.$refs.imageGallery.refreshCtr()
                        this.pages = []
                        this.pages = data.data
                        if (this.pages.length != 0) {
                            this.show.file_view_modal = false
                            document.querySelector('.videoFeedCon').style.top = '2%';
                            document.querySelector('.videoFeedCon').style.left = '1%';
                        }
                                
                    break
                    case 'nextPage': this.$refs.imageGallery.nextPage(true)
                                    break
                    case 'prevPage': this.$refs.imageGallery.prevPage(true)
                                    break
                    case 'releasePaint': 
                                    data = data.data
                                    this.$refs.imageGallery.canvasImageReceiver = data.data;
                                    this.$refs.imageGallery.canvasUpdated = data.canvasUpdated;
                                    if (!data.fromBtnPage) {
                                        this.$refs.imageGallery.copyCanvasBeforeDownload = data.data;
                                    }
                                    break
                    case 'oncanvasPost':
                                    // console.log("STATUS ON CANVAS POST ::: ",data.data)
                                    this.$refs.imageGallery.onCanvasPostRecieve = data.data;
                                    break
                    case 'onLoadedPage':
                            this.$refs.imageGallery.buttonShow = true;
                            console.log("onLoadedPage :::::: "+data.data)
                            break
                    case 'mouseMove': 
                                    // data = data.data
                                    this.mouseCoordinates = data.data
                                    if(this.initialMousemove){
                                        this.mouseMove('')
                                        this.initialMousemove = false
                                    }
                                    
                                    if(this.mouseCoordinates && this.mouseCoordinates.doctouch) {
                                        if(this.initialDocmousemove){
                                            // this.ownDocCoord.doctouch = true
                                            // this.mouseMove('')
                                            // this.$refs.imageGallery.callDocument()
                                            // this.initialDocmousemove = false
                                        }
                                    }
                                    this.updatePointer()
                                    break
                    case 'notes':
                        this.notes = data.data;
                    break
                    case 'getSessionId':
                        this.getSessionId = data.data
                        this.$refs.peerConnect.sessionIDsetColor(this.getSessionId)
                    break
                    case 'sendAudioTranscription':
                        let Subtitlecontent = document.getElementById('subtitle-content')
                        let shouldScroll = Subtitlecontent.scrollTop + Subtitlecontent.clientHeight === Subtitlecontent

                        if(this.isUsingTemasys) {
                            Subtitlecontent.innerHTML += '<p><span>' + data.data + '</span></p>';
                        } else {
                            this.subtitle.push(data.data)
                        }

                        if(!shouldScroll){
                            Subtitlecontent.scrollTop = Subtitlecontent.scrollHeight;
                        }
                    break
                    case 'sendChildCamsFromStaff':
                        this.staffChildCams = data.data
                    break
                    case 'colorConn':
                        this.colorPass.push(data.data)
                    break
                    case 'audioMute':
                        this.$refs.peerConnect.toggleAudioMute(data.data)
                    break
                    case 'endMeeting':
                        this.endMeetingFn()
                    break
                    case 'sharePeerId':
                        Cookie.set('peerId',data.data)
                    break
                    case 'sendNotes':
                        this.show.share_notes_modal = true;
                        this.show.share_doc_modal = false;
                        this.show.share_clientdoc_modal = false;
                        this.show.share_voice_report = false;
                        this.show.modal1 = false;
                        this.show.url = false;
                        this.show.uploaderror = false;
                        this.clientupload = false;
                        this.showUrl = false;
                        this.notesLink = data.data.file_name;
                        this.notesSize = this.bytesToSize(parseInt(data.data.file_size));
                    break
                    case 'sendVoiceReport':
                        let finalConvo = ''
                        if (this.subtitle){
                            for(let key in this.subtitle){
                                let convo = this.decodeEntities(this.subtitle[key])
                                finalConvo += convo+'\r\n'
                            }
                            let blobReport = new Blob([finalConvo],{
                                type:"text/plain;charset=utf-8;",
                            });
                            this.voice_report_size = this.bytesToSize(parseInt(blobReport.size))
                        }
                        this.show.share_voice_report = true;
                        this.show.share_doc_modal = false;
                        this.show.share_notes_modal = false;
                        this.show.share_clientdoc_modal = false;
                        this.show.modal1 = false;
                        this.show.url = false;
                        this.show.uploaderror = false;
                        this.clientupload = false;
                        this.showUrl = false;
                    break
                    case 'callFailed':
                        let id = data.data
                        this.$refs.peerConnect.connectToPeer(id, 'meeting')
                        this.$refs.peerConnect.initiateCall(id, false, this.callFailCB)
                        this.$refs.peerConnect.initiateCall(id, true)
                    break
                    case 'parentForceConnect':
                        let parent_id = data.data
                        let connec = this.getDataConnection(parent_id)
                        connec.connection.send({key: 'childCamReconnect', data: this.peer_id})
                    break
                    case 'shareLink':
                        let sendLink = this.getMeetingDataConnection()
                        let vm = this
                        let urlShareLink = data.data
                        this.sharelinkChecker = setInterval(vm.checkShareLink(urlShareLink,vm,sendLink),500);
                    break
                    case 'shareLinkToClient':
                        let sendLinkClient = this.getMeetingDataConnection()
                        let vmClient = this
                        let urlShareLinkClient = data.data
                        this.sharelinkChecker = setInterval(vmClient.checkShareLink(urlShareLinkClient,vmClient,sendLinkClient),500);
                    break
                    case 'screenShareStart':
                        if(!this.screenLoaded) {
                            this.main.loading = true
                            this.mainCam.isSet = false
                        }
                    break
                    //     this.$refs.peerConnect.initiateCall(this.meeting_key.id, false, this.callFailCB)
                    //     this.$refs.peerConnect.initiateCall(this.meeting_key.id, true)
                    //     this.refreshCam(this.isScreensharing.client)
                    //     break
                    case 'validKey':
                        if(data.data) {
                            this.shareLink.validated = true
                            this.shareLink.default = true
                            this.$refs.peerConnect.showVideoCall = true
                            this.connect.loaded = true
                            this.showToolbar = true
                            this.setLink()
                            let main = this.getMeetingDataConnection()
                            this.primaryCam = main.id
                            document.querySelector('.share_link').style.left = '1.7rem';
                            document.querySelector('.share_link').style.zIndex = 1;

                            //if (document.querySelector('.footer_menu')) {
                                //document.querySelector('.footer_menu').style.margin= '0% -13%';
                            //}
                        } else {
                            this.shareLink.validated = false
                            this.shareLink.default = false
                        }
                    break
                    case 'roomFull':
                        this.$broadcast('ui-snackbar::create', {
                            message: '申し訳ありませんが、部屋はいっぱいです',
                            duration: this.duration * 10000
                        });
                    break
                    case 'allChildCams':
                        data = data.data
                        let childIDS = data.childIDS
                        let ownkey = this.peer_id
                        // Filter the array without own id
                        let childarray = childIDS.filter(function(num){
                            return num != ownkey
                        })
                        console.log('allChildCams:::childIDS====', data.childIDS)
                        console.log('allChildCams:::clients====', this.clients)
                        for(let x = 0; x < childarray.length; x++){
                            let id = childarray[x]
                            let isAdded = this.clients.findIndex(cont => cont.id === id )
                            if(isAdded < 0){
                                this.addClientQueue(id)
                            }
                        }
                    break
                    case 'endingChild':
                        if(!!this.getDataConnection(data.data)) {
                            this.$refs.imageGallery.onCanvasPostRecieve = false;
                            this.removeClient(data.data)
                            this.$refs.peerConnect.endChildConnections(data.data);
                            this.message = '他のユーザーからのコールが終了しました。'
                            this.endMeetingPopup()
                        }
                    break
                    // When vidcon is not yet added, this will continously called
                    case 'forcescreenshareReconnect':
                        this.isScreensharing.client = data.data
                        this.$refs.staffDashboardFooterNav.screenshare = false
                        this.refreshCam()
                    break
                    // After screenshare ends, client waits for vidcon to be added on connection
                    case 'clientRequestSreenshareReconnect':
                        let client_id = data.data
                        let conne = this.getDataConnection(client_id)
                        conne.connection.send({key: 'forcescreenshareReconnect', data: this.peer_id})
                        this.$refs.staffDashboardFooterNav.screenshare = false
                    break
                    // After screenshare ends, reconnect webcams
                    case 'screenshareReconnect':
                        this.screenLoaded = false;
                        if(this.mainCam.id == data.data) {
                            this.mainCam.isSet = false
                        }
                        this.isScreensharing.client = null
                        this.$refs.peerConnect.reconnectVideoConnection(data.data);
                        // this.refreshCam()
                    break
                    case 'toggleClientWebcam':
                        data = data.data
                        if(data.data == this.primaryCam) {
                            this.$refs.peerConnect.togglePeerWebcam(data.status)
                        } else {
                            for ( let key in this.childCams ){
                                if( data.data == this.childCams[key].id ){
                                    let is_open = this.childCams[key].open
                                    // this.childCams[key].open = (is_open) ? false : true
                                    this.childCams[key].open = data.status
                                }
                            }
                        }
                    break
                    case 'toggleRequest':
                        let staff = this.getMeetingDataConnection()
                        staff.connection.send({key: 'toggleClientWebcam', data: {data:this.peer_id, status: this.reservedRequest}})
                    break
                    case 'toggleRequestChild':
                        let staffs = this.getMeetingDataConnection()
                        staffs.connection.send({key: 'toggleClientWebcam', data: {data:this.peer_id, status: this.reservedRequest}})
                    break
                    case 'toggleFirChildWebcam':
                        data = data.data
                        if (data.status){
                            this.toggleFirstChild = {
                                id: data.data, status: data.status
                            }
                        }
                    break
                    case 'toggleSecChildWebcam':
                        data = data.data
                        if (data.status){
                            this.toggleSecondChild = {
                                id: data.data, status: data.status
                            }
                        }
                    break
                    case 'staffDocToolbar':
                        let vms = this
                        this.isMaximized = data.data
                        // if(data.data){
                        //     vms.$refs.imageGallery.showToolbar()
                        //     vms.$refs.imageGallery.updateCurrentImage()
                        //     vms.$refs.imageGallery.updateCanvas()
                        // }else{
                        //     vms.$refs.imageGallery.saveCurrentDrawing()
                        //     vms.$refs.imageGallery.hideToolbar()
                        //     vms.$refs.imageGallery.paintToolOff()
                        // }
                    break
                    case 'isConnected':
                        let staffcon =  this.getDataConnection(data.data)
                        if(staffcon) staffcon.connection.send({key: 'connectedChild', data: this.peer_id})
                    break;
                    case 'removeChildClient':
                        this.removeClient(data.data)
                        this.$refs.peerConnect.endChildConnections(data.data);
                        this.message = '他のユーザーからのコールが終了しました。'
                        this.endMeetingPopup()
                    break;
                    case 'parentName':
                        this.parentProfile.name = data.data
                        Cookie.set('ParentNameHost', this.parentProfile.name)
                        //console.log("Parent name case :::", this.parentProfile.name)
                        //console.log(" Parent name cookie :::", Cookie.get('ParentNameHost'))
                    break;
                    default:
                        this[data.key] = data.data
                    break;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peer connection data'}
                let functionName = 'SecondClientConnect::peerconnect:peer:conn:data';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:screenshare:start'() {
            try {
                this.$refs.staffDashboardFooterNav.screenshare = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connect sceenshare'}
                let functionName = 'SecondClientConnect::peerconnect:screenshare:start';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:peer:screen:loaded' (vm) {
            try {
                this.screenLoaded  = true
                if(this.isUsingTemasys) {
                    this.main.loading = false;
                    return;
                }
                var checker = window.setInterval(function(){
                    try {
                        if (this.$els.shareScreenCon.duration == "Infinity") {
                            this.main.loading = false
                            clearInterval(checker)
                        }
                    } catch(e) { /** do nothing */}
                }.bind(this), 1000);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Listens to peer screen being loaded'}
                let functionName = 'SecondClientConnect::peerconnect:peer:screen:loaded';
                staff.checkError(errorStats, functionName);
            }
        },
        'peerconnect:peer:conn:error' (vm, err) {
            try {
                this.$refs.staffDashboardFooterNav.screenshare = false
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connection error'}
                let functionName = 'SecondClientConnect::peerconnect:peer:conn:error';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:screenshare:endclient'() {
            try {
                this.isScreensharing = false
                this.$refs.staffDashboardFooterNav.screenshare = false
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connect end client'}
                let functionName = 'SecondClientConnect::peerconnect:screenshare:endclient';
                helper.catchError(errorStats, functionName);
            }
        },
        'peerconnect:screenshare:end'(id) {
            try {
                this.isScreensharing.client = null
                this.$refs.staffDashboardFooterNav.screenshare = false
                let staffid = this.staffID
                for ( let key in this.childCams ){
                    let index = this.getDataConnection(this.childCams[key].id)
                    index.connection.send({key: 'screenshareReconnect', data: this.peer_id})
                }
                let staff = this.getMeetingDataConnection()
                if(staff) staff.connection.send({key: 'screenshareReconnect', data: this.peer_id})
                this.isScreensharing.active = false
                // this.refreshCam()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connect screenshare end'}
                let functionName = 'SecondClientConnect::peerconnect:screenshare:end';
                helper.catchError(errorStats, functionName);
            }
        },
        'keydown' (e) {
            try {
                this.relayNotes()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connect relay notes'}
                let functionName = 'SecondClientConnect::keydown';
                helper.catchError(errorStats, functionName);
            }
        },
        'changed' (e) {
            try {
                this.relayNotes()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Peer connect changed'}
                let functionName = 'SecondClientConnect::changed';
                helper.catchError(errorStats, functionName);
            }
        },
        'ui-image-gallery:BoxElement' (data) {
            this.boxEls = data;
        },
        'ui-image-gallery:loaded'(id) {
            try {
                if(document.querySelector('.forceOnTop')){
                    var forceOnTops = document.querySelectorAll('.forceOnTop');
                    for(var i = 0; i < forceOnTops.length; i++) {
                        forceOnTops[i].classList.remove('forceOnTop');
                        for (let ii = 0; ii < this.boxEls.length; ii++) {
                            if (forceOnTops[i] == this.boxEls[ii]) {
                                forceTopGet = this.boxEls[ii];
                                this.boxEls = this.boxEls.filter(function(value){
                                    return value != forceTopGet;
                                });
                                this.boxEls.unshift(forceTopGet);
                            }
                        }
                    }
                }

                if (typeof this.pages[id] != 'undefined') {
                    this.annotation = this.pages[id].annotation
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Image gallery loaded'}
                let functionName = 'SecondClientConnect::ui-image-gallery:loaded';
                helper.catchError(errorStats, functionName);
            }
        },
        'ui-image-gallery:Minimized'() {
            try {
                this.isMaximized = false
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Image gallery minimized'}
                let functionName = 'SecondClientConnect::ui-image-gallery:Minimized';
                helper.catchError(errorStats, functionName);
            }
        },
        'ui-image-gallery:docCoord' (data,value) {
            try {
                let ownCoordfromImageGallery = data
                this.ownDocCoord = ownCoordfromImageGallery
                document.addEventListener('mousemove', this.mouseMove)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Image gallery document coordinates'}
                let functionName = 'SecondClientConnect::ui-image-gallery:docCoord';
                helper.catchError(errorStats, functionName);
            }
        },
        'ui-image-gallery:getCoords' (data) {
            try {
                this.ownDocCoord.onScreenHeight = data.height
                this.ownDocCoord.onScreenWidth = data.width
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Image gallery get coordinates'}
                let functionName = 'SecondClientConnect::ui-image-gallery:getCoords';
                helper.catchError(errorStats, functionName);
            }
        },
         'ui-image-gallery:release-paint' (data,canvasUpdated,fromBtnPage){
        //    console.log("RELEASE CLIENT: ",data)
           let conn = this.getMeetingDataConnection()
            if(typeof conn != 'undefined') {
                conn.connection.send({key: 'releasePaint', data: {data: data, canvasUpdated: canvasUpdated, fromBtnPage:fromBtnPage}})
            }

            for ( let key in this.childCams ){
                let index = this.getDataConnection(this.childCams[key].id)
                index.connection.send({key: 'releasePaint', data: {data:data, canvasUpdated: canvasUpdated, fromBtnPage:fromBtnPage}});
            }
        },
        'ui-image-gallery:next-page' (id, canvasUpdated) {
            
            try {
                this.annotation = this.pages[id].annotation
                let conn = this.getMeetingDataConnection()
                if(typeof conn != 'undefined') {
                    conn.connection.send({key: 'nextPage', data: true})
                }
                for ( let key in this.childCams ) {
                    let index = this.getDataConnection(this.childCams[key].id)
                    index.connection.send({key: 'nextPage', data: true})
                }
                if (canvasUpdated) {
                    this.$refs.imageGallery.updatePage(true)
                } else {
                    console.log("\n\tNOTHING TO UPDATE")
                    // this.$refs.imageGallery.updateClearPage(true)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Image gallery next page'}
                let functionName = 'SecondClientConnect::ui-image-gallery:next-page';
                helper.catchError(errorStats, functionName);
            }
        },
        'ui-image-gallery:prev-page' (id, canvasUpdated) {
            try {
                this.annotation = this.pages[id].annotation
                let conn = this.getMeetingDataConnection()
                if(typeof conn != 'undefined') {
                    conn.connection.send({key: 'prevPage', data: true})
                }
                for ( let key in this.childCams ) {
                    let index = this.getDataConnection(this.childCams[key].id)
                    index.connection.send({key: 'prevPage', data: true})
                }
                if (canvasUpdated) {
                    this.$refs.imageGallery.updatePage(true)
                } else {
                    console.log("\n\tNOTHING TO UPDATE")
                    // this.$refs.imageGallery.updateClearPage(true)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Image gallery prevous page'}
                let functionName = 'SecondClientConnect::ui-image-gallery:prev-page';
                helper.catchError(errorStats, functionName);
            }
        },
        'ui-image-gallery:oncanvas-post'(status) {
            let conn = this.getMeetingDataConnection()
            if(typeof conn != 'undefined') {
                conn.connection.send({key: 'oncanvasPost', data: status})
            }
            for ( let key in this.childCams ){
                let index = this.getDataConnection(this.childCams[key].id)
                index.connection.send({key: 'oncanvasPost', data: status})
            }
        },
        'ui-image-gallery:to-waiting-page'(status,id) {
            // let conn = this.getMeetingDataConnection()
            // if(typeof conn != 'undefined') {
            //     conn.connection.send({key: 'onWaitingPage', data: status, id:id})
            // }
            // for ( let key in this.childCams ){
            //     let index = this.getDataConnection(this.childCams[key].id)
            //     index.connection.send({key: 'onWaitingPage', data: status, id:id})
            // }
        },
        'ui-image-gallery:closeDoc'(status,canvasUpdated) {
            if (status) {
                document.querySelector('.videoFeedCon').style.top = '15%';
                document.querySelector('.videoFeedCon').style.left = '33%';
                this.initialDocmousemove = true
            }
            if (canvasUpdated) {
                this.$refs.imageGallery.updatePage(true)
            } else {
                console.log("\n\tNOTHING TO UPDATE")
                this.$refs.imageGallery.updateClearPage(true)
            }
        },
        'resized' (el) {
            try {
                let isNotes = helper.hasClass(el, 'meetingNotes')
                let isMinutes = helper.hasClass(el, 'subtitle')
                if(isNotes) {
                    this.noteShare.disabled = true
                }
                if (isMinutes) {
                    this.noteSubtitle.disabled = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Resizing'}
                let functionName = 'SecondClientConnect::resized';
                helper.catchError(errorStats, functionName);
            }
        },
        'stop-resize' (el) {
            try {
                let isNotes = helper.hasClass(el, 'meetingNotes')
                let isMinutes = helper.hasClass(el, 'subtitle')
                if(isNotes) {
                    this.noteShare.disabled = false
                }
                if (isMinutes) {
                    this.noteSubtitle.disabled = false
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Stop Resizing'}
                let functionName = 'SecondClientConnect::stop-resize';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    directives: {
        draggable: DraggableDirective,
        onclickfront: OnclickToFrontDirective
    },
    components: {
        PeerConnect,
        UiImageGallery,
        UiResizeable,
        StaffDashboardFooterNav,
        StaffSidebar,
        FilePreview,
        UiAlert,
        UiSnackbar,
        UiProgressLinear
        // ThankYou
        // StaffDocShare
    },
    created() {
        document.addEventListener('mousemove', this.mouseMove)
        new ClipboardJS('.share_link_btn')
        new ClipboardJS('.sharelinkButton')

        /** Set Cookie for the document security */
        Cookie.set('peerId', '')
    },
    ready() {
        try {
            // window.onunload = function(event) { this.onbeforeunload(event) }
        //   this.initTinyMCE();
            window.onbeforeunload = function(event) { this.onbeforeunload(event) }
            window.addEventListener('beforeunload', (event) => { this.onbeforeunload(event) }, false);
            window.addEventListener('unload', (event) => { this.onbeforeunload(event) }, false);
            let config = {
                role: 'guest',
                showToolbar: false
            }
            // if(window.innerWidth <= 767){
            //          this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 51 + 'px';
            //     }
            if(window.innerWidth <= 767){
                 document.querySelector('.webcam-switch').style.bottom = 0;
                document.querySelector('.vid-min').style.marginBottom = '-50px';
                    if(navigator.userAgent.indexOf('Android') > 0){ 
                        document.querySelector('.vid-min').style.marginBottom = null;
                    }
            }
            // if (window.innerWidth<=321){
            //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 14%;';
            // } else if (window.innerWidth<=414){
            //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
            // } else if (window.innerWidth<=482){
            //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 15%;';
            // } else if (window.innerWidth<=641){
            //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 16%;';
            // } else if (window.innerWidth<=732){
            //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 16.5%;';
            // } else if (window.innerWidth<=768){
            //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
            // }

            if(window.innerWidth >= 960){
                document.querySelector('.videoFeedCon').style.left = '32%'

                 if(window.innerWidth >= 1115 ){
                    document.querySelector('.videoFeedCon').style.left = '33%'
                }

                if(window.innerWidth >= 1201 ){
                    document.querySelector('.videoFeedCon').style.left = '33%'
                }

                if(window.innerWidth >= 1270 ){
                    document.querySelector('.videoFeedCon').style.left = '34%'
                }

                if(window.innerWidth >= 1290 ){
                    document.querySelector('.videoFeedCon').style.left = '32%'
                    
                }
                if(window.innerWidth >= 1366 ){
                    document.querySelector('.videoFeedCon').style.left = '31.5%'
                
                }
                if(window.innerWidth >= 1445 ){
                    document.querySelector('.videoFeedCon').style.left = '33%'
                }
            }
            //this.$els.vidShare.style.bottom = '0px'
            if (window.innerWidth <= 1010){
                this.toggleNoteShare();
                this.toggleSubtitle();
            }

            window.addEventListener('resize', this.handleResize)
            let vm = this;
            document.addEventListener('visibilitychange', function() {
                if( /iPhone|iPad/i.test(navigator.userAgent) && !document.hidden) {
                    vm.$refs.peerConnect.toggleTracks()
                }
            })
            // setInterval(function(){
            //     if(window.innerWidth <=768){
                    // this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = ''
            //     }
            // },1000)
            // window.onbeforeunload = this.onbeforeunload
            // window.addEventListener('beforeunload', this.onbeforeunload);
            // ON DEV CONSOLE COMMENT THIS IF IN DEVELOPMENT
            window.onerror = function(message, url, lineNumber) {
                // code to execute on an error
                ///////return true; // prevents browser error messages
            };
            ///////console.log = function(){};
            // END DEV CONSOLE

            if (browser.detectIE().trigger) {
                this.errorCompatibility = true;
                // let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Browser Compatibility error'}
                // let functionName = 'Speech Recognition and Webrtc';
                // // this.checkError(errorStats,functionName);
                // helper.catchError(errorStats, functionName) ? this.errorCompatibility = true : this.errorCompatibility = false;
            }
            this.$dispatch('component-ready', config);
            try{
                /** As of the moment, speech recognition is only supported in Chrome */
                if(/Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) {
                    this.speechRecognitionStart()
                }
            }catch(e){
                this.errorCompatibility = true;
                // // this.errorCompatibility = true;
                //  let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Speech Recognition'}
                //  let functionName = 'Speech Recognition and Webrtc';
                //  // staff.checkError(errorStats,functionName);
                //  // this.errorCompatibility = true;
                //  helper.catchError(errorStats, functionName) ? this.errorCompatibility = true : this.errorCompatibility = false;
            }
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Second Client created component'}
            let functionName = 'SecondClientConnect::created';
            helper.catchError(errorStats, functionName);
        }
    }
}
</script>
<style lang="scss">
    .hideLoad {display:none;}
    // .showLoad {}


    .speechRecognition{
            position: absolute;
            margin-left: auto;
            margin-right: auto;
            bottom:200px;
            left: 0;
            right: 0;
            text-align:center;
            height:50px;
            z-index:100;
        }
        .words{
            font-size: 29px;
            color: #fff;
        }
    #staff-dashboard-footer .ui-toolbar .ui-toolbar-left .ui-icon-button {
        display: none;
    }
    #staff-dashboard-footer .ui-toolbar .ui-toolbar-title.appLogo img {
        margin-left: 20px;
    }
    .protoConnectContainer .wrapper {
        .meetingNotes {
            width: 300px;
            height: 35%;
            bottom: 195px;
            .meetingNotesWrapper.peer-ui-connect {
                height: 100%;
                .ui-resizeable {
                    bottom: -77px;
                }
                .meeting-note-footer {
                    bottom: -80px;
                    height: 25px !important;
                }
            }
            .meeting-note-head {
                height: 25px;
                border-bottom: 1px solid #3cb371;
                margin: 15px;
                display: flex;
                width: 90%;
            }
            .ui-textbox-textarea{
                background: #ffffff;
            }
        }
    }
        .share_link {
            position: fixed;
            min-width: 200px;
            // bottom: 7.5rem;
            margin-bottom: 1rem;
            .share_link_btn,.ui-textbox{
            display: inline-block;
        }
    .share_link_btn{
        background-color: #3cb371;
        color: #ffff;
        padding: 5px 2px 10px 2px;
        margin-top: -66px;
        position: absolute;
        right: -40px;
        .mee2box-custom-icon {
                transition: unset;
                background: url('../image/clipboard-icon.png') no-repeat 2px 3px;
                height: 35px;
                width: 35px;
                display: inline-block;
                text-indent: 9999px;
            }
    }
    .share_link_btn:hover{
        cursor: pointer;
    }
    .ui-textbox .ui-textbox-input, .ui-textbox .ui-textbox-textarea {
        color: rgba(0, 0, 0, 0.88) !important;
       float: left;
        width: 300px;
        height:50px;
        background-color: #ffffff;
    }
}
    .file-size{
        font-family: 'NotoSans-Bold';
        font-size: 12px;
        color: #3cb371;
        text-align: center;
        margin-top: 8px;
    }

    .ui-modal-header h1{
        padding:20px;
    }

    @media (max-width:439px){
        .subtitle-footer .ui-button-content {
            padding: 7px !important;
        }

        .RedLight{
            width: 12px!important;
            height: 12px!important;
            border: 1.5px solid #e4e6e9!important;
            margin: 11px 7px!important;
        }
        .BlackLight{
          width: 12px!important;
          height: 12px!important;
      }
    }//end
    @media (max-width: 767px){
            .share_link {
                top: 30px !important;
                .ui-textbox{
                    width:160px;
                    display:table-caption !important;
                }
                .share_link_btn{
                    position: initial !important;
                    float: right !important;
                    margin-top: -62px !important;
                }
            }
            .meetingNotes {
                top: inherit;
            }
            .ui-textbox .ui-textbox-input, .ui-textbox .ui-textbox-textarea {
                width:100% !important;
            }
    }
    @media (min-width: 768px){
            .share_link {
                top: unset;
            }
    }
    .compatibility-alert {
        .ui-alert-close-button{
            color: #fff;
        }
        .ui-alert-body {
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

//     @media (min-width: 320px) and (max-width: 331px){
// .footer {
//   a {
//     width: 42px !important;
//   }
// }
// }

</style>
<style lang="scss" scoped>


    .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs){
        .subtitle{
                background: #fff;
                width: 330px;
                height: 24%;
                z-index: 40; 
                flex-grow: 1;
                position: absolute;
                right:30px;
                bottom:160px;
                box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
                display:block;
                #subtitle-content{
                    overflow: auto;
                    height: 65%;
                    font-family: NotoSans-Regular; 
                    overflow:scroll; overflow-x:hidden; 
                    padding: 0  30px 20px 30px; 
                    line-height:2rem;
                }
                #subtitle-content::-webkit-scrollbar{
                    width: 1em;
                }
                #subtitle-content::-webkit-scrollbar-track{
                    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
                    box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
                }
                #subtitle-content::-webkit-scrollbar-thumb{
                    background-color: #0d275f;
                    outline: 1px solid #0d275f;
                }
                .subtitle-header{
                            color: #3cb371;
                            font-family: 'NotoSans-Bold';
                            height: 25px;
                            border-bottom: 1px solid #3cb371;
                            margin: 15px;
                            display: -ms-flexbox;
                            display: flex;
                            width: 90%;
                }
                .subtitle-footer {
                bottom: -50px;
                position: absolute;
                background: #3cb371;
                width: 100%;
                height: 50px;
                .ui-button-text {
                    font-family: 'NotoSans-Bold';
                }
                .ui-button-icon {
                    background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
                    background-position-x: -587px;
                    background-position-y: -14px;
                    font-size: 0;
                    width: 20px;
                    margin-right: 10px;
                    height: 18px;
                }
                .ui-button {
                    margin: 5px;
                    background: #ffffff;
                    color: #3cb371;
                }
            }
            }
            .subtitle.hide{
                display:none;
            }
    }
    .voice-switch {
        padding: 10px;
    }

    .VoiceSwitch-label {
        color: #fff;
        font-weight: 800;
        margin-right: 1rem;
        padding-top: 10px;
        font-size: 11px;
    }
    .VoiceSwitch-box {
        background: #e4e6e9;
        border-radius: 20px;
        padding: 2px;
        margin-top: 4px;
        height: 25px;
        width: 70px;
        .btn {
            background: #e4e6e9;
            border-radius: 20px;
            color: #3cb371;
            width: 31px;
            height: 21px;
            font-size: 10px;
            padding: 0px;
        }
    }

    .RedLight{
        background: #f00;
        width: 15px;
        height: 15px;
        border-radius: 100%;
        border: 2px solid #e4e6e9;
        margin: 8px 10px;
    }

    .BlackLight{
        background: #e4e6e9;
        width: 15px;
        height: 15px;
        border-radius: 100%;
        border: 2px solid #e4e6e9;
        margin: 8px 10px;
    }

// ========================
//     css-footer-sham
// ========================
.footer{
  height: 100px;
  width: 100%;
  position: fixed;
  background: #3cb371;
  padding: 0;
  color: #fff;
  bottom: 0;

  .logo{
    display: inline-block;
    color: #fff;
    font-size: 16px;
    padding: 20px;
    height: 100%;
    width: 20%;
  }

  .footer-menu{
    color: #fff;
    list-style-type: none;
    display: inline-block;
    float: right;
    margin: 0;
    width: 70%;
    margin-top: 0;
    margin-right: -11%;
    li{
      float: left;
      text-align: center;
      border-left: 3px solid rgba(0, 0, 0, 0.15);
      width: 12%;
      a{
        font-size: 10px;
        font-family: 'NotoSans-Regular';
        display: inline-block;
        padding: 15px 0px;
        width: 100%;
        height: 100%;

        .menu-text{
           color: #ffffff;
        }

        .menu-icon{
          background: url('/image/toolbar-sprite.png') no-repeat scroll 5px 0 transparent;
          height: 43px;
          width: 43px;
          display: inline-block;
          text-indent: 9999px;
          //margin-bottom: 10px;
            &.tv { background-position-x: -104px; }
            &.assignment {  background-position-x: -211px;}
            &.edit { background-position-x: -327px; }
            &.close {  background-position-x: -447px; background-position-y: -45px;}
          margin-bottom: 0.5rem;
        }

        .subtitle{
          background: url(image/subtitle.png) no-repeat;
          background-size: 100% 100%;
        }
        .url{
          background: url(image/icon-url.png) no-repeat;
          background-size: 100% 100%;
          filter: brightness(0) invert(1);
        }
        .video-call{
          background: url(image/video_call_icon.png) no-repeat;
          background-size: 100% 75%;
        }
       
      }
    }
  }

  li:hover {
    .menu-icon {
       &.close {background-position-y: -45px;}
    }
}

  li:hover{
    background:#ff9600;


  }
  // .logo img{
  //   height: 100px;
  // }
}//end






// ========================

// ==============================================
            //Box Media queries (Angel)
// ==============================================
@media (max-width:439px){
    .VoiceSwitch-label {
        font-size: 9px!important;
    }

    .VoiceSwitch-box {
        width: 58px!important;
        .btn{
            width: 25px!important;
            font-size: 9px !important;
        }
        

    }

    .subtitle-footer{

        .ui-button{
            margin: 5px!important;
            font-size: 9px!important;
            width: 120px!important;
            padding: 0 !important;
            right: 23px;
            top: 5px;
        }

        .ui-button-icon {
            margin-right: 5px!important;
        }

        .ui-button-content {
            padding: 7px !important;
        }

    }

//Boxes

    .subtitle{
        width: 90%!important;
        height: 30%!important;
        right: 20px!important;
        bottom: 120px!important;
    }

    .meetingSales, .meetingNotes{
        width: 90%!important;
        right: 20px!important;
    }
}//end
//Display footer notes
@media only screen and (max-device-width: 480px) {
.meetingNotesWrapper {
        &.peer-ui-connect {
            .ui-resizeable,
            .meeting-note-footer{
                display: block!important;
            }
        }
    }
}//end

@media (min-width:440px) and (max-width:814px){

// Boxes
    .subtitle{
        width: 400px!important;
        height: 35%!important;
    }


}//end

@media (max-width: 767px){
    .meetingSales {
        bottom: 165px!important;
    }

    .meetingNotes {
        bottom: 175px!important;
    }

    .subtitle{
        bottom: 160px!important;
    }

}//end
// For IE Browser ONLY!
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .proto .peerCon .protoConnectContainer {
        // File Viewer setup
        .wrapper .row .fileViewer .container {
            position: absolute !important;
        }
    }
    // Close Button
    .ui-close-button .ui-fab-mini i {
        position: relative !important;
    }
}
</style>