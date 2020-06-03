<template>
  <div class="protoDashboard">
    <staff-sidebar v-ref:sidebar :api-url="notices_post.api_url"></staff-sidebar>
    
    <div class="dashboardContent" v-el:adj-container>
      <div class="wrapper">
        <div class="row">
          <div class="col-xs-12">
            <h1>
              <span>
                  商談履歴
              </span>
            </h1>
            
            <ui-nego-table
                :api-url="documents_table.api_url"
                :data-key="documents_table.data_key"
                :fields="documents_table.fields"
                :actions="documents_table.actions"
                :no-content-text="documents_table.no_content_text"
                :role="role"
            v-if="roleLoaded"></ui-nego-table>
          </div>
        </div>
        <ui-modal v-bind:id="videoId"
          :show.sync="show.videoModal" hide-footer hide-body :header="view_modal.header"
          :show-close-button="false"
          :backdrop-dismissible="false">
             <div class="ui-close-button">
                <button class="ui-fab ui-fab-mini color-primary" @click="buttonVideosModal">
                    <i class="ui-icon material-icons ui-fab-icon close">close</i>
                    <div class="ui-ripple-ink"></div>
                </button>
            </div>
            <div class="video-textboard">
              <div id="textbox-video-modal">
                <!-- m2b-81 -->
                <div>
                  <video class="videoClass" preload controls autoplay id="videos">
                      <source :src="view_modal.data.video_report_url">
                  </video>
                </div>
              </div>
            </div>

          <div class="url-button">
            <!-- m2b-81 -->
            <div>
                <ui-button color="success" class="sharelinkButton" @click="downloadVideo(view_modal)"> {{ view_modal.content }}</ui-button>
            </div>    
            <!-- m2b-81 -->                            
             
          </div>
          <div class="file-size">データサイズ ： {{view_modal.size}}</div><br>
          <div>
              <ui-button id="videoRemove" @click="removeVideoFile(modal_view_data.video_report,'YES')"> 削除 </ui-button>
          </div>            
        
        </ui-modal>
          <!-- m2b-81 -->
        <ui-modal                 
          :show.sync="show.viewModal" hide-footer hide-body :header="view_modal.header">
            <!-- m2b-81 -->
            <div class="textboard">
            <div id="textbox-modal">
              <div v-if="show.view">
                <p v-for="sub in view_modal.voicetext" v-bind:key="sub">
                  <span v-html="sub">{{sub}}</span>
                </p>
              </div>
              <div v-else id="notes-modal">
                <div id="notes-context" v-html="view_modal.context"></div>
              </div>
            </div>
            </div>
          
          <div class="url-button">
              <ui-button color="success" class="sharelinkButton" @click="download(view_modal)"> {{ view_modal.content }}</ui-button>
          </div>
          <div class="file-size">データサイズ ： {{view_modal.size}}</div>
                  
        </ui-modal>
        <slot name="modal-slot"></slot>
        <ui-modal                    
            :show.sync="showPleaseWait" hide-footer remove-header="true" header="" transition="ui-modal-fade" align-top="true"
            :show-close-button="false"            
            :backdrop-dismissible="false">
                <div id="loadingAnimation"></div>
                <p id="openWait" style="text-align:center;padding:0;margin:0;color:gray;font-size:14px;font-weight:600;">動画を開いています。しばらくお待ちください。</p>
        </ui-modal>  
        <!-- m2b-81 -->        
      </div>
    </div>
  </div>
