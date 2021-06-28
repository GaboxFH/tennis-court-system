<template>
<v-container fluid>
<!-- <v-row>
  <v-col>
    <br><br>Previous: <br>
    {{previous}} <br>
    {{session_data}} <br>
  </v-col>
</v-row> -->
<!-- <h2>Reservations</h2> -->
<v-card
        class="mx-auto"
        max-width="1000px"
        outlined
    >
    <v-list-item three-line>
        <v-list-item-content>
            <!-- <div class="text-overline mb-4">
                Reservations
            </div> -->
            <h1 style="
            font-family: 'Roboto', sans-serif;
            font-size: 28px;
            font-weight: 300;
            letter-spacing: .1566666667em;
            ">RESERVATIONS</h1>
            <!-- <v-list-item-title class="text-h5 mb-1">
                Active Reservations
            </v-list-item-title> -->
        </v-list-item-content>

        <!-- <v-btn @click="club_settings_dialog=true" color="#757575" class="px-2" outlined small>
            Club Rules
            <v-icon color="#757575" class="pl-1">mdi-cog</v-icon>
        </v-btn> -->
        <v-list-item-avatar>
          <v-btn class="mx-2" fab dark color="primary" @click="new_event_dialog=true">
            <v-icon dark>
              mdi-plus
            </v-icon>
          </v-btn>
        </v-list-item-avatar>
    </v-list-item>
<v-row>
  <v-col>
    <v-data-table
      :loading="!load1"
      :headers="headers_act"
      :items="current"
      :items-per-page="15"
      :footer-props="{ 'items-per-page-options': [15, 30, 45, 60, 75, 100] }"
      disable-sort dense
      no-data-text="No Active Reservations"
      mobile-breakpoint="0"
      class="elevation-1"
    >
    <template v-slot:top>
      <v-toolbar flat dense>
        <v-toolbar-title>Active Reservations</v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
    </template>
    </v-data-table>
  </v-col>
</v-row>
<v-row>
  <v-col>
    <v-data-table
      :loading="!load1"
      :headers="headers_fut"
      :items="future"
      :items-per-page="15"
      :footer-props="{ 'items-per-page-options': [15, 30, 45, 60, 75, 100] }"
      disable-sort dense
      no-data-text="No Upcoming Reservations"
      class="elevation-1"
    >
    <template v-slot:top>
      <v-toolbar flat dense>
        <v-toolbar-title>Upcoming Reservations</v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
    </template>
    </v-data-table>
  </v-col>
</v-row>
<v-row>
  <v-col>
    <v-data-table
      :loading="!load1"
      :headers="headers_prev"
      :items="previous"
      :items-per-page="15"
      :footer-props="{ 'items-per-page-options': [15, 30, 45, 60, 75, 100] }"
      disable-sort dense
      no-data-text="No Previous Reservations"
      class="elevation-1"
    >
    <template v-slot:top>
      <v-toolbar flat dense>
        <v-toolbar-title>Previous Reservations</v-toolbar-title>
        <v-spacer></v-spacer>
      </v-toolbar>
    </template>
    </v-data-table>
  </v-col>
</v-row>
</v-card>

<v-dialog v-model="new_event_dialog" persistent max-width="600px" class="pa-0 ma-0" content-class="custom-dialog">
  <v-card class="pa-0 ma-0">   
      <v-toolbar class="mb-3" :color="computedColor" dark flat>
          <v-card-title class="categories_style">{{computedTitle}}</v-card-title>
      </v-toolbar>
      
      <div v-if="new_event" class="mx-2 pt-1">
      <v-row class="pa-0 ma-0">
      <v-col class="pa-0 ma-0">
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
                class="pr-2" :color="computedColor"
                v-bind="attrs"
                v-on="on"
                min-width="200px"
                append-icon="mdi-menu-down"
                @click:append="dropdown_cal = true"
                label="Date" dense
                readonly
                :value="formatDate(new_event.date)"
            ></v-text-field>
            </template>
            <v-date-picker
            v-model="new_event.date"
            no-title
            @input="dropdown_cal = false"
            ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="4" class="pa-0 ma-0">
      <v-select
      label="Court" dense
      v-model="new_event.category"
      :color="computedColor"
      :items="all_courts"
      :menu-props="{ top: false, offsetY: true }"
      >
      </v-select>
      </v-col>
      </v-row>
      <v-row class="pa-0 ma-0">
      <v-col cols="6" class="pa-0 ma-0">
      <!-- <v-text-field
          label="Start Time"
            
          v-model="computedTimes"
          outlined
          readonly
      ></v-text-field> 
            :item-color="getEventColor(new_event)"
