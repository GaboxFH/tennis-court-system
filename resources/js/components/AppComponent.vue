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
                        The Raquet Club
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
                        <v-icon>mdi-calendar</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>My Reservations</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/adminpanel">
                    <v-list-item-icon>
                        <v-icon>mdi-newspaper-variant</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Admin Panel</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item link to="/help">
                    <v-list-item-icon>
                        <v-icon>mdi-help-box</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>Help</v-list-item-title>
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

                <v-list-item @click="logout">
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
                @refresh-list="getReservations"
            >

            </router-view>
        </v-main>
    </v-app>
</template>

<script>
import ExampleComponent from "./ExampleComponent";

export default {
    data: () => ({
        drawer: null,
        reservations: [],

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

    },

    created() {
        this.getReservations();
    },

}
</script>