</template>
<script>
  import StaffSidebar from './partials/StaffSidebar.vue';
  import UiNegoTable from '../common/UiNegoTable.vue';
  import {APP_URL} from '../../js/config.js';
  import auth from '../../js/auth.js';
  import helper from '../../js/helpers.js';
  import {COMPANY_USER_NEGOTATIONS_RESOURCE, STAFF_NEGOTATIONS_RESOURCE, STAFF_NOTICES_RESOURCE} from '../../js/api_routes.js';
  // m2b-81
  import staff from '../../js/staff.js';				
  // m2b-81
  export default {
    data() {
      return {
        auth: this.$parent.auth,
        APP_URL,
        static_images: {
          avatar: `${APP_URL}/image/avatar_flat.png`
        },
        notices_post: {
          api_url: STAFF_NOTICES_RESOURCE,
        },
        documents_table: {
          api_url: '',
          data_key: `negotations`,
          fields: [
            { name: 'created_at', label: '商談日', width: 25, callback: this.formatMeetingDate },
            // { name: 'notes', label: 'ノート', width: 40 },
            { name: 'notes', label: 'ノート', width: 25 },
            { name: 'voice_report', label: '議事録', width: 25 },
            // m2b-81
            { name: 'video_report', label: '動画', width: 25 },				  
            { name: 'video_size', label: '動画サイズ', width: 25, callback: this.formatVideoSize },
            // m2b-81              
            { name: 'duration', label: '商談時間', width: 25, callback: this.formatMeetingDuration }
          ],
          actions: [
            { name: 'view-item', label: '', icon: 'search', icon_color: 'primary' },
          ],
          no_content_text: `商談履歴がありません`
        },
        show: {
          viewModal: false,
          view: false,
          videoModal: false,
        },
        view_modal: {
          header: '',
          content: '',
          context: '',
          size: '',
          data: '',
          voicetext: ''
        },
        current_item: {},
        role: '',
        roleLoaded: false,
        modal_view_data: [],
        video_exist: false,
        showPleaseWait: false,	 
        videoId: 'videoId',		
        datDecrypted: false,
        passedDecrypt: false,
        loadAnimation: 0
      }
    },
    methods: {
      formatMeetingDate(value) {
        try {
          return helper.formatDate(value, 'YYYY/MM/DD')
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Formatting the meeting date entries'}
          let functionName = 'StaffNegotation:formatMeetingDate';
          helper.catchError(errorStats, functionName);
        }
      },
      formatVideoSize(value) {
        try {
          var formattedValue = '';
          if(value > 0) {
            formattedValue = this.bytesToSize(value);  
          }
          return formattedValue;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Returning the formatted video size'}
          let functionName = 'StaffNegotation:formatVideoSize';
          helper.catchError(errorStats, functionName);
        }
      },      
      formatMeetingDuration(value) {
        try {
          return helper.formatDuration(value)
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Returning the formatted meeting duration'}
          let functionName = 'StaffNegotation:formatMeetingDuration';
          helper.catchError(errorStats, functionName);
        }
      },
        download(data){
            try {
                let context = data.context
                let time = data.data.created_at.split(' ');
                if (context){
                    let newElement = document.createElement('a')
                    newElement.setAttribute("id","downloadedConversation")
                    newElement.setAttribute('href','data:text/plain;charset=utf-8,'+ encodeURIComponent(context))
                    newElement.setAttribute('download','Mee2box '+data.header+' / '+time[0]+'.txt')
                    newElement.style.display='none'
                    document.body.appendChild(newElement)
                    newElement.click()
                    document.body.removeChild(newElement)
                    this.show.viewModal = false
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Downloading the document'}
                let functionName = 'StaffNegotation:download';
                helper.catchError(errorStats, functionName);
            }
        },
      decodeVoiceText(context){
        try {
          let decodedVoice = ''
          for(let key in context){
            let convo = this.decodeEntities(context[key])
            console.log(convo)
            decodedVoice += convo+'\r\n'
          }
          return decodedVoice;
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Decoding voice to text negotation'}
          let functionName = 'StaffNegotation:decodeVoiceText';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Converting bytes to size'}
          let functionName = 'StaffNegotation:bytesToSize';
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
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Decoding Entities'}
          let functionName = 'StaffNegotation:decodeEntities';
          helper.catchError(errorStats, functionName);
        }
      },
      // m2b-81        
      removeVideoFile(url,opt) {
          try {
              this.show.videoModal = false
              var videoElement = document.getElementById('videos');                  
              if(videoElement) videoElement.pause();               
              let data = new FormData();
              data.append('url', url);
              data.append('opt', opt);
              staff.removeVideoFile(data, this.role).then((response) => {
                if(opt == "YES") {
                  var n_id = this.modal_view_data.negotation_id;
                  let fdata = new FormData();
                  fdata.append('_method', 'PUT');
                  fdata.append('video_report', '');
                  fdata.append('video_size', '');
                  staff.updateNegotation(self, {id: n_id}, fdata, this.role);
                  this.modal_view_data.video_report = '';
                  this.modal_view_data.video_size = '';
                  this.modal_view_data.video_report_url = '';
                }
              });   
          } catch(e) {
              let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Remove decrypted Video file'}
              let functionName = 'StaffNegotation:removeVideoFile';
              helper.catchError(errorStats, functionName);
          }                      
      },
      buttonVideosModal(){
          try {
              this.removeVideoFile(this.modal_view_data.video_report,'NO');  
          } catch(e) {
              let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Button function of Video view modal'}
              let functionName = 'StaffNegotation:buttonVideosModal';
              helper.catchError(errorStats, functionName);
          }                       
      },
      downloadVideo(data){
          try {
              console.log('view_modal: ' + this.view_modal);
              let context = data.context
              let time = data.data.created_at.split(' ');
              if (context){
                  let newElement = document.createElement('a')
                  newElement.setAttribute("id","downloadedVideo")
                  newElement.setAttribute('href',context)
                  newElement.setAttribute('download','Mee2box '+data.header+' / '+time[0]+'.webm')
                  newElement.style.display='none'
                  document.body.appendChild(newElement)
                  newElement.click()
                  document.body.removeChild(newElement)
                  this.removeVideoFile(this.modal_view_data.video_report,'NO');            
              } else console.log('did not pass downloadVideo context');
          } catch(e) {
              let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Downloading the video'}
              let functionName = 'StaffNegotation:downloadVideo';
              helper.catchError(errorStats, functionName);
          }            
      },  
      processViewModal(url) { 
          try {
              var videoElement = document.getElementById('videos');
              if(videoElement) videoElement.load();
              this.view_modal.size = this.bytesToSize(parseInt(this.view_modal.data.video_size));
              this.view_modal.context = url;
              this.view_modal.data.video_report_url = `${this.view_modal.context}`;  
              
              var videoLoaded = setInterval(()=>{
                  if(videoElement.readyState >= 3){
                      clearInterval(videoLoaded);
                      this.loadAnimation.destroy();                     
                      this.showPleaseWait = false;
                      this.show.videoModal = true;
                      this.show.view = false;
                      this.view_modal.header = '動画';
                  }                   
              },500);              
          } catch(e) {
              let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Process view modal details'}
              let functionName = 'StaffNegotation:processViewModal';
              helper.catchError(errorStats, functionName);
          } 
      },
      decryptDatUrl(url) {
          try {
              this.showPleaseWait = true;                 
              this.passedDecrypt = false;
              let data = new FormData();
              let self = this;
              data.append('url', url);
              staff.decryptDatUrl(data, this.role).then((response) => {
                  if (response) {
                      if(response.body.success)
                          self.datDecrypted = true;
                      self.passedDecrypt = true;
                  }
              });
          } catch(e) {
              let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Decrypt .dat file'}
              let functionName = 'StaffNegotation:decryptDatUrl';
              helper.catchError(errorStats, functionName);
          }                 
      },    
      // m2b-81  
    },
    events: {
      'uitable:view-item': function(action, data) {
        // this.show.viewModal = true
        // this.view_modal.header = '商談内容'
        // let notes = data.notes ? data.notes : ''
        // this.view_modal.content = ` : `+ notes
        // this.view_modal.button = 'URLをコピー'
      },
      'uitable:notes': function(action, data) {
        try {
          this.show.viewModal = true
          this.show.view = false
          this.view_modal.header = 'ノート'
          this.view_modal.content = 'ダウンロード'
          this.view_modal.context = data.notes
          let blobReport = new Blob([data.notes],{
            type:"text/plain;charset=utf-8;",
          });
          this.view_modal.size = this.bytesToSize(parseInt(blobReport.size))
          this.view_modal.data = data
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Waiting for the listing of notes'}
          let functionName = 'StaffNegotation::uitables:notes';
          helper.catchError(errorStats, functionName);
        }
      },
      'uitable:voice_report': function(action, data) {
        try {
          this.show.viewModal = true
          this.show.view = true
          this.view_modal.header = '議事録'
          this.view_modal.content = 'ダウンロード'
          this.view_modal.voicetext = data.voice_report.split(',')
          this.view_modal.context = this.decodeVoiceText(this.view_modal.voicetext)
          let blobReport = new Blob([data.voice_report],{
            type:"text/plain;charset=utf-8;",
          });
          this.view_modal.size = this.bytesToSize(parseInt(blobReport.size))
          this.view_modal.data = data
        } catch(e) {
          let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Waiting for the voice reports'}
          let functionName = 'StaffNegotation::uitable:voice_report';
          helper.catchError(errorStats, functionName);
        }
      },
      // m2b-81         
      'uitable:video_report': function(action, data) {       
          try {     
              this.modal_view_data = data;
              data.video_report_url = data.video_report;
              var videoUrl = `${data.video_report_url}`;
              this.video_exist = false;
              this.view_modal.data = data;                 
              this.view_modal.content = 'ダウンロード';              
              var mWait = document.getElementById('openWait');
              this.loadAnimation = bodymovin.loadAnimation({
                container: document.getElementById('loadingAnimation'),
                renderer: 'svg',
                loop: true,
                autoplay : true,
                path: "/json/loading.json"
              });
              mWait.innerHTML = "動画を開いています。しばらくお待ちください。";
              if(videoUrl.indexOf(".mp4") != -1 ) {
                  this.datDecrypted = false;
                  this.passedDecrypt = false;
                  this.decryptDatUrl(videoUrl);
                  let retval = setInterval(() => {
                      if(this.datDecrypted) {
                          clearInterval(retval)
                          this.processViewModal(videoUrl);
                      } else if(this.passedDecrypt && !this.datDecrypted) {
                          clearInterval(retval)
                          let self = this;
                          mWait.innerHTML = "ビデオファイルが存在しないかリンクが壊れています";
                          setTimeout(function(){
                              self.showPleaseWait = false; 
                          },1500);
                      }  
                  }, 100)                      
              } else {
                  console.log('Sorry! Video/audio recording were not properly merged/converted!!!!!!!');
              }                         
          } catch(e) {
              let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> viewing for the video reports'}
              let functionName = 'StaffNegotation::uitable:video_report';
              helper.catchError(errorStats, functionName);
          }                
      },    
      // m2b-81                
    },
    ready() {
      try {
        let config = {
          role: 'staff',
          showToolbar: true
        }
            if(!!this.auth.user.role) {
              this.role = this.auth.user.role
              config.role = this.role
                this.documents_table.api_url = (this.role == 'company_user') ? COMPANY_USER_NEGOTATIONS_RESOURCE : STAFF_NEGOTATIONS_RESOURCE
                if (this.role == 'company_user' && this.auth.user.companyuser.manager == 1) {
                    this.documents_table.fields = [
                        { name: 'created_at', label: '商談日', width: 20, callback: this.formatMeetingDate },
                        { name: 'last_name', label: '担当者名', width: 20 },
                        { name: 'notes', label: 'ノート', width: 20 },
                        { name: 'voice_report', label: '議事録', width: 20 },
                        { name: 'video_report', label: '動画', width: 20 },	
                        { name: 'video_size', label: '動画サイズ', width: 20, callback: this.formatVideoSize },
                        { name: 'duration', label: '商談時間', width: 20, callback: this.formatMeetingDuration }
                    ]
                }
                this.roleLoaded = true
            } else {
              this.$watch('auth.user.role', function() {
                this.role = this.auth.user.role
                config.role = this.role
                this.documents_table.api_url = (this.role == 'company_user') ? COMPANY_USER_NEGOTATIONS_RESOURCE : STAFF_NEGOTATIONS_RESOURCE
                if (this.role == 'company_user' && this.auth.user.companyuser.manager == 1) {
                    this.documents_table.fields = [
                        { name: 'created_at', label: '商談日', width: 20, callback: this.formatMeetingDate },
                        { name: 'last_name', label: '担当者名', width: 20 },
                        { name: 'notes', label: 'ノート', width: 20 },
                        { name: 'voice_report', label: '議事録', width: 20 },
                        { name: 'video_report', label: '動画', width: 20 },	
                        { name: 'video_size', label: '動画サイズ', width: 20, callback: this.formatVideoSize },
                        { name: 'duration', label: '商談時間', width: 20, callback: this.formatMeetingDuration }
                    ]
                }
                  this.roleLoaded = true
              }, true)
            }
        this.$dispatch('component-ready', config)
      } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the negotiation component'}
        let functionName = 'StaffNegotation:ready';
        helper.catchError(errorStats, functionName);
      }
    },
    components: {
      StaffSidebar,
      UiNegoTable
    }
  }
