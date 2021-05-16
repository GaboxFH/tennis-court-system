/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Vuetify from "../plugins/vuetify"

import router from "./router"

import Permissions from './mixins/Permissions';
Vue.mixin(Permissions);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('users-component', require('./components/UsersComponent.vue').default);
Vue.component('landingpage-component', require('./components/LandingPageComponent.vue').default);
Vue.component('footer-component', require('./components/FooterComponent.vue').default);
Vue.component('app-component', require('./components/AppComponent.vue').default);
Vue.component('reservations-component', require('./components/ReservationsComponent.vue').default);
Vue.component('reserve-query-component', require('./components/ReserveQueryComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    vuetify: Vuetify,
    router,
    el: '#app',
});
