<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar></staff-sidebar>

        <div class="dashboardContent dashAddContent" v-el:adj-container>
            <div class="wrapper">

                <ui-alert @dismissed="error = false" type="error" :show="error">
                  <span v-html="error_message"></span>
                </ui-alert>

                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                    <!-- <ui-alert @dismiss="show.uploaderror = false" type="error" v-show="show.uploaderror">
                                <span v-html="show.uploadErrorText"></span>
                     </ui-alert> -->
                        <h1>ファイルを追加</h1>
                        <form autocomplete="off" >
                            <div class="form-group">
                                <ui-textbox
                                    label="タイトル" name="title" id="title" type="text" placeholder="ファイルタイトルを入力してください" :value.sync="document.title"
                                    validation-rules="required" v-ref:input-title
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="説明" name="description" id="description" type="text" placeholder="ファイル説明を入力してください" :value.sync="document.description" :multi-line="true" :max-length="256" validation-rules="max:256" help-text="最大256文字まで入力可能です" v-ref:input-description
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <div class="buttonCon">
                                    <ui-button type="normal" button-type="normal" color="primary" icon="file_upload" class="protoButton buttonCon" v-ref:upload-document :loading="loading.file_upload" @click="uploadClicked" text="アップロードファイル"></ui-button>
                                    <file-preview name="file" id="file" @changed="changeDocument" read-file="false" v-ref:upload-file hide-image-preview></file-preview>
                                </div>
                            </div>
                              <div class="row">
                                <div class="col-md-12 ui-textbox-help-text ui-textbox-feedback-toggle-transition" style="margin-top: -8px; margin-bottom: 5px; margin-left: -8px">.pdf .xls .xlsx .doc .docx .ppt .pptx のみアップロードが可能です</div>
                              </div>
                            <div class="form-group">
                              <ui-switch name="allow_download" :value.sync="document.allow_download">ファイルを共有する</ui-switch>
                            </div>
                         
                        </form>
                        <div class="row">
                            <div class="ui-button-group col-md-5" >
                                <ui-button :disabled="loading" @click="goBack" color="default" icon="keyboard_arrow_left">戻る</ui-button>
                                <ui-button type="normal" color="success" v-el:submit-btn :disabled="show.disable" @click="addDocument">ファイルを追加</ui-button>
                            </div>
                            
                            <!-- <p v-if="close" class="document-text-progress" > アップロード中 : <i>{{ progress }}%</i></p> -->
                            <div class=" col-md-7" >
                                <ui-progress-linear
                                :show="loading" type="determinate" id="progressLinear" color="darkblue" :value="progress"
                                ></ui-progress-linear>
                                <p v-if="serverError" class="errorConvertText {{errorConvert}}">There was an error occured. Please try again later...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 {{loading}}-" >
                         <!-- <ui-button  @click="goBack" color="default" icon="keyboard_arrow_left">戻る</ui-button> -->
                    </div>
                </div>
                <!-- <ui-modal hide-footer type="small" class="progress-modal" :show="loading" @closed="closeModal" remove-header="true" dismiss-on="close-button">
                    <div slot="header"></div>
                    <p class="textProgress"> Uploading ... Please Wait ... </p>
                    <ui-progress-linear
                        :show="loading" type="determinate" id="progressLinear" color="darkblue" :value="progress"
                    ></ui-progress-linear>
                    <p v-if="error" class="errorConvertText {{errorConvert}}">There was an error occured. Please try again later...</p>
                </ui-modal> -->
                <slot name="modal-slot"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from '../partials/StaffSidebar.vue';
import FilePreview from '../../common/FilePreview.vue';
import {APP_URL,doc} from '../../../js/config.js';
import {router} from '../../../js/app.js';
import staff from '../../../js/staff.js';
import helper from '../../../js/helpers.js';
import { UiModal, UiButton, UiTabs, UiTab, UiProgressLinear } from 'keen-ui';


