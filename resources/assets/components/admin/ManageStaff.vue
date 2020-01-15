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
                                ユーザー管理
                            </span>
                            <ui-fab type="mini" color="primary" icon="add" v-link="{ name: 'admin-register-staff' }"></ui-fab>
                        </h1>

                        <ui-table
                            :api-url="staff_table.api_url"
                            :data-key="staff_table.data_key"
                            :fields="staff_table.fields"
                            :actions="staff_table.actions"
                            :no-content-text="staff_table.no_content_text"
                            :manage-staff="true"
                        ></ui-table>
                    </div>
                </div>
                <ui-confirm
                header="Delete Staff" type="danger" confirm-button-text="削除"
                confirm-button-icon="delete" deny-button-text="キャンセル" @confirmed="deleteConfirmed"
                :show.sync="show.deleteConfirm" close-on-confirm
                >
                    Are you sure you want to delete this staff?
                </ui-confirm>
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
import helper from '../../js/helpers.js';
import {ADMIN_MANAGE_STAFF_RESOURCE} from '../../js/api_routes.js';

export default {
    data() {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            staff_table: {
                api_url: ADMIN_MANAGE_STAFF_RESOURCE,
                data_key: `staff`,
                fields: [
                    // { name: 'profile.email', label: 'Email アドレス', width: 25, sortby: 0,s : 'email'},
                    { name: 'profile.company', label: '会社名', width: 10, sortby: 1,s: 'company' },
                    { name: 'profile.last_name', label: '担当者名', width: 10, sortby: 2,s : 'last_name' },
                    { name: 'profile.trial' , label: '登録状況', width: 10, sortby: 3, s: 'trial'},
                    { name: 'profile.active' , label: 'アクティブ', width: 10, sortby: 4, s: 'active'},
                    { name: 'profile.trial_start' , label: 'トライアル開始', width: 10, sortby: 5, s: 'trial_start'},
                    { name: 'profile.end_of_trial' , label: 'トライアル終了', width: 10, sortby: 6, s: 'end_of_trial'},
                    { name: 'profile.registered_date' , label: '本登録', width: 10, sortby: 7, s: 'registered_date'}
                ],
                actions: [
                    // { name: 'view-item', label: '', icon: 'search', icon_color: 'primary' },
                    { name: 'edit-item', label: '', icon: 'create', icon_color: 'warning', 
                    routelink: { name: 'admin-edit-staff', params: { id: 'staff_id' } } },
                    { name: 'delete-item', label: '', icon: 'delete', icon_color: 'danger' }
                ],
                no_content_text: `There are no staff yet.`
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
            try {
                let vm = this
                admin.deleteStaff(this,{id: this.current_item.staff_id}, function(){
                    location.reload();
                    vm.$broadcast('uitable:refresh')
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Delete user confirmed'}
                let functionName = 'AdminManageStaff:deleteConfirmed';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    events: {
        'uitable:delete-item': function(action, data) {
            try {
                let vm = this
                this.show.deleteConfirm = true
                this.current_item = data
                vm.$broadcast('uitable:delete-item');
                //console.log(data);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Delete item from the table'}
                let functionName = 'AdminManageStaff::uitable:delete-item';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    ready() {
        try {
            let config = {
                role: 'admin',
                showToolbar: true
            }

            this.$dispatch('component-ready', config)
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the admin manage staff component'}
            let functionName = 'AdminManageStaff:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        // AdminSidebar,
        StaffSidebar,
        UiTable
    }
}
</script>