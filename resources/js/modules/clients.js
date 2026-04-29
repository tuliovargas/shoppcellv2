import Vue from "vue";
import money from 'v-money'
Vue.use(money, {precision: 4})

window.Vue = require('vue').default;

// MIXINS
import ResourcesMixin from '../mixins/base/resources';

//lista de clientes
Vue.component('client-list', require('../components/Clients/list.vue').default);

const app = new Vue({
    el: '#app',

    mixins:[
        ResourcesMixin
    ],

    methods:{
    },

});
