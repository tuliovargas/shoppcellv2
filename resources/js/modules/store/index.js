import Vue from 'vue'
import Vuex from 'vuex'
import cashier from "./cashier/index"

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        cashier: cashier,
    }
})


export default store
