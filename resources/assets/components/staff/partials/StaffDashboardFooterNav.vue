<template>


    <div class="footer" v-bind:class="[footer ? 'hide' : 'show' ]" v-if="showToolbar">
        <img src="/image/mee2box-white.png" alt="logo" class ="logo">
     
      <ul class="footer_menu" slot="actions">
         <!-- <li>
          <a href = "#">
              <span class="menu-icon assignment" @click="toggleCam"></span><br>
              <span class="menu-text">ウェブカメラ</span>
          </a>
        </li> -->
        <li class="menu-list" v-if="displaySalesMemo" @click="toggleMemo" v-bind:class="[memo ? 'isActive' : '' ]" v-bind:style="minwidth">
          <a href="#">
              <span class="menu-icon assignment"></span><br>
              <span class="menu-text">商談メモ</span>
          </a>
        </li>
        <li class="menu-list" v-if="displayClientUpload" @click="fileupload"  v-bind:style="minwidth">
          <a href = "#">
              <span class="menu-icon description"></span><br>
              <span class="menu-text">資料共有</span>
          </a>
        </li>
        <li class="menu-list" v-if="displayDocShare"  @click="toggleDocShare" v-bind:class="[docshare ? 'isActive' : '' ]" v-bind:style="minwidth">
          <a href = "#">
              <span class="menu-icon description"></span><br>
              <span class="menu-text">資料共有</span>
          </a>
        </li>
        <li class="menu-list"  @click="toggleSubtitle" v-bind:class="[subtitle ? 'isActive' : '' ]" v-bind:style="minwidth"><a href = "#">
              <span class="menu-icon subtitle"></span><br>
              <span class="menu-text">議事録</span>
            </a>
        </li >
        <li class="menu-list" @click="toggleUrl" v-bind:class="[url ? 'isActive' : '' ]" v-bind:style="minwidth"><a href = "#">
              <span class="menu-icon url"></span><br>
              <span class="menu-text">チャット招待</span>  
            </a>
        </li>
        <li class="menu-list" @click="toggleScreenShare" v-bind:class="[screenshare ? 'isActive' : '' ]" v-bind:style="minwidth"><a href = "#">
              <span class="menu-icon tv"></span><br>
              <span class="menu-text">画面共有</span>  
             </a>
        </li>
        <li class="menu-list" @click="toggleVidShare" v-bind:class="[vidshare ? 'isActive' : '' ]" v-bind:style="minwidth">
          <a href = "#">
              <span class="menu-icon video-call" ></span><br>
              <span class="menu-text">ビデオ共有</span>  
            </a>
        </li>
        <li class="menu-list" @click="toggleNoteShare" v-bind:class="[note ? 'isActive' : '' ]" v-bind:style="minwidth"><a href = "#" >
              <span class="menu-icon assignment" ></span><br>
              <span class="menu-text">ノート機能</span>  
            </a>
        </li>
        <li class="menu-list" @click="redirectToDashboard">
          <a href="">
              <span class="menu-icon close" ></span><br>
              <span class="menu-text">接続終了</span> 
          </a>
        </li>
      </ul>
  
    </div>
  

    <!-- <section v-if="showToolbar" class="section section-ui-toolbar" id="staff-dashboard-footer">
        <ui-toolbar type="colored" text-color="white">
            <div class="ui-toolbar-title appLogo">
                <img class="imgResponsive" :src="static_images.logo_white" v-cloak>
            </div>
            <div slot="actions"> -->
                <!-- <div class="menu-item" @click="toggleCam">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon mee2box-webcam">Webcam</span><br> 
                        <span class="menu-item-text">ウェブカメラ</span>
                    </a>
                </div> -->
               <!--  <div v-if="displaySalesMemo" class="menu-item" @click="toggleMemo" v-bind:class="[memo ? 'isActive' : '' ]">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon assignment">Memo</span><br>
                        <span class="menu-item-text">商談メモ</span>
                    </a>
                </div>

                <div v-if="displayClientUpload" class="menu-item" @click="fileupload" v-bind:class="[file ? 'isActive' : '' ]">
                     <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon description">description</span><br>
                        <span class="menu-item-text">資料共有</span>
                    </a>
                </div>

                <div v-if="displayDocShare" class="menu-item" @click="toggleDocShare" v-bind:class="[docshare ? 'isActive' : '' ]">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon description">description</span><br>
                        <span class="menu-item-text">資料共有</span>
                    </a>
                </div> -->

                <!-- <div class="menu-item" @click="toggleSubtitle" v-bind:class="[subtitle ? 'isActive' : '' ]">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon mee2box-subtitle">Subtitle</span><br> --> <!-- .mee2box-custom-icon
                        <span class="menu-item-text">議事録</span>
                    </a>
                </div> -->

                <!-- New added icon -->

                <!--<div class="menu-item" @click="toggleUrl" v-bind:class="[url ? 'isActive' : '' ]">

                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon icon-url"></span><br>
                        <span class="menu-item-text">チャット招待</span>
                    </a>
                </div> -->
                <!-- New added icon -->


                <!--<div class="menu-item" @click="toggleScreenShare" v-bind:class="[screenshare ? 'isActive' : '' ]">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon tv">tv</span><br>
                        <span class="menu-item-text">画面共有</span>
                    </a>
                </div>

                <div class="toggle-vid-share menu-item" @click="toggleVidShare" v-bind:class="[vidshare ? 'isActive' : '' ]">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon video-call-icon"></span><br>
                        <span class="menu-item-text">ビデオ共有</span>
                    </a>
                </div>

                <div class="menu-item" @click="toggleNoteShare" v-bind:class="[note ? 'isActive' : '' ]" >
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon assignment">assignment</span><br>
                        <span class="menu-item-text">ノート機能</span>
                    </a>
                </div> -->

                <!-- <div v-if="displayAnnotation" class="toggle-annotation menu-item" @click="toggleAnnotation">
                    <a href="#" class="center-xs">
                        <ui-icon icon="assignment"></ui-icon><br>
                        <span class="menu-item-text">メモ</span>
                    </a>
                </div> -->
                
              <!--   <div class="menu-item" v-if="false" @click="toggleAnnotation" v-bind:class="[annotation ? 'isActive' : '' ]">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon edit">edit</span><br>
                        <span>マニュアル</span>
                    </a>
                </div>

                <div class="menu-item close-button-item" @click="redirectToDashboard">
                    <a href="#" class="center-xs">
                        <span class="mee2box-custom-icon close">close</span><br>
                        <span class="menu-item-text">接続終了</span>
                    </a>
                </div>
                
            </div>
        </ui-toolbar>
    </section>
 -->
