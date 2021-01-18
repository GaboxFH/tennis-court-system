
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter)

// import Table from "../components/TableComponent"
// import Example from "../components/ExampleComponent"
// import Users from "../components/UsersComponent"

// let routes = [
//     {
//         component: Table,
//         name: "table",
//         path: "/"
//     },
//     {
//         component: Users,
//         name: "users",
//         path: "/users",
//         meta: {
//             authRequired: 'true'
//         },
//     },
//     {
//         component: Approvals,
//         name: "approvals",
//         path: "/approvals"
//     },
//     {
//         component: Export,
//         name: "export",
//         path: "/export"
//     }
// ];

export default new VueRouter({
    routes
});
