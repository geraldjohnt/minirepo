<template>
    <div class="protoCon protoForgotPassword invertToWhite">
        <div class="protoContentContainer invertToWhite">
            <ui-alert @dismissed="success = false" type="success" :show="success">
                {{ success_message }}
            </ui-alert>
            <ui-alert @dismissed="error = false" type="error" :show="error">
                {{ error_message }}
            </ui-alert>
            <div class="protoForgotPasswordCon">
                <img src="image/mee2box-white.png" class="imgResponsive logo" v-cloak>
                <h5 class="protoTitle"> オンライン商談システム </h5>
                <form autocomplete="off" @submit.prevent="resetPass">
                    <div class="form-group">
                        <ui-textbox
                            label="Pass" name="password" id="password" type="password" placeholder="パスワードを入力してください" :value.sync="password"
                        ></ui-textbox>
                    </div>
                    <div class="form-group">
                        <ui-textbox
                            label="Confirm Pass" name="password_confirmation" id="password_confirmation" type="password" placeholder="パスワードを入力してください" :value.sync="password_confirmation"
                        ></ui-textbox>
                    </div>
                    <div class="row button-con">
                        <div class="col-xs-6">
                            <ui-button type="normal" color="primary">パスワードを設定する</ui-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import auth from '../js/auth.js';
export default {
    data()
    {
        return {
            password: null,
            password_confirmation: null,
            token: null,
            success: false,
            success_message: null,
            error: false,
            error_message: null
        }
    },
    methods: 
    {
        resetPass(action) {
            auth.resetPass(
                this,
                this.password,
                this.password_confirmation,
                this.token,
                action
            )
        }
    },
    ready() {
        this.token = this.$route.params.token
        this.$dispatch('component-ready', {
            role: 'guest', 
            showToolbar: false
        })
        this.resetPass('validate')
    }
}
</script>
