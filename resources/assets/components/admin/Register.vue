<template>
    <div class="protoRegister">
        <div class="protoRegisterCon">
            <div class="alert alert-danger" v-if="error && !success">
                <p>There was an error, unable to complete registration.</p>
            </div>
            <div class="alert alert-success" v-if="success">
                <p>Registration completed. You can now sign in.</p>
            </div>
            <h3 class="protoTitle"> Register </h3>
            <form autocomplete="off" v-on:submit="register" v-if="!success">
                <div class="form-group" v-bind:class="{ 'has-error': error && response.first_name }">
                    <ui-textbox
                        label="First Name" name="first_name" id="first_name" type="text" placeholder="Enter your first name"
                    ></ui-textbox>
                    <span class="help-block" v-if="error && response.first_name">{{ response.first_name }}</span>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': error && response.last_name }">
                    <ui-textbox
                        label="Last Name" name="last_name" id="last_name" type="text" placeholder="Enter your last name"
                    ></ui-textbox>
                    <span class="help-block" v-if="error && response.last_name">{{ response.last_name }}</span>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': error && response.email }">
                    <ui-textbox
                        label="Email" name="email" id="email" type="text" placeholder="Enter your e-mail"
                    ></ui-textbox>
                    <span class="help-block" v-if="error && response.email">{{ response.email }}</span>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': error && response.password }">
                    <ui-textbox
                        label="Password" name="password" id="password" type="password" placeholder="Enter your password"
                    ></ui-textbox>
                    <span class="help-block" v-if="error && response.password">{{ response.password }}</span>
                </div>
                <ui-button type="normal" color="primary">Register</ui-button>
            </form>
        </div>
    </div>
</template>

<script>
import auth from '../../js/auth.js';
export default {
    data() {
        return {
            name: null,
            email: null,
            password: null,
            success: false,
            error: false,
            response: null
        }
    },
    methods: {
        register(event) {
            event.preventDefault();
            auth.register(this, this.name, this.email, this.password);
        }
    }
}
</script>