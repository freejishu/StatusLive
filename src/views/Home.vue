<template>

  <el-container class="container-max">
    <el-header class="style-header">
      <h2 class="title">
        <span class="title-big">{{this.main_title}}</span>
        &nbsp;&nbsp;
        <span style="font-size:1.7rem" id="title-small">{{this.main_title_eng}}</span>
      </h2>
    </el-header>
    <el-main class="style-main">
      <el-card shadow="always" class="all-status-card">
        <h4 class="card-title">实时总览<span style="font-size:1rem">&nbsp;Ontime</span></h4>
        <el-row :gutter="20">

          <el-col :span="8">
            <div class="all-status-number">
              <span class="bullet success-bg"></span>&nbsp;&nbsp;<span class="status-span success-color">正常运转&nbsp;XX&nbsp;个</span>
            </div>
          </el-col>

          <el-col :span="8">
            <div class="all-status-number">
              <span class="bullet danger-bg"></span>&nbsp;&nbsp;<span class="status-span danger-color">发生故障&nbsp;XX&nbsp;个</span>
            </div>
          </el-col>

          <el-col :span="8">
            <div class="all-status-number">
              <span class="bullet info-bg"></span>&nbsp;&nbsp;<span class="status-span info-color">正常运转&nbsp;XX&nbsp;个</span>
            </div>
          </el-col>
        </el-row>
        <p>最后一次故障：</p>
      </el-card>

      <el-card shadow="always" class="all-status-card">
        <!-- 数据中心 -->
        <h4 class="card-title">数据中心<span style="font-size:1rem">&nbsp;DataCenter</span></h4>
        

        <el-table :data="this.website_table" style="width: 100%">
          <el-table-column label="状态" width="50">
            <template slot-scope="scope">
              <div v-html="scope.row.status_html"></div>
            </template>
          </el-table-column>

          <el-table-column label="名称">
            <template slot-scope="scope">
              <div v-html="scope.row.friendly_name"></div>
            </template>
          </el-table-column>

          <el-table-column label="名称">
            <template slot-scope="scope">
              <div v-html="scope.row.friendly_name"></div>
            </template>
          </el-table-column>



        </el-table>
      </el-card>

      
    </el-main>
    <el-footer>
      <el-button>默认按钮</el-button>
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
}

.style-main{
  margin-top: 25px;
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
</style>

<script>
// @ is an alias to /src


export default {
  name: 'Index',
  data:function(){
    return{
      main_title: "状态监控",
      main_title_eng: "StausLive",
      json:[],
      website_table:[],
      datacenter_table:[],
    }
  },
  mounted:function(){
    //启动时加载json
    this.get_conf_json();

  },
  methods:{
    get_conf_json(){
      this.$axios.get("./conf.json")
      .then((response) => {
        this.main_title = response.data.title;
        this.main_title_eng = response.data.config_title_english;
        this.json=response.data;
        this.get_status();
      });


    },

    get_status(){
      //console.log(this.json.config_warning_flash);
      var link="";
      if(this.json.config_mode == 1){

        // 1为直接请求uptimerobot
        link = "https://api.uptimerobot.com/v2/getMonitors";
      }else{
        link = this.json.config_proxy_domain;
      }
      var time_now = (Date.parse(new Date()))/1000;

      this.$axios.post(link,{
        api_key : this.json.config_readonly_apikey,
        format  : "json",
        logs  : 1,
        custom_uptime_ratios: "7-30-45",
        custom_uptime_ranges: (time_now-3600*24) +"_"+time_now,
      }).then((response) => {
        if(response.data.stat != "ok"){
          console.log("出现意外");
        }else{
          this.refresh_status(response.data);
        }
        
      });
    },

    refresh_status(json_up){
      //var website_number = 0;
      //var datacenter_number = 0;
      for (let index = 0; index < json_up.monitors.length; index++) {
        //console.log(json_up.monitors[index]);
        if(json_up.monitors[index].type == 1){
          //HTTP检测
          //this.website_table[website_number] = json_up.monitors[index];
          //website_number++;
          if(json_up.monitors[index].status <2){
            json_up.monitors[index].status_html= '<span class="bullet info-bg"></span>';
          }else if(json_up.monitors[index].status == 2){
            json_up.monitors[index].status_html= '<span class="bullet success-bg"></span>';
          }else{
            json_up.monitors[index].status_html= '<span class="bullet danger-bg"></span>';
          }
          this.website_table.push(json_up.monitors[index])




        }else if(json_up.monitors[index].type == 3){
          //Ping检测
          this.datacenter_table.push(json_up.monitors[index])

        }
        
      }

      console.log(this.datacenter_table);
      console.log(this.website_table);



    }




  }

}
</script>
