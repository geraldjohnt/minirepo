<template>
    <div class="ui-table">
        <ui-alert class="noMargin" :show="!visible && dataloaded" :dismissible="false">{{ noContentText }}</ui-alert>
        <div class="table-responsive nego-table" v-if="visible && dataloaded" v-cloak>
            <table>
                <thead>
                    <tr>
                        <th v-for="field in fields" :key="field.label" width="{{getFieldWidth(field.width)}}">
                            {{ field.label }}
                        </th>
                        <!-- <th width="20%"></th> -->
                    </tr>
                </thead>
                <tbody v-cloak>
                    <tr v-for="item in items" :key="item">
                        <td v-for="field in fields" :key="field.name" width="{{getFieldWidth(field.width)}}">
                            <!-- m2b-81 -->
                            <span v-if="field.name == videoreport">
                                <span v-if="item.in_progress">{{ item.pro_percent }}</span>
                                <span v-else>
                                    <span v-if="item.video_size > 0">
                                    <span v-if="getValue(item, field.name, field.callback) == field.name" class="downloadable"                                
                                        style="font-weight:900; color: blue; cursor:pointer;" 
                                        @click="callAction(getValue(item, field.name, field.callback), item)"
                                    >
                                        詳細
                                    </span> 
                                    </span> 
                                    <!-- <span v-else>{{ getValue(item, field.name, field.callback) }}</span> -->
                                </span>
                            </span>
                            <!-- m2b-81 -->
                            <span v-else>                            
                                <span class="downloadable" v-if="getValue(item, field.name, field.callback) == field.name" 
                                    style="font-weight:900; color: blue; cursor:pointer;" 
                                    @click="callAction(getValue(item, field.name, field.callback), item)"
                                >
                                    詳細
                                </span>
                                <span v-else>{{ getValue(item, field.name, field.callback) }}</span>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="ui-table-pagination" v-if="visible && dataloaded">
            <span class="ui-table-pagination-label">Rows:</span> 
            <div class="ui-select-wrapper">
                <ui-select
                    name="paginate-options" :options="paginateOptions"
                    placeholder="5" :default.sync="perPage"
                ></ui-select>
            </div>
            <span>{{ pagination.info }}</span>
            <div class="buttons">
                <ui-icon-button 
                icon="keyboard_arrow_left" 
                type="flat"
                @click="prevPage"
                :disabled="currentPage == 1"
                ></ui-icon-button>
                <ui-icon-button 
                icon="keyboard_arrow_right" 
                type="flat"
                @click="nextPage"
                :disabled="pagination.disableNextPage"
                ></ui-icon-button>
            </div>
        </div>
        <ui-progress-circular :show="!loading.main" color="primary"></ui-progress-circular>
    </div>
</template>

