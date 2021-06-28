<template>
<v-container fluid>
  <!-- <div class="mx-auto" style="background-color:yellow; max-width:600px" > -->
  <v-card
    class="mx-auto"
    max-width="800px"
    outlined
  >
  <v-list-item three-line>
    <v-list-item-content>
      <div class="text-overline mb-4">
        Profile
      </div>
      <v-list-item-title class="text-h5 mb-1">
        {{session_data.name}}
      </v-list-item-title>
      <v-list-item-subtitle>{{session_data.email}}</v-list-item-subtitle>
      <v-list-item-subtitle>{{session_data.phone}}</v-list-item-subtitle>
    </v-list-item-content>

    <v-list-item-avatar>
      <v-icon>mdi-cog</v-icon>
    </v-list-item-avatar>
    <!-- <v-list-item-avatar
      tile
      size="80"
      color="grey"
    ></v-list-item-avatar> -->
  </v-list-item>

  <v-row class="pa-0 ma-0">
    <v-col class="pa-0 ma-0">
      <v-alert
        tile border="bottom"
        :colored-border="show_toggle"
        class="text-center mb-0"
        style="cursor: pointer; align-text: center; color: #1976D2"
        @click="show_toggle=true"
      >
      <p class="text-center ma-0 pa-0" style="cursor: pointer; align-text: center; color: black">Upcoming Reservations</p>
      </v-alert>
    </v-col>
    <v-col class="pa-0 ma-0">
      <v-alert
        tile border="bottom"
        :colored-border="!show_toggle"
        class="text-center mb-0"
        style="cursor: pointer; align-text: center; color: #1976D2"
        @click="show_toggle=false"
      >
      <p class="text-center ma-0 pa-0" style="cursor: pointer; align-text: center; color: black">Previous Reservations</p>
      </v-alert>
    </v-col>
  </v-row>


  <div v-if="loaded">
  <!-- UPCOMING -->
  <div v-if="future.length>0 && show_toggle==1">
    <v-list two-line class="py-0">
      <v-list-item-group multiple>
        <template v-for="(item, ind) in future">
          <v-list-item :key="item.id" inactive :ripple="false">
            <v-list-item-content>
              <v-list-item-title v-text="item.name +' on Court ' + item.category"></v-list-item-title>
              <v-list-item-subtitle class="text--primary" v-text="item.date + ' (' + item.start + ' - ' + item.end + ')'"></v-list-item-subtitle>
              <v-list-item-subtitle v-text="item.ppts"></v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-list-item-action-text>


                <!-- <v-icon v-if="item.host_id == session_data.id" @click="open_edit_dialog(item)">mdi-pencil</v-icon>
                <v-icon v-else @click="open_leave_dialog(item)">mdi-delete</v-icon> -->


              </v-list-item-action-text>
              <!-- <v-list-item-action-text v-text="item.timeUntil"></v-list-item-action-text> -->
            </v-list-item-action>
          </v-list-item>
          <v-divider
            v-if="ind < future.length - 1"
            :key="ind"
            class="ma-0 pa-0"
          ></v-divider>
        </template>
      </v-list-item-group>
    </v-list>
  </div>
  <div v-else-if="show_toggle==1" align="center">
    <p class="pa-4 mt-3">No upcoming reservations
    <a href="/home#/reserve_a_court" > Reserve A Court!</a></p>
  </div>
  <!-- PREVIOUS -->
  <div v-else-if="previous.length>0 && show_toggle==0">
    <v-list two-line class="py-0">
      <v-list-item-group multiple>
        <template v-for="(item, ind) in previous">
          <v-list-item :key="item.id" inactive :ripple="false">
            <v-list-item-content>
              <v-list-item-title v-text="item.name +' on Court ' + item.category"></v-list-item-title>
              <v-list-item-subtitle class="text--primary" v-text="item.date + ' (' + item.start + ' - ' + item.end + ')'"></v-list-item-subtitle>
              <v-list-item-subtitle v-text="item.ppts"></v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action>
              <v-list-item-action-text v-text="item.timeSince"></v-list-item-action-text>
            </v-list-item-action>
          </v-list-item>
          <v-divider
            v-if="ind < previous.length - 1"
            :key="ind"
            class="ma-0 pa-0"
          ></v-divider>
        </template>
      </v-list-item-group>
    </v-list>
  </div>
  <div v-else-if="show_toggle==0" align="center">
    <p class="pa-4 mt-3">No previous reservations</p> 
  </div>
  </div>
  
  </v-card>



  <v-dialog
  transition="slide-x-transition"
  v-model="edit_event_dialog"
  content-class="custom-dialog"
  max-width="600"
  >
  <v-card class="mx-auto">
    <v-toolbar color="primary" dark>
      <v-toolbar-title>Edit Reservation</v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      {{ selected_event }}
    </v-card-text>
    <v-card-text>
      ok
    </v-card-text>
    <v-card-text>
      ok
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn>Save</v-btn>
      <v-btn @click="edit_event_dialog=false">Close</v-btn>
    </v-card-actions>
  </v-card>
  </v-dialog>


  <!-- <v-card class="mx-auto" max-width="600">
    <v-toolbar color="primary" dark>
      <v-toolbar-title>Upcoming Reservations</v-toolbar-title>
    </v-toolbar>
    <div v-if="future.length>0">
      <v-list two-line class="py-0">
        <v-list-item-group multiple>
          <template v-for="(item, ind) in future">
            <v-list-item :key="item.id">
              <v-list-item-content>
                <v-list-item-title v-text="item.name +' on Court ' + item.category"></v-list-item-title>
                <v-list-item-subtitle class="text--primary" v-text="item.date + ' (' + item.start + ' - ' + item.end + ')'"></v-list-item-subtitle>
                <v-list-item-subtitle v-text="item.ppts"></v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-list-item-action-text v-text="item.timeUntil"></v-list-item-action-text>
                <v-icon></v-icon>
              </v-list-item-action>
            </v-list-item>
            <v-divider
              v-if="ind < future.length - 1"
              :key="ind"
              class="ma-0 pa-0"
            ></v-divider>
          </template>
        </v-list-item-group>
      </v-list>
    </div>
    <div v-else align="center">
      <p class="pa-4">No upcoming reservations
      <a href="/home#/reserve_a_court" > Reserve A Court!</a></p>
    </div>
  </v-card>

  <v-card class="mx-auto my-5" max-width="600">
    <v-toolbar color="primary" dark>
      <v-toolbar-title>Previous Reservations</v-toolbar-title>
    </v-toolbar>
    <div v-if="previous.length>0">
      <v-list two-line class="py-0">
        <v-list-item-group active-class="primary--text" multiple>
          <template v-for="(item, ind) in previous">
            <v-list-item :key="item.id">
              <v-list-item-content>
                <v-list-item-title v-text="item.name +' on Court ' + item.category"></v-list-item-title>
                <v-list-item-subtitle class="text--primary" v-text="item.date + ' (' + item.start + ' - ' + item.end + ')'"></v-list-item-subtitle>
                <v-list-item-subtitle v-text="item.ppts"></v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-list-item-action-text v-text="item.timeSince"></v-list-item-action-text>
                <v-icon></v-icon>
              </v-list-item-action>
            </v-list-item>
            <v-divider
              v-if="ind < previous.length - 1"
              :key="ind"
              class="ma-0 pa-0"
            ></v-divider>
          </template>
        </v-list-item-group>
      </v-list>
    </div>
    <div v-else align="center">
      <p class="pa-4">No previous reservations</p> 
      Welcome to the Racquet Club of St. Petersburg's <br> 
      Tennis Court Reservation System</p>
    </div>
  </v-card>  -->

        
