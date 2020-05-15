<template>
    <div class="col-md-12 ui-post">
        <ui-alert class="noMargin" :show="!visible && dataloaded" :dismissible="false">{{ noContentText }}</ui-alert>
        <div class="posts-container" v-if="visible && dataloaded" v-cloak>
            <!-- <div class="post-item" v-for="item in items" @click="postClicked(item)">  Remove Modal and pointers-->
             <!-- <div class="post-item" v-for="item in items">
                <div class="post">
                    <div class="details">
                        <h3 class="title"> {{ getValue(item, details.title.name, details.title.callback) }} </h3>
                        <p class="content"> {{ getValue(item, details.content.name, details.content.callback) }} </p>

                        <span class="date"> {{ getValue(item, details.date.name, details.date.callback) }} </span>

                        <div class="actions">
                            <span v-for="action in actions">
                                <ui-icon-button 
                                :icon="getActionIcon(action.icon)" 
                                :color="getActionIconColor(action.icon_color)"
                                type="flat"
                                @click="callAction(action.name, item)"
                                v-link="getRouteLink(action.routelink, item)"
                                ></ui-icon-button>
                            </span>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="row post-item" v-for="item in items" v-bind:key="item.title">
                <div class="col-sm-10 post">
                    <div class="details">
                    <!-- h3 class="title"> {{ getValue(item, details.title.name, details.title.callback) }} </h3> -->
                    <h3 class="title"> {{ item.title}} </h3>
                        <!-- <p class="content"> {{ getValue(item, details.content.name, details.content.callback) }} </p> -->

                        <div class="text-content">
                            <p class="read-more-wrap">
                                {{ item.textcont }}
                                <span v-if="item.excerpt.length > 100">
                                    <button type="button" class="viewbutton" @click="toggleSeeMore(item)" v-if="item.isShorten">
                                        <span  class="read-more-trigger" id="v{{ item.notice_id }}">全部見る</span>
                                    </button>
                                    <button type="button" class="viewbutton" @click="toggleSeeMore(item)" v-else>
                                        <span  class="read-more-trigger" id="v{{ item.notice_id }}">戻す</span>
                                    </button> 
                                </span>
                            </p>
                        </div>
                        
                        <span class="date"> {{ item.date.split('-').join('/') }} </span>

                        <div class="actions">
                            <span v-for="action in actions" v-bind:key="action.name">
                                <ui-icon-button 
                                :icon="getActionIcon(action.icon)" 
                                :color="getActionIconColor(action.icon_color)"
                                type="flat"
                                @click="callAction(action.name, item)"
                                v-link="getRouteLink(action.routelink, item)"
                                ></ui-icon-button>
                            </span>
                        </div>
                        
                    </div>
                </div>
                <div class="col-sm-2 side-avatar">
                    <div class="avatar">
                        <img class="imgResponsive DEF {{item.pic_url}} - {{ item.id }}" v-bind:src="item.pic_url">
                    </div>
                    <div class="name">
                        <p>i2m</p>
                    </div>
                    
                </div>
            </div>
        </div>
        <ui-progress-circular :show="!loading.main" color="primary"></ui-progress-circular>
    </div>
</template>