</template>
<script>
    import browser from './../../../js/browser_detect.js';
    import {APP_URL} from './../../../js/config';
    import helper from './../../../js/helpers.js';
    import Cookie from 'js-cookie';

    export default {
        name: 'staff-dashboard-footer-nav',
        props: {
            displayDocShare : {
                type: Boolean, 
                default: true
            },
            displayAnnotation : {
                type: Boolean, 
                default: true
            },
            showToolbar : {
                type: Boolean,
                default: true
            },
            displayClientUpload: {
                type: Boolean,
                default: false
            },
            displaySalesMemo: {
                type: Boolean,
                default: true
            },
        },
        data() {
            return {
                auth: {
                    user: {
                        role: 'staff'
                    }
                },
                static_images: {
                    logo_white: `${APP_URL}/image/mee2box-white.png`,
                    logo_dark: `${APP_URL}/image/mee2box-darkcyan.png`
                },
                is_mobile_view: true,
                size: 'normal',
                footer: true,
                annotation: true,
                memo: true,
                file: true,
                subtitle: true,
                docshare: false,
                file: false,
                url: false,
                screenshare: false,
                vidshare: true,
                note: true,
                minwidth: '',
                browserName: browser.specs().browser.name,
            };
        },
        methods: {
            toggleMemo(e){
                try{
                this.$emit('toggle-memo');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Memo'}
                let functionName = 'StaffDashboardFooterNav:toggleMemo';
                helper.catchError(errorStats, functionName);
                }
            },
            toggleCam(e){
                this.$emit('toggle-webcam');
                e.preventDefault();
            },
            toggleUrl(e){
                try{
                this.$emit('toggle-url');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Url'}
                let functionName = 'StaffDashboardFooterNav:toggleUrl';
                helper.catchError(errorStats, functionName);
                }
            },
            toggleSubtitle(e){
                try{
                this.$emit('toggle-subtitle');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Subtitle'}
                let functionName = 'StaffDashboardFooterNav:toggleSubtitle';
                helper.catchError(errorStats, functionName);
                }
            },
            toggledocumentLayout(e){
                this.$emit('toggle-document-layout');
                e.preventDefault();
            },
            fileupload(e){
                try{
                this.$emit('client-upload-file');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> File upload'}
                let functionName = 'StaffDashboardFooterNav:fileupload';
                helper.catchError(errorStats, functionName);
                }
            },
            /**
             * Toggle Video Sharing.
             */
            toggleVidShare(e) {
                try{
                this.$emit('toggle-vid-share');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Video Sharing'}
                let functionName = 'StaffDashboardFooterNav:toggleVidShare';
                helper.catchError(errorStats, functionName);
                }
            },
            /**
             * Toggle Screen Sharing.
             */
            toggleScreenShare(e) {
                try{
                this.$emit('toggle-screen-share');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Screen Sharing'}
                let functionName = 'StaffDashboardFooterNav:toggleScreenShare';
                helper.catchError(errorStats, functionName);
                }
            },
            /**
             * Toggle Annotation Sharing.
             */
            toggleAnnotation(e) {
                this.$emit('toggle-annotation');
                e.preventDefault();
            },
            /**
             * Toggle Note Sharing.
             */
            toggleNoteShare(e) {
                try{
                this.$emit('toggle-note-share');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Note Sharing'}
                let functionName = 'StaffDashboardFooterNav:toggleNoteShare';
                helper.catchError(errorStats, functionName);
                }
            },
            /**
             * Toggle Document Sharing.
             */
            toggleDocShare(e) {
                try{
                this.$emit('toggle-doc-share');
                e.preventDefault();
                } catch(a) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': a, 'ErrorMsgFromPage':'Staff Name: ' + Cookie.get('ParentNameHost') + '\n Object doesnt support this action -> Toggle Document Share'}
                let functionName = 'StaffDashboardFooterNav:toggleDocShare';
                helper.catchError(errorStats, functionName);
                }
            },
            /**
             * Terminate meeting session and redirect to dashboard.
             *
             * @param e
             */
            redirectToDashboard(e) {
                this.$emit('redirect-to-dashboard');
                e.preventDefault();
            }
        },
        components:{
        }
    }
