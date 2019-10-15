import Vue from "vue";
import App from "./App.vue";
import "@mdi/font/scss/materialdesignicons.scss";
import "@/assets/scss/app.scss";
import axios from "axios";

Vue.config.productionTip = false;

Vue.prototype.$http = axios.create({
    baseURL: process.env.VUE_APP_API_HOST,
    timeout: 30000, // request timeout
});


new Vue({
  render: h => h(App)
}).$mount("#app");
