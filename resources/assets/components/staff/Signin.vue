<template>
    <div class="protoSignin">
        <ui-alert @dismissed="error = false" type="error" :show="error">
            {{ error_message }}
        </ui-alert>
        <div class="protoSigninCon">
            <h3 class="protoTitle"> Staff Sign-in </h3>
            <form autocomplete="off" @submit.prevent="signin">
                <div class="form-group">
                    <ui-textbox
                        label="Email" name="email" id="email" type="text" placeholder="Enter your e-mail" :value.sync="email"
                    ></ui-textbox>
                </div>
                <div class="form-group">
                    <ui-textbox
                        label="Password" name="password" id="password" type="password" placeholder="Enter your password" :value.sync="password"
                    ></ui-textbox>
                </div>
                <ui-button type="normal" color="primary">Sign in</ui-button>
            </form>
        </div>
    </div>
</template>

<script>
import auth from '../../js/auth.js';
export default {
    data() {
        return {
            email: null,
            password: null,
            error: false,
            error_message: null,
            role: 'staff'
        }
    },
    methods: {
        signin() {
            auth.signin(this, this.email, this.password, this.role);
        }
    },
    ready() {
        this.$dispatch('component-ready')
    }
}
</script>