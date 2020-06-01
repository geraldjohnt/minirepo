<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar></staff-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">
                    <div class="col-md-12">
                        <div class="docCrumbs">
                            <div class="docCrumbsLeft" v-link="{ name: 'staff-documents' }">
                                <div class="back-btn">
                                    <i class="material-icons">arrow_back_ios</i>
                                    <span>ドキュメントに戻る</span>
                                </div>
                            </div>
                            <div class="docCrumbsRight">
                            <div class="rename-btn" @click="renameFolderModal" style="margin-right: 10px">
                                <i class="material-icons">edit</i>
                                <span>フォルダの名前を変更する</span>
                            </div>
                            <div class="remove-btn" @click="removeFolderModal">
                                <i class="material-icons">delete</i>
                                <span>フォルダを削除する</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="folderList.length">
                        <div class="body-wrapper folder-wrapper">
                            <h1>フォルダー</h1>
                        <ul v-for="folder_item in folderList" v-bind:key="folder_item.id" class="folder" id="folder{{folder_item.id}}" @click="gotoFolder(folder_item.id)">
                                <div class="inside-wrapper dropzone fold{{folder_item.id}}" id="{{folder_item.id}}"></div>
                                <i class="material-icons icon{{folder_item.id}}">folder</i>
                                <div class="right-stopper">
                                    <p class="folder-title">{{folder_item.text}}</p>
                                </div>
                            </ul>
                        </div>
                    </div>  <!-- End of Folder col-md-12 -->
                    <div class="col-md-12">
                        <div class="body-wrapper doc-wrapper" id="wrapperdoc">
                            <h1>{{folder.title}}</h1>
                            <div class="docAction" v-link="{ name: 'staff-add-document' }">
                                <span>資料</span>
                                <i class="material-icons">add_circle</i>
                            </div>
                            <ul class="draggable-container">
                            <div class="lds-roller" v-if="documentLoader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                            <div v-for="document in documents" v-bind:key="document.id" class="doclist" v-else>

                                <div v-if="document.image_url">
                                    <div id=doc{{document.id}} class="document draggable col-sm-12 col-md-12 col-lg-12" style="width:100%">
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
                        </div> <!-- End of doc-wrapper -->
                    </div>
                    <!-- <div class="col-md-12">
                        <div class="docwrapper">
                        <div class="docHeader">
                            <div class="docTitle">
                                <span>{{folder.title}}</span>
                            </div>
                            <div class="docAction" v-link="{ name: 'staff-add-document' }">
                                <span>資料</span>
                                <i class="material-icons">add_circle</i>
                            </div>
                        </div>

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
                <!-- Rename Folder -->
                 <ui-modal hide-footer type="small"
                :show.sync="show.renameFolderModal"
                class="addFolder"
                @closed="resetModalValue"
                >
                    <div slot="header">
                        <h1 class="ui-modal-header-text">
                             フォルダ名
                        </h1>
                    </div>
                    <ui-textbox
                         name="name" id="newFolderName" type="text" placeholder="フォルダ名を入力" v-el:foldername
                         :value.sync=folder.rename
                         validation-rules="required|min:1|max:64" validate-on-blur
                         @keyup="validateFolderName" @keyup.enter.native="renameFolder"
                         autocomplete="off"
                    ></ui-textbox>
                    <ui-button class="" color="success" @click="renameFolder" :disabled.sync="disabled.folderdisable">作成する</ui-button>
                </ui-modal>
                
                <!-- Remove Folder Confirmation -->
                <ui-confirm
                header="フォルダを削除する" type="danger" confirm-button-text="削除"
                confirm-button-icon="delete" deny-button-text="キャンセル" @confirmed="removeFolder"
                :show.sync="show.removeFolderConfirm" close-on-confirm
                >
                    フォルダを削除してもよろしいですか？
                </ui-confirm>
                
                <!-- Remove item from table Confirmation -->
                <ui-confirm
                header="ファイルを削除する" type="danger" confirm-button-text="削除"
                confirm-button-icon="delete" deny-button-text="キャンセル" @confirmed="deleteConfirmed"
                :show.sync="show.deleteConfirm" close-on-confirm
                >
                    ファイルを削除しますが宜しいですか？
                </ui-confirm>
                <ui-modal
                :show.sync="show.viewModal" hide-footer :header="view_modal.header" class="folder-modal"
                >
                    <div class="col-md-12"> 
                        <img :src="view_modal.image_url" class="thumbnail-modal" \>
                    </div>
                    <div class="col-md-12"> {{ view_modal.content }} </div>
                </ui-modal>
                <slot name="modal-slot"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from '../partials/StaffSidebar.vue';
