<template>
    <div class="pa-6 ma-auto" fluid>
        <div class="pb-5 row justify-content-center">
            <img src="../../images/TennisCourtMap.png" alt="Map of the Tennis Courts" style="width:80%; border: 5px solid #333;"/>
        </div>
        <v-card>
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
                        v-model="date_input_display"
                    >{{ displayDate(new Date(date_input+'T00:00')) }}</v-text-field>
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
        <div v-if="search_input=='Start Time'">
            <div v-for="(n, ind) in avail_temp" v-bind:key="n.court">
            <div v-if="n[0] || n[1] || n[2] || n[3]">
            <v-divider></v-divider>
            <v-row>
                <v-col cols="auto">
                    <v-card-subtitle class="ma-0 pa-0">Court {{ind}}</v-card-subtitle>
                    <v-chip-group
                        active-class="primary--text"
                        column
                    >
                        <div
                        v-for="(t,index) in n"
                        :key="t.start_times"
                        >
                        <div v-if="t">
                            <v-chip>
                            {{ dur_type[index].show }}
                            </v-chip>
                        </div>
                        </div>
                    </v-chip-group>
                </v-col>
            </v-row>
            </div>
            </div>
        </div>
        <div v-else>
            <div v-for="(n, ind) in avail_temp" v-bind:key="n.court">
            <div v-if="n[0] || n[1] || n[2] || n[3] || n[4] || n[5] || n[6] || n[7] || n[8] || n[9] 
            || n[10] || n[11] || n[12] || n[13] || n[14] || n[15] || n[16] || n[17] || n[18] || n[19] 
            || n[20] || n[21] || n[22] || n[23]">
            <v-divider></v-divider>
            <v-row>
                <v-col cols="auto">
                    <v-card-subtitle class="ma-0 pa-0">Court {{ind}}</v-card-subtitle>
                    <v-chip-group
                        active-class="primary--text"
                        column
                    >
                    <div
                    v-for="(t,index) in n"
                    :key="t.start_times"
                    >
                    <div v-if="t">
                        <v-chip>
                        {{start_type[index].show}}
                        </v-chip>
                    </div>
                    </div>
                    </v-chip-group>
                </v-col>
            </v-row>
            </div>
            </div>
        </div>
        <!-- {{ this.avail_temp.data }} <br>
        {{ this.avail_temp }} -->
        <!-- <div v-for="n in avail_courts" v-bind:key="n.court">
            <v-divider class="ma-0 pa-0"></v-divider>
            <v-row>
                <v-spacer></v-spacer>
                <v-col cols="auto">
                    <v-card-subtitle class="ma-0 pa-0">Court {{n.court}}</v-card-subtitle>
                    <v-chip-group
                        active-class="primary--text"
                        column
                    >
                        <v-chip
                        v-for="t in n.start_times"
                        :key="t.start_times"
                        >
                        {{ t }}
                        </v-chip>
                    </v-chip-group>
                </v-col>
                <v-spacer></v-spacer>
            </v-row>
        </div> -->
        
        </v-card>

    </div>
</template>

<script>
    export default {
        // props: ['reservations','users','session_data'],

    data: () => ({
        loading: false,
        search_input: "Start Time",
        search_type: ["Duration","Start Time"],
        court_input: "Clay Courts",
        court_type: ["Clay Courts","Hard Courts"],
        date_input: new Date().toISOString().substr(0, 10),
        date_input_display: new Date().toISOString().substr(0, 10),
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
            {show:"7:30 pm", val:"19:30:00"}
        ],
        // start_type: [{show:"8:00 am",val:"08:00:00"},,"8:30 am","9:00 am","9:30 am","10:00 am","10:30 am","11:00 am","11:30 am","12:00 pm","12:30 pm","1:00 pm","1:30 pm","2:00 pm","2:30 pm","3:00 pm","3:30 pm","4:00 pm","4:30 pm","5:00 pm","5:30 pm","6:00 pm","6:30 pm","7:00 pm","7:30 pm"],
        dur_input: 2,
        dur_type: [
            {show:"30 mins",  val: 1},
            {show:"1 Hour",   val: 2},
            {show:"1.5 Hours",val: 3},
            {show:"2 Hours",  val: 4}
        ],
        // dur_type: ["30 mins","1 Hour","1.5 Hours","2 Hours"],
        avail_courts: [
            {
                court: '1',
                start_times: ["8:00 am - 8:30 am (30 mins)","8:00 am - 9:00 am (1 Hour)","8:00 am - 9:50 am (1.5 Hours)","8:00 am - 10:00 am (2 Hours)"],
            },
            {
                court: '2',
                start_times: ["8:00 am","8:30 am","9:00 am","9:30 am","10:00 am","10:30 am","11:00 am","11:30 am","12:00 pm","12:30 pm","1:00 pm","1:30 pm","2:00 pm","2:30 pm","3:00 pm","3:30 pm","4:00 pm","4:30 pm","5:00 pm","5:30 pm","6:00 pm","6:30 pm","7:00 pm","7:30 pm"],
            },
            {
                court: '3',
                start_times: ["8:00 am - 8:30 am"],
            },
        ],
        avail_temp: ''
    }),
    created () {
        // console.log(new Date())
        // console.log(new Date().toLocaleString().substr(0, 10))
        // console.log(new Date().toISOString().substr(0, 10))

        this.date_input_display=this.displayDate(new Date(this.date_input+'T00:00'))
        // this.$emit('refresh-schedule')
        // this.$emit('refresh-users')
    },
    watch: {
        date_input(val){
            this.date_input_display=this.displayDate(new Date(val+'T00:00'))
        }
    },
    computed: {
        // computed_courts(){
        //     return this.avail_courts
        // },
        // computed_dur(){
        //     if(this.start_input==null){
        //         return this.dur_type
        //     } else if(this.start_input=="6:30 pm"){
        //         return [this.dur_type[0]]
        //     } else if(this.start_input=="7:00 pm"){
        //         return [this.dur_type[0],this.dur_type[1]]
        //     } else if(this.start_input=="7:30 pm"){
        //         return [this.dur_type[0],this.dur_type[1],this.dur_type[2]]
        //     }
        //     return this.dur_type
        // }
    },
    methods: {
        showEvents() {
            this.loading = true
            this.avail_temp = ''
            console.log("HI!")
            console.log(this.search_input)
            //4 hours added to account for timezone diff seems sketch
            const date_input_milliseconds = new Date(this.date_input).getTime()+4*60*60*1000 
            var param = ""
            if(this.search_input == "Start Time"){
                param=this.start_input
            } else {
                param=this.dur_input
            }
            console.log(param)

            axios.get('api/avail_reservations/'+date_input_milliseconds+'/'+param)
            .then(response => {
                console.log(response)
                this.avail_temp=response.data
                this.loading=false
            })
            .catch(error => {
                console.log(error)
                this.loading=false
            })
        },
        nth (d) {
            return d > 3 && d < 21
            ? 'th'
            : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
        },
        displayDate (calDate) {
            if(calDate==null){
                calDate = new Date().toISOString().substr(0, 10)
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
        },
    }

        
    }
</script>
