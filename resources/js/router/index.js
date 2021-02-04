
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

import AdminPanel from "../components/AdminPanelComponent"
import Reservations from "../components/ReservationsComponent"

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

];

export default new VueRouter({
    routes,
    // mode: 'history',
});
