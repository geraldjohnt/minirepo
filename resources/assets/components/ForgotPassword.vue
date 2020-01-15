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
                <img src="image/mee2box-white-vert.png" class="imgResponsive logo" v-cloak>
                <h5 class="protoTitle"> オンライン商談システム </h5>
                <form autocomplete="off" @submit.prevent="forgotPass">
                    <div class="form-group">
                        <ui-textbox
                            label="Email" name="email" id="email" type="text" placeholder="Email アドレスを入力してください" :value.sync="email"
                        ></ui-textbox>
                    </div>
                    <div class="row button-con">
                        <div class="col-xs-6">
                            <ui-button type="normal" color="primary">メールを送信する</ui-button>
                        </div>
                        <div class="col-xs-6">
                            <ui-button type="normal" color="default" v-link="{ name: 'signin' }">サインイン</ui-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import auth from '../js/auth.js';
import helper from '../js/helpers.js';
export default {
    data()
    {
        return {
            email: null,
            success: false,
            success_message: null,
            error: false,
            error_message: null
        }
    },
    methods: 
    {
        forgotPass() {
            try {
                auth.forgotPass(this, this.email)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Password forgotten'}
                let functionName = 'ForgotPassword:forgotPass';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    ready() {
        try {
            this.$dispatch('component-ready', {
                role: 'guest',
                showToolbar: false
            })
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the forgot password'}
            let functionName = 'ForgotPassword:ready';
            helper.catchError(errorStats, functionName);
        }
    }
}
</script>
