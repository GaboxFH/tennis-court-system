<template>
    
    <div class="container-fluid" style="margin: 0px; padding: 0px;">
        <!-- <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header text-white" style="background-color: rgb(51, 104, 153)">Court Rules</div>

                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header text-white" style="background-color: rgb(51, 104, 153)">Court Activity</div>

                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header text-white" style="background-color: rgb(51, 104, 153)">Members</div>

                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header text-white" style="background-color: rgb(51, 104, 153)">Groups</div>

                    <div class="card-body">
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card">
                    <div class="card-header text-white" style="background-color: rgb(51, 104, 153)">Event Categories</div>

                    <div class="card-body">
                        Club Activity    include admin activity [ ] [ Club Rules ]
                        [                                                        ]
                        Members [ Add Member ]
                        [                                                        ]
                    </div>
                </div>
            </div>
        </div> -->
    

    
        <!-- <v-row no-gutters class="pa-0 ma-0" style="background-color:red">
        <v-col cols="3" class="pa-0 ma-0" align="right">
test
        </v-col>
        <v-col cols="6">
<v-row class="pa-0 ma-0"> 
                <v-col class="pa-0 ma-0">
                    <div class="flex text-center mx-8" 
                        v-bind:style="[($vuetify.breakpoint.name == 'xs' || $vuetify.breakpoint.name == 'sm') ? { 
                            position: 'relative', left: 24 + 'px'
                        } : { position: 'relative', right: 18 + 'px'
                        }]">
                    <h1>Racquet Club Schedule</h1>
                    </div>
                </v-col>
            </v-row>
        </v-col>
        <v-col cols="3" class="pa-0 ma-0" align="right">
test
        </v-col>
      </v-row> -->
        <h1>Admin Panel</h1>
        <br><br>
        <v-tooltip top>
            <template v-slot:activator="{ on, attrs }">
                <v-icon v-bind="attrs" v-on="on">mdi-information-outline</v-icon>
            </template>
            <span>Guests may only play free once per month</span>
        </v-tooltip>
        <br>
        <div v-if="loaded==true">
        <v-row v-for="(rule, index) in club_rules" v-bind:key="club_rules.id">
            <v-col>
                {{club_rules[index].rule}}
            </v-col>
            <v-col>
                <v-text-field
                    v-model="club_rules[index].value"
                    :hint="hints[index]"
                    :label="labels[index]"
                    :disabled="!club_rules[index].active"
                ></v-text-field>
            </v-col>
            <v-col>
                <v-switch
                    v-model="club_rules[index].active"
                    @change="toggleRule(club_rules[index].active,index)"
                ></v-switch>
            </v-col>
        </v-row>
        <v-btn
            @click="updateClubRules()"
        >Update Rules</v-btn>
        

        <br>
        {{ club_rules[0].value }}<br>
        {{ club_rules }}
        </div>
        <div v-else>LOADING</div>




    </div>
</template>

<script>
export default {
// Admin Panel Functions

// Add members
// Customize club reseravation rules
// Send emails

// Max duration of events members may host per day:
    props: ['users', 'rules'],

    data: () => ({
        club_rules: [],
        default_rules: [],
        loaded: false,
        switch1: true,
        labels: [
            "Max duration (hours)", 
            "Max number of reservations", 
            "Time in Advance (hours)", 
            "Guest Policy (days)", 
            "Guest Price (price)", 
        ],
        hints: [
            "Increments of 0.5", 
            "Whole Numbers Only", 
            "Increments of 0.5", 
            "Whole Numbers Only", 
            "Increments of 0.01", 
        ],
        formRules: [
            [
                v => v.length <= 4 || 'Max 1 characters',
                v => !!v || 'Required.',
            ],
            [
                v => v.length <= 4 || 'Max 2 characters',
                v => !!v || 'Required.',
            ],
            [
                v => v.length <= 4 || 'Max 3 characters',
                v => !!v || 'Required.',
            ],
            [
                v => v.length <= 4 || 'Max 4 characters',
            ],
            [
                v => v.length <= 4 || 'Max 2 characters',
                v => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    return pattern.test(v) || 'Invalid e-mail.'
                },
            ]
        ]
        // {
        //     test: [v => v.length <= 4 || 'Max 2 characters']
        // },
        // {
        //     test: [v => v.length <= 4 || 'Max 3 characters']
        // },
        // {
        //     test: [v => v.length <= 4 || 'Max 4 characters']
        // },
        // {
        //     test: [v => v.length <= 4 || 'Max 5 characters']
        // }]
        // formRules: [
        //     (rule: [v => v.length <= 4 || 'Max 1 characters']),
        //     (v => v.length <= 4 || 'Max 2 characters'),
        //     (v => v.length <= 4 || 'Max 3 characters'),
        //     (v => v.length <= 4 || 'Max 4 characters'),
        //     (v => v.length <= 4 || 'Max 5 characters'),

        // ],
        // hourRules: [
        // v => v.length <= 4 || 'Max 4 characters',
        // v => !!v || 'Required.',
        // v => (v || '').length <= 20 || 'Max 20 characters',
        // v => {
        //     const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        //     return pattern.test(v) || 'Invalid e-mail.'
        // },
        // ],

    }),

    computed: {
    },

    watch: {
        // rules (val) {
        //     this.club_rules=val
        //     this.default_rules=this.club_rules
        //     this.loaded = true
        //     console.log(val)
        // },
    },

    created () {
        // this.$emit('load-rules')
        this.getRules()
    },

    methods: {
        updateClubRules() {
            console.log(this.club_rules)
        },
        getRules() {
            axios.get('api/rules')
                .then(response => {
                    this.club_rules = response.data
                    this.default_rules = JSON.parse(JSON.stringify(response.data))
                    this.loaded = true
                })
                .catch(error => {
                    console.log(error);
                })
        },
        toggleRule(state, index){
            if(state){
                // console.log(index)
                if(index == 4){
                    this.club_rules[3].active=true
                } else if (index == 3){
                    this.club_rules[4].active=true
                }
            } else {
                this.club_rules[index].value=this.default_rules[index].value
                if(index == 4){
                    this.club_rules[3].value=this.default_rules[3].value
                    this.club_rules[3].active=false
                } else if (index == 3){
                    this.club_rules[4].value=this.default_rules[4].value
                    this.club_rules[4].active=false
                }
            }
        }
    },
}
</script>
