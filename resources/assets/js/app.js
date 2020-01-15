import Vue from 'vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource';
import KeenUI from 'keen-ui';
import Cookie from 'js-cookie';

import {APP_ENV,APP_URL} from './config.js';
import App from '../components/App.vue';
import Signin from '../components/Signin.vue';
import ForgotPassword from '../components/ForgotPassword.vue';
import ResetPassword from '../components/ResetPassword.vue';
import ClientConnect from '../components/ClientConnect.vue';
import SecondClientConnect from '../components/SecondClientConnect.vue';
import ClientConnectFallback from '../components/ClientConnectFallback.vue';

import AdminSignin from '../components/admin/Signin.vue';
import AdminDashboard from '../components/admin/Dashboard.vue';
import AdminEditAccount from '../components/admin/EditAccount.vue';

import AdminManageStaff from '../components/admin/ManageStaff.vue';
import AdminRegisterStaff from '../components/admin/staff/Register.vue';
import AdminEditStaff from '../components/admin/staff/Edit.vue';
import AdminStaffProfile from '../components/admin/staff/Profile.vue';

import AdminManageNotices from '../components/admin/ManageNotices.vue';
import AdminAnalyzeAction from '../components/admin/AnalyzeAction.vue';
import AdminAddNotice from '../components/admin/notices/Add.vue';
import AdminEditNotice from '../components/admin/notices/Edit.vue';

import StaffSignin from '../components/staff/Signin.vue';
import StaffDashboard from '../components/staff/Dashboard.vue';
import StaffConnect from '../components/staff/Connect.vue';
import StaffConnectFallback from '../components/staff/ConnectFallback.vue';
import StaffEditAccount from '../components/staff/EditAccount.vue';

import StaffDocuments from '../components/staff/Documents.vue';
import StaffAddDocument from '../components/staff/documents/Add.vue';
import StaffEditDocument from '../components/staff/documents/Edit.vue';
import StaffDocumentFolder from '../components/staff/documents/Folder.vue';

import StaffNegotation from '../components/staff/Negotation.vue';
import StaffNotices from '../components/staff/Notices.vue';

import SalesMain from '../components/staff/SalesMain.vue';

import ThankYou from '../components/common/ThankYou.vue';
Vue.use(VueRouter);
Vue.use(VueResource);
Vue.use(KeenUI);

Vue.config.devtools = false;
Vue.config.debug = false;
Vue.config.productionTip = false;
Vue.config.silent = true;
Vue.http.headers.common['X-CSRF-TOKEN'] = document.getElementsByName('csrf-token')[0].getAttribute('content');
Vue.http.headers.common['Authorization'] = 'Bearer ' + Cookie.get('id_token');

Vue.http.options.root = APP_URL;

export default Vue;
export var router = new VueRouter({hashbang: false});

router.map({
    '/': {
        name: 'home',
        component: Signin
    },
    '/connect': {
        name: 'connect',
        component: ClientConnect
    },
    '/connect/:id': {
        name: 'connect',
        component: SecondClientConnect
    },
     '/connect/:id/:string': {
        name: 'connect',
        component: SecondClientConnect
    },
    // '/connect/fallback': {
    //     name: 'connect-fallback',
    //     component: ClientConnectFallback
    // },
    '/signin': {
        name: 'signin',
        component: Signin
    },
    '/forgot-password': {
        name: 'forgot-password',
        component: ForgotPassword
    },
    '/reset-password/:token': {
        name: 'reset-password',
        component: ResetPassword
    },

    '/admin/dashboard': {
        name: 'admin-dashboard',
        component: AdminDashboard
    },
    '/admin/edit-account': {
        name: 'admin-edit-account',
        component: AdminEditAccount
    },
    '/admin/manage-staff': {
        name: 'manage-staff',
        component: AdminManageStaff
    },
    '/admin/analyze': {
        name: 'analyze-action',
        component: AdminAnalyzeAction
    },
    '/admin/manage-staff/register': {
        name: 'admin-register-staff',
        component: AdminRegisterStaff
    },
    '/admin/manage-staff/edit/:id': {
        name: 'admin-edit-staff',
        component: AdminEditStaff
    },
    '/admin/staff/profile/:id':{
        name: 'admin-staff-profile',
        component: AdminStaffProfile
    },
    '/admin/manage-notices': {
        name: 'manage-notices',
        component: AdminManageNotices
    },
    '/admin/manage-notices/add': {
        name: 'admin-add-notice',
        component: AdminAddNotice
    },
    '/admin/manage-notices/edit/:id': {
        name: 'admin-edit-notice',
        component: AdminEditNotice
    },
    '/staff/dashboard': {
        name: 'staff-dashboard',
        component: StaffDashboard
    },
    '/staff/connect/:id': {
        name: 'staff-connect',
        component: StaffConnect
    },
    // '/staff/connect/fallback/:id': {
    //     name: 'staff-connect-fallback',
    //     component: StaffConnectFallback
    // },
    '/staff/edit-account': {
        name: 'staff-edit-account',
        component: StaffEditAccount
    },
    '/staff/documents': {
        name: 'staff-documents',
        component: StaffDocuments
    },
    '/staff/documents/folder/:id': {
        name: 'staff-documents-folder',
        component: StaffDocumentFolder
    },
    '/staff/documents/add/:id': {
        name: 'staff-add-document',
        component: StaffAddDocument,
    },
    '/staff/documents/edit/:id/:folder': {
        name: 'staff-edit-document',
        component: StaffEditDocument
    },
    '/staff/negotation-history': {
        name: 'staff-negotation-history',
        component: StaffNegotation
    },
    '/staff/notices': {
        name: 'staff-notices',
        component: StaffNotices
    },
     '/staff/sales': {
        name: 'sales-main',
        component: SalesMain
    },
    '/suggestion-page': {
        name: 'thank-you',
        component: ThankYou
    },
});

//404 page
router.redirect({
    '*': '/'
})

Vue.http.interceptors.push(function(request, next) {
    next(function(response) {
        if (response.status == 401 && response.body.message == 'Token has expired') {
            router.go({ name: 'home' })
        }
    })
})

router.start(App, '#app');
