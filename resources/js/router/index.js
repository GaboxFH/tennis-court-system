
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

import ReserveACourt from "../components/ReserveQueryComponent"
import MyReservations from "../components/MyReservationsComponent"
import Reservations from "../components/ReservationsComponent"
import Schedule from "../components/ScheduleComponent"
import Users from "../components/UsersComponent"
import AdminPanel from "../components/AdminPanelComponent"
import Report from "../components/ReportComponent"

let routes = [
    {
        component: MyReservations,
        name: "my_reservations",
        path: "/",
        meta: {
            authRequired: 'true'
        },
    },
    {
        component: ReserveACourt,
        name: "reserve_a_court",
        path: "/reserve_a_court"
    },
    {
        component: Reservations,
        name: "reservations",
        path: "/reservations"
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
        component: AdminPanel,
        name: "admin_panel",
        path: "/admin_panel"
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
