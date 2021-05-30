<template>
<v-container fluid class="pa-0 ma-0">
    <v-row class="fill-height pt-3">
        <v-col>
            <v-app-bar flat dense color="white">
                <v-btn v-if="$vuetify.breakpoint.name != 'xs'" outlined class="ml-4 mr-2" color="blue darken-4" @click="setToday">Today</v-btn>
                <div class="flex text-center mx-8" 
                v-bind:style="[$vuetify.breakpoint.name != 'xs' ? { 
                    position: 'relative', right: 32 + 'px'
                } : { position: 'relative', left: 24 + 'px'
                }]">
                <v-btn icon text medium color="blue darken-4" @click="prev">
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
                        color="blue darken-4"
                    > 
                    <!-- {{ displayDate(new Date(curr_date+'T00:00')) }}  -->
                    {{ displayDate(new Date(curr_date)) }} 
                    <!-- {{ curr_date }}  -->
                    </v-btn>
                    </template>
                    <v-date-picker
                    v-model="curr_date"
                    no-title
                    @input="dropdown_cal = false"
                    ></v-date-picker>
                </v-menu>

                <v-btn icon text medium color="blue darken-4" @click="next">
                    <v-icon>mdi-chevron-right</v-icon>
                </v-btn>
                </div>
            </v-app-bar>
        </v-col>
    </v-row>
    <v-row>
        <v-col align="center" class="mb-2">
            <div v-if="fullScreen==false">
            <v-btn-toggle style="position: relative; left: 24px;" class="mx-6" v-model="scheduleView" rounded dense mandatory btn-toggle-btn-height="170px">
                <v-btn
                >1-8</v-btn>
                <v-btn
                >9-17</v-btn>
            </v-btn-toggle>
            </div>
        </v-col>
    </v-row>
    <v-row class="pl-15 pr-4 pt-2 pb-2 blue darken-4 text-white">
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
            v-if="cal_loaded"
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
                <div v-if="selectedEvent.reoccur_id!=null">
                        (Reoccuring Event)</div>
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
                    <div v-if="selectedEvent.reoccur_id==null">
                        <v-list-item @click="showAreYouSure(0)">
                            <v-list-item-title>Delete Event</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="repeat_dialog=true">
                            <v-list-item-title>Repeat Event</v-list-item-title>
                        </v-list-item>
                    </div>
                    <div v-else>
                        <v-list-item @click="showAreYouSure(0)">
                            <v-list-item-title>Delete This Event Only</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="showAreYouSure(1)">
                            <v-list-item-title>Delete This Event and Following Events</v-list-item-title>
                        </v-list-item>
                    </div>
                </v-list>
                </v-menu>
            </v-toolbar>

            <div v-if="selectedEvent" class="mx-5">
            <v-row>
            <v-col>
            <v-text-field
              label="Date"
              :color="getEventColor(selectedEvent)"
              v-model="curr_date"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            <v-col>
            <v-text-field
              label="Start Time"
              :color="getEventColor(selectedEvent)"
              v-model="computedTimes"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            <v-col>
            <v-text-field
              label="End Time"
              :color="getEventColor(selectedEvent)"
              v-model="computedTimes2"
              outlined
              readonly
            ></v-text-field>
            </v-col>
            </v-row>
            <v-text-field v-model="selectedEvent.name" :color="getEventColor(selectedEvent)" label="Event Title"></v-text-field>
            <v-select
                v-model="selectedEvent.method"
                :items="method_type"
                item-text="name"
                item-value="name"
                :menu-props="{ top: false, offsetY: true }"
                :color="getEventColor(selectedEvent)"
                label="Reservation Type"
            ></v-select>
            <v-text-field v-model="selectedEvent.custom" v-if="selectedEvent.method=='Custom'" dense label="'Custom' Type Name"></v-text-field>
            <v-row>
            <v-col cols="7">
            <v-autocomplete
                v-model="selectedEvent.host_id"
                :items="members"
                :color="getEventColor(selectedEvent)"
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
                <v-text-field :color="getEventColor(selectedEvent)" height=42 readonly v-model="selectedEvent.num_of_guests" type="number" label="Number of Guests" append-outer-icon="mdi-plus" @click:append-outer="selectedEvent.num_of_guests = parseInt(selectedEvent.num_of_guests,10) + 1" prepend-icon="mdi-minus" @click:prepend="selectedEvent.num_of_guests = parseInt(selectedEvent.num_of_guests,10) - 1"></v-text-field>
            </v-col>
            </v-row>

            <v-autocomplete
                v-model="selectedEvent.participants"
                :items="computedMembers"
                :key="participantsLoaded"
                :loading="!participantsLoaded"
                :disabled="!participantsLoaded"
                :color="getEventColor(selectedEvent)"
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

            
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn :color="getEventColor(selectedEvent)" text @click="saveEvent(true)">Save</v-btn>
                <v-btn :color="getEventColor(selectedEvent)" text @click="saveEvent(false)">Close</v-btn>
            </v-card-actions>     
            </div>   
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
            v-model="areyousure_reoccur"
            persistent
            max-width="450"
        >
            <v-card>
                <v-card-title>
                    Delete Reservations
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete this reservation and future reoccuring reservations?
                </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="deleteReoccur(true)">Yes</v-btn>
                <v-btn color="blue darken-1" text @click="deleteReoccur(false)">No</v-btn>
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
                    :menu-props="{ top: false, offsetY: true }"
                ></v-select>
                <div v-if="reoccur_err">
                <v-alert
                    dense
                    outlined
                    type="error"
                >
                    {{reoccur_err_mess}}
                </v-alert>
                </div>
                </div>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="repeat_event(true)">Save</v-btn>
                <v-btn color="blue darken-1" text @click="repeat_event(false)">Cancel</v-btn>
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

    <v-overlay :value="refreshLoad">
    <v-progress-circular
        indeterminate
        size="64"
    ></v-progress-circular>
    </v-overlay>

