<template>
    <div
        class="ui-modal ui-modal-mask" v-show="show" :transition="transition" :class="[type]"
        :role="role" @transitionend="transitionEnd | debounce 100">
        <div class="ui-modal-wrapper" @click="close" v-el:modal-mask>
            <div class="ui-modal-container fv-modal-container" tabindex="-1" @keydown.esc="close" v-el:modal-container>

                 <ui-icon-button type="clear" icon="&#xE5CD" class="ui-modal-close-button fv-close-btn" @click="close"
                :disabled="!dismissible" v-if="showCloseButton" v-el:close-button></ui-icon-button>

                <div class="ui-modal-body fv-modal-body">
                    <div class="fv-modal-title">
                        <span>フォルダ</span>
                    </div>
                    
                    <div class="fv-folder-wrapper">
                        <div v-for="folder_item in folderList" class="folder" v-bind:key="folder_item.title" @click="getContent(folder_item.id, folder_item.title)">
                            <i class="material-icons">folder</i>
                            <div class="right-stopper"></div>
                            <span class="folder-title">{{folder_item.title}}</span>
                        </div>
                    </div>
                   
                    <div class="fv-doc-wrapper">
                        <div class="fv-docwrap-header">
                            <div class="fv-modal-title">
                                <span>{{ doc_navigation }}</span>
                            </div>
                            <div class="fv-exit-folder" @click="toggleExitFolder" :show.sync="show.exit_folder">
                               <i class="material-icons">navigate_before</i>
                               <span>戻る </span>
                               {{show.exit_folder}}
                            </div>
                        </div>
                        <div class="fv-docwrap-body col-md-12">
                            <ui-table
                                    :folder-id="current_folder"
                                    :per-page="5"
                                    :api-url="documents_table.api_url"
                                    :data-key="documents_table.data_key"
                                    :fields="documents_table.fields"
                                    :actions="documents_table.actions" 
                                    :no-content-text="documents_table.no_content_text"
                                    :documents-modal="true"
                            ></ui-table>
                        </div>
                    </div>
                </div>
                <div class="focus-redirector" @focus="redirectFocus" tabindex="0"></div>
            </div>
        </div>
    </div>
</template>
 