</script>
<style scoped lang="scss">

.footer{
 width: 100%;
 position: fixed;
 background: #3cb371;
 padding: 0;
 color: #fff;
 bottom: 0;
 display: flex;
//display: none;
 z-index:9999;

 .logo{
   width: 245px;
   max-height: 90%;
   padding: 24px;
   margin-right: 15%;
   margin-top: 3px;


 }

 .footer_menu{
   color: #fff;
   list-style-type: none;
   margin: 0;
   width: 80%;
   margin-top: 0;
   .menu-list{
     position: relative;
     display: table-cell;
     width: 1%;
     border-left: 3px solid rgba(0, 0, 0, 0.15);
     a{
       font-size: 10px;
       font-family: 'NotoSans-Regular';
       display: inline-block;
       padding: 15px 0px;
       width: 100%;
       height: 100%;
       text-align: center;

       .menu-text{
          color: #ffffff;
       }

       .menu-icon{
         background: url('/image/toolbar-sprite.png') no-repeat scroll 5px 0 transparent;
         height: 43px;
         width: 43px;
         display: inline-block;
         text-indent: 9999px;
         //margin-bottom: 10px;
           &.tv { background-position-x: -104px; }
           &.assignment {  background-position-x: -211px;}
           &.edit { background-position-x: -327px; }
           &.close {  background-position-x: -447px; background-position-y: -45px;}
           &.subtitle {
             background-position-x: -634px;
             background-position-y: -17px;
           }
           &.url {
             background-position-x: -779px;
             background-position-y: 0px;//-22px;
           }
           &.video-call{
             background-position-x: -706px;
             background-position-y: -20px;
           }
             margin-bottom: 0.5rem;
       }
       .url{
         filter: brightness(0) invert(1);
       }

     }
   }
 }

 .menu-list:hover {
   .menu-icon {
      &.close {background-position-y: -45px;}
   }
}

 .menu-list:hover{
   background:#ff9600;


 }
 // .logo img{
 //   height: 100px;
 // }
}//end


