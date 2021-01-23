<template>
    <v-app id="inspire">
        <v-navigation-drawer
            v-model="drawer"
            app
        >
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title class="title">
                        Dashboard
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        Comp Time Tracker
                    </v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>

            <v-divider></v-divider>

            <v-list
                dense
                nav
            >
                <v-list-item link to="/">
                    <v-list-item-icon>
                        <v-icon>mdi-island</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>My Comp Time</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item v-if="$can('View All Users')" link to="/users" @click="getUsers">
                    <v-list-item-icon>
                        <v-icon>mdi-account-group</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title >Users</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/approvals">
                    <v-list-item-icon>
                        <v-icon>mdi-check-decagram</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Approvals</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="">
                    <v-list-item-icon>
                        <v-icon>mdi-help-box</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Help</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/export">
                    <v-list-item-icon>
                        <v-icon>mdi-database-export</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Export</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link @click="logout">
                    <v-list-item-icon>
                        <v-icon>mdi-power</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Log Out</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
            <!--  -->
        </v-navigation-drawer>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-toolbar-title>Comp Time Tracker</v-toolbar-title>
        </v-app-bar>

        <v-main>
            <!--  -->
            <!--            <example-component></example-component>-->
            <!--            <table-component></table-component>-->
            <router-view
                :items="items"
                :user="user"
                :forApprovalItems="forApprovalItems"
                :users="users"
                @refresh-list="getList"
                :roles="roles"
                :myRoles="myRoles"
                :myPerms="myPerms"
            > </router-view>
            <!--            <table-component :items="items" :user="user" @refresh-list="getList"></table-component>-->

        </v-main>

        <footer-component></footer-component>
    </v-app>
</template>


<script>
// import TableComponent from "./TableComponent";
export default {
    // components: {TableComponent},

    data () {
        return {
            items: [],
            forApprovalItems: [],
            // right: null,
            drawer: null,
            user: [],
            users: [],
            roles: [],
            myRoles: [],
            myPerms: [],
        }
    },
    methods: {
        getList() {

            axios.all([
                axios.get(`api/user`),
                axios.get(`api/items`)
            ])
                .then(axios.spread((data1, data2) => {
                    // output of req.

                    this.user = data1.data

                    let first_name = data1.data.first_name
                    let last_name = data1.data.last_name

                    // this.items = data2.data

                    let results = data2.data

                    this.filteredItems = results.filter(item => item.requestor.includes(first_name + " " + last_name ))

                    this.approvalFilteredItems = results.filter(item => item.approver.includes(first_name + " " + last_name))

                    this.items = this.filteredItems

                    this.forApprovalItems = this.approvalFilteredItems
                }));

        },

        getUsers() {

            // let config = {
            //     headers: {
            //         'Authorization': 'Bearer '.$token,
            //         'Accept': 'application/json',
            //     }
            // }

            axios.get('api/users')
                .then(response => {

                    this.users = response.data
                })
                .catch(error => {
                    console.log(error);
                })

        },

        getRoles() {
            axios.get('api/roles')
                .then(response => {

                    this.roles = response.data
                })
                .catch(error => {
                    console.log(error);
                })

        },

        getMyRolesAndPerms() {

            axios.all([
                axios.get(`api/myroles`),
                axios.get(`api/myperms`)
            ])
                .then(axios.spread((roles, perms) => {
                    // output of req.

                    this.myRoles = roles.data

                    this.myPerms = perms.data
                }));
        },

        logout() {
            axios.post('/logout')
                .then(response => {
                    window.location.href = "login"
                })
        }
    },

    created() {
        this.getList();
        // this.getUsers();
        this.getRoles();
        this.getMyRolesAndPerms();
    },
}
</script>


