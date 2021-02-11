
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

import AdminPanel from "../components/AdminPanelComponent"
import Reservations from "../components/ReservationsComponent"
import Example from "../components/ExampleComponent"

let routes = [
    {
        component: Reservations,
        name: "reservations",
        path: "/",
        meta: {
            authRequired: 'true'
        },
    },
    {
        component: AdminPanel,
        name: "adminpanel",
        path: "/adminpanel"
    },
    {
        component: Example,
        name: "example",
        path: "/help"
    },

];

export default new VueRouter({
    routes,
    // mode: 'history',
});
