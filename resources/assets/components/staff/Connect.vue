<template>
  <div class="protoCon peerCon staffConnection">
    <div class="protoConnectContainer">
      <div class="wrapper noToolbar">
        <div v-for="client in clients" :key="client.id">
          <div class="pointer" v-bind:style.sync="client.stylePointer" id=pointer{{client.id}} elm={{client.pointerDoc.top}} v-el:pointer{{client.id}}>
            <ui-icon icon="pan_tool"></ui-icon>
          </div>
        </div>
        <div v-if="errorCompatibility">
          <ui-alert class="compatibility-alert" @dismiss="errorCompatibility = false" type="error" v-show="errorCompatibility">
            <!-- mee2box の全ての機能をお使いになりたい場合は Chrome の使用おすすめしております。 -->
            現在IEでは画面共有・レコーディング機能をご利用いただけません。
          </ui-alert>
        </div>

        <div v-if="showRecordingMessage" class="recording-message">
          <div class="record-box">        
            <div class="record-light"></div>           
            <span class="record-text">
              商談内容を録画しています。
            </span>            
          </div>
        </div>        

        <div class="overlay" v-if="main.loading && !invalidMeetingKey">
          <!-- <ui-alert class="compatibility-alert" v-if="errorCompatibility" @dismiss="errorCompatibility = false" type="error" v-show="errorCompatibility">
            mee2box の全ての機能をお使いになりたい場合は Chrome の使用おすすめしております。
          </ui-alert> -->
          <ui-progress-circular
              :show="main.loading" color="white" :value="main.progress"
          ></ui-progress-circular>
        </div>
        <div class="overlay row  middle-xs center-xs blurredBg" v-if="invalidMeetingKey">
          <div class="meetingMsg">
            <span>Invalid Meeting Key.</span>
          </div>
        </div>
        <div class="screen-sharing" v-show="$refs.peerConnect.screen_share_stream">
          <video id="shareScreenCon" v-el:share-screen-con muted playsinline autoplay></video>
        </div>
        <div class="row blurredBg noSelect" v-el:peer-container v-bind:class="[vidSize ? 'video-max' : 'video-min']" v-show="!main.loading && !invalidMeetingKey" >
          <div v-draggable v-onclickfront class="videoFeedCon videoParent withAttachFab"  v-el:vid-share :disabled="vidShare.disabled">
            <peer-connect :api-key="peer_api_key" :peer-host="peer_host" :peer-key-prefix="peer_key_prefix"
                          :show-audio-toggle="true" :video-enabled="video_enabled"
                          v-ref:peer-connect :primary-cam="primaryCam" :main-cam="mainCam" :child-cams="childCams" :clients="clients" :video-user-feed="videoUserFeed"></peer-connect>
            <button class="ui-fab zoom_out_screen ui-fab-mini icon-color"  v-if="video_enabled" @click="expandVidShare"> <!-- color-default -->
              <i class="zoom_out_screen_icon" aria-hidden="true"></i>
              <div class="ui-ripple-ink"></div>
              <!--  <span>最大化<span> -->
            </button>
          </div>
          <div v-draggable v-onclickfront class="meetingNotes" v-bind:style="{ height: meetingNotesTrue.height, width: meetingNotesTrue.width, top: meetingNotesTrue.top, left: meetingNotesTrue.left}" v-el:note-share :disabled="noteShare.disabled">
            <div class="meetingNotesWrapper">
              <div class="ui-close-button">
                <button class="ui-fab ui-fab-mini color-primary" @click="toggleNoteShare">
                  <i class="ui-icon material-icons ui-fab-icon close">close</i>
                  <div class="ui-ripple-ink"></div>

                </button>
              </div>
              <div class="meeting-note-head">ノート機能</div>
              <ui-textbox class="meeting-note-textarea"
                name="notes" :multi-line="true"
                autosize="true"
                placeholder="ノート"
                :value.sync="negotation.notes" id="notesID" v-on:input="notesBrad"
              ></ui-textbox>

              <div class="meeting-note-footer">
                <ui-button
                    type="flat" color="secondary" icon="save" tooltip="ノートを渡す" @click="sendNotes"
                    tooltip-position="bottom center"
                >ノートを渡す
                </ui-button>
              </div>
              <ui-resizeable class="withAttachFab adjusted"></ui-resizeable>
            </div>
          </div>

          <div v-draggable v-el:sales-share v-onclickfront class="meetingSales"  v-bind:style="{height: meetingSalesTrue.height, width: meetingSalesTrue.width, top: meetingSalesTrue.top, left: meetingSalesTrue.left}" :disabled="salesShare.disabled">
            <div class="meetingSalesWrapper">
              <div class="ui-close-button">
                <button class="ui-fab ui-fab-mini color-primary" @click="toggleSalesShare">
                  <i class="ui-icon material-icons ui-fab-icon close">close</i>
                  <div class="ui-ripple-ink"></div>

                </button>
              </div>
              <div class="meeting-sales-head">商談メモ</div>
              <ui-textbox class="meeting-sales-textarea"
                name="sales" :multi-line="true"
                autosize="true"
                placeholder="クライアントには表示されません。"
                :value.sync="memo"
                @blurred="updateBox()"
              ></ui-textbox> <!-- 商談ページに表示するメモを記入してください。 -->
              <div class="meeting-sales-footer">
                <p>自動で保存されます。</p>
              </div>
            <ui-resizeable class="withAttachFab"></ui-resizeable>
            </div>
          </div>
          <!-- <div v-draggable v-onclickfront  class="annotation" v-el:file-annotation>
                        <div class="ui-close-button">
                            <button class="ui-fab ui-fab-mini color-primary" @click="toggleAnnotation">
                                <i class="ui-icon material-icons ui-fab-icon close">close</i>
                                <div class="ui-ripple-ink"></div>
                            </button>
                        </div>
                        <h3 class="title">
                            <ui-icon icon="assignment"></ui-icon>
                            メモ
                        </h3>
                        <pre>{{ annotation }}</pre>
                    </div> -->
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
              <!--   <span> -->
              <ui-button
                  type="flat" color="secondary" icon="cloud_upload" tooltip="ノートを渡す"
                  @click="sendVoiceReport"
                  tooltip-position="bottom center">
                議事録を渡す
              </ui-button>
              <!-- </span> -->
            </div>
            <ui-resizeable class="withAttachFab adjusted"></ui-resizeable>
          </div>


          <div class="col-xs-12 fileViewer" v-el:adj-container>
            <div class="container">
              <div class="controlPanel">
                <ui-fab class="leftToggle" type="mini" icon="video_call"
                        @click="toggleVidShare"></ui-fab>
                <ui-fab class="leftToggle" type="mini" icon="note" @click="toggleNoteShare"></ui-fab>
                <ui-fab class="noteToggle" type="mini " icon="assignment"
                        @click="toggleAnnotation"></ui-fab>
                <ui-fab class="docToggle" type="mini" icon="description"
                        @click="toggleDocShare"></ui-fab>
              </div>
              <div class="viewer" id="viewerScreen" v-el:file-viewer>
                <!-- <span class="footerCopyright">&#169; 2017 Mee2box. All right Reserved.</span> -->
                <ui-image-gallery :image-list.sync="pages" :minimize-default-view.sync="permission" :annotation.sync="annotation" :document.sync="documentData" v-ref:image-gallery :clients.sync="clients" :is-maximized="isMaximized" :doc-from-client="docFromClient" :box-element="boxEls" :is-staff="true"></ui-image-gallery>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ui-modal hide-footer type="small" class="ask-record"
    :show-close-button="false"
    :backdrop-dismissible="false"
    :show.sync="showAskRecording">        
        <div slot="header">
            <div class="header-record">
                <span class="url-modal-header-text">レコーディング機能使用確認</span>
            </div>
        </div>
        <div class="center-xs ask-record-text">
            <span>商談内容を保存したい場合はYESをクリックしてください。レコーディングには画面共有機能が使われます。YESをクリックした後に全画面共有を許可してください。</span>
        </div>
        <div class="btn-group center-xs ask-record-button">
            <ui-button type="button" class="btn btn-primary" @click="recordAccepted">Yes</ui-button>
            <span style="padding-left:10px;padding-right:10px;"></span>
            <ui-button type="button" class="btn btn-primary" @click="recordDeclined">No</ui-button>
        </div>            
    </ui-modal>    

    <ui-modal
        :show.sync="showProgressBar" hide-footer header="" transition="ui-modal-fade" type="ui-modal-container"
        :show-close-button="false"
        :backdrop-dismissible="false">
            <p id="progressMessage" style="text-align:center;padding-bottom:40;margin:0;color:gray;font-size:15px;font-weight:600;">商談動画を保存しています。しばらくお待ちください。</p>                    
            <ui-progress-linear
                :show="loading" type="determinate" id="progressLinear" :value.prop="onUploadProgress" color="darkblue" :value="progress">  
            </ui-progress-linear>            
            <p>{{bytesToSize(progressVariable.loadedRate)}} - {{ bytesToSize(progressVariable.lastLoaded) }} of {{ bytesToSize(progressVariable.totalLoaded) }}, {{progressVariable.displayTime}} </p>
            <ui-button color="primary" class="new-dashboard-button" @click="openNewDashBoard()"> 新しく商談を開始 </ui-button>                      
    </ui-modal>    

    <!-- Document Modal -->
    <doc-view-modal  :show.sync="show.file_view_modal" class="file_view"></doc-view-modal>

    <!-- Share Doc Modal -->
    <ui-modal hide-footer type="small" :show.sync="show.share_doc_modal" class="downloadDoc">
      <div slot="header">
        <h1 class="ui-modal-header-text">
          <ui-icon icon="description"></ui-icon> 資料が届きました。
        </h1>
      </div>
      <ui-button icon="file_download" color="success" @click="downloadDoc">資料を受け取る。</ui-button>
      <div class="file-size">データサイズ ： {{ clientFileSize}}</div>
    </ui-modal>
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
    <!-- Ui Snackbar for toast when ending the meeting with the client -->
    <ui-snackbar-container :position="position" :queue-snackbars="queueSnackbars" style="z-index: 1000;"></ui-snackbar-container>
    <!-- End Ui Snackbar -->
    <staff-dashboard-footer-nav
        v-on:toggle-memo="toggleSalesShare"
        v-on:toggle-subtitle="toggleSubtitle"
        v-on:redirect-to-dashboard="redirectToDashboard"
        v-on:toggle-vid-share="toggleVidShare"
        v-on:toggle-url="toggleUrl"
        v-on:toggle-screen-share="screenShare"
        v-on:toggle-annotation="toggleAnnotation"
        v-on:toggle-note-share="toggleNoteShare"
        v-on:toggle-doc-share="toggleDocShare"
        v-on:file-upload="fileupload"
        v-ref:staff-dashboard-footer-nav
    >
    </staff-dashboard-footer-nav>
    <!-- Commented out for Prod -->
    <!--  v-on:toggle-webcam="toggleWebcam" -->
  </div>
