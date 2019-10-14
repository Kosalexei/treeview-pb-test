import Vue from "vue";
import App from "./App.vue";
import "@/assets/scss/app.scss";
import axios from "axios";

Vue.config.productionTip = false;
console.log(process.env)
Vue.prototype.$http = axios.create({
    baseURL: process.env.VUE_APP_API_HOST,
    timeout: 30000 // request timeout
});


new Vue({
  render: h => h(App)
}).$mount("#app");
