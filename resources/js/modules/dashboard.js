import Vue from "vue";
import money from "v-money";
import VueApexCharts from 'vue-apexcharts'
Vue.use(money, { precision: 4 });

window.Vue = require("vue").default;

// MIXINS
import ResourcesMixin from "../mixins/base/resources";

//lista de clientes
Vue.component("status-cards", require("../components/Dashboard/StatusCards.vue").default);
Vue.component("graphic", require("../components/Dashboard/Graphic.vue").default);
Vue.component('apexchart', VueApexCharts)

const app = new Vue({
	el: "#app",

	mixins: [ResourcesMixin],

	methods: {}
});