<script>
import { APP_URL } from '../../js/config.js';
import staff from '../../js/staff.js';
export default {
    name: 'ui-post',
    
    data() {
        return {
            PIC_URL : APP_URL,
            items: [],
            myData: [],
            auth: this.$parent.auth,
            static_images: {
                avatar: `${APP_URL}/image/avatar_flat.png`,
            },
            imgLoc_url: `${APP_URL}/storage/`,
            eventPrefix: 'uipost:',
            tablePagination: null,
            currentPage: 1,
            visible: false,
            dataloaded: false,
            loading: {
                main: false
            },
            default: {
                width: 25,
                action: {
                    icon: 'add',
                    icon_color: 'primary'
                }
            },
            length: '',
            isExist: ''
        }
    },
    props: {
        apiUrl: {
            type: String,
            required: true
        },
        dataKey: {
            type: String,
            required: true
        },
        details: {
            type: Object,
            required: true
        },
        actions: {
            type: Array,
            default: function() {
                return []
            }
        },
        noContentText: {
            type: String,
            required: false,
            default: function() {
                return `There are no data yet.`
            }
        },
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
        toggleSeeMore(item){
            // console.log(item)
            // console.log(this.items)
            for(let key in this.items){
                if(this.items[key].id == item.id){
                    if(item.isShorten){
                        //seee more
                        this.items[key].textcont = this.items[key].content
                        this.items[key].isShorten = false
                    }else{
                        // see less
                        this.items[key].textcont = this.items[key].excerpt
                        this.items[key].isShorten = true
                    }
                }
            }
           
        },
        loadData() {
            var url = this.apiUrl + '?' + this.getQueryParams()

            this.$http.get(
                url
            ).then(response => {
                if(response.status == 200) {
                    // console.log(response)
                    this.visible = true
                    // this.items = response.body[this.dataKey] 
                    let mydata = response.data.notices
                    // console.log("\n\n----------")
                    // console.log(mydata)
                    for(let key in mydata){
                        // let content = mydata[key].notice_details.content
                        // let title = mydata[key].notice_details.title
                        // let excerpt = content
                        // let noticeid = mydata[key].notice_id
                        // let textCont = ''
                        // let date = mydata[key].notice_details.created_at.date.split(' ')[0]

                        let content = mydata[key].content
                        let title = mydata[key].title
                        let excerpt = content
                        let noticeid = mydata[key].notice_id
                        let textCont = ''
                        let date = mydata[key].created_at.split(' ')[0]
                        let picUrl = mydata[key].pic_url

                        if (!!picUrl) {
                            // console.log("NOT NULL")
                            picUrl = this.imgLoc_url+""+picUrl;
                        } else {
                            // console.log("NULL")
                            picUrl = this.static_images.avatar;
                        }

                        if(content.length > 100){
                            excerpt = mydata[key].content.slice(0,100) + '..'
                            // excerpt = mydata[key].notice_details.content.slice(0,100) + '..'
                            let ob = {id: noticeid, content: content, excerpt: excerpt, title: title, textcont: excerpt, isShorten: true, date:date, pic_url:picUrl }
                            this.items.push(ob);
                        }else{
                            let ob = {id: noticeid, content: content, excerpt: excerpt, title: title, isShorten: false, textcont: content, date:date, pic_url:picUrl }
                            this.items.push(ob);
                        }

                        // console.log(this.items)
                    }
                } else {
                    this.visible = false
                }

                this.$nextTick(() => {
                    this.dataIsLoaded()
                })

            }, response => {
                this.dataIsLoaded()
            })

        },
        getQueryParams(data) {
            var params = [
                // this.queryParams.sort + '=' + this.getSortParam(),
                this.queryParams.page + '=' + this.currentPage,
                this.queryParams.perPage + '=' + this.perPage,

            ].join('&')
            if (this.appendParams.length > 0) {
                params += '&'+this.appendParams.join('&')
            }

            return params
        },
        addParam(param) {
            this.appendParams.push(param)
        },
        dataIsLoaded() {
            this.loading.main = true;
            setTimeout( () => {
                this.dataloaded = true
            }, 500)
        },
        getActionIcon(icon) {
            return typeof icon != 'undefined' ? icon : this.default.action.icon
        },
        getActionIconColor(icon_color) {
            return typeof icon_color != 'undefined' ? icon_color : this.default.action.icon_color
        },
        callAction(action, data) {
            this.$dispatch(this.eventPrefix+action, action, data)
        },
        getRouteLink(routelink, item) {
            if(typeof routelink != 'undefined') {
                if(typeof routelink.params != 'undefined') {
                    let routeparams = {}
                    for(let param in routelink.params) {
                        routeparams[param] = item[routelink.params[param]]
                    }
                    return { name: routelink.name, params: routeparams }
                }
                return { name: routelink.name }
            }
            return
        },
        getValue(item, detail_name, callback) {
            var s_exp = detail_name.split('.');
            for(let [key, value] in s_exp) {
                item = item[s_exp[key]];
            }
            if(typeof callback == 'function') {
                item = callback(item)
            }
            return item;
        },
        postClicked(item) {
            this.$dispatch(this.eventPrefix+'post:clicked', item)
        },
        getQuery(){
            var url = this.apiUrl + '?' + this.getQueryParams()

            this.$http.get(
                url
            ).then(response => {
                if(response.status == 200) {
                    // console.log("ARGG TRUE")
                    this.visible = true
                    this.myData = response.data.notices
                    // console.log(this.myData)
                    for (let key in this.myData){
                        let id = {id:this.myData[key].id}
                        staff.updateNotice(this, id)
                    }
                } else {
                    // console.log("ARGG FALSE")
                }
            })
            // console.log("LOAD DATA BRUH")
        }
    },
    events: {
        'uipost:refresh': function() {
            this.currentPage = 1
            this.loadData()
        },
    },
    created() {
        
    },
    ready() {
        this.getQuery();
        // console.log("LOAD")
        this.loadData();
    }
}
</script>

