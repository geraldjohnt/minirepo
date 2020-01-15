<template>
    <div class="protoDashboard">
        <!-- <admin-sidebar v-ref:sidebar></admin-sidebar> -->
 <staff-sidebar v-ref:sidebar ></staff-sidebar>
        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <h1>
                            <span>
                                通知管理
                            </span>
                            <ui-fab type="mini" color="primary" icon="add" v-link="{ name: 'admin-add-notice' }"></ui-fab>
                        </h1>

                        <ui-table
                            :api-url="notices_table.api_url"
                            :data-key="notices_table.data_key"
                            :fields="notices_table.fields"
                            :actions="notices_table.actions"
                            :no-content-text="notices_table.no_content_text"
                        ></ui-table>
                    </div>
                </div>
                <ui-confirm
                header="Delete Notice" type="danger" confirm-button-text="削除"
                confirm-button-icon="delete" deny-button-text="キャンセル" @confirmed="deleteConfirmed"
                :show.sync="show.deleteConfirm" close-on-confirm
                >
                    Are you sure you want to delete this notice?
                </ui-confirm>
                <ui-modal
                :show.sync="show.viewModal" :header="view_modal.header"
                >
                    {{ view_modal.content }}
                </ui-modal>
            </div>
        </div>
    </div>
</template>

<script>
// import AdminSidebar from './partials/AdminSidebar.vue';
import StaffSidebar from '../staff/partials/StaffSidebar.vue';
import UiTable from '../common/UiTable.vue';
import {APP_URL} from '../../js/config.js';
import admin from '../../js/admin.js';
import {ADMIN_NOTICES_RESOURCE} from '../../js/api_routes.js';

export default {
    data() {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            notices_table: {
                api_url: ADMIN_NOTICES_RESOURCE,
                data_key: `notices`,
                fields: [
                    { name: 'title', label: 'タイトル', width: 25 },
                    { name: 'content', label: 'Content', width: 40 },
                    { name: 'broadcast', label: '全ユーザーに向けて表示する', width: 25 }
                ],
                actions: [
                    { name: 'view-item', label: '', icon: 'search', icon_color: 'primary' },
                    { name: 'edit-item', label: '', icon: 'create', icon_color: 'warning', 
                    routelink: { name: 'admin-edit-notice', params: { id: 'notice_id' } } },
                    { name: 'delete-item', label: '', icon: 'delete', icon_color: 'danger' }
                ],
                no_content_text: `There are no notices yet.`
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
        deleteConfirmed() {
            let vm = this
            admin.deleteNotice(this,{id: this.current_item.notice_id}, function(){
                vm.$broadcast('uitable:refresh')
            })
        },
    },
    events: {
        'uitable:view-item': function(action, data) {
            this.show.viewModal = true
            this.view_modal.header = data.title
            this.view_modal.content = `Content: \n ${data.content}`
        },
        'uitable:delete-item': function(action, data) {
            this.show.deleteConfirm = true
            this.current_item = data
        }
    },
    ready() {
        let config = {
            role: 'admin',
            showToolbar: true
        }

        this.$dispatch('component-ready', config)
    },
    components: {
        // AdminSidebar,
        StaffSidebar,
        UiTable
    }
}
</script>