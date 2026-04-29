import Vue from "vue";
import money from 'v-money'
Vue.use(money, {precision: 4})

window.Vue = require('vue').default;

// MIXINS
import ResourcesMixin from '../mixins/base/resources';


// // CREATE PRODUCT
Vue.component('product-form', require('../components/Products/Form.vue').default);

// Select2
Vue.component('select2', require('../components/Resources/Select2.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',

    mixins:[
        ResourcesMixin
    ],

    methods:{

    },

});
