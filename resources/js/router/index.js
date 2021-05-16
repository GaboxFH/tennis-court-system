
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

import AdminPanel from "../components/ReserveQueryComponent"
import Reservations from "../components/ReservationsComponent"
import Schedule from "../components/ScheduleComponent"
import Users from "../components/UsersComponent"
import Report from "../components/ReportComponent"

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
        component: Schedule,
        name: "schedule",
        path: "/schedule"
    },
    {
        component: Users,
        name: "users",
        path: "/users"
    },
    {
        component: Report,
        name: "report",
        path: "/report"
    }


];

export default new VueRouter({
    routes,
    // mode: 'history',
});
