<template>
    <div class="protoDashboard">
        <!-- <admin-sidebar v-ref:sidebar></admin-sidebar> -->
        <staff-sidebar v-ref:sidebar ></staff-sidebar>
        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">

                <ui-alert @dismissed="error = false" type="error" :show="error">
                    {{ error_message }}
                </ui-alert>

                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h1>
                            <span>
                                {{ staff.profile.full_name }}
                            </span>
                            <ui-progress-circular :size="20" :stroke="6" :show="!dataloaded" color="primary"></ui-progress-circular>
                        </h1>
                        
                            <div class="avatarCon" v-el:avatar-upload>
                                <img class="avatarUpload imgResponsive" v-bind:src="getAvatar ? getAvatar : static_images.avatar" v-cloak>
                               
                            </div>
                            <div class="form-group">
                                <label class="profile-label">顧客名</label>
                                    <ul style="font-size:16px;">{{staff.profile.full_name}}</ul>
                              
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="profile-label">Eメール</label>
                                    <ul style="font-size:16px;">{{staff.profile.email}}</ul>
                              
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="profile-label">最終ログイン</label>
                                    <ul style="font-size:16px;">{{staff.profile.last_login ? moments(staff.profile.last_login) : '-----------------------------'}}</ul>
                              
                            </div>
                            <hr>
                            <div class="form-group">
                                 <label class="profile-label">接続一覧</label>

                                <ul>
                                    <li style="font-size: 16px; font-weight:400; line-height: 2" v-for="connections in staff.profile.connections" v-bind:key="connections.updated_at">
                                        {{moments(connections.updated_at)}}
                                    </li>
                                </ul>
                            </div>
                            <hr><br>
                            <div class="ui-button-group">
                                <ui-button type="normal" button-type="normal" v-link="{ name: 'analyze-action' }" color="default" icon="keyboard_arrow_left">戻る</ui-button>
                            </div>
                            
                            
                        <!-- </form> -->
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
// import AdminSidebar from '../partials/AdminSidebar.vue';
import StaffSidebar from '../../staff/partials/StaffSidebar.vue';
import FilePreview from '../../common/FilePreview.vue';
import {APP_URL} from '../../../js/config.js';
import admin from '../../../js/admin.js';
import helper from '../../../js/helpers.js';
import moment from 'moment';

export default {
    data() 
    {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            staff: admin.data.staff,
            avatar: '',
            error: false,
            error_message: null,
            loading: {
                avatar_upload: false
            },
            dataloaded: false
        }
    },
    methods:{
        moments(date){
            try {
                moment.locale('ja');
                return moment(new Date(date)).format(' YYYY年M月D日 HH時mm分ss秒');
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Returns the formmatted date'}
                let functionName = 'StaffProfile:moments';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    computed: {
        getAvatar() {
            try {
                if(this.staff.profile.pic_url) {
                    console.log('pasok')
                    return this.staff.profile.pic_url
                }

                return this.static_images.avatar
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the user avatar'}
                let functionName = 'StaffProfile:getAvatar';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    created() {
        try {
            // this.staff.profile.current_password = ''
            this.staff.profile.password = ''
            this.staff.profile.password_confirmation = ''
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created the profile component'}
            let functionName = 'StaffProfile:created';
            helper.catchError(errorStats, functionName);
        }
    },
    ready() {
        try {
            let config = {
                role: 'admin',
                showToolbar: true
            }
            this.$dispatch('component-ready', config)
            admin.getStaff(this, this.$route.params)

            // ON DEV CONSOLE COMMENT THIS IF IN DEVELOPMENT
            window.onerror = function(message, url, lineNumber) {  
                // code to execute on an error
                return true; // prevents browser error messages
            };

            console.log = function(){};
            // END DEV CONSOLE
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the staff profile component'}
            let functionName = 'StaffProfile:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        // AdminSidebar,
        StaffSidebar,
        FilePreview
    }
}
</script>

<style lang="scss">
    .profile-label{
        font-size: 18px; 
        font-weight:bold; 
        color: #333333
    }
    .conlist {
        padding:0;
    }
    .form-group .conlist li{
            font-family: 'NotoSans-Regular';
    color: rgba(0, 0, 0, 0.87);
    background-color: #E6E6E6;
    border-bottom-color: rgba(0, 0, 0, 0);
        padding-left: 10px;
    padding-right: 10px;
    // margin-bottom: 10px;
    list-style-type: none;
        padding: 10px;
    }

    .dashboardContent{
        .ui-textbox-input, .ui-textbox-textarea {
            color: rgba(0, 0, 0, 0.87) !important;
        }

        .proto .ui-textbox-label-text {
            font-size: 13px;
        }

        .ui-textbox-label-text {
            font-weight: bold;
        }
    }


</style>