-->
      <v-select
      class="pr-1"
      label="Start Time" dense
      v-model="new_event.start"
      :color="computedColor"
      :items="times_ranges"
      item-text="show"
      item-value="val"
      :menu-props="{ top: false, offsetY: true }"
      >
      </v-select>
      </v-col>
      <v-col cols="6" class="pa-0 ma-0">
      <!-- <v-text-field
          label="End Time"
            
          v-model="computedTimes2"
          outlined
          readonly
      ></v-text-field> -->
      <v-select
      label="End Time" dense
      v-model="new_event.end"
      class="pl-1"
      :color="computedColor"
      :items="times_ranges"
      item-text="show"
      item-value="val"
      :menu-props="{ top: false, offsetY: true }"
      >
      </v-select>
      </v-col>
      </v-row>
      <!-- <v-text-field v-model="new_event.name"    label="Event Title"></v-text-field> -->
      <v-select
          v-model="new_event.method"
          :items="categories"
          item-text="name"
          item-value="name"
          :menu-props="{ top: false, offsetY: true }"
          :color="computedColor"
          label="Reservation Type" dense
      ></v-select>
      <v-text-field v-model="new_event.custom" v-if="new_event.method=='Custom'"    dense label="'Custom' Type Name"></v-text-field>
      <v-row class="pa-0 ma-0">
      <v-col class="pa-0 ma-0">
      <v-autocomplete
          v-model="new_event.host_id"
          :items="users"
          :color="computedColor"
          label="Host" dense
          chips
          hide-selected
          item-text="name"
          item-value="id"
      >                    
      <!-- <template v-slot:selection="data">
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
      </template> -->
      </v-autocomplete>
      </v-col>
      <!-- <v-col cols="6" class="px-0 pt-8 pb-0 ma-0" align="right">
              <v-btn style="text-transform: unset !important;" height="6px"    class="px-2 py-5 mr-1 white--text small_btn_text" @click="addGuest(-1)">
                  Add Guest <br>
                  (non-member)</v-btn>
              <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                  <v-icon class="pa-0 ml-0 mr-2 my-0" v-bind="attrs" v-on="on">mdi-information-outline</v-icon>
              </template>
              <span>Guests may only play free once per month</span>
              </v-tooltip>
      </v-col> -->
      </v-row>

      <v-autocomplete
          v-model="new_event.participants"
          :items="computedMembers"
          :color="computedColor"
          chips
          deletable-chips
          label="Participants" dense
          hide-selected   
          multiple
          item-text="name"
          item-value="id"
          :allow-overflow="false"
          @input="searchInput=null"
          :search-input.sync="searchInput"
      >
      </v-autocomplete>
      <v-row class="px-0 pt-0 pb-4 ma-0"><v-col class="pa-0 ma-0">
      <v-btn style="text-transform: unset !important;" height="6px" :color="computedColor" depressed class="px-2 py-5 mr-1 white--text small_btn_text" @click="addGuest(-1)">
          Add Guest <br>
          (non-member)</v-btn>
      <v-tooltip top>
      <template v-slot:activator="{ on, attrs }">
          <v-icon class="pa-0 ml-0 mr-2 my-0" v-bind="attrs" v-on="on">mdi-information-outline</v-icon>
      </template>
      <span>Guests may only play once per month</span>
      </v-tooltip>
      </v-col></v-row>
      <div v-for="index in 4" :key="index">
          <v-row v-if="selectedGuest.length>=index" class="pa-0 ma-0">
              <v-col cols="11" class="px-0 py-0 ma-0">
                  <v-combobox
                  v-model="selectedGuest[index-1]"
                  :items="guests"
                  :label="'Guest '+ index +' (non-member)'" 
                  :color="computedColor"
                  dense
                  item-text="name"
                  item-value="name"
                  ></v-combobox>
              </v-col>
              <v-col cols="1" align="right" class="pa-0 ma-0">
                  <v-btn icon plain @click="addGuest(index-1)">
                      <v-icon>
                          mdi-close
                      </v-icon>
                  </v-btn>
              </v-col>
          </v-row>
      </div>
      <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn :color="computedColor" text @click="saveEvent(true)">Save</v-btn>
          <v-btn :color="computedColor" text @click="saveEvent(false)">Close</v-btn>
      </v-card-actions>     
      </div>   
  </v-card>
