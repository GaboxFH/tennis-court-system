<template>
    <v-container fluid class="pa-0 ma-0">
    <v-row>
        <v-col>
            Testing
        </v-col>
    </v-row>
    <v-row class="fill-height pt-3">
        <v-col align="left">
            <v-btn
                outlined
                class="ml-4 mr-2"
                color="grey darken-2"
                @click="setToday"
            >
                Today
            </v-btn>
            <v-btn
                fab
                text
                small
                color="grey darken-2"
                @click="prev"
            >
                <v-icon small>
                mdi-chevron-left
                </v-icon>
            </v-btn>
            <v-btn
                fab
                text
                small
                color="grey darken-2"
                @click="next"
            >
                <v-icon small>
                mdi-chevron-right
                </v-icon>
            </v-btn>
        </v-col>
        <v-col align="center" style="position: relative; left: 24px;"> 
            <v-toolbar-title v-if="$refs.calendar">
                {{ computedDate }}
            </v-toolbar-title>
        </v-col>
        <v-col align="right">  
            

        </v-col>
    </v-row>
    <v-row>
        <v-col align="center">
            <div v-if="fullScreen==false">
            <v-btn-toggle
                style="position: relative; left: 24px;"
                class="mx-6"
                v-model="scheduleView"
                rounded
                dense
                mandatory
                btn-toggle-btn-height="170px"
            >
                <v-btn @click="getReservations">1-8</v-btn>
                <v-btn @click="getReservations">9-16</v-btn>
            </v-btn-toggle>
            </div>
        </v-col>
    </v-row>
    <v-row class="pl-15 pr-4 pt-2 pb-2">
        <v-col v-for="n in computedCategories" v-bind:key="n" align="center" class="pa-0 ma-0">
            <!-- <div v-if="n%2==0" style="background-color:tomato;">{{ n }}</div>
            <div v-else style="background-color:orange;">{{ n }}</div> -->
            <div>{{ n }}</div>
        </v-col>
    </v-row>
    <v-row>
        <v-col>
        <v-sheet>
            <v-calendar
            ref="calendar"
            v-model="focus"
            color="primary"
            type="category"
            category-show-all
            interval-minutes="30"
            first-interval="16"
            interval-height="30"
            interval-width="48"
            hide-header
            :interval-style="intervalStyle"
            :show-interval-label="showIntervalLabel"
            :categories="computedCategories"
            :events="computedEvents"
            :event-color="getEventColor"
            @mousedown:time-category="newReservation"
            @mousemove:time-category="mouseMove"
            @mouseup:event="endDrag"
            @click:event="showEvent"
            >
            </v-calendar>
            <v-menu
                v-model="selectedOpen"
                :close-on-content-click="false"
                :activator="selectedElement"
                offset-x
            >
                <v-card
                color="grey lighten-4"
                min-width="350px"
                flat
                >
                <v-toolbar
                    :color="selectedEvent.color"
                    dark
                >
                    <v-toolbar-title class="font-weight-medium">{{ selectedEvent.name }}</v-toolbar-title>
                    <v-spacer></v-spacer>

                    <div v-if="editEvent">
                    <v-btn @click="closeCard" icon>
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    </div>

                    <div v-else>
                    <v-btn @click="editEvent = true" icon>
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn @click="deleteEvent" icon>
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                    </div>
                </v-toolbar>
                <div class="ma-2">
                    
                    <v-text-field 
                    v-if="selectedEvent.method == 'USTA' || selectedEvent.method == 'Admin Event'"
                        v-model="selectedEvent.title"
                        :disabled="!editEvent"
                        label="Reservation Title">

                    </v-text-field>
                    
                    <v-select
                        v-model="selectedEvent.method"
                        :items="method_type"
                        :disabled="!editEvent"
                        label="Reservation Type"
                    ></v-select>
                    <v-autocomplete
                        v-model="selectedEvent.host"
                        :disabled="!editEvent"
                        :items="computedHost"
                        label="Host"
                    >
                        <template v-slot:selection="data">
                            <v-chip
                                :disabled="!editEvent"
                                v-bind="data.attrs"
                                :input-value="data.selected"
                                @click="data.select"
                            >
                                {{ data.item }}
                            </v-chip>
                        </template>
                    </v-autocomplete>
                    <v-autocomplete
                        v-model="selectedEvent.participants"
                        :disabled="!editEvent"
                        :items="computedMembers"
                        chips
                        label="Participants"
                        multiple
                    >
                    <template v-slot:selection="data">
                        <v-chip
                            v-bind="data.attrs"
                            :disabled="!editEvent"
                            :input-value="data.selected"
                            close
                            @click="data.select"
                            @click:close="remove(data.item)"
                        >
                            {{ data.item }}
                        </v-chip>
                    </template>
                    </v-autocomplete>
                </div>
                
                <v-card-actions>
                    <div v-if="editEvent">
                    <v-btn
                    text
                    color="secondary"
                    @click="editEvent = false"
                    >
                    Cancel
                    </v-btn>
                    <v-btn
                    text
                    color="secondary"
                    @click="selectedOpen = false"
                    >
                    Save
                    </v-btn>
                    </div>
                </v-card-actions>
                </v-card>
            </v-menu>
        </v-sheet>
        </v-col>
    </v-row>
    <v-row>
        <v-dialog
            v-model="areyousure"
            persistent
            max-width="290"
        >
            <v-card>
                <v-card-title>
                    Are you sure you want to delete this reseravtion?
                </v-card-title>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                color="green darken-1"
                text
                @click="areyousure = false"
                >
                Yes
                </v-btn>
                <v-btn
                color="green darken-1"
                text
                @click="areyousure = false"
                >
                No
                </v-btn>
            </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
    </v-container>
