<template>
  <v-container class="pa-6 ma-auto" fluid>
    <div class="d-flex flex-column justify-space-between align-center">
      <v-img src="/public/images/TennisCourtMap.png" alt="Map of the Tennis Courts" style="width:70%"></v-img>
    </div>
    <v-container>
      <h3>Welcome</h3>

      <p>
        As a member, you will be able to make a reservation a minimum of 48 hours ahead.
        After you find a reservation, you can set whether the game will be singles or doubles.
        Bringing along a guest? Please follow the Guest Play policy and register the guest's name
        as part of the reservation. If the Guest has already played in the current calendar month, 
        the reservation will be rejected.
      </p>

      
    </v-container>
    <div
      v-bind:style="{
        backgroundColor: '#E0E0E0',
      }"
    >
    <v-container>
      <v-form
        ref="form"
        v-model="valid"
        lazy-validation
      >
        <h4>Find Available Courts</h4>
        <v-row align="start" justify="center"> 
          <v-col align="center" cols="3">
            <v-select
                :items="courtType"
                label="Select Court Type"
                v-model="selectedCourt"
                :rules="[v => !!v || 'Court Type is required']"
                background-color="grey lighten-5"
                required
                outlined
              ></v-select>
          </v-col>
          <v-col align="center" cols="2">
            <v-menu
                v-model="menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="auto"
                required
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="selectedDate"
                    label="Date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    clearable
                    append-icon="mdi-calendar pr-4"
                    background-color="grey lighten-5"
                    required
                    :rules="[v => !!v || 'Date is required']"
                    @click="log"
                    outlined
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="selectedDate"
                  @input="menu = false"
                  required
                  :min="date2days"
                ></v-date-picker>
              </v-menu>
          </v-col>
          <v-col align="center" cols="2">
            <v-select
                :items="startTime"
                label="Start Time"
                v-model="selectedStartTime"
                :rules="[v => !!v || 'Start Time is required']"
                background-color="grey lighten-5"
                required
                outlined
              ></v-select>
          </v-col>
          <v-col align="center" cols="2">
            <v-select
                :items="duration"
                label="Duration"
                v-model="selectedDuration"
                :rules="[v => !!v || 'Court Type is required']"
                background-color="grey lighten-5"
                required
                outlined
              ></v-select>
          </v-col>
          <v-col align="start" cols="2" justify="start">
            <v-btn 
              x-large 
              color="primary"
              @click="find()"
              :disabled="!valid"
            >Find</v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-container>
    </div>

    <v-list v-if="seen">
        <v-subheader><b>You Selected:</b> {{selectedCourt}} on {{selectedDate}} at {{selectedStartTime}} for {{selectedDuration}} </v-subheader>
        <v-list-item-group
            v-model="selectedItem"
        >
            <v-list-item
                v-for="(reserve, i) in foundReservations"
                :key="i"
            >
                <v-col class="d-flex ma-0 pa-0" cols="12" sm="3">
                    <v-list-item-title v-text="reserve.startTime + ' - ' + reserve.endTime + ' AM'"></v-list-item-title>
                </v-col>
                <v-col class="d-flex ma-0 pa-0" cols="12" sm="3">
                    <v-list-item-title v-text="reserve.courtType + ' ' + reserve.courtNumber"></v-list-item-title>
                </v-col>
                <v-spacer></v-spacer>
                <v-col class="d-flex ma-0 pa-0 justify-end" cols="12" sm="4">
                    <v-btn class="text-right" :right="true">
                        Reserve
                    </v-btn>
                </v-col>
            </v-list-item>
        </v-list-item-group>
    </v-list>

    <v-data-table
      :headers="headers"
      :items="reservations"
      sort-by="calories"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>My Reservations</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on">
                New Item
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field
                        v-model="editedItem.title"
                        label="Title"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                      <v-menu
                        v-model="menu"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="editedItem.date"
                            label="Date"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          ></v-text-field>
                        </template>
                        <v-date-picker
                          v-model="editedItem.date"
                          @input="menu = false"
                        ></v-date-picker>
                      </v-menu>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field
                        v-model="editedItem.court"
                        label="Court"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue darken-1" text @click="save"> Save </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="headline"
                >Are you sure you want to delete this item?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDelete"
                  >Cancel</v-btn
                >
                <v-btn color="blue darken-1" text @click="deleteItemConfirm"
                  >OK</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary"> Reset </v-btn>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import ReservationList from "./ReservationList";
export default {
  components: { ReservationList },

  props: ["reservations"],

  data: () => ({
    startTime: ["6:30 AM"],
    valid: false,
    courtType: ["Soft Court", "Hard Court"],
    dialog: false,
    dialogDelete: false,
    duration: ["30 mins", "1 hr", "1-1/2 hr", "2 hrs", "2-1/2 hrs"],
    menu: false,
    date: new Date().toISOString().substr(0, 10),
    date2days: new Date(new Date().setDate(new Date().getDate() + 2)).toISOString().substr(0, 10),
    headers: [
      {
        text: "Title",
        align: "start",
        sortable: false,
        value: "title",
      },
      { text: "Date", value: "date" },
      { text: "Court", value: "court" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      title: "",
      date: "",
      court: "",
    },
    defaultItem: {
      title: "",
      date: "",
      court: "",
    },

    created () {
        this.getReservations();    
    },
    // dateWeek() {
    //     console.log(new Date(new Date().setDate(new Date().getDate() + i)).toISOString().substr(0, 10))
    //     return new Date(new Date().setDate(new Date().getDate() + i)).toISOString().substr(0, 10);
    // }
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'New Reservation' : 'Edit Reservation'
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },

  created() {},

  methods: {
    getReservations() {
        axios.get('api/reservations')
            .then(response => {
                this.reservations = response.data
            })
            .catch(error => {
                console.log(error);
            })

    },
    log() {
        console.log(this.date, this.dateWeek);
    },
    find() {
        if (this.$refs.form.validate()) {
            this.seen = !this.seen;
            this.selectedReservation.selectedCourt = this.selectedCourt;
            this.selectedReservation.selectedDate = this.selectedDate;
            this.selectedReservation.selectedStartTime = this.selectedStartTime;
            this.selectedReservation.selectedDuration = this.selectedDuration;
            console.log(this.date, this.dateWeek);
        }
    },
    editItem(item) {
      this.editedIndex = this.reservations.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.reservations.indexOf(item);
      this.editedItem = Object.assign({}, item);
      console.log(this.editedIndex);
      console.log(item.id);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.reservations.splice(this.editedIndex, 1);
      axios.delete("api/reservation/" + this.editedItem.id);
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if (this.editedIndex > -1) {
        Object.assign(this.reservations[this.editedIndex], this.editedItem);

        let item = JSON.parse(JSON.stringify(this.editedItem));

        let editReservationPayload = {
          item,
        };

        axios.put("api/reservation/" + item.id, editReservationPayload);
      } else {
        let item = JSON.parse(JSON.stringify(this.editedItem));

        let newCompTimePayload = {
          item,
        };

        // console.log(item)

        axios.post("api/reservation/store", newCompTimePayload);
      }
      this.close();

      this.$emit("refresh-list");
    },
  },
};
</script>
