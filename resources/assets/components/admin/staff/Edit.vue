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
                                Edit Staff
                            </span>
                            <ui-progress-circular :size="20" :stroke="6" :show="!dataloaded" color="primary"></ui-progress-circular>
                        </h1>
                        <form class="protoForm" autocomplete="off" v-if="dataloaded" @submit.prevent="updateStaff">
                            <div class="avatarCon" v-el:avatar-upload>
                                <img class="avatarUpload imgResponsive" v-bind:src="getAvatar" v-cloak>
                                <file-preview name="avatar" id="avatar" @changed="changedAvatar" v-ref:image-upload></file-preview>
                                <div class="overlay">
                                    <ui-icon icon="add_a_photo"></ui-icon>
                                </div>
                                <ui-ripple-ink :trigger="$els.avatarUpload"></ui-ripple-ink>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="First Name" name="first_name" id="first_name" type="text" placeholder="Enter your first name" :value.sync="staff.profile.first_name" v-ref:input-first-name validation-rules="required"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="担当者名" name="last_name" id="last_name" type="text" placeholder="名前を入力してください" :value.sync="staff.profile.last_name" v-ref:input-last-name validation-rules="required"
                                ></ui-textbox>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="Title/Position" name="title" id="title" type="text" placeholder="Enter your title/position" :value.sync="staff.title" v-ref:input-title validation-rules="required"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="Email" name="email" id="email" type="text" placeholder="E-mailアドレスを入力してください" :value.sync="staff.profile.email" v-ref:input-email validation-rules="required|email" @change="thisChange"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="会社名" name="company" id="company" type="text" placeholder="会社名を入力してください" :value.sync="staff.profile.company"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="部署名" name="department" id="department" type="text" placeholder="部門を入力してください" :value.sync="staff.profile.department"
                                ></ui-textbox>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="Post" name="post" id="post" type="text" placeholder="Enter your post" :value.sync="staff.profile.post"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="電話番号" name="phone_number" id="phone_number" type="text" placeholder="電話番号を入力してください" :value.sync="staff.profile.phone_number"
                                ></ui-textbox>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="パスワード" name="current_password" id="current_password" type="current_password" placeholder="現在のパスワードを入力してください" :value.sync="staff.profile.current_password"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="パスワード" name="password" id="password_label" type="password" placeholder="新しいパスワードを入力してください" :value.sync="staff.profile.password"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="パスワード（確認）" name="password_confirmation" id="password_confirmation" type="password" placeholder="新しいパスワードを入力してください" :value.sync="staff.profile.password_confirmation"
                                ></ui-textbox>
                            </div>
                            <div class="additional-settings">
                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="ui-textbox-label-text">トライアル</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><span class="switch_off" style="{{style_off}}">OFF</span><span class="switch_on" style="{{style_on}}">ON</span></p>
                                            <ui-switch name="trial" :value.sync="trial_toggled"></ui-switch>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="additional-settings">
                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="ui-textbox-label-text">アクティブ</p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><span class="switch_off" style="{{style_off2}}">OFF</span><span class="switch_on" style="{{style_on2}}">ON</span></p>
                                            <ui-switch name="active" :value.sync="active_toggled"></ui-switch>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ui-button-group">
                                <ui-button type="normal" button-type="normal" v-link="{ name: 'manage-staff' }" color="default" icon="keyboard_arrow_left">戻る</ui-button>
                                <ui-button type="normal" button-type="submit" color="success">Update Staff</ui-button>
                            </div>
                        </form>
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
import UiSwitch from '../../common/UiSwitch.vue';
import {APP_URL} from '../../../js/config.js';
import helper from '../../../js/helpers.js';
import admin from '../../../js/admin.js';

