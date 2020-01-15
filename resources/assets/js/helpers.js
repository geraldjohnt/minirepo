//helpers
import moment from 'moment';
import errorLogs from './error_logs';
import browser from './browser_detect.js';
import {APP_URL} from './config.js';
import Vue, {router} from './app.js';

export const handleErrorRequest = (context, response, msg_key) => {
    try {
        let error_msg = response.data.message

        //blur inputs
        for(let key in context.$refs) {
            if(key.substr(0, 5) == 'input') {
                context.$refs[key].blurred()
            }
        }

        //append detail error messages
        if(response.data.errors) {
            for(let key in response.data.errors) {
                for(let in_key in response.data.errors[key]) {
                    error_msg += '<br\> '+ response.data.errors[key][in_key]
                    let key_capd = context.$options.filters.capitalize( key )

                    if(typeof context.$refs[`input${key_capd}`] != 'undefined') {
                        context.$broadcast('ui-input::set-validity', false, response.data.errors[key][in_key], key)
                        context.$refs[`input${key_capd}`].validationError = response.data.errors[key][in_key]
                    }
                }
            }
        }

        if(typeof msg_key == 'undefined') {
            context.error_message = error_msg
        } else {
            context[msg_key].error_message = error_msg
        }
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Handling the error request'}
        let functionName = 'Helpers:handleErrorRequest';
        this.catchError(errorStats, functionName);
    }
}

export const getFrameTargetElement =  (oI) => {
    try {
        /* get target document element based on provided frame element */

        var lF = oI.contentWindow;
        if(window.pageYOffset==undefined) {
            //IE
            lF= (lF.document.documentElement) ? lF.document.documentElement : lF=document.body;
        }
        //- return computed value
        return lF;
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the frame target element'}
        let functionName = 'Helpers:getFrameTargetElement';
        this.catchError(errorStats, functionName);
    }
}

export const getParentElementByClass = (s_parent_name, el) => {
    try {
        let o_parent = el.parentNode
        while(!hasClass(o_parent, s_parent_name)) {
            o_parent = o_parent.parentNode;
            if(!o_parent) return null
        }
        return o_parent
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the parent element by class'}
        let functionName = 'Helpers:getParentElementByClass';
        this.catchError(errorStats, functionName);
    }
}

function formatDate(date, format) {
    try {
        date = moment(date).format(format)
        return date
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Date Formatter'}
        let functionName = 'Helpers:formatDate';
        this.catchError(errorStats, functionName);
    }
}

export const objectToQueryString = (obj, a_keys) => {
    try {
        let qstring = ''
        if(typeof obj == 'object') {
            Object.entries(obj).forEach(([key,val]) => {
                let can_append = true
                if(Array.isArray(a_keys)) {
                    if(a_keys.indexOf(key) == -1) {
                        can_append = false
                    }
                }
                if(can_append) {
                    let symbol = (!qstring) ? '?' : '&'
                    qstring += `${symbol}${key}=${val}`
                }
            })
            return qstring
        }
        return
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Converting object to query strings'}
        let functionName = 'Helpers:objectToQueryString';
        this.catchError(errorStats, functionName);
    }
}

//accepts ms
function formatDuration(duration) {
    try {
        duration = duration * 1000
        let seconds = moment.duration(duration).seconds()
        let minutes = moment.duration(duration).minutes()
        // let hours = Math.trunc(moment.duration(duration).asHours())
        /** Start support for IE */
        let hoursHolder = moment.duration(duration).asHours()
        let hours = hoursHolder < 0 ? Math.ceil(hoursHolder) : Math.floor(hoursHolder);
        /** End support for IE */
        return `${hours}:${minutes}:${seconds}`
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Formatting the duration'}
        let functionName = 'Helpers:formatDuration';
        this.catchError(errorStats, functionName);
    }
}

function hasClass(el, cls) {
    try {
        if(el) {
            return (' ' + el.className + ' ').indexOf(' ' + cls + ' ') > -1;
        }
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Identifying if the element has class'}
        let functionName = 'Helpers:hasClass';
        this.catchError(errorStats, functionName);
    }
}

