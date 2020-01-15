import Vue, {router} from './app.js';
import helper from './helpers.js';
import {profile} from './config.js';
import {user_service} from './api_routes.js';
import Cookie from 'js-cookie';

export default {
    user: {
        authenticated: false,
        profile,
        role: null
    },
    check(role, callback) {
        try {
            if (role == 'guest' && !Cookie.get('remember_token')) {
                return
            }

            // let staff_token = localStorage.getItem("token")
            // console.log('Staff Token from Local Storage: ' +staff_token)
            // if(staff_token){
            //     this.setToken(staff_token)
            // }
            // console.log('Cookie ID TOKEN')
            // console.log(Cookie.get('id_token'))
            // console.log(Cookie.get('kent'))
            if (Cookie.get('id_token') != null) {
                // console.log('pasok may token')
                // console.log(Vue.http.headers.common['Authorization'])
                Vue.http.get(
                    user_service.USER_RESOURCE,
                ).then(response => {
                    console.log(response)
                    this.user.authenticated = true
                    this.user.profile = response.data.user
                    this.user.role = response.data.role

                    if(typeof callback == 'function') {
                        callback()
                    }

                    if(!!response.data.admin){
                        return
                    }
                    if (
                            typeof this.user.role != 'undefined' &&
                            typeof role != 'undefined' &&
                            role != this.user.role
                        )
                    {
                        router.go({
                            name: `${this.user.role}-dashboard`
                        })
                    }
                }, response => {
                    // console.log('second response sa user')
                    // console.log(Cookie.get('id_token'))
                    if (Cookie.get('remember_token') != null || Cookie.get('id_token') != null ) {
                        // console.log('refreshing token')
                        this.refreshToken()
                    } else {
                        // console.log('signout nakooo')
                        this.signout()
                    }
                })
                return
            }

            router.go({ name: 'home' })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking of the user sources'}
            let functionName = 'Auth:check';
            helper.catchError(errorStats, functionName);
        }
    },
    signin(context, email, password, remember_me) {
        try {
            Vue.http.post(
                user_service.USER_AUTHENTICATE,
                { email, password }
            ).then(response => {
                context.error = false
                this.setToken(response.data.token)

                this.user.authenticated = true
                this.user.profile = response.data.user
                this.user.role = response.data.role

                if(remember_me) {
                    Cookie.set('remember_token', true)
                }

                router.go({
                    name: `${this.user.role}-dashboard`
                })
            }, response => {
                context.error_message = response.data.message
                context.error = true
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Signing into the system'}
            let functionName = 'Auth:signin';
            helper.catchError(errorStats, functionName);
        }
    },
    signout() {
        try {
            Cookie.remove('remember_token')
            Cookie.remove('id_token')
            this.user.authenticated = false
            this.user.profile = profile
            this.user.role = null

            router.go({
                name: 'home'
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Signout from the system'}
            let functionName = 'Auth:signout';
            helper.catchError(errorStats, functionName);
        }
    },
    forgotPass(context, email) {
        try {
            Vue.http.get(
                user_service.USER_FORGOT_PASSWORD,
                {
                    params: {
                        email
                    }
                }
            ).then(response => {
                context.success_message = response.data.message
                context.success = true
                context.error = false
            }, response => {
                context.error_message = response.data.message
                context.success = false
                context.error = true
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Forgot Password'}
            let functionName = 'Auth:forgotPass';
            helper.catchError(errorStats, functionName);
        }
    },
    resetPass(context, password, password_confirmation, token, action) {
        try {
            Vue.http.post(
                user_service.USER_RESET_PASSWORD,
                { password, password_confirmation, token, action }
            ).then(response => {
                if(action == 'validate') return
                context.success_message = response.data.message
                context.success = true
                context.error = false

                setTimeout(function(){
                    router.go({ name: 'home' })
                }, 3000)
            }, response => {
                if(action == 'validate') {
                    router.go({ name: 'home' })
                    return
                }

                context.error_message = response.data.message
                context.success = false
                context.error = true
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Resetting the password'}
            let functionName = 'Auth:resetPass';
            helper.catchError(errorStats, functionName);
        }
    },
    refreshToken() {
        try {
            // console.log('refreshToken')
            Vue.http.get(
                user_service.USER_GET_TOKEN
            ).then(response => {
                this.setToken(response.data.refreshedToken)
            }, response => {
                router.go({ name: 'home' })
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Refreshing the token'}
            let functionName = 'Auth:refreshToken';
            helper.catchError(errorStats, functionName);
        }
    },
    setToken(token) {
        try {
            Cookie.set('id_token', token)
            // console.log('passed Token: '+ token)
            // console.log('Cookie Token:' + Cookie.get('id_token'))
            // console.log('LocalStorage Token: '+ localStorage.getItem("token"))
            // if(token != localStorage.getItem("token")){
                // localStorage.setItem("token", token);
            // }else{
                // console.log('equal ra sila kyaah')
            // }
            Vue.http.headers.common['Authorization'] = 'Bearer ' + token
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting the token'}
            let functionName = 'Auth:setToken';
            helper.catchError(errorStats, functionName);
        }
    }
}