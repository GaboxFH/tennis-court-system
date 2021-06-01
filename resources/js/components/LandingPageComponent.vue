<template>
    <v-app light>
        <v-main>
            <section>
                <v-parallax :src="imageLink.sub_main" height="600">
                    <v-layout column align-center justify-center class="white--text">
                        <h1 class="white--text mb-2 display-1 text-xs-center" style="font-weight: 900; text-shadow: 3px 2px #000000">The Racquet Club of St. Petersburg</h1>
                        <div class="white--text subheading mb-3 text-xs-center" style="font-weight: 900; text-shadow: 2px 2px #000000">Court Reservation System</div>
                        <v-btn class="blue lighten-2 mt-5" dark large href="home/">
                            Get Started
                        </v-btn>
                    </v-layout>
                </v-parallax>
            </section>

            <section>
                <v-layout
                    column
                    wrap
                    class="my-5"
                    align-center
                >
                    <v-flex xs12 sm4 class="my-3">
                        <div class="text-xs-center">
                            <h2 class="headline">The best way to share your amazing stuff</h2>
                            <span class="subheading">
                No more restrictions, no more limits
              </span>
                        </div>
                    </v-flex>
                    <v-flex xs12>
                        <v-container grid-list-xl>
                            <v-layout row wrap align-center>
                                <v-flex xs12 md4>
                                    <v-card class="elevation-0 transparent">
                                        <v-card-text class="text-xs-center">
                                            <v-icon x-large class="blue--text text--lighten-2">mdi-earth</v-icon>
                                        </v-card-text>
                                        <v-card-title primary-title class="layout justify-center">
                                            <div class="headline text-xs-center">Reach the world</div>
                                        </v-card-title>
                                        <v-card-text>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-card class="elevation-0 transparent">
                                        <v-card-text class="text-xs-center">
                                            <v-icon x-large class="blue--text text--lighten-2">mdi-flash</v-icon>
                                        </v-card-text>
                                        <v-card-title primary-title class="layout justify-center">
                                            <div class="headline">Fast feedback</div>
                                        </v-card-title>
                                        <v-card-text>
                                            Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-card class="elevation-0 transparent">
                                        <v-card-text class="text-xs-center">
                                            <v-icon x-large class="blue--text text--lighten-2">mdi-share</v-icon>
                                        </v-card-text>
                                        <v-card-title primary-title class="layout justify-center">
                                            <div class="headline text-xs-center">Create new connections</div>
                                        </v-card-title>
                                        <v-card-text>
                                            At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores.
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-flex>
                </v-layout>
            </section>

            <section>
                <v-parallax :src="imageLink.main" height="380">
                    <v-layout column align-center justify-center>
                        <div class="headline white--text mb-3 text-xs-center"> Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore </div>
                        <em>cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus,</em>
                        <v-btn class="blue lighten-2 mt-5" dark large href="/">
                            Get more info
                        </v-btn>
                    </v-layout>
                </v-parallax>
            </section>

        </v-main>
    </v-app>
</template>

<script>
export default {
    name: "App",
    data: function() {
        return {
            title: "Endorfine",
            imageLink: {
                main:
                    "https://racquetclubstpete.com/wp-content/uploads/2017/03/racquet_club_st_pete_main.jpg",
                sub_main:
                    "https://racquetclubstpete.com/wp-content/uploads/2017/03/racquet_club_st_pete_main.jpg",
                logo:
                    "https://firebasestorage.googleapis.com/v0/b/endorfinevue.appspot.com/o/assets%2Fandroid-chrome-512x512.png?alt=media&token=8a0a66f6-4741-4ff6-8f28-eb9ec74374df",
                social_cover:
                    "https://firebasestorage.googleapis.com/v0/b/endorfinevue.appspot.com/o/assets%2Fo-NIGHTCLUB-facebook.jpg?alt=media&token=cefc5c4c-9714-41da-9c22-f63caf5e89a4"
            },
            email: "",
            emailRules: [
                v => {
                    return !!v || "E-mail is required";
                },
                v =>
                    /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
                    "E-mail must be valid"
            ],
            subscribed: false
        };
    },
    methods: {
        subscribe: function() {
            this.subscribed = !this.subscribed;
        }
    },

    computed: {
        imgHeight: function() {
            var offset = 320;
            console.log("new image height is " + (this.pageHeight - offset));
            return this.pageHeight - offset;
        }
    },

    mounted: function() {
        // this.calculateHeight();
    }
};
</script>

<style scoped>
.finedTitle {
    font-weight: 900;
    text-shadow: 2px 2px #000000;
}

.social-icon {
    font-size: 21px;
    color: white;
}
</style>