function downloadFile(fileLink) {
    try {
        var element = document.createElement('a');
        element.setAttribute('target', '_blank');
        element.setAttribute('href', fileLink);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Downloading the file'}
        let functionName = 'Helpers:downloadFile';
        this.catchError(errorStats, functionName);
    }
}

function downloadNotes(downloadLink) {
    try {
        var element = document.createElement('a');
        element.setAttribute('target', '_blank');
        element.setAttribute('href', downloadLink);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Downloading the notes'}
        let functionName = 'Helpers:downloadNotes';
        this.catchError(errorStats, functionName);
    }
}

function catchError(errorStatus, fn) {
    let IEbrowser = browser.detectIE()
    let browser_select = ''
    browser_select = (IEbrowser.trigger) ? {'browser':IEbrowser.browser, 'version':IEbrowser.version } : 
                        { 'browser':browser.specs().browser.name, 'version':browser.specs().browser.version };
    let returnVal = "false";
    for (let e in errorLogs.error){
        if (errorLogs.error[e].code == errorStatus.status){
            let errorMsg = {
                'client' : true,
                'statusPageMsg' : errorStatus.ErrorMsgFromPage,
                'statusMsg' : errorStatus.ErrorTryCatchMsg ? errorStatus.ErrorTryCatchMsg.message : errorLogs.error[e].msg,
                'statusCode' : errorLogs.error[e].code,
                'statusText' : errorLogs.error[e].status,
                'specsOS': browser.specs().os.name,
                'specsOSversion': browser.specs().os.version,
                'specsBrowser': browser_select.browser,
                'specsBrowserversion': browser_select.version,
                'location' : fn,
            }
            
            let urlRoute = APP_URL+'/clientslack';
            Vue.http.post(urlRoute,
                JSON.stringify(errorMsg)
            ).then(response=>{ 
                // vm.errorCompatibility = true; 
                console.log("response")
                console.log(response)
                returnVal = "true";
            },error => {
                console.log("error")
            })
        }
    }
    return returnVal;
}

function progressConvertInterval(interval, vm, size) {
    try {
        // let size = vm.clientFileSize
        console.log(size+" SIZE")
        let splitsize = size.split(" ");
        // let progressInterval = false
        // console.log(splitsize[1])

        if (vm.progress == 0) {
            if (splitsize[1] == 'MB') {
                if (splitsize[0] >= 10) {
                    interval = 15000
                } else {  interval = 10000 }
            } else if  (splitsize[1] == 'KB') {
                // alert(splitsize[1] == 'KB')
                if (splitsize[0] >= 500) {
                    interval = 3000
                    // alert(interval+" interval")
                } else { interval = 1500 }
            }
        } else {
            // alert(vm.progress+" PROGRESS")
            interval = interval
        }
        console.log('interval = interval ', interval);
        let el = document.getElementById('progressLinear'),
            elChild = document.createElement('span'),
            progressPercent = document.getElementById('progressPercent');

        // Give the new div some content
        elChild.setAttribute('id', 'progressPercent');
        // Jug it into the parent element
        // console.log(progressPercent)
        // console.log(progressPercent)
        if (progressPercent) {
            progressPercent.parentNode.removeChild(progressPercent);
            el.appendChild(elChild);
            // alert("DELETE")
        } else {
            el.appendChild(elChild);
            // alert("ADD")
        }
    console.log('after vm.progress');
        vm.idStaff = vm.$route.params.id
    console.log('vm.idStaff');
        let prog = setInterval(() => {
            elChild.innerHTML = vm.progress+' %';
            console.log('elChild.innerHTML ', elChild.innerHTML);
            if (vm.convertSuccess) {
                clearInterval(prog);
                fastInterval(vm, elChild, "staff");
            } else if(!!vm.errorConvert || !!vm.error) {
                // document.getElementById('progressPercent').style.display = 'none'
                console.log('vm.error ', vm.error);
            } else {
                if (vm.progress == 95){ }else{ vm.progress += 1; }
                console.log('vm.progress ctr ', vm.progress);
            }
        }, interval);
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Counting the progress conversion interval'}
        let functionName = 'Helpers:handleErrorRequest';
        this.catchError(errorStats, functionName);
    }
}