</template>


<script>
export default {
    props: ['reservations','users'],

    data: () => ({
        focus: '',
        appointments: [],
        members: [],
        cal_events: [],
        styleInterval: 'workday',
        scheduleView: 0,
        fullScreen: false,
        // dragEvent: null,
        createEvent: null,
        clickStart: false,

        createStart: null,
        extendOriginal: null,

        startClick: undefined,
        endClick: false,
        currentDate: [],

        selectedEvent: {},
        selectedElement: null,
        selectedOpen: false,
        editEvent: false,
        new_endtime: '',

        areyousure: false,

        method_type: ['Admin Event', 'Call', 'Walk-In', 'Tennis Pro', 'USTA'],
        colors: ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1'],
        // names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
        categories: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']
    }),
    computed: {
        computedDate () {
            console.log(new Date())
            if(this.$refs.calendar.value != ''){
                return this.$refs.calendar.value
            } else {
                return "Today"
            }
        },
        computedHost () {
            return this.members.map(({ name }) => name)
        },
        computedMembers () {
            var members_no_host = this.members.map(({ name }) => name)
            const index = members_no_host.indexOf(this.selectedEvent.host)
            if (index >= 0) members_no_host.splice(index, 1)
            return members_no_host
        },
        computedCategories () {
            var categories = []
            if(this.$vuetify.breakpoint.name == 'xs' || this.$vuetify.breakpoint.name == 'sm' || this.$vuetify.breakpoint.name == 'md') {
                this.fullScreen = false
                if(this.scheduleView == 0){
                    categories = ['1', '2', '3','4', '5', '6', '7', '8']
                } else {
                    categories = ['9', '10', '11', '12', '13', '14', '15', '16']
                }
            } else {
                this.fullScreen = true
                categories = ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']
            }
            // console.log(categories)
            return categories
        },
        computedEvents () {
            var color = "blue"

            if(!this.clickStart && this.appointments.length != 0){
                this.cal_events = []
                for(var i=0; i<this.appointments.length; i++){
                    console.log(this.appointments[i])
                    if(this.appointments[i].method == "Admin Event"){ color = "cyan" }
                    else if(this.appointments[i].method == "Call" || this.appointments[i].method == "Walk-In"){ color = "blue" }
                    else if(this.appointments[i].method == "Tennis Pro"){ color = "green" }
                    else if(this.appointments[i].method == "USTA"){ color = "lime" }
                    else{ color = "red" }
                    this.cal_events.push({
                        id: this.appointments[i].id,
                        title: this.appointments[i].title,
                        method: this.appointments[i].method,
                        host: "Noah Smith", //this.appointments[i].user_id, should get hosts name
                        participants: this.appointments[i].participants,
                        num_of_members: this.appointments[i].num_of_members,
                        num_of_guests: this.appointments[i].num_of_guests,
                        user_id: this.appointments[i].user_id,
                        name: this.appointments[i].title,
                        start: this.appointments[i].start_datetime,
                        end: this.appointments[i].end_datetime,
                        // color: this.colors[this.rnd(0, this.colors.length - 1)],
                        color: color,
                        timed: true,
                        category: this.categories[this.appointments[i].court-1],
                    })
                }
                // this.refresh = false
            }
            return this.cal_events
        },
        intervalStyle () {
            const stylings = {
                default (interval) {
                    return undefined
                },
                workday (interval) {
                    const inactive = 
                    (interval.hour < 8 ||
                    interval.hour >= 20) //&& interval.category.categoryName == 3 
                    const startOfHour = interval.minute === 0
                    const dark = this.dark
                    const mid = dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'

                    return {
                    backgroundColor: inactive ? (dark ? 'rgba(0,0,0,0.4)' : 'rgba(0,0,0,0.05)') : undefined,
                    borderTop: startOfHour ? undefined : '1px dashed ' + mid
                    }
                },
                past (interval) {
                    return {
                    backgroundColor: interval.past ? (this.dark ? 'rgba(0,0,0,0.4)' : 'rgba(0,0,0,0.05)') : undefined
                    }
                }
            }
            return stylings['workday'].bind(this)
            
            // return stylings[ this.styleInterval ].bind(this)
            // return stylings['workday'].bind(this)
        },
    },
    watch: {
        selectedOpen (val) {
            val || (this.editEvent = false)
        },
        reservations (val) {
            this.appointments=val
            console.log(val)
        },
        users (val) {
            this.members=val
            console.log(val)
        },
    },
    created () {
        // this.getReservations()
        // this.getUsers();
        this.$emit('refresh-schedule')
        this.$emit('refresh-users')
        // .then(response => {
        // this.appointments = this.reservations
                // })
        // .then(
        //     if(this.appointments.length == 0){
        //         this.appointments = this.reservations
        //     }       
        // ) 
        
        // this.reloadEvents()
    },
    mounted () {
        this.$refs.calendar.checkChange()
    },
    methods: {
        newReservation (tms) {
            if (!this.selectedOpen) {
                console.log(tms)
                const time_clicked = this.roundTime(this.toTime(tms))
                var timeConflict = false
                // console.log(time_clicked)
                // console.log(new Date(this.cal_events[1].start).getTime())
                // console.log(this.cal_events)
                for(var i=0; i<this.cal_events.length; i++) {
                    if(time_clicked >= new Date(this.cal_events[i].start).getTime() && time_clicked < new Date(this.cal_events[i].end).getTime() && tms.category.categoryName == this.cal_events[i].category){
                        timeConflict = true
                    }
                }
                if(!timeConflict){
                    this.clickStart = true

                    this.createEvent = {
                        title: "Noah Smith", //should be admin name
                        method: "Call",
                        host: "Noah Smith", //should be admin name
                        participants: ["Kenia Rangel"],
                        num_of_members: 1,
                        num_of_guests: 0,
                        user_id: 1, //should be admin id
                        name: "Noah Smith", //should be admin name
                        start: time_clicked,
                        end: time_clicked+30*1000*60,
                        color: "blue",
                        timed: true,
                        category: tms.category.categoryName
                    }
                    this.cal_events.push(this.createEvent)

                    let item = JSON.parse(JSON.stringify(this.createEvent))
                    item.start = this.convertDate(new Date(item.start))
                    item.end = this.convertDate(new Date(item.end))

                    let newCompTimePayload = {
                        item
                    }
                    // console.log(newCompTimePayload)
                    this.editEvent = true
                    axios.post('api/reservation/store', newCompTimePayload)
                }
            }
        },
        mouseMove (tms){
            if(this.clickStart){
                console.log(this.cal_events[this.cal_events.length-1])
                this.new_endtime = this.roundTime(this.toTime(tms))+30*1000*60
                var timeConflict = false
                // if(tms.category.categoryName == this.cal_events[this.cal_events.length-1].category){
                    if(this.cal_events[this.cal_events.length-1].end != this.new_endtime && this.new_endtime >= (this.cal_events[this.cal_events.length-1].start+30*1000*60)){
                        
                        for(var i=0; i<this.cal_events.length-1; i++) {
                            if(this.cal_events[this.cal_events.length-1].category == this.cal_events[i].category
                            && (new Date(this.cal_events[i].start).getTime() < this.new_endtime
                            && new Date(this.cal_events[i].end).getTime() >  this.cal_events[this.cal_events.length-1].start)
                            ){
                                timeConflict = true
                            }
                        }
                        if(!timeConflict){
                            this.cal_events[this.cal_events.length-1].end = this.new_endtime
                            
                            let item = JSON.parse(JSON.stringify(this.cal_events[this.cal_events.length-1]))
                            item.start = this.convertDate(new Date(item.start))
                            item.end = this.convertDate(new Date(item.end))
                            let newCompTimePayload = {
                                item
                            }
                            console.log(newCompTimePayload)

                            axios.put('api/reservation/adminupdate', newCompTimePayload)
                        }
                        // this.cal_events[this.cal_events.length-1].end = new_endtime
                    }
                    // console.log(tms.time)
                // }
                // console.log(tms.category.categoryName)
            }
        },
        endDrag ({ nativeEvent, event }){
            this.getReservations()
            this.clickStart = false
            // this.cal_events[this.cal_events.length-1].end = this.new_endtime
            this.showEvent({ nativeEvent, event })
        },
        deleteEvent (){
            this.areyousure = true
            console.log(this.selectedEvent)
        },

        showEvent ({ nativeEvent, event }) {
            const open = () => {
                this.selectedEvent = event
                this.selectedEvent.participants = 
                // this.selectedEvent.start_time = this.selectedEvent.start.slice(11, 16)
                // this.selectedEvent.end_time = this.selectedEvent.end.slice(11, 16)
                this.selectedElement = nativeEvent.target
                
                console.log("event")
                console.log(this.selectedEvent)
                console.log("element")
                console.log(this.selectedElement)
                setTimeout(() => {
                    this.selectedOpen = true
                }, 10)
            }
            if (this.selectedOpen) {
                this.selectedOpen = false
                setTimeout(open, 10)
            } else {
                open()
            }
            nativeEvent.stopPropagation()
        },
        editEventFields (){
            this.editEvent = true
        },
        closeCard () {
            this.editEvent = false
            this.selectedOpen = false
        },
        remove (item) {
            // console.log(item)
            const index = this.selectedEvent.participants.indexOf(item)
            if (index >= 0) this.selectedEvent.participants.splice(index, 1)
        },
        toTime (tms) {
            return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
        },
        roundTime (time, down = true) {
            const roundTo = 30 // minutes
            const roundDownTime = roundTo * 60 * 1000

            return down
            ? time - time % roundDownTime
            : time + (roundDownTime - (time % roundDownTime))
        },
        showIntervalLabel (interval) {
            return (interval.minute === 0 && interval.hour != 8)
        },
        getEventColor (event) {
            return event.color
        },
        setToday () {
            this.focus = ''
        },
        prev () {
            this.$refs.calendar.prev()
        },
        next () {
            this.$refs.calendar.next()
        },
        reloadEvents (){
            this.$emit('refresh-schedule')
        },
        getReservations() {
            this.appointments = []
            axios.get('api/reservations')
                .then(response => {
                    this.appointments = response.data
                })
                .catch(error => {
                    console.log(error)
                })
        },
        rnd (a, b) {
            return Math.floor((b - a + 1) * Math.random()) + a
        },
        convertDate(dt){
            return `${
                dt.getFullYear().toString().padStart(4, '0')}/${
                (dt.getMonth()+1).toString().padStart(2, '0')}/${
                dt.getDate().toString().padStart(2, '0')} ${
                dt.getHours().toString().padStart(2, '0')}:${
                dt.getMinutes().toString().padStart(2, '0')}:${
                dt.getSeconds().toString().padStart(2, '0')}`
        }
    },
  }

</script>