<template>
<v-container fluid class="pa-0 ma-0">
    <v-row class="fill-height pt-3">
        <v-col>
            <v-app-bar flat dense color="white">
                <v-btn v-if="$vuetify.breakpoint.name != 'xs'" outlined class="ml-4 mr-2" color="grey darken-2" @click="setToday">Today</v-btn>
                <div class="flex text-center" 
                v-bind:style="[$vuetify.breakpoint.name != 'xs' ? { 
                    position: 'relative', right: 32 + 'px'
                } : { position: 'relative', left: 24 + 'px'
                }]">
                <v-btn icon text medium color="grey darken-2" @click="prev">
                    <v-icon>mdi-chevron-left</v-icon>
                </v-btn>

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
                    <v-btn
                        v-bind="attrs"
                        v-on="on"
                        min-width="200px"
                        outlined
                    > {{ displayDate(new Date(curr_date+'T00:00')) }} </v-btn>
                    </template>
                    <v-date-picker
                    v-model="curr_date"
                    no-title
                    @input="dropdown_cal = false"
                    ></v-date-picker>
                </v-menu>

                <v-btn icon text medium color="grey darken-2" @click="next">
                    <v-icon>mdi-chevron-right</v-icon>
                </v-btn>
                </div>
            </v-app-bar>
        </v-col>
    </v-row>
    <v-row>
        <v-col align="center">
            <div v-if="fullScreen==false">
            <v-btn-toggle style="position: relative; left: 24px;" class="mx-6" v-model="scheduleView" rounded dense mandatory btn-toggle-btn-height="170px">
                <v-btn>1-8</v-btn>
                <v-btn>9-17</v-btn>
            </v-btn-toggle>
            </div>
        </v-col>
    </v-row>
    <v-row class="pl-15 pr-4 pt-2 pb-2">
        <v-col v-for="n in computedCategories" v-bind:key="n" align="center" class="pa-0 ma-0">
            <!-- <div v-if="n%2==0" style="background-color:tomato;">{{ n }}</div>
            <div v-else style="background-color:orange;">{{ n }}</div> -->
            <div v-if="$vuetify.breakpoint.name == 'xs'">{{ n }}</div>
            <div v-else>Court {{ n }}</div>
        </v-col>
    </v-row>
    <v-row class="fill-height">
    <v-col>
        <v-sheet>
        <v-calendar
            ref="calendar"
            v-model="curr_date"
            color="primary"
            type="category"
            category-show-all
            interval-minutes="30"
            first-interval="16"
            interval-height="30"
            interval-width="48"
            hide-header
            :show-interval-label="showIntervalLabel"
            :categories="computedCategories"
            :events="events"
            :event-color="getEventColor"
            :event-ripple="false"
            :interval-style="intervalStyle"
            @mousedown:event="startDrag"
            @mousedown:time-category="startTime"
            @mousemove:time-category="mouseMove"
            @mouseup:time="endDrag"
            @mouseleave.native="cancelDrag"
        >
            <template v-slot:event="{ event, timed, eventSummary }">
            <div
                class="v-event-draggable"
                v-html="eventSummary()"
            ></div>
            <div
                v-if="timed"
                class="v-event-drag-bottom"
                @mousedown.stop="extendBottom(event)"
            ></div>
            </template>
        </v-calendar>

        <v-dialog v-model="dialog_edit" persistent max-width="600px"
        >
        <v-card>
           
            <v-toolbar v-if="selectedEvent" class="mb-3" :color="getEventColor(selectedEvent)" dark >
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
              v-model="curr_date"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            <v-col>
            <v-text-field
              label="Start Time"
              v-model="computedTimes"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            <v-col>
            <v-text-field
              label="End Time"
              v-model="computedTimes2"
              outlined
              readonly
            >yoo</v-text-field>
            </v-col>
            </v-row>
            <v-text-field v-model="selectedEvent.name" label="Event Title"></v-text-field>
            <v-select
                v-model="selectedEvent.method"
                :items="method_type"
                label="Reservation Type"
            ></v-select>
            <v-row>
            <v-col cols="7">
            <v-autocomplete
                v-model="selectedEvent.host_id"
                :items="members"
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
                @click="data.select"
            >
                {{ data.item.name }}
            </v-chip>
            </template>
            <template v-slot:item="data">
                <v-list-item-content v-text="data.item.name"></v-list-item-content>
            </template>
            </v-autocomplete>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="4">
                <v-text-field height=42 readonly v-model="selectedEvent.num_of_guests" type="number" label="Number of Guests" append-outer-icon="mdi-plus" @click:append-outer="selectedEvent.num_of_guests = parseInt(selectedEvent.num_of_guests,10) + 1" prepend-icon="mdi-minus" @click:prepend="selectedEvent.num_of_guests = parseInt(selectedEvent.num_of_guests,10) - 1"></v-text-field>
                
            </v-col>
            </v-row>

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
            >
            </v-autocomplete>

            </div>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="saveEvent(true)">Save</v-btn>
                <v-btn color="blue darken-1" text @click="saveEvent(false)">Close</v-btn>
            </v-card-actions>        
        </v-card>
        </v-dialog>


        <v-dialog
            v-model="dialog_verify_update"
            persistent
            max-width="450"
        >
            <v-card>
                <v-card-title class="headline grey lighten-2">
                    Update Reservation?
                </v-card-title>
            <div v-if="selectedEvent">
                
                <v-card-subtitle style="font-size: 1.4em" >Updated Reservation Details:</v-card-subtitle>
                <v-card-text style="font-size: 1.2em" >
                Event: {{ selectedEvent.name }}<br>
                Court: {{ selectedEvent.category }}<br>
                {{ displayDate(new Date(selectedEvent.start)) }}<br>
                {{ getTimes(selectedEvent.start) }} - {{ getTimes(selectedEvent.end) }}<br>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-subtitle style="font-size: 1.4em" >Current Reservation Details:</v-card-subtitle>
                <v-card-text style="font-size: 1.2em" >
                Event: {{ selectedEvent.name }}<br>
                Court: {{ selectedEvent.orig_category }}<br>
                {{ displayDate(new Date(selectedEvent.orig_start)) }}<br>
                {{ getTimes(selectedEvent.orig_start) }} - {{ getTimes(selectedEvent.orig_end) }}<br>
                </v-card-text>
            </div>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="updateEvent(true)">Yes</v-btn>
                <v-btn color="blue darken-1" text @click="updateEvent(false)">No</v-btn>
            </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
            v-model="areyousure"
            persistent
            max-width="450"
        >
            <v-card>
                <v-card-title>
                    Delete Reservation
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete this reservation?
                </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="deleteFinal(true)">Yes</v-btn>
                <v-btn color="blue darken-1" text @click="deleteFinal(false)">No</v-btn>
            </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
            v-model="repeat_dialog"
            persistent
            max-width="450"
        >
            <v-card>
                <div class="mx-5">
                <v-card-title>
                    Repeating Event
                </v-card-title>
                <v-select
                    
                    v-model="repeat_select"
                    :items="repeat_type"
                    label="Repeat"
                ></v-select>
                </div>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="repeat_event">Save</v-btn>
                <v-btn color="blue darken-1" text @click="repeat_dialog=false">Cancel</v-btn>
            </v-card-actions>
            </v-card>
        </v-dialog>
        

        
        </v-sheet>
    </v-col>
    </v-row>

    <v-snackbar v-model="message" :timeout="timeout" top>
            {{ message_text }}
        <template v-slot:action="{ attrs }">
            <v-btn color="blue" text v-bind="attrs" @click="message = false"> Close</v-btn>
        </template>
    </v-snackbar>

    <v-row class="pa-0 ma-0 fill-height" v-for="n in events" v-bind:key="n.id">
    <v-col>
        <!-- <div v-if="n%2==0" style="background-color:tomato;">{{ n }}</div>
        <div v-else style="background-color:orange;">{{ n }}</div> -->
        <div>{{ n }}</div>
    </v-col>
    </v-row>

    <v-row class="fill-height">
    <v-col>
        <p>Events:</p>
        <div v-if="events[0]">
            {{ new Date(events[0].start) }}
        </div>
    </v-col>
    </v-row>

    
