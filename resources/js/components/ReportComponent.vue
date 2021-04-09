`<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Court Utilization Report</div>

                    <div class="card-body">
                        <p>This report will be a weekly monthly listing of how many time slots each court has been used and total
                            time the court was used.
                        </p>
                        <template>
                            <v-card>
                                <v-card-title>
                                    Court Utilization Time
                                    <v-spacer></v-spacer>
                                <v-menu
                                        ref="menuCourt"
                                        v-model="menuCourt"
                                        :close-on-content-click="false"
                                        :return-value.sync="date"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="date"
                                                prepend-icon="mdi-calendar"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                            </v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="date"
                                            no-title
                                            scrollable
                                            type="month"
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="menuCourt = false"
                                            >Cancel</v-btn>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="$refs.menuCourt.save(date); filterCourts();"
                                            >OK</v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                    <v-spacer></v-spacer>
                                </v-card-title>
                                <v-data-table
                                :headers="headersCourt"
                                :items="computedCourt"
                                ></v-data-table>
                            </v-card>
                        </template>
                    </div>
                </div>
                 <div class="card">
                    <div class="card-header">Members Use Report</div>

                    <div class="card-body">
                        <p>This report will be a monthly listing of how much time (time slot count * 2) each member has played.</p>
                        <template>
                            <v-card>
                                <v-card-title>
                                Members Play Time
                                <v-spacer></v-spacer>
                                <v-menu
                                        ref="menuMember"
                                        v-model="menuMember"
                                        :close-on-content-click="false"
                                        :return-value.sync="dateMember"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="dateMember"
                                                prepend-icon="mdi-calendar"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                            </v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="dateMember"
                                            no-title
                                            scrollable
                                            type="month"
                                        >
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="menuMember = false"
                                            >Cancel</v-btn>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="$refs.menuMember.save(dateMember); filterDate();"
                                            >OK</v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                    <v-spacer></v-spacer>
                                <v-text-field
                                    v-model="searchMembers"
                                    append-icon="mdi-magnify"
                                    label="Search"
                                    single-line
                                    hide-details
                                ></v-text-field>
                                </v-card-title>
                                <v-data-table
                                    dense
                                    :headers="headers"
                                    :items="computedPlay"
                                    :search="searchMembers"
                                ></v-data-table>
                            </v-card>
                        </template>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Rainouts</div>

                    <div class="card-body">
                        <p>When a rainout occurs, the system will automatically send an automated message to the members listed 
                            on all court reservations. The system must provide a button to allow the FD to trigger the email blast.</p>
                        <p>The system will not automaticallly reassign any courts as a result of a rainout. Members must request 
                            courts when they become available again.</p>
                        <v-row
                            align="center"
                            justify="center"
                        >
                            <div class="text-center">
                                <v-dialog 
                                    v-model="dialog"
                                    width="500"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            color="red lighten-2"
                                            dark
                                            v-bind="attrs"
                                            v-on="on"
                                            x-large
                                        >Rainouts</v-btn>
                                    </template>

                                    <v-card>
                                        <v-card-title class="headline grey lighten-2">
                                            Rainouts
                                        </v-card-title>
                                        <v-card-text>
                                            This action will send an automated message to all members who currently have a registeration
                                            today. Are you sure you want to do this?
                                        </v-card-text>
                                        <v-divider></v-divider>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn
                                                color="primary"
                                                text
                                                @click="dialog = false"
                                            >
                                            No
                                            </v-btn>
                                            <v-btn
                                                color="primary"
                                                text
                                                @click="dialog = false; rainouts()"
                                            >Yes</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </div>
                        </v-row>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    data () {
      return {
        // Variables for Court Utilization Report
        dateNow: new Date,
        // dateDayEnd: new Date().setHours(23, 59, 59, 999),
        date: new Date().toISOString().substr(0, 7),
        menuCourt: false,
        search: '',
        headersCourt: [
            {
                text: 'Court Number',
                align: 'start',
                value: 'courtnumber'
            },
            {
                text: 'Total Time (hours)',
                value: 'totaltime',
            }
        ],
        courttime: [],
        // Variables for Member Report
        dateMember: new Date().toISOString().substr(0, 7),
        menuMember: false,
        searchMembers:'',
        playtime: [],
        headers: [
            {
                text: 'Member',
                align: 'start',
                value: 'name'
            },
            {
                text: 'Hours Played this Month',
                value: 'duration'
            }
        ],
        // Variables used for Rain Outs
        dialog: false,
        emailList: [],
      }
    },
    created () {
        this.getMembers();
        this.getCourts();
    },
    computed: {
        computedPlay () {
            return this.playtime
        },
        computedCourt () {
            return this.courttime
        }
    },
    methods: {
        // Methods for Court Report
        getCourts() {
            var start = new Date();
            start.setFullYear(this.date.substr(0,4))
            start.setMonth((this.date.substr(5, 2)-1))
            start.setDate(1)
            axios.get('api/court_play'+'/'+start.getTime())
                .then(response => {
                    this.courttime = response.data
                    this.courttime.forEach(element => element.totaltime = element.totaltime / 3600);
                })
                .catch(error => {
                    console.log(error)
                })
        },
        filterCourts() {
            this.getCourts();
        },
        // Methods for Member Report
        getMembers() {
            var start = new Date();
            start.setFullYear(this.dateMember.substr(0,4))
            start.setMonth((this.dateMember.substr(5, 2)-1))
            start.setDate(1)
            axios.get('api/member_play'+'/'+start.getTime())
                .then(response => {
                    this.playtime = response.data
                    this.playtime.forEach(element => element.duration = element.duration / 3600);
                })
                .catch(error => {
                    console.log(error)
                })
        },
        filterDate () {
            if (this.dateMember !== undefined) {
                this.getMembers();
            }
        },
        // Methods for Rainout
        rainouts () {
            var start = new Date();
            
            var end = new Date();
            end.setHours(23, 59, 59, 59);
            axios.get('api/rainout'+'/'+start.getTime()+'/'+end.getTime())
                .then(response => {
                    this.emailList = response.data
                })
                .catch(error => {
                    console.log(error)
                })
        }
    },
}

</script>