<template>
    <div class="protoDashboard">
        <admin-sidebar v-ref:sidebar></admin-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">

                <ui-alert @dismissed="error = false" type="error" :show="error">
                    {{{ error_message }}}
                </ui-alert>

                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h1>アカウント更新</h1>
                        <form autocomplete="off" @submit.prevent="updateAccount">
                            <div class="avatarCon" v-el:avatar-upload>
                                <img class="avatarUpload imgResponsive" v-bind:src="getAvatar">
                                <file-preview name="avatar" id="avatar" name="avatar" @changed="changedAvatar" v-ref:image-upload></file-preview>
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
                                    label="担当者名" name="last_name" id="last_name" type="text" placeholder="姓を入力してください" :value.sync="auth.user.profile.last_name"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="Email" name="email" id="email" type="text" placeholder="Email アドレスを入力してください" :value.sync="auth.user.profile.email"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="会社名" name="company" id="company" type="text" placeholder="会社名を入力してください" :value.sync="auth.user.profile.company"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="部門" name="department" id="department" type="text" placeholder="部門を入力してください" :value.sync="auth.user.profile.department"
                                ></ui-textbox>
                            </div>
                            <!-- <div class="form-group">
                                <ui-textbox
                                    label="役職" name="post" id="post" type="text" placeholder="役職を入力してください" :value.sync="auth.user.profile.post"
                                ></ui-textbox>
                            </div> -->
                            <div class="form-group">
                                <ui-textbox
                                    label="電話番号" name="phone_number" id="phone_number" type="text" placeholder="電話番号を入力してください" :value.sync="auth.user.profile.phone_number"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="パスワード" name="current_password" id="current_password" type="password" placeholder="現在のパスワードを入力してください" :value.sync="auth.user.profile.current_password"
                                ></ui-textbox>
                            </div>
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
            </div>
        </div>
    </div>
</template>

<script>
import AdminSidebar from './partials/AdminSidebar.vue';
import FilePreview from '../common/FilePreview.vue';
import {APP_URL} from '../../js/config.js';
import auth from '../../js/auth.js';
import admin from '../../js/admin.js';

export default {
    data() 
    {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            avatar: '',
            error: false,
            error_message: null 
        }
    },
    methods: {
        updateAccount() {
            let data = new FormData();
            for ( var key in this.auth.user.profile ) {
                data.append(key, this.auth.user.profile[key])
            }
            data.append('avatar', this.avatar)

            admin.update(this, data)
        },
        changedAvatar() {
            this.avatar = this.$refs.imageUpload.value
        }
    },
    computed: {
        getAvatar() {
            if(this.auth.user.authenticated && this.auth.user.profile.pic_url) {
                return this.auth.user.profile.pic_url
            }
            return this.static_images.avatar
        }
    },
    created() {
        this.auth.user.profile.current_password = ''
        this.auth.user.profile.password = ''
        this.auth.user.profile.password_confirmation = ''
    },
    ready() {
        let config = {
            role: 'admin',
            showToolbar: true
        }

        this.$dispatch('component-ready', config)
    },
    components: {
        AdminSidebar,
        FilePreview
    }
}
</script>