</template>
<script>
  import DocViewModal from '../common/DocViewModal.vue';
  import PeerConnect from '../common/PeerConnect.vue';
  import UiImageGallery from '../common/UiImageGallery.vue';
  import UiResizeable from '../common/UiResizeable.vue';
  import UiCloseButton from '../common/UiCloseButton.vue';
  import StaffDocShare from './partials/StaffDocShare.vue';
  import StaffDashboardFooterNav from './partials/StaffDashboardFooterNav.vue';
  import DraggableDirective from '../../directives/DraggableDirective.js';
  import OnclickToFrontDirective from '../../directives/OnclickToFrontDirective.js';
  import {APP_URL, PEERJS_API_KEY, PEERJS_HOST, hashUrl} from '../../js/config.js';
  import {STAFF_DOCUMENTS_RESOURCE} from '../../js/api_routes.js';
  import auth from '../../js/auth.js';
  import staff from '../../js/staff.js';
  import helper from '../../js/helpers.js';
  import {router} from '../../js/app.js';
  import isMobile from '../../js/isMobileDetect.js';
  import Cookie from 'js-cookie';
  import browser from '../../js/browser_detect.js';
  import { UiModal, UiButton, UiTabs, UiTab, UiSnackbar, UiAlert } from 'keen-ui';
  import {tinymceConf} from "../../js/config";
  // m2b-81
  import MediaStreamRecorder from '../../js/MediaStreamRecorder.js';  
  import {staff_service} from '../../js/api_routes.js';
  import Vue from '../../js/app.js';
  // m2b-81    
  export default {
    data() {
      return {
        isUsingTemasys: false,
        screenLoaded: false,
        roleLoaded: false,command: false,
        vidSize:false,
        recognition:[],
        isRecord: false,
        isScreensharing:{
          active: false,
          started: false,
          client: null
        },
        meetingNotesTrue:{
          top:'',
                    left:'',width : '',
          height:''
        },
        meetingSalesTrue:{
          width : '',
          height:'',
                    top:'',
                    left:''
                },
                noteSubtitle:{
                    top:'',
                    left:'',
                    width : '',
                    height:'',
                    disabled: false
                },
        meetingNotesWidth:0,
        meetingNotesHeight:0,
        notesTopPosition:0,
        notesLeftPosition:0,
        meetingSalesWidth:0,
        meetingSalesHeight:0,
        salesTopPosition:0,
                salesLeftPosition:0,
                noteSubtitleWidth:0,
                noteSubtitleHeight:0,
                subtitleTopPosition:0,
                subtitleLeftPosition:0,videoUserFeed:{
          position: 'relative !important',
          zIndex: '10 !important',
          float:'right',
          maxWidth: '25%',
          display: 'inline-block',
          border: '',
        },
        subtitleColors: ['custom-staff','custom-child1','custom-child2','custom-child3'],
        pointerFordoc: {
          top: 100,
          left: 200,
          color: '',
          background: '',
          opacity: 0.7,
          position: 'absolute'
        },
        subtitle:[],
        childId:'',
        staff_id: '',
        prevCon: [],
        isExpanded: false,
        isEndedProperly: false,
        isCamProcessing: false,
        staffID: '',
        mainCam: {
          id: '',
          video: '',
          audio: '',
          isSet: false
        },
        primaryCam: '',
        childCams: [],
        childPointers: [{
          id: '',
          style:{
            color:''
          }
        }],
        shareLink: {
          isSet: false,
          text: '',
          textBoxLink: '',
          key: '',
          validated: false
        },
        clients: [],
        clientPointers: [],
        isClientAdded: true,
        colorFull: '',
        isChildSlotFull: false,
        position: 'center',
        queueSnackbars: false,
        action: '',
        duration: 5,
        actionColor: 'accent',
        message: '',
        memo: '',
        loadOnce: true,
        clientFileSize:[],
        childFile:[],
        auth: this.$parent.auth,
        APP_URL,
        static_images: {
          avatar: `${APP_URL}/image/avatar_flat.png`
        },
        meeting_key: {
          id: this.$route.params.id,
          isRemoved: false
        },
        peer_api_key: PEERJS_API_KEY,
        peer_host: PEERJS_HOST,
        peer_key_prefix: 'staff',
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
        mouseCoordinates: {
          id: '',
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
        salesShare: {
          disabled: false
        },
        video_call_failed: false,
        video_enabled: Cookie.get('video_enabled') == 'true' ? true : false,
        memoStyles: {
          top: (window.innerHeight - 800) > 0 ? window.innerHeight - 800 : 26,
          height: (window.innerHeight - 800) > 0 ? 300 : window.innerHeight / 4
        },
        salesStyles: {
          top: (window.innerHeight - 800) > 0 ? window.innerHeight - 800 : 26,
          height: (window.innerHeight - 800) > 0 ? 300 : window.innerHeight / 4
        },
        show: {
          share_doc_modal: false,
          file_view_modal: false,
          url: false,
          disable:true,
        },
        showUrl: false,
        sessionids:[],
        sessionPointercolor: [],
        errorCompatibility: false,
        documentData: { },
        permission: true,
        screen: {
          'width' : document.documentElement.clientWidth,
          'height' : document.documentElement.clientHeight
        },
        initialMousemove : true,
        initialDocmousemove : true,
        isMaximized: false,
        docFromClient: true,
        boxEls: [],
        connectedClients: [],
        pendingClients: [],
        checkClientFlag: false,
        sortedPages: [],
        role: '',
        multiStreamRecorder: null,
        screenStreamRecorder: null,
        audio_blob: [],
        screen_blob: null,
        screen_name: '',
        audio_name: '',
        videos_name: '',
        avCreateIsDone: false,	
        screen_stream: [],
        audio_stream: [],
        gotScreenBlob: false,
        showAskRecording: false,
        isConfirmRecording: false,
        file_link: '',  
        showProgressBar: false,       
        doneCreateScreenFiles: false,              
        doneCreateAudioFiles: false,
        loading: false,
        progress: 0,
        progressVariable: {
          loadedRate: 0,
          lastLoaded: 0,
          totalLoaded: 0,
          displayTime: ''
        },
        donutShowReloadBox: false,
        dashBoardLink: `${APP_URL}/#/staff/dashboard`,
        showRecordingMessage: false,
        isHasAudioBlob: false
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action'};
          let functionName = 'Connect:isNoteActivePlaceholder';
          staff.checkError(errorStats, functionName);
        }
      },
      isNoteActive(active, id) {
        try{
          let elmnts = [id + '-footer', id + '-resizeIcon'];
          let cond = ((isMobile.phone || isMobile.tablet));
          let elm = document.getElementById(id);

          if (active && elm.classList.contains('active-note')) return false;

          if (cond) {
            let toolbarSize = (active) ? -40 : 40;

            for (let i = elmnts.length; i--;) {
              let style = window.getComputedStyle(document.getElementById(elmnts[i]));
              let bottom = style.getPropertyValue('bottom').replace('px', '');

              document.getElementById(elmnts[i]).style.bottom = (parseInt(bottom) + (toolbarSize)) + "px";

              if (active) elm.classList.add('active-note');
              else elm.classList.remove('active-note');

            }
          }
        }catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action'};
          let functionName = 'Connect:isNoteActive';
          staff.checkError(errorStats, functionName);
        }
        
      },
      initTinyMCE(){
        try {
          let cond = ((isMobile.phone || isMobile.tablet));
          let tinymceConfig = cond ? tinymceConf.mobile : tinymceConf.desktop;
          let saveNotes = this;
          let elmnts = ["#businessNote", "#notesID"];

          tinymceConfig.init_instance_callback = function(editor){
            editor.on('NodeChange', function (e) {
              saveNotes.notesCheck();
            });
          };

          for (let i= elmnts.length;i--;){
            let ftc = cond ? tinymceConfig.fixed_toolbar_container : false;
            tinymceConfig.selector = elmnts[i];

            if(cond) {
              tinymceConfig.fixed_toolbar_container += elmnts[i].replace("#","-");
            } else{
              let removeElm = "custom-toolbar-mce";
              document.getElementById( removeElm += elmnts[i].replace("#","-")).outerHTML = '';
            }

            tinymce.init(tinymceConfig).then(function(e){});
            tinymceConfig.fixed_toolbar_container = ftc;
          }
        }catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action'};
          let functionName = 'Connect:initTinyMCE';
          staff.checkError(errorStats, functionName);
        }
      },
      childConnectionChecker(){
        try {
          let vm = this
          this.clients.forEach(function(elements){
            setTimeout(elements.conn.connection.send({key: 'isConnected', data: vm.staff_id}), 1000)
            if(vm.pendingClients.indexOf(elements.id) < 0 ){
              vm.pendingClients.push(elements.id)
            }
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Child Connection Checker'}
          let functionName = 'Connect:childConnectionChecker';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> User exits the meeting'}
          let functionName = 'Connect:toggleSnackbar';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleUrl(){
        try {
          this.showUrl = true;
          this.show.share_doc_modal = false;
          this.show.file_view_modal = false;
          // this.$refs.staffDashboardFooterNav.url = true
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Showing of the URL'}
          let functionName = 'Connect:toggleUrl';
          staff.checkError(errorStats, functionName);
        }
      },
      notesCheck(){
        try {
          let notes = document.getElementById("notesID");
          let childElmnts = notes.getElementsByTagName('span');

          for (let i= childElmnts.length;i--;) {
            childElmnts[i].removeAttribute('style');
            if(childElmnts[i].getAttribute('data-mce-style') !==null){
              childElmnts[i].setAttribute('style',childElmnts[i].getAttribute('data-mce-style'));
            }
          }
          
          this.negotation.notes = (notes !== null)? notes.innerHTML : '';
          let x = this.negotation.notes;
          
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'notes', data: x})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Informing other users of the notes'}
          let functionName = 'Connect:notesCheck';
          staff.checkError(errorStats, functionName);
        }
      },
      notesBrad(){
        try {
          this.negotation.notes = document.getElementById("notesID").value;
          let x = this.negotation.notes
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'notes', data: x})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Informing other users of the notes'}
          let functionName = 'Connect:notesBrad';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleWebcam(){
        try {
          this.$refs.peerConnect.toggleOwnWebCam()
          let id = this.staffID
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'toggleClientWebcam', data: id})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Informing other users of the videobox status'}
          let functionName = 'Connect:toggleWebcam';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Voice Recorder'}
          let functionName = 'Connect:toggleVoiceRecorder';
          staff.checkError(errorStats, functionName);
        }
      },
      // setLink(){
      //     let oldid = this.$refs.peerConnect.peerKey.split('/');
      //     let newid = oldid[0].replace('staff', '');
      //     let path = this.APP_URL + "/#/" + "connect/" + newid;
      //     this.shareLink = path;
      // },
      endMeetingPopup(){
        try {
          this.$broadcast('ui-snackbar::create', {
            message: this.message,
            action: this.action,
            actionColor: this.actionColor,
            duration: this.duration * 1000
          });
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Current user exited the meeting'}
          let functionName = 'Connect:endMeetingPopup';
          staff.checkError(errorStats, functionName);
        }
      },
      downloadDoc(){
        try {
          this.show.share_doc_modal = false
          const e = document.createEvent('MouseEvents'),
            a = document.createElement('a');
          a.download = this.childFile.name;
          a.href = window.URL.createObjectURL(this.childFile);
          a.dataset.downloadurl = ['text/json', a.download, a.href].join(':');
          e.initEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
          a.dispatchEvent(e);
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Downloading the documents/file'}
          let functionName = 'Connect:downloadDoc';
          staff.checkError(errorStats, functionName);
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
            // m2b-81
            if(!this.$refs.peerConnect.is_screen_sharing) {
                if(this.$refs.peerConnect.isScreensharedStaff) {                    
                    this.$refs.peerConnect.screenShareStaff();
                } else {                    
                    this.$refs.peerConnect.screenShare();
                }
            } else {
                this.$broadcast('ui-snackbar::create',{
                    message: 'スクリーン共有は既に始まっています',
                    duration: 3000
                })                            
            }                        
            // m2b-81              
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Trigger to start Screen Share'}
          let functionName = 'Connect:screenShare';
          staff.checkError(errorStats, functionName);
        }
      },
      connectMeeting(){
        try {
          let id = this.meeting_key.id
          Cookie.set('peerId', id)
          this.$refs.peerConnect.connectToPeer(id, 'meeting')
          this.$refs.peerConnect.initiateCall(id, false, this.callFailCB)
          // this.$refs.peerConnect.initiateCall(id, true)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> User connects into the meeting'}
          let functionName = 'Connect:connectMeeting';
          staff.checkError(errorStats, functionName);
        }
      },
      callFailCB() {
        try {
          this.video_call_failed = true
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Assigning value to video_call_failed'}
          let functionName = 'Connect:callFailCB';
          staff.checkError(errorStats, functionName);
        }
      },
      getMeetingDataConnection() {
        try {
          return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {
            label: 'meeting',
            type: 'data'
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting user data connection'}
          let functionName = 'Connect:getMeetingDataConnection';
          staff.checkError(errorStats, functionName);
        }
      },
      getMeetingMediaConnection(peer_key) {
        try {
          return this.$refs.peerConnect.getMatchedConn(this.$refs.peerConnect.connections, {
            id: peer_key,
            type: 'media'
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting user media connection'}
          let functionName = 'Connect:getMeetingMediaConnection';
          staff.checkError(errorStats, functionName);
        }
      },
      // m2b-81
      pageXbuttonClicked(evt) {
        try {
          if (evt) {
            if(!this.donutShowReloadBox) {
              evt.preventDefault();
              evt.returnValue = "adding this evt.returnValue to ask the user if will continue quitting the page!";
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> page Xbutton Clicked Confirm exit Function'}
          let functionName = 'Connect:pageXbuttonClicked';
          staff.checkError(errorStats, functionName);
        }           
      },      
      // m2b-81
      onbeforeunload(evt) {
        try {
          this.donutShowReloadBox = true;
          //  evt = evt || window.event
          if (evt) {
            evt.preventDefault();
            this.endMeetingFn()
            setTimeout(function () {
              evt.returnValue = 'Ending Meeting'
              return ''
            }, 10000);
          } else {
            this.endMeetingFn();
            setTimeout(function() {
              /** do nothing */
            }, 10000);
          }

        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Executing processes before unload'}
          let functionName = 'Connect:onbeforeunload';
          staff.checkError(errorStats, functionName);
        }
      },
      // onbeforeunload(evt) {
      //     evt = evt || window.event
      //     if (evt) {
      //         this.endMeetingFn()
      //         evt.returnValue = 'Ending Meeting'
      //         setTimeout(function () {
      //             //not leaving
      //         }, 3000);
      //         return ''
      //     }
      // },
     endMeetingFn() {
        try {
          // m2b-81
          // this will terminate speechRecognition instance and 
          // will also remove the camera/audio device usage to allow open a new dashboard to start a new session
          if(this.recognition != null) {
            if(this.recognition.removeEventListener) this.recognition.removeEventListener('end',this.recognition.start)
            if(this.recognition.stop) this.recognition.stop();
            this.recognition = null;
            window.SpeechRecognition = null;
          }
          // m2b-81

          let vm = this
          if(!!this.clients) {
            this.clients.forEach(function(elements){
              elements.conn.connection.send({key: 'endingChild', data: vm.staffID })
            })
          }

          // this.clients = [];
          // this.childCams = [];
          // this.mainCam = [];
          // this.childFile = [];
          // this.connectedClients = [];
          // this.pendingClients = [];

          this.updateNegotation()
          clearInterval(this.timerInterval)
          staff.stopMeeting(this, this.role)

          // if (!!this.$refs.peerConnect.current_stream) {
          //   var tracks = this.$refs.peerConnect.current_stream.getTracks();
          //   if(tracks.length > 0) {
          //     tracks[0].stop()
          //     tracks.forEach(track => track.stop());
          //     this.$refs.peerConnect.current_stream.enabled = false;
          //   }
          // }

          // if (!!this.$refs.peerConnect.video_src_object) {
          //   var tracks = this.$refs.peerConnect.video_src_object.getTracks();
          //   if(tracks.length > 0) {
          //     tracks[0].stop()
          //     tracks.forEach(track => track.stop());
          //     this.$refs.peerConnect.video_src_object.enabled = false;
          //   }
          // }

          if (!!this.$refs.peerConnect.current_stream) {
            this.$refs.peerConnect.current_stream.getTracks()[0].stop()
            this.$refs.peerConnect.current_stream = null;
          }

          if (!!this.$refs.peerConnect.video_src_object) {
            this.$refs.peerConnect.video_src_object.getTracks().forEach(track => track.stop())
            this.$refs.peerConnect.video_src_object = null;
          }          

          this.$refs.peerConnect.screen_share_stream = false
          this.$refs.peerConnect.stopCurrentStream()          
          // this.$refs.peerConnect.peer.destroy()
          this.endMeeting = true
          Cookie.remove('peerId')
          Cookie.remove('ParentNameHost')

          // document.removeEventListener('beforeunload', this.onbeforeunload)

          // window.removeEventListener('beforeunload', this.onbeforeunload)

          document.removeEventListener('mousemove', this.mouseMove)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Executings processes to end the meeting'}
          let functionName = 'Connect:endMeetingFn';
          staff.checkError(errorStats, functionName);
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
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Downloading Conversation after ending the meeting'}
          let functionName = 'Connect:downloadConversation';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Decode Html Values inside an array to pass to the arguments'}
          let functionName = 'Connect:decodeEntities';
          staff.checkError(errorStats, functionName);
        }
      },
      mouseMove(evt) {
        try {
          if(!this.ownDocCoord.doctouch){
            let viewS = document.getElementById('viewerScreen');
            this.ownMouseCoordinates.top = (!!evt.clientY) ? evt.clientY : 0 ;
            this.ownMouseCoordinates.left = (!!evt.clientX) ? evt.clientX : 0 ;
            if(!!viewS){
              this.ownMouseCoordinates.onScreenHeight = viewS.clientHeight
              this.ownMouseCoordinates.onScreenWidth  = viewS.clientWidth
            }

            this.ownMouseCoordinates.percentageHeight = (0 >= this.ownMouseCoordinates.top || this.ownMouseCoordinates.onScreenHeight == 0) ? 0 : parseFloat(this.ownMouseCoordinates.top / this.ownMouseCoordinates.onScreenHeight)
            this.ownMouseCoordinates.percentageWidth  = (0 >= this.ownMouseCoordinates.left || this.ownMouseCoordinates.onScreenWidth == 0) ? 0 : parseFloat(this.ownMouseCoordinates.left / this.ownMouseCoordinates.onScreenWidth)
          }
          let ownMouse = (this.ownDocCoord.doctouch) ? this.ownDocCoord : this.ownMouseCoordinates
          // let conn = this.getMeetingDataConnection()
          // if(typeof conn != 'undefined') {
          //     conn.connection.send({key: 'mouseMove', data: ownMouse })
          // }
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'mouseMove', data: ownMouse})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the user mouse movements'}
          let functionName = 'Connect:mouseMove';
          staff.checkError(errorStats, functionName);
        }
      },
      updatePointer() {
        try {
          let vm = this
          let ownScreenHeight = vm.ownMouseCoordinates.onScreenHeight
          let ownScreenWidth  = vm.ownMouseCoordinates.onScreenWidth
          let ownDocScreenHeight = vm.ownDocCoord.onScreenHeight
          let ownDocScreenWidth  = vm.ownDocCoord.onScreenWidth
          this.clients.forEach(function(elements){
            let id = 'pointer'+elements.id
            let pointerId = document.getElementById(id)
            if (!!pointerId){
              if(elements.clientpointer.doctouch == true){
                pointerId.style.opacity = 0
                elements.pointerDoc.opacity = 0.7
                elements.pointerDoc.position = 'absolute'
                elements.pointerDoc.top = (ownDocScreenHeight * elements.clientpointer.percentageHeight) - 10 + 'px'
                elements.pointerDoc.left = (ownDocScreenWidth * elements.clientpointer.percentageWidth) - 15 + 'px'
                elements.pointerDoc.color = elements.stylePointer.color
                elements.pointerDoc.background = elements.stylePointer.background
                // vm.pointerFordoc.opacity = 0.7
                // vm.pointerFordoc.position = 'absolute'
                // vm.pointerFordoc.top = (ownDocScreenHeight * elements.clientpointer.percentageHeight) - 10 + 'px'
                // vm.pointerFordoc.left = (ownDocScreenWidth * elements.clientpointer.percentageWidth) - 15 + 'px'
                // vm.pointerFordoc.color = elements.stylePointer.color
                // vm.pointerFordoc.background = elements.stylePointer.background

                // console.log("\n---------------------\n TRUE NI "+vm.mouseCoordinates.doctouch+" x "+elements.clientpointer.doctouch+"\n---------------------\n")
              } else {
                elements.pointerDoc.opacity = 0
                elements.pointerDoc.position = 'absolute'
                // elements.pointerDoc.top = (ownDocScreenHeight * elements.clientpointer.percentageHeight) - 10 + 'px'
                // elements.pointerDoc.left = (ownDocScreenWidth * elements.clientpointer.percentageWidth) - 15 + 'px'
                elements.pointerDoc.top = '-50 px'
                elements.pointerDoc.left = '-50 px'
                elements.pointerDoc.color = elements.stylePointer.color
                elements.pointerDoc.background = elements.stylePointer.background
                // vm.pointerFordoc.opacity = 0
                // vm.pointerFordoc.position = 'absolute'
                // vm.pointerFordoc.top = (ownScreenHeight * elements.clientpointer.percentageHeight) - 10 + 'px'
                // vm.pointerFordoc.left = (ownScreenWidth * elements.clientpointer.percentageWidth) - 15 + 'px'
                // vm.pointerFordoc.color = elements.stylePointer.color
                // vm.pointerFordoc.background = elements.stylePointer.background

                // console.log("\n---------------------\n FALSE NI "+vm.mouseCoordinates.doctouch+" x "+elements.clientpointer.doctouch+"\n---------------------\n")
                pointerId.style.opacity = 0.7
                pointerId.style.top = `${(ownScreenHeight * elements.clientpointer.percentageHeight) - 10}px`
                pointerId.style.left = `${(ownScreenWidth * elements.clientpointer.percentageWidth) - 15}px`
              }
            }
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the mouse pointer'}
          let functionName = 'Connect:updatePointer';
          staff.checkError(errorStats, functionName);
        }
      },
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
      updateNegotation() {
        try {
          console.log(this.negotation)
          let data = new FormData();
          data.append('_method', 'PUT')
          for (var key in this.negotation) {
            data.append(key, this.negotation[key])
          }
          staff.updateNegotation(this, {id: this.negotation.negotation_id}, data, this.role)
          console.log("UPDATE NEGO")
          console.log(this.negotation)
          console.log(data)
          console.log(staff.updateNegotation(this, {id: this.negotation.negotation_id}, data, this.role))
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the negotation texts'}
          let functionName = 'Connect:updateNegotation';
          staff.checkError(errorStats, functionName);
        }
      },
      relayNotes(){
        // let notes = this.negotation.notes;
        // this.clients.forEach(function(elements){
        //    elements.conn.connection.send({key: 'notes', data: notes})
        // })
      },
      relayChildNotes(id){
        try {
          let notes = this.negotation.notes;
          let child = this.clients.filter(function(num){
            return num.id != id
          })
          if(child != null){
            child.forEach(function(elements){
              elements.conn.connection.send({key: 'notes', data: notes})
            })
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Informing child about the notes'}
          let functionName = 'Connect:relayChildNotes';
          staff.checkError(errorStats, functionName);
        }
      },
      expandVidShare() {
        try {
          this.vidShare.disabled = this.vidShare.disabled ? false : true
          if (this.vidShare.disabled) {
            this.videoUserFeed.maxWidth = '15%'
            this.vidShare.coordinates.top = this.$els.vidShare.style.top
            this.vidShare.coordinates.left = this.$els.vidShare.style.left
            this.$els.vidShare.style.top = '0'
            this.$els.vidShare.style.left = '0'
            this.$els.vidShare.style.maxWidth = '100%'
            this.$els.vidShare.style.width = '100%'
            // this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight + 'px';
            // this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight + 'px';
            document.querySelector('.protoConnectContainer').style.position = 'absolute';
            document.querySelector('.protoConnectContainer').style.zIndex = 1;
            document.querySelector('.zoom_out_screen').style.right = '10px';
            document.querySelector('.zoom_out_screen').style.top = '10px';
            document.querySelector('.videoFeedCon').style.zIndex = 110;
            document.querySelector('.videoFeedCon').style.height = '100%';
            document.querySelector('.footer').style.borderTop = '1px solid #232323';
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
            // this.$els.vidShare.style.width = '500px'
            // this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = '300px'
            document.querySelector('.protoConnectContainer').style.position = 'absolute';
            document.querySelector('.protoConnectContainer').style.zIndex = 0;
            document.querySelector('.zoom_out_screen').style.right = '-15px';
            document.querySelector('.zoom_out_screen').style.top = '-20px';
            document.querySelector('.videoFeedCon').style.zIndex = 45;
            document.querySelector('.videoFeedCon').style.height = 'auto';
            document.querySelector('.footer').style.borderTop = '';
            let vm = this
            this.childCams.forEach(function(elements){
              elements.style.width = '25%';
            })
            let elem = document.querySelector('.own-feed')
            elem.classList.remove('minified-cams')
            this.$refs.peerConnect.addClasses = false;
            this.vidSize = false;

          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Expanding the Video Box'}
          let functionName = 'Connect:expandVidShare';
          staff.checkError(errorStats, functionName);
        }
      },
      sendNotes(){
        try {
          staff.createNotes({staff_id : '1', notes: encodeURIComponent(this.negotation.notes)}, this.role).then((response) => {
            if (response) {
              // let conn = this.getMeetingDataConnection();
              // if (typeof conn != 'undefined') {
              //     conn.connection.send({key: 'sendNotes', data: response});
              // }
              this.clients.forEach(function(elements){
                elements.conn.connection.send({key: 'sendNotes', data: response})
              })
            }
          });
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> User send a notes update'}
          let functionName = 'Connect:sendNotes';
          staff.checkError(errorStats, functionName);
        }
      },
      sendVoiceReport(){
        try {
          this.clients.forEach(function(el){
            el.conn.connection.send({key:'sendVoiceReport'})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> User sends a voice report'}
          let functionName = 'Connect:sendVoiceReport';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> returns true if the file viewer is on top'}
          let functionName = 'Connect:fileViewerOnTop';
          staff.checkError(errorStats, functionName);
        }
      },
      forceOnTop(selector){
        try {
          const selectorClass = document.querySelector(selector).classList;
          selectorClass.add('forceOnTop');
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Forcing the container to be on top'}
          let functionName = 'Connect:forceOnTop';
          staff.checkError(errorStats, functionName);
        }
      },
      isAlreadyOnTop(selector){
        try {
          return !document.querySelector(selector).classList.value.includes('forceOnTop');
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> returns a boolean which tells if the container is already on top'}
          let functionName = 'Connect:isAlreadyOnTop';
          staff.checkError(errorStats, functionName);
        }
      },
      isBoxHiding(selector){
        try {
          let selectorClass = document.querySelector(selector).class
          return (!!selectorClass) ? !selectorClass.search(/hide/) : false;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Determines whether the box is hiding'}
          let functionName = 'Connect:isBoxHiding';
          staff.checkError(errorStats, functionName);
        }
      },

      toggleVidShare() {
        try {
          this.$refs.staffDashboardFooterNav.vidshare = this.isBoxHiding('.videoFeedCon') ? false : true

          if( this.isBoxHiding('.videoFeedCon') ){
            // this.$els.salesShare.style.top = '';
            // this.$els.salesShare.style.height = '';
            // this.$els.vidShare.style.top = '';
          }else{
            this.forceOnTop('.videoFeedCon');
            // this.$els.salesShare.style.top = '30px';
            //this.$els.salesShare.style.height = '30%';
            // this.$els.vidShare.style.top = '35px';
          }

          if(this.fileViewerOnTop() && this.isBoxHiding('.videoFeedCon') && this.isAlreadyOnTop('.videoFeedCon') && window.innerWidth > 480){
            // this.forceOnTop('.videoFeedCon');
          } else {
            this.toggleElement(this.$els.vidShare);
            if(this.isBoxHiding('.videoFeedCon') && window.innerWidth > 480){
              // this.forceOnTop('.videoFeedCon');
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggle Video Sharing'}
          let functionName = 'Connect:toggleVidShare';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleNoteShare() {
        try {
          this.$refs.staffDashboardFooterNav.note = this.isBoxHiding('.meetingNotes') ? false : true

          if( this.isBoxHiding('.meetingNotes') ){
            // this.$els.salesShare.style.top = '';
            // this.$els.salesShare.style.height = '';
            // this.$els.vidShare.style.top = '';
          }else{
            this.forceOnTop('.meetingNotes');
            // this.toggleElement(this.$els.noteShare)
            // this.$els.salesShare.style.top = '30px';
            //this.$els.salesShare.style.height = '30%';
            // this.$els.vidShare.style.top = '35px';
          }

          if(this.fileViewerOnTop() && this.isBoxHiding('.meetingNotes') && this.isAlreadyOnTop('.meetingNotes') && window.innerWidth > 480){
            this.forceOnTop('.meetingNotes');
          } else {
            this.toggleElement(this.$els.noteShare)
            if(this.isBoxHiding('.meetingNotes') && window.innerWidth > 480){
              this.forceOnTop('.meetingNotes');
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Click the note sharing'}
          let functionName = 'Connect:toggleNoteShare';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleSalesShare() {
        try {
          this.$refs.staffDashboardFooterNav.memo = this.isBoxHiding('.meetingSales') ? false : true

          if( this.isBoxHiding('.meetingSales') ){
            // this.$els.salesShare.style.top = '';
            // this.$els.salesShare.style.height = '';
            // this.$els.vidShare.style.top = '';
          }else{
            this.forceOnTop('.meetingSales');
            // this.toggleElement(this.$els.salesShare)
            // this.$els.salesShare.style.top = '30px';
            //this.$els.salesShare.style.height = '30%';
            // this.$els.vidShare.style.top = '35px';
          }

          if(this.fileViewerOnTop() && this.isBoxHiding('.meetingSales') && this.isAlreadyOnTop('.meetingSales') && window.innerWidth > 480){
            this.forceOnTop('.meetingSales');
          } else {
            this.toggleElement(this.$els.salesShare)
            if(this.isBoxHiding('.meetingSales') && window.innerWidth > 480){
              this.forceOnTop('.meetingSales');
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Click the sale share'}
          let functionName = 'Connect:toggleSalesShare';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleSubtitle(){
        try {
          this.$refs.staffDashboardFooterNav.subtitle = this.isBoxHiding('.subtitle') ? false : true

          if( this.isBoxHiding('.subtitle') ){
            this.$els.salesShare.style.top = '';
            this.$els.salesShare.style.height = '';
            // this.$els.vidShare.style.top = '';
          }else{
            this.forceOnTop('.subtitle');
            this.$els.salesShare.style.top = '50px';
            this.$els.salesShare.style.height = '30%';
            // this.$els.vidShare.style.top = '35px';
          }

          if(this.fileViewerOnTop() && this.isBoxHiding('.subtitle') && this.isAlreadyOnTop('.subtitle') && window.innerWidth > 480){
            this.forceOnTop('.subtitle');
          } else {
            this.toggleElement(this.$els.subTitle)
            if(this.isBoxHiding('.subtitle') && window.innerWidth > 480){
              this.forceOnTop('.subtitle');
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> User toggles the subtitle'}
          let functionName = 'Connect:toggleSubtitle';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleHide(value){
        try {
          for (let i = 0; i < value.length; i++) {
            if (value[i] === 'hide') {
              return false;
            }
          }
          return true;
        }  catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> toggles the hide button'}
          let functionName = 'Connect:toggleHide';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleAnnotation() {

        // this.$refs.staffDashboardFooterNav.annotation = this.isBoxHiding('.annotation') ? false : true

        // if(this.fileViewerOnTop() && this.isBoxHiding('.annotation') && this.isAlreadyOnTop('.annotation') && window.innerWidth > 480){
        //     this.forceOnTop('.annotation');
        // } else {
        //     this.toggleElement(this.$els.fileAnnotation);
        //     if(this.isBoxHiding('.annotation') && window.innerWidth > 480){
        //         this.forceOnTop('.annotation');
        //     }
        // }
      },
      toggleDocShare() {
        try {
          this.show.file_view_modal = true;
          this.show.share_doc_modal = false;
          this.showUrl = false;
          //this.show.share_doc_modal = true;
          // Not used anymore
          // var sidebar = this.$refs.docShare.$children[0];
          // sidebar.toggle();
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggles the document sharing'}
          let functionName = 'Connect:toggleDocShare';
          staff.checkError(errorStats, functionName);
        }
      },
      toggleElement(el) {
        try {
          let has_el = helper.hasClass(el, 'hide')
          if (has_el) {
            el.classList.remove('hide')
          } else {
            el.classList.add('hide')
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Add class "hide" into the HTML Element'}
          let functionName = 'Connect:toggleElement';
          staff.checkError(errorStats, functionName);
        }
      },
      getDocsCB(response) {
        try {
          if (response.data) {
            response.data.documents.forEach((val, key) => {
              this.documents.push(val)
            })
            let paginate = response.data.meta.pagination
            if (paginate.total_pages > paginate.current_page){
              this.show_more_docs_btn = true
            } else {
              this.show_more_docs_btn = false
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> User exits the meeting'}
          let functionName = 'Connect:toggleSnackbar';
          staff.checkError(errorStats, functionName);
        }
      },
      afterInit() {
        try {
          let conn = this.getMeetingDataConnection()
          if(conn) conn.connection.send({key: 'video_enabled', data: this.video_enabled})
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Enabling video after initialization'}
          let functionName = 'Connect:afterInit';
          staff.checkError(errorStats, functionName);
        }
      },
      // m2b-81
      stopRecordingCallback() {
        try {
          this.screen_blob = this.screenStreamRecorder.getBlob();
          this.screenStreamRecorder.destroy();
          this.screenStreamRecorder = null;   

          if (this.$refs.peerConnect.screen_recording_stream) {
              this.$refs.peerConnect.screen_recording_stream.getTracks()[0].stop(); 
              this.$refs.peerConnect.screen_recording_stream = null;
          }
          this.screen_stream = null
          this.gotScreenBlob = true;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Stop Screen Recording'}
          let functionName = 'Connect:stopRecordingCallback';
          staff.checkError(errorStats, functionName);
        }          
      },    
      // m2b-81        
      redirectToDashboard(){        
        try {

          this.donutShowReloadBox = true;
          this.endMeetingFn()

          if(this.isConfirmRecording && this.$refs.peerConnect.isRecordingStarted) {  
            document.querySelector('.footer').style.zIndex = 0;
            // m2b-81
            this.screenStreamRecorder.stopRecording(this.stopRecordingCallback);             
            var retval = window.setInterval(function(){
              if(this.gotScreenBlob) { 
                clearInterval(retval); 

                this.multiStreamRecorder.stop(); 
                var hasBlob = window.setInterval(function(){
                  if(this.isHasAudioBlob) {
                    clearInterval(hasBlob); 

                    this.createAudioScreenFiles(this.audio_blob); 
                    var retnew = window.setInterval(function(){
                      if(this.avCreateIsDone) {
                        clearInterval(retnew); 
                        router.go({
                          name: `${this.auth.user.role}-dashboard`
                        })
                        setTimeout(function () {
                          window.location.reload()
                        }, 1000);                        
                      }
                    }.bind(this), 1000);           
                  }
                }.bind(this), 1000); 
              }				                                    
            }.bind(this), 1000); 
            // m2b-81                            
          } else if(this.$refs.peerConnect.isStopRecordingStaff) {  

            if(!this.doneCreateAudioFiles) {
              document.querySelector('.footer').style.zIndex = 0;              

              this.main.loading = true;
              this.loading = true;
              this.showProgressBar = true;            
            }

            var recstop = window.setInterval(function(){
              if(this.doneCreateAudioFiles) {
                clearInterval(recstop); 
                
                var retends = window.setInterval(function(){
                  if(this.avCreateIsDone) {
                    clearInterval(retends); 
                    router.go({
                      name: `${this.auth.user.role}-dashboard`
                    })
                    setTimeout(function () {
                      window.location.reload()
                    }, 1000);                        
                  }
                }.bind(this), 1000);          

              }
            }.bind(this), 1000);               

          } else {

            router.go({
              name: `${this.auth.user.role}-dashboard`
            })
            setTimeout(function () {
              window.location.reload()
            }, 1000);

          }

        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Redirecting to the dashboard page'}
          let functionName = 'Connect:redirectToDashboard';
          staff.checkError(errorStats, functionName);
        }
      },
      updateBox(){
        try {
          let data = {memo: this.memo};
          staff.updateMemo(this, data);
        }  catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the Memo Box'}
          let functionName = 'Connect:updateBox';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the video connection'}
          let functionName = 'Connect:getVideoConn';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the Audio Connection'}
          let functionName = 'Connect:getAudioConn';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the Data Connection'}
          let functionName = 'Connect:getDataConnection';
          staff.checkError(errorStats, functionName);
        }
      },
      addClient(id){
        try {
          let testCon = this.getVideoConn(id)
          let testAud = this.getAudioConn(id)
          let clientsMousepointer = this.mouseCoordinates

          // Check if there's connection
          if(testCon != null && testAud != null){
            let isAdded = this.clients.findIndex(cont => cont.id === id)
            if(isAdded < 0){
              // Adding new Client
              let temp =  this.getDataConnection(id)
              temp.connection.send({key: 'isConnected', data: this.staff_id})
              if(this.connectedClients.indexOf(id) < 0){
                this.connectedClients.push(id)
              }
              let childpointerstyle = {
                color: 'white',
                background: '',
              }
              let emptyDoc = {
                background : '',
                color : 'white',
                left : 0,
                opacity : 0,
                position : 'absolute',
                top : 0,
              }
              childpointerstyle.background = temp.color
              emptyDoc.background = temp.color
              let a = { id:id, conn:temp , clientpointer:clientsMousepointer, stylePointer: childpointerstyle, pointerDoc:emptyDoc}
              this.clients.push(a)
              this.$refs.peerConnect.addColor(id)
              // STaff cam
              if(this.$refs.peerConnect.toggleStaffCatch()){
                this.$refs.peerConnect.isActive = false;
                this.$refs.peerConnect.hideOwnWebCam();
                let id = this.staffID
                temp.connection.send({key: 'toggleClientWebcam', data: {data:id, status:false}})
              }
              // Primary cam
              let primary = this.$refs.peerConnect.togglePrimaryCatch()
              if(primary) {
                let id = this.primaryCam
                temp.connection.send({key: 'toggleFirChildWebcam', data: {data:id, status:primary}})
              }
              // first and Second
              let child = this.childCams

              for (let key in child){
                if (child[key].open == false){
                  let id = child[key].id
                  let status = true
                  temp.connection.send({key: 'toggleSecChildWebcam', data: {data:id, status:status}})
                }
              }
            }
          }else{
            let conn = this.getDataConnection(id)
            this.$refs.peerConnect.checkMediaConnection()
            conn.connection.send({key: 'callFailed', data: this.staffID})
            let isAdded = this.clients.findIndex(cont => cont.id === id)
            if(isAdded < 0){
              // Adding new Client
              let temp =  this.getDataConnection(id)
              temp.connection.send({key: 'isConnected', data: this.staff_id})
              if(this.connectedClients.indexOf(id) < 0){
                this.connectedClients.push(id)
              }
              let childpointerstyle = {
                color: 'white',
                background: ''
              }
              let emptyDoc = {
                background : '',
                color : 'white',
                left : 0,
                opacity : 0,
                position : 'absolute',
                top : 0,
              }
              childpointerstyle.background = temp.color
              emptyDoc.background = temp.color
              let a = { id:id, conn:temp , clientpointer:clientsMousepointer, stylePointer: childpointerstyle, pointerDoc:emptyDoc}
              this.clients.push(a)

              if(Cookie.get('video_toggled') == 'false'){
                this.$refs.peerConnect.isActive = false;
                this.$refs.peerConnect.hideOwnWebCam();
                let id = this.staffID
                temp.connection.send({key: 'toggleClientWebcam', data: {data:id, status:false}})
              }
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding New Client into the meeting'}
          let functionName = 'Connect:addClient';
          staff.checkError(errorStats, functionName);
        }
      },
      updateColorPointer(){
        try {
          let clients = this.clients
          let sessionId = this.sessionids
          const colors = ['black','#333333','green','red']
          for(let key in sessionId){
            for(let index in clients){
              if (sessionId[key] == clients[index].id){
                clients[index].stylePointer.background = colors[key]
                clients[index].pointerDoc.background = colors[key]
              }
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the Color Pointer'}
          let functionName = 'Connect:updateColorPointer';
          staff.checkError(errorStats, functionName);
        }
      },
      setMainCam(id){
        try {
          if(this.mainCam.id == ''){
            let mainVid = this.getVideoConn(id)
            let mainAud = this.getAudioConn(id)

            this.mainCam.id = id
            this.mainCam.video = mainVid.src_object ? mainVid.src_object : ''
            this.mainCam.audio = mainAud.src_object ? mainAud.src_object : ''
            this.mainCam.isSet = true
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting the Main Camera'}
          let functionName = 'Connect:setMainCam';
          staff.checkError(errorStats, functionName);
        }
      },
      setLink(){
        try {
          if(!this.shareLink.isSet){
            for (var i = 0; i < 10; i++){
              hashUrl.shuffle += hashUrl.possible.charAt(Math.floor(Math.random() * hashUrl.possible.length));
            }
            let oldid = this.staffID.replace('staff', '');
            let path = this.APP_URL + "/#/" + "connect/" + oldid +"/"+ hashUrl.shuffle;
            this.shareLink.text = path
            this.shareLink.key = oldid+ hashUrl.shuffle
            this.shareLink.textBoxLink = this.shareLink.text
            this.shareLink.isSet = true
          } else {
            this.shareLink.textBoxLink = this.shareLink.text
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting the share link'}
          let functionName = 'Connect:setLink';
          staff.checkError(errorStats, functionName);
        }
      },
      validateClientKey(key, clientid){
        try {
          this.shareLink.validated = (this.shareLink.key === key) ? true : false
          let conn = this.getDataConnection(clientid)
          let isValid = !this.shareLink.validated ? false : true
          conn.connection.send({key: 'validKey', data: isValid})
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Validating the Client Key'}
          let functionName = 'Connect:validateClientKey';
          staff.checkError(errorStats, functionName);
        }
      },
      setChildCam(id){
        try {
          let vid = this.getVideoConn(id)
          let aud = this.getAudioConn(id)
          if(vid != null && aud != null){
            let childstyle = {   position: 'relative !important',
              zIndex: '10 !important',
              float:'right',
              maxWidth: '25%',
              display: 'inline-block',
              border: '',
              width:''}
            let colorConn = this.getDataConnection(id)
            childstyle.border = '2px solid '+colorConn.color
            vid.open = true
            let temp = {id: id, video: vid.src_object, audio: aud.src_object, open:vid.open, style: childstyle}
            this.childCams.push(temp)

            // M2B-81
            this.audio_stream = [];
            var add_flag = false;
            let self = this;
            this.$refs.peerConnect.peer.connections[id].forEach(function(stream) {
              if(stream.remoteStream) {
                self.audio_stream.push(stream.remoteStream);
                add_flag = true;
              }                        
              if(stream.localStream) {
                self.audio_stream.push(stream.localStream);
                add_flag = true;
              }
            }); 

            if(add_flag) this.processAudioScreen(id);   
            // M2B-81	

          }else{
            let conn = this.getDataConnection(id)
            conn.connection.send({key: 'parentForceConnect', data: this.staffID})
          }
          this.isCamProcessing = false
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting the child camera'}
          let functionName = 'Connect:setChildCam';
          staff.checkError(errorStats, functionName);
        }
      },
      removeClient(id){
        try {
          this.meeting_key.isRemoved = (id == this.sessionids[1]) ? true : false

          // Update Client Array
          this.clients = this.clients.filter((item) => item.id !== id)
          // Update ChildCams Array
          this.childCams = this.childCams.filter((item) => item.id !== id)
          // Updating session Array
          this.sessionids = this.sessionids.filter((item) => item !== id)

          if(this.meeting_key.isRemoved){
            this.meeting_key.id = this.sessionids[1]
            this.primaryCam = this.meeting_key.id
          }

          this.clientCamUpdate(id)
          this.updateMainCam(id)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Removing the Client'}
          let functionName = 'Connect:removeClient';
          staff.checkError(errorStats, functionName);
        }
      },
      updateMainCam(id){
        try {
          if(id == this.mainCam.id && this.clients.length != 0){
            let newID = this.clients[0].id
            let mainVid = this.getVideoConn(newID)
            let mainAud = this.getAudioConn(newID)

            this.mainCam.id = newID
            this.mainCam.video = mainVid ? mainVid.src_object : '';
            this.mainCam.audio = mainAud ? mainAud.src_object : '';
            let oldChildCams = this.childCams.filter((item) => item.id !== newID)
            this.childCams = oldChildCams
            this.primaryCam = newID
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the Main Camera'}
          let functionName = 'Connect:updateMainCam';
          staff.checkError(errorStats, functionName);
        }
      },
      checkChildSlot(id){
        try {
          if(this.sessionids.length == 4){
            this.isChildSlotFull = true
            let conn = this.getDataConnection(id)
            conn.connection.send({key: 'roomFull'})
          } else {
            this.isChildSlotFull = false
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking child slot'}
          let functionName = 'Connect:checkChildSlot';
          staff.checkError(errorStats, functionName);
        }
      },
      shareClients(){
        try {
          let arraay = []
          this.clients.forEach(function(elements){
            arraay.push(elements.id)
          })
          this.clients.forEach(function(elements){
            setTimeout(elements.conn.connection.send({key: 'allChildCams', data: {childIDS: arraay}}), 1000)
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Share all child cameras'}
          let functionName = 'Connect:shareClients';
          staff.checkError(errorStats, functionName);
        }
      },
      clientCamUpdate(id){
        try {
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'clientRemoved', data: id})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Client camera update'}
          let functionName = 'Connect:clientCamUpdate';
          staff.checkError(errorStats, functionName);
        }
      },

      refreshCam(){
        try {
          let id = this.isScreensharing.client

          let vid = this.getVideoConn(id)
          if(vid != null){
            console.log('refreshCam:::', this.childCams)
            for(let key in this.childCams){
              if(this.childCams[key].id == id){
                this.childCams[key].video = vid.src_object
                this.isScreensharing.client = null
              }
            }
          }else{
            let con = this.getDataConnection(this.isScreensharing.client)
            con.connection.send({key: 'clientRequestSreenshareReconnect', data: this.staffID})
          }
        }  catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Refreshing the Camera'}
          let functionName = 'Connect:refreshCam';
          staff.checkError(errorStats, functionName);
        }
      },
      sendSetId(data){
        try {
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'getSessionId', data: data})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Send the session ID'}
          let functionName = 'Connect:sendSetId';
          staff.checkError(errorStats, functionName);
        }
      },
      speechRecognitionStart(){ //Adding module for audio transcription
        try {
          let vm = this
          let interval = setInterval(() => {
            let staff_id = this.staff_id
            let staffid = (!!staff_id) ? staff_id.replace(/^staff+/i, '') : '' ;
            let firstchildid = this.primaryCam
            if(!!staffid && this.sessionids.length == 0){
              this.sessionids.push(staffid)
            }
            if(!!firstchildid && this.sessionids.length == 0){
              this.sessionids.push(firstchildid)
            }
            if(this.sessionids.length > 0){
              if(this.sessionids.indexOf(staffid)<0){
                this.sessionids.push(staffid)
              }
              if(this.sessionids.indexOf(firstchildid)<0){
                this.sessionids.push(firstchildid)
              }
              for(let key in this.childCams){
                if(this.sessionids.indexOf(this.childCams[key].id) < 0){
                  this.sessionids.push(this.childCams[key].id)
                }
              }
            }
            let filterArray = this.sessionids.filter(function(n){return n;});
            this.sessionids = filterArray;
            let newchildCams = []
            for (let key in this.childCams){
              let multipleCon = this.getDataConnection(this.childCams[key].id)
              if(!!multipleCon){
                newchildCams.push(this.childCams[key].id)
              }
            }
            for(let index in this.childCams){
              let multipleCon = this.getDataConnection(this.childCams[index].id)
              multipleCon.connection.send({key: 'sendChildCamsFromStaff',data: newchildCams})
            }
            let ids = this.sessionids
            this.$refs.peerConnect.sessionIDsetColor(ids)
            this.sendSetId(this.sessionids) // Passing the data to peerconnect
            this.updateColorPointer() // Update Color Pointer with the Session Id
            //  let newchildCams = []
            //  for (let key in this.childCams){
            //             let multipleCon = this.getDataConnection(this.childCams[key].id)
            //             if(!!multipleCon){
            //                 newchildCams.push(this.childCams[key].id)
            //             }
            //  }
            //  for(let index in this.childCams){
            //     let multipleCon = this.getDataConnection(this.childCams[index].id)
            //     multipleCon.connection.send({key: 'sendChildCamsFromStaff',data: newchildCams})
            //  }
            vm.childConnectionChecker()
          },1000)
          /** As of the moment, speech recognition is only supported in Chrome */
          if(/Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) {
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
            this.recognition = new SpeechRecognition()
            this.recognition.interimResults = true
            this.recognition.lang = 'ja'
            this.recognition.addEventListener('result',e => {
              const transcript = Array.from(e.results)
                .map(result=>result[0])
                .map(result=>result.transcript)
                .join('')
              const poopScript = transcript.replace(/poop|poo|shit|dump/gi, '💩')
              if (e.results[0].isFinal) {
                let Subtitlecontent = document.getElementById('subtitle-content')
                shouldScroll = Subtitlecontent.scrollTop + Subtitlecontent.clientHeight === Subtitlecontent
                this.pushSpeechToArray(poopScript)
                if(!shouldScroll){
                  Subtitlecontent.scrollTop = Subtitlecontent.scrollHeight;
                }
              }
            })
            this.recognition.addEventListener('end',this.recognition.start)
            this.recognition.start()
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Starting the Speech Recognition'}
          let functionName = 'Connect:speechRecognitionStart';
          staff.checkError(errorStats, functionName);
        }
      },
      pushSpeechToArray(poopScript){
        try {
          let con = this.getMeetingDataConnection()
          if(this.command){
            this.subtitle.push('<strong class="custom-staff">'+this.auth.user.profile.last_name +'</strong> : '+poopScript)

            if (!!con){
              con.connection.send({key:'sendAudioTranscription', data: '<strong class="custom-staff">'+this.auth.user.profile.last_name +'</strong> : '+poopScript})
            }
            for (let key in this.childCams){
              let multipleCon = this.getDataConnection(this.childCams[key].id)
              if(!!multipleCon){
                multipleCon.connection.send( {key: 'sendAudioTranscription', data: '<strong class="custom-staff">'+this.auth.user.profile.last_name +'</strong> : '+poopScript})
              }
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Displaying the speech to text'}
          let functionName = 'Connect:pushSpeechToArray';
          staff.checkError(errorStats, functionName);
        }
      },
      passSubtitleToChild(subtitles){// Passing subtitle From 1st to 3rd child recursive way
        try {
          for (let key in this.sessionids){
            if (subtitles[1] == this.sessionids[key]){
              let con = this.getMeetingDataConnection()
              if (!!con){
                con.connection.send({key:'sendAudioTranscription', data: '<strong class="'+this.subtitleColors[key]+'">User'+key+'</strong> : '+subtitles[0]})
              }
              for (let index in this.childCams){
                let multipleCon = this.getDataConnection(this.childCams[index].id)
                if(!!multipleCon){
                  multipleCon.connection.send( {key: 'sendAudioTranscription', data: '<strong class="'+this.subtitleColors[key]+'">User'+key+'</strong> : '+subtitles[0]} )
                }
              }
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Passing the subtitle to Child Cameras'}
          let functionName = 'Connect:passSubtitleToChild';
          staff.checkError(errorStats, functionName);
        }
      },
      shareClientDocRedirect(data){
        try {
          let newClients = this.clients.filter((item) => item.id !== data.sender)
          if(newClients){
            newClients.forEach(function(elements){
              elements.conn.connection.send({key: 'clientShareDoc', data: {blob: data.blob, name: data.name, type: data.type}})
            })
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sharing the document'}
          let functionName = 'Connect:shareClientDocRedirect';
          staff.checkError(errorStats, functionName);
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

          // if (window.innerWidth <= 1010){
          //     this.toggleNoteShare();
          //     this.toggleSubtitle();
          //     this.toggleSalesShare();
          // }

          // if (window.innerWidth >= 1011){
          //     this.toggleSubtitle();
          //     this.toggleAnnotation();
          // }

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
          }else{
            if(window.innerWidth <= 767){
              //this.$refs.peerConnect.$els.peersVideoContainer.style.minHeight = window.innerHeight - 120 + 'px';
              !!document.querySelector('.webcam-switch') ? document.querySelector('.webcam-switch').style.bottom = '69px' : null;
              !!document.querySelector('.vid-min') ? document.querySelector('.vid-min').style.marginBottom = '-120px' : null;
            }
          }

          // if(window.innerWidth <=768){
          //   this.$refs.staffDashboardFooterNav.minwidth = 'width : 16%; ';

          //   if (window.innerWidth <= 322){
          //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 12.3%; ';
          //   }
          //   else if(window.innerWidth <=360){
          //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 12.5%; ';
          //   }
          //   else if(window.innerWidth <=375){
          //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 13%; ';
          //   }
          //   else if(window.innerWidth <=411){
          //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 12.7%; ';
          //   }
          //   else if (window.innerWidth <= 481){
          //     this.$refs.staffDashboardFooterNav.minwidth = 'width : 16%; ';
          //   }
          // }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Resize Handler'}
          let functionName = 'Connect:handleResize';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting of Array'}
          let functionName = 'Connect:sortArr';
          staff.checkError(errorStats, functionName);
        }
      },
      relayToOpenPage(data) {
        console.log("RELAY PREEEEE")
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'onLoadedPage', data:data})
        })
      },

      // m2b-81
      recordAccepted() {   
        try {             
          this.isConfirmRecording = true;
          this.showAskRecording = false;
          document.querySelector('.footer').style.zIndex = 9999;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Session Record Accepted'}
          let functionName = 'Connect:recordAccepted';
          staff.checkError(errorStats, functionName);
        }           
      },
      recordDeclined() {
        try {
          this.isConfirmRecording = false;
          this.showAskRecording = false;
          document.querySelector('.footer').style.zIndex = 9999;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Session Record Declined'}
          let functionName = 'Connect:recordDeclined';
          staff.checkError(errorStats, functionName);
        }           
      },
      // Start recordring on Staff
      staffScreenRecording() {  
        try {
          this.$refs.peerConnect.screenShareStaffRecord();

          let self = this;
          var retval = window.setInterval(function(){
            try {
              if(self.$refs.peerConnect.screen_recording_stream) {   
                clearInterval(retval);  
                
                var add_flag = false;
                self.screen_stream = self.$refs.peerConnect.screen_recording_stream; 

                self.$refs.peerConnect.peer.connections[self.primaryCam].forEach(function(stream) {
                  if(stream.remoteStream) {
                    self.audio_stream.push(stream.remoteStream);
                    add_flag = true;
                  }                        
                  if(stream.localStream) {
                    self.audio_stream.push(stream.localStream);
                    add_flag = true;
                  }
                });                                                          
                
                if(add_flag) {
                  self.processAudioScreen(self.primaryCam)                        
                }
                  
              }
            } catch(e) { /** do nothing */}
          }.bind(this), 1000);  
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Get Staff Screen Streams'}
          let functionName = 'Connect:staffScreenRecording';
          staff.checkError(errorStats, functionName);
        }           
      },   
      processAudioScreen(id) {
        try {
          // Instantiate audio recording
          if(this.multiStreamRecorder == null) {
            this.multiStreamRecorder = new MediaStreamRecorder.MultiStreamRecorder( this.audio_stream );
            this.multiStreamRecorder.mimeType = 'audio/webm';  
          } else {
            this.multiStreamRecorder.addStreams( this.audio_stream );
          }

          let self = this;

          // get audio blob after stopping audio recording
          this.multiStreamRecorder.ondataavailable = function(blob) {
            self.audio_blob = blob;
            self.multiStreamRecorder.clearRecordedData;            
            self.multiStreamRecorder = null;  
            self.audio_stream = []; 
            self.isHasAudioBlob = true;
          };

          // start screen and audio recording
          if( id == this.primaryCam ) {

            // Instantiate screen recording
            this.screenStreamRecorder = RecordRTC(this.screen_stream, { type: 'video/webm' });

            // Start recording screen
            this.screenStreamRecorder.startRecording();
            var d = new Date();
            console.log('Screen recording started: ', d);

            // release screen on stopRecording
            this.screenStreamRecorder.screen = this.screen_stream;                    

            // Start recording audio
            this.multiStreamRecorder.start(60 * 300 * 1000); // 5 hours recording
            var d = new Date();
            console.log('Audio recording started : ', d);

            // this will make sure that the recording indicator will show only when recording has started
            this.showRecordingMessage = true;
          }                    
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Get Staff Audio Streams and Start Recording both streams'}
          let functionName = 'Connect:processAudioScreen';
          staff.checkError(errorStats, functionName);
        }         
      },   
      
      // Staff Recording functions - START              
      getAudioScreenNames() {
        try {
          let data = new FormData();
          data.append('connect_id', this.primaryCam);
          staff.getAudioScreenNames(data).then((response) => {
            let self = this;
            setTimeout(function(){
              if (response) {
                self.screen_name = response.body.file_screen;                 
                self.audio_name = response.body.file_audio; 
                self.videos_name = response.body.file_videos;                                     
                self.file_link = response.body.file_link; 
              }
            },50)                      
          });
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Get Audio / Screen File Names'}
          let functionName = 'Connect:getAudioScreenNames';
          staff.checkError(errorStats, functionName);
        }
      }, 
    
      createScreenFiles() {
        try {
          var data = new FormData();
          data.append('connect_id', this.primaryCam);
          data.append('screen_name', this.screen_name);
          data.append('screen_blob', this.screen_blob);               

          staff.createScreenFiles(data);                
          this.doneCreateScreenFiles = true;    
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Create Screen File from Blob '}
          let functionName = 'Connect:createScreenFiles';
          staff.checkError(errorStats, functionName);
        }        
      },

      openNewDashBoard() {
        try {
          let newElement = document.createElement('a')
          newElement.setAttribute("id","openNewDashBoard")
          newElement.setAttribute('href',this.dashBoardLink)
          newElement.setAttribute('target','_blank');
          newElement.click()
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Open New Dashboard'}
          let functionName = 'Connect:openNewDashBoard';
          staff.checkError(errorStats, functionName);
        }        
      },    

      createAudioFiles(ablob) {
        try {
          var data = new FormData();
          data.append('connect_id', this.primaryCam);
          data.append('audio_name', this.audio_name);
          data.append('audio_blob', ablob);

          var date = new Date();
          let timeStart = date.getTime();
          let timeEnd = 0;
          let lastLoaded = 0;
          let loadedRate = 0;
          let firstPass = 0;

          Vue.http.post(
              `${staff_service.STAFF_CREATE_AUDIO_FILES}`,
              data, {
                  headers: {
                      'Content-Type': 'multipart/form-data'
                  },
                  progress: function( progressEvent ) {

                      // get progress value
                      let percent = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
                      this.progress = percent;

                      // create a span element to show progress percentage 
                      let percentProgress = this.progress + "%";
                      let el = document.getElementById('progressLinear');
                      let elChild = document.createElement('span');
                      let progressPercent = document.getElementById('progressPercent');
                      elChild.setAttribute('id', 'progressPercent');

                      // add / remove existing span element to refresh display of value on screen
                      if(progressPercent) {
                        progressPercent.parentNode.removeChild(progressPercent);
                        el.appendChild(elChild);
                      } else el.appendChild(elChild);
                      elChild.innerHTML = percentProgress;

                      // get time start and time end for time duration per loaded
                      if(timeEnd > 0) timeStart = timeEnd;
                      var date = new Date();
                      timeEnd = date.getTime();

                      // compute elapsedTime per loaded based on timeStart and timeEnd
                      var elapsedTime = (timeEnd - timeStart);

                      // get loadedRate and lastLoaded values to get loadRemain and timePerLoaded
                      var loaded = progressEvent.loaded;
                      if(firstPass == 1) {  
                        loadedRate = loaded - lastLoaded;
                        lastLoaded = loaded; 
                      } else {                      
                        loadedRate = loaded;
                        lastLoaded = loaded;
                      }
                      firstPass = 1;                    

                      // get the estimatedTotalTime - time left to process saving blob
                      var loadRemain = progressEvent.total - lastLoaded;                    
                      var timePerLoaded = Math.round( loadRemain / loadedRate )
                      var estimatedTotalTime = Math.round( timePerLoaded * elapsedTime )
                      var displayTime = ''               

                      // break estimatedTotalTime into hours, minutes, and seconds
                      var seconds = estimatedTotalTime / 1000;
                      var hours = parseInt( seconds / 3600 );
                      seconds = seconds % 3600;
                      var minutes = parseInt( seconds / 60 );
                      seconds = parseInt( seconds % 60 );

                      if(hours) { // display only hours
                        displayTime = hours + ' hours(s) left '                      
                      } else if(minutes) { // display only minutes
                        displayTime = minutes + ' minute(s) left '                      
                      } else ( // display only seconds
                        displayTime = seconds + ' second(s) left '                      
                      )

                      // passing of data to array variable for display
                      this.progressVariable.loadedRate = loadedRate
                      this.progressVariable.lastLoaded = lastLoaded
                      this.progressVariable.totalLoaded = progressEvent.total
                      this.progressVariable.displayTime = displayTime

                  }.bind(this)                      
              }
          ).then(response => {
              this.doneCreateAudioFiles = true;
              this.loading = false;                            
              this.main.loading = false;                          
              this.showProgressBar = false; 
              return response;
          });        
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Create Audio File from Blob '}
          let functionName = 'Connect:createAudioFiles';
          staff.checkError(errorStats, functionName);
        }  
      },      
      
      mergeAudioScreenFiles() {
        try {
          var data = new FormData();
          data.append('screen_name', this.screen_name);
          data.append('audio_name', this.audio_name); 
          data.append('videos_name', this.videos_name); 
          data.append('connect_id', this.primaryCam);           
          
          staff.mergeAudioScreenFiles(data)                
          .then(() => {
            this.negotation.video_report = this.file_link;                     
            this.updateNegotation();
            this.avCreateIsDone = true;             
          });
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Merge Audio and Screen Files to Mp4 video File'}
          let functionName = 'Connect:mergeAudioScreenFiles';
          staff.checkError(errorStats, functionName);
        }           
      },      

      createAudioScreenFiles(ablob){ 
        try {        
          if ( (ablob instanceof Blob) ) { 
            this.avCreateIsDone = false;  
            this.doneCreateAudioFiles = false;  
            this.doneCreateScreenFiles = false;   

            if(!this.$refs.peerConnect.isStopRecordingStaff) {
              this.main.loading = true;
              this.loading = true;
              this.showProgressBar = true; 
            }

            this.createScreenFiles();
            var retval = window.setInterval(function(){
              if(this.doneCreateScreenFiles) {
                clearInterval(retval);

                this.createAudioFiles(ablob);
                var retnew = window.setInterval(function(){
                  if(this.doneCreateAudioFiles) {
                    clearInterval(retnew);
                    this.mergeAudioScreenFiles();
                  }                     
                }.bind(this), 500);                   

              }

            }.bind(this), 500); 
          } else console.log('blob is not an instance of Blob: ', blob); 
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Create Audio and Files Main Function'}
          let functionName = 'Connect:createAudioScreenFiles';
          staff.checkError(errorStats, functionName);
        }            
      },  
        // m2b-81   
    },
    watch: {
      // memo : function(value){

      //   var tmpEl = document.createElement("div");
      //   tmpEl.innerHTML = value;

      //   if(this.loadOnce) {
      //     document.getElementById('businessNote').innerHTML = value;

      //     if((tmpEl.textContent !== '') || (tmpEl.innerText !== '')  ){
      //       document.getElementById('businessNote').classList.remove("empty");
      //     }
      //   }
      //   this.loadOnce = false;
      // },
      notesTopPosition: function(value){
                    this.meetingNotesTrue.top = value + "px !important";
            },
            notesLeftPosition: function(value){
                    this.meetingNotesTrue.left = value + "px !important";
            },
            salesTopPosition: function(value){
                    this.meetingSalesTrue.top = value + "px !important";
            },
            salesLeftPosition: function(value){
                    this.meetingSalesTrue.left = value + "px !important";
            },      meetingNotesWidth: function(value){
        if (value < window.innerWidth){
                    if (value > '281'){
                    this.meetingNotesTrue.width = value + "px !important";
                    }else if(value <= '281'){
                    this.meetingNotesTrue.width = "282px !important";
                    }
                 }
      },
      meetingNotesHeight: function(value){if (value > '101'){
        this.meetingNotesTrue.height = value + "px !important";
                    }else if (value <= '101'){
                    this.meetingNotesTrue.height = "102px !important";
                    }
      },
      meetingSalesWidth: function(value){
        if (value < window.innerWidth){
                    if (value > '281'){
                    this.meetingSalesTrue.width = value + "px !important";
      }else if (value <= '281'){
                    this.meetingSalesTrue.width = "282px !important";
                    }
                }
            },
      meetingSalesHeight: function(value){
        if (value > '101'){
                    this.meetingSalesTrue.height = value + "px !important";
                    }else if (value <= '101'){
                    this.meetingSalesTrue.height = "102px !important";
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
                    if (value > '400'){
                    this.noteSubtitle.width = value + "px !important";
                    }else if(value <= '350'){
                    this.noteSubtitle.width = "351px !important";
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

      subtitle: function(value){
        try {
          this.negotation.voice_report = this.subtitle
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to Subtitle Changes'}
          let functionName = 'Connect:watch:subtitle';
          staff.checkError(errorStats, functionName);
        }
      },
      primaryCam: function(value){
        try {
          let conn = this.getDataConnection(value)
          // Request for new Client Primary Cam
          if(value) {
            conn.connection.send({key: 'toggleRequest', data: true})

            // m2b-81
            // Added !this.$refs.peerConnect.isRecordingStarted checking to not pass again this process
            // when 1st client clicked the button exit
            if(this.isConfirmRecording && !this.$refs.peerConnect.isRecordingStarted) { 
              this.staffScreenRecording(); 
              var retnew = window.setInterval(function(){
                try {
                  if(this.screenStreamRecorder) {   
                    clearInterval(retnew);  
                    this.getAudioScreenNames();
                  }
                } catch(e) { /** do nothing */}
              }.bind(this), 1000);                      
            }
            // m2b-81

          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to Primary Cam Changes'}
          let functionName = 'Connect:primaryCam';
          staff.checkError(errorStats, functionName);
        }
      },
      childCams: function(value){
        try {
          if (value.length != 0) {
            id = value[0].id
            let conn =  this.getDataConnection(id)
            // Request for new Client Primary Cam
            if(value[0].open == false) {
              conn.connection.send({key: 'toggleRequestChild', data: true})
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to Child Cams Changes'}
          let functionName = 'Connect:childCams';
          staff.checkError(errorStats, functionName);
        }
      },
      connections: function(value){
        console.log('Listening to CONNECTIONS:::', value)
      },
      // showUrl: function(value) {
      //     this.$nextTick(() => {
      //         let res = value ? true : false;
      //         this.showUrl = res
      //         this.$refs.staffDashboardFooterNav.url = res
      //     });
      // }
    },
    events: {
      'peerconnect:peer:open'(vm, id) {
        try {

          // m2b-81
          staff.fetchTotalDiskUsage().then((response) => {
            if(response) {

              var allowAskRecording = true;
              var total_size = this.bytesToSize(response.body.total_size);
              var brk_size = total_size.split(' ');
              if(brk_size[1] == "GB") {
                if(parseInt(brk_size[0]) > 5) 
                  allowAskRecording = false;                  
              }

              if(allowAskRecording) {
                document.querySelector('.footer').style.zIndex = 0;
                this.showAskRecording = true;
                let askval = setInterval(() => {

                  if(!this.showAskRecording) {
                    clearInterval(askval)

                    this.connectMeeting()
                    this.staffID = id                  

                  }   
                }, 100)                    
              } else {
                this.connectMeeting()
                this.staffID = id                      
              }
              
            }
          });           
          // m2b-81
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to peer open'}
          let functionName = 'Connect::peerconnect:peer:open';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:peer:toggle:cam'(isActive){
        try {
          this.$refs.peerConnect.toggleOwnWebCam()
          let id = this.staffID
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'toggleClientWebcam', data: {data:id, status: isActive}})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Camera toggle'}
          let functionName = 'Connect::peerconnect:peer:toggle:cam';
          staff.checkError(errorStats, functionName);
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Alerting for no device'}
          let functionName = 'Connect::peerconnect:peer:nodevice';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:peer:pluginChecker'(isPlugin){
        try {
          this.isUsingTemasys = isPlugin;
          console.log('value of our isUsingTemasys: ', this.isUsingTemasys)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Peerconnect updating temasys plugin variable'}
          let functionName = 'Connect::peerconnect:peer:pluginChecker';
          helper.catchError(errorStats, functionName);
        }
      },
      'peerconnect:peer:conn:open'(vm, conn) {
        try {
          setTimeout(function() {
            this.$refs.peerConnect.showVideoCall = true
            // send the staff peer key to client
            let mykey = this.$refs.peerConnect.getStaffPeerKey()
            conn.send({key: 'getStaffPeerKey', data: mykey})
            this.staff_id = mykey

            if(this.isScreensharing.client != null){
              this.refreshCam()
            }
            this.isCamProcessing = true
            let isAdded = this.sessionids.findIndex(cont => cont == this.meeting_key.id)
            if(isAdded < 0 && !this.meeting_key.isRemoved){
              this.addClient(this.meeting_key.id)
            }
            if(!this.mainCam.isSet){
              this.primaryCam = this.meeting_key.id
            }

            this.setLink()
            // Passing the Share Link to Child
            let shareData = this.shareLink.text
            conn.send({key: 'shareLink', data: shareData})
            this.timerInterval = setInterval(() => {
              this.negotation.duration++
            }, 1000)
            // if (this.video_call_failed) {
            //     conn.send({key: 'forceCall', data: this.$refs.peerConnect.peer.id})
            //     this.video_call_failed = false
            // }
            if (this.main.loading) {
              this.main.loading = false;
              conn.send({key: 'initData', data: true})
              this.afterInit()
              staff.addNegotation(this, null, this.role)
            }
            this.isCamProcessing = false
          }.bind(this), 10);
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to connection open'}
          let functionName = 'Connect::peerconnect:peer:conn:open';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:peer:error'(vm, err) {
        try {
          if (err.type == 'peer-unavailable') {
            this.$refs.peerConnect.peer.disconnect()
            this.invalidMeetingKey = true
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to peer error'}
          let functionName = 'Connect::peerconnect:peer:error';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:peer:conn:close'(vm){
        try {
          // this.message = '他のユーザーからのコールが終了しました。'
          // this.endMeetingPopup()
          let arr = []
          if(!this.isEndedProperly){
            this.isCamProcessing = true
          }
          // if(!this.isScreensharing){
          //     this.clients.forEach(function(content){
          //         arr.push(content.id)
          //     })
          //     for(let x = 0; x < arr.length; x++){
          //         let sample = this.getVideoConn(arr[x])
          //         if(sample == undefined){
          //             this.removeClient(arr[x])
          //             this.isChildSlotFull = false
          //         }
          //     }
          //     this.isCamProcessing = false
          //     this.isEndedProperly = false
          // }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to peer close'}
          let functionName = 'Connect::peerconnect:peer:conn:close';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:primary:pointer'(value) {
        // let sessionColor = this.sessionPointercolor
        // let primaryColor = value
        // if(!!primaryColor && this.sessionColor.length == 0){
        //     this.sessionColor.push(primaryColor)
        // }
      },
      'docmodalshare:viewdoc'(vm, doc) {
        try {
          // console.log('viewdoc')
          this.documentData = doc
          this.$refs.imageGallery.refreshCtr()
          this.pages = []
          this.sortedPages = []
          this.sortedPages = this.sortArr(doc.pages,'page_id')
          // console.log(this.sortedPages)
          for (var key in this.sortedPages) {
            this.pages.push({
              id: key,
              path: this.sortedPages[key].image_url,
              annotation: this.sortedPages[key].annotation,
              canvasImageDataUrl: '',
              hasImage: false,
            })
          }
          console.log("VIEW DOC :: ",this.pages)
          this.docFromClient = true
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'viewDoc', data: {data:doc, permission: false}})
          })
          if (this.pages.length != 0) {
            this.show.file_view_modal = false
            this.$refs.staffDashboardFooterNav.docshare = this.pages.length ? true : false
            document.querySelector('.videoFeedCon').style.top = '2%';
            document.querySelector('.videoFeedCon').style.left = '1%';
          }
          let vm=this;
          vm.$nextTick( () => {
            this.annotation = this.pages[0].annotation;
          });
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to document share modal'}
          let functionName = 'Connect::docmodalshare:viewdoc';
          staff.checkError(errorStats, functionName);
        }
      },
      'docmodalshare:sharedoc'(vm, docLink, docSize) {
        try {
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'shareDoc', data: {data:docLink, size: docSize}})
          })
        }  catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to share document'}
          let functionName = 'Connect::docmodalshare:sharedoc';
          staff.checkError(errorStats, functionName);
        }
      },
      // 'staffdocshare:showmore'(vm) {
      //     this.i_docs_page_no++
      //     staff.getDocumentsByFolder(this, {page: this.i_docs_page_no, folder: 0}, this.getDocsCB)
      // },
      // 'staffdocshare:selected:end_meeting'(option) {
      //     this.redirectToDashboard()
      // },
      'keydown'(e) {
        this.relayNotes()
      },
      'changed'(e) {
        this.relayNotes()
      },
      'peerconnect:peer:conn:data'(vm, data, conn) {
        try {
          switch (data.key) {
            case 'initData':
              this.main.loading = false
              this.afterInit()

              if (this.$parent.auth.user.profile.first_name == null){
                var Parentfirstname = "" 
              }else{
                var Parentfirstname = this.$parent.auth.user.profile.first_name
              } 

              if (this.$parent.auth.user.profile.last_name == null){
                var Parentlastname = "" 
              }else{
                var Parentlastname =  this.$parent.auth.user.profile.last_name
              } 

              var getparentDetails = Parentfirstname + " " + Parentlastname
              var parentName = getparentDetails.toString();
              //Cookie.set('ParentNameHost', parentName)
              this.clients.forEach(function(elements){
                  elements.conn.connection.send({key: 'parentName', data:parentName})
              })
              Cookie.set('ParentNameHost', parentName)
              break
            case 'nextPage':
              this.$refs.imageGallery.nextPage(true)
              break
            case 'prevPage':
              this.$refs.imageGallery.prevPage(true)
              break
            case 'releasePaint':
              data = data.data
              // console.log(" RELEASE PAINT ",data.data)
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
              console.log("fromPageOnClick :::: ",data.data)
              this.$refs.imageGallery.usersConnectedforPage.push(data.data)
              let usersConn = this.$refs.imageGallery.usersConnectedforPage;
              let ids = this.sessionids.length;
              for (let i = 0; i < usersConn.length; i++) {
                if (!usersConn[i]) {
                  console.log("There's still a false")
                } else { console.log("ALL TRUE") }
              }
              console.log("usersConnectedforPage :::: ",this.$refs.imageGallery.usersConnectedforPage)
              break
            case 'onLoadedPage':
              console.log("onLoadedPage :::::: "+data.data)
              this.$refs.imageGallery.buttonShow = true;
              break
            case 'mouseMove':
              data = data.data
              this.mouseCoordinates = data.data
              this.childId = data.id
              let vm= this
              this.clients.forEach(function(elements){
                if (data.id == elements.id){
                  elements.clientpointer = data.data
                }
              })
              if(this.initialMousemove){
                this.mouseMove('')
                this.initialMousemove = false
              }
              if(this.mouseCoordinates.doctouch) {
                if(this.initialDocmousemove){
                  // this.$refs.imageGallery.callDocument()
                  // this.ownDocCoord.doctouch = true
                  // this.mouseMove('')
                  // this.initialDocmousemove = false
                }
              }
              this.updatePointer()
              break
            case 'notes':
              this.negotation.notes = data.data;
              break
            case 'childFile':
              data = data.data
              this.show.share_doc_modal = true;
              this.show.file_view_modal = false;
              this.showUrl = false;
              let newf = new File([data.blob],data.name, {type:data.type})
              this.childFile = newf
              this.clientFileSize = this.bytesToSize(parseInt(newf.size))
              this.shareClientDocRedirect(data)
              break
            case 'childFileView':
              data = data.data
              this.pages = []
              this.pages = data.data
              let pages = data.data
              this.clients.forEach(function(elements){
                elements.conn.connection.send({key: 'childFileView', data: pages })
              })
              this.docFromClient = data.fromClient
              this.$refs.imageGallery.refreshCtr()
              if (this.pages.length != 0) {
                this.show.file_view_modal = false
                this.$refs.staffDashboardFooterNav.docshare = this.pages.length ? true : false
                document.querySelector('.videoFeedCon').style.top = '2%';
                document.querySelector('.videoFeedCon').style.left = '1%';
              }
              break
            case 'secondClientConnect':
              data = data.data
              console.log('secondClienConnect::::', data.data)
              this.checkChildSlot(data.data)
              // let conn = this.getMeetingDataConnection()
              let conn = this.getDataConnection(data.data)
              if(typeof conn != 'undefined' && !this.isChildSlotFull){
                this.isCamProcessing = true
                let id = data.data
                if(!this.meeting_key.isRemoved){
                  this.setMainCam(this.meeting_key.id)
                }
                this.validateClientKey(data.link, id)
                if(this.shareLink.validated){
                  this.addClient(data.data)
                  this.shareClients()
                  let isChildAdded = this.childCams.findIndex(cont => cont.id === id)
                  if(isChildAdded < 0){
                    this.setChildCam(id)
                    let conn = this.getDataConnection(id)
                    conn.connection.send({key: 'shareLink', data: this.shareLink.text})
                    conn.connection.send({key: 'sharePeerId', data: this.meeting_key.id})

                    if (this.$parent.auth.user.profile.first_name == null){
                      var Parentfirstname = "" 
                    }else{
                      var Parentfirstname = this.$parent.auth.user.profile.first_name
                    } 
                    if (this.$parent.auth.user.profile.last_name == null){
                      var Parentlastname = "" 
                    }else{
                      var Parentlastname =  this.$parent.auth.user.profile.last_name
                    } 
                    var getparentDetails = Parentfirstname + " " + Parentlastname
                    var parentName = getparentDetails.toString();
                    this.clients.forEach(function(elements){
                    elements.conn.connection.send({key: 'parentName', data:parentName})
                    })
                  }
                }
              }
              this.notesCheck() // relaying notes to clients
              this.isCamProcessing = false
              break
            // If child media connections are not yet added on connections then set again
            case 'childCamReconnect':
              this.setChildCam(data.data)
              break
            case 'otherClientNotes':
              this.negotation.notes = data.data
              this.relayChildNotes(data.id)
              break
            case 'endingChild':
              if(!!this.getDataConnection(data.data)) {
                console.log("END ----------------------------------")
                this.$refs.imageGallery.onCanvasPostRecieve = false;
                this.isEndedProperly = true
                this.message = '他のユーザーからのコールが終了しました。'
                this.endMeetingPopup()
                let id = data.data
                this.removeClient(id)
                this.$refs.peerConnect.endChildConnections(data.data);
                this.isCamProcessing = false
              }
              break
            // Not used anymore
            // case 'callBackChild':
            //     let sender = data.childkey
            //     let senderclient = data.clientID
            //     let child = []
            //     child.push(senderclient)
            //     let connect = this.getDataConnection(sender)
            //     connect.connection.send({key: 'allChildCams', childIDS: child})
            //     break
            case 'sendShareLinktoClient':
              let con = this.getDataConnection(data.data)
              let shareData = this.shareLink.text
              if(con) con.connection.send({key: 'shareLinkToClient', data: shareData})
              break
            case 'screenShareStart':
              if(this.primaryCam == data.data && !this.screenLoaded){
                this.main.loading = true
                this.mainCam.isSet = false
              }
              break
            // After screenshare ends, reconnect webcams
            case 'screenshareReconnect':
              this.screenLoaded  = false
              if(this.primaryCam == data.data){
                this.mainCam.isSet = false
              }
              this.isScreensharing.client = null
              this.$refs.peerConnect.reconnectVideoConnection(data.data);
              // this.refreshCam()
              break
            // After screenshare ends, client waits for vidcon to be added on connection
            case 'clientRequestSreenshareReconnect':
              let client_id = data.data
              let conne = this.getDataConnection(client_id)
              conne.connection.send({key: 'forcescreenshareReconnect', data: this.staffID})
              break
            // When vidcon is not yet added, this will continously called
            case 'forcescreenshareReconnect':
              this.isScreensharing.client = data.data
              this.refreshCam()
              break
            case 'toggleClientWebcam':
              data = data.data
              let camStatus = 'showCamera' + data.data;
              Cookie.set(camStatus, data.status)
              if(data.data == this.primaryCam){
                this.$refs.peerConnect.togglePeerWebcam(data.status)
              }else{
                for ( let key in this.childCams ){
                  if( data.data == this.childCams[key].id ){
                    let is_open = this.childCams[key].open
                    // this.childCams[key].open = (is_open) ? false : true
                    this.childCams[key].open = data.status
                  }
                }
              }
              break
            case 'clientPeerKeyRequest':
              break;
            case 'sendAudioTranscription': // Get audio transcription From child
              let Subtitlecontent = document.getElementById('subtitle-content')
              shouldScroll = Subtitlecontent.scrollTop + Subtitlecontent.clientHeight === Subtitlecontent
              for (let key in this.sessionids){
                if (data.data[1] == this.sessionids[key]){
                  if(this.isUsingTemasys) {
                    Subtitlecontent.innerHTML += '<p><span><strong class="'+ this.subtitleColors[key] + '">User' + key + '</strong> : ' + data.data[0] + '</span></p>';
                  } else {
                    this.subtitle.push('<strong class="'+this.subtitleColors[key]+'">User'+key+'</strong> : '+data.data[0])
                  }
                }
              }
              this.passSubtitleToChild(data.data)
              if(!shouldScroll){
                Subtitlecontent.scrollTop = Subtitlecontent.scrollHeight;
              }
              break
            case 'connectedChild':
              if(this.connectedClients.indexOf(data.data) < 0){
                this.connectedClients.push(data.data)
              }
            // Remove the clients didn't respond after 10 seconds
            // let vms = this
            // if(!this.checkClientFlag){
            //     this.checkClientFlag = true
            //     setTimeout(function(){
            //         vms.checkClientAvailability()
            //         vms.checkClientFlag = false
            //     },20000)
            // }
            default:
              if(!!data.key) {
                this[data.key] = data.data
              }
              break
          }
        }  catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to the data being passed : ' + data.key}
          let functionName = 'Connect::peerconnect:peer:conn:data';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:loaded'(id) {
        try {
          if(document.querySelector('.forceOnTop')){
            var forceOnTops = document.querySelectorAll('.forceOnTop');
            for(var i = 0; i < forceOnTops.length; i++)
            {
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
          this.$refs.imageGallery.hideFullscreenDoc()
          this.$refs.imageGallery.showMinDoc()
          if (typeof this.pages[id] != 'undefined') {
            this.annotation = this.pages[id].annotation
          }
        }  catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to the Image Gallery being loaded'}
          let functionName = 'Connect::ui-image-gallery:loaded';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:Maximized'() {
        try {
          this.isMaximized = true
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'staffDocToolbar', data:true})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to image gallery being maximized'}
          let functionName = 'Connect::ui-image-gallery:Maximized';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:Minimized'() {
        try {
          this.isMaximized = false
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'staffDocToolbar', data:false})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to image gallery being minimized'}
          let functionName = 'Connect::ui-image-gallery:Minimized';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:hideBoxesduringMaximized'(value,boxImageStatus) {
        try {
          let meetingNotes = document.querySelector('.meetingNotes')
          let meetingSales = document.querySelector('.meetingSales')
          let subtitle     = document.querySelector('.subtitle')
          if (value) {
            // if (meetingNotes.classList != null) {
            //     let hideNotes = this.toggleHide(meetingNotes.classList)
            //     if (hideNotes) {
            //         this.toggleNoteShare()
            //     }
            //     if (boxImageStatus) {
            //         this.toggleNoteShare()
            //     }
            // }
            // if (meetingSales.classList != null) {
            //     let hideSales = this.toggleHide(meetingSales.classList)
            //     if (hideSales ) {
            //         this.toggleSalesShare()
            //     }
            //     if (boxImageStatus) {
            //         this.toggleSalesShare()
            //     }
            // }
            // if (subtitle.classList != null) {
            //     this.toggleHide()
            // }
          } else {
            if (meetingNotes.classList != null) {
              let showNotes = this.toggleHide(meetingNotes.classList)
              if (!showNotes) {
                this.toggleNoteShare()
              }
            }
            if (meetingSales.classList != null) {
              let showSales = this.toggleHide(meetingSales.classList)
              if (!showSales) {
                this.toggleSalesShare()
              }
            }
          }
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to hiding the boxes during maximized'}
          let functionName = 'Connect::ui-image-gallery:hideBoxesduringMaximized';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:BoxElement' (data){
        try {
          this.boxEls = data;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to the Image Gallery box element'}
          let functionName = 'Connect::ui-image-gallery:BoxElement';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:docCoord' (data,value){
        try {
          let ownCoordfromImageGallery = data
          this.ownDocCoord = ownCoordfromImageGallery
          document.addEventListener('mousemove', this.mouseMove)
          // this.initialDocmousemove = value
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Image Gallery document coordinates'}
          let functionName = 'Connect::ui-image-gallery:docCoord';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:getCoords' (data){
        try {
          this.ownDocCoord.onScreenHeight = data.height
          this.ownDocCoord.onScreenWidth = data.width
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Image gallery GET Coordinates'}
          let functionName = 'Connect::ui-image-gallery:getCoords';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:release-paint' (data, canvasUpdated,fromBtnPage){
        // console.log("RELEASE : ")
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'releasePaint', data: {data:data, canvasUpdated: canvasUpdated, fromBtnPage:fromBtnPage}})
        })
      },
      'ui-image-gallery:next-page'(id,canvasUpdated) {
        try {
          this.annotation = this.pages[id].annotation
          //let conn = this.getMeetingDataConnection()
          //conn.connection.send({key: 'nextPage', data: true})
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'nextPage', data:true})
          })
          console.log(" canvasUpdated : ", canvasUpdated," || id :: ",id)
          if (canvasUpdated) {
            this.$refs.imageGallery.updatePage(true)
          } else {
            console.log("\n\tNOTHING TO UPDATE")
            // this.$refs.imageGallery.updateClearPage(true)
          }
        } catch(e) {
          console.log("NEXT PAGE :: ",e)
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Image Gallery Next Page'}
          let functionName = 'Connect::ui-image-gallery:next-page';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:prev-page'(id,canvasUpdated) {
        try {
          this.annotation = this.pages[id].annotation
          //let conn = this.getMeetingDataConnection()
          //conn.connection.send({key: 'prevPage', data: true})
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'prevPage', data:true})
          })
          console.log(" canvasUpdated : ", canvasUpdated)
          if (canvasUpdated) {
            this.$refs.imageGallery.updatePage(true)
          } else {
            console.log("\n\tNOTHING TO UPDATE")
            // this.$refs.imageGallery.updateClearPage(true)
          }
        } catch(e) {
          console.log("PREV PAGE :: ",e)
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Image Gallery Previous Page'}
          let functionName = 'Connect::ui-image-gallery:prev-page';
          staff.checkError(errorStats, functionName);
        }
      },
      'ui-image-gallery:oncanvas-post'(status) {
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'oncanvasPost', data:status})
        })
      },
      'ui-image-gallery:relayToOtherPage'(status) {
        this.clients.forEach(function(elements){
          elements.conn.connection.send({key: 'fromPageOnClick', data:status})
        })
      },
      'ui-image-gallery:to-waiting-page'(status,id) {
        // this.clients.forEach(function(elements){
        //     elements.conn.connection.send({key: 'onWaitingPage', data:status, id:id})
        // })
      },
      'ui-image-gallery:closeDoc'(status,canvasUpdated) {
        if (status) {
          this.$refs.staffDashboardFooterNav.docshare = false
          document.querySelector('.videoFeedCon').style.top = '15%';
          document.querySelector('.videoFeedCon').style.left = '33%';
          this.initialDocmousemove = true
        }
        console.log(" canvasUpdated : ", canvasUpdated)
        if (canvasUpdated) {
          this.$refs.imageGallery.updatePage(true)
        } else {
          console.log("\n\tNOTHING TO UPDATE")
          this.$refs.imageGallery.updateClearPage(true)
        }
      },
      'resized'(el) {
        try {
          let isNotes = helper.hasClass(el, 'meetingNotes')
          let isSales = helper.hasClass(el, 'meetingSales')
          let isMinutes = helper.hasClass(el, 'subtitle')
          if (isNotes) {
            this.noteShare.disabled = true
          }
          if(isSales){
            this.salesShare.disabled = true
          }
        if (isMinutes) {
                        this.noteSubtitle.disabled = true
                    }} catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to resizing'}
          let functionName = 'Connect::resized';
          staff.checkError(errorStats, functionName);
        }
      },
      'stop-resize'(el) {
        try {
          let isNotes = helper.hasClass(el, 'meetingNotes')
          let isSales = helper.hasClass(el, 'meetingSales')
          let isMinutes = helper.hasClass(el, 'subtitle')
          if (isNotes) {
            this.noteShare.disabled = false
          }
          if(isSales){
            this.salesShare.disabled = false
          }
        if (isMinutes) {
                        this.noteSubtitle.disabled = false
                    }} catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Stop Resizing'}
          let functionName = 'Connect:stop-resize';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:audio:mute'(vm, audio_mute) {
        // commented cause of audio bug for client
        // let conn = this.getMeetingDataConnection()
        // conn.connection.send({key: 'audioMute', data: audio_mute})
      },
      'peerconnect:peer:screen:loaded' (vm) {
        try {
          this.screenLoaded = true;
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to peer screen being loaded'}
          let functionName = 'Connect::peerconnect:peer:screen:loaded';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:screenshare:error'(vm, error) {
        try {
          this.redirectToDashboard()
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to screen share error'}
          let functionName = 'Connect::peerconnect:screenshare:error';
          staff.checkError(errorStats, functionName);
        }
      },
      'peerconnect:screenshare:start'(){
        try {
          this.isScreensharing.started = true
          this.isScreensharing.active = true
          this.mainCam.isSet = false
          this.$refs.staffDashboardFooterNav.screenshare = true
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'screenShareStart'})
          })
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to screenshare being started'}
          let functionName = 'Connect::peerconnect:screenshare:start';
          staff.checkError(errorStats, functionName);
        }
      },
      // m2b-81      
      'peerconnect:staffRecording:end'(id){
        try {

          if(this.$refs.peerConnect.isRecordingStarted && !this.$refs.peerConnect.isScreensharedStaff) {
            if(!this.$refs.peerConnect.isStopRecordingStaff) {
              this.$broadcast('ui-snackbar::create',{
                message: 'レコーディング機能を停止しました。会話はそのまま続行できます。',
                duration: 6000
              })                         
              this.showRecordingMessage = false;
              this.$refs.peerConnect.isStopRecordingStaff = true;

              this.isConfirmRecording = false;
              this.$refs.peerConnect.isRecordingStarted = false;

              this.screenStreamRecorder.stopRecording(this.stopRecordingCallback); 
              var retval = window.setInterval(function(){
                if(this.gotScreenBlob) {
                  clearInterval(retval); 
                  this.multiStreamRecorder.stop();
                  var hasBlob = window.setInterval(function(){
                    if(this.isHasAudioBlob) {
                      clearInterval(hasBlob); 
                      this.createAudioScreenFiles(this.audio_blob); 
                    }
                  }.bind(this), 500); 

                }				                                    
              }.bind(this), 500); 

            }
          } else if(id == 'nostream') {
            this.isConfirmRecording = false;
          }
           
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Staff Screen share recording being ended'}
          let functionName = 'Connect::peerconnect:staffRecording:end';
          staff.checkError(errorStats, functionName);
        }              
      },
      // m2b-81       
      'peerconnect:screenshare:end'(id){
        try {

          this.isScreensharing.client = null
          // let staffid = this.staffID
          this.clients.forEach(function(elements){
            elements.conn.connection.send({key: 'screenshareReconnect', data: this.staffID})
          })
          this.isScreensharing.active = false
          this.$refs.staffDashboardFooterNav.screenshare = false
          // this.refreshCam()
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listens to Screen shared being ended'}
          let functionName = 'Connect::peerconnect:screenshare:end';
          staff.checkError(errorStats, functionName);
        }
      }
    },
    created(){
      try {
        
        new ClipboardJS('.share_link_btn')
        new ClipboardJS('.sharelinkButton')
        // document.addEventListener('beforeunload', this.onbeforeunload)
        document.addEventListener('mousemove', this.mouseMove)
                /** Set Cookie for the document security */
                Cookie.set('peerId', '')
            } catch(e) {
                    let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Component Created'}
                    let functionName = 'Connect:created';
                    staff.checkError(errorStats, functionName);
                }
          if (browser.detectIE().trigger) {
            this.errorCompatibility = true;
          }
        },
        ready(){
          // this.initTinyMCE();
            let config = {
                role: 'staff',
                showToolbar: false
            };
            if(!!this.auth.user.role) {
                this.role = this.auth.user.role
                config.role = this.role
                window.onbeforeunload = function(event) { this.onbeforeunload(event) }
                // window.addEventListener('beforeunload', (event) => { this.onbeforeunload(event) }, false);
                window.addEventListener('unload', (event) => { this.onbeforeunload(event) }, false);
                staff.getDocuments(this, {page: this.i_docs_page_no}, this.getDocsCB, this.role)
                // staff.getDocumentsByFolder(this, {page: this.i_docs_page_no, folder: 0}, this.getDocsCB)
                staff.getMemo(this);
                this.roleLoaded = true
            } else {
              console.log('No User')
              this.$watch('auth.user.role', function() {
                this.role = this.auth.user.role
                config.role = this.role
                window.onbeforeunload = function(event) { this.onbeforeunload(event) }
                window.addEventListener('beforeunload', (event) => { this.onbeforeunload(event) }, false);
                window.addEventListener('unload', (event) => { this.onbeforeunload(event) }, false);
                staff.getDocuments(this, {page: this.i_docs_page_no}, this.getDocsCB, this.role)
                // staff.getDocumentsByFolder(this, {page: this.i_docs_page_no, folder: 0}, this.getDocsCB)
                staff.getMemo(this);
                this.roleLoaded = true

              }, true)
            }
            window.addEventListener('resize', this.handleResize)

            // m2b-81 - this will ask the user to confirm "leave page" or "cancel" if clicked the windows "x" button
            window.addEventListener('beforeunload', (event) => { this.pageXbuttonClicked(event) }, false);
            // m2b-81

      if (window.innerWidth <= 1010){
        this.toggleNoteShare();
        this.toggleSubtitle();
        this.toggleSalesShare();
      }
      // if(window.innerWidth >= 960 && window.innerWidth <= 1100  ){
      //     document.getElementById('
      // }
      if (window.innerWidth >= 1011){
        this.toggleSubtitle();
        this.toggleAnnotation();
      }
      if(window.innerWidth <= 767){
        document.querySelector('.webcam-switch').style.bottom = 0;
        document.querySelector('.vid-min').style.marginBottom = '-50px';
        if(navigator.userAgent.indexOf('Android') > 0){
          document.querySelector('.vid-min').style.marginBottom = null;
        }
      }
      if(window.innerWidth<=375){
        this.$refs.staffDashboardFooterNav.minwidth = 'width : 14%;';
      }
            let vm =this
            setInterval(function(){
                window.addEventListener('resize', vm.handleResize)
            },1000)
            document.addEventListener('visibilitychange', function() {
                if( /iPhone|iPad/i.test(navigator.userAgent) && !document.hidden) {
                   vm.$refs.peerConnect.toggleTracks();
                }
            })
            // window.onbeforeunload = this.onbeforeunload
            this.$dispatch('component-ready', config)
      // ON DEV CONSOLE COMMENT THIS IF IN DEVELOPMENT
      window.onerror = function(message, url, lineNumber) {
        // code to execute on an error
        return true; // prevents browser error messages
      };
      console.log = function(){};
      // END DEV CONSOLE
      // console.log(this)

      try{
        this.speechRecognitionStart()
      }catch(e){
        this.errorCompatibility = true;
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Speech Recognition'}
        let functionName = 'Speech Recognition and Webrtc';
        staff.checkError(errorStats,functionName);
        // // this.errorCompatibility = true;
        // helper.catchError(errorStats, functionName) ? this.errorCompatibility = true : this.errorCompatibility = false;
      }

    },
    directives: {
      draggable: DraggableDirective,
      onclickfront: OnclickToFrontDirective
    },
    components: {
      PeerConnect,
      StaffDocShare,
      UiImageGallery,
      UiResizeable,
      UiCloseButton,
      StaffDashboardFooterNav,
      UiModal,
      UiButton,
      UiTab,
      UiTabs,
      UiSnackbar,
      UiAlert,
      DocViewModal
    },
    beforeDestroy() {
      this.endMeetingFn()
    }
  }
</script>
<style lang="scss">
  @media only screen
  and (min-device-width: 375px)
  and (max-device-width: 768px)
  and (-webkit-min-device-pixel-ratio: 2)
  and (orientation: landscape) {
    .proto .peerCon .protoConnectContainer
    .wrapper .row div[class*="col-"].fileViewer {
      margin-bottom: 65px !important;
    }
  }
  @media only screen
  and (min-device-width: 769px)
  and (max-device-width: 812px)
  and (-webkit-min-device-pixel-ratio: 2)
  and (orientation: landscape) {
    .proto .peerCon .protoConnectContainer
    .wrapper .row div[class*="col-"].fileViewer {
      margin-bottom: 70px !important;
    }
  }
  //     @media only screen
  //         and (min-device-width: 980px)
  //         and (max-device-width: 1024px)
  //         and (-webkit-min-device-pixel-ratio: 2)
  //         and (orientation: landscape) {
  //         .proto .peerCon .protoConnectContainer
  //             .wrapper .row div[class*="col-"].fileViewer {
  //                 margin-bottom: 80px !important;
  //         }
  //    }
  @media only screen
  and (min-device-width: 619px)
  and (max-device-width: 768px)
  and (-webkit-min-device-pixel-ratio: 2)
  and (orientation: portrait) {
    .proto .peerCon .protoConnectContainer
    .wrapper .row div[class*="col-"].fileViewer {
      margin-bottom: 68px !important;
    }
  }
  @media only screen
  and (min-width: 1024px)
  and (max-width: 1024px)
  and (-webkit-min-device-pixel-ratio: 2)
  and (orientation : landscape) {
    .proto .peerCon .protoConnectContainer
    .wrapper .row div[class*="col-"].fileViewer {
      margin-bottom: 100px !important;
    }
  }
  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .peer-connect .controlVid {
    padding: 5px;
    position: relative!important;
    z-index: 300;
    left: 10px;
    bottom: 100px!important;
  }

  .custom-staff{
    font-size:14px;
    color: #544a4a;
    font-weight: 600;
  }
  .custom-child1{
    font-size:14px;
    color: #333333;
    font-weight: 600;
  }
  .custom-child2{
    font-size:14px;
    color: #6dbf5f;
    font-weight: 600;
  }
  .custom-child3{
    font-size:14px;
    color: #cc6969;
    font-weight: 600;
  }


  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) {

    .loader-container{
      position: absolute;
      width: 100%;
      padding-left: 50%;
      height: 100%;
      background-color: #333333;
      top: 0;
      z-index: 20;
    }
    .lds-ripple {
      display: inline-block;
      position: absolute;
      padding: 20px;
      width: 50px;
      height: 50px;
      z-index: 50;
      top: 45%;
      left: 45%;
    }
    .lds-ripple div {
      position: absolute;
      border: 4px solid #fff;
      opacity: 1;
      border-radius: 50%;
      animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
    }
    .lds-ripple div:nth-child(2) {
      animation-delay: -0.5s;
    }
    @keyframes lds-ripple {
      0% {
        top: 28px;
        left: 28px;
        width: 0;
        height: 0;
        opacity: 1;
      }
      100% {
        top: -1px;
        left: -1px;
        width: 58px;
        height: 58px;
        opacity: 0;
      }
    }


    .zoom_out_screen {
      top: -15px;
      width: 35px !important;
      height: 35px !important;
      i {
        position: absolute;
        // top: 7px;
        // left: 16px;
        top: 8px;
        left: 4px;
        background: url(../image/zoom-icon.png) no-repeat scroll 5px 0 transparent;
        // background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
        background-size: 40%;
        // height: 43px;
        // width: 43px;
        display: block;
        // background-position-x: -529px;
      }
      span {
        font-family: 'NotoSans-Medium';
        color: #333333;
        bottom: 10px;
        position: absolute;
      }
    }
    .icon-color{
      background:#333333;
    }

    // .annotation {
    //     // left: 20px;
    // }


    .annotation,
    .meetingNotes,
    .subtitle {
      .ui-close-button {
        position: absolute;
        right: -10px;
        top: -10px;
        .ui-fab-mini {
          width: 30px;
          height: 30px;
          i {
            font-size: 22px;
            position: absolute;
          }
          span {
            font-family: 'NotoSans-Medium';
            bottom: 10px;
            position: absolute;
            font-size: 12px;
          }
        }
      }
    }

    .meetingNotes {
      width: 330px;
      height: 35%;
      bottom: 220px;

      .meetingNotesWrapper {
        height: 100%;
        .ui-resizeable {
          .ui-fab-mini {
            background-color: #333333;
            height: 25px;
            border-radius: 0;
            box-shadow: none;
            width: 25px;
            position: relative;
            bottom: -3px;
            right: 0;
          }
          i {
            background: url(../image/resize-button.png) no-repeat scroll 5px 0 transparent;
            width: 24px;
            height: 19px;
            position: absolute;
            right: 0;
            bottom: 0;
            font-size: 0;
          }
        }
        .ui-resizeable.adjusted{
          .ui-fab-mini {
            bottom: -2px;
          }
        }
      }

      .meeting-note-textarea {
        height: 100%;
        .ui-textbox-textarea {
          // width:68%;
          width:100%; // change
          height:100%;
          box-sizing: border-box;         /* For IE and modern versions of Chrome */
          -moz-box-sizing: border-box;    /* For Firefox                          */
          -webkit-box-sizing: border-box; /* For Safari                           */
          border-bottom: none;
          background: #ffffff;
        }
      }
      .meeting-note-head {
        color:#333333;
        font-family: 'NotoSans-Bold';
        height: 25px;
        border-bottom: 1px solid #333333;
        margin: 15px;
        display: flex;
        width: 90%;
      }

      .meeting-note-footer {
        bottom: -105px;
        position: absolute;
        background: #333333;
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
          color: #333333;
          background-position: center;
          transition: background 0.8s;
          padding:0!important;

        }

        .ui-button-content{
          padding:10px 16px;
        }

        .ui-button-content:hover{
          filter: brightness(0) invert(1);
        }

        .ui-button:hover{
          background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;

        }
        .ui-button:active {
          background-color: #507b9d;
          background-size: 100%;
          transition: background 0s;
        }
      }
    }

    .meetingSales{
      background: #fff;
      width: 330px;
      height: 38%;
      bottom:220px;
      z-index: 42;
      flex-grow: 1;
      position: absolute;
      right:30px;
      // left: unset !important;
      display: block;
      box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12);
    }
    .meetingSales.hide{
      display: none;
    }
    .meetingSalesWrapper{
      height:100%;
    }
    .meetingSales .ui-close-button{
      position: absolute;
      right: -10px;
      top: -10px;
    }
    .meetingSales .ui-close-button .ui-fab-mini {
      width: 30px;
      height: 30px;
    }
    .meetingSales .ui-close-button .ui-fab-mini i{
      font-size: 22px;
      position: absolute;
    }
    .meetingSales .ui-close-button .ui-fab-mini span{
      font-family: 'NotoSans-Medium';
      bottom: 10px;
      position: absolute;
      font-size: 12px;
    }
    .meetingSales .meeting-sales-head{
      color: #333333;
      font-family: 'NotoSans-Bold';
      height: 25px;
      border-bottom: 1px solid #333333;
      margin: 15px;
      display: -ms-flexbox;
      display: flex;
      width: 90%;
    }
    .meetingSales .meeting-sales-textarea{
      height:100%;
      margin-bottom:0px;
    }
    .meetingSales .ui-textbox .ui-textbox-content{
      height:100%;
    }
    .meetingSales .ui-textbox .ui-textbox-content .ui-textbox-label-text{
      margin-bottom: 0px;
    }
    .meetingSales .ui-textbox .ui-textbox-content .ui-textbox-textarea{
      height:100%;
      width:100%;
      padding: 20px;
      border-bottom: none;
      background: #ffffff;
      font-size:14px;
      resize:none;
    }
    .meetingSales .meeting-sales-footer{
      bottom: -105px;
      position: absolute;
      background: #333333;
      width: 100%;
      height: 50px;
    }
    .meetingSales .meeting-sales-footer p{
      color: #fff;
      padding: 5px 15px;
      font-weight: 900;
      letter-spacing: 2px;
      font-size: 14px;
    }

    .meetingSales .meetingSalesWrapper .ui-resizeable {
      position: absolute;
      /* bottom: -78px; */
      right: 0;
    }
    .meetingSales .meetingSalesWrapper .ui-resizeable .ui-fab-mini {
      bottom: -2px;
      background-color: #333333;
      height: 25px;
      border-radius: 0;
      box-shadow: none;
      width: 25px;
      position: relative;
      right: 0;
    }
    .meetingSales .meetingSalesWrapper .ui-resizeable i{
      background: url(../image/resize-button.png) no-repeat scroll 5px 0 transparent;
      width: 24px;
      height: 19px;
      position: absolute;
      right: 0;
      bottom: 0;
      font-size: 0;
    }
  }
  .ui-modal-mask{
    height:100vh;
  }
  .share_link {
    // position: fixed;
    // min-width: 200px;
    z-index: 5;
    // bottom: 7.5rem;
    left: 1.7rem;
    .share_link_btn,.ui-textbox{
      display: inline-block;
    }

    .share_link_btn{
      // background-color: #333333;
      // color: #ffff;
      // padding: 5px 2px 10px 2px;
      // margin-top: -66px;
      // -moz-margin-top: 5px;
      // position: absolute;
      // right: -40px;
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
  .annotation {
    bottom: 200px !important;
  }
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

  }  //end

  @media (min-width: 768px){
    .share_link {
      position: fixed;
      top: unset;
      min-width: 200px;
      z-index: 5;
      bottom: 7.5rem;
      left: 1.7rem;
      .share_link_btn,.ui-textbox{
        display: inline-block;
      }

      .share_link_btn{
        background-color: #333333;
        color: #ffff;
        padding: 5px 2px 10px 2px;
        margin-top: -66px;
        -moz-margin-top: 5px;
        position: absolute;
        right: -40px;
      }
    }
  }

  #docPage{
    display: block !important;
    width: 100% ;
    height: 100% !important;
  }
  .videoParent{
    .peer-connect{
      .video-connections-feed{
        .video-container{
          .ui-media{
            video {
              border: 2px solid rgb(59, 89, 152);
            }                    }
        }
      }
    }
  }

  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .subtitle-footer .ui-button-icon {
    background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent;
    background-position-x: -587px;
    background-position-y: -14px;
    font-size: 0;
    width: 20px;
    margin-right: 10px;
    height: 18px;
  }

  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .subtitle-footer .ui-button-content {
    padding:5px;

  }
  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .subtitle-footer .ui-button-content:hover {
    filter: brightness(0) invert(1);

  }

  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .subtitle-footer .ui-button-text {
    font-family: 'NotoSans-Bold';
  }


  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .ui-resizeable {
    position: absolute;
    bottom: -78px;
    right: 0;
  }
  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .ui-resizeable .ui-fab-mini {
    bottom: 28px;
    background-color: #333333;
    height: 25px;
    border-radius: 0;
    box-shadow: none;
    width: 5px;
    position: relative;
    right: 0;
  }
  .proto .peerCon .protoConnectContainer .wrapper .row:not(.middle-xs):not(.center-xs) .subtitle .ui-resizeable i{
    background: url(../image/resize-button.png) no-repeat scroll 5px 0 transparent;
    width: 24px;
    height: 19px;
    position: absolute;
    right: 0;
    bottom: 0;
    font-size: 0!important;
  }

  .compatibility-alert {
    // display: block !important;
    float: left;
    .ui-alert-close-button{
      color: #fff;
    }
    .ui-alert-body {
      background: #ef5c51;
      text-align: center;
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
        width: 1rem;
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
        color: #333333;
        font-family: 'NotoSans-Bold';
        height: 25px;
        border-bottom: 1px solid #333333;
        margin: 15px;
        display: -ms-flexbox;
        display: flex;
        width: 90%;
      }
      .subtitle-footer {
        bottom: -50px;
        position: absolute;
        background: #333333;
        width: 100%;
        height: 50px;

        .ui-button-text {
          font-family: 'NotoSans-Bold';
        }
        .ui-button-icon {
          background: url(../image/toolbar-sprite.png) no-repeat scroll 5px 0 transparent !important;
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
          color: #333333;
          font-size: 11px;
          width: 110px;
          position: absolute;
          transition: background 0.8s;
          padding:0!important;
          right: 20px;
          top: 5px;
        }

        .ui-button:hover{
          background: #4e76c8 radial-gradient(circle, transparent 2%, #4e76c8 2%) center/15000%;

        }
        .ui-button:active {
          background-color: #507b9d;
          background-size: 100%;
          transition: background 0s;
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
      color: #333333;
      width: 31px;
      height: 21px;
      font-size: 10px;
      padding: 0px;
    }
  }

  .recording-message {
    top: 1px;
    z-index: 100;
    font-weight: 900;
    text-align: center;
    background: #333333;
    position: fixed;
    left: 50%;
    height: 34px;
    text-align: center;
    width: auto;
    padding : 2px 20px;
    border:1px solid #4682b4;
    transform: translate(-50%, 0);

    .record-box{
      padding: 5px 0;
      position: relative;
      margin:0 auto;
    }    

    .record-light {      
      margin-right:10px;
      float:left;
      display: inline-block;
      background: #f00;
      width: 15px;
      height: 15px;
      border-radius: 100%;
      border: 2px solid #e4e6e9;
    }    

    .record-text {
      color: #fff;
      font-weight: 800;
      margin-right: 0 auto;
      padding-top: 5px;
      font-size: 11px;
      clear:both;
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

  // New Added CSS class
  .meeting-sales-content{
    background:#fff;
    height:240px;
    p{
      font-weight:700;
      font-size:20px;
    }
  }


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
    // For mobile
    .onTop {
      z-index:50 !important;
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

  #progressPercent {
    position: relative;
    font-weight: 800;
    color: #e3dddd;
    z-index: 100;
    text-align: -webkit-center;
    border-radius: 15px;
  }


// For IE Browser ONLY!
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
  .proto .peerCon .protoConnectContainer {
    // File Viewer setup
    .wrapper .row .fileViewer .container {
      position: absolute !important;
    }
  }
  // Close Button
  .ui-close-button .ui-fab i {
    position: inherit !important;
  }
}
.ui-button.new-dashboard-button {
  -webkit-border-radius: 20px;
  -moz-border-radius: 20px;
  border-radius: 20px;
  width: 80% !important;  
  margin-top: 20px;
  margin-left: 10%;
}
</style>

