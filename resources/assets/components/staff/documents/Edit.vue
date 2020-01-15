<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar></staff-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">

                <ui-alert @dismissed="error = false" type="error" :show="error">
                    {{ error_message }}
                </ui-alert>

                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h1>
                            <span>
                                ファイルを更新
                            </span>
                            <ui-progress-circular :size="20" :stroke="6" :show="!dataloaded" color="primary"></ui-progress-circular>
                        </h1>
                        <form autocomplete="off" v-if="dataloaded">
                            <div class="form-group">
                                <ui-textbox
                                    label="タイトル" name="title" id="title" type="text" placeholder="ファイルタイトルを入力してください" :value.sync="document.title" 
                                    validation-rules="required" v-ref:input-title
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="説明" name="description" id="description" type="text" placeholder="ファイル説明を入力してください" :value.sync="document.description" :multi-line="true" :max-length="256" validation-rules="max:256" help-text="Max 256 characters" v-ref:input-description
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-switch name="allow_download" :value.sync="document.allow_download">ファイルを共有する</ui-switch>
                            </div>
                            <div class="form-group docPages">
                                <div v-if="sortingFlag">
                                    <div class="pageItemCon" v-for="page in document.pages" :key="page.id">
                                        <h3>Page {{ $index+1 }}</h3>
                                        <div class="pageItem">
                                            <ui-textbox
                                            label="説明" name="annotation" id="annotation" type="text" placeholder="ファイル説明を入力してください" :value.sync="page.annotation" :multi-line="true" validation-rules="" help-text="" 
                                            ></ui-textbox>
                                            <img :src="page.image_url" class="imgResponsive" \>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="ui-button-group">
                             <ui-button  @click="goBack" color="default" icon="keyboard_arrow_left">戻る</ui-button>
                            <ui-button @click="updateDocument" type="normal" button-type="submit" :loading="loading.submit" color="success" v-el:submit-btn>ファイルを更新する</ui-button>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        
                    </div>
                </div>
                
                <slot name="modal-slot"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from '../partials/StaffSidebar.vue';
import FilePreview from '../../common/FilePreview.vue';
import {APP_URL} from '../../../js/config.js';
import staff from '../../../js/staff.js';
import {router} from '../../../js/app.js';
export default {
    data() 
    {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            document: staff.data.document,
            file: '',
            docpages: [],
            sortingFlag: false,
            error: false,
            error_message: null,
            loading: {
                file_upload: false,
                submit: false
            },
            dataloaded: false
        }
    },
    methods: {
        updateDocument() {
            try {
                this.loading.submit =  true
                let data = new FormData();
                data.append('_method', 'PUT')
                for ( var key in this.document ) {
                    let value
                        = (key === 'allow_download')
                        ? (this.document[key] == true ? 1:0)
                        : this.document[key]

                    if(key == 'pages') {
                        for ( var page in this.document[key] ) {
                            data.append(`pages[${this.document[key][page]['page_id']}]`, this.document[key][page]['annotation'])
                        }
                        break;
                    }
                    data.append(key, value)
                }
                data.append('file', this.file)
                staff.updateDocument(this, this.$route.params, data)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the document'}
                let functionName = 'StaffDocumentEdit:updateDocument';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleValidation() {
            try {
                this.$refs.inputTitle.blurred()
                this.$refs.inputDescription.blurred()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the document'}
                let functionName = 'StaffDocumentEdit:onDroppableDragStop';
                helper.catchError(errorStats, functionName);
            }
        },
        uploadClicked(e) {
            e.preventDefault()
        },
        goBack(){
            try {
                // console.log('going back')
                let back = this.$route.params.folder
                if(back == '0'){
                    // console.log('true')
                    router.go({ name: 'staff-documents'})
                } else{
                    // console.log('false')
                    router.go({ name: 'staff-documents-folder', params: { id: back } })
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting back to the previously viewed page'}
                let functionName = 'StaffDocumentEdit:goBack';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting Array'}
                let functionName = 'StaffDocumentEdit:sortArr';
                helper.catchError(errorStats, functionName);
            }
        },
        sortPages(){
            try {
                // console.log('sortPages')
                // console.log(this.docpages)
                this.docpages = this.sortArr(this.docpages,'page_id')
                this.sortingFlag = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting pages'}
                let functionName = 'StaffDocumentEdit:sortPages';
                helper.catchError(errorStats, functionName);
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
            staff.getDocument(this, this.$route.params)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the Edit Staff Component'}
            let functionName = 'StaffDocumentEdit:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        StaffSidebar,
        FilePreview
    }
}
</script>