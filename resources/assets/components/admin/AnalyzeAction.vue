<template>
    <div class="protoDashboard">
        <!-- <admin-sidebar v-ref:sidebar></admin-sidebar> -->
        <staff-sidebar v-ref:sidebar ></staff-sidebar>
        <div class="dashboardContent" v-el:adj-container>
            <div class="wrapper">

                         <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <h1>
                                    <br><br>
                                    <span>顧客接続状況</span>
                                    <br><br>
                                    </h1>
                                </div>
                            </div>
                         </div>

                          <div class="col-md-12">
                            <div class="row">                 
                                <div class="col-md-6">
                                  <div class="pdashForm1">
                                      <div class="col-md-3 col-sm-3 cbox" v-for="days in filterDays" v-bind:key="days">
                                       <label>{{days}} 日以上未ログイン<br>
                                           <input type="checkbox" v-model="search.differ" v-bind:value="days" v-onclick>
                                           <span class="checkmark"></span>
                                       </label>
                                      </div> 

                                      <div class="col-md-3 col-sm-3 cbox" v-for="noconnect in filterConnection" v-bind:key="noconnect">
                                       <label>接続有<br>
                                           <input type="checkbox" v-model="search.nullConnect" v-bind:value="noconnect" v-onclick>
                                           <span class="checkmark"></span>
                                       </label>
                                      </div>

                                      <div class="col-md-3 col-sm-3 cbox" v-for="nologin in filterLogin" v-bind:key="nologin">
                                       <label>ログイン有<br>
                                           <input type="checkbox" v-model="search.nullLogin" v-bind:value="nologin" v-onclick>
                                           <span class="checkmark"></span>
                                       </label>
                                      </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                  <div class="pdashForm2">
                                    <input class="input1" type="text" v-model="search.company" placeholder="Search Company">
                                    <input class="input2" type="text" v-model="search.name" placeholder="Search Fullname">
                                  </div>
                                </div>
                            </div>
                          <br><br>
                        </div>

                <div class="col-md-12">
                  <div class="row">
                        <ui-table
                            :api-url="staff_table.api_url"
                            :data-key="staff_table.data_key" 
                            :fields="staff_table.fields"
                            :actions="staff_table.actions"
                            :no-content-text="staff_table.no_content_text"
                            :search-company="search.company"
                            :search-name="search.name"
                            :search-differ="search.differ"
                            :search-login="search.nullLogin"
                            :search-connect="search.nullConnect"
                            :analyze-action="true"
                        ></ui-table>
                </div>  
            </div>
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
import {UiCheckbox,UiButton,UiTabs,UiTab} from 'keen-ui';

