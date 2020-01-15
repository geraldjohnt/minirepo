<template>
    <div class="protoDashboard">
        <admin-sidebar v-ref:sidebar></admin-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <h1>ダッシュボード</h1>
                        <p v-if="auth.user.authenticated" >Hi, {{ auth.user.profile.last_name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AdminSidebar from './partials/AdminSidebar.vue';
import {APP_URL} from '../../js/config.js';

export default {
    data() {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            }
        }
    },
    methods: {

    },
    ready() {
        let config = {
            role: 'admin',
            showToolbar: true
        }

        this.$dispatch('component-ready', config)
    },
    components: {
        AdminSidebar
    }
}
</script>