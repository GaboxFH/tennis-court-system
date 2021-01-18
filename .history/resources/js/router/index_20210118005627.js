
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

// import Table from "../components/TableComponent"
// import Users from "../components/UsersComponent"

let routes = [
    {
        component: Table,
        name: "table",
        path: "/"
    },
    {
        component: Users,
        name: "users",
        path: "/users",
        meta: {
            authRequired: 'true'
        },
    }
// ];

export default new VueRouter({
    routes
});