</v-container>
</template>

<script>
export default {
    props: ['reservations','users','session_data', 'categories'],

    data: () => ({
        curr_date: new Date().toISOString().substr(0, 10),
        dropdown_cal: false,
        events: [],
        members: [],
        method_type: [],
        cal_loaded: false,
        // method_type: ['Admin Event', 'Call', 'Walk-In', 'Tennis Pro', 'USTA', 'Member', 'Custom'],
        // repeat_type: ['Repeat Event Weekly', 'Repeat Event Monthly'],
        repeat_select: 0,
        // {   display: 'Repeat Event Weekly',
        //     value: 0
        // },
        repeat_type: [{
            text: 'Repeat Event Weekly',
            value: 0
        },{
            text: 'Repeat Event Monthly',
            value: 1
        }],
        // repeat_select: 'Repeat Event Weekly',
        repeat_dialog: false,
        // colors: ['#0196F3', '#3F51B5', '#00BCD4', '#4CAF50', '#FF9800', '#757575'],        // names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
        scheduleView: 0,
        searchInput: null,
        fullScreen: false,

        participantsLoaded: false,
        refreshLoad: true,

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
        areyousure_reoccur: false,

        message: false,
        message_text: '',
        timeout: 3000,
        reoccur_err: false,
        reoccur_err_mess: null,

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
        console.log("first")
        this.refreshLoad = true
        this.$emit('refresh-users')
        this.$emit('refresh-categories')
        this.$emit('refresh-schedule')

        
    },
    watch: {
        reservations (val) {
            this.events=val
            this.refreshLoad = false
            console.log("second")
        },
        users (val) {
            this.members=val
        },
        categories (val) {
            this.method_type=val
            this.cal_loaded = true
            // var members_no_host = JSON.parse(JSON.stringify(this.method_type))
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
        repeat_event(bool) {
            if(bool){
                if(this.repeat_select == 0){//weekly
                    let item = JSON.parse(JSON.stringify(this.selectedEvent))
                    this.newCompTimePayload = {
                        item
                    }
                    item.reoccur_type = "weekly"
                    // new event
                    this.refreshLoad = true
                    axios.post('api/reservation/storeReoccur', this.newCompTimePayload)
                    .then((response) => {
                        console.log(response);
                        if(response.data["status"]=="success"){
                            this.repeat_dialog = false
                            this.saveEvent(false)
                            this.message_text = response.data["message"]
                            this.message = true
                        } else if(response.data["status"]=="error"){
                            this.reoccur_err_mess = response.data["message"]
                            this.reoccur_err = true
                            // console.log("POOP")
                        } 
                        // if(response.data=="error"){
                        //     this.message_text = "Time Conflict"
                        //     this.message = true
                        // } else {
                        //     this.message_text = "Event Created"
                        //     this.message = true
                        //     this.selectedEvent.id = response.data.id
                        //     this.getParticipants()
                        //     // this.dialog_edit = true
                        // }
                        // this.refreshLoad = true
                        // this.$emit('refresh-schedule')
                    }, (error) => {
                        console.log(error);
                    });
                } else if(this.repeat_select == 1){//monthly
                    console.log("hi")
                }
                console.log(this.selectedEvent)
                console.log(this.repeat_select)
                // console.log(this.reoccur_id)
            } else{
                this.repeat_dialog = false
                this.reoccur_err = false
            }
        },
        saveEvent(save) {
            if(save){
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
                this.newCompTimePayload = {
                    item
                }
                // console.log("this.newCompTimePayload")
                // console.log(this.selectedEvent.host_id)
                // console.log(this.newCompTimePayload.item.host_id)
                this.participantsLoaded = false
                this.updateEvent(true)
            } else {
                this.participantsLoaded = false
                this.refreshLoad = true
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
                    this.refreshLoad = true
                    this.$emit('refresh-schedule')
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.refreshLoad = true
                this.$emit('refresh-schedule')
            }
            this.dialog_edit = false
            this.areyousure = false
        },
        deleteReoccur(del_event){
            if(del_event){
                axios.delete('api/reservation/deleteReoccur/'+this.selectedEvent.id)
                .then((response) => {
                    console.log(response);
                    this.message_text = "Event Deleted"
                    this.message = true
                    this.refreshLoad = true
                    this.$emit('refresh-schedule')
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.refreshLoad = true
                this.$emit('refresh-schedule')
            }
            this.dialog_edit = false
            this.areyousure_reoccur = false
        },
        showAreYouSure(num){
            if(num==1){
                console.log(this.selectedEvent)
                this.areyousure_reoccur=true;
            } else if(num==0){
                this.areyousure=true;
                // deleteFinal(true)
            }
            
        },
        updateEvent(edit) {
            if(edit){
                console.log(this.newCompTimePayload)
                if(this.newCompTimePayload.item.method=="Custom"){
                    this.newCompTimePayload.item.method=this.newCompTimePayload.item.custom
                }
                // this.newCompTimePayload.ordered_participants_ids = ""
                axios.put('api/reservation/update', this.newCompTimePayload)
                .then((response) => {
                    console.log(response);
                    if(response.data == "error"){
                        this.message_text = "Time Conflict"
                        this.message = true
                        this.refreshLoad = true
                        this.$emit('refresh-schedule')
                    } else{
                        this.message_text = "Event Updated"
                        this.message = true
                        this.refreshLoad = true
                        this.$emit('refresh-schedule')
                    }
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.message_text = "Event Drag Canceled"
                this.message = true
                this.refreshLoad = true
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
                    reoccur_id: null,
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
                    if(!this.checkForCustomMethod(this.selectedEvent.method)){
                        this.selectedEvent.custom = this.selectedEvent.method
                        this.selectedEvent.method = "Custom"
                    }
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
                        // this.refreshLoad = true
                        // this.$emit('refresh-schedule')
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
            // console.log("left native!")
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
            this.dialog_edit = true
            axios.get('api/reservation_users/'+this.selectedEvent.id+'/'+this.selectedEvent.host_id)
            .then(response => {
                console.log(response)
                console.log("response")
                this.selectedEvent.host_id = response.data.res_host
                this.selectedEvent.participants = response.data.res_participants
                // this.dialog_edit = true
                this.participantsLoaded = true
                // this.participantsLoaded = false
            })
            .catch(error => {
                console.log(error)
            })
        },
        checkForCustomMethod (method){

            this.method_type.forEach(function (item, index) {
                if(method==item.name){
                    return true
                }
            });
            return false
            // if(method=="temp function") {
            //     return true
            // } else {
            //     return true
            // }
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
            const colors = ['#0196F3', '#3F51B5', '#00BCD4', '#4CAF50', '#FF9800', '#723575', '#550725', '#752302', '#014575','#515075', '#753021', '#145715']
            
            event.color = colors[6]

            this.method_type.forEach(function (item, index) {
                if(event.method==item.name){
                    // console.log("i be damned",colors[item.color])
                    event.color = colors[item.color]
                    // return colors[item.color]
                }
                // console.log(colors[0], index);
            });

            const rgb = parseInt(event.color.substring(1), 16)
            const r = (rgb >> 16) & 0xFF
            const g = (rgb >> 8) & 0xFF
            const b = (rgb >> 0) & 0xFF

            return event === this.dragEvent
                ? `rgba(${r}, ${g}, ${b}, 0.7)`
                : event === this.createEvent
                ? `rgba(${r}, ${g}, ${b}, 0.7)`
                : event.color
            // .indexOf(this.selectedEvent.host_id)
            // return colors[0];
        },
        rnd (a, b) {
            return Math.floor((b - a + 1) * Math.random()) + a
        },
        rndElement (arr) {
            return arr[this.rnd(0, arr.length - 1)]
        },
        setToday () {
            //using new Date() migth be an issue
            this.refreshLoad = true
            this.$emit('refresh-schedule')
            this.curr_date = new Date().toISOString().substr(0, 10)
        },
        prev () {
            this.refreshLoad = true
            this.$emit('refresh-schedule')
            this.$refs.calendar.prev()

        },
        next () {
            this.refreshLoad = true
            this.$emit('refresh-schedule')
            this.$refs.calendar.next()
        },
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




<style>
/* .#test{
    $calendar-event-right-empty: 2px !default;
} */
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
