<template>
  <div class="protoCon peerCon">
    <div class="protoConnectContainer">
      <div class="wrapper noToolbar">
        <div class="pointer" v-el:pointer v-if="connect.loaded && !endMeeting">
          <ui-icon icon="pan_tool"></ui-icon>
        </div>
        <div class="share_link" style="z-index:-1 !important;left:-60rem; display:none">
          <ui-textbox
              name="name" type="text" :value.sync=shareLink.textBoxLink id="urlink" @blurred="setLink(shareLink.text)"
          ></ui-textbox>
          <div class="share_link_btn" data-clipboard-target="#urlink">
            <span class="mee2box-custom-icon description"></span><br>
          </div>
        </div>
        <!-- Modal for sharing file of child -->
        <ui-modal
            :show.sync="clientupload" hide-footer header="資料共有" transition="ui-modal-fade"
            body="Please Input Some Files" id="clientuploadmodal">
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
          <div class="ui-button-group" v-bind:class="[btnLoad ? 'showLoad' : 'hideLoad']" id="{{btnLoad}} i">
            <ui-button
                type="normal"
                :loading="false"
                color="success"
                :disabled ="show.disable"
                class="fpreview-upload"
                @click="convertDocument"
            >アップロード</ui-button>

            <!-- ファイルを渡す -->
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
            <span>{{ peer_id }}</span>
          </div>
        </div>
        <!-- <div class="overlay row  middle-xs center-xs blurredBg" v-if="endMeeting"> -->
        <!-- <div class="" v-if="endMeeting"> -->
        <!-- <div class="meetingMsg"> -->
        <!-- <span>接続を終了しました。</span> -->
        <!-- <thank-you></thank-you> -->
        <!-- </div> -->
        <!-- </div> -->
        <div class="row blurredBg noSelect" v-bind:class="[vidSize ? 'video-max' : 'video-min']" v-el:peer-container v-show="connect.loaded && !endMeeting">
          <div v-draggable v-onclickfront class="videoFeedCon withAttachFab" v-el:vid-share :disabled="vidShare.disabled">
            <peer-connect :api-key="peer_api_key" :peer-host="peer_host" :peer-key-prefix="peer_key_prefix" :video-enabled="video_enabled" v-ref:peer-connect
                          :primary-cam="primaryCam" :show-audio-toggle="true" :main-cam="mainCam" :child-cams="childCams" :video-user-feed="videoUserFeed" ></peer-connect>
            <button class="ui-fab zoom_out_screen ui-fab-mini icon-color"  v-if="video_enabled" @click="expandVidShare">
              <i class="zoom_out_screen_icon" aria-hidden="true"></i>
              <div class="ui-ripple-ink"></div>
              <!-- <span>最大化<span> -->
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
          <!-- <div class="speechRecognition">
              <div id="words"></div>
          </div> -->
          <div v-draggable v-el:sub-title v-onclickfront  class="subtitle" v-bind:style="{ height: noteSubtitle.height, width: noteSubtitle.width, top: noteSubtitle.top, left: noteSubtitle.left}" :disabled="noteSubtitle.disabled" >
                    <!--div v-draggable v-onclickfront class="subtitle" v-el:sub-title-->
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
                <ui-image-gallery :canvas-pass.sync="canvasUsage" :image-list.sync="pages" :minimize-default-view.sync="permission" :annotation.sync="annotation" v-ref:image-gallery :client-pointer.sync="pointerFordoc" :clients.sync="clientContainer" :is-maximized="isMaximized" :box-element="boxEls" :is-staff="false"></ui-image-gallery>
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
          <!--<div class="row">
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
        v-on:toggle-screen-share="screenShare"
        v-on:toggle-annotation="toggleAnnotation"
        v-on:toggle-note-share="toggleNoteShare"
        v-on:toggle-doc-share="toggleDocShare"
        v-on:client-upload-file="clientUpload"
        v-on:toggle-webcam="toggleWebcam"
        v-on:toggle-subtitle="toggleSubtitle"
        v-on:toggle-url="toggleUrl"
        v-ref:staff-dashboard-footer-nav
    >
    </staff-dashboard-footer-nav>
  </div>
