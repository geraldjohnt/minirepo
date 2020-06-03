<template>
    <div class="protoCon protoSignin invertToWhite">
        <div class="protoContentContainer invertToWhite">
            <ui-alert @dismissed="error = false" type="error" :show="error">
                {{ error_message }}
            </ui-alert>
            <div class="protoSigninCon">
                <img style="height: 90px; max-width: 100%;" src="image/mee2box-white.png" class="imgResponsive logo" v-cloak>
		<br />
                <form autocomplete="off" @submit.prevent="signin">
                    <div class="form-group">
                        <ui-textbox
                            label="Email" name="email" id="email" type="text" placeholder="Email アドレスを入力してください" :value.sync="email"
                        ></ui-textbox>
                    </div>
                    <div class="form-group">
                        <ui-textbox
                            label="PASS" name="password" id="password" type="password" placeholder="パスワードを入力してください" :value.sync="password"
                        ></ui-textbox>
                    </div>
                    <ui-checkbox class="remember_me" name="remember_me" :model.sync="remember_me">パスワードを保存する</ui-checkbox>
                    <div class="row button-con">
                        <div class="col-xs-6">
                            <ui-button type="normal" color="primary">ログイン</ui-button>
                        </div>
                        <div class="col-xs-6">
                            <ui-button type="normal" color="default" v-link="{ name: 'forgot-password' }">パスワードを忘れた方</ui-button>
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
            password: null,
            remember_me: false,
            error: false,
            error_message: null
        }
    },
    methods: 
    {
        signin() {
            try {
                auth.signin(this, this.email, this.password, this.remember_me);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Signing into the system'}
                let functionName = 'Signin:signin';
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
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the signin component'}
            let functionName = 'Signin:ready';
            helper.catchError(errorStats, functionName);
        }
    }
}
</script>

<style scoped lang="scss">
.footerCopyright {
    left: 0;
    right: 0;
    margin: 0 auto;
    bottom: 8px;
    text-align: center;
}
</style>
