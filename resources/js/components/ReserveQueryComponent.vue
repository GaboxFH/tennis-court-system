<template>
    <div class="pa-6 ma-auto" align="center" fluid>
        <div class="pb-5 row justify-content-center">
            <img src="../../images/TennisCourtMap.png" alt="Map of the Tennis Courts" style="max-width: 1000px; min-width: 100px; height: auto; border: 5px solid #333;"/>
        </div>
        <v-card max-width=1400>
            <v-toolbar class="mb-3">
            <v-card-title>Find Available Courts</v-card-title>
            <v-spacer></v-spacer>
            <v-col cols="3">
            <v-select
                v-model="search_input"
                :items="search_type"
                label="Search by:"
                outlined
                dense
                style="margin-top:25px;"
            ></v-select>
            </v-col>
            </v-toolbar>
            <v-progress-linear
            :active="loading"
            :indeterminate="loading"
            absolute
            bottom
            color="primary"
            ></v-progress-linear>
        <v-row class="px-3">
            <v-col>
                <v-select
                    v-model="court_input"
                    :items="court_type"
                    label="Court Type"
                ></v-select>
            </v-col>
            <v-col>
                <v-menu
                    ref="dropdown_cal"
                    v-model="dropdown_cal"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    max-width="590px"
                    min-width="auto"
                >
                    <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-bind="attrs"
                        v-on="on"
                        min-width="200px"
                        label="Date"
                        v-model="date_input"
                    ></v-text-field>
                    </template>
                    <v-date-picker
                    v-model="date_input"
                    no-title
                    @input="dropdown_cal = false"
                    ></v-date-picker>
                </v-menu>
            </v-col>
            <v-col v-if="search_input=='Start Time'">
                <v-select
                    v-model="start_input"
                    :items="start_type"
                    item-text="show"
                    item-value="val"
                    label="Start Time"
                ></v-select>
            </v-col>
            <v-col v-else>
                <v-select
                    v-model="dur_input"
                    :items="dur_type"
                    item-text="show"
                    item-value="val"
                    label="Duration"
                ></v-select>
            </v-col>
            <v-col align="right" cols="auto">
                <v-btn
                min-width="75px"
                color="primary"
                style="margin-top:10px;"
                @click="showEvents"
                >Find
                </v-btn>
            </v-col>
        </v-row>
        <div v-if="results=='start_time'" align="left"> 
            <v-divider></v-divider>
            <v-card-title class="pt-0"><p>Showing Available Time Slots for <b>{{selectedEvent.dateShow}}</b> at <b>{{selectedEvent.paramShow}}</b></p></v-card-title>
            <v-card-subtitle class="pb-0">217 results</v-card-subtitle>
            <div v-for="(n, ind) in avail_slots" v-bind:key="n.court">
            <div v-if="n[0] || n[1] || n[2] || n[3]">
            <v-divider></v-divider>
            <v-row class="px-3">
                <v-col cols="auto" align="left">
                    <v-card-subtitle class="ma-0 pa-0">Court {{ind}}</v-card-subtitle>
                    <v-chip-group
                        column
                    >
                        <div
                        v-for="(t,index) in n"
                        :key="t.start_times"
                        >
                        <div v-if="t">
                            <v-chip @click="open_reservation_dialog(dur_type[index].val,ind)">
                            <!-- {{ dur_type[index].show }} -->
                            {{ bugFix(index*30+30) }}
                            <!-- {{ index }} -->
                            </v-chip>
                        </div>
                        </div>
                    </v-chip-group>
                </v-col>
            </v-row>
            </div>
            </div>
        </div>
        <div v-else-if="results=='duration'" align="left">
            <v-divider></v-divider>
            <v-card-title class="pt-0"><p>Showing Available <b>{{dur_type[selectedEvent.param-1].show}}</b> Time Slots on <b>{{selectedEvent.dateShow}}</b></p></v-card-title>
            <v-card-subtitle class="pb-0">217 results</v-card-subtitle>
            <div v-for="(n, ind) in avail_slots" v-bind:key="n.court">
            <div v-if="n[0] || n[1] || n[2] || n[3] || n[4] || n[5] || n[6] || n[7] || n[8] || n[9] 
            || n[10] || n[11] || n[12] || n[13] || n[14] || n[15] || n[16] || n[17] || n[18] || n[19] 
            || n[20] || n[21] || n[22] || n[23]">
            <v-divider></v-divider>
            <v-row class="px-3">
                <v-col cols="auto" align="left">
                    <v-card-subtitle class="ma-0 pa-0">Court {{ind}}</v-card-subtitle>
                    <v-chip-group
                        column
                    >
                    <div
                    v-for="(t,index) in n"
                    :key="t.start_times"
                    >
                    <div v-if="t">
                        <v-chip @click="open_reservation_dialog(start_type[index].val,ind)">
                        {{start_type[index].show}} - {{start_type[index+selectedEvent.param].show}}
                        </v-chip>
                    </div>
                    </div>
                    </v-chip-group>
                </v-col>
            </v-row>
            </div>
            </div>
        </div>
        <div v-if="results=='onload'">
            <v-row>
                <v-col>
                    <v-divider></v-divider>
                    <v-card-title class="pt-0">Reservation Rules:</v-card-title>
                    <v-card-text>
                    <!-- Members may only host up to 2 hours per day.<br> -->
                    Reservations may be made up to 48 hours in advance.<br>
                    Please see the front desk for guests who have played more than once in a caledar month<br>
                    </v-card-text>
                </v-col>
            </v-row>
        </div>
        </v-card>

        <div v-if="results=='loading'">
            <v-row>
                <v-col>
                    <br><br><br><br><br><br><br><br><br><br><br><br>
                </v-col>
            </v-row>
        </div>

        <v-dialog v-model="dialog_edit" persistent max-width="600px"
        >
        <v-card>
            <v-toolbar v-if="selectedEvent" class="mb-3 headline grey lighten-2">
                <v-card-title>{{ selectedEvent.name }}</v-card-title>
                <v-spacer></v-spacer>
                <v-menu
                left
                bottom
                offset-y
                >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                    icon
                    v-bind="attrs"
                    v-on="on"
                    >
                    <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </template>
        
                <v-list>
                    <v-list-item @click="areyousure=true">
                        <v-list-item-title>Delete Event</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="repeat_dialog=true">
                        <v-list-item-title>Repeat Event</v-list-item-title>
                    </v-list-item>
                </v-list>
                </v-menu>
            </v-toolbar>

            <div v-if="selectedEvent" class="mx-5">
            <v-row>
            <v-col>
            <v-text-field
              label="Date"
              v-model="selectedEvent.dateShow"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            <v-col>
            <v-text-field
              label="Start Time"
              v-model="selectedEvent.startDisplay"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            <v-col>
            <v-text-field
              label="End Time"
              v-model="selectedEvent.endDisplay"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            </v-row>
            <v-text-field v-model="selectedEvent.name" label="Event Title"></v-text-field>
            <v-row>
                <v-col cols="7">
            <v-autocomplete
                v-model="selectedEvent.participants"
                :items="computedMembers"
                chips
                deletable-chips
                label="Participants"
                hide-selected   
                multiple
                item-text="name"
                item-value="id"
                :allow-overflow="false"
                @input="searchInput=null"
                :search-input.sync="searchInput"
            >
            
            </v-autocomplete>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="4">
                <v-text-field height=42 readonly v-model="selectedEvent.num_of_guests" type="number" label="# of Guests" append-outer-icon="mdi-plus" @click:append-outer="selectedEvent.num_of_guests = parseInt(selectedEvent.num_of_guests,10) + 1" prepend-icon="mdi-minus" @click:prepend="selectedEvent.num_of_guests = parseInt(selectedEvent.num_of_guests,10) - 1"></v-text-field>
            </v-col>
            </v-row>

            </div>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="saveEvent">Save</v-btn>
                <v-btn color="blue darken-1" text @click="dialog_edit=false">Close</v-btn>
            </v-card-actions>        
        </v-card>
        </v-dialog>

        <v-snackbar v-model="message" :timeout="timeout" top>
            {{ message_text }}
        <template v-slot:action="{ attrs }">
            <v-btn color="blue" text v-bind="attrs" @click="message = false"> Close</v-btn>
        </template>
    </v-snackbar>
    </div>
