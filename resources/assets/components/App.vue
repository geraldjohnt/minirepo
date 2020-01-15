<template>
    <div class="proto">
        <section v-if="showToolbar" class="section section-ui-toolbar">
            <ui-toolbar type="colored" text-color="white">
                <div class="ui-toolbar-title appLogo">
                    <img class="imgResponsive" :src="static_images.logo_white" v-cloak>
                </div>
                <div slot="actions">
                    <ui-button 
                        class="protoButton signin" color="primary" type="normal" 
                        v-link="{ name: 'signin' }" v-if="!auth.user.authenticated">
                        ログアウト
                    </ui-button>
                    <ui-icon-button
                        type="clear" color="white" icon="help" v-if="auth.user.role == 'staff' && is_mobile_view"
                        tooltip="よくある質問" tooltip-position="bottom center" @click="showFaqModal"
                    ></ui-icon-button>
                    <ui-button
                        type="flat" color="primary" icon="help" v-if="auth.user.role == 'staff' && !is_mobile_view"
                        tooltip="よくある質問" tooltip-position="bottom center" @click="showFaqModal"
                    >よくある質問</ui-button>

                    <ui-icon-button
                        type="clear" color="white" icon="mail" v-if="auth.user.role == 'staff' && is_mobile_view" 
                        tooltip="お問合せ" tooltip-position="bottom center" @click="showFeedbackModal"
                    ></ui-icon-button>
                    <ui-button
                        type="flat" color="primary" icon="mail" v-if="auth.user.role == 'staff' && !is_mobile_view" 
                        tooltip="お問合せ" tooltip-position="bottom center" @click="showFeedbackModal"
                    >お問合せ</ui-button>

                    <ui-icon-button
                        type="clear" color="white" icon="icon-settings" v-if="auth.user.role == 'staff' && is_mobile_view"
                        tooltip="設定変更" tooltip-position="bottom center" v-link="{name: 'staff-edit-account'}"
                    ></ui-icon-button>
                    <ui-button
                        type="flat" color="primary" icon="icon-settings" v-if="auth.user.role == 'staff' && !is_mobile_view"
                        tooltip="設定変更" tooltip-position="bottom center" v-link="{name: 'staff-edit-account'}">
                    設定変更</ui-button>

                    <ui-icon-button
                        type="clear" color="white" icon="exit_to_app" v-if="auth.user.authenticated && is_mobile_view" v-on:click="signout"
                        tooltip="ログアウト" tooltip-position="left top"
                    >ログアウト</ui-icon-button>
                    <ui-button
                        type="flat" color="primary" icon="exit_to_app" v-if="auth.user.authenticated && !is_mobile_view" v-on:click="signout"
                        tooltip="ログアウト" tooltip-position="left top"
                    >ログアウト</ui-button>
                </div>
            </ui-toolbar>
        </section>

        <div class="main-content">
            <router-view>
                <div slot="modal-slot">
                    
                    <ui-modal type="large" class="faqModal"
                        :show.sync="show.faq_modal" header="FAQ"
                        >
                        <ui-collapsible header="利用環境">
                            <ui-collapsible header="動作環境">
                                <div class="row divTable">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="title col-xs-4">
                                                スタッフ推奨環境
                                            </div>
                                            <div class="col-xs-8"></div>
                                            <div class="col-xs-4">
                                                OS
                                            </div>
                                            <div class="col-xs-8">
                                                Mac OS<br \>
                                                Windows XP以降
                                            </div>
                                            <div class="col-xs-4">
                                                ブラウザ
                                            </div>
                                            <div class="col-xs-8">
                                                Google Chrome
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="title col-xs-4">
                                                お客様推奨環境
                                            </div>
                                            <div class="col-xs-8"></div>
                                            <div class="col-xs-4">
                                                OS
                                            </div>
                                            <div class="col-xs-8">
                                                Mac OS<br \>
                                                Windows XP以降
                                            </div>
                                            <div class="col-xs-4">
                                                ブラウザ
                                            </div>
                                            <div class="col-xs-8">
                                                Google Chrome<br \>
                                                Firefox
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </ui-collapsible>
                            <ui-collapsible header="カメラ設定">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <b class="title">Surface等の前、背面にカメラがある場合の設定方法</b><br \>
                                        <b>1.Google Chromeの場合</b><br \>
                                        Chromeの環境設定 → 詳細設定を表示 → コンテンツの設定 → カメラ → “内側のカメラ”を選択。<br \><br \>
                                        <b>2.Firefoxの場合</b><br \>
                                        カメラ起動時に画面左上の設定ページから共有するカメラを“内側のカメラ”に設定した上で、「選択したデバイスを共有」もしくは「常に共有」をクリック。<br \><br \>
                                        <b>3.Google Chromeのカメラ設定を拒否してしまった場合</b><br \>
                                        Chromeの環境設定 → 詳細設定を表示 → コンテンツの設定 → カメラ → 例外の管理 → “Mee2box”が含まれるホスト名にて「ブロック」となっているものを選択し、右端の［ ✕ ］ボタンにて削除。<br \>
                                    </div>
                                </div>
                            </ui-collapsible>
                        </ui-collapsible>
                        <ui-collapsible header="資料">
                            <ui-collapsible header="編集">
                                <div>
                                    鉛筆のアイコンをクリックすることにより編集ができます。<br \>
                                    各ページのトークスプリプトもこちらから入力、編集ができます。<br \>
                                </div>
                            </ui-collapsible>
                            <ui-collapsible header="削除">
                                <div>
                                    ゴミ箱のアイコンをクリックで削除ができます。<br \>
                                </div>
                            </ui-collapsible>
                        </ui-collapsible>
                    </ui-modal>

                    <ui-modal hide-footer
                        :show.sync="show.feedback_modal" header="フィードバック"
                        >
                            <ui-alert @dismissed="feedback.error = false" type="error" :show="feedback.error">
                                <span v-html="feedback.error_message"></span><!--{{{ feedback.error_message }}}-->
                            </ui-alert>
                        <form autocomplete="off" @submit.prevent="sendFeedback">
                            <div class="form-group">
                                <ui-textbox
                                    label="お名前" name="name" id="name" type="text" placeholder="お名前入力してください" :value.sync="feedback.name" validation-rules="required" v-ref:input-name :validation-messages="feedback.vm_name"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="Email" name="feedback_email" id="feedback_email" type="email" placeholder="Email アドレスを入力してください" :value.sync="feedback.email" validation-rules="required" v-ref:input-email :validation-messages="feedback.vm_email"
                                ></ui-textbox>
                            </div>
                            <div class="form-group">
                                <ui-textbox
                                    label="内容" name="message" id="message" type="text" placeholder="内容入力してください" :value.sync="feedback.message" :multi-line="true" validation-rules="required" v-ref:input-message :validation-messages="feedback.vm_message"
                                ></ui-textbox>
                            </div>
                            <ui-button type="normal" color="primary" :loading="feedback.loading"  v-el:feedback-submit-btn>送信</ui-button>
                        </form>
                    </ui-modal>

                    <ui-modal
                        :show.sync="show.notice_modal" :header="notice_modal.header"
                        >
                        {{ notice_modal.content }}
                    </ui-modal>

                </div>
            </router-view>
            <ui-snackbar-container class="mainSnackBar"
                position="center" queue-snackbars
            ></ui-snackbar-container>

        </div>
    </div>
