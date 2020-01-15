<template>
    <div class="staff-doc-share">
        <ui-sidebar class="protoSidebar" position-right :visible="false">
            <div slot="top-con" class="avatarCon">
                <h3>
                    <ui-icon class="black" icon="description"></ui-icon>
                    資料一覧 
                    <ui-icon class="black" icon="keyboard_arrow_right"></ui-icon>
                    
                    <div v-if="false" class="aContain">
                        <div class="avatar" v-el:user-avatar>
                            <img class="imgResponsive" v-bind:src="getStaffAvatar" v-cloak>
                            <ui-ripple-ink :trigger="$els.userAvatar"></ui-ripple-ink>
                            <ui-menu :trigger="$els.userAvatar" :options="meetingMenu" show-icons v-show="meetingMenu.length"></ui-menu>
                            <ui-tooltip
                                :trigger="$els.userAvatar" :content="getStaffFullName" position="left top"
                            ></ui-tooltip>
                        </div>
                    </div>
                </h3>
            </div>
            <div slot="bottom-con">
                <div class="doc-item" v-for="doc in docsList">
                    <span class="title">タイトル: {{ doc.title }}</span>
                    <span class="desc">説明: {{ doc.description }}</span>
                    <span class="downloadButton" v-if="doc.allow_download">
                        <ui-icon-button type="flat" icon="remove_red_eye" @click="viewDoc(doc)" v-if="viewDocAllowed"></ui-icon-button>
                        <!-- <ui-icon-button type="flat" icon="file_download" @click="downloadDoc(doc)"></ui-icon-button> -->
                        <ui-icon-button type="flat" icon="file_download" @click="shareDoc(doc)"></ui-icon-button>
                    </span>
                </div>
                <div class="btn-con" v-if="showMoreIsVisible">
                    <ui-button @click="showMore" color="success">Show More</ui-button>
                </div>
            </div>
        </ui-sidebar>
    </div>
</template>

<script>
import UiSidebar from '../../common/UiSidebar.vue';
import {APP_URL, profile} from '../../../js/config.js';
import helper from '../../../js/helpers.js';
import Cookie from 'js-cookie';

export default {
    name: 'staff-doc-share',
    data() {
        return {
            eventPrefix: 'staffdocshare:',
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            parsedDateTime: '',
        }
    },
    props: {
        staffProfile: {
            type: Object,
            default: function() {
                return profile
            }
        },
        docsList: {
            type: Array,
            default: function() {
                return []
            }
        },
        viewDocAllowed: {
            type: Boolean,
            default: false
        },
        meetingMenu: {
            type: Array,
            default: function() {
                return []
            }
        },
        showMoreIsVisible: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        getStaffAvatar() {
            if(this.staffProfile.pic_url) {
                return this.staffProfile.pic_url;
            }
            return this.static_images.avatar;
        },
        getStaffFullName() {
            return `${this.staffProfile.first_name} ${this.staffProfile.last_name}`;
        }
    },
    methods: {
        getDocLink(doc) {
            let dateHolder = new Date();
            dateHolder.setMinutes(dateHolder.getMinutes() + 1);
            this.parsedDateTime = [Cookie.get('peerId'), dateHolder.getTime()].join('-');
            return `${APP_URL}/${this.parsedDateTime}/document/${doc.document_id}/download/${doc.file_name}`
            //return `${APP_URL}/document/${doc.document_id}/download/${doc.file_name}`
        },
        downloadDoc(doc) {
            helper.downloadFile(this.getDocLink(doc))
        },
        viewDoc(doc) {
            this.$dispatch(this.eventPrefix+'viewdoc', this, doc)
        },
        shareDoc(doc) {
            this.$dispatch(this.eventPrefix+'sharedoc', this, this.getDocLink(doc), doc.size)
        },
        showMore() {
            this.$dispatch(this.eventPrefix+'showmore', this)
        }
    },
    events: {
        'option-selected' (option) {
            this.$dispatch(this.eventPrefix+'selected:'+option.id, option)
        }
    },
    components: {
        UiSidebar
    }
}
</script>

<style lang="sass">
.drop-content {
    min-width: 200px;
    .doc-item {

    }
}
</style>

