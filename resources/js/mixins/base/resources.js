import Vue from 'vue';

window._ = require('lodash');


//import axios
const axios = require('axios');
axios.interceptors.request.use(function (config) {
	return config
}, function (error) {
	return Promise.reject(error);
});

axios.interceptors.response.use(function (response) {
	return response;
}, function (error) {
	return Promise.reject(error);
});
window.axios = require('axios');

//resources
import SelectSquareOptions from '../../components/Resources/SelectSquareOptions.vue';

Vue.component('select-square-options', require('../../components/Resources/SelectSquareOptions.vue').default);
Vue.component('datasource', require('../../components/Resources/Datasource.vue').default);

let NotifierMixin = {
	data: {},
	methods: {
		collapseMenu() {
			$('.sidebar-mini').addClass('sidebar-collapse');
		}
	},
	mounted() {

	}
};
export default NotifierMixin;
