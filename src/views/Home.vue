<template>
  <el-container class="container-max">
    <el-header class="style-header">
      <h2 class="title">
        <span class="title-big">{{this.main_title}}</span>
        &nbsp;&nbsp;
        <span style="font-size:1.7rem" id="title-small">{{this.main_title_eng}}</span>
      </h2>
      <h4 class="title" style="margin-top:10px;">报告生成时间：<span>{{time_text}}</span>&nbsp;&nbsp;<span v-if="json.config_auto_refresh_seconds > 0" ><i class="el-icon-refresh" v-bind:class="{ 'loading-icon': icon_loading }"></i>{{counter}}s</span></h4>
    </el-header>
    <el-main class="style-main" >
      <el-alert :title="alert_title" :type="alert_type" :description="alert_description" show-icon :closable="false" :center="false" style=" text-align: left;"></el-alert>
      <br />

      <el-card shadow="always" class="all-status-card" v-loading="table_loading">
        <h4 class="card-title">实时总览<span style="font-size:1rem">&nbsp;Ontime</span></h4>
        <el-row :gutter="20">

          <el-col :span="8">
            <div class="all-status-number">
              <span class="bullet success-bg"></span>&nbsp;&nbsp;<span class="status-span success-color">正常运转&nbsp;{{success}}&nbsp;个</span>
            </div>
          </el-col>

          <el-col :span="8">
            <div class="all-status-number">
              <span class="bullet danger-bg"></span>&nbsp;&nbsp;<span class="status-span danger-color">发生故障&nbsp;{{danger}}&nbsp;个</span>
            </div>
          </el-col>

          <el-col :span="8">
            <el-tooltip effect="dark" content="由于计划性维护等正常原因而暂时暂停监控。" placement="top-start">
              <div class="all-status-number" >
                <span class="bullet info-bg"></span>&nbsp;&nbsp;<el-badge is-dot :hidden="info==0"><span class="status-span info-color">暂停监控&nbsp;{{info}}&nbsp;个</span></el-badge>
              </div>
            </el-tooltip>
            
          </el-col>
        </el-row>
        <!--<p>最后一次故障：</p>-->
      </el-card>
      
      <!-- 响应时间面板 -->

      <el-dialog :title="response_title" :visible.sync="response_time_dialog" width="60%">
        <el-alert :title="dialog.alert_title" :type="dialog.alert_type" :description="dialog.alert_description" show-icon :closable="false" :center="false" style="text-align: left;"></el-alert>
        <v-chart class="chart" :option="option" :update-options="{notMerge: true}" :autoresize="true" v-if="dialog.chart_show"/>
        <el-empty description="暂无响应时间数据" v-if="!dialog.chart_show"></el-empty>
        
        <span slot="footer" class="dialog-footer">
          <el-button @click="response_time_dialog = false">关闭</el-button>
        </span>
      </el-dialog>

      <el-card shadow="always" class="all-status-card" v-loading="table_loading">
        <!-- 数据中心 -->
        <h4 class="card-title">数据中心<span style="font-size:1rem">&nbsp;DataCenter</span></h4>
        

        <el-table :data="this.datacenter_table" style="width: 100%;" @cell-click="table_click">
          <el-table-column label="状态" width="50" min-width="40">
            <template slot-scope="scope">
              <div v-html="scope.row.status_html" @click="show_respontime(scope.row)"></div>
            </template>
          </el-table-column>

          <el-table-column label="可用率" width="90" min-width="70">
            <template slot-scope="scope">
              <b><span v-bind:class="scope.row.custom_uptime_ratio_class" @click="show_respontime(scope.row)">{{scope.row.custom_uptime_ratio}}%</span></b>
            </template>
          </el-table-column>

          <el-table-column label="名称" width="110" min-width="70">
            <template slot-scope="scope">
              <b><div v-html="scope.row.friendly_name"  @click="show_respontime(scope.row)"></div></b>
            </template>
          </el-table-column>

          <el-table-column :label="'详细可用率（过去'+json.config_history_time+'天）'" min-width="670">
            <template slot-scope="scope">
              <el-tooltip class="" effect="dark" :content="range.time + ' ' + range.range + '%'" placement="top" v-for="range in scope.row.custom_uptime_ranges_a" :key="range.key">
                <span class="square" v-bind:class="[range.info ==1 ? 'info-bg' : (range.range > json.config_success_min ? 'success-bg' : (range.range > json.config_warning_min ? 'warning-bg' : 'danger-bg'))]"></span>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
      </el-card>


      <el-card shadow="always" class="all-status-card" v-loading="table_loading">
        <!-- 网站 -->
        <h4 class="card-title">网站<span style="font-size:1rem">&nbsp;WebSite</span></h4>
        

        <el-table :data="this.website_table" style="width: 100%;" @cell-click="table_click">
          <el-table-column label="状态" width="50" min-width="40">
            <template slot-scope="scope">
              <div v-html="scope.row.status_html"></div>
            </template>
          </el-table-column>

          <el-table-column label="可用率" width="90" min-width="70">
            <template slot-scope="scope">
              <b><span v-bind:class="scope.row.custom_uptime_ratio_class">{{scope.row.custom_uptime_ratio}}%</span></b>
            </template>
          </el-table-column>

          <el-table-column label="名称" width="110" min-width="70">
            <template slot-scope="scope">
              <b><div v-html="scope.row.friendly_name" @click="show_respontime(scope.row)"></div></b>
            </template>
          </el-table-column>

          <el-table-column :label="'详细可用率（过去'+json.config_history_time+'天）'" min-width="670">
            <template slot-scope="scope">
              <el-tooltip class="" effect="dark" :content="range.time + ' ' + range.range + '%'" placement="top" v-for="range in scope.row.custom_uptime_ranges_a" :key="range.key" size="large" color="activity.color">
                <span class="square" :class="[range.info == 1 ? 'info-bg' : (range.range > json.config_success_min ? 'success-bg ' : (range.range > json.config_warning_min ? 'warning-bg' : 'danger-bg'))]"></span>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
      </el-card>

      <el-card shadow="always" class="all-status-card" v-loading="table_loading">
        <!-- 服务 -->
        <h4 class="card-title">服务<span style="font-size:1rem">&nbsp;Service</span></h4>
        

        <el-table :data="this.service_table" style="width: 100%;" @cell-click="table_click">
          <el-table-column label="状态" width="50" min-width="40">
            <template slot-scope="scope">
              <div v-html="scope.row.status_html"></div>
            </template>
          </el-table-column>

          <el-table-column label="可用率" width="90" min-width="70">
            <template slot-scope="scope">
              <b><span v-bind:class="scope.row.custom_uptime_ratio_class">{{scope.row.custom_uptime_ratio}}%</span></b>
            </template>
          </el-table-column>

          <el-table-column label="名称" width="110" min-width="70">
            <template slot-scope="scope">
              <b><div v-html="scope.row.friendly_name" @click="show_respontime(scope.row)"></div></b>
            </template>
          </el-table-column>

          <el-table-column :label="'详细可用率（过去'+json.config_history_time+'天）'" min-width="670">
            <template slot-scope="scope">
              <el-tooltip class="" effect="dark" :content="range.time + ' ' + range.range + '%'" placement="top" v-for="range in scope.row.custom_uptime_ranges_a" :key="range.key" size="large" color="activity.color">
                <span class="square" :class="[range.info == 1 ? 'info-bg' : (range.range > json.config_success_min ? 'success-bg ' : (range.range > json.config_warning_min ? 'warning-bg' : 'danger-bg'))]"></span>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
      </el-card>

      <el-card v-loading="table_loading">
        <el-row :gutter="5">
          <el-col :xs="14" :sm="14" :md="14" :lg="14" :xl="14">
            <h4 class="card-title">状态日志<span style="font-size:1rem">&nbsp;Logs</span></h4>
          </el-col>
          <el-col :xs="10" :sm="10" :md="10" :lg="10" :xl="10">
            <div style="text-align: right;margin-top:8px;">
              <el-switch
                v-model="reverse"
                @change="pages_change()"
                active-text="倒序"
                inactive-text="正序">
              </el-switch>
            </div>
          </el-col>
        </el-row>
        <br />
        <el-empty description="加载中，请稍后..." v-if="table_loading"></el-empty>
        <el-timeline style="text-align: left;" >
          <el-timeline-item v-for="(logs, index) in logs_list_inpage" :key="index" :timestamp="get_full_time(logs.datetime)" :icon="type_to_icon(logs.type)" :color="type_to_color(logs.type)">
          {{logs.name}} {{type_to_text(logs.type)}} - 具体信息：{{logs.reason.code}} - {{logs.reason.detail}}<span v-if="logs.type == 1"> - 持续 {{duration_to_text(logs.duration)}}</span>
          </el-timeline-item>
        </el-timeline>
        <center>
          <el-pagination
            layout="prev, pager, next"
            :total="logs_list.length"
            @current-change="pages_change"
            :current-page.sync="logs_now_page"
            :page-size="json.logs_each_page ? json.logs_each_page : 10"
          >
          </el-pagination>
        </center>
       
        
      </el-card>      
    </el-main>
    <el-footer>
      
      <!--<el-button>默认按钮</el-button>-->
      
    </el-footer>
  </el-container>
