
<template>
  
  <v-row class="fill-height">
    <v-col>
      {{ this.appointments }}
      <v-sheet height="64">
        <v-toolbar
          flat
        >
          <v-btn
            outlined
            class="mr-4"
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
          <v-toolbar-title v-if="$refs.calendar">
            {{ $refs.calendar.title }} 
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <div v-if="fullScreen==false">
          <v-btn-toggle
            class="mx-6"
            v-model="scheduleView"
            rounded
            dense
            mandatory
            btn-toggle-btn-height="170px"
          >
            <v-btn>1-8</v-btn>
            <v-btn>9-16</v-btn>
          </v-btn-toggle>
          </div>
        </v-toolbar>
      </v-sheet>
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
          interval-width="50"
          :interval-style="intervalStyle"
          :show-interval-label="showIntervalLabel"
          :categories="computedCategories"
          :events="computerEvents"
          :event-color="getEventColor"
          @change="fetchEvents"
          @click:time-category="newReservation"
          @mousemove:time="extendReservation"

        >
          <template v-slot:day-body="{ date, week }">
            <div
              class="v-current-time"
              :class="{ first: date === week[0].date }"
            ></div>
          </template>
        </v-calendar>
        
      </v-sheet>
    </v-col>
  </v-row>
</template>


<script>
const stylings = {
  default (interval) {
    return undefined
  },
  workday (interval) {
    const inactive = 
      (interval.hour < 10 ||
      interval.hour >= 20) && interval.category.categoryName == 3 
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
  export default {
    data: () => ({
      focus: '',
      appointments: [],
      events: [],
      styleInterval: 'workday',
      scheduleView: 0,
      fullScreen: false,
      // dragEvent: null,
      createEvent: null,
      createStart: null,
      extendOriginal: null,

      startClick: undefined,
      endClick: false,

      colors: ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1'],
      // names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
      categories: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']
    }),
    computed: {
        computedCategories () {
            var categories = []
            if(this.$vuetify.breakpoint.name == 'xs' || this.$vuetify.breakpoint.name == 'sm' || this.$vuetify.breakpoint.name == 'md') {
            this.fullScreen = false
            if(this.scheduleView == 0){
                categories = ['1', '2', '3','4', '5', '6', '7', '8']
            } else {
                categories = ['9', '10', '11', '12', '13', '14', '15', '16']
            }
            }
            else {
            this.fullScreen = true
            categories = ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16']
            }
            // console.log(categories)
            return categories
        },
        computerEvents () {
            const cal_events = []
            for(var i=0; i<this.appointments.length; i++){
                console.log(this.appointments[i])
                cal_events.push({
                    // name: this.appointments[0].user_id---name,
                    name: "Member",
                    start: this.appointments[i].start_datetime,
                    end: this.appointments[i].end_datetime,
                    // color: this.colors[this.rnd(0, this.colors.length - 1)],
                    color: "blue",
                    timed: true,
                    category: this.categories[this.appointments[i].court-1],
                })
            }
            
            // cal_events.push({
            //     // name: this.names[this.rnd(0, this.names.length - 1)],
            //     name: "name",
            //     start: "2021-03-25 10:00:00",
            //     end: "2021-03-25 12:00:00",
            //     // color: this.colors[this.rnd(0, this.colors.length - 1)],
            //     color: "blue",
            //     timed: true,
            //     category: this.categories[1],
            // })
            return cal_events
        },
        intervalStyle () {
            return stylings['workday'].bind(this)
            
            // return stylings[ this.styleInterval ].bind(this)
            // return stylings['workday'].bind(this)
        },
    },
    created () {
        this.getReservations()
        console.log(this.appointments)
    },
    mounted () {
      this.$refs.calendar.checkChange()
    },
    methods: {
      newReservation (tms) {
        if(this.startClick == undefined) {
          const selectedDate = this.roundTime(this.toTime(tms))
          // console.log(new Date(this.roundTime(this.toTime(tms))))
          // console.log(tms.category.categoryName-1)
          // console.log(this.scheduleView)
          // console.log(this.events)
          var timeConflict = false
          for(var i=0; i<this.events.length; i++) {
            if(selectedDate >= this.events[i].start && selectedDate < this.events[i].end && tms.category.categoryName == this.events[i].category){
              timeConflict = true
            }
          }
          if(!timeConflict) {
            this.startClick = selectedDate

            this.createEvent = {
              name: 'New Event',
              color: "red darken-4",
              start: selectedDate,
              end: selectedDate+30*1000*60,
              timed: true,
              category: this.categories[tms.category.categoryName-1],
            }
            
            this.events.push(this.createEvent)
            // this.createEvent = null
          }
        } else {
          if(this.createEvent.category != tms.category.categoryName){
            this.createEvent = null
            this.startClick = undefined
            this.events.pop()
          } else {
            this.createEvent.color = "red"
            // this.createEvent.end = this.createEvent.end+60*1000*60
            console.log("skiz")
            this.createEvent = null
            this.startClick = undefined
          }
        }
        
        // console.log(conflict)
        },
        extendReservation (tms) {
            if(this.startClick){
            // console.log(new Date(this.roundTime(this.toTime(tms))))
            // if(this.createEvent.category == tms.category.categoryName) {
                this.createEvent.end = this.roundTime(this.toTime(tms)+30*1000*60)
            //   console.log("here")
            // } else {
                // this.startClick = false
                // this.events.pop()
            // }
            // console.log(this.createEvent.category)
            }
        },
        // startTime (tms) {
            
        //   const mouse = this.toTime(tms)
        //   console.log("createEvent: ")
        //   // console.log(new Date(mouse))
        //   // console.log(tms.category.categoryName-1)
        //   if (this.dragEvent && this.dragTime === null) {
        //     const start = this.dragEvent.start
        //     console.log("createEvent1: ")
        //     this.dragTime = mouse - start
        //   } else {
            
        //     this.createStart = this.roundTime(mouse)
        //     this.createEvent = {
        //       // name: `Event #${this.events.length}`,
        //       name: 'New Event',
        //       color: "red",
        //       start: this.createStart,
        //       // end: this.createStart,
        //       end: this.createStart+30*1000*60,
        //       timed: true,
        //       category: this.categories[tms.category.categoryName-1],
        //     }
            
        //     this.events.push(this.createEvent)
        //   }
        // },
        // mouseMove (tms) {
        //   const mouse = this.toTime(tms)
            
        //   if (this.dragEvent && this.dragTime !== null) {
        //     const start = this.dragEvent.start
        //     const end = this.dragEvent.end
        //     const duration = end - start
        //     const newStartTime = mouse - this.dragTime
        //     const newStart = this.roundTime(newStartTime)
        //     const newEnd = newStart + duration

        //     this.dragEvent.start = newStart
        //     this.dragEvent.end = newEnd
        //     console.log("mouseMove1: ")
        //   } else if (this.createEvent && this.createStart !== null) {
        //     const mouseRounded = this.roundTime(mouse, false)
        //     const min = Math.min(mouseRounded, this.createStart)
        //     const max = Math.max(mouseRounded, this.createStart)

        //     this.createEvent.start = min
        //     this.createEvent.end = max
        //     console.log("mouseMove2: ")
        //   }
        // },
        // endDrag () {
        //   this.dragTime = null
        //   this.dragEvent = null
        //   this.createEvent = null
        //   this.createStart = null
        //   this.extendOriginal = null
        // },
        // cancelDrag () {
        //   if (this.createEvent) {
        //     if (this.extendOriginal) {
        //       this.createEvent.end = this.extendOriginal
        //     } else {
        //       const i = this.events.indexOf(this.createEvent)
        //       if (i !== -1) {
        //         this.events.splice(i, 1)
        //       }
        //     }
        //   }

        //   this.createEvent = null
        //   this.createStart = null
        //   this.dragTime = null
        //   this.dragEvent = null
        // },
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
        fetchEvents ({ start, end }) {
            const events = []
            // console.log("start " +end.date)
            // const min = new Date(`${start.date}T00:08:00`)
            // const max = new Date(`${end.date}T13:59:59`)
            
            // const days = (max.getTime() - min.getTime()) / 86400000
            // const eventCount = this.rnd(days, days + 20)

            // for (let i = 0; i < 16; i++) {
            //   // const allDay = this.rnd(0, 3) === 0
            
            //   // const firstTimestamp = this.rnd(min.getTime(), max.getTime())
            //   // const firstTimestamp = new Date("2021-03-16T10:30:00").getTime()
            //   // const first = new Date(firstTimestamp - (firstTimestamp % 900000))
            //   const first = new Date("2021-03-25T10:00:00")
            //   first.setHours(first.getHours() + this.rnd(0,6))
            //   // first.setHours(first.getHours() + 4);
            //   // first = first + (1000*60*30)
            //   // const secondTimestamp = this.rnd(2, allDay ? 288 : 8) * 900000
            //   // const secondTimestamp = this.rnd(2, allDay ? 288 : 8) * 900000
            //   // const second = new Date(first.getTime() + secondTimestamp)
            //   const second = new Date(first)
            //   second.setMinutes(first.getMinutes() + 30*this.rnd(1,5));

            //   events.push({
            //     // name: this.names[this.rnd(0, this.names.length - 1)],
            //     name: "name",
            //     start: first,
            //     end: second,
            //     color: this.colors[this.rnd(0, this.colors.length - 1)],
            //     // color: "blue",
            //     timed: true,
            //     category: this.categories[i],
            //   })
            // }
            // console.log(this.appointments)
            this.events = events
        },
        getReservations() {
            axios.get('api/reservations')
            .then(response => {
                this.appointments = response.data
            })
            .catch(error => {
                console.log(error);
            })
        },
        rnd (a, b) {
            return Math.floor((b - a + 1) * Math.random()) + a
        },
    },
  }

</script>