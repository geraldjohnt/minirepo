<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar></staff-sidebar>

        <div class="dashboardContent">
            <div class="wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <h1>
                            <span>
                                Negotation History
                            </span>
                        </h1>

                        <div class="ui-panel">
                            <ui-alert class="noMargin" text="There are no negotation history yet."
                            :show="!visible && dataloaded" :dismissible="false"></ui-alert>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from './partials/StaffSidebar.vue';
import {APP_URL} from '../../js/config.js';
import auth from '../../js/auth.js';

export default {
    data() {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            visible: false,
            dataloaded: false
        }
    },
    methods: {
        loadNegotationHistory() {
            this.dataloaded = true
        }
    },
    ready() {
        this.$parent.auth.check('staff')
        this.$dispatch('component-ready')
        this.loadNegotationHistory()
    },
    components: {
        StaffSidebar
    }
}
</script>

