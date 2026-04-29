import Vue from "vue";

window.Vue = require("vue").default;

// MIXINS
import ResourcesMixin from "../mixins/base/resources";

//lista de clientes
Vue.component(
	"supplier-form",
	require("../components/Supplier/Form.vue").default
);

const app = new Vue({
	el: "#app",

	mixins: [ResourcesMixin],

	methods: {}
});