.section-ui-popover {
    .link-trigger {
        font-size: 16px;
        cursor: pointer;
    }
}
.ui-popover {
    p {
        font-size: 1.5rem;
        text-align: center;
    }
}
.ui-textbox{
    margin-bottom: 0px;
}
.drop-content .ui-popover {
     padding: 16px !important; 
}
    .page--ui-fileupload {
        .ui-radio-group {
            margin-bottom: rem-calc(16px);
        }
        .page__demo-group {
            display: flex;
            flex-wrap: wrap;
            .ui-fileupload {
                margin-right: 8px;
            }
        }
        .page__demo-preview {
            display: table;
            border: 1px solid #DDD;
            margin-top: 8px;
            padding: 4px;
            line-height: 1;
        }
        .page__demo-preview-image {
            min-width: 24px;
            max-width: 256px;
        }
    }
    #staff-dashboard-footer {
        bottom: 0;
        position: fixed;
        width: 100%;
        z-index: 999;
        .ui-toolbar {
            height: 100px;
            .ui-toolbar-title.appLogo img {  max-height: 50px;  }
        }
        .menu-item {
            text-align: center;
            width: 120px;
            border-left: 3px solid rgba(0, 0, 0, 0.15);
            padding: 12px 0; //16px
            .mee2box-custom-icon {
                transition: unset;
                background: url('../image/toolbar-sprite.png') no-repeat scroll 5px 0 transparent;
                height: 50px;
                width: 50px;
                display: inline-block;
                text-indent: 9999px;
                margin-bottom: 10px;
                &.tv { background-position-x: -104px; }
                &.assignment {  background-position-x: -211px;}
                &.edit { background-position-x: -327px; }
                &.close {  background-position-x: -447px; background-position-y: -45px; }
            }
            .mee2box-webcam{
                background: url(../image/webcam.png) no-repeat;
                height: 50px;
                width: 50px;
                background-position: center;
                margin-bottom: 10px;
                text-indent: 9999px;
                display: inline-block;
            }
            .mee2box-subtitle{
                background: url(../image/subtitle.png) no-repeat;

    background-size: 100% 100% !important;
                // height: 43px;
                // width: 43px;
                // background-position: center;
                // margin-bottom: 10px;
                // text-indent: 9999px;
                // display: inline-block;
            }
            a {
                font-size: 14px;
                color: #ffffff;
                font-family: 'NotoSans-Regular';
            }
        }
        
        .toggle-vid-share i,
        .toggle-annotation i {
            font-size: 45px;
            margin-bottom: 8px; 
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
             -khtml-user-select: none; /* Konqueror HTML */
               -moz-user-select: none; /* Firefox */
                -ms-user-select: none; /* Internet Explorer/Edge */
                    user-select: none; /* Non-prefixed version, currently
                                          supported by Chrome and Opera */
        }
        .close-button-item:hover {
             .mee2box-custom-icon {
                    &.close {   background-position-y: -45px;
                    }
                   
             }
        }
        .menu-item:hover {
            background-color: #ff9600;
            border-left: 3px solid #ff9600;
            cursor: pointer;
        }
    }

</style>

<style lang="scss">

    .icon-url {
    background: url(../image/icon-url.png) !important;
    background: no-repeat;
    background-size: 100% 100% !important;
    webkit-filter: contrast(4) invert(1);
    -moz-filter: contrast(4) invert(1);
    -o-filter: contrast(4) invert(1);
    -ms-filter: contrast(4) invert(1);
    -webkit-filter: contrast(4) invert(1);
    filter: contrast(4) invert(1);
    -webkit-filter: brightness(0) invert(1);
    filter: brightness(0) invert(1);
    }

    .video-call-icon {
    background: url(../image/video_call_icon.png) no-repeat !important;
    background: no-repeat;
    background-size: 100% 65% !important;
    position: relative;
    top: 5px;
    // webkit-filter: contrast(4) invert(1);
    // -moz-filter: contrast(4) invert(1);
    // -o-filter: contrast(4) invert(1);
    // -ms-filter: contrast(4) invert(1);
    // -webkit-filter: contrast(4) invert(1);
    // filter: contrast(4) invert(1);
    // -webkit-filter: brightness(0) invert(1);
    // filter: brightness(0) invert(1);
    }

.header-url{
   .col-sm-2,.col-sm-10{
       padding:0;
   }
   .url-modal-header-text{
       font-size: 23px;
       color: #3cb371;
       font-family: 'NotoSans-Bold';
   }
   .url-icon-color{
            width: 12%;
    margin-right: 21px;
   //     width: 60px;
   // padding-left: 20px;
   // padding-top: 8px;
   }
}
.url-link{
        margin-bottom: 4rem;
   .url-link-text{
    color: #3cb371 !important;
   padding: 10px;
   font-size: 14px;
   font-weight: 600;
   background: #f2f2f2;
   border: none;
   width: 100%;
   }
}
.ui-modal-header {
       padding: 15px 10px !important;
       text-align: center;
      display: inline !important;
}

