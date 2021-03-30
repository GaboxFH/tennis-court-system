<template>
    <v-container fluid class="pa-0 ma-0">
    <v-row>
        <v-col>
            Welcome {{ this.session_data }}
        </v-col>
    </v-row>
    <v-row>
        <v-col>
            Testing {{ this.selectedEvent }}
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
            @mousemove:event="startDrag"
            @mousedown:time-category="startTime"
            @mousemove:time-category="mouseMove"
            @mouseup:event="endDrag"
            >
            </v-calendar>
            <v-menu
                v-model="selectedOpen"
                :close-on-content-click="false"
                :close-on-click="!editEvent"
                :activator="selectedElement"
                offset-x
            >
                <v-card
                color="grey lighten-4"
                min-width="250px"
                max-width="350px"
                flat
                >
                <v-toolbar
                    :color="selectedEvent.color"
                    dark
                >
                    <v-toolbar-title class="font-weight-medium">{{ selectedEvent.name }}</v-toolbar-title>
                    <v-spacer></v-spacer>

                    <div v-if="editEvent">
                    <v-btn @click="closeCard" icon small>
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    </div>

                    <div v-else>
                    <v-btn @click="editEvent = true" icon small>
                        <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn @click="deleteEvent" icon small>
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                    <v-btn @click="closeCard" icon small>
                        <v-icon>mdi-close</v-icon>
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
                        :items="members"
                        :search-input.sync="hostInput"
                        @change="hostInput=''"
                        label="Host"
                        chips
                        hide-selected

                        item-text="name"
                        item-value="id"
                    >                    
                    <template v-slot:selection="data">
                    <v-chip
                        v-bind="data.attrs"
                        :input-value="data.selected"
                        :disabled="!editEvent"
                        @click="data.select"
                    >
                        {{ data.item.name }}
                    </v-chip>
                    </template>
                    <template v-slot:item="data">
                        <v-list-item-content v-text="data.item.name"></v-list-item-content>
                    </template>
                    </v-autocomplete>
                    <v-autocomplete
                        v-model="selectedEvent.participants"
                        :disabled="!editEvent"
                        :items="computedMembers"
                        :search-input.sync="participantInput"
                        @change="participantInput=''"
                        chips
                        deletable-chips
                        label="Participants"
                        hide-selected   
                        multiple
                        item-text="name"
                        item-value="id"
                    >
                    <template v-slot:selection="data">
                    <v-chip
                        v-bind="data.attrs"
                        :input-value="data.selected"
                        :disabled="!editEvent"
                        close
                        @click="data.select"
                        @click:close="remove(data.item)"
                    >
                        {{ data.item.name }}
                    </v-chip>
                    </template>
                    <template v-slot:item="data">
                        <v-list-item-content v-text="data.item.name"></v-list-item-content>
                    </template>
                    </v-autocomplete>
                </div>
                
                <v-card-actions>
                    <div v-if="editEvent">
                    <v-btn
                    text
                    color="secondary"
                    @click="cancelEvent"
                    >
                    Cancel
                    </v-btn>
                    <v-btn
                    text
                    color="secondary"
                    @click="saveEvent"
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
                @click="deleteFinal"
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
    props: ['reservations','users','session_data'],

    data: () => ({
        focus: '',
        hostInput: '',
        participantInput: '',
        appointments: [],
        members: [],
        cal_events: [],
        styleInterval: 'workday',
        scheduleView: 0,
        fullScreen: false,
        // dragEvent: null,
        createEvent: null,
        getEvent: false,
        clickStart: false,
        dragStart: false,
        dragEvent: null,
        new_endtime: '',
        first_edit: false,
        
        selectedEvent: {},
        selectedElement: null,
        selectedOpen: false,
        editEvent: false,
        

        areyousure: false,

        method_type: ['Admin Event', 'Call', 'Walk-In', 'Tennis Pro', 'USTA'],
        colors: ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1'],
        // names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
        categories: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']
    }),
    computed: {
        computedDate () {
            // console.log(new Date())
            if(this.$refs.calendar.value != ''){
                return this.$refs.calendar.value
            } else {
                return "Today"
            }
        },
        computedMembers () {
            if(this.selectedEvent.host == null){
                return this.members
            } else {
                // dont show host on option list
                var members_no_host = JSON.parse(JSON.stringify(this.members))
                var host_index = members_no_host.map(function(item) { return item.id; }).indexOf(this.selectedEvent.host)
                members_no_host.splice(host_index, 1)
            
                // remove host from list
                if(Object.entries(this.selectedEvent).length != 0){
                    var index = this.selectedEvent.participants.indexOf(this.selectedEvent.host)
                    if (index >= 0) this.selectedEvent.participants.splice(index, 1)
                }
                // console.log("members_no_host")
                // console.log(members_no_host)
                return members_no_host
            }
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
            // var color = "blue"

            // if(!this.clickStart && this.appointments.length != 0){
            //     this.cal_events = []
                // for(var i=0; i<this.appointments.length; i++){
                //     // console.log(this.appointments[i])
                //     if(this.appointments[i].method == "Admin Event"){ color = "cyan" }
                //     else if(this.appointments[i].method == "Call" || this.appointments[i].method == "Walk-In"){ color = "blue" }
                //     else if(this.appointments[i].method == "Tennis Pro"){ color = "green" }
                //     else if(this.appointments[i].method == "USTA"){ color = "lime" }
                //     else{ color = "red" }
                //     this.cal_events.push({
                //         id: this.appointments[i].id,
                //         title: this.appointments[i].title,
                //         method: this.appointments[i].method,
                //         host: this.appointments[i].user_id, 
                //         participants: this.appointments[i].participants,
                //         num_of_members: this.appointments[i].num_of_members,
                //         num_of_guests: this.appointments[i].num_of_guests,
                //         user_id: this.appointments[i].user_id,
                //         name: this.appointments[i].title,
                //         start: this.appointments[i].start_datetime,
                //         end: this.appointments[i].end_datetime,
                //         // color: this.colors[this.rnd(0, this.colors.length - 1)],
                //         color: color,
                //         timed: true,
                //         category: this.categories[this.appointments[i].court-1],
                //     })
                // }
                // this.refresh = false
            // }
            // return this.cal_events


            var color = "blue"

            if(!this.clickStart && this.appointments.length != 0){
                this.cal_events = []
                for(var i=0; i<this.appointments.length; i++){
                    // console.log(this.appointments[i])
                    if(this.appointments[i].method == "Admin Event"){ color = "cyan" }
                    else if(this.appointments[i].method == "Call" || this.appointments[i].method == "Walk-In"){ color = "blue" }
                    else if(this.appointments[i].method == "Tennis Pro"){ color = "green" }
                    else if(this.appointments[i].method == "USTA"){ color = "lime" }
                    else{ color = "red" }
                    this.cal_events.push({
                        id: this.appointments[i].id,
                        title: this.appointments[i].title,
                        method: this.appointments[i].method,
                        host: this.appointments[i].user_id, 
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
            // console.log(val)
        },
        users (val) {
            this.members=val
            // console.log(val)
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
        startTime (tms) {
            // console.log(tms)
            if (!this.selectedOpen) {
                const time_clicked = this.roundTime(this.toTime(tms))
                var timeConflict = false
                for(var i=0; i<this.cal_events.length; i++) {
                    if(time_clicked >= new Date(this.cal_events[i].start).getTime() && time_clicked < new Date(this.cal_events[i].end).getTime() && tms.category.categoryName == this.cal_events[i].category){
                        timeConflict = true
                    }
                }
                if(!timeConflict){
                    this.clickStart = true

                    this.createEvent = {
                        title: "New Event", //should be admin name this.session_data.name
                        method: "Call",
                        host: null, //should be admin id this.session_data.id
                        participants: [],
                        num_of_members: 1,
                        num_of_guests: 0,
                        user_id: null, //should be admin id this.session_data.id
                        name: "New Event", //should be admin name this.session_data.name
                        start: time_clicked,
                        end: time_clicked+30*1000*60,
                        duration: (end-start),
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

                    this.editEvent = true
                    axios.post('api/reservation/store', newCompTimePayload)
                }
            }
        },
        startDrag ({ event }) {
            if(this.clickStart && !this.getEvent){
                console.log(event)
                this.getEvent = true
                this.dragStart = true
                this.dragEvent = event
            }
        },
        mouseMove (tms) {
            if(this.dragStart){
                console.log(this.dragEvent)
                this.new_endtime = this.roundTime(this.toTime(tms))+30*1000*60
                var timeConflict = false
                if(this.dragEvent.end != this.new_endtime && this.new_endtime >= (this.dragEvent.start+30*1000*60)){
                    
                    for(var i=0; i<this.cal_events.length-1; i++) {
                        if(this.dragEvent.category == this.cal_events[i].category
                        && (new Date(this.cal_events[i].start).getTime() < this.new_endtime
                        && new Date(this.cal_events[i].end).getTime() >  this.dragEvent.start)
                        ){
                            timeConflict = true
                        }
                    }
                    if(!timeConflict){
                        this.dragEvent.end = this.new_endtime
                        
                        let item = JSON.parse(JSON.stringify(this.dragEvent))
                        item.start = this.convertDate(new Date(item.start))
                        item.end = this.convertDate(new Date(item.end))
                        let newCompTimePayload = {
                            item
                        }
                        console.log(newCompTimePayload)

                        axios.put('api/reservation/adminupdate', newCompTimePayload)
                    }
                }
            }
        },
        endDrag ({ nativeEvent, event }){
            this.getReservations()
            this.clickStart = false
            this.first_edit = true
            this.getEvent = false
            this.dragStart = false
            this.showEvent({ nativeEvent, event })
        },
        deleteEvent (){
            this.areyousure = true
            console.log(this.selectedEvent)
        },
        deleteFinal (){
            // let item = JSON.parse(JSON.stringify(this.selectedEvent))
            // let newCompTimePayload = {
            //     item
            // }
            // console.log(newCompTimePayload)
            // this.editEvent = true
            axios.delete('api/reservation/'+this.selectedEvent.id)
            this.areyousure = false
            this.getReservations()
        },
        showEvent ({ nativeEvent, event }) {
            const open = () => {
                this.selectedEvent = event
                this.getParticipants()
                this.selectedElement = nativeEvent.target
                
                // console.log("event")
                // console.log(this.selectedEvent)
                // console.log("element")
                // console.log(this.selectedElement)
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
        saveEvent () {
            console.log("saveEvent")
            // var ordered_participants_ids = []
            console.log(this.selectedEvent.host)
            console.log(this.selectedEvent.participants)
            // var index = this.selectedEvent.participants.indexOf(this.selectedEvent.host)
            // if (index >= 0){
            //     ordered_participants_ids = this.selectedEvent.participants
            // } else {
            //     ordered_participants_ids = this.selectedEvent.participants
            //     ordered_participants_ids.push(this.selectedEvent.host)
            // }
            var ordered_participants_ids = JSON.parse(JSON.stringify(this.selectedEvent.participants))
            if(this.selectedEvent.host==''){ 
                this.selectedEvent.host = null
            }
            if(this.selectedEvent.host!=null){
                ordered_participants_ids.push(this.selectedEvent.host)
            }
            ordered_participants_ids.sort(function(a, b){return a-b})
            // console.log(ordered_participants_ids)
            
            let item = JSON.parse(JSON.stringify(this.selectedEvent))
            item.start = this.convertDate(new Date(item.start))
            item.end = this.convertDate(new Date(item.end))
            item.ordered_participants_ids = ordered_participants_ids
            let newCompTimePayload = {
                item
            }
            console.log(newCompTimePayload)
            // this.editEvent = true
            axios.put('api/reservation/update', newCompTimePayload)
            this.getReservations()
            this.closeCard()

        },
        cancelEvent () {
            this.closeCard ()
        },
        editEventFields (){
            this.editEvent = true
        },
        closeCard () {
            this.editEvent = false
            this.selectedOpen = false
        },
        remove (item) {
            var index = this.selectedEvent.participants.indexOf(item.id)
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
        getParticipants() {
            this.selectedEvent.host = ''
            this.selectedEvent.participants = []
            axios.get('api/reservation_users/'+this.selectedEvent.id+'/'+this.selectedEvent.user_id)
            .then(response => {
                this.selectedEvent.host = response.data.res_host
                this.selectedEvent.participants = response.data.res_participants
            })
            .catch(error => {
                console.log(error)
            })

        },
        getReservations() {
            this.appointments = []
            axios.get('api/reservations')
                .then(response => {
                    this.appointments = response.data
                    // this.fillCal()
                })
                .catch(error => {
                    console.log(error)
                })
        },
        fillCal() {
            this.cal_events = []
            for(var i=0; i<this.appointments.length; i++){
                // console.log(this.appointments[i])
                if(this.appointments[i].method == "Admin Event"){ color = "cyan" }
                else if(this.appointments[i].method == "Call" || this.appointments[i].method == "Walk-In"){ color = "blue" }
                else if(this.appointments[i].method == "Tennis Pro"){ color = "green" }
                else if(this.appointments[i].method == "USTA"){ color = "lime" }
                else{ color = "red" }
                this.cal_events.push({
                    id: this.appointments[i].id,
                    title: this.appointments[i].title,
                    method: this.appointments[i].method,
                    host: this.appointments[i].user_id, 
                    participants: this.appointments[i].participants,
                    num_of_members: this.appointments[i].num_of_members,
                    num_of_guests: this.appointments[i].num_of_guests,
                    user_id: this.appointments[i].user_id,
                    name: this.appointments[i].title,
                    start: this.appointments[i].start_datetime,
                    end: this.appointments[i].end_datetime,
                    duration: (end-start),
                    // color: this.colors[this.rnd(0, this.colors.length - 1)],
                    color: color,
                    timed: true,
                    category: this.categories[this.appointments[i].court-1],
                })
            }
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