function fastInterval(vm, elChild, role) {
    let prog = setInterval(function() {
      elChild.innerHTML = vm.progress+' %';
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
              let urlname = (role == 'company_user') ? 'company_user-documents-folder' : 'staff-documents-folder'
              router.go({ name: urlname, params: { id: vm.idStaff } })
            }
          }
        }
      } else {
        vm.progress += 1;
      }
    }, 100);
}

/**
 * Returns a 12 random generated characters which consists of lowercase, uppercase, numbers, and symbols.
 */
function generatePassword() {
    try {
        let uppercaseLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let lowercaseLetters = 'abcdefghijklmnopqrstuvwxyz';
        let numbers = '0123456789';
        let symbols = '!@#$%^&*?><';
        let tempPassword = '';

        for (var i = 0; i < 3; i++){
            tempPassword += uppercaseLetters.charAt(Math.floor(Math.random() * uppercaseLetters.length));
            tempPassword += lowercaseLetters.charAt(Math.floor(Math.random() * lowercaseLetters.length));
            tempPassword += numbers.charAt(Math.floor(Math.random() * numbers.length));
            tempPassword += symbols.charAt(Math.floor(Math.random() * symbols.length));
        }

        return tempPassword;
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Generating the password'}
        let functionName = 'Helpers:generatePassword';
        this.catchError(errorStats, functionName);
    }
}

//remove loading UiButton
export const removeLoadingBtn = (el) => {
    try {
        el.children[1].style.display = 'none'
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Removing the loading button'}
        let functionName = 'Helpers:removeLoadingBtn';
        this.catchError(errorStats, functionName);
    }
}

//peerjs helpers
export const getPeerKey = (prefix) => {
    try {
        let s_prefix = typeof prefix != 'undefined' ? prefix : ''
        let random_key = (Math.floor(10000 + Math.random() * 90000))
        return `${s_prefix}${random_key}`
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the peerkey'}
        let functionName = 'Helpers:getPeerKey';
        this.catchError(errorStats, functionName);
    }
}

export const getPeerOptions = (peer_host, api_key, peer_config) => {
    try {
        if(peer_host) {
            return {
                host: peer_host,
                port: '',
                secure: true,
                debug: 1,
                config: peer_config
            }
        }

        return {
            key: api_key,
            secure: true,
            debug: 1,
        }
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting peer options'}
        let functionName = 'Helpers:getPeerOptions';
        this.catchError(errorStats, functionName);
    }
}

export const attachMediaStream_ = (videoDom,stream) => {
    try {
        // Adapter.jsをインクルードしている場合はそちらのFunctionを利用する
        videoDom.srcObject = null;
        if(typeof (attachMediaStream) !== 'undefined' && attachMediaStream){
            console.log('inside attachMediaStream')
            videoDom = attachMediaStream(videoDom,stream);
        }else{
            // setTimeout(function(){
            videoDom.srcObject = null;
            videoDom.srcObject = stream;
            // },1000)
        // videoDom.setAttribute('src', URL.createObjectURL(stream));
        }
    } catch(e) {
        let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Attaching the media stream'}
        let functionName = 'Helpers:attachMediaStream_';
        this.catchError(errorStats, functionName);
    }
}

export default {
    handleErrorRequest,
    getFrameTargetElement,
    getParentElementByClass,
    formatDate,
    objectToQueryString,
    formatDuration,
    hasClass,
    downloadFile,
    downloadNotes,
    removeLoadingBtn,
    catchError,
    progressConvertInterval,
    generatePassword,
    data: {
        idStaff:'', // For progressConvertInterval Upload id Session
    },
};
