<template>
    <v-app id="inspire">
        <v-navigation-drawer
            v-model="drawer"
            app
        >
            <v-list-item>
                <v-list-item-content>
                    <img
                        style="width:100%"
                        src="../../images/racquetclub_logo.png"
                        alt="St. Petersburg Racquet Club Logo"
                    />
                </v-list-item-content>
            </v-list-item>

            <v-divider></v-divider>

            <v-list
                dense
                nav
            >
                <v-list-item link to="/" style="text-decoration: none;">
                    <v-list-item-icon>
                        <v-icon>mdi-calendar</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>My Reservations</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/adminpanel" style="text-decoration: none;">
                    <v-list-item-icon>
                        <v-icon>mdi-newspaper-variant</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Reserve a Court</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/schedule" style="text-decoration: none;">
                    <v-list-item-icon>
                        <v-icon>mdi-calendar-range</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Schedule</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/users" style="text-decoration: none;">
                    <v-list-item-icon>
                        <v-icon>mdi-account</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Members</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/report" style="text-decoration: none;">
                    <v-list-item-icon>
                        <v-icon>mdi-file-chart</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Data Report</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item>
                    <v-list-item-icon>
                        <v-icon>mdi-database-export</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Export</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item @click="logout" style="text-decoration: none;">
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

            <v-toolbar-title>Reservation System</v-toolbar-title>
        </v-app-bar>

        <v-main>
            <router-view
                :reservations="reservations"
                @refresh-schedule="getReservations"
                :users="users"
                @refresh-users="getUsers"
                :session_data="session_data"
            >

            </router-view>
        </v-main>
    </v-app>
</template>

<script>

export default {
    props: ['session_data'],

    data: () => ({
        drawer: null,
        reservations: [],
        users: [],
    }),

    methods: {

        logout() {
            axios.post('/logout')
                .then(response => {
                    window.location.href = "login"
                })
        },

        getReservations() {
            axios.get('api/reservations')
                .then(response => {
                    this.reservations = response.data
                })
                .catch(error => {
                    console.log(error);
                })

        },
        getUsers() {
            axios.get('api/users')
                .then(response => {
                    this.users = response.data
                })
                .catch(error => {
                    console.log(error);
                })
        },

    },

    created() {
        console.log("session_data")
        console.log(this.session_data.access)
        // this.getReservations();
        // this.getUsers();
    },

}
</script>