<script>
import classlist  from '../../js/keenui_helper.js';
import {UiIconButton, UiButton} from 'keen-ui';
import UiTable from '../common/UiTable.vue'; 
import {STAFF_DOCUMENTS_RESOURCE, STAFF_GET_FOLDER_CONTENT} from '../../js/api_routes.js';
import staff from '../../js/staff.js';
import helper from '../../js/helpers.js';
import {APP_URL} from '../../js/config.js';
import Cookie from 'js-cookie';
export default {
    name: 'doc-view-modal',
    props: {
        show: {
            type: Boolean,
            required: true,
            twoWay: true,
            exit_folder: false,
            show_more_btn: false
        },
        type: {
            type: String,
            default: 'normal', // 'small', 'normal', or 'large'
            coerce(type) {
                return 'ui-modal-' + type;
            }
        },
        header: {
            type: String,
            default: 'UiModal Header'
        },
        body: {
            type: String,
            default: 'UiModal body'
        },
        role: {
            type: String,
            default: 'dialog', // 'dialog' or 'alertdialog'
        },
        transition: {
            type: String,
            default: 'ui-modal-scale', // 'ui-modal-scale', or 'ui-modal-fade'
        },
        showCloseButton: {
            type: Boolean,
            default: true
        },
        hideFooter: {
            type: Boolean,
            default: false
        },
        dismissible: {
            type: Boolean,
            default: true
        },
        backdropDismissible: {
            type: Boolean,
            default: true
        },
    },
    data(){
        return {
            lastFocussedElement: null,
            folderList: [],
            folderOptions: [],
            documents: [],
            outside_folder: true,
            current_folder: 0,
            folderDoc: [],
            doc_page_no: 1,
            folder_page_no: 1,
            doc_navigation: 'ドキュメント',
            eventPrefix: 'docmodalshare:',
            documents_table: {
                api_url: STAFF_GET_FOLDER_CONTENT,
                data_key: `documents`,
                fields: [
                    { name: 'title', label: 'タイトル', width: 25 },
                    { name: 'description', label: '説明', width: 40 },
                ],
                actions: [
                    { name: 'view-doc', label: 'View', icon: 'visibility', icon_color: 'primary' },
                    { name: 'share-doc', label: 'Share', icon: 'share', icon_color: 'primary' }, 
                ],
                no_content_text: `ファイルがありません`,
            },
            parsedDateTime: '',
        }
    },
    events: {
        'uitable:view-doc': function(action, data) {
            try {
                // console.log('viewing doc')
                this.$dispatch(this.eventPrefix+'viewdoc', this, data)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Viewing the document'}
                let functionName = 'DocViewModal::uitable:view-doc';
                helper.catchError(errorStats, functionName);
            }
        },
        'uitable:share-doc': function(action, data){
            try {
                // console.log('sharing doc')
                this.$dispatch(this.eventPrefix+'sharedoc', this, this.getDocLink(data), data.size)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sharing the document'}
                let functionName = 'DocViewModal::uitable:share-doc';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    created(){
        try {
            // console.log('created on docviewmodal')
            staff.getFolders(this,this.getFolderCB);
            // staff.getDocumentsByFolder(this, {page: this.doc_page_no, folder: 0}, this.getDocCB)
            // console.log('docviewmodal documents')
            // console.log(this.documents)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created the document view modal component'}
            let functionName = 'DocViewModal:created';
            helper.catchError(errorStats, functionName);
        }
    },
    watch: {
        show(){
            try {
                this.$nextTick(() => {
                    if (this.show) {
                        this.opened();
                    } else {
                        this.closed();
                    }
                });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Watching the show variable'}
                let functionName = 'DocViewModal:show';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    beforeDestroy() {
        try {
            if (this.show) {
                this.tearDown();
            }
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Before destroy of the component'}
            let functionName = 'DocViewModal:beforeDestroy';
            helper.catchError(errorStats, functionName);
        }
    },
    methods: {
        getFolderCB(response){
            try {
                if (response.data) {
                    let mydata = response.body.staff_folders

                for(let key in mydata){
                        let title = mydata[key].title
                        let list = {id: mydata[key].id, title: title}
                        this.folderList.push(list)
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the folder callbacks'}
                let functionName = 'DocViewModal:getFolderCB';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the document link'}
                let functionName = 'DocViewModal:getDocLink';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleExitFolder(){
            try {
                this.doc_navigation = 'ドキュメント'
                this.outside_folder = true
                this.exit_folder = false
                this.current_folder = 0
                let vm = this
                vm.$broadcast('uitable:refresh')
                setTimeout(function(){vm.$broadcast('uitable:refresh')}, 1000)
                // console.log('modalDoc')
                // console.log(this.modalDoc)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the exit folder'}
                let functionName = 'DocViewModal:toggleExitFolder';
                helper.catchError(errorStats, functionName);
            }
        },
        getContent(id,title){
            try {
                this.current_folder = id;
                let vm = this
                // console.log('documents table')
                // console.log(this.documents_table)
                vm.$broadcast('uitable:refresh')
                setTimeout(function(){vm.$broadcast('uitable:refresh')}, 1000)
                // console.log('folder_id', id)
                this.doc_navigation = 'ドキュメント > ' + title
                this.show.exit_folder = true;
                // this.folder_page_no = 1;
                // staff.getDocumentsByFolder(this, {page: this.folder_page_no, per_page: 10, folder: id}, this.getDocFolderCB)
                this.outside_folder = false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the content from the folder'}
                let functionName = 'DocViewModal:getContent';
                helper.catchError(errorStats, functionName);
            }
        },
        getDocFolderCB(response){
            try {
                if (response.data) {
                    this.folderDoc = [];
                    response.data.documents.forEach((val, key) => {
                        this.folderDoc.push(val)
                    })

                    let paginate = response.data.meta.pagination
                    if (paginate.total_pages > paginate.current_page) {
                        this.show_more_btn = true
                    } else {
                        this.show_more_btn = false
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the document folder callback'}
                let functionName = 'DocViewModal:getDocFolderCB';
                helper.catchError(errorStats, functionName);
            }
        },
        close(e) {
            try {
                if (!this.dismissible) {
                    return;
                }

                // Make sure the element clicked was the modal mask and not a child
                // whose click event has bubbled up
                if (e.currentTarget === this.$els.modalMask && e.target !== e.currentTarget) {
                    return;
                }

                // Don't close if this event was triggered by the modal mask
                // and this.backdropDismissible is false
                if (e.currentTarget === this.$els.modalMask && !this.backdropDismissible) {
                    return;
                }

                this.show = false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Closing the document folder modal'}
                let functionName = 'DocViewModal:close';
                helper.catchError(errorStats, functionName);
            }
        },
        opened() {
            try {
                this.lastFocussedElement = document.activeElement;
                this.$els.modalContainer.focus();

                classlist.add(document.body, 'ui-modal-open');

                document.addEventListener('focus', this.restrictFocus, true);

                this.$dispatch('opened');
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Opening the document folder modal'}
                let functionName = 'DocViewModal:crated';
                helper.catchError(errorStats, functionName);
            }
        },
        closed() {
            try {
                this.tearDown();
                this.$dispatch('closed');
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Closed down the document view modal'}
                let functionName = 'DocViewModal:closed';
                helper.catchError(errorStats, functionName);
            }
        },
        redirectFocus(e) {
            try {
                e.stopPropagation();

                this.$els.modalContainer.focus();
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Redirect the focus'}
                let functionName = 'DocViewModal:redirectFocus';
                helper.catchError(errorStats, functionName);
            }
        },
        restrictFocus(e) {
            try {
                if (!this.$els.modalContainer.contains(e.target)) {
                    e.stopPropagation();
                    this.$els.modalContainer.focus();
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Restrict the focus'}
                let functionName = 'DocViewModal:restrictFocus';
                helper.catchError(errorStats, functionName);
            }
        },
        tearDown() {
            try {
                classlist.remove(document.body, 'ui-modal-open');

                document.removeEventListener('focus', this.restrictFocus, true);

                if (this.lastFocussedElement) {
                    this.lastFocussedElement.focus();
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Tearing down the modal'}
                let functionName = 'DocViewModal:tearDown';
                helper.catchError(errorStats, functionName);
            }
        },
        transitionEnd() {
            try {
                if (this.show) {
                    this.$dispatch('revealed');
                } else {
                    this.$dispatch('hidden');
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Transition Ended'}
                let functionName = 'DocViewModal:transitionEnd';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    components: {
        UiIconButton,
        UiButton,
        UiTable
    }
};
</script>

<style lang="scss" scoped>
.ui-table {
    padding: 0px; 
    table{
      tr{
        th{
            background: #333333;
            color: #fff;
        }
      }
    }
    
}
.show_more{
    position: absolute;
    margin-top: 10px;
    left: 40%;
    padding: 10px 15px 10px 15px;
    background-color: #dfdfdf;

    span{
        font-size: 12px;
    }
}
.show_more:hover{
    cursor: pointer;
}

.fv-modal-container{
    //width: 50% !important;
    padding: 42px 15px 180px 0px !important;
    height: 50%;

    .fv-close-btn{
        top: -20px !important;
        z-index:999;
    }

    .fv-modal-body{
        position: absolute;
        top: 0;
        width: 100%;
        overflow-y: scroll;
        height: 100%;

        .fv-modal-title{
            font-size: 17px;
            color: #1d2c4c;
        }

        .fv-folder-wrapper{
            padding-bottom: 5px;
            padding-left: 15px;
            margin-bottom: 15px;
            margin-top: 15px;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            height: auto;
            max-height: 85px;
            box-shadow: 0px 2.4px 1px -1px #808080;                    
        }

        .fv-doc-wrapper{
            .fv-docwrap-header{
                margin-bottom: 15px;

                .fv-modal-title , .fv-exit-folder{
                    display: inline-block;
                }
                .fv-modal-title{
                    margin-top: 5px;   
                }
                .fv-exit-folder{
                    float: right;
                    padding: 3px 15px 0px 12px;
                    border-radius: 25px;
                    background-color: rgba(0, 0, 0, 0.13);
                    
                    i, span {
                        display: inline-block;
                    }
                    i{
                        font-size: 23px !important;
                    }
                    span{
                        position: relative;
                        top: -7px;
                        font-size: 13px;
                        right: 3px;
                    }
                }
                .fv-exit-folder:hover{
                    background-color: #c8c8c891;
                    cursor:pointer;
                }
            }
            .fv-docwrap-body{
                width:100%;
                height: auto;
                padding-left: 0px;
                padding-right: 0px;
                background: rgba(0, 0, 0, 0.08);

                .docwrap-body-header{
                    margin-bottom: 10px;
                    background: #333333;
                    box-shadow: 0px 2.4px 1px -1px #808080;
                    
                    span {
                        display: inline-block;
                        color: #ffff;
                        padding-top: 5px;
                        padding-left: 5px;
                        padding-bottom: 5px;
                        background-color: #333333;
                    }
                    .title{
                        width: 30%;
                    }
                    .description{
                       width: 35%;
                    }
                    .actions{
                        width: 30%;
                    }
                }

                .docwrap-body-content{
                    height: auto;
                    max-height: 110px;
                    overflow-y: scroll;

                    .content{
                        width: 100%;
                        border-bottom: 1px solid rgba(0, 0, 0, 0.42);
                        height: 100%;
                        padding-top: 5px;
                        padding-bottom: 5px;

                        .title, .description, .actions{
                            display: inline-block;
                            padding-left: 5px;
                        }
                        .title{
                            width: 30%;
                        }
                        .description{
                            width: 35%;
                        }
                        .actions{
                            width: 30%;
                            position: relative;
                            top: -2px;
                            
                            .disabled-btn{
                                color: rgba(0, 0, 0, 0.43) !important;
                            }
                            .disabled-btn:hover{
                                cursor: default !important;
                            }
                            .view, .share{
                                color: #333333;
                                padding: 0px 10px 0px 10px;
                                display: inline-block;

                                i , span{
                                    display:inline-block;
                                }

                                i{
                                    position: relative;
                                    top: 4px;
                                    font-size: 15px;
                                }
                                span{
                                    font-size: 13px;
                                    position: relative;
                                    top: 1px;
                                    line-height: 3px;
                                }
                            }
                            .view:hover, .share:hover{
                                cursor: pointer;
                            }
                        }

                     @media only screen and (max-device-width: 1190px){
                       .actions{
                            width: 35% !important;
                       }
                       .description{
                            width: 30% !important;
                       }
                       .title{
                            width: 30% !important;
                       }
                    }

                    }
                    .content:nth-child(even){
                        background-color: #fff;
                    }
                }

            }
        }

    }
}



.folder{
    height: 33px;
    width: 90px;
    display: inline-block;
    padding-bottom: 5px;
    padding-top: 5px;
    padding-left: 8px;
    padding-right: 5px;
    margin-bottom: 10px;    
    border: 1px solid rgba(0,0,0,0.09);
    margin-right: 5px;
    box-shadow: 0 0 2px rgba(0,0,0,0.12), 0 2px 2px rgba(0,0,0,0.2);
    -webkit-transition: 0.3s;
    border-radius: 7px;
    overflow: hidden;
     i{
        font-size: 17px;
        color: #333333;
        margin-right: 2px;
        position: relative;
        bottom: -1px;
    }
    i , span{
        display:inline-block;
    }
    .folder-title{
        font-size: 13px;
        position: relative;
        left: 22px;
        bottom: 43px;
        color: #000000;
        width: 100%;
        line-height: 25px;
    }
    .right-stopper{
        width: 15px;
        height: 100%;
        top: -20px;
        left: 68px;
        position: relative;
        background-color: #fff;
        z-index: 1;
        }
}
    .folder:hover{
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #333333;
        border: 1px solid #333333;
        cursor: pointer;
        transition: .3s;
        -webkit-transition: .3s;

        .folder-title{
            color:  #333333;
        }
    }

.ui-modal {
    font-size: 14px;

    &.ui-modal-large {
        .ui-modal-container {
            width: 848px;
        }
    }

    &.ui-modal-small {
        .ui-modal-container {
            width: 320px;
        }
    }
}

body.ui-modal-open {
    overflow: hidden;
}

.ui-modal-mask {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: table;
}

.ui-modal-wrapper {
    display: table-cell;
    vertical-align: middle;
}

.ui-modal-container {
    outline: none;
    width: 50%;
    margin: 0px auto;
    padding: 0;
    background-color: white;
    border-radius: 2px;

    max-height: 100vh;
    max-width: 100vw;
    overflow-x: hidden;
    overflow-y: auto;
}

.ui-modal-header {
    display: flex;
    align-items: center;
    padding: 24px 24px 8px;

    .ui-modal-header-text {
        flex-grow: 1;
        font-size: 22px;
        display: flex;
        align-items: center;
        margin: 0;
        font-weight: normal;
    }

    .ui-modal-close-button {
        margin-top: -12px;
        margin-right: -8px;
        margin-left: auto;

        &:hover:not([disabled]),
        body[modality="keyboard"] &:focus {
         
        }

        .ui-icon {
            font-size: 20px;
        }

        &[disabled] {
            opacity: 0.5;
        }
    }
}

.ui-modal-body {
    padding: 16px 24px 24px;
}

.ui-modal-footer {
    margin-top: -8px;
    padding: 24px;
    padding-top: 8px;

    &,
    [slot] {
        display: flex;
        justify-content: flex-end;
    }

    .ui-modal-footer-left,
    [slot].ui-modal-footer-left {
        justify-content: flex-start;
    }

    .ui-button {
        margin-left: 8px;

        &:first-child {
            margin-left: 0;
        }
    }
}

.ui-modal-fade-enter,
.ui-modal-fade-leave {
    opacity: 0;
}

.ui-modal-scale-enter,
.ui-modal-scale-leave {
    opacity: 0;
}

.ui-modal-scale-enter .fv-ui-modal-container,
.ui-modal-scale-leave .fv-ui-modal-container {
    transform: scale(1.1);
}

@media only screen and (max-device-width: 480px){
    .fv-modal-container{
        height: 80%;
        //width: 100% !important;
    }
    .ui-modal-close-button{
        right: -9px !important;
        top: -21px;
    }
}

@media only screen and (max-device-width: 320px){
    .actions {
        .share, .view{
            padding: 0px 0px 0px 0px!important 
        }
        .view{
            margin-bottom: 5px !important;
        }
    }

    .show_more{
        padding: 5px 10px 5px 10px !important;
        span{
            font-size: 10px !important;
        }
    }
    .docwrap-body-content{
        max-height: 150px !important;
    }

    .fv-modal-container{
        height: 80% !important;
    }
    .ui-modal-close-button{
        right: -9px !important;
        top: -21px !important;
    }
    
    .fv-modal-title{
        font-size: 13px !important;
        width:100%;
    }
    .fv-docwrap-header{
        margin-bottom: 30px !important;
    }
    .folder{
        width: 80px !important;
        i{
            font-size: 14px !important; 
        }
        span{
            font-size: 13px !important;
        }
    }
    .fv-exit-folder{
        span{
            font-size: 12px !important;
        }
    }

}

@media only screen and (max-width: 567px){
    .fv-modal-container{
        width: 100% !important;
    }
}

@media only screen and (min-device-width : 569px) and (max-device-width : 767px){
    .fv-modal-container{
        width: 80% !important;
    }
}

@media only screen and (min-width: 768px){
    .fv-modal-container{
        width: 50% !important;
    }
}

</style>
