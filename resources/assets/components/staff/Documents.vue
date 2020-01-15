<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar :api-url="notices_post.api_url"></staff-sidebar>

        <div class="dashboardContent" v-el:adj-container  id="dashboard">
            <div class="wrapper">
                    <div class="col-md-12">
                        <div class="docHeader">
                            <div class="docTitle">
                                <span>ドキュメント</span>
                            </div>
                            <div class="divRight">
                               <div class="docActions" @click="addDocument">
                                    <span>資料</span>
                                    <i class="material-icons">add_circle</i>
                                </div>
                                <div class="docActions" @click="folderModal">
                                    <span>フォルダーを追加</span>
                                    <i class="material-icons">create_new_folder</i>
                                </div> 
                            </div>
                        </div>
                    </div>
                     <div class="col-md-12" v-if="folderList.length">
                        <div class="body-wrapper folder-wrapper">
                            <h1>フォルダー</h1>
                            <ul v-for="folder_item in folderList" class="folder" id="folder{{folder_item.id}}" @click="getContent(folder_item.id)" v-bind:key="folder_item.id">
                                <div class="inside-wrapper dropzone fold{{folder_item.id}}" id="{{folder_item.id}}"></div>
                                <i class="material-icons icon{{folder_item.id}}">folder</i>
                                <div class="right-stopper">
                                    <p class="folder-title">{{folder_item.text}}</p>
                                </div>
                            </ul>
                        </div>
                    </div>  
                    <div class="col-md-12">
                        <div class="body-wrapper doc-wrapper" id="wrapperdoc">
                            <h1>資料</h1>
                            <ul class="draggable-container">
                            <div class="lds-roller" v-if="documentLoader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                            <div v-for="document in documents" v-bind:key="document.title" class="doclist" v-else>
                                <div v-if="document.image_url">
                                    <div id=doc{{document.id}} style="width:100% !important" class="document draggable col-sm-12 col-md-12 col-lg-12">
                                        <div class="title-stopper col-xs-5 col-sm-3 col-md-4 col-lg-4">
                                            <p class="document-title">{{document.title}}</p>
                                        </div>
                                        <div class="desc-stopper col-xs-3 col-sm-3 col-md-3  col-lg-4">
                                            <p class="document-title">{{document.description}}</p>
                                        </div>
                                        <div class="doc-actions col-xs-3 col-sm-6 col-md-4 col-lg-4">
                                            <i class="material-icons doc-burger" @click="toggleContextMenu(document.id)">menu</i>
                                            <p @click="toggleViewDocModal(document)"><i class="material-icons doc-icon" >visibility</i> <span>ビュー</span></p>
                                            <p @click="toggleEditDoc(document.id)"><i class="material-icons doc-icon" >edit</i> <span>編集</span></p>
                                            <p @click="toggleDeleteDocModal(document.id)"><i class="material-icons doc-icon" >delete</i> <span>削除</span></p>
                                        </div>
                                    </div>
                                    <ul class="ui-menu doc-menu has-icons" role="menu" tabindex="-1" v-if="document.menu">
                                        <a class="ui-menu-option" role="menu-item" tabindex="0" @click="toggleViewDocModal(document)">
                                            <div class="ui-menu-option-content ui-menu-default">
                                                <i class="ui-icon material-icons ui-menu-option-icon visibility" aria-hidden="true">visibility</i>
                                                    <div class="ui-menu-option-text">View</div>
                                            </div>
                                            <div class="ui-ripple-ink"></div>
                                        </a>
                                        <a class="ui-menu-option" role="menu-item" tabindex="1"  @click="toggleEditDoc(document.id)">
                                            <div class="ui-menu-option-content ui-menu-default">
                                                <i class="ui-icon material-icons ui-menu-option-icon edit" aria-hidden="true">edit</i>
                                                    <div class="ui-menu-option-text">Edit</div>
                                            </div>
                                            <div class="ui-ripple-ink"></div>
                                        </a>
                                        <a class="ui-menu-option" role="menu-item" tabindex="2" @click="toggleDeleteDocModal(document.id)">
                                            <div class="ui-menu-option-content ui-menu-default">
                                                <i class="ui-icon material-icons ui-menu-option-icon delete" aria-hidden="true">delete</i>
                                                    <div class="ui-menu-option-text">Delete</div>
                                            </div>
                                            <div class="ui-ripple-ink"></div>
                                        </a>
                                    </ul>
                                </div>
                            </div>
                            <h3 v-if="noDoc">ドキュメントなし</h3>
                            </ul>
                            <div class="docpagination" v-if="!noDoc">
                                 <ui-fab type="mini"
                                    color="primary" icon="navigate_next" tooltip="次のページ" tooltip-position="top center" @click="nextPaginate"
                                ></ui-fab>
                                 <ui-fab type="mini" class="prev"
                                    color="primary" icon="navigate_before" tooltip="前のページ" tooltip-position="top center" @click="prevPaginate"
                                ></ui-fab>
                            </div>
                        </div> 
                    </div>
                           <!--  <div v-for="document in documents" class="doclist">
                                <div id=doc{{document.id}} @contextmenu.prevent="toggleContextMenu(document.id)"class="document draggable drag-drop">
                                    <div class="inside-docwrapper" id="doc{{document.id}}"></div>
                                    <i class="material-icons doc-icon">insert_drive_file</i>
                                    <div class="right-stopper">
                                        <p class="document-title">{{document.title}}</p>
                                    </div>
                                    <div>
                                        <i class="material-icons doc-icon">insert_drive_file</i>
                                        <i class="material-icons doc-icon">insert_drive_file</i>
                                        <i class="material-icons doc-icon">insert_drive_file</i>
                                    </div>
                                </div>  
                                <ui-menu id="menu{{document.id}}" style="position: absolute;top: 112px" :options="menuOptions" show-icons v-if="document.menu"></ui-menu>

                                <ul class="ui-menu doc-menu has-icons" role="menu" tabindex="-1" style="position: absolute; z-index: 3" v-if="document.menu">
                                    <a class="ui-menu-option" role="menu-item" tabindex="0" @click="toggleViewDocModal(document)">
                                        <div class="ui-menu-option-content ui-menu-default">
                                            <i class="ui-icon material-icons ui-menu-option-icon visibility" aria-hidden="true">visibility</i>
                                                <div class="ui-menu-option-text">View</div>
                                        </div>
                                        <div class="ui-ripple-ink"></div>
                                    </a>
                                    <a class="ui-menu-option" role="menu-item" tabindex="1"  @click="toggleEditDoc(document.id)">
                                        <div class="ui-menu-option-content ui-menu-default">
                                            <i class="ui-icon material-icons ui-menu-option-icon edit" aria-hidden="true">edit</i>
                                                <div class="ui-menu-option-text">Edit</div>
                                        </div>
                                        <div class="ui-ripple-ink"></div>
                                    </a>
                                    <a class="ui-menu-option" role="menu-item" tabindex="2" @click="toggleDeleteDocModal(document.id)">
                                        <div class="ui-menu-option-content ui-menu-default">
                                            <i class="ui-icon material-icons ui-menu-option-icon delete" aria-hidden="true">delete</i>
                                                <div class="ui-menu-option-text">Delete</div>
                                        </div>
                                        <div class="ui-ripple-ink"></div>
                                    </a>
                                </ul>
                                <ui-menu option-selected="contextSelected" class="doc-menu" style="position: absolute; z-index: 2" :options="menuOptions" show-icons v-if="document.menu"></ui-menu>
                            </div>
                       
                 <div class="col-md-12">
                        <div class="doc-wrapper">
                            <ui-table
                                :folder-id="documents_table.folder_id"
                                :api-url="documents_table.api_url"
                                :data-key="documents_table.data_key"
                                :fields="documents_table.fields"
                                :actions="documents_table.actions" 
                                :no-content-text="documents_table.no_content_text"
                            ></ui-table>
                        </div>
                    </div> -->
                <!-- Add New Folder Modal-->
                <ui-modal hide-footer type="small"
                :show.sync="show.addFolderModal"
                class="addFolder"
                >
                    <div slot="header">
                        <h1 class="ui-modal-header-text">
                             フォルダ名
                        </h1>
                    </div>
                    <ui-textbox
                         name="name" id="newFolderName" type="text" placeholder="フォルダ名を入力" v-el:foldername
                         :value.sync=folderName
                         validation-rules="required|min:1|max:64" validate-on-blur
                         @keyup="validateFolderName" @keyup.enter.native="addFolder"
                         autocomplete="off"
                    ></ui-textbox>
                    <ui-button color="success" @click="addFolder" :disabled.sync="disabled.folderdisable">作成する</ui-button>
                </ui-modal>
                
                <!-- Move Document to Folder Modal-->
                <ui-modal hide-footer type="small"
                :show.sync="show.moveDocModal"
                class="addFolder"
                @closed="toggleMoveDisable"
                >
                    <div slot="header">
                        <h1 class="ui-modal-header-text">
                             文書の移動
                        </h1>
                    </div>
                    <ui-button class="movedoc-btn" color="success" @click="moveDocument" >作成する</ui-button>
                </ui-modal>
                <ui-confirm
                header="ファイルを削除する" type="danger" confirm-button-text="削除"
                confirm-button-icon="delete" deny-button-text="キャンセル" @confirmed="deleteConfirmed"
                :show.sync="show.deleteConfirm" close-on-confirm
                >
                    ファイルを削除しますが宜しいですか？
                </ui-confirm>
                <ui-modal :show.sync="show.viewModal" hide-footer :header="view_modal.header" class="document-modal">
                    <div class="col-md-12"> 
                        <img :src="view_modal.image_url" class="thumbnail-modal" \>
                    </div>
                    <div class="col-md-12"> {{ view_modal.content }} </div>
                        
                </ui-modal>
                <slot name="modal-slot"></slot>
                
                 <!-- Ui Snackbar for moving document -->
            </div>
        </div>
    </div>