<style lang="sass">
button.viewbutton{
    border: none;
    background: none;
}
.read-more-state{
    display: none;
}
.read-more-target {
  opacity: 0;
  max-height: 0;
  font-size: 0;
  transition: .25s ease;
}

.read-more-state:checked ~ .read-more-wrap .read-more-target {
  opacity: 1;
  font-size: inherit;
  max-height: 999em;
}
.read-more-state ~ .read-more-trigger:before {
  content: '';
}

.read-more-state:checked ~ .read-more-trigger:before {
  content: 'See Less';
}
.read-more-trigger {
    color: #3cb371;
    font-family: calibri;
    cursor: pointer;
     font-size:14px!important;
}

.ui-post {
    width: 100%;
    border: none;
    // box-shadow: 0 0 2px rgba(0,0,0,.12), 0 2px 2px rgba(0,0,0,.2);
    padding: 10px; //20px
    .posts-container {
        width: 100%;
        .post-item {
            //cursor: pointer; remove style
            box-shadow: 0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);//new added style
            margin-bottom: 15px;//new added style
            //new addedclass
            .side-avatar{
                padding:15px;
                .avatar{
                    position: relative;
                    width:60px;
                    height:60px;
                    border-radius: 50%;
                    margin: 0 auto;
                    overflow: hidden;
                    box-shadow:0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);
                }
                .name{
                    text-align:center;
                    font-family: calibri;
                    font-size:14px!important;
                }

            }//end

            .col-sm-10,.col-sm-2{
                position: relative;
                width: 100%;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        .text-content{
            p{
                font-family: calibri;
                font-size:13px!important;
            }
        }
        .post {
            padding: 20px; //15px
            // box-shadow: 0 0 2px rgba(0,0,0,.12), 0 0px 2px rgba(0,0,0,.2);
            margin-bottom: 2px;
            &:last-child {
                margin-bottom: 0px;
            }
            .details {
                position: relative;
                h3.title {
                    margin-top: 0px;
                    margin-bottom: 10px;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    margin-right: 80px;
                    color: #3cb371; //new added style
                    font-family: calibri;
                    font-size:17px!important;
                }
                p.content {
                    margin: 0px;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    font-family: calibri !important;
                }
                .date {
                    position: absolute;
                    top: 0;
                    right: 0;
                    font-size: 11px;
                    line-height: 11px;
                    color: #3cb371; //new added style
                    font-family: calibri;
                    font-size:14px!important;
                }
            }
        }
    }
    .ui-progress-circular-toggle-transition {
        margin: 0 auto;
    }
}

// New added query
@media (max-width: 575px) {
    .side-avatar {
        display:none !important;
    }
}

@media (min-width: 575px) and (max-width: 1300px){
    .col-sm-10 {
        -ms-flex: 0 0 69.333333%;
        flex: 0 0 69.333333%;
        max-width: 69.333333%;
    }
    .col-sm-2 {
        -ms-flex: 0 0 30.333333%;
        flex: 0 0 30.333333%;
        max-width: 30.333333%;
    }
}

@media (min-width: 1300px){
    .col-sm-10 {
        -ms-flex: 0 0 79.333333%;
        flex: 0 0 79.333333%;
        max-width: 79.333333%;
    }
    .col-sm-2 {
        -ms-flex: 0 0 30.333333%;
        flex: 0 0 20.333333%;
        max-width: 20.333333%;
    }
}
</style>