</template>

<style>
.container-max{
  width: 95%;
  max-width: 1000px;
  margin:auto;
  
}
.style-header{
  margin-top: 25px;
}

.title-big{
  font-size:2.5rem;

}

.title{
  text-align: left;
  margin: 0px;
  /* padding-bottom:20px;*/
}

.style-main{
  margin-top: 25px;
  overflow-x:hidden;
}

@media (max-width: 479px) {
  .all-status-card{
    width: 100%;
    margin: auto;
    margin-bottom: 20px;
  }
}
@media (min-width: 480px) {
  .all-status-card{
    width: 100%;
    margin: auto;
    margin-bottom: 30px;
  }
}
.bullet{
  display: inline-block;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  vertical-align: middle;
  line-height: 3rem;
}
.bullet{
  display: inline-block;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  vertical-align: middle;
  line-height: 3rem;
}
.square{
  display: inline-block;
  width: 7px;
  height: 28px;
  vertical-align: middle;
  line-height: 3rem;
  margin-right: 3px;
}
.all-status-number{
  text-align: left;
}
.success-bg{
  background: #67C23A;
}

.warning-bg{
  background: #E6A23C;
}

.danger-bg{
  background: #F56C6C;
}

.info-bg{
  background: #909399;  
}

.success-color{
  color: #67C23A;
}

