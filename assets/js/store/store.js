import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

import user from './modules/user';
import converastion from './modules/conversation';

export default new Vuex.Store( {
    modules: {
        converastion,
        user
    }
})