<template>
  <div class="row justify-content-center">
  <div class="col-md-10">
  <div class="card">
    <div class="card-header blue darken-4 text-white">
      <v-toolbar-title>Reservations</v-toolbar-title>
    </div>
    
    <v-container class="pa-0 ma-auto" fluid>
    <div>
      <v-list two-line>
        <v-list-item-group multiple>
          <template v-for="(n,ind) in myReservations" class="pa-0 ma-0">
            <v-divider
            v-if="ind!=0" 
            :key="ind"
            ></v-divider>
            <v-list-item :key="n.id">

              <v-list-item-content>
                <v-list-item-title>{{n.name}} on Court {{n.category}}</v-list-item-title>

                <v-list-item-subtitle class="text--primary">{{new Date(n.start)}}</v-list-item-subtitle>
                <v-list-item-subtitle>Duration {{n.duration}}</v-list-item-subtitle>
              
              </v-list-item-content>
            
            <v-list-item-action>
              <v-icon
                color="yellow darken-3"
                @click="open_delete_dialog(n.id)"
              >
                mdi-delete
              </v-icon>
            </v-list-item-action>
            </v-list-item>
          </template>
        </v-list-item-group>
      </v-list>

      
      
      
    </div>
    <v-dialog
        v-model="areyousure"
        persistent
        max-width="450"
    >
        <v-card>
            <v-card-title>
                Cancel Reservation
            </v-card-title>
            <v-card-text>
                Are you sure you want to cancel this reservation?
            </v-card-text>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="deleteFinal(true)">Yes</v-btn>
            <v-btn color="blue darken-1" text @click="deleteFinal(false)">No</v-btn>
        </v-card-actions>
        </v-card>
    </v-dialog>
        
    </v-container>


  </div>
  </div>
  </div>
</template>

<script>
export default {
  props: ['session_data'],

  data: () => ({
    myReservations: null,
    areyousure: false,
    message: false,
    message_text: '',
    timeout: 3000,      
  }),

  created () {
    this.getReservations();    
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
    this.getReservations()    
  },

  methods: {
    open_delete_dialog(id){
      this.delete_id = id
      this.areyousure=true;
    },
    deleteFinal(del_event){
      if(del_event){
          axios.delete('api/reservation/'+this.delete_id)
          .then((response) => {
              console.log(response);
              this.message_text = "Event Deleted"
              this.message = true
              this.getReservations()
              this.areyousure = false
          }, (error) => {
              console.log(error);
          });
      } 
    },
    getReservations(){
      console.log(this.session_data)
      axios.get('api/getUserReservations/'+this.session_data.id)
      .then(response => {
        console.log(response)
        this.myReservations = response.data
      })
      .catch(error => {
        console.log(error);
      })
    }
  },
};
</script>