/* m2b-81 */
.header-record{
   .col-sm-2,.col-sm-10{
       padding:0;
   }
   .url-modal-header-text{
       font-size: 20px;
       color: #3cb371;
       font-family: 'NotoSans-Bold';
   }
}
.ask-record{
    margin-bottom: 2rem;
    z-index: 701;
    .ask-record-text{
        /* color: #3cb371 !important; */
        border-radius: 20px 20px;
        color: darkblue !important;
        padding: 10px;
        font-size: 12px;
        font-weight: 600;
        background: #f2f2f2;
        border: none;
        width: 100%;
        display: block;
    }
   .ask-record-button{
        margin-top: 2rem !important;
        font-size: 12px !important;     
    }
}
/* m2b-81 */

@media only screen and (max-width:360px){
   .col-sm-2 {
       -ms-flex: 0 0 25.333333%;
       flex: 0 0 25.333333%;
       max-width: 25.333333%;
   }
   .col-sm-10 {
   -ms-flex: 0 0 69.333333%;
   flex: 0 0 69.333333%;
   max-width: 69.333333%;
}
.url-modal-header-text{
       font-size: 15px!important;
   }
   .url-icon-color{
       // width: 55px!important;
       // padding-left: 20px!important;
       // padding-top: 5px!important;
        width: 9%;
    margin-right: 21px;
   }
}
@media only screen and (min-width:361px) and (max-width:580px){
   .col-sm-2 {
       -ms-flex: 0 0 30.333333%;
       flex: 0 0 30.333333%;
       max-width: 30.333333%;
   }
   .col-sm-10 {
   -ms-flex: 0 0 69.333333%;
   flex: 0 0 69.333333%;
   max-width: 69.333333%;
}
.url-modal-header-text{
       font-size: 18px!important;
   }
   .url-icon-color{
       // width: 80px!important;
       // padding-left: 40px!important;
       // padding-top: 5px!important;
        width: 10%;
    margin-right: 21px;
   }
}
@media only screen and (min-width:580px) and (max-width: 1300px){
   .url-modal-header-text{
       font-size: 20px!important;
   }
   .url-icon-color{
       // width: 85px!important;
       // padding-left: 40px!important;
       // padding-top: 5px!important;
        width: 10%;
    margin-right: 21px;
   }
}
@media only screen and (max-width:360px){
   .col-sm-2 {
       -ms-flex: 0 0 25.333333%;
       flex: 0 0 25.333333%;
       max-width: 25.333333%;
   }
   .col-sm-10 {
   -ms-flex: 0 0 69.333333%;
   flex: 0 0 69.333333%;
   max-width: 69.333333%;
}
.url-modal-header-text{
       font-size: 15px!important;
   }
   .url-icon-color{
       // width: 55px!important;
       // padding-left: 20px!important;
       // padding-top: 5px!important;
        width: 10%;
    margin-right: 21px;
   }
}
@media only screen and (min-width:361px) and (max-width:580px){
   .col-sm-2 {
       -ms-flex: 0 0 30.333333%;
       flex: 0 0 30.333333%;
       max-width: 30.333333%;
   }
   .col-sm-10 {
   -ms-flex: 0 0 69.333333%;
   flex: 0 0 69.333333%;
   max-width: 69.333333%;
}
.url-modal-header-text{
       font-size: 18px!important;
   }
   .url-icon-color{
       // width: 80px!important;
       // padding-left: 40px!important;
       // padding-top: 5px!important;
        width: 10%;
    margin-right: 21px;
   }
}
@media only screen and (min-width:580px) and (max-width: 1300px){
   .url-modal-header-text{
       font-size: 20px!important;
   }
   .url-icon-color{
       // width: 85px!important;
       // padding-left: 40px!important;
       // padding-top: 5px!important;
       width: 12%;
    margin-right: 21px;
   }
}
@media only screen and (min-width:515px){
    .url-link{
        // margin: 0 -45px 3rem;
    }
}

  

@media screen and (max-width : 767px) and (orientation : landscape){

  .header-url .url-icon-color {
      width: 9%!important;
      margin-right: 21px;
  }

  .proto .ui-modal-mask .ui-modal-container{
      width: 60% !important;
  }

}

@media screen and (min-width : 768px) and (max-width : 1024px)and (orientation : landscape){

  .footer .footer_menu .menu-list a .menu-text{
      font-size: 8px !important;
  }

}//end

@media screen and (min-width : 768px) and (max-width : 1024px)and (orientation : portrait){

  .footer .footer_menu .menu-list a .menu-text{
      font-size: 8px !important;
  }

}//end


</style>