.warning-color{
  color: #E6A23C;
}

.danger-color{
  color: #F56C6C;
}

.info-color{
  color: #909399;
}

.status-span {
  vertical-align: middle;
  height: 100%;
  line-height: 3rem;
  

}
.card-title{
  text-align: left;
  font-size:1.5rem;
  margin: 0px 0px 10px 0px;

}

.loading-icon{
  animation: rotating 2s linear infinite;
}

.chart {
  height: 400px;
}
</style>

<script>
// @ is an alias to /src
import { use } from "echarts/core";
import { SVGRenderer } from "echarts/renderers";
import { LineChart } from 'echarts/charts';
import {
  GridComponent,
  TooltipComponent
} from "echarts/components";
import VChart from "vue-echarts";

use([
  SVGRenderer,
  TooltipComponent,
  GridComponent,
  LineChart
]);


export default {
  name: 'Index',
  components: {
    VChart
  },
  data:function(){
    return{
      main_title: "状态监控",
      main_title_eng: "StatusLive",
      json: [],
      website_table: [],
      datacenter_table: [],
      service_table: [],
      success: 0,
      danger: 0,
      info: 0,
      table_loading: true,
      icon_loading: true,
      time_now: 0,
      alert_type: "info",
      alert_title: "请稍等",
      alert_description: "正在连接服务器加载数据...",
      time_text: "Loading...",
      counter: 1,
      refresh_timer: [],
      logs_list: [], //完整日志列表
      logs_list_inpage: [],  //当前页日志列表
      logs_now_page: 1, //当前页面
      logs_page_total: -1, //总计页数
      logs_each_page: 10, //每页几条
      danger_times: 0,
      reverse: true, //false正序 true倒序
      response_time_dialog: false,
      option : {},
      response_title: '响应时间',
      dialog: { 
        alert_title:'状态加载中……',
        alert_type:'success',
        alert_description:'Loading……',
        chart_show : true,
      }
    }
  },
  mounted:function(){
    //启动时加载json
    this.get_conf_json();
    this.table_loading = true  ;

  },
  beforeUnmount:function() {
    clearInterval(this.refresh_timer);


  },
  methods:{
    get_conf_json(){
      //自检
      if(process.env.VUE_APP_use_env){
        //使用环境变量
        console.log("[StatusLive]使用环境变量模式");
        this.main_title = process.env.VUE_APP_config_title ? process.env.VUE_APP_config_title : "状态监控";
        this.main_title_eng = process.env.VUE_APP_config_title_english;
        this.json = {
          config_title : process.env.VUE_APP_config_title ? process.env.VUE_APP_config_title : "状态监控", 
          config_title_english : process.env.VUE_APP_config_title_english ? process.env.VUE_APP_config_title_english : "StatusLive",

          config_mode: process.env.VUE_APP_config_mode ? process.env.VUE_APP_config_mode : 2,
          config_readonly_apikey: process.env.VUE_APP_config_readonly_apikey ? process.env.VUE_APP_config_readonly_apikey : "USE_SERVER_APIKEY",
          config_proxy_link: process.env.VUE_APP_config_proxy_link ? process.env.VUE_APP_config_proxy_link : "/core.php",
          config_history_time: process.env.VUE_APP_config_history_time ? process.env.VUE_APP_config_history_time : 60,
          config_logs_history_days: process.env.VUE_APP_config_logs_history_days ? process.env.VUE_APP_config_logs_history_days : 30,

          config_success_min: parseFloat(process.env.VUE_APP_config_success_min ? process.env.VUE_APP_config_success_min : 98) ,
          config_warning_min: parseFloat(process.env.VUE_APP_config_warning_min ? process.env.VUE_APP_config_warning_min : 90) ,
          config_auto_refresh_seconds: process.env.VUE_APP_config_auto_refresh_seconds ? process.env.VUE_APP_config_auto_refresh_seconds : 60,

          logs_each_page: parseInt(process.env.VUE_APP_logs_each_page) ? parseInt(process.env.VUE_APP_logs_each_page) : 10
        };
        this.main_title = this.json.config_title;
        this.main_title_eng = this.json.config_title_english;
        this.get_status();
      }else{
        console.log("[StatusLive]使用conf.json模式");
        this.$axios.get("./conf.json")
        .then((response) => {
          this.json=response.data;
          this.main_title = this.json.config_title;
          this.main_title_eng = this.json.config_title_english;
          this.get_status();
        })
        .catch((error) => {
          console.log("[StatusLive]配置项载入失败了，检查一下配置项吧:(");
          console.log(error);
          this.$notify.error({
            title: '出现异常',
            message: '连接服务器失败，请刷新页面尝试恢复。',
            type: 'danger'
          });

      });
      }



    },

    get_status(){
      this.alert_type = "info";
      this.alert_title = "请稍等";
      this.alert_description = "正在连接服务器加载数据...";
      this.icon_loading = true; 



      //console.log(this.json.config_warning_flash);
      var link="";
      if(this.json.config_mode == 1){

        // 1为直接请求uptimerobot
        link = "https://api.uptimerobot.com/v2/getMonitors";
      }else{
        link = this.json.config_proxy_link;
      }

      //生成时间树
      var uptime_ranges = "";
      this.time_now = (Date.parse(new Date()))/1000;
      uptime_ranges = (this.time_now-86400) + "_" + this.time_now ;
      if(this.json.config_history_time>0){
        for (let ii = 1; ii < this.json.config_history_time; ii++) {
          uptime_ranges=uptime_ranges+"-"+(this.time_now-(60*60*24*(ii+1)))+"_"+(this.time_now-(60*60*24*ii)); 
        }
      }
      this.$axios.post(link,{
        api_key : this.json.config_readonly_apikey,
        format  : "json",
        logs  : 1,
        custom_uptime_ratios: "7",
        custom_uptime_ranges: uptime_ranges,
        custom_down_durations: 1,
        logs_start_date: this.time_now-86400*this.json.config_logs_history_days,
        response_times: 1,
        response_times_average : 5
      }).then((response) => {
        if(response.data.stat != "ok"){
          console.log("[StatusLive]UptimeRobot API有返回错误信息：");
          console.log(response.data);
          if(this.danger_times<3){
            this.$notify.error({
              title: '出现异常',
              message: '请求参数异常，5秒后将会自动重试...',
              type: 'danger'
            });
            this.danger_times++;
            this.counter=5;
            this.refresh_timer = setInterval(this.countdown_function, 1000);
          }else{
            this.$notify.error({
              title: '出现异常',
              message: '请求参数异常。连续三次连接服务器失败，请检查您的网络（或配置），并刷新页面重试。',
            });
            this.icon_loading = false; 

          }
        }else{
          this.refresh_status(response.data);
        }
        
      }).catch((error) => {
          console.log("[StatusLive]连接UptimeRobot API时出现问题：");
          console.log(error);
          if(this.danger_times<3){
            this.$notify.error({
              title: '出现异常',
              message: '连接服务器失败，5秒后将会自动重试...',
              type: 'danger'
            });
            this.danger_times++;
            this.counter=5;
            this.refresh_timer = setInterval(this.countdown_function, 1000);
            this.icon_loading = false; 
          }else{
            this.$notify.error({
              title: '出现异常',
              message: '连接服务器失败。连续三次连接服务器失败，请检查您的网络（或配置），并刷新页面重试。',
            });
            this.icon_loading = false; 
          }

      });

    },

    refresh_status(json_up){
      //var website_number = 0;
      //var datacenter_number = 0;
      var logs_list_temp = [];
      
      this.success = 0;
      this.danger = 0;
      this.info = 0;
      this.website_table=[];
      this.datacenter_table=[];
      this.service_table=[];
      for (let index = 0; index < json_up.monitors.length; index++) {
        //当前状态
        if(json_up.monitors[index].status < 2){
          json_up.monitors[index].status_html= '<span class="bullet info-bg"></span>';
          this.info++;
        }else if(json_up.monitors[index].status == 2){
          json_up.monitors[index].status_html= '<span class="bullet success-bg"></span>';
          this.success++;
        }else{
          json_up.monitors[index].status_html= '<span class="bullet danger-bg"></span>';
          this.danger++;
        }

        //处理可用率
        if(json_up.monitors[index].status < 2){
          json_up.monitors[index].custom_uptime_ratio_class = "info-color";
        }else if(json_up.monitors[index].custom_uptime_ratio < this.json.config_success_min && json_up.monitors[index].status == 2){
          json_up.monitors[index].custom_uptime_ratio_class = "warning-color";
        }else if(json_up.monitors[index].custom_uptime_ratio < this.json.config_warning_min || json_up.monitors[index].status >= 8){
          json_up.monitors[index].custom_uptime_ratio_class = "danger-color";
        }else{
          json_up.monitors[index].custom_uptime_ratio_class = "success-color";
        }
        var custom_uptime_ranges_a = [];
        json_up.monitors[index].custom_uptime_ranges_a = [];
        custom_uptime_ranges_a = json_up.monitors[index].custom_uptime_ranges.split("-");
        for (let ia = 0; ia < custom_uptime_ranges_a.length; ia++) {
          json_up.monitors[index].custom_uptime_ranges_a.push({ 
            key: ia, 
            range: custom_uptime_ranges_a[ia], 
            info: (this.time_now-(60*60*24*ia) < json_up.monitors[index].create_datetime ? 1 : 0),
            time: this.get_time_ymd(this.time_now-(60*60*24*ia))
          });
        }
        //console.log(json_up.monitors[index]);
        if(json_up.monitors[index].type == 1){
          //HTTP检测归位
          this.website_table.push(json_up.monitors[index])
        }else if(json_up.monitors[index].type == 3){
          //Ping检测归位
          this.datacenter_table.push(json_up.monitors[index])
        }else{
          //没地方去的去这里
          this.service_table.push(json_up.monitors[index])
        }

        //处理日志
        var logs_one=[];
        for (let logs_i = 0; logs_i < json_up.monitors[index].logs.length; logs_i++) {

          logs_one = { 
            name:json_up.monitors[index].friendly_name, 
            datetime: String(json_up.monitors[index].logs[logs_i].datetime), 
            duration: json_up.monitors[index].logs[logs_i].duration,
            reason: json_up.monitors[index].logs[logs_i].reason,
            type: Number(json_up.monitors[index].logs[logs_i].type),
          };
          logs_list_temp.push(logs_one);

        }
      }

      //日志排序
      logs_list_temp.sort(function(a, b){return parseInt(a.datetime) - parseInt(b.datetime)});
      this.logs_list = logs_list_temp;
    
      //console.log(this.datacenter_table);
      //console.log(this.website_table);
      //确定最终提示
      if(this.danger>0){
        if(this.danger>=this.success){
          this.alert_type = "danger";
        }else{
          this.alert_type = "warning";
        }
        this.alert_title = "服务异常";
        this.alert_description = "很抱歉，当前部分服务遇到问题，我们会尽快修复。您可持续关注本页面以获取最新信息。";
      }else{
        this.alert_type = "success";
        this.alert_title = "服务正常";
        this.alert_description = "当前所有服务正常运转。您可持续关注本页面以获取最新信息。";

      }
      //刷新时间代码
      var date = new Date(this.time_now*1000);
      var Y = date.getFullYear() + '-';
      var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
      var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
      var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
      var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
      var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
      this.time_text = Y+M+D+h+m+s;
      this.danger_times = 0;

      this.table_loading = false;
      this.icon_loading = false; 
      this.counter = this.json.config_auto_refresh_seconds;
      if(this.counter > 0){
        //0为不刷新
        this.refresh_timer = setInterval(this.countdown_function, 1000);

      }
      this.pages_change();
    },
    countdown_function(){
      //定时刷新
      this.counter--;
      if(this.counter==0){
        //启动刷新 销毁前一个定时器
        clearInterval(this.refresh_timer);
        this.counter = this.json.config_auto_refresh_seconds;
        this.get_status();
      }
    },
    get_full_time(time){
      var date = new Date(time*1000);
      var Y = date.getFullYear() + '-';
      var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
      var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate()) + ' ';
      var h = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
      var m = (date.getMinutes() <10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
      var s = (date.getSeconds() <10 ? '0' + date.getSeconds() : date.getSeconds());
      return Y+M+D+h+m+s;
    },
    get_time_ymd(time){
      var date = new Date(time*1000);
      var Y = date.getFullYear() + '-';
      var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
      var D = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate());
      return Y+M+D;
    },
    type_to_icon(type){
      type=Number(type);
      if(type == 1){
        return 'el-icon-close';
      }else if(type == 2){
        return 'el-icon-check';
      }else if(type == 98){
        return 'el-icon-video-play';
      }else if(type == 99){
        return 'el-icon-video-pause';
      }else{
        return 'el-icon-full-screen';
      }
    },
    type_to_color(type){
      type=Number(type);
      if(type == 1){
        return '#F56C6C';
      }else if(type == 2){
        return '#67C23A';
      }else if(type == 98){
        return '#909399';
      }else if(type == 99){
        return '#909399';
      }else{
        return '#303133';
      }
    },
    type_to_text(type){
      type=Number(type);
      if(type == 1){
        return '发生故障';
      }else if(type == 2){
        return '恢复正常';
      }else if(type == 98){
        return '开始';
      }else if(type == 99){
        return '开始维护（暂停监控）';
      }else{
        return '发生特情';
      }
    },
    duration_to_text(duration){
      duration=Number(duration);
      var s = duration % 60;
      var m = (duration-s) /60;
      var h = (duration-s-m*60)/60
      if( h > 0){
        //console.log(h);
        return `${h} 小时 ${m} 分钟 ${s} 秒`;

      }else{
        return ` ${m} 分钟 ${s} 秒`;
      }
    },
    pages_change(){

      let logs_list_temp_change = JSON.parse(JSON.stringify(this.logs_list)) ; //解决浅拷贝问题 默认正序
      if(this.reverse){
        //倒序 翻转
        logs_list_temp_change.reverse();
      }
      this.logs_each_page  = this.json.logs_each_page;
      this.logs_page_total = Math.ceil(logs_list_temp_change.length / this.logs_each_page ); //计算页数总计
      
      if(this.logs_now_page > this.logs_page_total){
        this.logs_now_page = 1; //如果超出现有页数 回第一页
      }
      this.logs_list_inpage = [];//数组置空
      
      for (let page_index = 0; page_index < this.logs_each_page; page_index++) {
        if(this.logs_each_page*(this.logs_now_page-1)+page_index < logs_list_temp_change.length){
          this.logs_list_inpage.push(logs_list_temp_change[this.logs_each_page*(this.logs_now_page-1)+page_index]);
        }
      }
    },
    show_respontime(t){
      //let lastLogTypeBeforeStartDate = t.lastLogTypeBeforeStartDate;
      let response_times = t.response_times;
      console.log(t);
      let data = [];
      let time = [];
      for (let resp_index = 0; resp_index < response_times.length; resp_index++) {
        data.push(this.get_full_time(response_times[resp_index].datetime));
        time.push(response_times[resp_index].value > 1 ? response_times[resp_index].value : '-' );
        
      }
      //console.log(data);
      //console.log(time);
      //this.option.xAxis.data = data;
      //this.option.series.data = time;
      this.response_title = t.friendly_name + ' 详细信息'

      if(t.status < 2){
        this.dialog.alert_title = t.friendly_name+' 监控因故暂停，监控期平均在线率为 '+t.custom_uptime_ratio+'% ，最近一次探测响应时间：'+time[0]+' ms';
        this.dialog.alert_type = 'info';
      }else if(t.custom_uptime_ratio < this.json.config_success_min && t.status == 2){
        this.dialog.alert_title = t.friendly_name+' 当前状态正常，在线率为 '+t.custom_uptime_ratio+'% ，最近一次探测响应时间：'+time[0]+' ms';
        this.dialog.alert_type = 'warning';
      }else if(t.custom_uptime_ratio < this.json.config_warning_min || t.status >= 8){
        this.dialog.alert_title = t.friendly_name+' 当前状态'+(t.status >= 8 ? '异常' : '正常')+'，在线率为 '+t.custom_uptime_ratio+'% ，最近一次探测响应时间：'+time[0]+' ms';
        this.dialog.alert_type = 'danger';
      }else{
        this.dialog.alert_title = t.friendly_name+' 当前状态正常，在线率为 '+t.custom_uptime_ratio+'% ，最近一次探测响应时间：'+time[0]+' ms';
        this.dialog.alert_type = 'success';
        /* */
      }
      this.dialog.alert_title = t.friendly_name+' '+( t.status < 2 ? ' 监控因故暂停，近期平均在线率为 ' :'当前状态'+(t.status >= 8 ? '异常' : '正常') +'，在线率为 ') + t.custom_uptime_ratio+'% ，' + ( time[0]  ?  '最近一次探测响应时间：'+time[0]+' ms' : '暂无最近探测响应时间数据'  ) + "。";
      
      this.dialog.alert_description = '最近动态：'+this.get_full_time(t.lastLogTypeBeforeStartDate.datetime)+' - '+this.type_to_text(t.lastLogTypeBeforeStartDate.type) +' - 具体信息：'+t.lastLogTypeBeforeStartDate.reason.code + ' - ' +t.lastLogTypeBeforeStartDate.reason.detail +  (t.lastLogTypeBeforeStartDate.type == 1 ? ' - 持续' +this.duration_to_text(t.lastLogTypeBeforeStartDate.duration) : '');

      if(!time[0]){
        this.dialog.chart_show = false;

      }else{
        this.dialog.chart_show = true;
      }



      
      /*
      dialog: { 
        alert_title:'状态加载中……',
        alert_type:'success',
        alert_description:'Loading……',
      }
      */


      this.response_time_dialog = true;
      this.option = {

        xAxis: {
          type: 'category',
          data: data.reverse()
        },
        tooltip: {
          trigger: 'axis',
        },
        grid: {
          show: true
        },
        yAxis: {
          type: 'value',
          name: 'ResponseTime(ms)',
        },
        series: [
          {
            data: time.reverse(),
            type: 'line',
            smooth: true,
            connectNulls: true,
            symbol: "none",
            endLabel: {
              show: true
            }
          }
        ]
      }
      //console.log(this.option.series.data);
      

    },
    table_click(a){
      this.show_respontime(a);

    }
  }
}
</script>