</script>
<style lang="scss" scoped>
    /* m2b-81 */  
    #videoRemove{
      background: #f44336 !important;
    }
    #videoRemove:hover{
      opacity:0.7;
    }
    #videoId{
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
      .video-textboard{
        #textbox-video-modal {
            margin-bottom: 20px;
            /* height: 100%;
            max-height: 120px;
            width: 100%;
            overflow: auto;
            background: #f2f3f5;
            font-family: sans-serif;
            font-size: 12px;
            padding: 5px; */
        }
    }
  }
  /* #negotation-textboard {
    padding: 15px;
    margin: 10px 0 15px;
    background: #f2f3f5;
    border: 1px solid rgba(137, 128, 128, 0.521);
    border-radius: 10px;
    #textbox-modal {
      height: 100%;
      max-height: 120px;
      width: 100%;
      overflow: auto;
      background: #f2f3f5;
      font-family: sans-serif;
      font-size: 12px;
      padding: 5px;
    }
  } */
   .textboard{
        #textbox-modal {
            padding: 15px;
            margin: 10px 0 15px;
            background: #f2f3f5;
            border: 1px solid rgba(137, 128, 128, 0.521);
            border-radius: 10px;
            max-height: 150px;
            overflow: auto;
        }
        #textbox-modal::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        #textbox-modal::-webkit-scrollbar-track {}
        #textbox-modal::-webkit-scrollbar-thumb {
            background-color: #333333;
        }
    }
.Modaltext{
        padding: 15px;
        margin: 10px 0 15px;
        background: #f2f3f5;
        border: 1px solid rgba(137, 128, 128, 0.521);
        border-radius: 10px;
}
/* m2b-81 */
  .videoClass {
      height: 100%;
      overflow: hidden;
      max-height: 200px;
      max-width:325px;
      margin-left: auto;        
      margin-left: auto;
      margin-right: auto;        
      display: block           
  }     
  #loadingAnimation {
    top: 20px;
    background:transparent;
    position: absolute;
    height: 100px;
    width: auto;
    left: 50%;
    transform: translate(-50%, 0);    
  }
/* m2b-81 */
</style>

