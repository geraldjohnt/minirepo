<template>
    <div class="ui-table">
        <ui-alert class="noMargin" :show="!visible && dataloaded" :dismissible="false">{{ noContentText }}</ui-alert>
        <div class="table-responsive" v-if="visible && dataloaded" v-cloak>
            <table id="sort_this_table">
                <thead >
                   <tr v-cloak v-if="analyzeAction" >
                        <template v-for="field in fields">
                            <th v-bind:key="field.name" v-if="field.label == '最終ログイン' || field.label == '最終接続'" @click="filter ? sortFilter(field.sortby):sort(field.name)"  width="{{getFieldWidth(field.width)}}">
                               <span class="spanText"> {{ field.label }} </span><i class="material-icons md-24">unfold_more</i>
                            </th>
                            <th v-bind:key="field.name" v-else  width="{{getFieldWidth(field.width)}}">
                                {{ field.label }}
                            </th>
                        </template>
                        <th width="20%"></th>
                    </tr>
         
                    <tr v-else v-cloak>
                        <template v-cloak v-if="manageStaff">
                            <template v-cloak v-for="field in fields">
                                <th v-bind:key="field.s" @click="qSort(field.s)"  width="{{getFieldWidth(field.width)}}">
                                    <span class="spanText"> {{ field.label }} </span><i class="material-icons md-24">unfold_more</i>
                                </th>
                            </template>
                        </template>
                        <template v-cloak v-if="!manageStaff">
                            <template v-cloak v-for="field in fields">
                                <th v-bind:key="field.width" width="{{getFieldWidth(field.width)}}">
                                {{ field.label }}
                                </th>
                            </template>
                        </template>
                        
                        <th width="10%"></th>
                    </tr>
                
                </thead>
                <tbody v-cloak v-if="analyzeAction" >
                        <tr v-for="item in filteredList || sortedList" v-bind:key="item.profile.differInt" v-bind:class="[(item.profile.differInt >= 3 && item.profile.differInt <= 6)? 'borderYellow' : (item.profile.differInt >= 7) ? 'borderRed' : '']">
    
                        <td v-for="field in fields" width="{{getFieldWidth(field.width)}}" v-bind:key="field.width">
                            {{ getValue(item, field.name, field.callback )}}
                        </td>
                        <td class="actions" width="20%">
                        <span v-for="action in actions" width="20%" v-bind:key="action.name">
                                <ui-icon-button 
                                :icon="getActionIcon(action.icon)" 
                                :color="getActionIconColor(action.icon_color)"
                                type="flat"
                                tooltip="View Staff"
                                tooltipPosition="top right"
                                @click="callAction(action.name, item)"
                                v-link="getRouteLink(action.routelink, item)"
                                ></ui-icon-button>
                        </span>
                        </td>
                    </tr>
                </tbody>
                <tbody v-cloak v-else>
                    <tr v-for="item in items" v-bind:key="item.staff_id">
                        <td v-for="field in fields" v-bind:key="field.s" width="{{getFieldWidth(field.width)}}" v-cloak>
                            <span v-cloak v-if="manageStaff && field.s == 'active'">
                                <ui-switch trueValue="true" falseValue="false" :value.sync="staff_sync[item.staff_id]" @change="changeActiveStatus(item.staff_id)"></ui-switch>
                            </span>
                            <span v-cloak v-else>{{ getValueCondition(item, field.name, field.callback) }}</span>
                        </td>
                        <td class="actions" width="10%">
                            <span v-for="action in actions" v-bind:key="action.icon">
                                <ui-icon-button v-if="documentsModal"
                                    :icon="getActionIcon(action.icon) "  
                                    :color="getActionIconColor(action.icon_color)"
                                    type="flat"
                                    v-show="action.icon == 'share' && item.allow_download == 1 || action.icon == 'visibility' ?
                                    true : false "
                                    @click="callAction(action.name, item)"
                                    v-link="getRouteLink(action.routelink, item)"
                                ></ui-icon-button>
                                <ui-icon-button v-cloak v-else
                                    :icon="getActionIcon(action.icon) "  
                                    :color="getActionIconColor(action.icon_color)"
                                    type="flat"
                                    @click="callAction(action.name, item)"
                                    v-link="getRouteLink(action.routelink, item)"
                                ></ui-icon-button>
                
                            </span>
                        </td>
                    </tr>
                   
                </tbody>
            </table>
        </div>
        <div class="ui-table-pagination" v-if="visible && dataloaded">
            <span class="ui-table-pagination-label" v-if="!analyzeAction">Rows:</span> 
            <div class="ui-select-wrapper" v-bind:class="[analyzeAction ? 'disabled' : null]">
                <ui-select
                    name="paginate-options" :options="paginateOptions"
                    placeholder="5" :default.sync="perPage"
                ></ui-select>
            </div>
            <span v-cloak v-if="manageStaff">{{ pageInfo2() }}</span>
            <span v-else>{{ analyzeAction ? searchTrigger ? searchInfo() : pageInfo() : pagination.info }}</span>
            <div class="buttons" v-if="!searchTrigger">
                <ui-icon-button 
                icon="keyboard_arrow_left" 
                type="flat"
                @click="analyzeAction ? previousPage() :  prevPage()"
                :disabled="currentPage == 1"
                ></ui-icon-button>
                <ui-icon-button 
                v-cloak v-if="manageStaff"
                icon="keyboard_arrow_right" 
                type="flat"
                @click="nextsPage2()"
                :disabled="pagination2.disableNextPage"
                ></ui-icon-button>
                <ui-icon-button 
                v-else
                icon="keyboard_arrow_right" 
                type="flat"
                @click="analyzeAction ? nextsPage() : nextPage()"
                :disabled="pagination.disableNextPage"
                ></ui-icon-button>
            </div>
        </div>
        <ui-progress-circular :show="!loading.main" color="primary"></ui-progress-circular>
    </div>