</template>
<script>
import StaffSidebar from './partials/StaffSidebar.vue';
import UiTable from '../common/UiTable.vue';
import { UiModal, UiButton, UiTabs, UiTab, UiRadioGroup, UiSnackbar, UiMenu, UiFab, UiProgressLinear } from 'keen-ui';
import {APP_URL} from '../../js/config.js';
import staff from '../../js/staff.js';
import {router} from '../../js/app.js';
import {STAFF_DOCUMENTS_RESOURCE, STAFF_GET_FOLDER_CONTENT, STAFF_NOTICES_RESOURCE} from '../../js/api_routes.js';
import helper from '../../js/helpers.js';

export default {
    data() {
        return {
            noDoc: true,
            documentLoader: true,
            draggableChecker: '',
            folderName: '',
            auth: this.$parent.auth,
            droppableLib: '',
            APP_URL,
            docMeta: '',
            moveDoc: '0',
            Doc: {
                id: '',
                folder: ''
            },
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            dragDrop: {
                id: '', // Document ID
                folder: '',
            },
            contextMenuId: '',
            contextSelected: '',
            menuOptions: [
                {
                    id: 'view',
                    text: 'View',
                    icon: 'visibility',
                    value: 1
                },
                {
                    id: 'edit',
                    text: 'Edit',
                    icon: 'edit',
                    value: 2
                }, {
                    id: 'delete',
                    text: 'Delete',
                    icon: 'delete',
                    value: 3
                }
            ],
            notices_post: {
                api_url: STAFF_NOTICES_RESOURCE,
            },
            // documents_table: {
            //     folder_id: 0,
            //     api_url: STAFF_DOCUMENTS_RESOURCE,
            //     data_key: `documents`,
            //     fields: [
            //         { name: 'title', label: 'タイトル', width: 25 },
            //         { name: 'description', label: '説明', width: 40 },
            //         { name: 'file_url', label: 'ファイル', width: 25 }
            //     ],
            //     actions: [
            //         { name: 'view-item', label: '', icon: 'search', icon_color: 'primary' },
            //         { name: 'edit-item', label: '', icon: 'create', icon_color: 'warning', 
            //             routelink: { name: 'staff-edit-document', params: { id: 'document_id' } } },
            //         { name: 'move-item', label: '', icon: 'reply', icon_color: 'success' },
            //         { name: 'delete-item', label: '', icon: 'delete', icon_color: 'danger' }
            //     ],
            //     no_content_text: `ファイルがありません`
            // },
            doc_page_no: 1,
            disabled: {
                folderdisable: true,
                movedisable: true
            },
            show: {
                viewModal: false,
                addFolderModal: false,
                deleteConfirm: false,
                moveDocModal: false
            },
            view_modal: {
                header: '',
                content: '',
                image_url:''
            },  
            current_item: {},
            folderList: [],
            folderOptions: [],
            documents: [],
        }
    },
    methods: {
        toggleEditDoc(docid){
            try {
                router.go({ name: 'staff-edit-document', params: { id: docid, folder: 0 } })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the edit document'}
                let functionName = 'StaffDocuments:toggleEditDoc';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleDeleteDocModal(docid){
            try {
                this.show.deleteConfirm = true
                this.current_item = docid
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the delete document modal'}
                let functionName = 'StaffDocuments:toggleDeleteDocModal';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleViewDocModal(doc){
            try {
                this.show.viewModal = true
                this.view_modal.header = doc.title
                this.view_modal.content = `説明 : \n ${doc.description ? doc.description : ''}`
                this.view_modal.image_url = `${doc.image_url}`
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the view document'}
                let functionName = 'StaffDocuments:toggleViewDocModal';
                helper.catchError(errorStats, functionName);
            }
        },
        moveDocument(){
            try {
                // console.log('moving document')
                let params = this.dragDrop
                let vm = this
                let id = this.dragDrop.id
                let docu = this.documents.filter((item) => item.id != id)
                this.documents = docu
                staff.moveDoctoFolder(this,params)
                // this.updateDocuments()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the move document'}
                let functionName = 'StaffDocuments:moveDocument';
                helper.catchError(errorStats, functionName);
            }
        },  
        updateDocuments(){
            try {
                staff.getDocumentsByFolder(this, {page: this.doc_page_no, folder: 0}, this.getDocCB)
                this.initializeDroppable()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the update documents'}
                let functionName = 'StaffDocuments:updateDocuments';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleMoveDisable(){
            try {
                // console.log('toggled move disable')
                this.moveDoc = '0'
                this.disabled.movedisable = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the move disable'}
                let functionName = 'StaffDocuments:toggleMoveDisable';
                helper.catchError(errorStats, functionName);
            }
        },
        validateFolderName(){
            try {
                if(this.folderName.length > 0){
                    this.disabled.folderdisable = false
                } else{
                    this.disabled.folderdisable = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Validating the folder name'}
                let functionName = 'StaffDocuments:validateFolderName';
                helper.catchError(errorStats, functionName);
            }
        },
        folderModal(){
            try {
                this.show.addFolderModal = true;
                let elem = document.querySelector('#newFolderName').value
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Showing the folder modal'}
                let functionName = 'StaffDocuments:folderModal';
                helper.catchError(errorStats, functionName);
            }
        },
        addDocument(){
            try {
                router.go({ name: 'staff-add-document', params: { id: '0' } })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Routing the the adding of document page'}
                let functionName = 'StaffDocuments:addDocument';
                helper.catchError(errorStats, functionName);
            }
        },
        addFolder(){
            try {
                if(this.folderName.length > 0){
                    let b = {title: this.folderName}
                    this.folderList.push(b)
                    staff.createFolder(this,b)
                    this.show.addFolderModal = false
                    this.folderName = ''

                    // staff.getFolders(this)
                    // staff.getFolders(this,this.getFolderCB);
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding a folder'}
                let functionName = 'StaffDocuments:addFolder';
                helper.catchError(errorStats, functionName);
            }
        },
        getFolderCB(response){
            try {
                if (response.data) {
                    let mydata = response.body.staff_folders
                    this.folderList = []
                    for(let key in mydata){
                        let title = mydata[key].title
                        let folderid = mydata[key].id
                        let list = {id: folderid, value:'' + folderid, text: title}
                        this.folderList.push(list)
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the folder callbacks'}
                let functionName = 'StaffDocuments:getFolderCB';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting the array'}
                let functionName = 'StaffDocuments:sortArr';
                helper.catchError(errorStats, functionName);
            }
        },
        getDocCB(response){
            try {
                // console.log(response)
                if (response.data) {
                    //console.log('get docsCB')
                    //console.log(response.data)
                    this.documents = [];
                    this.noDoc = false
                    let menu = false
                    let vm = this

                    response.data.documents.forEach((val, key) => {
                        let sortedArr = []
                        sortedArr = vm.sortArr(val.pages, 'page_id')
                        let doc = {id: val.document_id, title: val.title, menu: false, description: val.description, image_url: (!!val.pages.length) ? sortedArr[0].image_url : ''}
                        this.documents.push(doc)
                    })
                    // console.log('getDocsCb')
                    // console.log(this.documents)

                    this.docMeta = response.data.meta.pagination
                    //  console.log(this)
                    // Add Interval Checker for Document Draggables
                    this.draggableChecker = setInterval(this.checkDocDraggables, 1000)
                } else {
                    this.noDoc = true;
                    this.documentLoader = false
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the document callbacks'}
                let functionName = 'StaffDocuments:getDocCB';
                helper.catchError(errorStats, functionName);
            }
        },
        nextPaginate(){
            try {
                if(this.docMeta.current_page < this.docMeta.total_pages){
                    this.droppableLib.destroy()
                    this.docMeta.current_page++
                    staff.getDocumentsByFolder(this, {page: this.docMeta.current_page, folder: 0}, this.getDocCB)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Next pagination'}
                let functionName = 'StaffDocuments:nextPaginate';
                helper.catchError(errorStats, functionName);
            }
        },
        prevPaginate(){
            try {
                if(this.docMeta.current_page > 1){
                    this.droppableLib.destroy()
                    this.docMeta.current_page--
                    staff.getDocumentsByFolder(this, {page: this.docMeta.current_page, folder: 0}, this.getDocCB)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Previous pagination'}
                let functionName = 'StaffDocuments:prevPaginate';
                helper.catchError(errorStats, functionName);
            }
        },
        getContent(content){
            try {
                router.go({ name: 'staff-documents-folder', params: { id: content } })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Routing to the documents folder'}
                let functionName = 'StaffDocuments:getContent';
                helper.catchError(errorStats, functionName);
            }
        },
        deleteConfirmed() {
            try {
                let vm = this
                let currItem = this.current_item
                console.log(currItem)
                let params = {id: currItem}
                staff.deleteDocumentFromStorage(params)

                staff.deleteDocument(this,{id: this.current_item}, () =>{
                    vm.documents = vm.documents.filter((item) => item.id !== currItem)
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Confirmation for the delete'}
                let functionName = 'StaffDocuments:deleteConfirmed';
                helper.catchError(errorStats, functionName);
            }
        },
        onDroppableDragStart(mirror){
            try {
                mirror.classList.remove("col-md-4")
                mirror.classList.remove("col-xs-4")
                mirror.classList.add("col-xs-12")
                mirror.classList.add("col-md-12")
                mirror.style.height = '50px'
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Drop box - draggable start'}
                let functionName = 'StaffDocuments:onDroppableDragStart';
                helper.catchError(errorStats, functionName);
            }
        },
        onDroppableDragStop(mirror){
            try {
                let dropped = document.getElementsByClassName('draggable-dropzone--occupied')
                // console.log(dropped)
                if(dropped.length > 0 && dropped[0].classList[0] != "draggable-container"){
                    let folderID = '#'+ dropped[0].id
                    // console.log(folderID)
                    let folder = document.querySelector(folderID).classList.remove("draggable-dropzone--occupied")
                    this.dragDrop.folder = folderID.replace('#folder', '')
                    this.moveDocument()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Droppable box - draggging stop'}
                let functionName = 'StaffDocuments:onDroppableDragStop';
                helper.catchError(errorStats, functionName);
            }
        },
        initializeDroppable(){
            try {
                // Don't initialize for IE
                if(/Trident/i.test(navigator.userAgent)) return;

                let vm = this
                this.droppableLib = new Draggable.Droppable(document.querySelectorAll("ul"),{
                    draggable: '.document',
                    dropzone: 'ul',
                    mirror: {
                        constrainDimensions: true,
                        cursorOffsetX: 10,
                        cursorOffsetY: 10,
                    },
                    delay: 200
                });

                this.droppableLib.on('drag:start', function(e){
                    // console.log('dragstart')
                    // console.log(e)
                    let mirror = e.data.mirror.children[0]
                    // console.log(mirror)
                    vm.onDroppableDragStart(mirror)
                    let docid = e.data.source.id.replace('doc', '')
                    vm.dragDrop.id = docid
                    vm.hideMenu(docid)
                });
                this.droppableLib.on('drag:stop', function(e){
                    // console.log('drag stop')
                    // console.log(e)
                    vm.onDroppableDragStop()
                });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Initializing of the droppable'}
                let functionName = 'StaffDocuments:initializeDroppable';
                helper.catchError(errorStats, functionName);
            }
        },
        checkDocDraggables(){
            try {
                // console.log('checking Doc draggables')
                if(this.documents.length > 0){
                    this.documentLoader = false;
                    // console.log('removing checkDocDraggable Interval')
                    clearInterval(this.draggableChecker)
                    this.initializeDroppable()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking the document draggables'}
                let functionName = 'StaffDocuments:checkDocDraggable';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleContextMenu(id){
            try {
                // console.log('toggled Context Menu with ID: ' + id)
                this.contextMenuId = id
                document.removeEventListener('click', this.contextFocus)
                let options = document.getElementsByClassName('ui-menu-option')
                let vm = this
                for(let key in this.documents){
                    if(id == this.documents[key].id){ // Get the doc id of menu
                        // console.log('key match')
                        if(!this.documents[key].menu){ // If menu is not open
                            // console.log('got the it right with' + this.documents[key].id)
                            this.documents[key].menu = true
                            setTimeout(function(){document.addEventListener('click', vm.contextFocus)}, 500);
                        } else {
                            // console.log('document is already true, will hide it now')
                            this.documents[key].menu = false
                        }
                    } else {
                        // console.log('menu already true')
                            this.documents[key].menu = false
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling context menu'}
                let functionName = 'StaffDocuments:toggleContextMenu';
                helper.catchError(errorStats, functionName);
            }
        },
        contextFocus(event){
            try {
                if(typeof event.path == "undefined" || typeof event.path[1] == "undefined") return;
                // console.log('contextFocusClickEvent')
                // console.log(event)
                let eventTarget = event.path[1].className
                if(eventTarget == 'ui-menu-option'){
                    // console.log(event)
                    // console.log(this.contextSelected)
                } else {
                    // console.log('removing Listener')
                    this.hideMenu(this.contextMenuId)
                    document.removeEventListener('click', this.contextFocus)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Focusing the context'}
                let functionName = 'StaffDocuments:contextFocus';
                helper.catchError(errorStats, functionName);
            }
        },
        hideMenu(id){
            try {
                // console.log('hiding: ' + id)
                for(let key in this.documents){
                    if(id == this.documents[key].id){
                        this.documents[key].menu = false
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Hiding the menu'}
                let functionName = 'StaffDocuments:hideMenu';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    events: {
        'uitable:view-item': function(action, data) {
            try {
                this.show.viewModal = true
                this.view_modal.header = data.title
                this.view_modal.content = `説明 : \n ${data.description}`
                this.view_modal.image_url = `${data.image_url}`
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to the view button of the table'}
                let functionName = 'StaffDocuments::uitable:view-item';
                helper.catchError(errorStats, functionName);
            }
        },
        'uitable:delete-item': function(action, data){
            try {
                this.show.deleteConfirm = true
                this.current_item = data
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to the delete button of the table'}
                let functionName = 'StaffDocuments:toggleEditDoc';
                helper.catchError(errorStats, functionName);
            }
        },
        'uitable:move-item': function(action, data){
            try {
                // console.log(data)
                this.Doc.id = data.document_id
                // console.log('movedoc')
                // console.log(this.moveDoc)
                this.disabled.movedisable = true
                this.show.moveDocModal = true
                this.current_item = data
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to the move document'}
                let functionName = 'StaffDocuments::uitable:move-item';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    watch:{
        moveDoc: function(value){
            try {
                this.disabled.movedisable = false
                this.Doc.folder = value
                // console.log(value)
                // console.log(this)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Moving the document'}
                let functionName = 'StaffDocuments:moveDoc';
                helper.catchError(errorStats, functionName);
            }
        },
        documents: function(value){
            try {
                if(!value.length){
                    this.noDoc = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to the documents'}
                let functionName = 'StaffDocuments:toggleEditDoc';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    ready() {
        try {
            // console.log('ready')
            let config = {
                role: 'staff',
                showToolbar: true
            }
            this.$dispatch('component-ready', config)
            let vm = this
            staff.getFolders(this, this.getFolderCB)
            staff.getDocumentsByFolder(this, {page: this.doc_page_no, folder: 0}, this.getDocCB)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Documents being created'}
            let functionName = 'StaffDocuments:created';
            helper.catchError(errorStats, functionName);
        }
    },
    created(){
        // console.log(this)
    },
    components: {
        StaffSidebar,
        UiTable,
        UiRadioGroup,
        UiSnackbar,
        UiMenu ,
        UiProgressLinear
    }
}
</script>
<style lang="scss">
.docpagination{
    margin-bottom: 30px;
    margin-top: -10px;

    .prev{
        margin-right: 10px;
    }
    .ui-fab{
        display: inline-block;
    }
    .ui-fab-mini{
        width: 30px;
        height: 30px;
        float: right;
    }
}

.lds-roller {
  display: inline-block;
  position: relative;
  width: 64px;
  height: 64px;
  left: 40%;
}
.lds-roller div {
  animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  transform-origin: 32px 32px;
}
.lds-roller div:after {
  content: " ";
  display: block;
  position: absolute;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #385899;
  margin: -3px 0 0 -3px;
}
.lds-roller div:nth-child(1) {
  animation-delay: -0.036s;
}
.lds-roller div:nth-child(1):after {
  top: 50px;
  left: 50px;
}
.lds-roller div:nth-child(2) {
  animation-delay: -0.072s;
}
.lds-roller div:nth-child(2):after {
  top: 54px;
  left: 45px;
}
.lds-roller div:nth-child(3) {
  animation-delay: -0.108s;
}
.lds-roller div:nth-child(3):after {
  top: 57px;
  left: 39px;
}
.lds-roller div:nth-child(4) {
  animation-delay: -0.144s;
}
.lds-roller div:nth-child(4):after {
  top: 58px;
  left: 32px;
}
.lds-roller div:nth-child(5) {
  animation-delay: -0.18s;
}
.lds-roller div:nth-child(5):after {
  top: 57px;
  left: 25px;
}
.lds-roller div:nth-child(6) {
  animation-delay: -0.216s;
}
.lds-roller div:nth-child(6):after {
  top: 54px;
  left: 19px;
}
.lds-roller div:nth-child(7) {
  animation-delay: -0.252s;
}
.lds-roller div:nth-child(7):after {
  top: 50px;
  left: 14px;
}
.lds-roller div:nth-child(8) {
  animation-delay: -0.288s;
}
.lds-roller div:nth-child(8):after {
  top: 45px;
  left: 10px;
}
@keyframes lds-roller {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

// .draggable-source--is-dragging{
//
// }

.draggable-container{
    padding: 0px;
    background-color: #fff !important;
    color: #1A1A1A !important;
    box-shadow: none !important;
    border: none !important;
}
*, *::before, *::after {
    transition: unset;
}
.movedoc-btn{
    margin-top: 10px;
}
.ui-radio-group-help-text{
    color: rgba(0, 0, 0, 0.82);
}
.docHeader{
    margin-bottom: 40px;
    margin-top: 40px;
    padding-right: 15px;
    padding-left: 15px;
    .divRight{
        float: right;
        
        .docActions{
            // margin-right: 0 !important;
            margin-right: 5px !important;

        span {
            padding-right: 15px;
            padding-left: 10px;
            font-size: 17px;
            position: relative;
            top: -7px;
        }
            display:inline-block;
            background: #385899;
            padding: 5px 5px 5px 5px;
            color: white;
        i {
            font-size: 30px;
        }
            span, i {
                display:inline-block;
            }
        }
        .docActions:hover{
            cursor:pointer;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.12), 0 4px 5px rgba(0, 0, 0, 0.2);
        }
    }
    
    .docTitle{
        margin-top: 25px ;
        font-size: 18px;
        font-weight: 500;
    }
    .docTitle,.divRight{
        display: inline-block;
    }
}
.folder-wrapper, .doc-wrapper{
    margin-bottom: 25px;
    margin-right: 15px;
    margin-left: 15px;
    
}
.folder-wrapper{
 position: -webkit-sticky;
  position: sticky;
  top: 0;
}
.body-wrapper{
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.12), 0 2px 2px rgba(0, 0, 0, 0.2);
    height: auto;
    padding: 20px 15px 20px 15px;
    .folder{
        height: 40px;
        width: 150px;
        display: inline-block;
        padding-bottom: 2px;
        padding-top: 2px;
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 10px;
        border: 1px solid rgba(0, 0, 0, 0.09);
        margin-right: 15px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.12), 0 2px 2px rgba(0, 0, 0, 0.2);
        -webkit-transition: .3s;
        border-radius: 7px;
        overflow: hidden;
        background-color: #fff;
        color: #000000;
        i{
            font-size: 35px;
            color: #3b5998;
            position: relative;
            top: -40px;
        }
        i , span{
            display:inline-block;
        }
        .folder-title{
            font-size: 14px;
            position: relative;
            top: -10px;
            width: 100%;
            line-height: 29px;
        }
        .right-stopper{
            width: 80px;
            height: 100%;
            top: -78px;
            left: 43px;
            position: relative;
            overflow: hidden;
        }
        .inside-wrapper{
            width: 150px;
            background-color: transparent;
            height: 40px;
            position: relative;
            top: -3px;
            left: -15px;
        }
    }
    .draggable-container--over{
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #385899;
        border: 1px solid #385899;
        cursor: pointer;
        transition: .3s;
        color: #fff !important;
        background-color: #385899;
        i{
            color: #ffffff;
        }
    }
    .folder:hover{
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #385899;
        border: 1px solid #385899;
        cursor: pointer;
        transition: .3s;
        -webkit-transition: .3s;
    }
    .doclist{
        display: block;
        height: auto;
    }

    .draggable-mirror{
       background-color: #385899 !important;
        width: 150px !important;
        height: 38px !important;
        box-shadow: 0 0 15px #fff, 0 2px 2px rgba(0, 0, 0, 0.2) !important;
        line-height: 0px !important;
       .doc-actions, .desc-stopper{
        display: none !important;
       }
        .title-stopper{
            color: #fff;
            top: 1px !important;
        }
    }

    .document{
        height: 50px;
        line-height: 40px;
        display: inline-block;
        padding-bottom: 2px;
        padding-top: 2px;
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 10px;
        border: 1px solid rgba(0, 0, 0, 0.09);
        margin-right: 15px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.12), 0 2px 2px rgba(0, 0, 0, 0.2);
        border-radius: 7px;
        overflow: hidden;
        background-color: #fff;
        color: #1A1A1A;
        .doc-icon , .title-stopper, .desc-stopper, .doc-actions{
            display: inline-block
        }
        .doc-icon{
            font-size: 30px;
            color: #3b5998;
            position: relative;
        }
        .doc-icon , span{
            display:inline-block;
        }
        .document-title{
            font-size: 14px;
            position: relative;
            width: 100%;
        }

        .title-stopper, .desc-stopper{
            position: relative;
            top: -9px;
        }
        .title-stopper{
        //    width: 35%;
            height: 100%;
            overflow:hidden;
        }
        .desc-stopper{
        //    width: 35%;
         height: 100%;
         overflow:hidden;
        }
        .doc-actions{
        text-align: center;
        height: 100%;
        position: relative;
        //top: -13px;
        padding-left: 0px;
        padding-right: 0px;
        float: right;

        .doc-burger{
            font-size: 30px;
        }
        p{
            display:inline-block;
            margin-right: 5px;

            span{
                position: relative;
                top: -9px;
            }
        
        }
       
        }
        
        .inside-docwrapper{
           // width: 130%;
            background-color: transparent;
            height: 100%;
            position: relative;
            top: 0px;
            left: -15px;
            z-index: 2;
        }
        @media (min-width: 992px){
            float: none !important;
        }
       
    }
    .document:hover{
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #385899;
        border: 1px solid #385899;
        cursor: pointer;
        transition: .3s;
        -webkit-transition: .3s;
        .document-title{
            color:  #385899;
        }
    }

    .doc-menu{
        display: block;
        background-color: rgba(0, 0, 0, 0.02);
        position: relative;
        max-width: 100%;
        border-radius: 8px;
        top: -11px;

        .ui-menu-option-icon {
            margin-right: 16px;
            font-size: 26px;
            color: #385899;
        }

        .ui-menu-option-text {
            color: rgb(0, 0, 0);
        }
    }
}
@media (max-width: 426px){
    .docTitle{
        width: 100%;
    }
    .divRight{
        width:100%;
        margin-top: 15px !important;
        
         .docActions:nth-child(2){
            margin-left: 0 !important;
            margin-top: 7px !important;
        }
    }
    .docHeader{
        margin-bottom: 90px;
    }
}
@media only screen and (min-device-width : 320px) and (max-device-width : 374px){
    .docHeader {
        margin-bottom: 130px;
    }
}

@media only screen and (min-device-width : 320px) and (max-device-width : 768px){
   h1{
    font-size: 19px !important;
   }
}

@media only screen and (min-device-width : 320px) and (max-device-width : 425px){
    .document{
        padding-right: 0px !important;
        padding-left: 0px !important;
    }
    .doc-actions{
        top: 11px;
        p{
            display: none !important;
        }
    }
}

@media (min-device-width: 426px) and (max-device-width : 767px){
    .doc-actions{
        top: 11px;
          p{
            display: none !important;
        }
    }
}

@media (max-width: 768px) {
    .document-modal {
        padding-bottom: 145px;
    }
}


@media (max-width: 768px) and (orientation:landscape) {
    .doc-actions{
        top: 12px;
          p{
            display: none !important;
        }
    }
}

@media only screen and (min-width: 767px){
    .doc-actions{
        .doc-burger{
            display: none !important;
        }
    }
}
@media only screen and (min-width: 992px){
    .doc-actions{
        top: -3px !important;
        float: none;
    }
}
.document-modal {
    .ui-modal-header-text {
        padding: 0 !important;
    }
    .thumbnail-modal {
        width: 100%;    
        background: #ccc;
        padding: 5px 50px;
        margin-bottom: 10px;
    }
    .ui-modal-body {
        padding: 0 !important;
        font-weight: 600;
        font-size: 15px;
    }
}
</style>
