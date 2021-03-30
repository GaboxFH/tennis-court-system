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
                        <v-data-iterator
                            :items="items"
                            :items-per-page.sync="itemsPerPage"
                            :page.sync="page"
                            :search="date"
                            :sort-by="sortBy.toLowerCase().replace(/\s+/g, '')"
                            :sort-desc="sortDesc"
                            group-by="type"
                            hide-default-footer
                            >
                            <template v-slot:header>
                                <v-toolbar
                                dark
                                color="blue darken-3"
                                class="mb-1"
                                >
                                    <v-menu
                                        ref="menu"
                                        v-model="menu"
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
                                                @click="menu = false"
                                            >Cancel</v-btn>
                                            <v-btn
                                                text
                                                color="primary"
                                                @click="$refs.menu.save(date)"
                                            >OK</v-btn>
                                        </v-date-picker>
                                    </v-menu>
                                <template v-if="$vuetify.breakpoint.mdAndUp">
                                    <v-spacer></v-spacer>
                                    <v-select
                                    v-model="sortBy"
                                    flat
                                    solo-inverted
                                    hide-details
                                    :items="keys"
                                    prepend-inner-icon="mdi-magnify"
                                    label="Sort by"
                                    ></v-select>
                                    <v-spacer></v-spacer>
                                    <v-btn-toggle
                                    v-model="sortDesc"
                                    mandatory
                                    >
                                    <v-btn
                                        large
                                        depressed
                                        color="blue"
                                        :value="false"
                                    >
                                        <v-icon>mdi-arrow-up</v-icon>
                                    </v-btn>
                                    <v-btn
                                        large
                                        depressed
                                        color="blue"
                                        :value="true"
                                    >
                                        <v-icon>mdi-arrow-down</v-icon>
                                    </v-btn>
                                    </v-btn-toggle>
                                </template>
                                </v-toolbar>
                            </template>

                            <template v-slot:default="props">
                                <v-row>
                                <v-col
                                    v-for="item in props.items"
                                    :key="item.courtnumber"
                                    cols="12"
                                    sm="6"
                                    md="4"
                                    lg="3"
                                >
                                    <v-card>
                                    <v-card-title class="subheading font-weight-bold">
                                        Court {{ item.courtnumber }}
                                    </v-card-title>

                                    <v-divider></v-divider>

                                    <v-list dense>
                                        <v-list-item
                                        v-for="(key, index) in filteredKeys"
                                        :key="index"
                                        >
                                        <v-list-item-content :class="{ 'blue--text': sortBy === key }">
                                            {{ key }}:
                                        </v-list-item-content>
                                        <v-list-item-content
                                            class="align-end"
                                            :class="{ 'blue--text': sortBy === key }"
                                        >
                                            <div v-if="key.toLowerCase().replace(/\s+/g, '') == 'totaltime'">
                                                {{ item[key.toLowerCase().replace(/\s+/g, '')] }} hrs
                                            </div>
                                            <div v-else>
                                                {{ item[key.toLowerCase().replace(/\s+/g, '')] }} 
                                            </div>
                                            <!-- {{ item[key.toLowerCase().replace(/\s+/g, '')] }}  -->
                                        </v-list-item-content>
                                        </v-list-item>
                                    </v-list>
                                    </v-card>
                                </v-col>
                                </v-row>
                            </template>

                            <template v-slot:footer>
                                <v-row
                                class="mt-2"
                                align="center"
                                justify="center"
                                >
                                <span class="grey--text">Items per page</span>
                                <v-menu offset-y>
                                    <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        dark
                                        text
                                        color="primary"
                                        class="ml-2"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        {{ itemsPerPage }}
                                        <v-icon>mdi-chevron-down</v-icon>
                                    </v-btn>
                                    </template>
                                    <v-list>
                                    <v-list-item
                                        v-for="(number, index) in itemsPerPageArray"
                                        :key="index"
                                        @click="updateItemsPerPage(number)"
                                    >
                                        <v-list-item-title>{{ number }}</v-list-item-title>
                                    </v-list-item>
                                    </v-list>
                                </v-menu>

                                <v-spacer></v-spacer>

                                <span
                                    class="mr-4
                                    grey--text"
                                >
                                    Page {{ page }} of {{ numberOfPages }}
                                </span>
                                <v-btn
                                    fab
                                    dark
                                    color="blue darken-3"
                                    class="mr-1"
                                    @click="formerPage"
                                >
                                    <v-icon>mdi-chevron-left</v-icon>
                                </v-btn>
                                <v-btn
                                    fab
                                    dark
                                    color="blue darken-3"
                                    class="ml-1"
                                    @click="nextPage"
                                >
                                    <v-icon>mdi-chevron-right</v-icon>
                                </v-btn>
                                </v-row>
                            </template>
                            </v-data-iterator>
                    </div>
                </div>
                 <div class="card">
                    <div class="card-header">Members Use Report</div>

                    <div class="card-body">
                        <p>This report will be a montly listing of how much time (time slot count * 2) each member has played.</p>
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
                                </v-card-title>
                                <v-data-table
                                    :headers="headers"
                                    :items="computedPlay"
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
export default {
    data () {
      return {
        // Variables for Court Utilization Report
        date: new Date().toISOString().substr(0, 7),
        menu: false,
        modal: false,
        menu2: false,
        itemsPerPageArray: [4, 8, 12],
        search: '',
        filter: {},
        sortDesc: false,
        page: 1,
        itemsPerPage: 4,
        sortBy: 'Court Number',
        keys: [
          'Court Number',
          'Time Slots Used',
          'Total Time',
          'Type',
          'Date Reserved'
        ],
        items: [
          {
              courtnumber: 1,
              timeslotsused: 24,
              totaltime: 12,
              type: "hard",
              datereserved: "2021-01"
          },
          {
              courtnumber: 2,
              timeslotsused: 12,
              totaltime: 6,
              type: "hard",
              datereserved: "2021-01"
          },
          {
              courtnumber: 3,
              timeslotsused: 36,
              totaltime: 18,
              type: "hard",
              datereserved: "2021-01"
          },
          {
              courtnumber: 4,
              timeslotsused: 40,
              totaltime: 20,
              type: "hard",
              datereserved: "2021-02"
          },
          {
              courtnumber: 5,
              timeslotsused: 20,
              totaltime: 10,
              type: "hard",
              datereserved: "2021-02"
          },
          {
              courtnumber: 6,
              timeslotsused: 40,
              totaltime: 20,
              type: "hard",
              datereserved: "2021-02"
          },
          {
              courtnumber: 7,
              timeslotsused: 10,
              totaltime: 5,
              type: "hard",
              datereserved: "2021-03"
          },
          {
              courtnumber: 8,
              timeslotsused: 30,
              totaltime: 15,
              type: "hard",
              datereserved: "2021-03"
          },
          {
              courtnumber: 9,
              timeslotsused: 50,
              totaltime: 25,
              type: "hard",
              datereserved: "2021-03"
          },
          {
              courtnumber: 10,
              timeslotsused: 8,
              totaltime: 4,
              type: "hard",
              datereserved: "2021-04"
          },
          {
              courtnumber: 11,
              timeslotsused: 22,
              totaltime: 11,
              type: "hard",
              datereserved: "2021-04"
          },
          {
              courtnumber: 12,
              timeslotsused: 13,
              totaltime: 6.5,
              type: "hard",
              datereserved: "2021-04"
          },
          {
              courtnumber: 13,
              timeslotsused: 0,
              totaltime: 0,
              type: "hard",
              datereserved: "2022-01"
          },
          {
              courtnumber: 14,
              timeslotsused: 9,
              totaltime: 4.5,
              type: "hard",
              datereserved: "2022-02"
          },
          {
              courtnumber: 15,
              timeslotsused: 23,
              totaltime: 11.5,
              type: "soft",
              datereserved: "2022-03"
          },
          {
              courtnumber: 16,
              timeslotsused: 7,
              totaltime: 3.5,
              type: "soft",
              datereserved: "2022-04"
          },
          {
              courtnumber: 17,
              timeslotsused: 18,
              totaltime: 9,
              type:"soft",
              datereserved: "2022-05"
          },
        ],
        // Variables for Member Report
        dateMember: new Date().toISOString().substr(0, 7),
        menuMember: false,
        searchMembers:'',
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
        data: [
            {
                name: 'Jim Jam',
                duration: 9.5,
                yearMonth: "2021-01"
            },
             {
                name: 'Jimmy Jammadone',
                duration: 2.5,
                yearMonth: '2021-01'
            },
             {
                name: 'Jimbo Jambrowski',
                duration: 10.5,
                yearMonth: '2021-01'
            },
             {
                name: 'Jam Jimmyson',
                duration: 9.5,
                yearMonth: '2021-01'
            },
            {
                name: 'Jim Jam',
                duration: 1,
                yearMonth: '2021-02'
            },
             {
                name: 'Jimmy Jammadone',
                duration: 20.5,
                yearMonth: '2021-02'
            },
             {
                name: 'Jimbo Jambrowski',
                duration: 11,
                yearMonth: '2021-02'
            },
             {
                name: 'Jam Jimmyson',
                duration: 9,
                yearMonth: '2021-02'
            },
            {
                name: 'Jim Jam',
                duration: 90,
                yearMonth: '2021-03'
            },
             {
                name: 'Jimmy Jammadone',
                duration: 10,
                yearMonth: '2021-03'
            },
             {
                name: 'Jimbo Jambrowski',
                duration: 14.5,
                yearMonth: '2021-03'
            },
             {
                name: 'Jam Jimmyson',
                duration: 4.5,
                yearMonth: '2021-03'
            },
        ],
        dataF: [
            {
                name: 'Jim Jam',
                duration: 9.5,
                yearMonth: "2021-01"
            },
             {
                name: 'Jimmy Jammadone',
                duration: 2.5,
                yearMonth: '2021-01'
            },
             {
                name: 'Jimbo Jambrowski',
                duration: 10.5,
                yearMonth: '2021-01'
            },
             {
                name: 'Jam Jimmyson',
                duration: 9.5,
                yearMonth: '2021-01'
            },
            {
                name: 'Jim Jam',
                duration: 1,
                yearMonth: '2021-02'
            },
             {
                name: 'Jimmy Jammadone',
                duration: 20.5,
                yearMonth: '2021-02'
            },
             {
                name: 'Jimbo Jambrowski',
                duration: 11,
                yearMonth: '2021-02'
            },
             {
                name: 'Jam Jimmyson',
                duration: 9,
                yearMonth: '2021-02'
            },
            {
                name: 'Jim Jam',
                duration: 90,
                yearMonth: '2021-03'
            },
             {
                name: 'Jimmy Jammadone',
                duration: 10,
                yearMonth: '2021-03'
            },
             {
                name: 'Jimbo Jambrowski',
                duration: 14.5,
                yearMonth: '2021-03'
            },
             {
                name: 'Jam Jimmyson',
                duration: 4.5,
                yearMonth: '2021-03'
            },
        ],// Variables used for Rain Outs
        dialog: false,
        playtime: ''
      }
    },
    created () {
        this.getReservations()
    },
    computed: {
        computedPlay () {
            return this.playtime
        },
        numberOfPages () {
            return Math.ceil(this.items.length / this.itemsPerPage)
        },
        filteredKeys () {
            return this.keys.filter(key => key !== 'Name')
        },
    },
    methods: {
        
        nextPage () {
            if (this.page + 1 <= this.numberOfPages) this.page += 1
        },
        formerPage () {
            if (this.page - 1 >= 1) this.page -= 1
        },
        updateItemsPerPage (number) {
            this.itemsPerPage = number
        },
        filterDate () {
            if (this.dateMember !== undefined) {
                this.dataF = this.data.filter((item) => item.yearMonth == this.dateMember)
            }
        },
        getReservations() {
        axios.get('api/member_play'+'/1')
            .then(response => {
                this.playtime = response.data
                console.log("this.playtime")
                console.log(this.playtime)
            })
            .catch(error => {
                console.log(error)
            })
        }
    },
}

</script>
`