export default {
    data() {
        return {
            filterLogin:[1],
            filterConnection:[1],
            filterDays: [3,7],
            search: {
                company: '',
                name: '',
                nullLogin:[],
                nullConnect:[],
                differ: []
            },
            logins: [],
            auth: this.$parent.auth,
            APP_URL,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`
            },
            staff_table: {
                api_url: ADMIN_MANAGE_STAFF_RESOURCE,
                data_key: `staff`,
                fields: [
                    { name: 'profile.full_name', label: '顧客名', width: 25,sortby: 0 },
                    { name: 'profile.company', label: '会社名', width: 20 ,sortby: 1},
                    { name: 'profile.differ', label: 'ログイン', width: 20 ,sortby: 2},
                    { name: 'profile.last_login', label: '最終ログイン', width: 20, function: 'last_login',sortby: 3},
                    { name: 'profile.last_connection', label: '最終接続', width: 20, function: 'lastCon',sortby: 4 }
                ],
                actions: [
                    { name: 'edit-item', label: '', icon: 'search', icon_color: 'black ', 
                    routelink: { name: 'admin-staff-profile', params: { id: 'staff_id' } } },
                ],
                no_content_text: `There are no staff yet.`
            },
            current_item: {}
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
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the analyze action component'}
            let functionName = 'AnalyzeAction:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    components: {
        // AdminSidebar,
        StaffSidebar,
        UiTable,
        
    }
}
</script>

<style lang="scss" scoped>
    .pdashForm1{
           text-align: left;

        .cbox{
            display: inline-block;
            text-align: center;
            font-size: 13px;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            padding:5px;
            // width:100px;
            // label{
            //     position: absolute;
            //     top: -18px;
            // }

            input {
              position: absolute;
              opacity: 0;
              cursor: pointer;
              height: 0;
              width: 0;
            }
        }
        .checkmark {
            position: absolute;
            top:25px;
            /* left: -18px; */
            height: 25px;
            width: 25px;
            background: #f3f3f3;
            border-radius: 5px;
            cursor: pointer;
        }
        .cbox:hover input ~ .checkmark {
          background-color: #ccc;
        }

        .cbox input:checked ~ .checkmark {
          background-color: #3B5998;
        }

        .checkmark:after {
          content: "";
          position: absolute;
          display: none;
        }

        .cbox input:checked ~ .checkmark:after {
          display: block;
        }

        .cbox .checkmark:after {
            left: 10px;
            top: 4px;
            width: 6px;
            color:gray;
            height: 13px;
            border: solid white;
            border-width: 0 3px 3px 0;
          -webkit-transform: rotate(45deg);
          -ms-transform: rotate(45deg);
          transform: rotate(45deg);
        }
    }

    .pdashForm2{
      text-align: center;
            padding-top: 25px;

        .input1{
            margin-right: 17px;
        }

        .input1,.input2{
            padding: 5px;
            width: 45%;
        }
        input[type="text"]{
           background: #f3f3f3;
           border: 1px solid #aec8ff;
           border-radius: 5px;
        }

        input[type="text"]:focus{
            outline:none;
            border: 1px solid #3B5998;
            box-shadow: 0 0 5px rgba(81, 203, 238, 1);
        }

    }


//Desktop screens
@media (min-width: 1150px) and (max-width: 1385px){
  /* cannot be text truncate, the label used in for loop along with checkboxes*/
 //.cbox{
 //   white-space: nowrap;
 //   overflow: hidden;
 //   text-overflow: ellipsis;
 // }
  .cbox{
    font-size:9px!important;
  }
}//end
@media (min-width: 1087px) and (max-width: 1149px){
  .cbox{
    font-size:9px!important;
  }
}//end
@media (min-width: 1024px ) and (max-width: 1086px){
      .cbox{
        font-size:8px!important;
      }
}//end


/* 
  ##Device = Tablets, Ipads (portrait)
  ##Screen = B/w 768px to 1023px
*/
@media (min-width: 768px) and (max-width: 1023px){
  .pdashForm1 .cbox{
    padding-bottom:30px;
  }
  .pdashForm2{
    padding-top:40px;
  }
}//end

/* 
  ##Device = Low Resolution Tablets, Mobiles (Landscape)
  ##Screen = B/w 481px to 767px
*/
  @media (min-width: 446px) and (max-width: 767px) {
  
  .cbox{
    font-size:9px!important;
  }
  .col-sm-3{
    width:25%!important;
  }
  .col-md-3{
    width:25%!important;
  }
  .col-sm-6{
    width:50%!important;
  }
  .col-md-6{
    width:100%!important;
  }
  .pdashForm2{
    padding-top:40px;
  }
  .pdashForm1 .cbox{
    padding-bottom:30px;
  }
  
}

/* 
  ##Device = Most of the Smartphones Mobiles (Portrait)
  ##Screen = B/w 320px to 479px
*/
@media (min-width: 320px) and (max-width: 445px){

  .cbox{
    font-size:10px!important;
  }
  .pdashForm1 .checkmark{
    top:40px;
  }
  .col-sm-3{
    width:25%!important;
  }
  .col-md-3{
    width:25%!important;
  }
  .col-sm-6{
    width:50%!important;
  }
  .pdashForm2{
    padding-top:40px;
  }
  .pdashForm1 .cbox{
    padding-bottom:30px;
  }
}

//Col-md
.col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col,
.col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm,
.col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md,
.col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg,
.col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl,
.col-xl-auto {
  position: relative;
  width: 100%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}
.col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12{
    float: left;
}
</style>