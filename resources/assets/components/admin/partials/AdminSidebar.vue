<template>
    <ui-sidebar class="protoSidebar">
        <div slot="top-con" class="avatarCon">
            <div class="avatar" v-el:user-avatar>
                <img class="imgResponsive" v-bind:src="getAvatar" v-cloak>
                <ui-ripple-ink :trigger="$els.userAvatar"></ui-ripple-ink>
            </div>
            <p class="userFullName" v-if="auth.user.authenticated">{{ auth.user.profile.last_name }} </p>
            <ui-button v-link="{ name: 'admin-edit-account' }" color="primary" icon="edit">アカウント更新</ui-button>
        </div>
        <div slot="bottom-con">
            <ul class="sidebarMenu">
                <li>
                    <a v-link="{ name: 'admin-dashboard' }" v-el:anchor-dash>
                        <ui-icon class="primary material-icons-new icon-white outline-dashboard"></ui-icon>ダッシュボード
                        <ui-ripple-ink :trigger="$els.anchorDash"></ui-ripple-ink>
                    </a>
                </li>
                <li>
                    <a v-link="{ name: 'manage-staff' }" v-el:anchor-staff>
                        <ui-icon class="primary material-icons-new icon-white outline-assignment"></ui-icon>ユーザー管理
                        <ui-ripple-ink :trigger="$els.anchorStaff"></ui-ripple-ink>
                    </a>
                </li>
                <li>
                    <a v-link="{ name: 'manage-notices' }" v-el:anchor-notices>
                        <ui-icon class="primary material-icons-new icon-white outline-new_releases"></ui-icon>通知管理
                        <ui-ripple-ink :trigger="$els.anchorNotices"></ui-ripple-ink>
                    </a>
                </li>
            </ul>
        </div>
    </ui-sidebar>
</template>

<script>
import UiSidebar from '../../common/UiSidebar.vue';
import {APP_URL} from '../../../js/config.js';
import helper from '../../../js/helpers.js';

export default {
    name: 'admin-sidebar',
    data()
    {
        return {
            auth: this.$parent.auth,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            }
        }
    },
    computed: {
        getAvatar() {
            try {
                if(this.auth.user.authenticated && this.auth.user.profile.pic_url) {
                    return this.auth.user.profile.pic_url;
                }
                return this.static_images.avatar;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the avatar'}
                let functionName = 'AdminSidebar:getAvatar';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    components: {
        UiSidebar
    }
}
</script>