</v-container>
</template>

<script>
export default {
    props: ['reservations','users','session_data'],

    data: () => ({
        curr_date: new Date().toISOString().substr(0, 10),
        dropdown_cal: false,
        events: [],
        method_type: ['Admin Event', 'Call', 'Walk-In', 'Tennis Pro', 'USTA'],
        repeat_type: ['Repeat Event Daily', 'Repeat Event Weekly', 'Repeat Event Monthly'],
        repeat_select: 'Repeat Event Weekly',
        repeat_dialog: false,
        colors: ['#0196F3', '#3F51B5', '#00BCD4', '#4CAF50', '#FF9800', '#757575'],        // names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
        scheduleView: 0,
        fullScreen: false,

        dragEvent: null,
        dragStart: null,
        createEvent: null,
        createStart: null,
        extendOriginal: null,
        dragOriginalStart: null,
        dragOriginalEnd: null,
        dragOriginalCategory: null,
        dialog_edit: false,
        dialog_verify_update: false,
        selectedEvent: null,
        areyousure: false,

        message: false,
        message_text: '',
        timeout: 3000,

        newCompTimePayload: [],
        // test: [ 
        //     { 
        //     id: 1, 
        //     name: "Test Row", 
        //     method: "Call", 
        //     start: "2021-04-04 09:00:00", 
        //     end: "2021-04-04 11:00:00", 
        //     duration: "00:00:02", 
        //     category: "6", 
        //     num_of_members: 0, 
        //     num_of_guests: 0, 
        //     host_id: null, 
        //     created_at: null, 
        //     updated_at: null, 
        //     color: "red" 
        //     }, 
        // ]
        // categories: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']
        // categories: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']

    }),
    created () {
        this.$emit('refresh-schedule')
        this.$emit('refresh-users')
    },
    watch: {
        reservations (val) {
            this.events=val
        },
        users (val) {
            this.members=val
        },
    },
    computed: {
        computedTimes() {
            return this.getTimes(this.selectedEvent.start)
        },
        computedTimes2(){
            return this.getTimes(this.selectedEvent.end)
        },
        computedMembers() {
            if(this.selectedEvent.host_id == null || this.selectedEvent.participants == null){
                return this.members
            } else {
                // dont show host on option list
                var members_no_host = JSON.parse(JSON.stringify(this.members))
                var host_index = members_no_host.map(function(item) { return item.id; }).indexOf(this.selectedEvent.host_id)
                members_no_host.splice(host_index, 1)
            
                // remove host from list
                if(Object.entries(this.selectedEvent.participants).length != 0){
                    var index = this.selectedEvent.participants.indexOf(this.selectedEvent.host_id)
                    if (index >= 0) this.selectedEvent.participants.splice(index, 1)
                }
                // console.log("members_no_host")
                // console.log(members_no_host)
                return members_no_host
            }
        },
        computedCategories(){
            var categories = []
            if(this.$vuetify.breakpoint.name == 'xs' || this.$vuetify.breakpoint.name == 'sm' || this.$vuetify.breakpoint.name == 'md' || this.$vuetify.breakpoint.name == 'lg') {
                this.fullScreen = false
                if(this.scheduleView == 0){
                    categories = ['1', '2', '3','4', '5', '6', '7', '8']
                } else {
                    categories = ['9', '10', '11', '12', '13', '14', '15', '16', '17']
                }
            } else {
                this.fullScreen = true
                categories = ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17']
            }
            return categories
        },
        intervalStyle () {
            const stylings = {
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
            }
            return stylings['workday'].bind(this)
        },
    },
    methods: {
        repeat_event() {
            console.log(this.selectedEvent)
            console.log(this.repeat_select)
        },
        saveEvent(save) {
            if(save){
                var ordered_participants_ids = JSON.parse(JSON.stringify(this.selectedEvent.participants))
                if(this.selectedEvent.host_id==''){ 
                    this.selectedEvent.host_id = null
                }
<<<<<<< HEAD
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
                        ///duration: (end-start),
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
||||||| 5840f3e
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
=======
                if(this.selectedEvent.host_id!=null){
                    ordered_participants_ids.push(this.selectedEvent.host_id)
                }
                ordered_participants_ids.sort(function(a, b){return a-b})
>>>>>>> development

                let item = JSON.parse(JSON.stringify(this.selectedEvent))
                item.ordered_participants_ids = ordered_participants_ids
                this.newCompTimePayload = {
                    item
                }
                // console.log("this.newCompTimePayload")
                // console.log(this.selectedEvent.host_id)
                // console.log(this.newCompTimePayload.item.host_id)
                this.updateEvent(true)
            } else {
                this.$emit('refresh-schedule')
            }
            this.dialog_edit = false
        },
        deleteFinal(del_event){
            if(del_event){
                axios.delete('api/reservation/'+this.selectedEvent.id)
                .then((response) => {
                    console.log(response);
                    this.message_text = "Event Deleted"
                    this.message = true
                    this.$emit('refresh-schedule')
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.$emit('refresh-schedule')
            }
            this.dialog_edit = false
            this.areyousure = false
        },
        updateEvent(edit) {
            if(edit){
                console.log(this.newCompTimePayload)
                // this.newCompTimePayload.ordered_participants_ids = ""
                axios.put('api/reservation/update', this.newCompTimePayload)
                .then((response) => {
                    console.log(response);
                    if(response.data == "error"){
                        this.message_text = "Time Conflict"
                        this.message = true
                        this.$emit('refresh-schedule')
                    } else{
                        this.message_text = "Event Updated"
                        this.message = true
                        this.$emit('refresh-schedule')
                    }
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.message_text = "Event Drag Canceled"
                this.message = true
                this.$emit('refresh-schedule')
            }
            this.dialog_verify_update = false
        },
        showIntervalLabel(interval){
            return (interval.minute === 0 && interval.hour != 8)
        },
        startDrag ({ event, timed }) {
            if (event && timed) {
                console.log("start drag")
                this.dragOriginalStart = event.start
                this.dragOriginalEnd = event.end
                this.dragOriginalCategory = event.category
                this.dragEvent = event
                this.dragTime = null
                this.extendOriginal = null
            }
        },
        startTime (tms) {
            const mouse = this.toTime(tms)
            
            if (this.dragEvent && this.dragTime === null) {
                console.log("here 1")
                const start = this.dragEvent.start

                this.dragTime = mouse - start
            } else {
                console.log("here 2")
                this.createStart = this.roundTime(mouse)
                this.createEvent = {
                    name: `New Event`,
                    start: this.createStart,
                    end: this.createStart+30*1000*60,
                    category: tms.category.categoryName,
                    timed: true,
                    method: "Call",
                    num_of_members: 0,
                    num_of_guests: 0,
                    host_id: null,
                    timed: 1
                }

                this.events.push(this.createEvent)
            }
        },
        extendBottom (event) {
            console.log("extend")
            this.createEvent = event
            this.createStart = event.start
            this.extendOriginal = event.end
        },
        mouseMove (tms) {
            const mouse = this.toTime(tms)

            if (this.dragEvent && this.dragTime !== null) {
                console.log("here 3")
                console.log(tms.category.categoryName)
                const start = this.dragEvent.start
                const end = this.dragEvent.end
                const duration = end - start
                const newStartTime = mouse - this.dragTime
                const newStart = this.roundTime(newStartTime)
                const newEnd = newStart + duration

                // console.log(newStart)
                this.dragEvent.start = newStart
                this.dragEvent.end = newEnd
                this.dragEvent.category = tms.category.categoryName
            } else if (this.createEvent && this.createStart !== null) {
                
                
                console.log("here 4")
                
                // console.log(this.createStart)
                // console.log(this.extendOriginal)

                const mouseRounded = this.roundTime(mouse, false)
                const min = Math.min(mouseRounded, this.createStart)
                const max = Math.max(mouseRounded, this.createStart)

                this.createEvent.start = min
                this.createEvent.end = max

                if(this.createEvent.start==this.createEvent.end){
                    this.createEvent.end+=30*1000*60
                }
                if(this.createEvent.category != tms.category.categoryName){
                    this.cancelDrag()
                }
            }
        },
        // getReservations() {
        //     axios.get('api/reservations')
        //         .then(response => {
        //             this.reservations = response.data
        //         })
        //         .catch(error => {
        //             console.log(error);
        //         })
        // },
        endDrag () {
            // Drag event always an update (put)
            if(this.dragEvent){
                if(new Date(this.dragEvent.start).getHours() < 8){ 
                    var temp = new Date(this.dragEvent.start)
                    temp.setHours(8)
                    temp.setMinutes(0)
                    this.dragEvent.start = temp.getTime() 
                }
                // short term fix 
                // console.log(new Date(this.dragEvent.end).getHours())
                if(new Date(this.dragEvent.end).getHours() >= 20){ 
                    var temp = new Date(this.dragEvent.end)
                    temp.setHours(20)
                    temp.setMinutes(0)
                    this.dragEvent.end = temp.getTime() 
                }
                
                let item = JSON.parse(JSON.stringify(this.dragEvent))
                
                this.newCompTimePayload = {
                    item
                }
                this.newCompTimePayload.item.ordered_participants_ids = -1

                this.selectedEvent = this.dragEvent
                // this.dialog_edit = true
                console.log("dialog 1 (are you sure)")
                if(this.dragOriginalStart != this.dragEvent.start || this.dragOriginalEnd != this.dragEvent.end || this.dragOriginalCategory != this.dragEvent.category){
                    console.log("slide date!")
                    this.selectedEvent.orig_category = this.dragOriginalCategory
                    this.selectedEvent.orig_start = this.dragOriginalStart
                    this.selectedEvent.orig_end = this.dragOriginalEnd
                    this.dialog_verify_update = true
                    // console.log("slide date!")
                } else {
                    this.getParticipants()
                    // this.dialog_edit = true
                }
                
            } 
            // Extend could be update or create
            else if(this.createEvent){
                this.selectedEvent = this.createEvent
                this.selectedEvent.orig_category = this.selectedEvent.category
                this.selectedEvent.orig_start = this.createStart
                this.selectedEvent.orig_end = this.extendOriginal
                
                let item = JSON.parse(JSON.stringify(this.createEvent))

                this.newCompTimePayload = {
                    item
                }
                // new event
                if(!this.createEvent.id){
                    axios.post('api/reservation/store', this.newCompTimePayload)
                    .then((response) => {
                        console.log(response);
                        if(response.data=="error"){
                            this.message_text = "Time Conflict"
                            this.message = true
                        } else {
                            this.message_text = "Event Created"
                            this.message = true
                            this.selectedEvent.id = response.data.id
                            this.getParticipants()
                            // this.dialog_edit = true
                        }
                        this.$emit('refresh-schedule')
                    }, (error) => {
                        console.log(error);
                    });
                } 
                // extending existing event
                else {
                    this.newCompTimePayload.item.ordered_participants_ids = -1
                    this.dialog_verify_update = true
                }
                console.log("dialog 2")

            }
            this.dragTime = null
            this.dragEvent = null
            this.dragOriginalStart = null
            this.dragOriginalEnd = null
            this.dragOriginalCategory = null
            this.createEvent = null
            this.createStart = null
            this.extendOriginal = null
        },
        cancelDrag () {
            console.log("left native!")
            if (this.createEvent) {
                if (this.extendOriginal) {
                    this.createEvent.end = this.extendOriginal
                } else {
                    const i = this.events.indexOf(this.createEvent)
                    if (i !== -1) {
                        this.events.splice(i, 1)
                    }
                }
            }

            this.createEvent = null
            this.createStart = null
            this.dragTime = null
            this.dragEvent = null
        },
        getParticipants() {
            // this.selectedEvent.host_id = ''
            this.selectedEvent.participants = []
            axios.get('api/reservation_users/'+this.selectedEvent.id+'/'+this.selectedEvent.host_id)
            .then(response => {
                console.log(response)
                console.log("response")
                this.selectedEvent.host_id = response.data.res_host
                this.selectedEvent.participants = response.data.res_participants
                this.dialog_edit = true
            })
            .catch(error => {
                console.log(error)
            })
        },
        roundTime (time, down = true) {
            const roundTo = 30 // minutes
            const roundDownTime = roundTo * 60 * 1000

            return down
                ? time - time % roundDownTime
                : time + (roundDownTime - (time % roundDownTime))
        },
        toTime (tms) {
            return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
        },
        getEventColor (event) {
            if(event.method == "Admin Event"){ event.color = this.colors[0] }
            else if(event.method == "Call" || event.method == "Walk-In"){ event.color = this.colors[1] }
            else if(event.method == "Tennis Pro"){ event.color = this.colors[2] }
            else if(event.method == "USTA"){ event.color = this.colors[3] }
            else{ event.color = this.colors[4] }
            const rgb = parseInt(event.color.substring(1), 16)
            const r = (rgb >> 16) & 0xFF
            const g = (rgb >> 8) & 0xFF
            const b = (rgb >> 0) & 0xFF

            return event === this.dragEvent
                ? `rgba(${r}, ${g}, ${b}, 0.7)`
                : event === this.createEvent
                ? `rgba(${r}, ${g}, ${b}, 0.7)`
                : event.color
        },
        rnd (a, b) {
            return Math.floor((b - a + 1) * Math.random()) + a
        },
        rndElement (arr) {
            return arr[this.rnd(0, arr.length - 1)]
        },
        setToday () {
            //using new Date() migth be an issue
            this.$emit('refresh-schedule')
            this.curr_date = new Date().toISOString().substr(0, 10)
        },
        prev () {
            this.$emit('refresh-schedule')
            this.$refs.calendar.prev()

        },
        next () {
            this.$emit('refresh-schedule')
            this.$refs.calendar.next()
        },
<<<<<<< HEAD
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
                    //duration: (end-start),
                    // color: this.colors[this.rnd(0, this.colors.length - 1)],
                    color: color,
                    timed: true,
                    category: this.categories[this.appointments[i].court-1],
                })
||||||| 5840f3e
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
=======
        nth (d) {
            return d > 3 && d < 21
            ? 'th'
            : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
        },
        // convertDate(dt){
        //     return `${
        //         dt.getFullYear().toString().padStart(4, '0')}-${
        //         (dt.getMonth()+1).toString().padStart(2, '0')}-${
        //         dt.getDate().toString().padStart(2, '0')} ${
        //         dt.getHours().toString().padStart(2, '0')}:${
        //         dt.getMinutes().toString().padStart(2, '0')}:${
        //         dt.getSeconds().toString().padStart(2, '0')}`
        // },
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
>>>>>>> development
            }
            hour = parseInt(hour)
            return hour+ ":"+min+" "+amOrPm
        },
        displayDate (calDate) {
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
        },
    },

}
</script>


<style lang="scss">
//   @import '~vuetify/src/components/VStepper/_variables.scss';
    // @import '~vuetify/src/resources/sass/_variables.scss';
    @import 'resources/sass/_variables.scss';
    $calendar-event-right-empty: 0px;
</style>

<style>
/* $calendar-event-right-empty 0px  */
.v-event-draggable {
padding-left: 6px;
}

.v-event-timed {
user-select: none;
-webkit-user-select: none;
}

.v-event-drag-bottom {
position: absolute;
left: 0;
right: 0;
bottom: 4px;
height: 4px;
cursor: ns-resize;
}

.v-event-drag-bottom::after {
display: none;
position: absolute;
left: 50%;
height: 4px;
border-top: 1px solid white;
border-bottom: 1px solid white;
width: 16px;
margin-left: -8px;
opacity: 0.8;
content: '';
}

.v-event-drag-bottom:hover::after {
display: block;
}
</style>