</template>

<script>
import Vue from '../js/app.js';
import auth from '../js/auth.js'
import helper from '../js/helpers.js';
import {USER_EMAIL_FEEDBACK, STAFF_NOTICES_RESOURCE} from '../js/api_routes.js';
import {APP_URL} from '../js/config.js';
import UiPost from './common/UiPost.vue';

export default {
    data() 
    {
        return {
            auth: auth,
            showToolbar: true,
            show: {
                faq_modal: false,
                feedback_modal: false,
                notice_modal: false
            },
            feedback: {
                name: '',
                email: '',
                message: '',
                error: false,
                error_message: '',
                loading: false,
                vm_name: {
                    required : 'お名前が未入力です'
                },
                vm_email: {
                    required : 'メールアドレスが未入力です'
                },
                vm_message: {
                    required : 'フィードバック内容が未入力です'
                }
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
            notice_current_item: {},
            notice_modal: {
                header: '',
                content: ''
            },
            static_images: {
                logo_white: `${APP_URL}/image/mee2box-white.png`,
                logo_dark: `${APP_URL}/image/mee2box-darkcyan.png`
            },
            is_mobile_view: false
        }
    },
    methods: {
        signout() {
            try {
                auth.signout()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Signing out of the system'}
                let functionName = 'App:signout';
                helper.catchError(errorStats, functionName);
            }
        },
        hasSidebar() {
            try {
                let child_component = this.getChildComponent('VueComponent')
                if(child_component && typeof child_component.$refs.sidebar != 'undefined') {
                    let sidebar = child_component.$refs.sidebar.$children[0]
                    if (sidebar.constructor.name === 'UiSidebar') {
                        return true;
                    }
                }
                return false;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking if sidebar is present'}
                let functionName = 'App:hasSidebar';
                helper.catchError(errorStats, functionName);
            }
        },
        getChildComponent(constructor) {
            try {
                for(let key in this.$children) {
                    if(this.$children[key].constructor.name == constructor) {
                        return this.$children[key]
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting child component'}
                let functionName = 'App:getChildComponent';
                helper.catchError(errorStats, functionName);
            }
        },
        toggleSidebar() {
            try {
                let child_component = this.getChildComponent('UiToolbar')
                if(typeof child_component != 'undefined') {
                    if(!this.hasSidebar()) {
                        child_component.hideNavIcon = true
                        child_component.$el.classList.add('hideNavIcon')
                    } else {
                        child_component.hideNavIcon = false
                        child_component.$el.classList.remove('hideNavIcon')
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Toggling the sidebar'}
                let functionName = 'App:toggleSidebar';
                helper.catchError(errorStats, functionName);
            }
        },
        sendFeedback() {
            try {
                this.feedback.loading = true
                let data = new FormData();
                for ( var key in this.feedback ) {
                    data.append(key, this.feedback[key])
                }
                // console.log(this.feedback.validation_messages)
                Vue.http.post(
                    USER_EMAIL_FEEDBACK,
                    data
                ).then(response => {
                    this.feedback.loading = false
                    this.show.feedback_modal = false

                    helper.removeLoadingBtn(this.$els.feedbackSubmitBtn)
                    this.$broadcast('ui-snackbar::create', {
                        message: response.data.message,
                        duration: 5000
                    });
                }, response => {
                    this.feedback.error = true
                    helper.handleErrorRequest(this, response, 'feedback')
                    this.feedback.loading = false
                    helper.removeLoadingBtn(this.$els.feedbackSubmitBtn)
                    // this.$broadcast('ui-input::set-validity', false, 'here', 'message')
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sending the feedback'}
                let functionName = 'App:sendFeedback';
                helper.catchError(errorStats, functionName);
            }
        },
        showFaqModal(e) {
            try {
                e.stopPropagation()
                this.show.faq_modal = this.show.faq_modal ? false : true
                this.closeModals()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Showing the FAQ Modal'}
                let functionName = 'App:showFaqModal';
                helper.catchError(errorStats, functionName);
            }
        },
        showFeedbackModal(e) {
            try {
                e.stopPropagation()
                this.show.feedback_modal = this.show.feedback_modal ? false : true
                this.closeModals()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Showing the feedback modal'}
                let functionName = 'App:showFeedbackModal';
                helper.catchError(errorStats, functionName);
            }
        },
        closeModals() {
            try {
                var el = document.getElementsByClassName('ui-modal-close-button')
                for (var i=0;i<el.length; i++) {
                    el[i].click();
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Closing the modal'}
                let functionName = 'App:closeModals';
                helper.catchError(errorStats, functionName);
            }
        },
        formatMeetingDate(value) {
            try {
                return helper.formatDate(value, 'YYYY/MM/DD')
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Formatting the dates of the meeting'}
                let functionName = 'App:formatMeetingDate';
                helper.catchError(errorStats, functionName);
            }
        },
        handleResize() {
            try {
                let width = document.documentElement.clientWidth
                this.is_mobile_view = width <= 1024
                if(!(width <= 1024)) {
                    this.$el.classList.add('pc-view')
                } else {
                    this.$el.classList.remove('pc-view')
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Handling the resizing'}
                let functionName = 'App:handleResize';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    events: {
        'nav-icon-clicked': function() {
            try {
                if(this.hasSidebar()) {
                    let child_component = this.getChildComponent('VueComponent')
                    let sidebar = child_component.$refs.sidebar.$children[0]
                    sidebar.toggle()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Navigation icon clicked'}
                let functionName = 'App:nav-icon-clicked';
                helper.catchError(errorStats, functionName);
            }
        },
        'component-ready': function(param){
            try {
                let cb = () => {
                    if(this.auth.user.authenticated) {
                        this.feedback.name = this.auth.user.profile.last_name
                        this.feedback.email = this.auth.user.profile.email
                    }
                }
                if(typeof param != 'undefined') {
                    auth.check(param.role, cb)
                    this.showToolbar = typeof param.showToolbar == 'boolean' ? param.showToolbar : true
                } else {
                    auth.check('undefined', cb)
                    this.showToolbar = true
                    this.handleResize()
                }
                this.toggleSidebar()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Component is ready'}
                let functionName = 'App:component-ready';
                helper.catchError(errorStats, functionName);
            }
        },
        'opened': function() {
            try {
                var modal_opened = helper.hasClass(document.body, 'ui-modal-open')
                if(modal_opened) {
                    var el = document.getElementsByClassName('ui-modal')
                    if(typeof el != 'undefined') {
                        let wrapper = helper.getParentElementByClass('wrapper', el[0])
                        if(wrapper) {
                            wrapper.scrollTop = 0
                            wrapper.classList.add('noScroll')
                        }
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Application is opened'}
                let functionName = 'App:opened';
                helper.catchError(errorStats, functionName);
            }
        },
        'hidden': function() {
            try {
                var el = document.getElementsByClassName('ui-modal')
                if(typeof el != 'undefined') {
                    let wrapper = helper.getParentElementByClass('wrapper', el[0])
                    if(wrapper) {
                        wrapper.classList.remove('noScroll')
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Application is hidden'}
                let functionName = 'App:hidden';
                helper.catchError(errorStats, functionName);
            }
        },
        'uipost:post:clicked': function(item) {
            try {
                this.notice_current_item = item
                this.notice_modal.header = item.notice_details.title
                this.notice_modal.content = item.notice_details.content
                this.show.notice_modal = this.show.notice_modal ? false : true
                this.closeModals()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> UI post is clicked'}
                let functionName = 'App::uipost:post:clicked';
                helper.catchError(errorStats, functionName);
            }
        },
        'handle-resize': function(width) {
            try {
                this.handleResize()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Handling the resizing'}
                let functionName = 'App:handle-resize';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    components: {
        UiPost
    },
    created(){
        
        
    }
  
}
</script>

<style lang="scss">
    .proto {
        .ui-modal-mask.faqModal {
            height: 100% !important;
            .ui-modal-container {
                overflow-y: scroll !important;
                scrollbar-color: #0d275f #0d275f !important;
                .ui-modal-close-button {
                    top: 13px;
                    right: 9px;
                }
            }
            .ui-modal-container::-webkit-scrollbar-thumb {
                background-color: #0d275f;
                outline: 1px solid #0d275f;
            }
            .ui-modal-container::-webkit-scrollbar{
                width:1rem;
            }

            .ui-modal-container::-webkit-scrollbar-track{
                -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3) !important;
                box-shadow: inset 0 0 6px rgba(0,0,0,0.3) !important;
            }
        }
    }
</style>