</template>
<script>
  import PeerConnect from './common/PeerConnect.vue';
  import UiImageGallery from './common/UiImageGallery.vue';
  import UiResizeable from './common/UiResizeable.vue';
  import ThankYou from './common/ThankYou.vue';
  import staff from '../js/staff.js';
  import browser from '../js/browser_detect.js';
  import errorLogs from '../js/error_logs.js';
  import DraggableDirective from '../directives/DraggableDirective.js';
  import StaffSidebar from './staff/partials/StaffSidebar.vue';
  import FilePreview from './common/FilePreview.vue';
  import {UiAlert , UiProgressLinear} from 'keen-ui';
  import OnclickToFrontDirective from '../directives/OnclickToFrontDirective.js';
  import StaffDashboardFooterNav from './staff/partials/StaffDashboardFooterNav.vue';
  // import StaffDocShare from './staff/partials/StaffDocShare.vue';
  import isMobile from '../js/isMobileDetect.js';
  import {APP_URL,PEERJS_API_KEY, PEERJS_HOST, profile , doc, hashUrl} from '../js/config.js';
  import helper from '../js/helpers.js';
  import {CLIENT_UPLOAD, CLIENT_EMPTY_DOCUMENTS} from '../js/api_routes.js';
  import Vue, {router} from '../js/app.js';
  import Cookie from 'js-cookie';
  import {tinymceConf} from "../js/config";
  import NewVue from 'vue';
  export default {
    data() {
      return {
        isUsingTemasys: false,
        screenLoaded: false,
        isMaximized: false,
        command: false,
        isRecord: false,
        vidSize:false,
        subtitle:[],
        sortedPages: [],
        isScreensharing:{
          active: false,
          started: false,
          client: null
        },
        docClient: {
          file: '',
          size: ''
        },
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
        canvasUsage: {
          fillStyle: '',
          strokeStyle: '',
          lineWidth: '',
          globalCompositeOperation: '',
          font: '',
          fillText: '',
          clientX: '',
          clientY: '',
          top: 1,
          left: 1,
          toolUsed: '',
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
        prevCon: [],
        mainCam: {
          id: '',
          video: '',
          audio: '',
          isSet: false
        },
        primaryCam: '',
        childCams: [],
        setOfColor: [],
        clients: [],
        APP_URL,
        shareLink: {
          isSet: false,
          text: '',
          textBoxLink: ''
        },
        sharelinkChecker: '',
        queueSnackbars: false,
        position: 'center',
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
        meeting_key: {
          id: '',
          isRemoved: false
        },
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
          share_voice_report: false,
          modal1: false,
          url: false,
          disable: true,
          uploaderror: false,
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
        noteShare: {
          disabled: false
        },
        video_enabled: true,
        showToolbar: false,
        notesPositionTop: 20,
        convertRequestCtr: 0,
        clientDoc: [],
        clientDocUrls: {
          documents: [],
          docpages: []
        },
        uploadSuccess: false,
        getSessionId: [],
        errorMsg:'',
        errorCompatibility: false,
        permission: false,
        annotation: '',
        initialMousemove : true,
        initialDocmousemove : true,
        action: '',
        duration: 5,
        actionColor: 'accent',
        message: '',
        loading: false,
        btnLoad: true,
        progress: 0,
        progressInterval: null,
        timeStatusInterval: 1000,
        convertSuccess: false,
        errorConvert: false,
        boxEls: [],
        newClientQueue: [],
        checkDataConInterval: '',
        parsedDateTime: '',
        // notesPositionTop: window.innerHeight, (It use by the window screen height)
      }
    },
    watch: {
      // clientupload: function(value){
      //      this.$nextTick(() => {
      //         let res = value ? true : false;
      //         this.clientupload = res
      //         this.$refs.staffDashboardFooterNav.file = res
      //     });
      // },
      // showUrl: function(value) {
      //     this.$nextTick(() => {
      //         let res = value ? true : false;
      //         this.showUrl = res
      //         this.$refs.staffDashboardFooterNav.url = res
      //     });
      // }
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action'};
          let functionName = 'Connect:isNoteActivePlaceholder';
          staff.checkError(errorStats, functionName);
        }
      },
      isNoteActive(active,id){
        try {
          let elmnts = [id + '-footer', id + '-resizeIcon'];
          let cond = ((isMobile.phone || isMobile.tablet));
          let elm = document.getElementById(id);

          if (active && elm.classList.contains('active-note')) return false;

          if (cond) {
            let toolbarSize = (active) ? -40 : 40;

            for (let i = elmnts.length; i--;) {
              let style = window.getComputedStyle(document.getElementById(elmnts[i]));
              let bottom = style.getPropertyValue('bottom').replace('px', '');

              document.getElementById(elmnts[i]).setAttribute('style', "bottom:" + (parseInt(bottom) + (toolbarSize)) + "px !important");

              if (active) elm.classList.add('active-note');
              else elm.classList.remove('active-note');

            }
          }
        }catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action'};
          let functionName = 'ClientConnect:isNoteActive';
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
          let functionName = 'ClientConnect:initTinyMCE';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Showing End Meeting Popup'}
          let functionName = 'Clientconnect:endMeetingPopup';
          helper.catchError(errorStats, functionName);
        }
      },
      thankyou() {
        try {
          router.go({ name: 'thank-you' })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Redirect to suggestion page'}
          let functionName = 'Clientconnect:thankyou';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Toggling showing of message'}
          let functionName = 'Clientconnect:toggleSnackbar';
          helper.catchError(errorStats, functionName);
        }
      },
      toggleUrl(){
        try {
          this.showUrl = true;
          this.show.share_doc_modal = false;
          this.show.share_notes_modal = false;
          this.show.share_clientdoc_modal = false;
          this.show.share_voice_report = false;
          this.show.modal1 = false;
          this.show.url = false;
          this.show.uploaderror = false;
          this.clientupload = false;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Toggling URL'}
          let functionName = 'Clientconnect:toggleURL';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Checking of notes'}
          let functionName = 'Clientconnect:notesCheck';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Toggling own webcam'}
          let functionName = 'Clientconnect:toggleWebcam';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Toggling Voice Recorder'}
          let functionName = 'Clientconnect:endMeetingPopup';
          helper.catchError(errorStats, functionName);
        }
      },
      setLink(id){
        try {
          if(!this.shareLink.isSet){
            this.shareLink.text = id
            this.shareLink.textBoxLink = id
            this.shareLink.isSet = (!!id) ? true : false;
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Setting the share link'}
          let functionName = 'Clientconnect:setLink';
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
            this.uploadSuccess = false;
          } else {
            this.show.disable = false;
            this.show.uploaderror = false;
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Uploading validation'}
          let functionName = 'Clientconnect:uploadValidation';
          helper.catchError(errorStats, functionName);
        }
      },
      convertDocument(){
        try {
          this.convertRequestCtr++
          // this.loading.submit = true
          this.loading = true
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
              console.log('clientDoc')
              console.log(this.clientDoc)
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
            let errorStats = {'status':e.status,'ErrorMsgFromPage': e.body.message}
            let functionName = 'Convert Document';
            let catchError = helper.catchError(errorStats, functionName);
          });
          this.btnLoad = false
          helper.progressConvertInterval(this.timeStatusInterval,this,this.clientFileSize)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Converting the document'}
          let functionName = 'Clientconnect:convertDocument';
          helper.catchError(errorStats, functionName);
        }
      },
      shareFileToAll(){
        try {
          this.clientupload = false
          // this.$refs.staffDashboardFooterNav.file = false
          // alert('ファイルが正常に共有されました !');
          let conn = this.getMeetingDataConnection()
          let myfile = this.file
          let myId = this.peer_id
          let blob = new Blob([myfile], {type: myfile.type});
          conn.connection.send({key: 'childFile', data: {file: myfile, blob: blob, name: myfile.name, type: myfile.type, sender: myId}})
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Sharing file to all'}
          let functionName = 'Clientconnect:shareFileToAll';
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
          conn.connection.send({key: 'childFileView', data: {data:this.pages, fromClient: false, id: this.peer_id}})

          this.clientupload = false;
          // let pages =this.pages
          // this.clients.forEach(function(elements){
          //     elements.conn.connection.send({key: 'childFileView', data: pages})
          // })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Viewing the file to parent'}
          let functionName = 'Clientconnect:viewFileToParent';
          helper.catchError(errorStats, functionName);
        }
      },
      getDocLink(doc) {
        try {
          let dateHolder = new Date();
          dateHolder.setMinutes(dateHolder.getMinutes() + 1);
          this.parsedDateTime = [Cookie.get('peerId'), dateHolder.getTime()].join('-');
          return `${APP_URL}/${this.parsedDateTime}/document/${doc.document_id}/download/${doc.file_name}`
          // return `${APP_URL}/document/${doc.document_id}/download/${doc.file_name}`
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Getting the document link'}
          let functionName = 'Clientconnect:getDocLink';
          helper.catchError(errorStats, functionName);
        }
      },
      uploadClicked(e) {
        e.preventDefault()
      },
      changeDocument() {
        try {
          this.loading = false // Hide the Progress Bar
          this.clientFileSize = this.bytesToSize(parseInt(this.$refs.uploadFile.value.size))
          let filename = this.$refs.uploadFile.value.name
          this.file = this.$refs.uploadFile.value
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Changing the document'}
          let functionName = 'Clientconnect:endMeetingPopup';
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
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Client uploading'}
          let functionName = 'Clientconnect:clientUpload';
          helper.catchError(errorStats, functionName);
        }
      },
      bytesToSize(bytes) {
        try {
          var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
          if (bytes == 0) return '0 Byte';
          var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
          return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Converting bytes to size'}
          let functionName = 'Clientconnect:bytesToSize';
          helper.catchError(errorStats, functionName);
        }
      },
      screenShare(){
        try {
          var isMobile = /iPhone|iPad|iPod|Android|Trident/i.test(navigator.userAgent);
          if (isMobile) {
            this.$broadcast('ui-snackbar::create', {
              message: 'モバイルデバイスでは画面共有機能をサポートしておりません。PCでご利用ください。',
              action: this.action,
              actionColor: this.actionColor,
              duration: this.duration * 1000
            });
          } else {
            this.$refs.peerConnect.screenShare();
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Triggering a screen share'}
          let functionName = 'ClientConnect:screenShare';
          helper.catchError(errorStats, functionName);
        }
      },
      beforeUnload() {
        // evt = evt || window.event
        //   if (evt) {
        //       this.endMeetingFn()
        //       evt.returnValue = 'Ending Meeting'
        //       setTimeout(function () {
        //           //not leaving
        //       }, 3000);
        //       return ''
        //   }
      },
      onbeforeunload(evt) {
        try {
          // evt = evt || window.event
          if (evt) {
            evt.preventDefault();
            this.closeTab()
            setTimeout(function () {
              evt.returnValue = 'Ending Meeting'
              return ''
            }, 3000);
          } else {
            this.closeTab();
            setTimeout(function(){
              /** do nothing */
            }, 10000);
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Processing task onbeforeunload'}
          let functionName = 'Clientconnect:onbeforeunload';
          helper.catchError(errorStats, functionName);
        }
      },
      mouseMove(evt) {
        try {
          if(!this.ownDocCoord.doctouch){
            let viewS = document.getElementById('viewerScreen');
            this.ownMouseCoordinates.top = (!!evt.clientY) ? evt.clientY : 0;
            this.ownMouseCoordinates.left = (!!evt.clientX) ? evt.clientX : 0;

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
            conn.connection.send({key: 'mouseMove', data: {data:ownMouse, id:myId }})
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':' Object doesnt support this action -> Tracking mouse movements'}
          let functionName = 'Clientconnect:mouseMove';
          helper.catchError(errorStats, functionName);
        }
      },
      updatePointer(evt_trigger) {
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Updating of the mouse pointer'}
          let functionName = 'Clientconnect:updatePointer';
          helper.catchError(errorStats, functionName);
        }
      },
      getMeetingDataConnection() {
        try {
          return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {label: 'meeting', type: 'data'})
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting data connection'}
          let functionName = 'Clientconnect:getMeetingDataConnection';
          helper.catchError(errorStats, functionName);
        }
      },
      getMeetingMediaConnection(peer_key) {
        try {
          return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {id: peer_key, type: 'media'})
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Getting media connection'}
          let functionName = 'Clientconnect:getMeetingMediaConnection';
          helper.catchError(errorStats, functionName);
        }
      },
      relayNotes() {
        // let conn = this.getMeetingDataConnection()
        // if(typeof conn != 'undefined') {
        //     conn.connection.send({key: 'notes', data: this.notes})
        //     conn.connection.send({key: 'otherClientNotes', data: this.notes, id:this.peer_id})
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
            // this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight + 'px';
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

            if(window.innerWidth >= 1024){
              this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 150 + 'px';
              if(window.innerWidth >= 1280){
                this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 153 + 'px';
              }
            }
          } else {
            this.videoUserFeed.maxWidth = '25%'
            this.$els.vidShare.style.top = this.vidShare.coordinates.top
            this.$els.vidShare.style.left = this.vidShare.coordinates.left
            this.$els.vidShare.style.transform = 'none'
            // this.$els.vidShare.style.maxWidth = '600px'
            // this.$els.vidShare.style.width = '480px'
            // this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = '360px'
            document.querySelector('.protoConnectContainer').style.position = 'absolute';
            document.querySelector('.protoConnectContainer').style.zIndex = 0;
            document.querySelector('.zoom_out_screen').style.right = '-15px';
            document.querySelector('.zoom_out_screen').style.top = '-20px';
            document.querySelector('.videoFeedCon').style.zIndex = 48;
            document.querySelector('.video-user-feed').style.width = '30%';
            document.querySelector('.videoFeedCon').style.height = 'auto';
            document.querySelector('.footer').style.borderTop = '';
            this.childCams.forEach(function(elements){
              elements.style.width = '25%'
            })
            let elem = document.querySelector('.own-feed')
            elem.classList.remove('minified-cams')
            this.$refs.peerConnect.addClasses = false;
            this.vidSize = false;
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Expanding the video share'}
          let functionName = 'Clientconnect:expandVidShare';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> File viewer on top'}
          let functionName = 'Clientconnect:fileViewerOnTop';
          helper.catchError(errorStats, functionName);
        }
      },
      forceOnTop(selector){
        try {
          const selectorClass = document.querySelector(selector).classList;
          selectorClass.add('forceOnTop');
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Force element to be on top'}
          let functionName = 'Clientconnect:forceOnTop';
          helper.catchError(errorStats, functionName);
        }
      },
      isAlreadyOnTop(selector){
        try {
          return !document.querySelector(selector).classList.value.includes('forceOnTop');
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Checks whether element is on top'}
          let functionName = 'Clientconnect:isAlreadyOnTop';
          helper.catchError(errorStats, functionName);
        }
      },
      isBoxHiding(selector){
        try {
          let selectorClass = document.querySelector(selector).class
          return (!!selectorClass) ? !selectorClass.search(/hide/) : false;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checks whether the box is hiding'}
          let functionName = 'Clientconnect:isBoxHiding';
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
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Toggling the Video Share'}
          let functionName = 'Clientconnect:toggleVidShare';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling of note share'}
          let functionName = 'Clientconnect:toggleNoteShare';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling of elements'}
          let functionName = 'Clientconnect:toggleElement';
          helper.catchError(errorStats, functionName);
        }
      },
      toggleSubtitle(){
        try {
          this.$refs.staffDashboardFooterNav.subtitle = this.isBoxHiding('.subtitle') ? false : true
          if(this.fileViewerOnTop() && this.isBoxHiding('.subtitle') && this.isAlreadyOnTop('.subtitle') && window.innerWidth > 480){
            this.forceOnTop('.subtitle');
          } else {
            this.toggleElement(this.$els.subTitle)
            if(this.isBoxHiding('.subtitle') && window.innerWidth > 480){
              this.forceOnTop('.subtitle');
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling of Subtitle'}
          let functionName = 'Clientconnect:toggleSubtitle';
          helper.catchError(errorStats, functionName);
        }
      },
      downloadDoc() {

        try {
          this.show.share_doc_modal = false
          helper.downloadFile(this.docLink);
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Downloading of document'}
          let functionName = 'Clientconnect:downloadDoc';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Downloading of client document'}
          let functionName = 'Clientconnect:downloadClientDoc';
          helper.catchError(errorStats, functionName);
        }
      },
      downloadNotes() {
        try {
          helper.downloadNotes(this.notesLink);
          this.show.share_notes_modal = false;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Downloading of notes'}
          let functionName = 'Clientconnect:downloadNotes';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Setting up the final closing of connection'}
          let functionName = 'Clientconnect:finalClosing';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Clearing of documents'}
          let functionName = 'Clientconnect:clearDocuments';
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

          document.removeEventListener('beforeunload', this.beforeunload)
          window.removeEventListener('beforeunload', this.onbeforeunload)
          document.removeEventListener('mousemove', this.mouseMove)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Closing the tab'}
          let functionName = 'Clientconnect:closeTab';
          helper.catchError(errorStats, functionName);
        }
      },
      endMeetingFn() {
        try {
          this.clearDocuments()
          // if(!this.endMeeting) {
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
          this.endMeeting = true
          this.connect.loaded = false
          this.showToolbar = false
          document.querySelector('.share_link').style.display = 'none';
          // Commented because connections are removed on the clients side not on this side
          // this.finalClosing()
          // this.endMeeting = true
          // this.connect.loaded = false
          // if(this.$refs.peerConnect.current_stream) {
          // }
          let camStatus = 'showCamera' + Cookie.get('peerId')
          Cookie.remove(camStatus)
          Cookie.remove('peerId')
          Cookie.remove('ParentNameHost')
          document.removeEventListener('beforeunload', this.beforeunload)
          window.removeEventListener('beforeunload', this.onbeforeunload)
          document.removeEventListener('mousemove', this.mouseMove)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Ending the meeting connection'}
          let functionName = 'Clientconnect:endMeetingFn';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Downloading the conversation'}
          let functionName = 'Clientconnect:downloadConversation';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Decoding Entities'}
          let functionName = 'Clientconnect:decodeEntities';
          helper.catchError(errorStats, functionName);
        }
      },
      listenToConnection(conn) {
        try {
          let interval = setInterval(() => {
            if(!conn.open) {
              this.endMeetingFn()
              clearInterval(interval)
            }
          },1000)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Listening to connection'}
          let functionName = 'Clientconnect:listenToConnection';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Getting the video connection'}
          let functionName = 'Clientconnect:getVideoConn';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Getting the audio connection'}
          let functionName = 'Clientconnect:getAudioConn';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the data connection'}
          let functionName = 'Clientconnect:getDataConnection';
          helper.catchError(errorStats, functionName);
        }
      },
      callClient(id){
        try {
          this.$refs.peerConnect.connectToPeer(id, 'meeting')
          this.$refs.peerConnect.initiateCall(id, false, this.callFailCB)
          // this.$refs.peerConnect.initiateCall(id, true)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Calling the client'}
          let functionName = 'Clientconnect:callClient';
          helper.catchError(errorStats, functionName);
        }
      },
      callFailCB() {
        try {
          this.video_call_failed = true
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Callsbacks for calling client'}
          let functionName = 'Clientconnect:callFailCB';
          helper.catchError(errorStats, functionName);
        }
      },
      // setMainCam(id){
      setMainCam(){
        try {
          let id = this.primaryCam
          if(!this.mainCam.isSet){
            let mainVid = this.getVideoConn(id)
            let mainAud = this.getAudioConn(id)
            this.mainCam.id = id
            this.mainCam.video = (mainVid.src_object == 'undefined') ? '' : mainVid.src_object;
            this.mainCam.audio = (mainAud.src_object == 'undefined') ? '' : mainAud.src_object;
            this.mainCam.isSet = true
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Setting the main camera'}
          let functionName = 'Clientconnect:setMainCam';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Setting child camera border'}
          let functionName = 'Clientconnect:endMeetingPopup';
          helper.catchError(errorStats, functionName);
        }
      },
      addClient(index){
        try {
          let id = this.newClientQueue[index]
          let testCon = this.getVideoConn(id)
          let testAud = this.getAudioConn(id)
          let conn = this.getDataConnection(id)
          // Check if there's connection
          if(testCon != null && testAud != null){
            // console.log("CONNECT")
            let isAdded = this.clients.findIndex(cont => cont.id === id)
            if(isAdded < 0){
              // Adding new Client
              this.setMainCam()
              this.setChildCam(id,testCon,testAud)
              let temp =  this.getDataConnection(id)
              let a = { id:id, conn:temp  }
              this.clients.push(a)
              let pos = this.newClientQueue.indexOf(id)
              this.newClientQueue.splice(pos,1)
              // this.$refs.peerConnect.addColor(id)
              this.checkDataConInterval = setInterval(this.checkDataConConnection(id), 1000)
            }
          } else {
            // console.log(testCon)
            // console.log(testAud)
            // // Make client to connect again
            // conn.connection.send({key: 'callFailed', data: this.peer_id})
            // let isAdded = this.clients.findIndex(cont => cont.id === id)
            // if(isAdded < 0){
            //     // Adding new Client
            //     let temp =  this.getDataConnection(id)
            //     let a = { id:id, conn:temp  }
            //     this.clients.push(a)
            // }
            this.callClient(id)
          }
          // Share link
          if(typeof(conn) != "undefined") {
            conn.connection.send({key: 'shareLink', data: this.shareLink.text})
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Adding Client'}
          let functionName = 'Clientconnect:addClient';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Checking data connection'}
          let functionName = 'Clientconnect:checkDataConConnection';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Adding client to a queue'}
          let functionName = 'Clientconnect:addClientQueue';
          helper.catchError(errorStats, functionName);
        }
      },
      passingColor(id){
        try {
          let colorConn = this.getDataConnection(id)
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'colorConn', data: colorConn.color})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Passing Color'}
          let functionName = 'Clientconnect:paddingColor';
          helper.catchError(errorStats, functionName);
        }
      },
      setChildCam(id,vid,aud){
        try {
          // let vid = this.getVideoConn(id)
          // let aud = this.getAudioConn(id)
          // if(vid != null && aud != null){
          let added = this.childCams.findIndex(cont => cont.id === id)
          if(added < 0){
            let childstyle = {
              position: 'relative !important',
              zIndex: '10 !important',
              float:'right',
              maxWidth: '25%',
              display: 'inline-block',
              border: '',
              width:''
            }
            // let colorConn = this.getDataConnection(id)
            childstyle.border = '2px solid'
            // vid.open = true
            let temp = {id: id, video: vid.src_object, audio: aud.src_object, open:true, style: childstyle}
            // this.childCams.push(temp)
            let vm=this;
            setTimeout(function(){
              vm.childCams.push(temp)
            },2000)
          } else {
            let conn = this.getDataConnection(id)
            conn.connection.send({key: 'callFailed', data: this.peer_id})
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Setting child camera'}
          let functionName = 'Clientconnect:setChildCam';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Removing Client'}
          let functionName = 'Clientconnect:removeClient';
          helper.catchError(errorStats, functionName);
        }
      },
      refreshCam(){
        try {
          let client_id = this.isScreensharing.client
          let vid = this.getVideoConn(client_id)
          if(vid != null) {
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Refreshing the camera'}
          let functionName = 'Clientconnect:refreshCam';
          helper.catchError(errorStats, functionName);
        }
      },
      speechFirstChildRecognitionStart(){ // This is first Client Speech Recognition Function
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

            let mult = this.getDataConnection()
            const poopScript = transcript.replace(/poop|poo|shit|dump/gi, '💩')
            if (e.results[0].isFinal) {
              let ownContent = [poopScript,this.peer_id]
              this.pushSpeechToArray(ownContent)
            }
          })
          recognition.addEventListener('end',recognition.start)
          recognition.start()
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting up the speech recognition'}
          let functionName = 'Clientconnect:speechFirstChildRecognitionStart';
          helper.catchError(errorStats, functionName);
        }
      },
      pushSpeechToArray(ownContent){
        try {
          if(this.command){
            let con = this.getMeetingDataConnection()
            if (typeof con != 'undefined'){
              con.connection.send({key:'sendAudioTranscription', data:ownContent})
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Pushing speech to array'}
          let functionName = 'Clientconnect:pushSpeechToArray';
          helper.catchError(errorStats, functionName);
        }
      },
      checkShareLink(urlShareLink,vm,sendLink){
        try {
          if (urlShareLink){
            urlShareLink = urlShareLink
            vm.setLink(urlShareLink)
            clearInterval(vm.sharelinkChecker);
          } else {
            let id = vm.peer_id
            sendLink.connection.send({key: 'sendShareLinktoClient', data: id });
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Checking of the share link'}
          let functionName = 'Clientconnect:checkShareLink';
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

          // if(window.innerWidth <= 767){
          //     document.querySelector('.vid-min').style.minHeight = !this.$refs.staffDashboardFooterNav.footer ? window.innerHeight - 120 + 'px' : window.innerHeight - 51 + 'px';
          // }

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
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 14%;';
          // } else if (window.innerWidth<=414){
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
          // } else if (window.innerWidth<=482){
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 15%;';
          // } else if (window.innerWidth<=641){
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 16%;';
          // } else if (window.innerWidth<=732){
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 16.5%;';
          // } else if (window.innerWidth<=768){
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
          // }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Handling of the Screen Resize'}
          let functionName = 'Clientconnect:handleResize';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Sorting of array'}
          let functionName = 'Clientconnect:sortArr';
          helper.catchError(errorStats, functionName);
        }
      },
      relayToOpenPage(data) {
        console.log("RELAY PREEEEE")
        // let conn = this.getMeetingDataConnection()
        // if(typeof conn != 'undefined') {
        //     conn.connection.send({key: 'onLoadedPage', data: data})
        // }
        // this.clients.forEach(function(elements){
        //     elements.conn.connection.send({key: 'onLoadedPage', data: data})
        // })
      }
    },
    events: {
      'peerconnect:peer:open' (vm, id) {
        try {
          this.peer_id = id
          Cookie.set('peerId', id)
          this.main.loading = false
          // console.log(this)
          // console.log(this.$route.params.id)
          // let newid =  this.$route.params.id
          // this.meeting_key.id = 'staff'+ newid
          // this.$refs.peerConnect.connectToPeer(this.meeting_key.id, 'meeting')
          // this.$refs.peerConnect.initiateCall(this.meeting_key.id, false, this.callFailCB)
          // this.$refs.peerConnect.initiateCall(this.meeting_key.id, true)
          this.$refs.peerConnect.isActive = true
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peerconnect open'}
          let functionName = 'Clientconnect::peerconnect:peer:open';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:peer:toggle:cam'(isActive){
        try {
          this.$refs.peerConnect.toggleOwnWebCam()
          for ( let key in this.childCams ){
            let index = this.getDataConnection(this.childCams[key].id)
            index.connection.send({key: 'toggleClientWebcam', data: {data:this.peer_id, status:isActive}})
          }
          let staff = this.getMeetingDataConnection()
          staff.connection.send({key: 'toggleClientWebcam', data: {data:this.peer_id, status:isActive}})
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect toggling of camera'}
          let functionName = 'Clientconnect::peerconnect:peer:open';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:peer:pluginChecker'(isPlugin){
        try {
          this.isUsingTemasys = isPlugin;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect updating temasys plugin variable'}
          let functionName = 'Clientconnect::peerconnect:peer:pluginChecker';
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
          let functionName = 'ClientConnect::peerconnect:peer:nodevice';
          staff.checkError(errorStats, functionName);
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
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peerconnect connection'}
          let functionName = 'Clientconnect::peerconnect:peer:connection';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:peer:closed' (vm) {
      },
      'peerconnect:peer:conn:close'(vm){
        //     if(!this.isScreensharing){
        //         let sample2 = this.getVideoConn(this.primaryCam)
        //         if(sample2 == undefined){
        //              this.endMeeting = true
        //              this.connect.loaded = false
        //              if(this.$refs.peerConnect.current_stream) {
        //                  this.$refs.peerConnect.current_stream.getTracks()[0].stop()
        //              }
        //              this.$refs.peerConnect.screen_share_stream = false
        //              this.$refs.peerConnect.stopCurrentStream()
        //              this.$refs.peerConnect.peer.destroy()
        //         }else{
        //         let arr = []
        //         this.clients.forEach(function(content){
        //             arr.push(content.id)
        //         })
        //         for(let x = 0; x < arr.length; x++){
        //             let sample = this.getVideoConn(arr[x])
        //             if(sample == undefined){
        //                 this.removeClient(arr[x])
        //             }
        //         }
        //     }
        // }
      },
      'peerconnect:peer:conn:open' (vm , conn) {
        try {
          setTimeout(function() {
            this.$refs.peerConnect.getStaffPeerKey()
            this.connect.loaded = true
            Cookie.set('peerId', this.peer_id)
            conn.send({key: 'initData', data: true});
            this.$refs.peerConnect.showVideoCall = true
            if(this.isScreensharing.client != null){
              this.refreshCam()
            }
            if(this.newClientQueue.length > 0){
              for(let x = 0; x < this.newClientQueue.length; x++){
                this.addClient(x)
              }
            }
            let vb = this
            let shareData = vb.shareLink.isSet
            if (shareData) {
            } else {
              let id = vb.peer_id
              conn.send({key: 'sendShareLinktoClient', data: id });
            }
            this.$refs.peerConnect.checkMediaConnection();
            this.$nextTick( () => {
              let main = this.getMeetingDataConnection()
              this.primaryCam = main.id
            })
            // this.setPrimaryCam()
            document.querySelector('.share_link').style.left = '1.7rem';
            document.querySelector('.share_link').style.zIndex = 1;
            document.querySelector('.ui-modal-mask').style.zIndex = 53;
          }.bind(this), 1000);
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peerconnect peer open'}
          let functionName = 'Clientconnect::peerconnect:peer:conn:open';
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
          let functionName = 'ClientConnect::peerconnect:peer:screen:loaded';
          staff.checkError(errorStats, functionName);
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
            case 'clientShareDoc':  this.show.share_clientdoc_modal = true;
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
              this.pages = []
              this.pages = data.data
              if (this.pages.length != 0) {
                this.show.file_view_modal = false
                document.querySelector('.videoFeedCon').style.top = '2%';
                document.querySelector('.videoFeedCon').style.left = '1%';
              }
              this.$refs.imageGallery.refreshCtr()
              break
            // case 'canvasUsage': this.canvasUsage = data.data
            //                 break
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

            case 'fromPageOnClick':
              // this.$refs.imageGallery.buttonShow = true;
              console.log("fromPageOnClick :::::: ",data.data)
              break
            case 'mouseMove': this.mouseCoordinates = data.data

              if(this.initialMousemove){
                this.mouseMove('')
                this.initialMousemove = false
              }

              if(this.mouseCoordinates.doctouch) {
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
              break;
            case 'getSessionId':
              this.getSessionId = data.data
              this.$refs.peerConnect.sessionIDsetColor(this.getSessionId)
              break;
            case 'sendAudioTranscription':
              let Subtitlecontent = document.getElementById('subtitle-content')
              shouldScroll = Subtitlecontent.scrollTop + Subtitlecontent.clientHeight === Subtitlecontent;

              if(this.isUsingTemasys) {
                  Subtitlecontent.innerHTML += '<p><span>' + data.data + '</span></p>';
              } else {
                  this.subtitle.push(data.data)
              }

              if(!shouldScroll) {
                Subtitlecontent.scrollTop = Subtitlecontent.scrollHeight;
              }
              break;
            case 'setOfColor':
              this.setOfColor = data.data
              break;
            case 'audioMute':
              this.$refs.peerConnect.toggleAudioMute(data.data)
              break;
            case 'endMeeting':
              this.endMeetingFn()
              break;
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
              break;
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
              break;
            case 'parentForceConnect':
              let parent_id = data.data
              let connec = this.getDataConnection(client_id)
              connec.connection.send({key: 'childCamReconnect', data: this.peer_id})
              break
            case 'shareLinkToClient':
            case 'shareLink':
              let sendLink = this.getMeetingDataConnection()
              let vm = this
              let urlShareLink = data.data
              this.sharelinkChecker = setInterval(vm.checkShareLink(urlShareLink,vm,sendLink),500);
              break;
            case 'endSession':
              this.endMeeting = true
              this.connect.loaded = false
              this.showToolbar = false
              document.querySelector('.share_link').style.display = 'none';
              if(this.$refs.peerConnect.current_stream){
                this.$refs.peerConnect.current_stream.getTracks()[0].stop()
              }
              this.$refs.peerConnect.screen_share_stream = false
              this.$refs.peerConnect.stopCurrentStream()
              this.$refs.peerConnect.peer.destroy()
              break;
            // Not used anymore
            // case 'callBackChildCam':
            //     let childkey = data.data
            //     let testingVid = this.getVideoConn(childkey)
            //     let testingAud = this.getAudioConn(childkey)
            //     break
            case 'screenShareStart':
              if(!this.screenLoaded) {
                this.main.loading = true
                this.mainCam.isSet = false
              }
              break;
            case 'endingChild':
              if(!!this.getDataConnection(data.data)) {
                this.$refs.imageGallery.onCanvasPostRecieve = false;
                this.removeClient(data.data)
                this.$refs.peerConnect.endChildConnections(data.data);
                this.message = '他のユーザーからのコールが終了しました。'
                this.endMeetingPopup()
              }
              break;
            case 'ChildConnect':
              // let clientid = data.data
              // let conn = this.getDataConnection(clientid)
              // if(typeof conn != 'undefined'){
              //     let meeting = this.getMeetingDataConnection()
              //     this.setMainCam(meeting.id)
              //     let isAdded = this.clients.findIndex(cont => cont.id === clientid )
              //      if(isAdded < 0){
              //         this.addClient(clientid)
              //         this.setChildCam(clientid)
              //     }
              // }else{
              // }
              break;
            case 'allChildCams':
              data = data.data
              let childIDS = data.childIDS
              let ownkey = this.peer_id
              // Filter the array without own id
              let childarray = childIDS.filter(function(num){
                return num != ownkey
              })
              for(let x = 0; x < childarray.length; x++){
                let id = childarray[x]
                let isAdded = this.clients.findIndex(cont => cont.id === id )
                if(isAdded < 0){
                  this.addClientQueue(id)
                }
              }
              break;
            case 'forcescreenshareReconnect':
              let forceid = data.data
              let dataConforce = this.getDataConnection(forceid)
              let vidforce = this.getVideoConn(forceid)
              if(vidforce != null){
                for(let key in this.childCams){
                  if(this.childCams[key].id == forceid){
                    this.childCams[key].video = vidforce.src_object
                    this.isScreensharing.client = null
                  }
                }
              } else {
                dataConforce.connection.send({key: 'clientRequestSreenshareReconnect', data: this.peer_id})
              }
              // this.refreshCam()
              break;
            case 'toggleClientWebcam':
              data = data.data
              let camStatus = 'showCamera' + data.data;
              Cookie.set(camStatus, data.status)

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
              break;
            case 'screenshareReconnect':
              this.screenLoaded  = false
              let reconnect_id = data.data
              if(this.mainCam.id == reconnect_id){
                this.mainCam.isSet = false
              }
              this.$refs.peerConnect.reconnectVideoConnection(reconnect_id);
              this.isScreensharing.client = null
              // this.isScreensharing.client = reconnect_id
              //  let dataCon = this.getDataConnection(reconnect_id)
              //  let vid = this.getVideoConn(reconnect_id)
              //     if(vid != null){
              //         for(let key in this.childCams){
              //             if(this.childCams[key].id == reconnect_id){
              //                 this.childCams[key].video = vid.src_object
              //                 this.isScreensharing.client = null
              //             }
              //         }
              //     }else{
              //         // dataCon.connection.send({key: 'clientRequestSreenshareReconnect', data: this.peer_id})
              //     }
              break;
            case 'clientRequestSreenshareReconnect':
              let client_id = data.data
              let conne = this.getDataConnection(client_id)
              conne.connection.send({key: 'forcescreenshareReconnect', data: this.peer_id})
              break;
            case 'getStaffPeerKey':
              let currentprimarycam = this.primaryCam
              let match_flag = (currentprimarycam == data.data) ? true : false
              if(!match_flag){
                this.$refs.peerConnect.endChildConnections(currentprimarycam);
                this.primaryCam = data.data
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
              break;
            case 'isConnected':
              let staffcon = this.getMeetingDataConnection()
              if(staffcon)staffcon.connection.send({key: 'connectedChild', data: this.peer_id})
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
            break
            default:
              this[data.key] = data.data
              break
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect data::' + data.key}
          let functionName = 'Clientconnect::peerconnect:peer:conn:data';
          // helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:screenshare:start'() {
        try {
          for ( let key in this.childCams ){
            let index = this.getDataConnection(this.childCams[key].id)
            index.connection.send({key: 'screenShareStart', data: this.peer_id})
          }
          let staff = this.getMeetingDataConnection()
          staff.connection.send({key: 'screenShareStart', data: this.peer_id})
          this.isScreensharing = true
          this.$refs.staffDashboardFooterNav.screenshare = true
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect start screensharing'}
          let functionName = 'Clientconnect::peerconnect:screenshare:start';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:peer:screenshare:close'() {
        try {
          let main = this.getMeetingDataConnection()
          let id = main.id
          this.$refs.peerConnect.initiateCall(id, false, this.callFailCB)
          this.$refs.peerConnect.initiateCall(id, true)
          this.primaryCam = id
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect closing screen sharing'}
          let functionName = 'Clientconnect::peerconnect:peer:screenshare:close';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:screenshare:endclient'() {
        try {
          this.isScreensharing = false
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect End client screen share'}
          let functionName = 'Clientconnect::peerconnect:screenshare:endclient';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:screenshare:removedclient'(id) {
      },
      'peerconnect:screenshare:end'(id) {
        try {
          this.isScreensharing.client = null
          for ( let key in this.childCams ){
            let index = this.getDataConnection(this.childCams[key].id)
            index.connection.send({key: 'screenshareReconnect', data: this.peer_id})
          }
          let staff = this.getMeetingDataConnection()
          if(staff) staff.connection.send({key: 'screenshareReconnect', data: this.peer_id})
          this.isScreensharing.active = false
          this.$refs.staffDashboardFooterNav.screenshare = false
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Peerconnect end the screensharing'}
          let functionName = 'Clientconnect::peerconnect:screenshare:end';
          helper.catchError(errorStats, functionName);
        }
        // let client_id = id
        //    let vid = this.getVideoConn(client_id)
        //         if(vid != null){
        //             for(let key in this.childCams){
        //                 if(this.childCams[key].id == client_id){
        //                     this.childCams[key].video = vid.src_object
        //                     this.isScreensharing.client = null
        //                 }
        //             }
        //         }else{
        //             let con = this.getDataConnection(client_id)
        //             if(typeof con != 'undefined'){
        //                 con.connection.send({key: 'clientRequestSreenshareReconnect', data: this.peer_id})
        //             }
        // }
      },
      'peerconnect:peer:conn:error' (vm, err) {
      },
      'keydown' (e) {
        try {
          this.relayNotes()
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Tracking keydown'}
          let functionName = 'Clientconnect::keydown';
          helper.catchError(errorStats, functionName);
        }
      },
      'changed' (e) {
        try {
          this.relayNotes()
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Tracking changes'}
          let functionName = 'Clientconnect::changed';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:BoxElement' (data){
        try {
          this.boxEls = data;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Box element of the Image Gallery'}
          let functionName = 'Clientconnect::ui-image-gallery:BoxElement';
          helper.catchError(errorStats, functionName);
        }
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
          this.$refs.imageGallery.hideMinDoc()
          if (typeof this.pages[id] != 'undefined') {
            this.annotation = this.pages[id].annotation
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Image gallery loaded'}
          let functionName = 'Clientconnect::ui-image-gallery:loaded';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:Minimized'() {
        try {
          this.isMaximized = false
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Image gallery minimized'}
          let functionName = 'Clientconnect::ui-image-gallery:Minimized';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:docCoord' (data,value){
        try {
          let ownCoordfromImageGallery = data
          this.ownDocCoord = ownCoordfromImageGallery
          document.addEventListener('mousemove', this.mouseMove)
          // this.initialDocmousemove = value
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Image gallery document coordinates'}
          let functionName = 'Clientconnect::ui-image-gallery:docCoord';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:getCoords' (data){
        try {
          this.ownDocCoord.onScreenHeight = data.height
          this.ownDocCoord.onScreenWidth = data.width
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Image gallery get coordinates'}
          let functionName = 'Clientconnect::ui-image-gallery:getCoords';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:release-paint' (data,canvasUpdated,fromBtnPage){
        //    console.log("RELEASE CLIENT: ")
        let conn = this.getMeetingDataConnection()
        if(typeof conn != 'undefined') {
          conn.connection.send({key: 'releasePaint', data: {data: data, canvasUpdated: canvasUpdated, fromBtnPage:fromBtnPage}})
        }
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'releasePaint', data: {data:data, canvasUpdated: canvasUpdated, fromBtnPage:fromBtnPage}})
        })
      },
      'ui-image-gallery:next-page' (id, canvasUpdated) {

        try {
          this.annotation = this.pages[id].annotation
          let conn = this.getMeetingDataConnection()
          if(typeof conn != 'undefined') {
            conn.connection.send({key: 'nextPage', data: true})
          }
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'nextPage', data:true})
          })

          if (canvasUpdated) {
            this.$refs.imageGallery.updatePage(true)
          } else {
            console.log("\n\tNOTHING TO UPDATE")
            // this.$refs.imageGallery.updateClearPage(true)
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Image gallery next page'}
          let functionName = 'Clientconnect::ui-image-gallery:next-page';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:prev-page' (id, canvasUpdated) {

        try {
          this.annotation = this.pages[id].annotation;
          let conn = this.getMeetingDataConnection();

          if(typeof conn != 'undefined') {
            conn.connection.send({key: 'prevPage', data: true})
          }
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'prevPage', data:true})
          })

          if (canvasUpdated) {
            this.$refs.imageGallery.updatePage(true)
          } else {
            console.log("\n\tNOTHING TO UPDATE")
            // this.$refs.imageGallery.updateClearPage(true)
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Image gallery previous page'}
          let functionName = 'Clientconnect::ui-image-gallery:prev-page';
          helper.catchError(errorStats, functionName);
        }
      },
      'ui-image-gallery:relayToOtherPage'(status) {
        let conn = this.getMeetingDataConnection()
        if(typeof conn != 'undefined') {
          conn.connection.send({key: 'fromPageOnClick', data: status})
        }
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'fromPageOnClick', data:status})
        })
      },
      'ui-image-gallery:oncanvas-post'(status) {
        let conn = this.getMeetingDataConnection()
        if(typeof conn != 'undefined') {
          conn.connection.send({key: 'oncanvasPost', data: status})
        }
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'oncanvasPost', data:status})
        })
      },
      'ui-image-gallery:to-waiting-page'(status,id) {
        // let conn = this.getMeetingDataConnection()
        // if(typeof conn != 'undefined') {
        //     conn.connection.send({key: 'onWaitingPage', data: status, id:id})
        // }
        // this.clients.forEach(function(elements){
        //     elements.conn.connection.send({key: 'onWaitingPage', data:status, id:id})
        // })
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
                }} catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Screen resizing'}
          let functionName = 'Clientconnect::resized';
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
                    this.noteSubtitle.disabled =false
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Stop resizing'}
          let functionName = 'Clientconnect::stop-resize';
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
      UiProgressLinear
      // ThankYou
      // StaffDocShare
    },
    created() {
      try {
        // document.addEventListener('beforeunload', this.beforeUnload)
        // document.removeEventListener('beforeunload', this.onbeforeunload)
        document.addEventListener('mousemove', this.mouseMove)
        new ClipboardJS('.share_link_btn');
        new ClipboardJS('.sharelinkButton');
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created client connect component'}
        let functionName = 'Clientconnect:created';
        helper.catchError(errorStats, functionName);
      }
      // document.beforeUnload = function(){
      //     return '';
      // }

      //  window.addEventListener("pageshow", function(){
      //     alert("page shown");
      // }, false)

    },
    ready() {
      try {
        // window.onunload = function(event) { this.onbeforeunload(event) }
        // this.initTinyMCE();
        window.onbeforeunload = function(event) { this.onbeforeunload(event) }
        // window.addEventListener('beforeunload', (event) => { this.onbeforeunload(event) }, false);
        window.addEventListener('unload', (event) => { this.onbeforeunload(event) }, false);
        let config = {
          role: 'guest',
          showToolbar: false
        }

        if(window.innerWidth <= 767){
          document.querySelector('.webcam-switch').style.bottom = 0;
          document.querySelector('.vid-min').style.marginBottom = '-50px';
          if(navigator.userAgent.indexOf('Android') > 0){
            document.querySelector('.vid-min').style.marginBottom = null;
          }
        }

        // if (window.innerWidth<=321){
        //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 14%;';
        // } else if (window.innerWidth<=414){
        //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
        // } else if (window.innerWidth<=482){
        //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 15%;';
        // } else if (window.innerWidth<=641){
        //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 16%;';
        // } else if (window.innerWidth<=732){
        //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 16.5%;';
        // } else if (window.innerWidth<=768){
        //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 18%;';
        // }

        //this.$els.vidShare.style.bottom = '0px'
        // if (window.innerWidth <= 768){
        //     this.toggleNoteShare();
        //     this.toggleSubtitle();
        // }

        //this.$els.vidShare.style.bottom = '0px'
        // if (window.innerWidth <= 768){
        //     this.toggleNoteShare();
        //     this.toggleSubtitle();
        // }

        if (window.innerWidth <= 1010){
          this.toggleNoteShare();
          this.toggleSubtitle();
        }

            window.addEventListener('resize', this.handleResize)
            let vm = this;
            document.addEventListener('visibilitychange', function() {
                if(/iPhone|iPad/i.test(navigator.userAgent) && !document.hidden) {
                    vm.$refs.peerConnect.toggleTracks()
                }
            })
            //  if(window.innerWidth<=320){
            //      document.querySelector('.videoFeedCon').style.maxWidth = '15% !important';
            // }
            // window.onbeforeunload = this.onbeforeunload
            // ON DEV CONSOLE COMMENT THIS IF IN DEVELOPMENT
            window.onerror = function(message, url, lineNumber) {
                // code to execute on an error
                return true; // prevents browser error messages
            };
            
            console.log = function(){};
            // END DEV CONSOLE

        this.$dispatch('component-ready', config);
        if (browser.detectIE().trigger) {
          this.errorCompatibility = true;
          // let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Browser Compatibility error'}
          // let functionName = 'Speech Recognition and Webrtc';
          // // this.checkError(errorStats,functionName);
          // helper.catchError(errorStats, functionName) ? this.errorCompatibility = true : this.errorCompatibility = false;
        }

        try{
          /** As of the moment, speech recognition is only supported in Chrome */
          if(/Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) {
            this.speechFirstChildRecognitionStart()
          }
        } catch(e){
          this.errorCompatibility = true;
          // // this.errorCompatibility = true;
          //  let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n  Object doesnt support this action -> Speech Recognition'}
          //  let functionName = 'Speech Recognition and Webrtc';
          //  // staff.checkError(errorStats,functionName);
          //  // this.errorCompatibility = true;
          //  helper.catchError(errorStats, functionName) ? this.errorCompatibility = true : this.errorCompatibility = false;
        }

        /** Set Cookie for the document security */
        Cookie.set('peerId', '')
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Client component ready'}
        let functionName = 'Clientconnect::peerconnect:peer:connection';
        helper.catchError(errorStats, functionName);
      }
    },
    beforeDestroy() {
      this.endMeetingFn()
    }
  }
