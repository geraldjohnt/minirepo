import Vue, {router} from './app.js';
import {profile,notice,staff} from './config.js';
import {ADMIN_RESOURCE, ADMIN_MANAGE_STAFF_RESOURCE, ADMIN_NOTICES_RESOURCE, ADMIN_EMAIL_CREDENTIALS} from './api_routes.js';
import {handleErrorRequest} from '../js/helpers.js';

export default {
    data: {
        profile,
        notice,
        staff
    },
    update(context, params, callback) {
        Vue.http.post(
            ADMIN_RESOURCE,
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
                name: `admin-dashboard`
            })
            
        }, response => {
            handleErrorRequest(context,response)
            context.error = true
        })
    },
    registerStaff(context, params) {
        Vue.http.post(
            ADMIN_MANAGE_STAFF_RESOURCE,
            params
        ).then(response => {
            context.error = false

            context.$root.$broadcast('ui-snackbar::create', {
                message: '登録が完了しました',
                duration: 5000
            })

            Vue.http.post(
                ADMIN_EMAIL_CREDENTIALS,
                params
            ).then(emailResponse => {
                /** Done Sending Email */
            }, emailResponse => {
                /** Fail Sending Email */
            })

            router.go({
                name: `manage-staff`
            })
        }, response => {
            context.error_message = response.data.message
            context.toggleValidation()
            handleErrorRequest(context,response)
            context.error = true
        })
    },
    updateStaff(context, routeparams, params) {
        Vue.http.post(
            `${ADMIN_MANAGE_STAFF_RESOURCE}/${routeparams.id}`,
            params
        ).then(response => {
            context.error = false

            context.$root.$broadcast('ui-snackbar::create', {
                message: '更新が完了しました',
                duration: 5000
            })

            router.go({
                name: `manage-staff`
            })
        }, response => {
            context.error_message = response.data.message
            context.toggleValidation()
            handleErrorRequest(context,response)
            context.error = true
        })
    },
    getStaff(context, routeparams) {
        Vue.http.get(
            `${ADMIN_MANAGE_STAFF_RESOURCE}/${routeparams.id}`
        ).then(response => {
            context.error = false
            context.staff = response.data.staff
            context.dataloaded = true

        }, response => {
            router.go({
                name: `manage-staff`
            })
        })
    },
    deleteStaff(context, routeparams, callback) {
        //Vue.http.delete(
        //    `${ADMIN_MANAGE_STAFF_RESOURCE}/${routeparams.id}`
        Vue.http.get(
            `/api/admin/staff/getProcess?staff=${routeparams.id}`
        ).then(response => {
            context.error = false

            context.$root.$broadcast('ui-snackbar::create', {
                message: '更新が完了しました',
                duration: 5000
            })

            if(typeof callback == 'function') {
                callback()
            }
        }, response => {
            context.error_message = response.data.message
            context.error = true
        })
    },
    addNotice(context, params) {
        Vue.http.post(
            ADMIN_NOTICES_RESOURCE,
            params
        ).then(response => {
            context.error = false

            router.go({
                name: `manage-notices`
            })
        }, response => {
            context.error_message = response.data.message
            context.toggleValidation()
            context.error = true
        })
    },
    updateNotice(context, routeparams, params) {
        Vue.http.post(
            `${ADMIN_NOTICES_RESOURCE}/${routeparams.id}`,
            params
        ).then(response => {
            context.error = false

            router.go({
                name: `manage-notices`
            })
        }, response => {
            context.error_message = response.data.message
            context.toggleValidation()
            context.error = true
        })
    },
    getNotice(context, routeparams) {
        Vue.http.get(
            `${ADMIN_NOTICES_RESOURCE}/${routeparams.id}`
        ).then(response => {
            context.error = false
            let notice = response.data.notice
            notice.broadcast = !!response.data.notice.broadcast
            context.notice = notice
            context.dataloaded = true
        }, response => {
            router.go({
                name: `manage-notices`
            })
        })
    },
    deleteNotice(context, routeparams, callback) {
        Vue.http.delete(
            `${ADMIN_NOTICES_RESOURCE}/${routeparams.id}`
        ).then(response => {
            context.error = false

            if(typeof callback == 'function') {
                callback()
            }
        }, response => {
            context.error_message = response.data.message
            context.error = true
        })
    }
}