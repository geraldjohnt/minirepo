import Vue, {router} from './app.js';
import errorLogs from './error_logs';
import auth from './auth.js';
import browserDetect from './browser_detect.js';
import {profile, doc, negotation, notice} from './config.js';
import {staff_service, user_service} from './api_routes.js';
import {handleErrorRequest, removeLoadingBtn, objectToQueryString} from '../js/helpers.js';
import helper from './helpers.js';


export default {
    data: {
        auth,
        browserDetect,
        profile,
        document: doc,
        negotation,
        notice,
        in_meeting: 0
    },
    moveDoctoFolder(context, params){
        try {
            //  (this)
            Vue.http.post(
                    staff_service.STAFF_DOC_MOVE, params
                ).then(response => {
                    context.$broadcast('uitable:refresh')
                    //  (response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Moving the document to a folder'}
            let functionName = 'AssetsStaff:moveDocToFolder';
            this.checkError(errorStats, functionName);
        }
    },
    getFolders(context, cb){
        try {
            Vue.http.get(
                    staff_service.STAFF_GET_FOLDERS,
                ).then(response => {
                    //  (response)
                    if(response.body.length != 0){
                        if(typeof cb == 'function') {
                            //  ('getFolder if')
                            cb(response)
                        }else{
                            //  ('getFolder else')
                            let mydata = response.body.staff_folders
                            context.folderOptions = []
                            context.folderList = []

                            for(let key in mydata){
                                let title = mydata[key].title
                                if(title.length > 7){
                                    title = mydata[key].title.slice(0,7) + '..'
                                }
                                let list = {id: mydata[key].id, title: title}
                                context.folderList.push(list)
                                let ob = {value:'' + mydata[key].id ,text:mydata[key].title}
                                context.folderOptions.push(ob);
                            }
                        }
                    }
                })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the folders list'}
            let functionName = 'AssetsStaff:getFolders';
            this.checkError(errorStats, functionName);
        }
    },
    getFolder(context, params){
        try {
            Vue.http.post(
                    staff_service.STAFF_GET_FOLDER, params
                ).then(response => {
                    context.folder.title = response.body.staff_folder.title
                    context.folder.rename = response.body.staff_folder.title
                    context.folder.id = response.body.staff_folder.id
                })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the folder'}
            let functionName = 'AssetsStaff:getFolder';
            this.checkError(errorStats, functionName);
        }
    },
    createFolder(context, params){
        try {
            Vue.http.post(
                    staff_service.STAFF_CREATE_FOLDER, params
                ).then(response => {
                    this.getFolders(context,context.getFolderCB)
                })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Creating a folder'}
            let functionName = 'AssetsStaff:createFolder';
            this.checkError(errorStats, functionName);
        }
    },
    deleteFolder(params){
        try {
            Vue.http.post(
                    staff_service.STAFF_DELETE_FOLDER,params
                ).then(response => {
                    //  ('response from Delete Folder')
                    //  (response)
                })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Deleting a folder'}
            let functionName = 'AssetsStaff:deleteFolder';
            this.checkError(errorStats, functionName);
        }
    },
    updateFolder(context,params){
        try {
            Vue.http.post(
                    staff_service.STAFF_UPDATE_FOLDER,params
                ).then(response => {
                    context.folder.title = response.body
                    context.folder.rename = response.body
                })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating a folder'}
            let functionName = 'AssetsStaff:updateFolder';
            this.checkError(errorStats, functionName);
        }
    },
    getMemo(context){
        try {
            Vue.http.get(
                    user_service.USER_GET_MEMO,
                ).then(response => {
                    context.memo = response.body;
                })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the memo'}
            let functionName = 'AssetsStaff:getMemo';
            this.checkError(errorStats, functionName);
        }
    },
    updateMemo(context, params){
        try {
            Vue.http.post(
            user_service.USER_UPDATE_MEMO,
                params
            ).then(response => {
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the memo'}
            let functionName = 'AssetsStaff:updateMemo';
            this.checkError(errorStats, functionName);
        }
    },
    updateCheck(context, params){ // Mee2box to slack message
        try {
            Vue.http.post(
                staff_service.CHECK_ERR,
                params
            ).then(response => {
                console.log(response)
            }, response => {
                context.error_message = response.data.message
                context.error = true
            }).catch(e => {})
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the check'}
            let functionName = 'AssetsStaff:updateCheck';
            this.checkError(errorStats, functionName);
        }
    },
    checkError(res,local) { // Error reciever
        let errorMsg = ''
        let userInfo = this.data.auth.user
        let IEbrowser = browserDetect.detectIE()
        let browser_select = ''

        browser_select = (IEbrowser.trigger) ? {'browser':IEbrowser.browser, 'version':IEbrowser.version } : 
                            { 'browser':browserDetect.specs().browser.name, 'version':browserDetect.specs().browser.version };

        for (let e in errorLogs.error){
            if (errorLogs.error[e].code == res.status){
                errorMsg = {
                    'userAuth' : userInfo.authenticated,
                    'userRole' : userInfo.role,
                    'userEmail' : userInfo.profile.email,
                    'userFname' : userInfo.profile.first_name,
                    'userLname' : userInfo.profile.last_name,
                    'statusPageMsg' : res.ErrorMsgFromPage,
                    'statusMsg' : res.ErrorTryCatchMsg ? res.ErrorTryCatchMsg.message : errorLogs.error[e].msg,
                    'statusCode' : errorLogs.error[e].code,
                    'statusText' : errorLogs.error[e].status,
                    'specsOS': browserDetect.specs().os.name,
                    'specsOSversion': browserDetect.specs().os.version,
                    'specsBrowser': browser_select.browser,
                    'specsBrowserversion': browser_select.version,
                    'location' : local,
                }
            }
        }
        this.updateCheck({"":""},errorMsg);
    },
    update(context, params) {
        try {
            Vue.http.post(
                staff_service.STAFF_RESOURCE,
                params
            ).then(response => {
                context.error = false
                this.data.profile = response.data.user
                context.auth.user.profile = response.data.user
                context.$root.$broadcast('ui-snackbar::create', {
                    message: '更新が完了しました',
                    duration: 5000
                })
                router.go({
                    name: `staff-dashboard`
                })
            }, response => {
                handleErrorRequest(context,response)
                context.error = true
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the user profile'}
            let functionName = 'AssetsStaff:update';
            this.checkError(errorStats, functionName);
        }
    },
    updateNotice(context,params) {
        try {
            Vue.http.post(
                staff_service.STAFF_NOTICES_READ_RESOURCE,
                params
            ).then(response => {
                //  (response)
            }).catch(e => {
                //  (e);
                //  (params);
            });
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the notice'}
            let functionName = 'AssetsStaff:updateNotice';
            this.checkError(errorStats, functionName);
        }
    },
    checkMeeting(context) {
        try {
            Vue.http.get(
                staff_service.STAFF_GET_RESOURCE
            ).then(response => {
                context.in_meeting = response.data.staff.in_meeting ? true : false
            }, response => { })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking the meeting'}
            let functionName = 'AssetsStaff:checkMeeting';
            this.checkError(errorStats, functionName);
        }
    },
    checkPeerId(context, peer_id, cb) {
        try {
            Vue.http.get(
                `${staff_service.STAFF_CHECK_PEER_ID}?peer_id=${peer_id}`
            ).then(response => {
                if(typeof cb == 'function') {
                    cb()
                }
            }, response => {
                context.error = true
                context.error_message = "同じ接続ナンバーが使われています"
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking the Peer ID'}
            let functionName = 'AssetsStaff:checkPeerId';
            this.checkError(errorStats, functionName);
        }
    },
    startMeeting(context, params) {
        try {
            Vue.http.get(
                `${staff_service.STAFF_START_MEETING_RESOURCE}?peer_id=${params.peer_id}`
            ).then(response => {
                context.in_meeting = response.data.staff.in_meeting ? true : false
            }, response => { })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Starting the meeting'}
            let functionName = 'AssetsStaff:startMeeting';
            this.checkError(errorStats, functionName);
        }
    },
    stopMeeting(context) {
        try {
            Vue.http.get(
                staff_service.STAFF_STOP_MEETING_RESOURCE
            ).then(response => {
                context.in_meeting = response.data.staff.in_meeting ? true : false
            }, response => { })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Stopping the meeting'}
            let functionName = 'AssetsStaff:stopMeeting';
            this.checkError(errorStats, functionName);
        }
    },
    createNotes(params){
        try {
            return Vue.http.post(
                `${staff_service.STAFF_CREATE_NOTES}`,
                params
            ).then(response => {
                return {
                    file_size: response.data.file_size,
                    file_name: 'sharednotes/'+ response.data.file_name
                }
            },response => {
                let splitmsg = response.body.message.split('):');
                response.ErrorMsgFromPage = splitmsg[1];
                let functionName = 'Create Notes / Shared Notes';
                this.checkError(response,functionName); // pass the errors with two params, one is reponse ,other one is the function name
            }).catch(e => {
                //  (e);
                //  (params);
            });
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Creating the notes'}
            let functionName = 'AssetsStaff:createNotes';
            this.checkError(errorStats, functionName);
        }
    },
    addDocument(context, params, cb) {
        try {
            document.getElementById('progressLinear').style.display = "block";
            Vue.http.post(
                staff_service.STAFF_DOCUMENTS_RESOURCE,
                params
            ).then(response => {
                context.error = false
                //  ('add Document')
                //  (response)
                //  (context)
                // context.$root.$broadcast('ui-snackbar::create', {
                //     message: 'ファイルアップロードが完了しました',
                //     duration: 5000
                // })
                // context.loading.submit =  false
                removeLoadingBtn(context.$els.submitBtn)
                context.convertSuccess = true

                helper.progressConvertInterval(100, context, context.size)

                // console.log(progress)
                // if (progress){
                    // if(cb == '0'){
                    //     //  ('true')
                    //     router.go({ name: 'staff-documents'})
                    // }
                    // else{
                    //     //  ('false')
                    //     router.go({ name: 'staff-documents-folder', params: { id: cb } })
                    // }
                // }
                // For Prod
                // router.go({
                //     name: `staff-documents`
                // })
            }, response => {
                if(response.status == '504') {
                    let el = document.getElementById('progressLinear'),
                            elChild = document.createElement('span'),
                            progressPercent = document.getElementById('progressPercent');

                    // Give the new div some content
                    elChild.setAttribute('id', 'progressPercent');
                    // Jug it into the parent element
                    if (progressPercent) {
                        progressPercent.parentNode.removeChild(progressPercent);
                        el.appendChild(elChild);
                    } else {
                        el.appendChild(elChild);
                    }
                    let vm = context;
                    let prog = setInterval(() => {
                        elChild.innerHTML = vm.progress+' %';
                        if (vm.convertSuccess) {
                            console.log('vm.convertSuccess ', vm.convertSuccess);
                            if (vm.progress == 100) {
                                vm.loading = false
                                vm.btnLoad = true
                                // vm.loading.submit = false
                                vm.uploadSuccess = true
                                vm.show.disable = true
                                vm.timeStatusInterval = 1000
                                clearInterval(prog)
                                if (!!vm.staff) {
                                    if(vm.uploadSuccess){
                                        if(vm.idStaff == 0){
                                            let urlname = (role == 'company_user') ? 'company_user-documents' : 'staff-documents'
                                            router.go({ name: urlname})
                                            vm.idStaff = vm.idStaff
                                        }else{
                                            let urlname = (vm.role == 'company_user') ? 'company_user-documents-folder' : 'staff-documents-folder'
                                            router.go({ name: urlname, params: { id: vm.idStaff } })
                                        }
                                    }
                                }
                            } else {
                                if (vm.convertSuccess && vm.progress != 0){
                                    vm.progress += 1;
                                }
                            }
                        } else {
                            if (vm.progress == 100){ vm.convertSuccess = true; }else{ vm.progress += 1; }
                        }
                    }, 200);
                } else {
            
                    ccontext.error = true;
                    if (response.body.message == "入力内容に不備があります") {
                        context.show.disable = false;
                        document.getElementById('progressLinear').style.display = "none";
                        removeLoadingBtn(context.$els.submitBtn)
                        handleErrorRequest(context,response)
                        context.error_message += "<br />";
                    } else {
                        context.serverError = true;
                        // context.loading.submit =  false
                        context.convertSuccess = false
                        let determinateProgress = document.querySelector(".ui-progress-linear-determinate");
                        let progressError = document.getElementById("progressLinear");
                        progressError.style.background = "red";
                        determinateProgress.style.background = "#de2828";
                        
                        let errorStats = {'status':response.status,'ErrorMsgFromPage': response.body.message}
                        let functionName = 'Convert Document in Staff';
                        this.checkError(errorStats, functionName);
                    }
                }
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding the document'}
            let functionName = 'AssetsStaff:addDocument';
            this.checkError(errorStats, functionName);
        }
    },
    getDocumentsByFolder(context, params,cb){
        try {
            Vue.http.get(
                staff_service.STAFF_GET_FOLDER_CONTENT,
                { params }
            ).then(response => {
                //  ('get Documents By Folder')
                //  (response)
                context.error = false
                if(typeof cb == 'function') {
                    cb(response)
                }
                // context.documents = response.data.documents
            }, response => {
                // context.error_message = response.data.message
                // context.error = true
                //this.checkError(response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the documents by folder'}
            let functionName = 'AssetsStaff:getDocumentsByFolder';
            this.checkError(errorStats, functionName);
        }
    },
    getDocuments(context, params, cb) {
        try {
            Vue.http.get(
                // staff_service.STAFF_DOCUMENTS_RESOURCE,
                staff_service.STAFF_ALL_DOCUMENTS,
                { params }
            ).then(response => {
                //  ('get Documents')
                //  (response)
                context.error = false
                // context.documents = response.data.documents
                if(typeof cb == 'function') {
                    cb(response)
                }
            }, response => {
                context.error_message = response.data.message
                context.error = true
                // False Alarm
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the documents'}
            let functionName = 'AssetsStaff:getDocuments';
            this.checkError(errorStats, functionName);
        }
    },
    getDocument(context, routeparams) {
        try {
            Vue.http.get(
                `${staff_service.STAFF_DOCUMENTS_RESOURCE}/${routeparams.id}`
            ).then(response => {
                //  ('get Document')
                //  (response)
                // console.log('getDocument')
                try {
                    context.error = false
                    let document = response.data.documents[0];
                    document.allow_download = !!document.allow_download
                    context.document = document
                    context.docpages = document.pages
                    context.sortPages()
                    context.dataloaded = true
                } catch(e) {
                    router.go({
                        name: `staff-documents`
                    })
                }
            }, response => {
                router.go({
                    name: `staff-documents`
                })
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the specific document'}
            let functionName = 'AssetsStaff:getDocument';
            this.checkError(errorStats, functionName);
        }
    },
    updateDocument(context, routeparamas, params) {
        try {
            Vue.http.post(
                `${staff_service.STAFF_DOCUMENTS_RESOURCE}/${routeparamas.id}`,
                params
            ).then(response => {
                context.error = false
                context.$root.$broadcast('ui-snackbar::create', {
                    message: 'ファイル更新が完了しました',
                    duration: 5000
                })

                context.loading.submit =  false
                removeLoadingBtn(context.$els.submitBtn)
                router.go({
                    name: `staff-documents`
                })
            }, response => {
                context.error = true
                context.loading.submit =  false
                removeLoadingBtn(context.$els.submitBtn)
                handleErrorRequest(context,response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the document'}
            let functionName = 'AssetsStaff:updateDocument';
            this.checkError(errorStats, functionName);
        }
    },
    deleteDirectDocument(context, routeparams, callback) {
        try {
            Vue.http.delete(
                `${staff_service.STAFF_DOCUMENTS_RESOURCE}/${routeparams.id}`
            ).then(response => {
                context.error = false
                if(typeof callback == 'function') {
                    callback()
                }
                router.go({
                    name: `staff-dashboard`
                })
            }, response => {
                context.error_message = response.data.message
                context.error = true
                //this.checkError(response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Deleting directly the document'}
            let functionName = 'AssetsStaff:deleteDirectDocument';
            this.checkError(errorStats, functionName);
        }
    },
    deleteDocument(context, routeparams, callback) {
        try {
            Vue.http.delete(
                `${staff_service.STAFF_DOCUMENTS_RESOURCE}/${routeparams.id}`
            ).then(response => {
                context.error = false
                if(typeof callback == 'function') {
                    callback()
                }
                router.go({
                    name: `staff-documents`
                })
            }, response => {
                context.error_message = response.data.message
                context.error = true
                //this.checkError(response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Deleting the document'}
            let functionName = 'AssetsStaff:moveDocToFolder';
            this.checkError(errorStats, functionName);
        }
    },
    deleteDocumentFromStorage(params){
        try {
            console.log('params')
            console.log(params)
            Vue.http.post(
            staff_service.STAFF_DELETE_FROM_STORAGE,
                params
                // `${staff_service.STAFF_DELETE_FROM_STORAGE}/${params}`
            ).then(response => {
                console.log('deleting from storage')
                console.log(response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Deleting document from storage'}
            let functionName = 'AssetsStaff:deleteDocumentFromStorage';
            this.checkError(errorStats, functionName);
        }
    },
    addNegotation(context, params) {
        try {
            Vue.http.post(
                staff_service.STAFF_NEGOTATIONS_RESOURCE,
                params
            ).then(response => {
                context.negotation = response.data.negotation
            }, response => { })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding a negotation'}
            let functionName = 'AssetsStaff:addNegotation';
            this.checkError(errorStats, functionName);
        }
    },
    getNegotations(context, params) {
        try {
            Vue.http.get(
                staff_service.STAFF_NEGOTATIONS_RESOURCE,
                params
            ).then(response => {
                context.error = false
                context.negotations = response.data.negotations
            }, response => {
                context.error_message = response.data.message
                context.error = true
                //this.checkError(response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the negotations'}
            let functionName = 'AssetsStaff:getNegotations';
            this.checkError(errorStats, functionName);
        }
    },
    getNegotation(context, routeparams) {
        try {
            Vue.http.get(
                `${staff_service.STAFF_NEGOTATIONS_RESOURCE}/${routeparams.id}`
            ).then(response => {
                context.error = false
                context.negotation = negotation
                context.dataloaded = true
            }, response => {
                router.go({
                    name: `staff-negotation-history`
                })
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the specific negotation'}
            let functionName = 'AssetsStaff:getNegotation';
            this.checkError(errorStats, functionName);
        }
    },
    updateNegotation(context, routeparamas, params) {
        try {
            Vue.http.post(
                `${staff_service.STAFF_NEGOTATIONS_RESOURCE}/${routeparamas.id}`,
                params
            ).then(response => {
                console.log(response)
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the specific negotation'}
            let functionName = 'AssetsStaff:updateNegotation';
            this.checkError(errorStats, functionName);
        }
    },
    getNotices(context, params) {
        try {
            Vue.http.get(
                staff_service.STAFF_NOTICES_RESOURCE,
                params
            ).then(response => {
                context.error = false
                context.notices = response.data.notices
                context.dataloaded = true

            }, response => {
                context.error_message = response.data.message
                context.error = true
                //this.checkError(response)
            }).catch(e => {
                //  (e);
            });
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the notices'}
            let functionName = 'AssetsStaff:getNotices';
            this.checkError(errorStats, functionName);
        }
    },
    getNotice(context, routeparams) {
        try {
            Vue.http.get(
                `${staff_service.STAFF_NOTICES_RESOURCE}/${routeparams.id}`
            ).then(response => {
                context.error = false
                context.notice = notice
                context.dataloaded = true
            }, response => {
                router.go({
                    name: `staff-notices`
                })
            }).catch(e => {
                //  (e);
            });
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the notices'}
            let functionName = 'AssetsStaff:getNotice';
            this.checkError(errorStats, functionName);
        }
    },
    // m2b-81
    getAudioScreenNames(params){        
        // console.log('getAudioScreenNames params connect_id: ', params.get('connect_id'));   
        // console.log('staff_service.STAFF_AUDIO_SCREEN: ', staff_service.STAFF_GET_AUDIO_SCREEN_NAMES);     
        return Vue.http.post(
            `${staff_service.STAFF_GET_AUDIO_SCREEN_NAMES}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            // console.log('getAudioScreenNames response: ', response);
            return response;
        });
    },
    createScreenFiles(params){
        return Vue.http.post(
            `${staff_service.STAFF_CREATE_SCREEN_FILES}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            return response;
        });
    },  
    createAudioFiles(params){
        return Vue.http.post(
            `${staff_service.STAFF_CREATE_AUDIO_FILES}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            return response;
        });
    },
    mergeAudioScreenFiles(params){
        return Vue.http.post(
            `${staff_service.STAFF_MERGE_AUDIO_SCREEN_FILES}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            return response;
        });
    },        
    checkVideoUrl(params){
        // console.log('checkVideoUrl params url: ', params.get('url'));
        return Vue.http.post(
            `${staff_service.STAFF_CHECK_VIDEO}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            // console.log('checkVideoUrl response: ', response);
            return response;
        });
    }, 
    decryptDatUrl(params){
        return Vue.http.post(
            `${staff_service.STAFF_DECRYPT_DAT}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            return response;
        });
    },   
    removeVideoFile(params){
        return Vue.http.post(
            `${staff_service.STAFF_REMOVE_VIDEO_FILE}`,
            params, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {
            return response;
        });
    },       
    fetchTotalDiskUsage(){
        return Vue.http.post(
            `${staff_service.STAFF_FETCH_TOTAL_DISK_USAGE}`,
            {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
        ).then(response => {

            return response;
        });
    },           
    // m2b-81    
}