</script>
<style lang="scss">
  .protoCon .overlay {
    background-color:#2b2a2a !important;
  }
  .hideLoad {display:none;}
  // .showLoad {}
  .errorConvertText {
    text-align: center;
    font-weight: 600;
    color: red;
  }
  #progressPercent {
    position: relative;
    font-weight: 800;
    color: #e3dddd;
    z-index: 1;
    text-align: -webkit-center;
    border-radius: 15px;
  }
  .ui-progress-linear {
    -webkit-box-shadow: rgba(0, 0, 0, 0.1) 0 1px 2px inset;
    -moz-box-shadow: rgba(0, 0, 0, 0.1) 0 1px 2px inset;
    box-shadow: rgba(0, 0, 0, 0.1) 0 1px 2px inset;
    position: relative;
    -webkit-border-radius: 25px;
    -moz-border-radius: 25px;
    -ms-border-radius: 25px;
    border-radius: 25px;
    background-color: #555555;
    height: 30px;
    padding: 5px;
    border-color: #484848 #484848 #414141;
    border-style: solid;
    border-width: 1px;
    .ui-progress-linear-determinate {
      content: "";
      position: absolute;
      overflow: hidden;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      z-index: 1;
      -webkit-animation: big 2s linear infinite;
      -moz-animation: big 2s linear infinite;
      -ms-animation: big 2s linear infinite;
      animation: big 2s linear infinite;
      background: -webkit-linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
      background: -moz-linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
      background: linear-gradient(-45deg, rgba(255, 255, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.2) 75%, transparent 75%, transparent);
      -webkit-background-size: 50px 50px;
      -moz-background-size: 50px 50px;
      background-size: 50px 50px;

      // Border
      -moz-border-radius-topleft: 20px;
      -webkit-border-top-left-radius: 20px;
      border-top-left-radius: 20px;
      -moz-border-radius-bottomleft: 20px;
      -webkit-border-bottom-left-radius: 20px;
      border-bottom-left-radius: 20px;
      -moz-border-radius-topright: 8px;
      -webkit-border-top-right-radius: 8px;
      border-top-right-radius: 8px;
      -moz-border-radius-bottomright: 8px;
      -webkit-border-bottom-right-radius: 8px;
      border-bottom-right-radius: 8px;
      background-color: #3cb371;
      position: absolute;
    }

  }
  .ui-progress-linear  > span {
    -webkit-box-shadow: rgba(255, 255, 255, 0.3) 0 2px 9px inset, rgba(0, 0, 0, 0.4) 0 -2px 6px inset;
    -moz-box-shadow: rgba(255, 255, 255, 0.3) 0 2px 9px inset, rgba(0, 0, 0, 0.4) 0 -2px 6px inset;
    box-shadow: rgba(255, 255, 255, 0.3) 0 2px 9px inset, rgba(0, 0, 0, 0.4) 0 -2px 6px inset;
    display: block;
    height: 100%;
    overflow: hidden;
    position: relative;
    background: #2ac052, -webkit-gradient(linear, 50% 100%, 50% 0%, color-stop(37%, #2ac052), color-stop(69%, #56f056));
    background: #2ac052, -webkit-linear-gradient(center bottom, #2ac052 37%, #56f056 69%);
    background: #2ac052, -moz-linear-gradient(center bottom, #2ac052 37%, #56f056 69%);
    background: #2ac052, linear-gradient(center bottom, #2ac052 37%, #56f056 69%);
  }


  @-moz-keyframes big {
    0% {
      background-position: 0 0;
    }

    100% {
      background-position: 50px 50px;
    }
  }

  @-webkit-keyframes big {
    0% {
      background-position: 0 0;
    }

    100% {
      background-position: 50px 50px;
    }
  }

  @-ms-keyframes big {
    0% {
      background-position: 0 0;
    }

    100% {
      background-position: 50px 50px;
    }
  }

  @keyframes big {
    0% {
      background-position: 0 0;
    }

    100% {
      background-position: 50px 50px;
    }
  }


  .fpreview-actions{
    .factions-btn{
      display:inline-block;
      width: 48% !important;
      // width: 49.5% !important;
      border-radius: 4px;
      height: 35px;
      background-color: rgb(56, 88, 153);
      margin-bottom: 20px;
      .ui-button-text{
        color: #fff;
        font-size: 16px !important;
      }
    }
    .btn-between-space {
      margin-right: 7px;
    }
  }
  .fpreview-upload[disabled] {
    opacity: 0.6;
    background-color: rgba(0, 0, 0, 0.38) !important;
    color: rgba(0, 0, 0, 0.50);
  }
  .fpreview-upload{
    margin: 0 !important;
  }
  .fpreview-btn{
    padding: 10px !important;
    border-radius: 20px;
    color: #eee !important;
    background-color: #3cb371;
  }
  .fpreview-size{
    font-family: 'NotoSans-Bold';
    font-size: 12px;
    color: rgba(0, 0, 0, 0.82);
    text-align: left;
    margin-top: -7px;
    margin-bottom: 20px;
  }
  .buttonCon{
    width: 100%;
  }
  .peer-connect {
    .video-container {
      width: 100%;
      video, object {
        width: 100%;
      }
    }
  }
  #staff-dashboard-footer .ui-toolbar .ui-toolbar-left .ui-icon-button {
    display: none;
  }
  #staff-dashboard-footer .ui-toolbar .ui-toolbar-title.appLogo img {
    margin-left: 20px;
  }
  .protoConnectContainer .wrapper {
    .zoom_out_screen {
      top: -35px;
      width: 70px !important;
      height: 70px !important;
      i {
        position: absolute;
        top: 7px;
        left: 16px;
        background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
        height: 43px;
        width: 43px;
        display: block;
        background-position-x: -529px;
      }
      span {
        font-family: 'NotoSans-Medium';
        color: #3cb371;
        bottom: 10px;
        position: absolute;
      }
    }
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
          bottom: -80px !important;
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
    margin-bottom: 1rem; //7.5rem
    // bottom: 8.5rem; //7.5rem
    display: inline-block;
    z-index: 48 !important;
    //.share_link_btn,.ui-textbox{
    // display: inline-block;
    //}
    .share_link_btn:hover{
      cursor: pointer;
    }
    //New added Css
    .ui-textbox{
      width: 330px;
      height:55px;
      display:table-caption !important;
    }
    .share_link_btn{
      position: initial !important;
      padding: 5px 2px 10px 2px !important;
      height: 50px;
      float: right !important;
      margin-top: -62px !important;
    }
    .meetingNotes {
      top: inherit;
    }
    //End
    .ui-textbox .ui-textbox-input, .ui-textbox .ui-textbox-textarea {
      color: rgba(0, 0, 0, 0.88) !important;
      float: left;
      width: 285px;
      height:50px;
      background-color: #ffffff;
    }
  }
  .share_link .share_link_btn{
    z-index: -1 !important;
    background-color: #3cb371;
    color: #ffff;
    //padding: 5px 2px 10px 2px;
    //margin-top: -66px;
    //position: absolute;
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
        width:285px!important;
        display:table-caption !important;
      }
      .share_link_btn{
        position: initial !important;
        padding: 5PX 2PX 7PX 2PX !important;
        height: 50px;
        float: right !important;
        margin-top: -62px !important;
      }
    }
    .meetingNotes {
      top: inherit;
    }
    .ui-textbox .ui-textbox-input, .ui-textbox .ui-textbox-textarea {
      width:100% !important;
      padding-right: 40px; //new added css
    }
  }
  //New added query(Angelica)
  //portrait
  @media (min-width: 768px) and (max-width: 1024px) {
    .share_link {
      .ui-textbox{
        width: 325px;
        height:55px;
        display:table-caption !important;
      }
      .share_link_btn{
        position: initial !important;
        padding: 5px 2px 10px 2px !important;
        height: 50px;
        float: right !important;
        margin-top: -62px !important;
      }
    }
    .meetingNotes {
      top: inherit;
    }
    .ui-textbox .ui-textbox-input, .ui-textbox .ui-textbox-textarea {
      // width:285px !important;
      width:100% !important;
    }
    .share_link {
      top: unset;
    }
  }//end query
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
  @media (min-width: 538px) and (max-width: 767px){
    .footer{
      height: 70px !important;

      .logo{
        display: none !important;
      }

      .footer_menu{
        float: none;
        width: 119% !important;
        padding: 0;

        li{
          a{
            padding: 5px 0px !important;

            .menu-icon{
              transform: scale(0.8);
              margin-bottom: 0 !important;
            }
          }
        }
      }
    }
  }
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

    .meetingNotes{
        width: 90%!important;
        right: 20px!important;
    }
}//end
//Display footer notes
@media only screen and (max-device-width: 480px) {
.meetingNotesWrapper {
    &.peer-ui-connect {
        .ui-resizeable, .meeting-note-footer{
           display: block!important;
        }
    }
}
}//end

  @media (min-width:440px) and (max-width:814px){

    // Boxes
    .subtitle{
      width: 400px!important;
      height: 30%!important;
    }


  }//end

  @media (max-width: 767px){
    .meetingNotes {
      bottom: 175px!important;
    }

    .subtitle{
      bottom: 160px!important;
    }
  }

// IE Support for CSS
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