</template>
<script>
import helper from '../../js/helpers.js';
export default {
    name: 'ui-table',
    data() {
        return {
            currentSort: '',
            trialS : 'desc',
            last_nameS : 'asc',
            companyS : 'asc',
            emailS: 'asc',
            activeS: 'asc',
            trial_startS: 'asc',
            end_of_trialS: 'asc',
            registered_dateS: 'asc',
            currentSortDir: 'desc', 
            filter: false,
            sortBoolean: false,
            searchList: [],
            retval : [],
            newval : [],
            begin: 0,
            end: 0,
            begin2: 0,
            end2:0,
            staff_sync : [],
            searchTrigger: false,
            list:[],
            pageList:[],
            numberPerPage : 10,
            numberPerPage2 : 5,
            numberOfPages : 1,
            filterJobs: [],
            eventPrefix: 'uitable:',
            tablePagination: null,
            currentPage: 1,
            visible: false,
            dataloaded: false,
            loading: {
                main: false
            },
            items: [],
            itemsRaw:[],
            analyzeValue:[],
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
            pagination2:{
                disableNextPage : true
            }
        }
    },
    props: {
        manageStaff: {
            type: Boolean,
            default: false
        },
        manageCompany: {
            type: Boolean,
            default: false
        },
        manageCompanyUser: {
            type: Boolean,
            default: false
        },
        documentsModal: {
            type: Boolean,
            default: false
        },
        folderId: '0',
        searchCompany:{
            type: String,
            required: false
        },
        analyzeAction:{
            type: Boolean,
            default: false
        },
        searchName:{
            type: String,
            required: false
        },
        searchDiffer:{
            type: Array,
            required: false
        },
        searchLogin:{
            type: Array,
            required: false
        },
        searchConnect:{
            type: Array,
            required: false
        },
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
            type: Number,
            default: 5
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
                this.analyzeAction ? this.getAllItems() : null
                if(this.perPage == 25 && navigator.userAgent.indexOf('iPad') > 0){
                    let body = document.getElementsByTagName('body')[0];
                    body.classList.add('ipad-long');
                }else {
                    let body = document.getElementsByTagName('body')[0];
                    body.classList.remove('ipad-long');
                }
                
                if(this.manageStaff && this.itemsRaw.length > 0){
                    var urlParams = new URLSearchParams(this.getQueryParams());
                    this.numberPerPage2 = parseInt(urlParams.get('per_page'));
                    this.loadList2();
                    this.pagination2.disableNextPage = this.getNumberOfPages2() == this.currentPage ? true : false
                } else {
                    var url = this.manageStaff ? '/api/admin/manage-staff?page=1&per_page=10000' : this.apiUrl + '?' + this.getQueryParams()
                    //var url =  this.apiUrl + '?' + this.getQueryParams()
                    this.$http.get(
                        url
                    ).then(response => {
                        if(response.status == 200) {
                            if (this.dataKey == 'documents') {
                                // let doc = response.body[this.dataKey];
                                // let done = [];
                                // if (!!doc.length) {
                                //     this.visible = true
                                //     for (let i = 0 ; i < doc.length; i++) {
                                //         if (doc[i].pages.length != 0) {
                                //             done.push(doc[i]);
                                //         }
                                //     }
                                // }
                                // this.items = done;
                                // this.pagination.data = response.body.meta.pagination;
                                this.visible = true;
                                this.items = response.body[this.dataKey];
                                this.pagination.data = response.body.meta.pagination;
                            } else if(this.dataKey == 'staff') {
                                //console.log(this.dataKey);
                                this.visible = true;
                                this.itemsRaw = response.body[this.dataKey];
                                this.qSortD('created_at');
                                this.pagination2.disableNextPage = this.getNumberOfPages2() == this.currentPage ? true : false
                                for(var rr = 0; rr < this.itemsRaw.length; rr++){
                                    this.staff_sync[this.itemsRaw[rr].staff_id] = this.itemsRaw[rr].profile.active == 1 ? true : false;
                                }
                                //console.log(this.itemsRaw[0].profile.active);
                            } else {
                                this.visible = true;
                                this.items = response.body[this.dataKey];
                                this.pagination.data = response.body.meta.pagination;
                            }
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
                }//end-else
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data'}
                let functionName = 'UiTable:loadData';
                helper.catchError(errorStats, functionName);
            }
        },
        changeActiveStatus(id){
            try {
                var currentStatus = this.staff_sync[id] ? 1 : 0;
                this.$http.get('api/admin/staff/setStatus?staff_id='+ id + '&status=' + currentStatus).then(response => {
                })
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Changing the active status'}
                let functionName = 'UiTable:changeActiveStatus';
                helper.catchError(errorStats, functionName);
            }
        },
        getSVal(field,i){
            try {
                var val = "";
                switch(field){
                    case 'email':
                        val = this.itemsRaw[i].profile.email == null || this.itemsRaw[i].profile.email == 'undefined' ? "" : this.itemsRaw[i].profile.email;
                    break;
                    case 'company':
                        val = this.itemsRaw[i].profile.company == null || this.itemsRaw[i].profile.company == 'undefined' ? "" : this.itemsRaw[i].profile.company;
                    break;
                    case 'last_name':
                    val = this.itemsRaw[i].profile.last_name == null || this.itemsRaw[i].profile.last_name == 'undefined' ? "" : this.itemsRaw[i].profile.last_name;
                    break;
                    case 'trial':
                    val = this.itemsRaw[i].profile.trial == null || this.itemsRaw[i].profile.trial == 'undefined' ? "" : this.itemsRaw[i].profile.trial;
                    break;
                    case 'active':
                    val = this.itemsRaw[i].profile.active == null || this.itemsRaw[i].profile.active == 'undefined' ? "" : this.itemsRaw[i].profile.active;
                    break;
                    case 'trial_start':
                    val = this.itemsRaw[i].profile.trial_start == null || this.itemsRaw[i].profile.trial_start == 'undefined' ? "" : this.itemsRaw[i].profile.trial_start;
                    break;
                    
                    case 'end_of_trial':
                    val = this.itemsRaw[i].profile.end_of_trial == null || this.itemsRaw[i].profile.end_of_trial == 'undefined' ? "" : this.itemsRaw[i].profile.end_of_trial;    
                    break;
                    case 'registered_date':
                    val = this.itemsRaw[i].profile.registered_date == null || this.itemsRaw[i].profile.registered_date == 'undefined' ? "" : this.itemsRaw[i].profile.registered_date;
                    break;
                    case 'created_at':
                    val = this.itemsRaw[i].profile.created_at == null || this.itemsRaw[i].profile.created_at == 'undefined' ? "" : this.itemsRaw[i].profile.created_at;
                    break;
                    
                }
                return val;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting values'}
                let functionName = 'UiTable:getSVal';
                helper.catchError(errorStats, functionName);
            }
        },
        pList(){
            try {
                if(this.itemsRaw.length > 10) {
                    this.items = this.itemsRaw.slice(0,10);
                } else {
                    this.items = this.itemsRaw;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the Items'}
                let functionName = 'UiTable:pList';
                helper.catchError(errorStats, functionName);
            }
        },
        qSort(field){
            try {
                switch(field){
                    case 'email':
                        if(this.emailS == "asc"){
                            this.qSortD(field);
                            this.emailS="desc";
                        } else {
                            this.qSortA(field);
                            this.emailS="asc";
                        }
                    break;
                    case 'company':
                        if(this.companyS == "asc"){
                            this.qSortD(field);
                            this.companyS="desc";
                        } else {
                            this.qSortA(field);
                            this.companyS="asc";
                        }
                    break;
                    case 'last_name':
                        if(this.last_nameS == "asc"){
                            this.qSortD(field);
                            this.last_nameS="desc";
                        } else {
                            this.qSortA(field);
                            this.last_nameS="asc";
                        }
                    break;
                    case 'trial':
                        if(this.trialS == "asc"){
                            this.qSortD(field);
                            this.trialS="desc";
                        } else {
                            this.qSortA(field);
                            this.trialS="asc";
                        }
                    break;
                    case 'active':
                        if(this.activeS == "asc"){
                            this.qSortD(field);
                            this.activeS="desc";
                        } else {
                            this.qSortA(field);
                            this.activeS="asc";
                        }
                    break;
                    case 'trial_start':
                        if(this.trial_startS == "asc"){
                            this.qSortD(field);
                            this.trial_startS="desc";
                        } else {
                            this.qSortA(field);
                            this.trial_startS="asc";
                        }
                    break;
                    case 'end_of_trial':
                        if(this.end_of_trialS == "asc"){
                            this.qSortD(field);
                            this.end_of_trialS="desc";
                        } else {
                            this.qSortA(field);
                            this.end_of_trialS="asc";
                        }
                    break;
                    case 'registered_date':
                        if(this.registered_dateS == "asc"){
                            this.qSortD(field);
                            this.registered_dateS="desc";
                        } else {
                            this.qSortA(field);
                            this.registered_dateS="asc";
                        }
                    break;
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting the columns'}
                let functionName = 'UiTable:qSort';
                helper.catchError(errorStats, functionName);
            }
        },
        qSortA(field){
            try {
                //console.log('sorting on the way');
                var len = this.itemsRaw.length;
                for (var i = 0; i < len; i++) {
                    let min = i;
                    for (var j = i + 1; j < len; j++) {
                        if (this.getSVal(field,min) > this.getSVal(field,j))
                            min = j;
                    }
                    if (i !== min)
                        this.swap(i,min);
                }
                this.loadList2();
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting the data in ascending'}
                let functionName = 'UiTable:qSortA';
                helper.catchError(errorStats, functionName);
            }
        },
        qSortD(field){
            try {
                //console.log('sorting on the way');
                var len = this.itemsRaw.length;
                for (var i = 0; i < len; i++) {
                    let min = i;
                    for (var j = i + 1; j < len; j++) {
                        if (this.getSVal(field,min) < this.getSVal(field,j))
                            min = j;
                    }
                    if (i !== min)
                        this.swap(i,min);
                }
                this.loadList2();
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting the data in descending'}
                let functionName = 'UiTable:qSortD';
                helper.catchError(errorStats, functionName);
            }
        },
        swap(i, j){
            try {
                var temp = this.itemsRaw[i];
                this.itemsRaw[i] = this.itemsRaw[j];
                this.itemsRaw[j] = temp;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Swapping the items'}
                let functionName = 'UiTable:swap';
                helper.catchError(errorStats, functionName);
            }
        },
        sort(sort){
            try {
                if(sort ===  this.currentSort){
                    this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                }
                this.currentSort = sort;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the data'}
                let functionName = 'UiTable:loadData';
                helper.catchError(errorStats, functionName);
            }
        },
        recomposeObj(obj,string){
            try {
                var parts = string.split('.');
                var newObj = obj[parts[0]];
                if(parts[1]){
                    parts.splice(0,1);
                    var newString = parts.join('.');
                    return this.recomposeObj(newObj,newString);
                }
                return newObj;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Recomposing the objects'}
                let functionName = 'UiTable:recomposeObj';
                helper.catchError(errorStats, functionName);
            }
        },
        sortFilter(data){
            try {
                var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
                table = document.getElementById("sort_this_table");
                switching = true;
                dir = "asc";
                while (switching) {
                    switching = false;
                    rows = table.rows;
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        x = rows[i].getElementsByTagName("TD")[data].innerHTML
                        y = rows[i + 1].getElementsByTagName("TD")[data].innerHTML
                        if (dir == "asc") {
                            if (x > y) {
                                shouldSwitch = true;
                                break;
                            }
                        } else if (dir == "desc") {
                            if (x < y) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                        switchcount++;
                    } else {
                        if (switchcount == 0 && dir == "asc") {
                            dir = "desc";
                            switching = true;
                        }
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting the filter'}
                let functionName = 'UiTable:sortFilter';
                helper.catchError(errorStats, functionName);
            }
        },
        getAllItems(){
            try {
                this.getAnalyzeValue();
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting all the items'}
                let functionName = 'UiTable:getAllItems';
                helper.catchError(errorStats, functionName);
            }
        },
         getAnalyzeValue(){
            try {
                this.$http.get('api/admin/staff/getlogin').then(response => {
                    // console.log('response',response.body)
                    this.analyzeValue = response.body
                    this.numberOfPages = this.getNumberOfPages()
                    this.numberOfPages = this.getNumberOfPages()
                    this.loadList(this.analyzeValue)
                    this.pageInfo()
                    this.list = this.analyzeValue
                
                })
                
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the analyzed value'}
                let functionName = 'UiTable:getAnalyzeValue';
                helper.catchError(errorStats, functionName);
            }
        },
        pageInfo(){
            try {
                return (this.begin+1)+ '-' + this.end + ' of '+ Object.keys(this.analyzeValue).length
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the page information'}
                let functionName = 'UiTable:pageInfo';
                helper.catchError(errorStats, functionName);
            }
        },
        searchInfo(){
            try {
                return 'Showing ' +Object.keys(this.newval).length + ' entries'
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Searching the information'}
                let functionName = 'UiTable:searchInfo';
                helper.catchError(errorStats, functionName);
            }
        },
        getNumberOfPages(){
            try {
                return Math.ceil( Object.keys(this.analyzeValue).length / this.numberPerPage)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the number of pages'}
                let functionName = 'UiTable:getNumberOfPages';
                helper.catchError(errorStats, functionName);
            }
        },
        nextsPage(){
            try {
                this.currentPage += 1;
                //console.log('this.getNumberOfPages()');
                //console.log(this.getNumberOfPages());
                //console.log('this.currentPage');
                // console.log(this.currentPage);
                //console.log("this.getNumberOfPages() == this.currentPage ");
                //console.log(this.pagination.disableNextPage);
                this.loadList(this.analyzeValue);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting th next page'}
                let functionName = 'UiTable:nextsPage';
                helper.catchError(errorStats, functionName);
            }
        },
        previousPage(){
            try {
                this.currentPage -= 1;
                this.loadList(this.analyzeValue);
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Showing the previous page'}
                let functionName = 'UiTable:loadData';
                helper.catchError(errorStats, functionName);
            }
        },
        loadList(data){
            try {
                this.begin = ((this.currentPage - 1) * this.numberPerPage);
                this.end = this.begin + this.numberPerPage;
                this.pagination.disableNextPage = this.getNumberOfPages() == this.currentPage ? true : false
                this.searchList = Object.keys(data).slice(this.begin,this.end).reduce((result, key) => {
                    result[key] = data[key];
                    return result;
                }, {});
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Loading the list'}
                let functionName = 'UiTable:loadList';
                helper.catchError(errorStats, functionName);
            }
        },
        getNumberOfPages2(){
            try {
                var num = Math.ceil( Object.keys(this.itemsRaw).length / this.numberPerPage2);
                //console.log('num pages ' + num);
                return num;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Second setup - Getting the number of pages'}
                let functionName = 'UiTable:getNumberOfPages2';
                helper.catchError(errorStats, functionName);
            }
        },
        nextsPage2(){
            try {
                //console.log('check next page');
                this.currentPage += 1;
                this.pagination2.disableNextPage = this.getNumberOfPages2() == this.currentPage ? true : false
                this.loadList2();
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Second setup - showing the next page'}
                let functionName = 'UiTable:nextsPage2';
                helper.catchError(errorStats, functionName);
            }
        },
        previousPage2(){
            try {
                //console.log('prev page 2')
                this.currentPage -= 1;
                this.loadList2();
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Second setup - showing the previous page'}
                let functionName = 'UiTable:previousPage2';
                helper.catchError(errorStats, functionName);
            }
        },
        loadList2(data){
            try {
                //console.log('running at loadlist2');
                //console.log('number per page ' + this.numberPerPage2);
                this.begin2 = ((this.currentPage - 1) * this.numberPerPage2);
                //console.log(this.begin2);
                this.end2 = this.begin2 + this.numberPerPage2;
                //console.log(this.end2);
                this.list = this.itemsRaw;
                this.items = this.itemsRaw.slice(this.begin2,this.end2);
                this.numberOfPages = this.getNumberOfPages2()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Second setup - Loading the data'}
                let functionName = 'UiTable:loadList2';
                helper.catchError(errorStats, functionName);
            }
        },
        pageInfo2(){
            try {
                return (this.begin2+1)+ '-' + this.end2 + ' of '+ Object.keys(this.itemsRaw).length
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Second setup - getting the page info'}
                let functionName = 'UiTable:pageInfo2';
                helper.catchError(errorStats, functionName);
            }
        },
        getQueryParams() {
            try {
                let perpages = this.analyzeAction ? 10 : this.perPage
                var params = [
                    // this.queryParams.sort + '=' + this.getSortParam(),
                    this.queryParams.page + '=' + this.currentPage,
                    this.queryParams.perPage + '=' + perpages,
                    this.queryParams.folder + '=' + this.folderId
                ].join('&')
                if (this.appendParams.length > 0) {
                    params += '&'+this.appendParams.join('&')
                }
                return params;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the query parameters'}
                let functionName = 'UiTable:getQueryParams';
                helper.catchError(errorStats, functionName);
            }
        },
        addParam(param) {
            try {
                this.appendParams.push(param)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Adding something into the query paramaters'}
                let functionName = 'UiTable:addParam';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Checks whether the data is being loaded'}
                let functionName = 'UiTable:dataIsLoaded';
                helper.catchError(errorStats, functionName);
            }
        },
        getFieldWidth(width) {
            try {
                return typeof width != 'undefined' ? width + '%' : this.default.width + '%'
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the field width'}
                let functionName = 'UiTable:getFieldWidth';
                helper.catchError(errorStats, functionName);
            }
        },
        getActionIcon(icon) {
            try {
                return typeof icon != 'undefined' ? icon : this.default.action.icon
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the action icons'}
                let functionName = 'UiTable:getActionIcon';
                helper.catchError(errorStats, functionName);
            }
        },
        getActionIconColor(icon_color) {
            try {
                //console.log('getActionIconColor');
                //this.pagination.disableNextPage = this.getNumberOfPages() < this.currentPage ? true : false
                //console.log("this.pagination.disableNextPage = this.getNumberOfPages() < this.currentPage ? true : false")
                //console.log(this.pagination.disableNextPage);
                return typeof icon_color != 'undefined' ? icon_color : this.default.action.icon_color
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting action icon color'}
                let functionName = 'UiTable:getActionIconColor';
                helper.catchError(errorStats, functionName);
            }
        },
        callAction(action, data) {
            try {
                //console.log(action);
                this.$dispatch(this.eventPrefix+action, action, data)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Calling the actions'}
                let functionName = 'UiTable:callAction';
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
                let functionName = 'UiTable:getRouteLink';
                helper.catchError(errorStats, functionName);
            }
        },
        getValueCondition(item, fieldname, callback) {
            try {
                var s_exp = fieldname.split('.');
                var i = '';
                var data = '';
                for(let [key, value] in s_exp) {
                    i = item[s_exp[key]];
                }
                if (fieldname == 'trial' && this.manageCompany) {
                    console.log('Trial')
                    data = (i == 0) ? 'Official':'Trial' 
                } else if (fieldname == 'manager' && this.manageCompanyUser) {
                    data = (i == 0) ? 'NO' : 'YES'
                } else {
                    data = this.getValue(item, fieldname, callback)
                }
                return data;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the values'}
                let functionName = 'UiTable:getValueCondition';
                helper.catchError(errorStats, functionName);
            }
        },
        getValue(item, fieldname, callback) {
            try {
                var s_exp = fieldname.split('.');
                for(let [key, value] in s_exp) {
                    item = item[s_exp[key]];
                }
                if(typeof callback == 'function') {
                    item = callback(item)
                }
                return item;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the values'}
                let functionName = 'UiTable:getValue';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the previous page'}
                let functionName = 'UiTable:prevPage';
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
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Getting the next page'}
                let functionName = 'UiTable:nextPage';
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
                let functionName = 'UiTable:loadPagination';
                helper.catchError(errorStats, functionName);
            }
        },
        hasCheckboxSearch(){
            try {
                this.searchTrigger = true
                for(let y=0; y<this.searchDiffer.length; y++ ){
                    for(let i=0; i<this.newval.length; i++){
                        if(this.searchDiffer[y] == 3 ){
                            if((this.newval[i].profile.differInt >= 3) && (this.newval[i].profile.differInt <= 6)){
                                this.retval.push(this.newval[i])
                            }
                        } else {
                            if(this.newval[i].profile.differInt >= parseInt(this.searchDiffer[y])){
                                this.retval.push(this.newval[i])
                            }
                        }
                    }
                }
                this.newval = []
                this.newval = this.retval
                this.retval = []
                if(this.searchConnect.length != 0){
                    this.filterDaysConnect()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Determines whether search has a checkbox'}
                let functionName = 'UiTable:hasCheckboxSearch';
                helper.catchError(errorStats, functionName);
            }
        },
        checkboxNullLogin(){
            try {
                this.searchTrigger = true
                    for(let y=0; y<this.searchLogin.length; y++ ){
                        for(let i=0; i<this.newval.length; i++){
                            if(this.searchLogin[y] == 1 ){
                                if(!!this.newval[i].profile.last_login){
                                    this.retval.push(this.newval[i])
                                }
                            }
                        }
                    }
                    this.newval = []
                    this.newval = this.retval
                    this.retval = []
                if(this.searchConnect.length != 0){
                    this.filterDaysConnect()
                }
                
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Determines whether search has a checkbox for Null Login'}
                let functionName = 'UiTable:checkboxNullLogin';
                helper.catchError(errorStats, functionName);
            }
        },
        checkboxNullConnect(){
            try {
                this.searchTrigger = true
                    for(let y=0; y<this.searchConnect.length; y++ ){
                        for(let i=0; i<this.newval.length; i++){
                            if(this.searchConnect[y] == 1 ){
                                if(!!this.newval[i].profile.last_connection){
                                    this.retval.push(this.newval[i])
                                }
                            }
                        }
                    }
                    this.newval = []
                    this.newval = this.retval
                    this.retval = []
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Determines whether search has a checkbox for Null Connection'}
                let functionName = 'UiTable:checkboxNullConnection';
                helper.catchError(errorStats, functionName);
            }
        },
        filterDaysConnect(){
            try {
                var obj = this.newval;
                var filterConnDays = obj.filter(function(daysConn) {
                    if (!!daysConn.profile.last_connection){
                        return daysConn;
                    }
                });
                this.newval = filterConnDays;
                //console.log('laman ng filterDaysConnect() - filtered', filterConnDays)
                //console.log('laman ng filterDaysConnect() - this.newval', this.newval)
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Determines whether the condition filtered the Connection checkbox and Filtered days'}
                let functionName = 'UiTable:filterDaysConnect';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    events: {
        'uitable:refresh': function() {
            try {
                this.currentPage = 1
                this.loadData()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Refreshing the table through the UI'}
                let functionName = 'UiTable::uitable:refresh';
                helper.catchError(errorStats, functionName);
            }
        },
        'selected': function(option) {
            try {
                this.perPage = option
                this.loadData()
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Selected the items'}
                let functionName = 'UiTable:selected';
                helper.catchError(errorStats, functionName);
            }
        }
    },
    created() {
        try {
            this.perPage = 5
            this.loadData()
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Create the UI Table component'}
            let functionName = 'UiTable:created';
            helper.catchError(errorStats, functionName);
        }
    },
    ready(){
        try {
            this.staff_sync
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Synchronize the staff'}
            let functionName = 'UiTable:ready';
            helper.catchError(errorStats, functionName);
        }
    },
    computed: {
        sortedList(){
            try {
               return this.list.sort((a,b) => {
                    let mod = 1;
                    if(this.currentSortDir == 'desc'){mod = -1}
                    if(this.recomposeObj(a,this.currentSort) === null ||  typeof(this.recomposeObj(a,this.currentSort)) == 'undefined'){
                        return 1 * mod
                    } else if(this.recomposeObj(b,this.currentSort) === null  || typeof(this.recomposeObj(b,this.currentSort)) == 'undefined'){
                        return -1 * mod
                    } else if(this.recomposeObj(a,this.currentSort) === this.recomposeObj(b,this.currentSort)){
                        return 0;
                    } else if(this.recomposeObj(a,this.currentSort) < this.recomposeObj(b,this.currentSort)){
                        return -1 * mod
                    } else if(this.recomposeObj(a,this.currentSort) > this.recomposeObj(b,this.currentSort)){
                        return 1 * mod
                    }
                }).filter((row, index) => {
                    let start = (this.currentPage-1)*this.numberPerPage;
                    let end = this.currentPage * this.numberPerPage;
                    if(index >= start && index < end) return true;
                });
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Sorting the list'}
                let functionName = 'UiTable:sortedList';
                helper.catchError(errorStats, functionName);
            }
        },
        filteredList(){
            try {
                //let filterBy = this.$options.filters.filterBy;
                if(!!this.searchCompany.toLowerCase() || !!this.searchName.toLowerCase()){
                   this.filter = true
                    this.newval = this.list.filter(post => {
                        if(!!this.searchCompany.toLowerCase()){
                            return post.profile.company ? post.profile.company.toLowerCase().includes(this.searchCompany.toLowerCase()) : null
                        
                        //} else if(!!this.searchName.toLowerCase()){
                        //    console.log('SECOND condition')
                            //debugger;
                        //    return post.profile.full_name.toLowerCase().includes(this.searchName.toLowerCase())
                       } else{
                            return post
                       }
                    })
                if(!!this.searchName.toLowerCase()){
                    var ComObj = this.newval;
                    var filterSearchName = ComObj.filter(searchPost => {
                        if(!!this.searchName.toLowerCase()){
                            return searchPost.profile.full_name ? searchPost.profile.full_name.toLowerCase().includes(this.searchName.toLowerCase()) : null
                        }else{
                        return filterSearchName
                    }
                })
                this.newval = filterSearchName;
                //console.log('Searchname post', this.newval)
                }
                   //console.log('General result post', this.newval)
                    this.searchTrigger = true
                    if(this.searchLogin.length != 0){
                        this.checkboxNullLogin()
                        }
                    if(this.searchConnect.length != 0){
                        this.checkboxNullConnect()
                        }
                    if(this.searchDiffer.length != 0){
                        this.hasCheckboxSearch()
                        }
                    this.analyzeValue = []
                    this.analyzeValue = this.newval
                    this.searchInfo()
                    this.loadList(this.analyzeValue)
                    return this.analyzeValue;
                }else {
                    if(this.searchDiffer.length != 0){
                        this.filter = true
                        this.newval  = this.list
                        this.hasCheckboxSearch()
                        this.analyzeValue = []
                        this.analyzeValue = this.newval;
                        this.searchInfo()
                        this.loadList(this.analyzeValue)
                        return this.analyzeValue;
                    }else if(this.searchLogin != 0){
                        this.filter = true
                        this.newval  = this.list
                        this.checkboxNullLogin()
                        this.analyzeValue = []
                        this.analyzeValue = this.newval;
                        this.searchInfo()
                        this.loadList(this.analyzeValue)
                        return this.analyzeValue;
                    }else if(this.searchConnect != 0){
                        this.filter = true
                        this.newval  = this.list
                        this.checkboxNullConnect()
                        this.analyzeValue = []
                        this.analyzeValue = this.newval;
                        this.searchInfo()
                        this.loadList(this.analyzeValue)
                        return this.analyzeValue;
                    }else{
                        this.filter = false
                        this.getAllItems()
                        this.searchTrigger = false
                        return null;
                    }
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Filtering the list'}
                let functionName = 'UiTable:filteredList';
                helper.catchError(errorStats, functionName);
            }
        }
    }
}
</script>
<style lang="sass">
.spanText{
    position:relative;
    bottom:5px;
    cursor: pointer;
}
    .ui-switch {
        margin-bottom: rem(8px);
    }
.spanText+i{
    cursor: pointer;
}
.disabled{
    visibility:hidden;
}
.borderRed{
    background:#ef9393;
}
.borderYellow{
    background:#efef5c;
}
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
                white-space: nowrap;
                background: #333333;
                color: #fff;
            }
            td {
                padding: 8px;
                line-height: 1.4em;
                vertical-align: top;
                border-top: 1px solid #ddd;
                white-space: nowrap;
                
            }
        }
    }
    .ui-progress-circular-toggle-transition {
        margin: 0 auto;
    }
    .actions {
        text-align: center;
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
.ipad-long .ui-table .table-responsive {
    height: 70vh;
}
</style>