import UiTable from '../../common/UiTable.vue'; 
import {Vue, router} from '../../../js/app.js';
import {APP_URL,doc} from '../../../js/config.js'; 
import staff from '../../../js/staff.js';
import helper from '../../../js/helpers.js';
import {STAFF_DOCUMENTS_RESOURCE, STAFF_GET_FOLDER_CONTENT} from '../../../js/api_routes.js';

export default {
    data() {
        return {
            noDoc: true,
            auth: this.$parent.auth,
            APP_URL,
            folderList: [],
            folder: {
                title: '',
                id: this.$route.params.id,
                rename: ''
            },
            documents : [],
            docMeta: '',
            documentLoader: true,
            doc_page_no: 1,
            droppableLib: '',
            dragDrop: {
                id: '', // Document ID
                folder: '',
            },
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            // documents_table: {
            //     folder_id: this.$route.params.id,
            //     api_url: STAFF_GET_FOLDER_CONTENT,
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
            //         { name: 'delete-item', label: '', icon: 'delete', icon_color: 'danger' }
            //     ],
            //     no_content_text: `ファイルがありません`
            // },
            show: {
                viewModal: false,
                deleteConfirm: false,
                removeFolderConfirm: false,
                renameFolderModal: false
            },
            disabled: {
                folderdisable: true
            },
            view_modal: {
                header: '',
                content: '',
                image_url: ''
            },
            current_item: {},
        }
    },
    methods: {
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
                        }else{
                            // console.log('document is already true, will hide it now')
                            this.documents[key].menu = false
                        }
                    }else{
                        // console.log('menu already true')
                            this.documents[key].menu = false
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the context menu'}
                let functionName = 'StaffFolder:toggleContextMenu';
                helper.catchError(errorStats, functionName);
            }
        },
        contextFocus(event){
            try {
                // console.log('contextFocusClickEvent')
                // console.log(event)
                let eventTarget = event.path[1].className
                if(eventTarget == 'ui-menu-option'){
                    // console.log(event)
                    // console.log(this.contextSelected)
                }else{
                    // console.log('removing Listener')
                    this.hideMenu(this.contextMenuId)
                    document.removeEventListener('click', this.contextFocus)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Focusing the context'}
                let functionName = 'StaffFolder:contextFocus';
                helper.catchError(errorStats, functionName);
            }
        },
        gotoFolder(folderid){
            try {
                // console.log('gotoFolder: '+ folderid)
                let folder = folderid
                this.folder.id = folderid
                router.go({ name: 'staff-documents-folder', params: { id: folderid } })
                location.reload()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting inside the child folder'}
                let functionName = 'StaffFolder:gotoFolder';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleEditDoc(docid){
            try {
                let folderid = this.folder.id
                router.go({ name: 'staff-edit-document', params: { id: docid, folder: folderid } })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the edit document'}
                let functionName = 'StaffFolder:toggleEditDoc';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleDeleteDocModal(docid){
            try {
                this.show.deleteConfirm = true
                this.current_item = docid
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the delete document modal'}
                let functionName = 'StaffFolder:toggleDeleteDocModal';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleViewDocModal(doc){
            try {
                this.show.viewModal = true
                this.view_modal.header = doc.title
                this.view_modal.content = `説明: \n ${doc.description ? doc.description : ''}`
                this.view_modal.image_url = `${doc.image_url}`
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the view document modal'}
                let functionName = 'StaffFolder:toggleViewDocModal';
                helper.catchError(errorStats, functionName);
            }
        }, 
        validateFolderName(){
            try {
                if(this.folder.rename.length > 0){
                    this.disabled.folderdisable = false
                } else{
                    this.disabled.folderdisable = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Validating the folder name'}
                let functionName = 'StaffFolder:validateFolderName';
                helper.catchError(errorStats, functionName);
            }
        },
        resetModalValue(){
            try {
                this.folder.rename = this.folder.title;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Resetting the modal value'}
                let functionName = 'StaffFolder:resetModalValue';
                helper.catchError(errorStats, functionName);
            }
        },
        renameFolder(){
            try {
                let ob = {id: this.folder.id, title:this.folder.rename}
                staff.updateFolder(this,ob)
                this.show.renameFolderModal = false
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Renaming the folder'}
                let functionName = 'StaffFolder:renameFolder';
                helper.catchError(errorStats, functionName);
            }
        },
        renameFolderModal(){
            try {
                this.show.renameFolderModal = true
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Renaming folder modal'}
                let functionName = 'StaffFolder:renameFolderModal';
                helper.catchError(errorStats, functionName);
            }
        },
        removeFolderModal(){
            try {
                this.show.removeFolderConfirm = true;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Removing a folder modal'}
                let functionName = 'StaffFolder:removeFolderModal';
                helper.catchError(errorStats, functionName);
            }
        },
        removeFolder(){
            try {
                let id = {id: this.folder.id}
                staff.deleteFolder(id)
                router.go({ name: 'staff-documents'})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Removing a folder'}
                let functionName = 'StaffFolder:removeFolder';
                helper.catchError(errorStats, functionName);
            }
        },
        addFolder(){
            try {
                let a = 'folder_' +  (this.folderList.length + 1)
                let b = {name: a}
                this.folderList.push(b)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding a folder'}
                let functionName = 'StaffFolder:toggleEditDoc';
                helper.catchError(errorStats, functionName);
            }
        },   
        getContent(content){
            try {
                // console.log('get Content')
                router.push({ name: 'staff-documents-folder'})
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting folder content'}
                let functionName = 'StaffFolder:getContent';
                helper.catchError(errorStats, functionName);
            }
        },
        deleteConfirmed(){
            try {
                let vm = this
                staff.deleteDocument(this,{id: this.current_item}, () =>{
                    vm.$broadcast('uitable:refresh')
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Showing of the delete confirmation'}
                let functionName = 'StaffFolder:deleteConfirmed';
                helper.catchError(errorStats, functionName);
            }
        },
        getFoldersCB(response){
            try {
                if (response.data) {
                    // console.log('getting Folders')
                    // console.log(response.data)
                    let mydata = response.body.staff_folders
                    this.folderList = []

                    for(let key in mydata){
                        if(mydata[key].id != this.folder.id){
                            // console.log('folder not match')
                            let title = mydata[key].title
                            let folderid = mydata[key].id

                            let list = {id: folderid, value:'' + folderid, text: title}
                            this.folderList.push(list)
                            // console.log(this.folderList)
                        }else{
                            // console.log('folder match')
                            this.folder.title = mydata[key].title
                        }
                    }
                    // console.log(this.folderList)
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting folder callbacks'}
                let functionName = 'StaffFolder:getFoldersCB';
                helper.catchError(errorStats, functionName);
            }
        },
        getDocCB(response){
            try {
                // console.log(response)
                if (response.data) {
                    // console.log('get docsCB')
                    // console.log(response.data)
                    this.documents = [];
                    this.noDoc = false
                    let menu = false
                    response.data.documents.forEach((val, key) => {
                        console.log(val)
                        let doc = {id: val.document_id, title: val.title, menu: false, description: val.description, image_url: val.pages[0].image_url}
                        this.documents.push(doc)
                    })
                    // console.log('getDocsCb')
                    // console.log(this.documents)

                    this.docMeta = response.data.meta.pagination
                    //  console.log(this)
                    // Add Interval Checker for Document Draggables
                    this.draggableChecker = setInterval(this.checkDocDraggables, 1000)
                }else{
                    this.noDoc = true
                    this.documentLoader = false
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the documents callback'}
                let functionName = 'StaffFolder:getDocCB';
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
                let functionName = 'StaffFolder:nextPagination';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Previous Pagination'}
                let functionName = 'StaffFolder:prevPaginate';
                helper.catchError(errorStats, functionName);
            }
        },
        initializeDroppable(){
            try {
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Initializing the droppable'}
                let functionName = 'StaffFolder:initializeDroppable';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Droppable dragging start'}
                let functionName = 'StaffFolder:onDroppableDragStart';
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
                }else{
                    // console.log('no documents')
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking of documents that are draggable'}
                let functionName = 'StaffFolder:checkDocDraggable';
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
                let functionName = 'StaffFolder:hideMenu';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Droppable dragging stop'}
                let functionName = 'StaffFolder:onDroppableDragStop';
                helper.catchError(errorStats, functionName);
            }
        },
        moveDocument(){
            try {
                // console.log('moving document')
                let params = this.dragDrop
                let id = this.dragDrop.id
                let docu = this.documents.filter((item) => item.id != id)
                this.documents = docu
                staff.moveDoctoFolder(this,params)
                this.$broadcast('ui-snackbar::create', {
                    message: '文書が正常に移動された',
                    duration: 5 * 1000
                });
                // this.updateDocuments()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Moving the document'}
                let functionName = 'StaffFolder:moveDocument';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    events: {
        'uitable:view-item': function(action, data) {
            try {
                this.show.viewModal = true
                this.view_modal.header = data.title
                this.view_modal.content = `説明: \n ${data.description ? data.description : ''}`
                this.view_modal.image_url = `${data.image_url}`
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Viewing the items inside the table'}
                let functionName = 'StaffFolder::uitable:view-item';
                helper.catchError(errorStats, functionName);
            }
        },
        'uitable:delete-item': function(action, data){
            try {
                this.show.deleteConfirm = true
                this.current_item = data
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Deleting an item inside the table'}
                let functionName = 'StaffFolder::uitable:delete-item';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    watch:{
        documents: function(value){
            try {
                if(!value.length){
                    this.noDoc = true
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to the documents listing'}
                let functionName = 'StaffFolder:documents';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    ready() {
        try {
            let config = {
                role: 'staff',
                showToolbar: true
            }
            this.$dispatch('component-ready', config)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Preparing the Folder Component'}
            let functionName = 'StaffFolder:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    created(){
        try {
            // console.log('getting Folder')
            staff.getFolders(this, this.getFoldersCB)
            staff.getDocumentsByFolder(this, {page: this.doc_page_no, folder: this.folder.id}, this.getDocCB)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Creating the Folder Component'}
            let functionName = 'StaffFolder:created';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        StaffSidebar,
        UiTable
    }
}
</script>
<style lang="scss">
    .folder-modal {
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
<style lang="scss" scoped>
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
  background: #333333;
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

.docwrapper{
    margin-left: 15px;
    margin-right: 15px;
}
.docAction{
        span {
            padding-right: 15px;
            padding-left: 10px;
            font-size: 18px;
            position: relative;
            bottom: 6px;
        }
        i {
            font-size: 28px;
        }

        display:inline-block;
        float: right;
        margin-right: 5px;
        margin-top: -45px ;
        margin-bottom: 15px;
        background: #333333;
        padding: 5px 5px 5px 5px;
        color: white;
    }
    .docAction:hover{
            cursor:pointer;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.12), 0 4px 5px rgba(0, 0, 0, 0.2);
    }
.docHeader{
    
    .docTitle{
        float:left;
        margin-top: 25px;
        margin-left: 15px;
    }
}

.docCrumbs{
    margin-bottom: 20px;
    font-size: 13px;
    margin-right: 15px;
    margin-left: 15px;
    margin-top: 40px;
    
   

    .docCrumbsLeft{
        display:inline-block;
    }

    .docCrumbLeft, .docCrumbsRight{
        display: inline-block;
    }
    
    .back-btn{
        position: relative;
        padding: 10px 15px 10px 15px;
        background: rgba(0, 0, 0, 0.11);

        i{
            font-size: 15px;
        }
        span{
            position: relative;
            bottom: 2px;
        }
    }
    .docCrumbsRight{
        float:right;
    }

    .rename-btn, .remove-btn{
        display:inline-block;

        i{
            font-size: 15px;
        }
        span{
            position: relative;
            bottom: 2px;
        }
    }
    .rename-btn:hover, .remove-btn:hover, .back-btn:hover{
            cursor:pointer;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.12), 0 4px 5px rgba(0, 0, 0, 0.2);
    }
    .remove-btn{
        padding: 10px 15px 10px 15px;
        color: white;
        background: rgba(255, 0, 0, 0.58);
    }
    .rename-btn{
        padding: 10px 15px 10px 15px;
        background: rgba(0, 0, 0, 0.11);
    }
}
//.body-wrapper{
//    margin-top: 7%;
//    margin-right: 15px;
//    margin-left: 15px;
//    box-shadow: 0 0 2px rgba(0, 0, 0, 0.12), 0 2px 2px rgba(0, 0, 0, 0.2);
//    height: auto;
//    padding: 20px 15px 20px 15px;

//    .folder{
//        height: auto;
//        width: 100px;
//        display: inline-block;
//        margin-left: 15px;
//        margin-bottom: 15px;
//        text-align: center;
//        padding-bottom: 5px;
//        border: 1px solid #ffffff;
//        box-shadow: 0 0 2px rgba(0, 0, 0, 0.12), 0 2px 2px rgba(0, 0, 0, 0.2);
//        transition: .3s;
//        -webkit-transition: .3s;

//        .folder-icon{
//            background: url('../image/folder.png') no-repeat;
//            width: 64px;
//            height: 64px;
//            margin-left: 21px;
//        }
//        .folder-title{
//            font-size: 14px;
//        }
//    }
//    .folder:hover{
//        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #333333;
//        border: 1px solid #333333;
//        cursor: pointer;
//        transition: .3s;
//       -webkit-transition: .3s;
//    }
//}

@media only screen and (min-device-width : 320px) and (max-device-width : 768px){
   h1{
    font-size: 19px !important;
   }
}

@media (max-width: 600px){
    
    .docCrumbs{
        margin-bottom: -60px !important;
    }

    .docCrumbsLeft{
        display: flex !important;
    }

    .docCrumbsRight{
        float: none !important;
        margin-top: 15px;
    }
}

@media (max-width: 426px){
    .docCrumbs{
        margin-bottom: -90px !important;

        .docCrumbsRight{
            .remove-btn{
                margin-top: 15px !important;
            }
        }
    }

    .docwrapper{
        margin-top: 120px;
    }
    
    .docAction{
            margin-top: 8px !important;
        }

    .folder-wrapper{
        margin-top: 110px;
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
            color: #333333;
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
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #333333;
        border: 1px solid #333333;
        cursor: pointer;
        transition: .3s;
        color: #fff !important;
        background-color: #333333;
        i{
            color: #ffffff;
        }
    }
    .folder:hover{
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #333333;
        border: 1px solid #333333;
        cursor: pointer;
        transition: .3s;
        -webkit-transition: .3s;
    }

    .doclist{
        display: block;
        height: auto;
    }

    .draggable-mirror{
       background-color: #333333 !important;
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
            color: #333333;
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
        box-shadow: 0 0 2px rgb(56, 88, 153), 0 2px 2px #333333;
        border: 1px solid #333333;
        cursor: pointer;
        transition: .3s;
        -webkit-transition: .3s;

        .document-title{
            color:  #333333;
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
            color: #333333;
        }

        .ui-menu-option-text {
            color: rgb(0, 0, 0);
        }
    }
}

</style>