</template>

<script>
import moment from 'moment-timezone'

export default {
    // props: ['reservations','users','session_data'],
    props: ['users','session_data'],
        // Showing Available 2 Hr Time Slots on 4/10 
        // 217 results

        // Available Times Slots for 4/10 @8:00am 
        // 217 results
    data: () => ({
        members: null,
        searchInput: null,
        member_id: null,
        selectedEvent: null,
        dialog_edit: false,
        results: "onload",
        message: false,
        message_text: '',
        timeout: 3000,        
        loading: false,
        // search_input: "Duration",
        search_input: "Start Time",
        search_type: ["Duration","Start Time"],
        court_input: "Clay Courts",
        court_type: ["Clay Courts","Hard Courts","Ball Machine"],
        date_input: null,
        dropdown_cal: false,
        start_input: "08:00:00",
        start_type: [
            {show:"8:00 am", val:"08:00:00"},
            {show:"8:30 am", val:"08:30:00"},
            {show:"9:00 am", val:"09:00:00"},
            {show:"9:30 am", val:"09:30:00"},
            {show:"10:00 am",val:"10:00:00"},
            {show:"10:30 am",val:"10:30:00"},
            {show:"11:00 am",val:"11:00:00"},
            {show:"11:30 am",val:"11:30:00"},
            {show:"12:00 pm",val:"12:00:00"},
            {show:"12:30 pm",val:"12:30:00"},
            {show:"1:00 pm", val:"13:00:00"},
            {show:"1:30 pm", val:"13:30:00"},
            {show:"2:00 pm", val:"14:00:00"},
            {show:"2:30 pm", val:"14:30:00"},
            {show:"3:00 pm", val:"15:00:00"},
            {show:"3:30 pm", val:"15:30:00"},
            {show:"4:00 pm", val:"16:00:00"},
            {show:"4:30 pm", val:"16:30:00"},
            {show:"5:00 pm", val:"17:00:00"},
            {show:"5:30 pm", val:"17:30:00"},
            {show:"6:00 pm", val:"18:00:00"},
            {show:"6:30 pm", val:"18:30:00"},
            {show:"7:00 pm", val:"19:00:00"},
            {show:"7:30 pm", val:"19:30:00"},
            {show:"8:00 pm", val:"20:00:00"}
        ],
        // start_type: [{show:"8:00 am",val:"08:00:00"},,"8:30 am","9:00 am","9:30 am","10:00 am","10:30 am","11:00 am","11:30 am","12:00 pm","12:30 pm","1:00 pm","1:30 pm","2:00 pm","2:30 pm","3:00 pm","3:30 pm","4:00 pm","4:30 pm","5:00 pm","5:30 pm","6:00 pm","6:30 pm","7:00 pm","7:30 pm"],
        dur_input: 2,
        dur_type: [
            {show:"30 mins",  val: 1, data: '00:30:00'},
            {show:"1 Hour",   val: 2, data: '01:00:00'},
            {show:"1.5 Hours",val: 3, data: '01:30:00'},
            {show:"2 Hours",  val: 4, data: '02:00:00'}
        ],
        // dur_type: ["30 mins","1 Hour","1.5 Hours","2 Hours"],
        
        avail_slots: []
    }),
    created () {
        this.date_input = moment().tz('America/New_York').format('YYYY-MM-DD')
        // console.log("session_data :)")
        // console.log(this.session_data.id)

        // this.$emit('refresh-schedule')
        this.$emit('refresh-users')
    },
    watch: {
        users (val) {
            this.members=val
        },
    },
    computed: {
        computedMembers(){
            // if(this.selectedEvent)
            // dont show host on option list
            var members_no_host = JSON.parse(JSON.stringify(this.members))
            var host_index = members_no_host.map(function(item) { return item.id; }).indexOf(this.session_data.id)
            members_no_host.splice(host_index, 1)
        
            // remove host from list
            // if(Object.entries(this.selectedEvent.participants).length != 0){
            //     var index = this.selectedEvent.participants.indexOf(this.session_data.id)
            //     if (index >= 0) this.selectedEvent.participants.splice(index, 1)
            // }
            // console.log("members_no_host")
            // console.log(members_no_host)
            return members_no_host
            // return this.members
        }
    },
    methods: {
        saveEvent(){
            var ordered_participants_ids = JSON.parse(JSON.stringify(this.selectedEvent.participants))
            if(this.selectedEvent.host_id==''){ 
                this.selectedEvent.host_id = null
            }
            if(this.selectedEvent.host_id!=null){
                ordered_participants_ids.push(this.selectedEvent.host_id)
            }
            ordered_participants_ids.sort(function(a, b){return a-b})

            let item = JSON.parse(JSON.stringify(this.selectedEvent))
            item.ordered_participants_ids = ordered_participants_ids
            item.date = this.date_input
            this.newCompTimePayload = {
                item
            }
            console.log(this.newCompTimePayload)
            // this.newCompTimePayload.ordered_participants_ids = ""
            axios.post('api/reservation/memberStore', this.newCompTimePayload)
            .then((response) => {
                console.log(response);
                if(response.data == "error"){
                    this.message_text = "Time Conflict"
                    this.message = true
                } else{
                    this.message_text = "Reservation Created"
                    this.message = true
                    this.$router.push({ name: 'reservations' });
                    // window.location.href = 'http://localhost:3000/home#/';
                }
            }, (error) => {
                console.log(error);
            });

        },
        open_reservation_dialog(n,court){
            console.log(n)
            if(n<=4){
                this.selectedEvent.category=court;
                this.selectedEvent.end=this.selectedEvent.start+n*30*60*1000;
                this.selectedEvent.duration=this.dur_type[n-1].data

                this.selectedEvent.startDisplay = this.getTimes(new Date(this.selectedEvent.start))
                this.selectedEvent.endDisplay = this.getTimes(new Date(this.selectedEvent.end))
                console.log("dur")
                this.dialog_edit = true

            } else {
                this.selectedEvent.category=court;
                // console.log(this.selectedEvent.date)
                this.selectedEvent.start=this.hhmmssTomilli(n)+this.selectedEvent.date;
                this.selectedEvent.end=this.selectedEvent.start+this.selectedEvent.param*30*60*1000;

                this.selectedEvent.duration=this.dur_type[this.selectedEvent.param-1].data
                console.log("time")

                this.selectedEvent.startDisplay = this.getTimes(new Date(this.selectedEvent.start))
                this.selectedEvent.endDisplay = this.getTimes(new Date(this.selectedEvent.end))
                this.dialog_edit = true
            }
            console.log(this.selectedEvent)
        

        },
        showEvents() {
            this.loading = true
            this.results="loading"
            this.avail_slots = ''
            // console.log("Input: ")
            // console.log(this.start_input)
            // console.log(new Date(this.start_input).getTime())
            //4 hours added to account for timezone diff seems sketch
            // const date_input_milliseconds = new Date(this.date_input).getTime()+4*60*60*1000 
            // console.log(date_input_milliseconds)
            // console.log(moment(this.date_input).valueOf())
            const date_input_milliseconds = moment(this.date_input).valueOf()
            // console.log(date_input_milliseconds)
            // console.log(new Date(this.selectedEvent.start))
            var param = ""
            var search_type = 0
            if(this.search_input == "Start Time"){
                search_type = 1
                this.selectedEvent = { 
                    date: date_input_milliseconds,
                    // dateShow: this.displayDate(new Date()),
                    dateShow: this.formatDate(this.date_input),
                    // dateShow: "hey",
                    // dateShow: this.displayDate(new Date(date_input_milliseconds)),
                    start: (this.hhmmssTomilli(this.start_input)+date_input_milliseconds),
                    end: null,
                    duration: null,
                    category: null,
                    name: "Member Event",
                    method: "Members",
                    host_id: this.session_data.id,
                    participants: [],
                    num_of_guests: 0,
                    num_of_members: 1,
                    timed: true,
                    param: this.start_input,
                    paramShow: this.displayTime(this.start_input)
                }
                param=this.start_input
            } else {
                search_type = 0
                this.selectedEvent = { 
                    date: date_input_milliseconds,
                    dateShow: this.formatDate(this.date_input),
                    // dateShow: this.displayDate(new Date(date_input_milliseconds)),
                    start: null,
                    end: null,
                    duration: this.dur_type[this.dur_input-1].data,
                    category: null,
                    name: "Member Event",
                    method: "Member",
                    host_id: this.session_data.id,
                    participants: [],
                    num_of_guests: 0,
                    num_of_members: 1,
                    timed: true,
                    param: this.dur_input
                }
                param=this.dur_input
            }
            console.log(this.selectedEvent)
            axios.get('api/avail_reservations/'+search_type+'/'+date_input_milliseconds+'/'+param)
            .then(response => {
                console.log(response)
                if(this.selectedEvent.duration==null){
                    this.results="start_time"
                } else if(this.selectedEvent.start==null){
                    this.results="duration"
                }
                this.avail_slots=response.data
                this.loading=false
            })
            .catch(error => {
                console.log(error)
                this.loading=false
            })
        },
        hhmmssTomilli(hhmmssString){
            var hms = hhmmssString;   // your input string
            var a = hms.split(':'); // split it at the colons

            // minutes are worth 60 seconds. Hours are worth 60 minutes.
            var millisecs = ((+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]))*1000; 

            return millisecs
        },
        displayTime(time){
            console.log(time)
            var hour = time.substr(0,2)
            var min = time.substr(3,2)
            var amOrPm = "am"
            if(hour>=12){
                if(hour != 12){
                    hour = hour %12
                }
                amOrPm = "pm"
            } else {
                amOrPm = "am"
            }
            hour = parseInt(hour)
            return hour+ ":"+min+" "+amOrPm
        },
        bugFix(n){
            if(n==30){ return "30 mins" } 
            else if(n==60){ return "1 Hour"}
            else if(n==90){ return "1.5 Hours"}
            else if(n==120){ return "2 Hours"}
        },
        formatDate(date){
            return moment(date).format('dddd MMMM Do')
        },
        nth (d) {
            return d > 3 && d < 21
            ? 'th'
            : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
        },
        getTimes (datetime) {
            var hour = new Date(datetime).toString().substr(16,2)
            var min = new Date(datetime).toString().substr(19,2)
            var amOrPm = "am"
            if(hour>=12){
                if(hour != 12){
                    hour = hour %12
                }
                amOrPm = "pm"
            } else {
                amOrPm = "am"
            }
            hour = parseInt(hour)
            return hour+ ":"+min+" "+amOrPm
        },
        displayDate (calDate) {

            if(calDate==null){
                calDate = new Date()
            }
            var months, days
            if(this.$vuetify.breakpoint.name == 'xs' || this.$vuetify.breakpoint.name == 'sm') {
                months = ['Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec']
                days = ['Mon','Tue','Wed','Thur','Fri','Sat','Sun']
            } else {
                months = ['January','February','March','April','May','June','July','August','September','October','November','December']
                days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']
            }
            // const days = ['Mon','Tue','Wed','Thur','Fri','Sat','Sun']
            const monthTitle = months[calDate.getMonth()]
            const dayTitle = days[calDate.getDay()]
            const dateTitle = calDate.getUTCDate()
            const dateNthTitle = this.nth(calDate.getUTCDate())
            const yearTitle = calDate.getFullYear()
            return dayTitle+' '+monthTitle+' '+dateTitle+dateNthTitle+' '+yearTitle
            // return monthTitle+' '+dateTitle+dateNthTitle+' '+yearTitle
        },
    }

        
    }
</script>
