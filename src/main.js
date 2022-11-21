import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import 'element-ui/lib/theme-chalk/display.css';
import axios from 'axios';

import App from './App.vue';
import router from './router';


Vue.config.productionTip = false;

Vue.prototype.$axios = axios;
//axios.defaults.baseURL = '/v2';



Vue.use(ElementUI);
router.beforeEach((to, from, next) => {
  if(to.meta.title){
    document.title = to.meta.title
  }
  next();
})

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
