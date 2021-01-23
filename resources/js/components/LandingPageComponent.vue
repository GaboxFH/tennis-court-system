<template>
    <v-app light>
        <v-main>
            <section>
                <v-parallax :src="imageLink.sub_main" height="600">
                    <v-layout column align-center justify-center class="white--text">
                        <h1 class="white--text mb-2 display-1 text-xs-center" style="font-weight: 900; text-shadow: 3px 2px #000000">The social network for epic content</h1>
                        <div class="white--text subheading mb-3 text-xs-center" style="font-weight: 900; text-shadow: 2px 2px #000000">Unlesh your creativity without limitations</div>
                        <v-btn class="blue lighten-2 mt-5" dark large href="/pre-made-themes">
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
                                            <v-icon x-large class="blue--text text--lighten-2">public</v-icon>
                                        </v-card-text>
                                        <v-card-title primary-title class="layout justify-center">
                                            <div class="headline text-xs-center">Reach the world</div>
                                        </v-card-title>
                                        <v-card-text>
                                            Show your stuff to the whole community of Endorfine not only to your mum or your friends. We love making good content viral. In this moment Endorfine is used by artists who are not famous but that want to share their works to the world. Unfortunately with other social networks this is hard, slow and sometime expensive.
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-card class="elevation-0 transparent">
                                        <v-card-text class="text-xs-center">
                                            <v-icon x-large class="blue--text text--lighten-2">flash_on</v-icon>
                                        </v-card-text>
                                        <v-card-title primary-title class="layout justify-center">
                                            <div class="headline">Fast feedback</div>
                                        </v-card-title>
                                        <v-card-text>
                                            Time is important, we don't want you to waste it. Here you can get a massive feedback from real users in minutes. And if your stuff is appreciated you won't only get positive feedback but also lovely and sincere fans <3
                                        </v-card-text>
                                    </v-card>
                                </v-flex>
                                <v-flex xs12 md4>
                                    <v-card class="elevation-0 transparent">
                                        <v-card-text class="text-xs-center">
                                            <v-icon x-large class="blue--text text--lighten-2">share</v-icon>
                                        </v-card-text>
                                        <v-card-title primary-title class="layout justify-center">
                                            <div class="headline text-xs-center">Create new connections</div>
                                        </v-card-title>
                                        <v-card-text>
                                            Imagine if you can directly speak with the world's population. Don't you think it would be easier to find nice people to interact with? Endorfine is both local and global and help you to connect without limitations with people from your city, your state and your universe!
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
                        <div class="headline white--text mb-3 text-xs-center">Endorfine is a social network that allows everyone to reach a huge audience with a tap </div>
                        <em>With the power of Endorfine you don't need to be famous or post pics of cute cats in order to get visibility</em>
                        <v-btn class="blue lighten-2 mt-5" dark large href="/pre-made-themes">
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
