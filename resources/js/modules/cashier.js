import Vue from "vue";
import money from "v-money";
import VueHtmlToPaper from "vue-html-to-paper";
import store from "./store/index"
import VueLoading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const options = {
	name: "_blank",
	specs: ["fullscreen=yes", "titlebar=yes", "scrollbars=yes"],
	styles: [
		"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
	]
};

Vue.use(VueHtmlToPaper, options);

Vue.use(money, {precision: 4});

window.Vue = require("vue").default;

// MIXINS
import ResourcesMixin from "../mixins/base/resources";

// //CASHIER
Vue.component(
	"cashier-payment",
	require("../components/Cashier/Payment.vue").default
);

// Select2
Vue.component(
	"select2",
	require("../components/Resources/Select2.vue").default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.use(VueLoading)

const app = new Vue({
	el: "#app",
	store,
	mixins: [ResourcesMixin],
	methods: {}
});

