<template>
    <!-- <div class="sideContent"> -->
    <ui-sidebar class="protoSidebar staffSidebar">
        <div slot="top-con" class="avatarCon">
            <div class="avatar" v-el:user-avatar>
                <img class="imgResponsive" v-bind:src="getAvatar" v-cloak>
                <ui-ripple-ink :trigger="$els.userAvatar"></ui-ripple-ink>
            </div>
            <p class="userFullName" v-if="auth.user.authenticated">{{ auth.user.profile.last_name }} </p>
        </div>
         <div slot="bottom-con">
            
            <ul class="sidebarMenu">
               <li>
                   <a v-link="{ name: 'staff-dashboard' }" v-el:anchor-dash>
                       <!-- <ui-icon class="primary" icon="voice_chat"></ui-icon>商談を開始する -->
                       <ui-icon icon="" class="primary material-icons-new icon-white icon-voice_chat"></ui-icon>商談を開始する
                       <ui-ripple-ink :trigger="$els.anchorDash"></ui-ripple-ink>
                   </a>
               </li>
               <li>
                   <a v-link="{ name: 'staff-documents' }" v-el:anchor-documents>
                      <!--  <ui-icon class="primary" icon="description"></ui-icon>資料 -->
                       <ui-icon icon="" class="primary material-icons-new icon-white icon-file"></ui-icon>資料を設定する
                       <ui-ripple-ink :trigger="$els.anchorDocuments"></ui-ripple-ink>
                   </a>
               </li>
               <li>
                   <a v-link="{ name: 'staff-negotation-history' }" v-el:anchor-history>
                       <!-- <ui-icon class="primary" icon="history"></ui-icon>商談履歴 -->
                       <ui-icon icon="" class="primary material-icons-new icon-white outline-history"></ui-icon>商談履歴の確認
                       <ui-ripple-ink :trigger="$els.anchorHistory"></ui-ripple-ink>
                   </a>
               </li>
               <li>
                   <!-- <a v-link="{ name: 'staff-notices' }" v-el:anchor-notices>
                       <ui-icon class="primary" icon="notifications"></ui-icon>お知らせ
                       <ui-ripple-ink :trigger="$els.anchorNotices"></ui-ripple-ink>
                   </a> -->
                   <a v-link="{ name: 'staff-notices' }" v-el:anchor-notices>
                       <!-- <ui-icon class="primary" icon="notifications"></ui-icon>お知らせ -->
                       <mark v-if="notifCount > 0">{{ notifCount }}</mark>
                       <ui-icon icon="" class="primary material-icons-new icon-white outline-email"></ui-icon>お知らせをみる
                       <ui-ripple-ink :trigger="$els.anchorNotices"></ui-ripple-ink>

                   </a>
               </li>
               <li>
                   <a v-link="{ name: 'sales-main' }" v-el:anchor-sales>
                       <ui-icon icon="" class="primary material-icons-new icon-white icon-file"></ui-icon>商談メモを設定
                       <ui-ripple-ink :trigger="$els.anchorSales"></ui-ripple-ink>
                   </a>
               </li>
                <li v-if="admin">
                   <a v-link="{ name: 'analyze-action' }" v-el:anchor-analyze>
                       <ui-icon icon="" class="primary material-icons-new icon-white outline-people_outline"></ui-icon>顧客接続状況
                       <ui-ripple-ink :trigger="$els.anchorAnalyze"></ui-ripple-ink>
                   </a>
               </li>
               <li v-if="admin">
                   <a v-link="{ name: 'manage-staff' }" v-el:anchor-staff>
                       <ui-icon icon="" class="primary material-icons-new icon-white outline-assignment"></ui-icon>ユーザー管理
                       <ui-ripple-ink :trigger="$els.anchorStaff"></ui-ripple-ink>
                   </a>
               </li>
                <li v-if="admin">
                   <a v-link="{ name: 'manage-notices' }" v-el:anchor-notices>
                       <ui-icon icon="" class="primary material-icons-new icon-white outline-new_releases"></ui-icon>通知管理
                       <ui-ripple-ink :trigger="$els.anchorNotices"></ui-ripple-ink>
                   </a>
               </li>
           </ul>
        </div>
    </ui-sidebar>
    <!-- </div> -->
