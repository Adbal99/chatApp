/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import Vue from 'vue';
import VueRouter from "vue-router";
import store from './js/store/store';

import app from './components/app.vue';
import blank from './components/right/blank.vue';
import right from './components/right/right.vue';

Vue.use(VueRouter);

const routes = [
    {
        name: 'blank',
        path: '/',
        component: blank
    },

    {
        name: 'conversation',
        path: '/converesation/:id',
        component: right
    }
];

const router = new VueRouter({
    mode: "abstract",
    routes
})


new Vue({
    store,
    router,
    render: h => h(app)
}).$mount('#app');

router.replace('/');