</v-container>
</template>

<script>
import moment from 'moment-timezone'

export default {
  props: ['session_data'],

  data: () => ({
    future: null,
    previous: null,

    edit_event_dialog: false,
    selected_event: null,

    current_time: null,
    loaded: false,
    show_toggle: true,

    // message: false,
    // message_text: '',
    // timeout: 3000,      
  }),

  created () {
    this.reloadPage();    
  },
  // computed: {
    
  // },
  // watch: {

  // },

  methods: {
    reloadPage(){
      this.current_time = moment().tz('America/New_York').valueOf()
      this.getReservations()
    },
    open_edit_dialog(event_details){
      this.selected_event = event_details
      this.edit_event_dialog = true
    },
    // open_delete_dialog(id){
    //   this.delete_id = id
    //   this.areyousure=true;
    // },
    // deleteFinal(del_event){
    //   if(del_event){
    //       axios.delete('api/reservation/'+this.delete_id)
    //       .then((response) => {
    //           console.log(response);
    //           this.message_text = "Event Deleted"
    //           this.message = true
    //           this.getReservations()
    //           this.areyousure = false
    //       }, (error) => {
    //           console.log(error);
    //       });
    //   } 
    // },
    getReservations(){
      this.loaded = false
      console.log(this.session_data)
      axios.get('api/getUserReservations/'+this.session_data.id+'/'+this.current_time)
      .then(response => {
        console.log(response)

        this.future = response.data.future
        this.previous = response.data.previous

        for(var i = 0; i<this.future.length; i++){
          // this.future[i].timeUntil = moment(this.future[i].start).fromNow()
          if(this.future[i].start < this.current_time && this.future[i].end > this.current_time){
            this.future[i].timeUntil = "Now"
          } else {
            this.future[i].timeUntil = moment(this.future[i].start).fromNow()
          }
          this.future[i].date = moment(this.future[i].date).format('MMM Do YYYY')
          this.future[i].start = moment(this.future[i].start).format('h:mm a')
          this.future[i].end = moment(this.future[i].end).format('h:mm a')
        }

        for(var i = 0; i<this.previous.length; i++){
          this.previous[i].timeSince = moment(this.previous[i].end).fromNow()
          this.previous[i].date = moment(this.previous[i].date).format('MMM Do YYYY')
          this.previous[i].start = moment(this.previous[i].start).format('h:mm a')
          this.previous[i].end = moment(this.previous[i].end).format('h:mm a')
        }

        this.loaded = true


      })
      .catch(error => {
        console.log(error);
      })
    }
  },
};
</script>

<style>
.custom-dialog.v-dialog{
  margin: 0px;
}
</style>