export default {
    data() 
    {
        return {
            switch1: true,
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
            dataloaded: false,
            mytrialsw: true,
            trial_toggled: false,
            active_toggled: false,
            style_on:'color:black',
            style_off:'color:gray',
            style_on2:'color:black',
            style_off2:'color:gray',
        }
    },
    computed: {
        getAvatar() {
            try {
                if(this.staff.profile.pic_url) {
                    return this.staff.profile.pic_url
                }
                return this.static_images.avatar
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the Avatar'}
                let functionName = 'AdminStaffEdit:getAvatar';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    methods: {
        updateStaff() {
            try {
                let data = new FormData();
                //console.log(data);
                data.append('_method', 'PUT')
                for (let key in this.staff.profile ) {
                    data.append(key, this.staff.profile[key])
                }
                data.append('title', this.staff.title)
                data.append('avatar', this.avatar)

                admin.updateStaff(this, this.$route.params, data)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the staff'}
                let functionName = 'AdminStaffEdit:updateStaff';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleValidation() {
            try {
                // this.$refs.inputFirstName.blurred()
                this.$refs.inputLastName.blurred()
                // this.$refs.inputTitle.blurred()
                this.$refs.inputEmail.blurred()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the validation'}
                let functionName = 'AdminStaffEdit:toggleValidation';
                helper.catchError(errorStats, functionName);
            }
        },
        changedAvatar() {
            try {
                this.avatar = this.$refs.imageUpload.value
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Changing the avatar'}
                let functionName = 'AdminStaffEdit:changedAvatar';
                helper.catchError(errorStats, functionName);
            }
        },
        thisChange(){
            try {
                console.log('from change')
                console.log(this.staff.profile)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Testing just to display that the form is changed'}
                let functionName = 'AdminStaffEdit:thisChange';
                helper.catchError(errorStats, functionName);
            }
        },
        myClickEvent(){
            try {
                console.log(this.value);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Tracking the click event'}
                let functionName = 'AdminStaffEdit:myClickEvent';
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
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created the admin:staff edit'}
            let functionName = 'AdminStaffEdit:created';
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

            var element = document.getElementsByClassName("ui-switch");
            element.id="trialSwitch";
            var el = document.getElementById("trialSwitch");
            console.log(el);
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the admin: staff edit'}
            let functionName = 'AdminStaffEdit:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    watch: {
        staff: function(value) {
            try {
                this.trial_toggled = value.profile.t_checked
                this.active_toggled = parseInt(value.profile.active) == 1 ? true : false;
                console.log('this.active_toggled');
                console.log(this.active_toggled);
                if (value.profile.t_checked){
                    this.style_on = 'color:#000;'
                    this.style_off = 'color:#a3a3a3;'
                } else{
                    this.style_off = 'color:#000'
                    this.style_on = 'color:#a3a3a3;'
                }

                if(value.profile.active){
                    this.style_on2 = 'color:#000;'
                    this.style_off2 = 'color:#a3a3a3;'
                }else{
                    this.style_off2 = 'color:#000'
                    this.style_on2 = 'color:#a3a3a3;'
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Listening to the staff variable'}
                let functionName = 'AdminStaffEdit:staff';
                helper.catchError(errorStats, functionName);
            }
        },
        trial_toggled: function(value) {
            try {
                this.staff.profile.trial = value;
                if(value){
                    this.style_on = 'color:#000;'
                    this.style_off = 'color:#a3a3a3;'
                }else{
                    this.style_off = 'color:#000'
                    this.style_on = 'color:#a3a3a3;'
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the trial'}
                let functionName = 'AdminStaffEdit:trial_toggled';
                helper.catchError(errorStats, functionName);
            }
        },
        active_toggled: function(value) {
            try {
                this.staff.profile.active = value;
                if(value){
                    this.style_on2 = 'color:#000;'
                    this.style_off2 = 'color:#a3a3a3;'
                }else{
                    this.style_off2 = 'color:#000'
                    this.style_on2 = 'color:#a3a3a3;'
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Active is toggled'}
                let functionName = 'AdminStaffEdit:active_toggled';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    components: {
        // AdminSidebar,
        StaffSidebar,
        FilePreview
    }
}
</script>

<style lang="scss" scoped>
    .proto .protoDashboard .dashboardContent .additional-settings{
        position: relative;
        right: 0;
        bottom: 0;
        padding-right: 0px;
        padding-left: 0;
        margin: 1rem 0 1rem;
    }
    .onoff-label{
        position: absolute;
        top: -30px;
        .onoff-space{
            margin-left:2rem;
        }
    }
    .ui-button.fullwidth {
        height: 39px;
    }
    .proto .dashboardContent .doc-list-con {
        padding-left: 10px;
    }
</style>
<style lang="scss">
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.ui-switch-thumb {
    width: 30px;
    height: 30px;
    background-color: #3b5998!important;
}
.ui-switch-label-text {
    margin-left: 0;
    margin-right: 2rem;
}
.ui-switch-track {
    top: 7px;
    height: 15px;
    width: 60px;
}
.ui-switch-container {
    position: relative;
    width: 60px;
    height: 30px;
}
.ui-switch.checked .ui-switch-thumb {
    left: 35px;
}
.additional-settings {
    .col-md-4, .col-md-3{
        padding:0;
    }
    p {
        text-align:left;
            margin-bottom: .5rem;
        .switch_off .switch_off2{
           padding-right:1rem;
        }
        .switch_on .switch_on2{
            padding-left:1rem;
        }
    }
}
</style>