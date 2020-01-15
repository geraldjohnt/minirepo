<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar :api-url="notices_post.api_url"></staff-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <h1>
                            <span>
                                商談メモ
                            </span>
                        </h1>
                        <div style="background-color: #E6E6E6">
                             <sales
                                :memo.sync="memo"
                                @updatelist="updateBox"
                            >
                            </sales>
                        </div>
                </div>
                <ui-modal
                :show.sync="show.viewModal" :header="view_modal.header"
                >
                    {{ view_modal.content }}
                </ui-modal> 
                <slot name="modal-slot"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from './partials/StaffSidebar.vue';
import Sales from './partials/Sales.vue';
import UiTable from '../common/UiTable.vue';
import {APP_URL} from '../../js/config.js';
import staff from '../../js/staff.js';
import auth from '../../js/auth.js';
import {  UiButton } from 'keen-ui';
import {STAFF_NOTICES_RESOURCE} from '../../js/api_routes.js';

export default {
    data() {
        return {
            auth: this.$parent.auth,
            APP_URL,
            memo: '',
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            notices_post: {
                api_url: STAFF_NOTICES_RESOURCE,
            },
            show: {
                viewModal: false,
                deleteConfirm: false
            },
            view_modal: {
                header: '',
                content: ''
            },
            current_item: {}
        }
    },
    methods: {
        updateBox(){
            try {
                let data = {memo: this.memo};
                staff.updateMemo(this, data);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Updating the box'}
                let functionName = 'SalesMain:updateBox';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    events: {
    },
    created(){
        try {
            staff.getMemo(this);
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data from our server'}
            let functionName = 'SalesMain:created';
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
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the sales main component'}
            let functionName = 'SalesMain:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        StaffSidebar,
        Sales,
        UiTable,
        UiButton
    }
}
</script>
<style lang="scss">
    .page--ui-button {
    .ui-radio-group,
    .ui-switch {
        margin-bottom: rem-calc(16px);
    }
    .ui-switch {
        display: inline-flex;
    }
    .ui-button {
        margin-bottom: rem-calc(12px);
        margin-right: rem-calc(8px);
    }
    .page__demo-group {
        margin-bottom: rem-calc(18px);
    }
    .page__demo-table {
        max-width: rem-calc(600px);
        .ui-button {
            display: flex;
            &.ui-button--size-normal {
                min-width: rem-calc(100px);
            }
            &.ui-button--size-large {
                min-width: rem-calc(124px);
            }
        }
    }
}
.custom-popover-content {
    padding: rem-calc(16px);
    max-width: rem-calc(360px);
    p:first-child {
        margin-top: 0;
    }
    p:last-child {
        margin-bottom: 0;
    }
}

</style>