export default {
    data() 
    {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            document: JSON.parse(JSON.stringify(staff.data.document)),
            file: '',
            serverError: false,
            error: false,
            error_message: '',
            loading: {
                file_upload: false,
                submit: false
            },
            show: {
                disable: true,
                uploaderror: false
            },

            loading: false,
            btnLoad: true,
            progress: 0,
            progressInterval: null,
            timeStatusInterval: 1000,
            convertSuccess: false,
            errorConvert: false,
            size: 0,
            staff: true,
            modalWatch: '',
            close: false
        }
    },
    methods: {
        goBack(){
            try {
                // console.log('going back')
                let back = this.$route.params.id
                if(back == 0){
                    // console.log('true')
                    router.go({ name: 'staff-documents'})
                } else {
                    // console.log('false')
                    router.go({ name: 'staff-documents-folder', params: { id: back } })
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting back to the previous page'}
                let functionName = 'StaffDocumentAdd:goBack';
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
                let functionName = 'StaffDocumentAdd:bytesToSize';
                helper.catchError(errorStats, functionName);
            }
        },
        closeModal(){
            try {
                this.close = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Closing the modal'}
                let functionName = 'StaffDocumentAdd:closeModal';
                helper.catchError(errorStats, functionName);
            }
        },
        addDocument() {
            try {
                this.loading = true;
                // this.loading.submit =  true;
                let data = new FormData();
                let folderid = this.$route.params.id
                this.document.folder_id = folderid

                for ( var key in this.document ) {
                    let value  = (key === 'allow_download') ? (this.document[key] == true ? 1:0) : this.document[key]
                    data.append(key, value)
                }

                data.append('file', this.file)
                this.show.disable = true;
                staff.addDocument(this, data, folderid)
                helper.progressConvertInterval(this.timeStatusInterval, this, this.size)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding the document'}
                let functionName = 'StaffDocumentAdd:addDocument';
                helper.catchError(errorStats, functionName);
            }
        },
        uploadClicked(e) {
            e.preventDefault()
        },
        changeDocument() {
            try {
                // console.log(this)
                let filename = this.$refs.uploadFile.value.name
                this.file = this.$refs.uploadFile.value
                this.size = this.bytesToSize(this.$refs.uploadFile.value.size)
                filename = `${filename.replace(/.[^.]+$/,'').substring(0,10)}.${filename.replace(/^.*\./,'')}`
                this.$refs.uploadDocument.text = `FILE: ${filename}`
                this.uploadValidation(filename);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Changing the document'}
                let functionName = 'StaffDocumentAdd:changeDocument';
                helper.catchError(errorStats, functionName);
            }
        },
         uploadValidation(filename){  //upload validation for file sharing child
           let filext = this.file.name.split('.').pop();
           let fileTypes = [
              'pdf','doc','docx','pptx','xlsx','xls','ppt'
            ]

            let ctr = 0;
            if(fileTypes.includes(filext.toLowerCase())) {
              ctr = 1;

              if(this.error_message.includes('ファイルは、pdf、doc、ppt、xlsx、xls')) {
                this.error_message = this.error_message.replace('ファイルは、pdf、doc、ppt、xlsx、xls、docxタイプのファイルである必要があります。<br />', '');
              }
            } else if(!this.error_message.includes('ファイルは、pdf、doc、ppt、xlsx、xls')) {
              this.error_message += 'ファイルは、pdf、doc、ppt、xlsx、xls、docxタイプのファイルである必要があります。<br />';
            }

            // Limit file upload up to 10MB only
            if (this.file.size > 10485760) {
              if(!this.error_message.includes('アップロード出来るファイルサ')) {
                this.error_message += 'アップロード出来るファイルサイズは10mbまでとなっております。<br />';
              }
              ctr = 0;
            } else if(this.error_message.includes('アップロード出来るファイルサ')) {
              this.error_message = this.error_message.replace('アップロード出来るファイルサイズは10mbまでとなっております。<br />', '');
            }

            if(ctr == 0){
                this.error = true;
                this.show.disable = true;
                // this.show.uploaderror = true;
            } else if(this.error_message != "") {
                this.error = true;
                this.show.disable = false;
            } else{
                this.error = false;
                this.show.disable = false;
                // this.show.uploaderror = false;
            }
        },
    },
    ready() {
        try {
            let config = {
                role: 'staff',
                showToolbar: true
            }
            // console.log(this)

            this.$dispatch('component-ready', config)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the Staff Document Add component'}
            let functionName = 'StaffDocumentAdd:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        StaffSidebar,
        FilePreview,
        UiModal, UiButton, UiTabs, UiTab, UiProgressLinear
    }
}
</script>
<style lang="scss" >
    .document-text-progress {
        font-weight:600;
        font-size:15px;
    }
    .progress-modal {
        // display: block !important;
        .ui-modal-wrapper {
            display: block !important;
            margin: 13% auto;
            padding: 0 !important;
            width: fit-content;
        }
        .textProgress {
            text-align: center;
            font-weight:600;
        }
    }

</style>
<style lang="scss" scoped>
    

.col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col,
.col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm,
.col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md,
.col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg,
.col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl,
.col-xl-auto {
  position: relative;
  width: 100%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}
@media (min-width: 992px) {
  .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
    float: left;
  }
  .col-md-12 {
    width: 100%;
  }
  .col-md-11 {
    width: 91.66666667%;
  }
  .col-md-10 {
    width: 83.33333333%;
  }
  .col-md-9 {
    width: 75%;
  }
  .col-md-8 {
    width: 66.66666667%;
  }
  .col-md-7 {
    width: 58.33333333%;
  }
  .col-md-6 {
    width: 50%;
  }
  .col-md-5 {
    width: 41.66666667%;
  }
  .col-md-4 {
    width: 33.33333333%;
  }
  .col-md-3 {
    width: 25%;
  }
  .col-md-2 {
    width: 16.66666667%;
  }
  .col-md-1 {
    width: 8.33333333%;
  }
}
@media (min-width: 768px) {
  .col-md {
    flex-basis: 0;
    flex-grow: 1;
    max-width: 100%;
  }
  .col-md-auto {
    flex: 0 0 auto;
    width: auto;
    max-width: none;
  }
  .col-md-1 {
    flex: 0 0 8.333333%;
    max-width: 8.333333%;
  }
  .col-md-2 {
    flex: 0 0 16.666667%;
    max-width: 16.666667%;
  }
  .col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
  .col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
  }
  .col-md-5 {
    flex: 0 0 41.666667%;
    max-width: 41.666667%;
  }
  .col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
  }
  .col-md-7 {
    flex: 0 0 58.333333%;
    max-width: 58.333333%;
  }
  .col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
  }
  .col-md-9 {
    flex: 0 0 75%;
    max-width: 75%;
  }
  .col-md-10 {
    flex: 0 0 83.333333%;
    max-width: 83.333333%;
  }
  .col-md-11 {
    flex: 0 0 91.666667%;
    max-width: 91.666667%;
  }
  .col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
  }
}
</style>