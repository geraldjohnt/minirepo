<template>
    <div class="protoDashboard">
        <staff-sidebar v-ref:sidebar 
            :api-url="notices_post.api_url"      
        ></staff-sidebar>

        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <h1>
                            <span>
                                お知らせ
                            </span>
                        </h1>

                        <ui-post
                            class="noticesContainer"
                            :api-url="notices_post.api_url"
                            :data-key="notices_post.data_key"
                            :details="notices_post.details"
                            :actions="notices_post.actions"
                            :no-content-text="notices_post.no_content_text"
                        ></ui-post>
                    </div>
                </div>
                <slot name="modal-slot"></slot>
            </div>
        </div>
    </div>
</template>

<script>
import StaffSidebar from './partials/StaffSidebar.vue';
import UiPost from '../common/UiPost.vue';
import {APP_URL} from '../../js/config.js';
import auth from '../../js/auth.js';
import helper from '../../js/helpers.js';
import {STAFF_NOTICES_RESOURCE} from '../../js/api_routes.js';

export default {
    data() {
        return {
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            notices_post: {
                api_url: STAFF_NOTICES_RESOURCE,
                data_key: `notices`,
                details: {
                    title: {
                        name: 'notice_details.title'
                    },
                    content: {
                        name: 'notice_details.content'
                    },
                    date: {
                        name: 'notice_details.created_at.date',
                        callback: this.formatMeetingDate
                    }
                },
                actions: [],
                no_content_text: `お知らせメッセージがありません`
            },
            current_item: {}
        }
    },
    methods: {
        formatMeetingDate(value) {
            try {
                return helper.formatDate(value, 'YYYY/MM/DD')
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Formatting the dates of the meeting'}
                let functionName = 'StaffNotices:formatMeetingDate';
                helper.catchError(errorStats, functionName);
            }
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
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready component for the staff notices'}
            let functionName = 'StaffNotices:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        StaffSidebar,
        UiPost
    }
}
</script>