</template>
<script>
import Vue from '../../../js/app.js';
import {user_service} from '../../../js/api_routes.js';
import UiSidebar from '../../common/UiSidebar.vue';
import {APP_URL} from '../../../js/config.js';
import {STAFF_NOTICES_RESOURCE} from '../../../js/api_routes.js';
export default {
    name: 'staff-sidebar',
    data() 
    {
        return {
            auth: this.$parent.auth,
            notifCount: 0,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            items: [],
            tablePagination: null,
            currentPage: 1,
            visible: false,
            dataloaded: false,
            loading: {
                main: false
            },
            admin: false,
            apiUrl: STAFF_NOTICES_RESOURCE,
            // default: {
            //     width: 25,
            //     action: {
            //         icon: 'add',
            //         icon_color: 'primary'
            //     }
            // },
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
                let functionName = 'StaffSidebar:getAvatar';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    props: {
        perPage: {
            type: Number,
            coerce: function(val) {
                return parseInt(val)
            },
            default: function() {
                return 10
            }
        },
        queryParams: {
            type: Object,
            default: function() {
                return {
                    sort: 'sort',
                    page: 'page',
                    perPage: 'per_page'
                }
            }
        },
        appendParams: {
            type: Array,
            default: function() {
                return []
            }
        },
    },
    methods: {
        loadData() {
            try {
                if(!!this.apiUrl){
                    var url = this.apiUrl + '?' + this.getQueryParams()

                    this.$http.get(
                        url
                    ).then(response => {
                        if(response.status == 200) {
                            // console.log(response)
                            this.visible = true
                            // this.items = response.body[this.dataKey]
                            let mydata = response.data.notices
                            // console.log("--------")
                            // console.log(mydata.length)
                            this.items = response.data.notices
                            // console.log(this.items)
                            // console.log("--------")
                            let x = []
                            // let a = {}
                            for (let key in mydata){
                                if(mydata[key].read == 0){
                                    x.push({read:mydata[key].read})
                                }
                            }
                            this.notifCount = x.length
                        } else {
                            this.visible = false
                        }

                        this.$nextTick(() => {
                            this.dataIsLoaded()
                        })

                    }, response => {
                        this.dataIsLoaded()
                    })
                } else {
                    console.clear()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data'}
                let functionName = 'StaffSidebar:loadData';
                helper.catchError(errorStats, functionName);
            }
        },
        getQueryParams(data) {
            try {
                var params = [
                    // this.queryParams.sort + '=' + this.getSortParam(),
                    this.queryParams.page + '=' + this.currentPage,
                    this.queryParams.perPage + '=' + this.perPage
                ].join('&')
                if (this.appendParams.length > 0) {
                    params += '&'+this.appendParams.join('&')
                }

                return params
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the query parameters'}
                let functionName = 'StaffSidebar:getQueryParams';
                helper.catchError(errorStats, functionName);
            }
        },
        addParam(param) {
            try {
                this.appendParams.push(param)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding into the parameters'}
                let functionName = 'StaffSidebar:addParam';
                helper.catchError(errorStats, functionName);
            }
        },
        dataIsLoaded() {
            try {
                this.loading.main = true;
                setTimeout( () => {
                    this.dataloaded = true
                }, 500)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking if the data is already loaded'}
                let functionName = 'StaffSidebar:dataIsLoaded';
                helper.catchError(errorStats, functionName);
            }
        },
        postClicked(item) {
            try {
                this.$dispatch(this.eventPrefix+'post:clicked', item)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Post Clicked'}
                let functionName = 'StaffSidebar:getAvatar';
                helper.catchError(errorStats, functionName);
            }
        },
        getRole(){
            try {
                // console.log('getting Role')
                Vue.http.get(
                    user_service.USER_RESOURCE,
                ).then(response => {
                    if(!!response.data.admin){
                        this.admin = true
                    }
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the user roles'}
                let functionName = 'StaffSidebar:getRole';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    events: {
        'uipost:refresh': function() {
            try {
                this.currentPage = 1
                this.loadData()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data on refresh'}
                let functionName = 'StaffSidebar::uipost:refresh';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    created() {
        try {
            this.getRole()
            this.loadData()
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Creating the data staff sidebar component'}
            let functionName = 'StaffSidebar:created';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        UiSidebar
    }
}
</script>

<style lang="scss">
   mark {
     border: 1px solid #FFF;
   border-radius: 10px;
   width: 20px;
   height: 20px;
   background-color: #ff9600;
   position: absolute;
   font-size: 10px;
   line-height: 17px;
   font-family: 'Roboto', sans-serif;
   font-weight: 400;
   color: #FFF;
   font-weight: 700;
   text-align: center;
   margin: -8px -15px;
   }

   .material-icons-new {
   display: inline-block;
   width: 24px;
   height: 24px;
   background-repeat: no-repeat;
   background-size: contain;
}

.icon-white {
   -webkit-filter: grayscale(100%) brightness(75%) sepia(100%) hue-rotate(-183deg) saturate(700%) contrast(0.8);
   filter: grayscale(100%) brightness(75%) sepia(100%) hue-rotate(-183deg) saturate(700%) contrast(0.8);
}

.proto .protoDashboard .protoSidebar.ui-sidebar .ui-sidebar-content ul.sidebarMenu li a.v-link-active .ui-icon{
   webkit-filter: contrast(4) invert(1);
   -moz-filter: contrast(4) invert(1);
   -o-filter: contrast(4) invert(1);
   -ms-filter: contrast(4) invert(1);
   filter: contrast(4) invert(1);
   filter: brightness(0) invert(1);
}
.proto .protoDashboard .protoSidebar.ui-sidebar .ui-sidebar-content ul.sidebarMenu li a .ui-icon, .proto .protoCon .protoSidebar.ui-sidebar .ui-sidebar-content ul.sidebarMenu li a .ui-icon {
   padding: 16px;
}

.outline-description {
   -webkit-transform: scaleX(-1);
   transform: scaleX(-1);
}

.sideContent {
   background-color: #f2f2f2;
   width: 260px;
   position: absolute;
   top: 0;
   bottom: 0;
   // min-height: 650px;
   // overflow: hidden;
}


.ui-sidebar .ui-sidebar-content {
   background-color: transparent!important;
   width: 280px!important;
   overflow-x: hidden!important;
}

/* width */
.ui-sidebar .ui-sidebar-content::-webkit-scrollbar {
   width: 5px;
}

/* Track */
.ui-sidebar .ui-sidebar-content::-webkit-scrollbar-track {
   box-shadow: inset 0 0 5px transparent;
   border-radius: 10px;
}

/* Handle */
.ui-sidebar .ui-sidebar-content::-webkit-scrollbar-thumb {
   background: transparent;
   border-radius: 10px;
}

/* Handle on hover */
.ui-sidebar .ui-sidebar-content::-webkit-scrollbar-thumb:hover {
   background: transparent;
}


.proto .protoDashboard .protoSidebar.ui-sidebar .ui-sidebar-content ul.sidebarMenu li a.v-link-active{
    position: initial!important;
}

.proto .protoDashboard .protoSidebar.ui-sidebar .ui-sidebar-content ul.sidebarMenu li a.v-link-active::after{
   content: "";
   position: absolute;
   right: -13px !important;
   top: auto;
   border-top: 15px solid transparent;
   border-bottom: 15px solid transparent;
   border-left: 20px solid #3B5998;
}

.proto .protoDashboard .protoSidebar.ui-sidebar .ui-sidebar-content ul.sidebarMenu{
   background: #f5f5f5!important;
}

.ui-ripple-ink {
   display:contents;
}

.icon-voice_chat{
   background-image: url('../image/icon-voice_chat.png');
}

.icon-file{
   background-image: url('../image/icon-file.png');
       background-size: 70%;
   background-position: center;
}

.icon-settings{
   background-image: url('../image/icon-settings.png')!important;
   background: no-repeat;
   background-size: 100% 100%;
   margin-right: 0.8rem;
   webkit-filter: contrast(4) invert(1);
   -moz-filter: contrast(4) invert(1);
   -o-filter: contrast(4) invert(1);
   -ms-filter: contrast(4) invert(1);
   filter: contrast(4) invert(1);
   filter: brightness(0) invert(1);
}

// .settings{
//     visibility: hidden!important;
// }

.ui-sidebar.opened .ui-sidebar-content {
   box-shadow: none!important;
}

.footer-copyright{
   // position: absolute;
   position:relative;
   bottom: 0px;
   right: 10px;
   color: #fff;
   font-family: 'texgyreadventor';
}

.footer{
   background:#f5f5f5;
   padding: 50px 0 0;
   
}

@media (max-width:1024px){
  .icon-settings {
      margin-right: 0!important;
  }

  .proto .ui-toolbar .ui-icon-button {
    width: 45px!important;
  }

}//end


</style>
