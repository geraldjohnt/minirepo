<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar :api-url="notices_post.api_url"></staff-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">

                <ui-alert @dismissed="error = false" type="error" :show="error">
                    <span v-html="error_message"></span><!-- {{{ error_message }}} -->
                </ui-alert>

                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h1>アカウント更新</h1>
                        <form autocomplete="off" @submit.prevent="updateAccount" id="formUpdateAccount">
                            <div class="avatarCon" v-el:avatar-upload>
                                <img class="avatarUpload imgResponsive" v-bind:src="getAvatar">
                                <file-preview name="avatar" id="avatar" @changed="changedAvatar" v-ref:image-upload></file-preview>
                                <div class="overlay">
                                    <ui-icon icon="add_a_photo"></ui-icon>
                                </div>
                                <ui-ripple-ink :trigger="$els.avatarUpload"></ui-ripple-ink>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="名前" name="first_name" id="first_name" type="text" placeholder="名を入力してください" :value.sync="auth.user.profile.first_name"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="担当者名" name="last_name" id="last_name" type="text" placeholder="姓を入力してください" :value.sync="auth.user.profile.last_name" disabled
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="Email" name="email" id="email" type="text" placeholder="Email アドレスを入力してください" :value.sync="auth.user.profile.email" disabled
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="会社名" name="company" id="company" type="text" placeholder="会社名を入力してください" :value.sync="auth.user.profile.company" disabled
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="部門" name="department" id="department" type="text" placeholder="部門を入力してください" :value.sync="auth.user.profile.department" disabled
                                ></ui-textbox>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="役職" name="post" id="post" type="text" placeholder="役職を入力してください" :value.sync="auth.user.profile.post"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="電話番号" name="phone_number" id="phone_number" type="text" placeholder="電話番号を入力してください" :value.sync="auth.user.profile.phone_number" disabled
                                ></ui-textbox>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="現在のパスワード" name="current_password" id="current_password" type="password" placeholder="現在のパスワードを入力してください" :value.sync="auth.user.profile.current_password"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="パスワード" name="password" id="password" type="password" placeholder="新しいパスワードを入力してください" :value.sync="auth.user.profile.password"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="パスワード（確認）" name="password_confirmation" id="password_confirmation" type="password" placeholder="新しいパスワードを入力してください" :value.sync="auth.user.profile.password_confirmation"
                                ></ui-textbox>
                            </div>
                            <ui-button type="normal" color="primary">アカウントを更新する</ui-button>
                        </form>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        
                    </div>

                </div>
                <slot name="modal-slot"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from './partials/StaffSidebar.vue';
import FilePreview from '../common/FilePreview.vue';
import {APP_URL} from '../../js/config.js';
import auth from '../../js/auth.js';
import staff from '../../js/staff.js';
import {STAFF_NOTICES_RESOURCE} from '../../js/api_routes.js';

export default {
    data() 
    {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            notices_post: {
                api_url: STAFF_NOTICES_RESOURCE,
            },
            avatar: '',
            error: false,
            error_message: null 
        }
    },
    methods: {
        updateAccount() {
            try {
                let data = new FormData();
                for ( var key in this.auth.user.profile ) {
                    data.append(key, this.auth.user.profile[key])
                }
                data.append('avatar', this.avatar)

                staff.update(this, data)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating user account'}
                let functionName = 'StaffEditAccount:updateAccount';
                helper.catchError(errorStats, functionName);
            }
        },
        changedAvatar() {
            try {
                this.avatar = this.$refs.imageUpload.value
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Changing the avatar'}
                let functionName = 'StaffEditAccount:changedAvatar';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    computed: {
        getAvatar() {
            try {
                if(this.avatar) {
                    return this.avatar
                } else {
                    if(this.auth.user.authenticated && this.auth.user.profile.pic_url) {
                        return this.auth.user.profile.pic_url
                    }
                }
                return this.static_images.avatar
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the user avatar'}
                let functionName = 'StaffEditAccount:getAvatar';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    created() {
        try {
            this.auth.user.profile.current_password = ''
            this.auth.user.profile.password = ''
            this.auth.user.profile.password_confirmation = ''
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created the user account edit'}
            let functionName = 'StaffEditAccount:created';
            helper.catchError(errorStats, functionName);
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
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the edit user account'}
            let functionName = 'StaffEditAccount:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        StaffSidebar,
        FilePreview
    }
}
</script>
<style lang="scss">
    #formUpdateAccount .disabled {
        visibility: visible;
    }
</style>