<script>
// m2b-81
import staff from '../../js/staff.js';
// m2b-81 
export default {
    name: 'ui-nego-table',
    data() {
        return {
            eventPrefix: 'uitable:',
            tablePagination: null,
            currentPage: 1,
            visible: false,
            dataloaded: false,
            loading: {
                main: false
            },
            items: [],
            default: {
                width: 25,
                action: {
                    icon: 'add',
                    icon_color: 'primary'
                }
            },
            pagination: {
                info: '',
                data: {},
                disableNextPage: true
            },
            docViewRows: 5,
            video_exist: false,
            text_exist: false,
            videoreport: 'video_report',
            itemCheckDone: false,
            reload_interval: 0
        }
    },
    props: {
        folderId: '0',
        apiUrl: {
            type: String,
            required: true
        },
        dataKey: {
            type: String,
            required: true
        },
        fields: {
            type: Array,
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
            type: String,
            default: '10'
        },
        queryParams: {
            type: Object,
            default: function() {
                return {
                    sort: 'sort',
                    page: 'page',
                    perPage: 'per_page',
                    folder: 'folder'
                }
            }
        },
        appendParams: {
            type: Array,
            default: function() {
                return []
            }
        },
        paginateOptions: {
            type: Array,
            default: function() {
                return [ '5', '10', '25' ]
            }
        }
    },
    methods: {
        loadData() {
            try {

                if(this.perPage == 25 && navigator.userAgent.indexOf('iPad') > 0){
                    let body = document.getElementsByTagName('body')[0];
                    body.classList.add('ipad-long');
                }else {
                    let body = document.getElementsByTagName('body')[0];
                    body.classList.remove('ipad-long');
                }

                // console.log(this.folderId)
                var url = this.apiUrl + '?' + this.getQueryParams()
                this.$http.get(
                    url
                ).then(response => {
                    if(response.status == 200) {
                        this.visible = true
                        this.pagination.data = response.body.meta.pagination

                        // m2b-81
                        var negoData = response.body[this.dataKey]
                        let self = this;

                        var tempArray = [];
                        var line = 0;
                        negoData.forEach(function(data) {
                            tempArray.push(data); 
                            tempArray[line].in_progress = false; 
                            tempArray[line].is_exist = false;
                            tempArray[line].pro_percent = '';                            
                            line++;
                        });  

                        this.items = tempArray;
                        this.items.forEach(function(itm) {
                            if(itm.video_report) {
                                if(parseInt(itm.video_size) == 0) {

                                    self.checkItems(itm);
                                    var retval = window.setInterval(function(){
                                    try {
                                        if(self.itemCheckDone) {
                                            clearInterval(retval);
                                            self.itemCheckDone = false;
                                        }
                                    } catch(e) { /** do nothing */}
                                    }.bind(this), 500);   
                                    
                                } else itm.is_exist = true;   
                            }
                        });                         
                        // m2b-81 

                    } else {
                        this.visible = false
                    }

                    this.$nextTick(() => {
                        this.dataIsLoaded()
                        this.loadPagination()
                    })
                }, response => {
                    this.dataIsLoaded()
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data from our server'}
                let functionName = 'UiNegoTable:loadData';
                helper.catchError(errorStats, functionName);
            }
        },
        getQueryParams() {
            try {
                var params = [
                    // this.queryParams.sort + '=' + this.getSortParam(),
                    this.queryParams.page + '=' + this.currentPage,
                    this.queryParams.perPage + '=' + this.perPage,
                    this.queryParams.folder + '=' + this.folderId
                ].join('&')
                if (this.appendParams.length > 0) {
                    params += '&'+this.appendParams.join('&')
                }

                return params
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Setting up the query parameters'}
                let functionName = 'UiNegoTable:getQueryParams';
                helper.catchError(errorStats, functionName);
            }
        },
        addParam(param) {
            try {
                this.appendParams.push(param)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Appending parameters to our query'}
                let functionName = 'UiNegoTable:addParam';
                helper.catchError(errorStats, functionName);
            }
        },
        dataIsLoaded() {
            try {
                // console.log('data is loaded')
                this.loading.main = true;
                setTimeout( () => {
                    this.dataloaded = true
                }, 500)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checking if the data is done loading'}
                let functionName = 'UiNegoTable:dataIsLoaded';
                helper.catchError(errorStats, functionName);
            }
        },
        getFieldWidth(width) {
            try {
                return typeof width != 'undefined' ? width + '%' : this.default.width + '%'
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the field width'}
                let functionName = 'UiNegoTable:getFieldWidth';
                helper.catchError(errorStats, functionName);
            }
        },
        getActionIcon(icon) {
            try {
                return typeof icon != 'undefined' ? icon : this.default.action.icon
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the action icon'}
                let functionName = 'UiNegoTable:getActionIcon';
                helper.catchError(errorStats, functionName);
            }
        },
        getActionIconColor(icon_color) {
            try {
                return typeof icon_color != 'undefined' ? icon_color : this.default.action.icon_color
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the action icon color'}
                let functionName = 'UiNegoTable:getActionIconColor';
                helper.catchError(errorStats, functionName);
            }
        },
        callAction(action, data) {
            try {
                this.$dispatch(this.eventPrefix+action, action, data)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the call action'}
                let functionName = 'UiNegoTable:callAction';
                helper.catchError(errorStats, functionName);
            }
        },
        getRouteLink(routelink, item) {
            try {
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
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the route link'}
                let functionName = 'UiNegoTable:getRouteLink';
                helper.catchError(errorStats, functionName);
            }
        },
        getValue(item, fieldname, callback) {
            try {
                var s_exp = fieldname.split('.');
                for(let [key, value] in s_exp) {
                    item = item[s_exp[key]];
                    // console.log(item)
                }
                if(typeof callback == 'function') {
                    item = callback(item)
                }
                item = (s_exp == 'notes' || s_exp == 'voice_report' || s_exp == 'video_report') ? (!!item) ? s_exp : undefined : item;
                return item;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the value'}
                let functionName = 'UiNegoTable:getValue';
                helper.catchError(errorStats, functionName);
            }
        },
        prevPage() {
            try {
                if(this.currentPage != 1) {
                    this.currentPage --
                    this.loadData()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the previous page'}
                let functionName = 'UiNegoTable:prevPage';
                helper.catchError(errorStats, functionName);
            }
        },
        nextPage() {
            try {
                if(this.pagination.data.total_pages > this.currentPage) {
                    this.currentPage ++
                    this.loadData()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the next page'}
                let functionName = 'UiNegoTable:nextPage';
                helper.catchError(errorStats, functionName);
            }
        },
        loadPagination() {
            try {
                let o_paginate = this.pagination.data
                let i_range1 = (o_paginate.current_page-1) * o_paginate.per_page + 1
                let i_range2 = (o_paginate.current_page-1) * o_paginate.per_page + o_paginate.count
                this.pagination.info = `${i_range1}-${i_range2} of ${o_paginate.total}`
                this.pagination.disableNextPage = !(o_paginate.total_pages > o_paginate.current_page)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the pagination'}
                let functionName = 'UiNegoTable:loadPagination';
                helper.catchError(errorStats, functionName);
            }
        },

        // m2b-81
        checkItems(itm) {
            try {
                let self = this;            
                var n_id = itm.negotation_id;
                var formData = new FormData();
                formData.append('url', itm.video_report);
                staff.checkVideoUrl(formData, this.role).then((response) => {
                    if (response) {
                        if(response.body.text_exist) {
                            itm.pro_percent = response.body.pro_percent + '%';
                        } 
                        itm.in_progress = response.body.text_exist;
                        itm.is_exist = response.body.video_exist;

                        if(response.body.pro_percent >= 100) {
                            var size = response.body.size;
                            let fdata = new FormData();
                            fdata.append('_method', 'PUT')
                            fdata.append('video_size', size)
                            staff.updateNegotation(self, {id: n_id}, fdata, this.role)
                            self.loadData();            
                        }              

                        if(itm.duration != response.body.duration) {
                            if(response.body.duration > 0) {
                                var dura = response.body.duration;
                                var diff = itm.duration - dura;
                                // check if time difference is > 2 mins
                                if(diff < 120) {
                                    // updating duration column in staff_negotation with and empty link
                                    let fdata = new FormData();
                                    fdata.append('_method', 'PUT')
                                    fdata.append('duration', dura)
                                    staff.updateNegotation(self, {id: n_id}, fdata, this.role)
                                    self.loadData();                                               
                                }
                            }
                        } 
                        if(itm.video_size > 0 || itm.is_exist) self.loadData();
                        self.itemCheckDone = true;              
                    }
                });      
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Check Video Links if Exist and process percentage during audio/video file merging'}
                let functionName = 'UiNegoTable:checkItems';
                helper.catchError(errorStats, functionName);
            }                                      
        },   
        reloadInProgress() {
            try {
                let self = this;
                var cnt = 0;
                this.items.forEach(function(itm) {
                    if(itm.in_progress) {
                        self.checkItems(itm);
                        var retval = window.setInterval(function(){
                        try {
                            if(self.itemCheckDone) {
                                clearInterval(retval);
                                self.itemCheckDone = false;
                            }
                        } catch(e) { /** do nothing */}
                        }.bind(this), 200);   
                    }
                    cnt++;
                });                         
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data from our server'}
                let functionName = 'UiNegoTable:loadData';
                helper.catchError(errorStats, functionName);
            }
        },
        // m2b-81          
    },
    events: {
        'uitable:refresh': function() {
            try {
                this.currentPage = 1
                this.loadData()
                // console.log('refreshing table')
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Waiting for the refresh page'}
                let functionName = 'UiNegoTable::uitable:refresh';
                helper.catchError(errorStats, functionName);
            }
        },
        'selected': function(option) {
            try {
                this.perPage = option
                this.currentPage = 1;
                this.loadData()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Waiting for the selection'}
                let functionName = 'UiNegoTable:selected';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    created() {
        try {
            this.loadData()
            // m2b-81
            this.reload_interval = setInterval(this.reloadInProgress, 1000)
            // m2b-81               
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Created the negotation component'}
            let functionName = 'UiNegoTable:created';
            helper.catchError(errorStats, functionName);
        }
    }
}
</script>

<style lang="sass">
.ui-alert.noMargin {
    .ui-alert-body {
        margin-bottom: 0px;
    }
}
.ui-table {
    width: 100%;
    border: none;
    box-shadow: 0 0 2px rgba(0,0,0,.12), 0 2px 2px rgba(0,0,0,.2);
    padding: 20px;
    .table-responsive {
        width: 100%;
        overflow: auto;
        -ms-overflow-style: -ms-autohiding-scrollbar;
        // border: 1px solid #ddd;
    }
    table {
        // width: 100%;
        text-align: left;
        border-collapse: collapse;
        font-size: 12px;
        table-layout: fixed;
        min-width: 200px;
        tr {
            th {
                padding: 8px;
                border-bottom: 2px solid #ddd;
                line-height: 1.4em;
                overflow-wrap: break-word;
                word-break: break-all;
                background: #3cb371;
                color: #fff;
            }
            td {
                padding: 8px;
                line-height: 1.4em;
                vertical-align: top;
                border-top: 1px solid #ddd;
                overflow-wrap: break-word;
                word-break: break-all;
                span.downloadable:hover {
                    font-size: 12.5px;
                    text-decoration: underline;
                }
            }
        }
    }

    .nego-table table{
        width:100%;
    }
    .ui-progress-circular-toggle-transition {
        margin: 0 auto;
    }
    .actions {
        text-align: center;
        @media (max-width: 763px) {
            span {
                display: block;
            }
        }
        .ui-icon-button {
            width: 32px;
            height: 32px;
            .ui-icon {
                font-size: 20px;
            }
        }
    }
    .ui-table-pagination {
        text-align: right;
        .ui-select-wrapper {
            min-width: 80px;
            max-width: 200px;
            display: inline-block;
            text-align: left;
            .ui-select {
                margin-bottom: 0px;
                .ui-select-display {
                    .ui-select-value {
                        padding: 0px 10px;
                    }
                }
                .ui-select-dropdown {
                    min-width: 80px;
                }
            }
        }
        .buttons {
            display: inline;
            .ui-icon-button {
                display: inline-block;
            }
        }
    }
}
.iphone-phone .ui-table .table-responsive {
    height: 60vh;
}
</style>