</v-dialog>


<v-snackbar v-model="snackbar_dialog" :timeout="3000" top>
    {{ snackbar_text }}
    <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar_dialog = false"> Close</v-btn>
    </template>
</v-snackbar>

<!-- <v-overlay :value="!(load1 && load2)">
    <v-progress-circular indeterminate size="64"></v-progress-circular>
</v-overlay> -->

</v-container>
</template>

<script>
import moment from 'moment-timezone'

export default {
  props: ['session_data'],

  data: () => ({
    previous: [],
    current: [],
    future: [],
    categories: [],
    users: [],
    guests: [],

    load1: false,
    load2: false,
    current_time: null,

    snackbar_dialog: false,
    snackbar_text: null,   
    searchInput: null, 

    new_event_dialog: false,
    dropdown_cal: false,
    new_event: {
      date: null,
      category: null,
      start: null,
      end: null,
      method: "Members",
      host_id: null,
      participants: [],
      timed: 1,
    },
    selectedGuest: [],

    headers_act: [
      { text: 'Date', align: 'start', value: 'date' },
      { text: 'Start', value: 'start' },
      { text: 'End', value: 'end' },
      { text: 'Court', value: 'category' },
      { text: 'Category', value: 'method' },
      { text: 'Participants', value: 'ppts' },
      { text: 'Time Left', value: 'timeRemain' },
    ],
    headers_fut: [
      { text: 'Date', align: 'start', value: 'date' },
      { text: 'Start', value: 'start' },
      { text: 'End', value: 'end' },
      { text: 'Court', value: 'category' },
      { text: 'Category', value: 'method' },
      { text: 'Participants', value: 'ppts' },
      { text: 'Time Until', value: 'timeUntil' },
    ],
    headers_prev: [
      { text: 'Date', align: 'start', value: 'date' },
      { text: 'Start', value: 'start' },
      { text: 'End', value: 'end' },
      { text: 'Court', value: 'category' },
      { text: 'Category', value: 'method' },
      { text: 'Participants', value: 'ppts' },
      { text: 'Time Since', value: 'timeSince' },
    ],
    all_courts: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17'],
    times_ranges: [
        {show:"8:00 am", val: 28800000, ind: 0},
        {show:"8:30 am", val: 30600000, ind: 1},
        {show:"9:00 am", val: 32400000, ind: 2},
        {show:"9:30 am", val: 34200000, ind: 3},
        {show:"10:00 am",val: 36000000, ind: 4},
        {show:"10:30 am",val: 37800000, ind: 5},
        {show:"11:00 am",val: 39600000, ind: 6},
        {show:"11:30 am",val: 41400000, ind: 7},
        {show:"12:00 pm",val: 43200000, ind: 8},
        {show:"12:30 pm",val: 45000000, ind: 9},
        {show:"1:00 pm", val: 46800000, ind: 10},
        {show:"1:30 pm", val: 48600000, ind: 11},
        {show:"2:00 pm", val: 50400000, ind: 12},
        {show:"2:30 pm", val: 52200000, ind: 13},
        {show:"3:00 pm", val: 54000000, ind: 14},
        {show:"3:30 pm", val: 55800000, ind: 15},
        {show:"4:00 pm", val: 57600000, ind: 16},
        {show:"4:30 pm", val: 59400000, ind: 17},
        {show:"5:00 pm", val: 61200000, ind: 18},
        {show:"5:30 pm", val: 63000000, ind: 19},
        {show:"6:00 pm", val: 64800000, ind: 20},
        {show:"6:30 pm", val: 66600000, ind: 21},
        {show:"7:00 pm", val: 68400000, ind: 22},
        {show:"7:30 pm", val: 70200000, ind: 23},
        {show:"8:00 pm", val: 72000000, ind: 24},
    ],
    color_options: [ '#064b9a', '#f0da16', '#548e66', '#f68324', '#f2618f', '#7c7c7c', '#db4c4b', '#a5e3f6', '#167d9f', '#90DF68', '#B266FF', '#8e6a6b' ],

  }),

  
  computed: {
    computedMembers() {
        if(this.new_event.host_id == null || this.new_event.participants == null){
            return this.users
        } else {
            // dont show host on option list
            var members_no_host = JSON.parse(JSON.stringify(this.users))
            var host_index = members_no_host.map(function(item) { return item.id; }).indexOf(this.new_event.host_id)
            members_no_host.splice(host_index, 1)
        
            // remove host from list
            if(Object.entries(this.new_event.participants).length != 0){
                var index = this.new_event.participants.indexOf(this.new_event.host_id)
                if (index >= 0) this.new_event.participants.splice(index, 1)
            }
            // console.log("members_no_host")
            // console.log(members_no_host)
            return members_no_host
        }
    },
    computedColor(){
      // console.log("this.new_event.method")
      // console.log(this.new_event.method)
      // if(!this.load2 || this.new_event){
      //   return `rgba(150, 150, 150,0.6)`;
      // } 
      // else {
      // const colors = this.color_options;
      // var event_color = colors[this.categories[0].color]
      // this.categories.forEach(function (item, index) {
      //     if(this.new_event.method==item.name){
      //         event_color = colors[item.color]
      //     }
      // });
      // const rgb = parseInt(event_color.substring(1), 16)
      // const r = (rgb >> 16) & 0xFF
      // const g = (rgb >> 8) & 0xFF
      // const b = (rgb >> 0) & 0xFF

      // return `rgba(${r}, ${g}, ${b}, 1)`
      // }
      if(this.load2){
      const colors = this.color_options;
      var event_color = colors[this.categories[0].color]
      for(var x=0; x<this.categories.length; x++){
        // if(this.categories[x].name)
        if(this.categories[x].name == this.new_event.method){
          return colors[this.categories[x].color]
        }
        // return this.new_event.method
        // return this.categories[x].name
      }
      return event_color
      }
      return
      // return colors;
      // return this.categories
      // return this.new_event.method
      // return `rgba(150, 150, 150,0.6)`;
      // if(this.new_event.)
      // this.new_event.name = this.new_event.method
      // this.categories[]
    },
    computedTitle(){
      // if(this.new_event.)
      // this.new_event.name = this.new_event.method
      
      return this.new_event.method
    },
    tellTime(ok){
      // return new Date()
      return ok
    }
  },
  watch: {
    // dialog(val) {
    //   val || this.close();
    // },
    // dialogDelete(val) {
    //   val || this.closeDelete();
    // },
  },

  created() {
    this.loadPage() 
  },

  methods: {
    addGuest(num){
        if(num==-1 && this.selectedGuest.length<4){
            this.selectedGuest.push(null);
        } else if(num!=-1){
            this.selectedGuest.splice(num,1);
        }
        console.log(this.selectedGuest)
    },
    loadPage(){
      this.schedule_date = moment().tz('America/New_York').format('YYYY-MM-DD')
      this.new_event.date = moment().tz('America/New_York').format('YYYY-MM-DD')
      this.current_time = moment().tz('America/New_York').valueOf()
      this.getReservations()   
      this.getCategories() 
      this.getUsers()
      this.getGuests()
    },
    formatDate(date){
        return moment(date).format('dddd MMMM Do')
    },
    shortFormatDate(date){
        return moment(date).format('ddd MMM Do')
    },
    dateToMilli(date){
        return moment(date).valueOf()
    },
    hoursToHHMMSS(decimalTimeString){
      var decimalTime = parseFloat(decimalTimeString);
      decimalTime = decimalTime * 60 * 60;
      var hours = Math.floor((decimalTime / (60 * 60)));
      decimalTime = decimalTime - (hours * 60 * 60);
      var minutes = Math.floor((decimalTime / 60));
      decimalTime = decimalTime - (minutes * 60);
      var seconds = Math.round(decimalTime);
      if(hours < 10)
      {
        hours = "0" + hours;
      }
      if(minutes < 10)
      {
        minutes = "0" + minutes;
      }
      if(seconds < 10)
      {
        seconds = "0" + seconds;
      }
      // alert("" + hours + ":" + minutes + ":" + seconds);
      return(hours + ":" + minutes + ":" + seconds);
    },
    saveEvent(yes){
      if(yes){
        // console.log(this.newCompTimePayload)
        var ordered_participants_ids = JSON.parse(JSON.stringify(this.new_event.participants))
        if(this.new_event.host_id==''){ 
            this.new_event.host_id = null
        }
        if(this.new_event.host_id!=null){
            ordered_participants_ids.push(this.new_event.host_id)
        }
        ordered_participants_ids.sort(function(a, b){return a-b})

        let item = JSON.parse(JSON.stringify(this.new_event))
        item.ordered_participants_ids = ordered_participants_ids
        // item.date = this.date_input
        item.guests = JSON.parse(JSON.stringify(this.selectedGuest))
        // let item = JSON.parse(JSON.stringify(this.new_event))
        item.num_of_guests = 0
        item.num_of_members = 1
        var newCompTimePayload = {
          item
        }
        if(newCompTimePayload.item.start != null && newCompTimePayload.item.end != null){
          newCompTimePayload.item.start = newCompTimePayload.item.start + this.dateToMilli(this.new_event.date)
          newCompTimePayload.item.end = newCompTimePayload.item.end + this.dateToMilli(this.new_event.date)
          // newCompTimePayload.item.duration = moment((newCompTimePayload.item.end - newCompTimePayload.item.start)).format('hh:mm:ss')
          newCompTimePayload.item.duration = this.hoursToHHMMSS((newCompTimePayload.item.end - newCompTimePayload.item.start)/(60*60*1000))
        }

        // newCompTimePayload.item.start = 
        // dateToMilli
        console.log(newCompTimePayload.item)
        // var newCompTimePayload = null

        axios.post('api/reservation/adminStore', newCompTimePayload)
        .then((response) => {
            console.log(response);
            if(response.data.status == "error"){
                  this.snackbar_text = response.data.message 
                  this.snackbar_dialog = true
              } else if(response.data.status == "success"){
                  this.snackbar_text = response.data.message
                  this.snackbar_dialog = true
                  // this.$router.push({ name: 'my_reservations' });
                  // window.location.href = 'http://localhost:3000/home#/';
                  this.new_event_dialog = false
                  this.loadPage()
              }
            // if(response.data=="error"){
            //     this.snackbar_text = "Time Conflict"
            //     this.snackbar_dialog = true
            //     this.reloadPage()
            //     // this.refreshLoad = true
            //     // this.$emit('refresh-schedule')
            // } else {
            //     this.snackbar_text = "Event Created"
            //     this.snackbar_dialog = true
            //     this.new_event.id = response.data.id
            //     this.new_event.start = this.new_event.start-this.dateToMilli(this.new_event.date)
            //     this.new_event.end = this.new_event.end-this.dateToMilli(this.new_event.date)
            //     this.getParticipants()
            //     // this.edit_event_dialog = true
            // }
            // this.dialog_load = true
        }, (error) => {
            console.log(error);
        });
      } else {
        this.new_event_dialog = false
      }
    },
    getReservations(){
      console.log(this.session_data)
      axios.get('api/curr_reservations/'+this.current_time)
      .then(response => {
        console.log(response)
        this.previous = response.data.previous
        this.current = response.data.current
        this.future = response.data.future
        console.log(this.future.length)

        // var n = 0
        // while(n<this.current.length && n<this.future.length && n<this.previous.length){
        //   if(n<this.current.length){
        //     this.current[i].timeRemain = moment(this.current[i].end).fromNow(true)
        //     this.current[i].date = moment(this.current[i].date).format('MMM Do YYYY')
        //     this.current[i].start = moment(this.current[i].start).format('h:mm a')
        //     this.current[i].end = moment(this.current[i].end).format('h:mm a')
        //   }
        //   if(n<this.future.length){
        //     this.future[i].timeUntil = moment(this.future[i].start).fromNow(true)
        //     this.future[i].date = moment(this.future[i].date).format('MMM Do YYYY')
        //     this.future[i].start = moment(this.future[i].start).format('h:mm a')
        //     this.future[i].end = moment(this.future[i].end).format('h:mm a')
        //   }
        //   if(n<this.previous.length){
        //     this.previous[i].timeSince = moment(this.previous[i].end).fromNow(true)
        //     this.previous[i].date = moment(this.previous[i].date).format('MMM Do YYYY')
        //     this.previous[i].start = moment(this.previous[i].start).format('h:mm a')
        //     this.previous[i].end = moment(this.previous[i].end).format('h:mm a')
        //   }
        //   n+=1;
        // }

        for(var i = 0; i<this.current.length; i++){
          this.current[i].timeRemain = moment(this.current[i].end).fromNow(true)
          this.current[i].date = moment(this.current[i].date).format('MMM Do YYYY')
          this.current[i].start = moment(this.current[i].start).format('h:mm a')
          this.current[i].end = moment(this.current[i].end).format('h:mm a')
        }

        for(var i = 0; i<this.future.length; i++){
          this.future[i].timeUntil = moment(this.future[i].start).fromNow(true)
          this.future[i].date = moment(this.future[i].date).format('MMM Do YYYY')
          this.future[i].start = moment(this.future[i].start).format('h:mm a')
          this.future[i].end = moment(this.future[i].end).format('h:mm a')
        }

        for(var i = 0; i<this.previous.length; i++){
          this.previous[i].timeSince = moment(this.previous[i].end).fromNow(true)
          this.previous[i].date = moment(this.previous[i].date).format('MMM Do YYYY')
          this.previous[i].start = moment(this.previous[i].start).format('h:mm a')
          this.previous[i].end = moment(this.previous[i].end).format('h:mm a')
        }


        this.reservations = response.data
        this.load1 = true;
      })
      .catch(error => {
        console.log(error);
      })
    },
    getCategories(){
      this.load2 = false
      axios.get('api/categories')
      .then(response => {
          console.log("response.data")
          console.log(response.data)
          this.categories = response.data
          this.load2 = true
      })
      .catch(error => {
          console.log(error);
      })
    },
    getUsers(){
        this.load3 = false
        axios.get('api/users')
        .then(response => {
            console.log(response.data)
            this.users = response.data
            this.load3 = true
        })
        .catch(error => {
            console.log(error);
        })
    },
    getGuests(){
        this.load4 = false
        axios.get('api/guests')
        .then(response => {
            console.log(response.data)
            this.guests = response.data
            this.load4 = true
        })
        .catch(error => {
            console.log(error);
        })
    }


  }
};
</script>
