<template>
  <el-container class="container_max">
    <el-header>
      <h2 class="title">
        <span class="title_big">状态监控</span>
        &nbsp;&nbsp;
        <span style="font-size:1.7rem" id="title_small">StatusLive</span>
      </h2>
    </el-header>
    <el-main>
      <el-row :gutter="12">
        <el-col :span="8">
          <el-card shadow="always">
            总是显示
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card shadow="hover">
            鼠标悬浮时显示
          </el-card>
        </el-col>
        <el-col :span="8">
          <el-card shadow="never">
            从不显示
          </el-card>
        </el-col>
      </el-row>
      <el-button>默认按钮</el-button>
    </el-main>
    <el-footer>Footer</el-footer>
  </el-container>
</template>

<style>
.container_max{
  width: 95%;
  max-width: 1200px;
  margin:auto;
}

.title_big{
  font-size:2.5rem;

}

.title{
  text-align: left;
}

</style>

<script>
// @ is an alias to /src
export default {
  name: 'Index',

}
</script>
