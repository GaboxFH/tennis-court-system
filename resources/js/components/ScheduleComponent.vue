<template>
<v-container fluid class="pa-0 ma-0">
<!-- <v-row>
    <v-col>
        <p>{{ closed_court_periods }}</p>
    </v-col>
</v-row> -->
<v-row no-gutters class="fill-height pb-3" :class="{ cal_header: ($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')}">
    <v-col cols="3" class="pa-0 ma-0" v-if="($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')">
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0" align="left">
                <v-btn outlined v-if="($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')" color="blue darken-4" @click="court_close_dialog=true">
                    <!-- Close Courts -->
                    <v-icon>mdi-cancel</v-icon>
                </v-btn> 
            </v-col>
        </v-row>
        <v-row class="px-0 pt-12 pb-0 ma-0">
            <v-col class="pa-0 ma-0" align="left">
                <v-btn outlined color="blue darken-4" @click="setScheduleDate(0)">Today</v-btn>
            </v-col>
        </v-row>
    </v-col>
    <v-col class="pa-0 ma-0">
        <v-row no-gutters class="px-0 pb-7 pt-0 ma-0"> 
            <v-col class="pa-0 ma-0">
                <div class="flex text-center mx-8" 
                    v-bind:style="[($vuetify.breakpoint.name == 'xs' || $vuetify.breakpoint.name == 'sm') ? { 
                        position: 'relative', right: '0px'
                    } : { position: 'relative', right: '7.1%',
                    }]">
                <h1>Racquet Club Schedule</h1>
                <!-- <h1>Schedule</h1> -->
                <v-btn outlined small v-if="!($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')" color="blue darken-4" @click="court_close_dialog=true">
                    Close Courts
                    <!-- <v-icon>mdi-cancel</v-icon> -->
                </v-btn>                    
                </div>
            </v-col>
        </v-row>
        <v-row no-gutters class="px-0 pb-4 pt-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-app-bar flat dense color="white" class="pa-0 ma-0">
                    <div class="flex text-center pa-0 ma-0" 
                    v-bind:style="[($vuetify.breakpoint.name == 'xs' || $vuetify.breakpoint.name == 'sm') ? { 
                        position: 'relative', right: '0px'
                    } : { position: 'relative', right: '7.1%'
                    }]">
                    <v-btn icon text :small="!($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')" color="blue darken-4" @click="setScheduleDate(-1)">
                        <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>

                    <v-menu
                        ref="calendar_dialog"
                        v-model="calendar_dialog"
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
                            min-width="150px"
                            outlined
                            color="blue darken-4"
                        > 
                        {{ formatDate(schedule_date) }}
                        </v-btn>
                        </template>
                        <v-date-picker
                        v-model="schedule_date"
                        no-title
                        @input="calendar_dialog = false; reloadPage()"
                        ></v-date-picker>
                    </v-menu>

                    <v-btn icon text :small="!($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')" color="blue darken-4" @click="setScheduleDate(1)">
                        <v-icon>mdi-chevron-right</v-icon>
                    </v-btn>
                    </div>
                </v-app-bar>
            </v-col>
        </v-row>
        <v-row no-gutters class="pa-0 ma-0"> 
            <v-col class="pa-0 ma-0">
                <div v-if="this.$vuetify.breakpoint.name != 'xl'" class="flex text-center mx-8" 
                v-bind:style="[($vuetify.breakpoint.name == 'xs' || $vuetify.breakpoint.name == 'sm') ? { 
                position: 'relative', right: '0px'
                } : { position: 'relative', right: '7.1%'
                }]">
                <v-btn-toggle class="mx-6 mb-2" v-model="selected_view" rounded dense mandatory btn-toggle-btn-height="170px">
                    <v-btn text :small="!($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')" color="rgb(51,104,153)"
                    >1-8</v-btn>
                    <v-btn text :small="!($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')" color="rgb(51,104,153)"
                    >9-17</v-btn>
                </v-btn-toggle>
                </div>
            </v-col>
        </v-row>
    </v-col>
    <v-col cols="2" class="pa-0 ma-0" v-if="($vuetify.breakpoint.name != 'xs' && $vuetify.breakpoint.name != 'sm')">
        <v-row style="height: 32px" class="pa-0 ma-0 clickable" align="center" justify="center"
            @click="categories_dialog=!categories_dialog"
            @mouseover="field_active=true"
            @mouseleave="field_active=false"
            :class="{ list_item_active: field_active}"
        >
            <v-col class="pa-0 ma-0">
            <h5 class="pa-0 ma-0 ml-5">
            Categories
            <v-btn class="pa-0 ma-0" v-if="!new_text_field" small icon @click="categories_dialog=!categories_dialog; new_text_field = true">
                <v-icon class="pa-0 ma-0" small>mdi-plus-circle-outline</v-icon>
            </v-btn></h5>
            </v-col>
        </v-row>
        <div v-if="categories_dialog">
        <v-row 
            v-for="(item,ind) in categories" :key="item.id" 
            @mouseover="categories[ind].active = 1"
            @mouseleave="categories[ind].active = 0"
            class="pa-0 ma-0 clickable" no-gutters
            :class="{ list_item_active: item.active, list_item_selected: item.selected }"
            align="center" justify="center"
        >
            <v-col cols="2" max-width="300px" align="center" class="pa-0 ma-0 categories_style" style="justify-content: center;">
                <v-menu v-model="categories[ind].selected" left :offset-x="true" :close-on-content-click="false">
                    <template v-slot:activator="{ on, attrs }">
                        <v-avatar
                            v-bind="attrs" v-on="on"
                            size=18
                            :color="color_options[categories[ind].color]"
                            class="pa-0 ma-0 rounded clickable"
                            @click="selected_color=categories[ind].color; categories[ind].selected=1;"
                        ></v-avatar>
                    </template>
                    <v-card
                        color="grey lighten-4"
                        class="pa-2 ma-0"
                        min-width="150px"
                        max-width="200px"
                        v-model="categories[ind].color"
                    >
                        <v-row no-gutters>
                            <v-col align="center" justify="center">
                                <h5>Pick Color</h5>
                            </v-col>
                        </v-row>
                        <v-row v-for="i in 4" :key="i" align="center" justify="center" no-gutters class="py-1 ma-0" style="height: 36px">
                            <v-col v-for="j in 3" :key="j" align="center" justify="center" class="pa-0 ma-0" cols="4">
                                <v-avatar
                                    v-if="(i*3+j-4)==selected_color"
                                    size=28
                                    :color="color_options[(i*3+j-4)]"
                                    class="pa-0 ma-0 rounded clickable"
                                    @click="selected_color = (i*3+j-4)"
                                ></v-avatar>
                                <v-avatar
                                    v-else
                                    size=18
                                    :color="color_options[(i*3+j-4)]"
                                    class="pa-0 ma-0 rounded clickable"
                                    @click="selected_color = (i*3+j-4)"
                                ></v-avatar>
                            </v-col>
                        </v-row>
                        <v-row no-gutters class="py-2">
                            <v-col align="center" justify="center" class="pr-1">
                                <v-btn @click="categories[ind].selected=0" style="text-transform: none;" outlined depressed small>Cancel</v-btn>
                            </v-col>
                            <v-col align="center" justify="center" class="pl-1">
                                <v-btn @click="updateCategory(item.id, selected_color); categories[ind].selected=0" style="text-transform: none;" small depressed color="primary">Apply</v-btn>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-menu>
            </v-col>
            <v-col class="pa-0 ma-0 categories_style">
                {{categories[ind].name}}
            </v-col>
            <v-col cols="2" align="center" class="pa-0 ma-0">
                <v-menu offset-y left>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        small icon class="pa-0 ma-0"
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon small class="pa-0 ma-0">
                        mdi-dots-vertical
                        </v-icon>
                    </v-btn>
                    </template>
                    <v-list class="pa-0 ma-0">
                    <v-list-item dense link @click="deleteCategory(item.id)">
                        <v-list-item-title dense>Delete Category</v-list-item-title>
                    </v-list-item>
                    </v-list>
                </v-menu>
            </v-col>
        </v-row>
        </div>
        <v-row
            v-if="new_text_field"
            class="pa-0 ma-0" no-gutters
            align="center" justify="center"
        >
            <v-col cols="2" align="center" class="pa-0 ma-0">
                <v-avatar @click="createCategory()" tile color="primary" class="pa-0 ma-0 rounded clickable" size="24">
                    <v-icon x-small color="white" class="pa-0 ma-0">
                        mdi-plus
                    </v-icon>
                </v-avatar>
            </v-col>
            <v-col class="pa-0 ma-0">
                <v-text-field
                    v-model="new_category"
                    label="New Category" dense
                    class="pt-4 pb-0 py-0 ma-0"
                >
                </v-text-field>
            </v-col>
            <v-col cols="2" align="left" class="pa-0 ma-0">
                <v-btn @click="new_text_field=false" small icon class="pa-0 ma-0">
                    <v-icon small class="pa-0 ma-0">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-col>
        </v-row>
    </v-col>
</v-row>
<v-row class="pl-15 pr-4 pt-2 pb-2 text-white" style="background-color: rgb(51,104,153)">
    <v-col v-for="n in computedCategories" v-bind:key="n" align="center" class="pa-0 ma-0">
        <div v-if="$vuetify.breakpoint.name == 'xs'">{{ n }}</div>
        <div v-else>Court {{ n }}</div>
    </v-col>
</v-row>
<v-row class="fill-height">
<v-col>
    <v-sheet>
        <!-- {{events}} -->
        <v-calendar
            v-if="(load1&&load2&&load3)"
            ref="calendar"
            v-model="schedule_date"
            color="primary"
            type="category"
            category-show-all
            interval-minutes="30"
            first-interval="16"
            interval-height="30"
            interval-width="48"
            hide-header
            style="cursor: pointer" 
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
    </v-sheet>
</v-col>
</v-row>

<v-dialog v-model="edit_event_dialog" persistent max-width="600px"
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
                    <v-list-item @click="areyousure_dialog=true">
                        <v-list-item-title>Delete Event</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="reoccur_dialog=true">
                        <v-list-item-title>Repeat Event</v-list-item-title>
                    </v-list-item>
                </div>
                <div v-else>
                    <v-list-item @click="areyousure_dialog=true">
                        <v-list-item-title>Delete This Event Only</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="areyousure_reoccur_dialog=true">
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
            v-model="schedule_date"
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
            :items="categories"
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
            :items="users"
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
    v-model="areyousure_dialog"
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
    v-model="verify_update_dialog"
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
        {{ formatDate(this.schedule_date) }}<br>
        {{ getTimes(selectedEvent.start) }} - {{ getTimes(selectedEvent.end) }}<br>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-subtitle style="font-size: 1.4em" >Current Reservation Details:</v-card-subtitle>
        <v-card-text style="font-size: 1.2em" >
        Event: {{ selectedEvent.name }}<br>
        Court: {{ selectedEvent.orig_category }}<br>
        {{ formatDate(this.schedule_date) }}<br>
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
    v-model="areyousure_reoccur_dialog"
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
    v-model="reoccur_dialog"
    persistent
    max-width="450"
>
    <v-card>
        <div class="mx-5">
        <v-card-title>
            Repeating Event
        </v-card-title>
        <v-select
            v-model="reoccur_select"
            :items="reoccur_type"
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

<v-dialog
    v-model="court_close_dialog"
    persistent
    max-width="700"
>
    <v-card>
    <v-toolbar
    color="rgb(51,104,153)"
    dark
    >
        <v-toolbar-title>Court Closure Menu</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="cancel_close()">
            <v-icon>mdi-close</v-icon>
        </v-btn>
    </v-toolbar>
    <v-stepper
    v-model="close_step"
    vertical
    >
    <v-stepper-step
        :complete="close_step > 1"
        step="1"
    >
        Closure Options for {{ formatDate(schedule_date) }}
        <!-- <small>{{ formatDate(schedule_date) }}</small> -->
    </v-stepper-step>

    <v-stepper-content step="1">
        <v-card
        class="mb-6" outlined :loading="closed_courts.loading"
        >
        <v-row class="my-0 mx-5 pa-0">
            <v-col class="ma-0 pa-0">
                <v-select
                    v-model="closed_courts.courts"
                    label="Courts"
                    multiple
                    :items="all_courts"
                    :menu-props="{ top: false, offsetY: true }"
                ></v-select>
            </v-col>
            <v-col cols="3">
                <v-btn @click="selectAll(1)">
                    Select All
                </v-btn>
            </v-col>
        </v-row>
        <v-row class="my-0 mx-5 pa-0">
            <v-col class="ma-0 pa-0">
                <v-select
                v-model="closed_courts.start"
                label="Time Range Start"
                :items="times_ranges"
                item-text="show"
                item-value="ind"
                :menu-props="{ top: false, offsetY: true }"
            ></v-select>
            </v-col>
            <v-col class="ma-0 pa-0">
                <v-select
                v-model="closed_courts.end"
                label="Time Range Stop"
                :items="times_ranges"
                item-text="show"
                item-value="ind"
                :menu-props="{ top: false, offsetY: true }"
            ></v-select>
            </v-col>
            <v-col cols="3">
                <v-btn @click="selectAll(2)">
                    All Day
                </v-btn>
            </v-col>
        </v-row>
        <v-row v-if="closed_courts.err_message!=null" class="ma-0 pa-0">
            <v-col class="ma-0 pa-0">
                <v-alert
                    colored-border
                    type="error"
                    dense color="red"
                    class="ma-0 px-4 pt-0 pb-6"
                >
                    {{closed_courts.err_message}}
                </v-alert>
            </v-col>
        </v-row>
        </v-card>
        <v-btn
        color="primary"
        @click="step1()"
        >
        Continue
        </v-btn>
        <v-btn text @click="cancel_close()">
        Cancel
        </v-btn>
    </v-stepper-content>

    <v-stepper-step
        :complete="close_step > 2"
        step="2"
    >
    Affected Reservations
    </v-stepper-step>

    <v-stepper-content step="2">
        <v-card
        class="mb-6" outlined :loading="closed_courts.loading"
        >
        <v-row class="ma-0 pa-0">
            <v-col class="ma-0 px-2 py-2">
                This actions will result in the cancellation of {{ closed_courts.conflicting_res }} reservations. 
            </v-col>
        </v-row>
        <v-row class="ma-0 pa-0" v-if="closed_courts.start!=null &&closed_courts.end!=null">
            <v-col class="ma-0 px-2 py-2"
            v-if="closed_courts.courts==all_courts 
            && times_ranges[closed_courts.start].ind==0  
            && times_ranges[closed_courts.end].ind==24"
            >
                Are you sure you want to close all courts
                on {{ formatDate(schedule_date) }}?
            </v-col>
            <v-col class="ma-0 px-2 py-2"
            v-else-if="closed_courts.courts==all_courts"
            >
                Are you sure you want to close all courts
                on {{ formatDate(schedule_date) }}
                from {{ times_ranges[closed_courts.start].show }} to {{ times_ranges[closed_courts.end].show }}?
            </v-col>
            <v-col class="ma-0 px-2 py-2"
            v-else-if="times_ranges[closed_courts.start].ind==0  
            && times_ranges[closed_courts.end].ind==24"
            >
                Are you sure you want to close courts {{ closed_courts.courts.join(', ') }} 
                on {{ formatDate(schedule_date) }}?
            </v-col>
            <v-col class="ma-0 px-2 py-2"
            v-else
            >
                Are you sure you want to close courts {{ closed_courts.courts.join(', ') }} 
                on {{ formatDate(schedule_date) }}
                from {{ times_ranges[closed_courts.start].show }} to {{ times_ranges[closed_courts.end].show }}?
            </v-col>
        </v-row>
        </v-card>
        <v-btn
        color="primary"
        @click="close_step = 3"
        >
        Continue
        </v-btn>
        <v-btn text @click="close_step = 1">
        Back
        </v-btn>
    </v-stepper-content>

    <v-stepper-step
        :complete="close_step > 3"
        step="3"
    >
    Cancellation message      
    </v-stepper-step>

    <v-stepper-content step="3">
        <v-card
        class="mb-6" outlined 
        >
        <v-row class="ma-0 pa-0" v-if="closed_courts.start!=null &&closed_courts.end!=null">
            <v-col class="ma-0 px-2 py-2">
                The following cancellation message will be sent to all participants of the affected reservations and the reason for cancellation will be recorded. 
                <!-- This section is for customizing the cancellation email message.  -->
            </v-col>
        </v-row>
        <v-row class="ma-0 pa-0">
            <v-col class="ma-0 px-2 py-2">
                <v-select
                v-model="closed_courts.cancellation_reason"
                :items="cancellation_reason_options"
                item-text="name"
                item-value="name"
                color="primary"
                :menu-props="{ top: false, offsetY: true }"
                label="Reason for Cancellation"
                disabled
                >
                </v-select>
                <v-textarea
                auto-grow dense
                counter 
                maxlength="300"
                v-model="closed_courts.email_message"
                color="primary"
                label="Cancellation Message"
                disabled
                >
                </v-textarea>
                Email messages are not currently enabled. Coming soon. You may continue. 
            </v-col>
        </v-row>
        </v-card>
        <v-btn
        color="primary"
        @click="close_step = 4"
        >
        Continue
        </v-btn>
        <v-btn text @click="close_step = 2">
        Back
        </v-btn>
    </v-stepper-content>

    <v-stepper-step step="4">
    Review
    </v-stepper-step>
    <v-stepper-content step="4">
        <v-card
        class="mb-6" outlined :loading="closed_courts.loading"
        >
        <v-row class="ma-0 pa-0" v-if="closed_courts.start!=null &&closed_courts.end!=null">
            <v-col class="ma-0 px-2 py-2">
                Affected Courts: <b>{{ closed_courts.courts.join(', ') }}</b> <br>
                Affected Times: <b>{{ times_ranges[closed_courts.start].show }} to {{ times_ranges[closed_courts.end].show }}</b> <br>
                Date: <b>{{ formatDate(schedule_date) }}</b>
            </v-col>
        </v-row>
        <v-row class="ma-0 pa-0">
            <v-col class="ma-0 px-2 py-2">
                This action will result in the cancellation of <b>{{ closed_courts.conflicting_res }} reservations. </b>
            </v-col>
        </v-row>
        <v-row class="ma-0 pa-0">
            <v-col class="ma-0 px-2 py-2">
                The reason for cancellation: <b>{{ closed_courts.cancellation_reason }}</b>
            </v-col>
        </v-row>
        </v-card>
        <v-btn :disabled="closed_courts.loading"
        color="primary"
        @click="closeCourts()"
        >
        Submit
        </v-btn>
        <v-btn text @click="close_step=3" :disabled="closed_courts.loading">
        Back
        </v-btn>
    </v-stepper-content>
    </v-stepper>
    </v-card>
</v-dialog>

<!-- persistent
    max-width="700"
>
    <v-card>
    <v-toolbar
    color="rgb(51,104,153)"
    dark
    > -->

<v-dialog v-model="closure_edit_dialog" persistent max-width="400">
    <v-card>
        <v-toolbar color="rgb(51,104,153)" dark>
            <v-toolbar-title>Re-open Court Menu</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="closure_edit_dialog = false">
                <v-icon>mdi-close</v-icon>
            </v-btn>        
        </v-toolbar>

        <div class="ma-4">
            <p class="mx-0 mb-1">Closure Info:</p>
            Date: <b>{{ formatDate(schedule_date) }}</b><br>
            Affected Courts: <b>{{ closure_info.courts.join(', ') }}</b> <br>
            Affected Time: <b>{{ getTimes(closure_info.start) }} - {{ getTimes(closure_info.end) }}</b> <br>
        </div>
        <v-card-actions>
            <v-btn @click="deleteClosure(closure_info.id)">Re-open</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

<v-snackbar v-model="snackbar_dialog" :timeout="3000" top>
        {{ snackbar_text }}
    <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar_dialog = false"> Close</v-btn>
    </template>
</v-snackbar>

<v-overlay :value="!(load1&&load2&&load3) || !dialog_load">
    <v-progress-circular indeterminate size="64"></v-progress-circular>
</v-overlay>

</v-container>



</template>




<script>
import moment from 'moment-timezone'

export default {
    data: () => ({
        // schedule variables
        schedule_date: null,
        selected_view: 0,

        // event variables
        dragEvent: null,
        createEvent: null,
        createStart: null,
        extendOriginal: null,
        dragOriginalStart: null,
        dragOriginalEnd: null,
        dragOriginalCategory: null,
        selectedEvent: null,
        searchInput: null,
        courtOpen: null,

        // database values
        events: null,
        closed_court_periods: null,
        categories: null,
        users: null,
        // newCompTimePayload: null,
        newCompTimePayload: null,
        

        // loading bars
        load1: false,
        load2: false,
        load3: false,
        dialog_load: true,

        // dialogs
        calendar_dialog: false,
        categories_dialog: true,
            field_active: false,
            new_text_field: false,
            selected_color: null,
            color_options: [ '#064b9a', '#f0da16', '#548e66', '#f68324', '#f2618f', '#7c7c7c', '#db4c4b', '#a5e3f6', '#167d9f', '#b6e95f', '#6478f7', '#8e6a6b' ],
            new_category: null,
        snackbar_dialog: false,
            snackbar_text: null,
        edit_event_dialog: false,
            participantsLoaded: false,
        verify_update_dialog: false,
        areyousure_dialog: false,
        reoccur_dialog: false,
            reoccur_select: 0,
            reoccur_type: [{
                text: 'Repeat Event Weekly',
                value: 0
            }],
            reoccur_err: false,
            reoccur_err_mess: null,
        areyousure_reoccur_dialog: false,
        court_close_dialog: false,
            close_step: 1,
            closed_courts: { 
                courts: [],
                start: null,
                end: null,
                loading: false,
                err_message: null,
                conflicting_res: null,
                cancellation_reason: 'Rainout',
                email_message: null,
                court_label: null,
                duration_label: null
            },
        closure_edit_dialog: false,
            closure_info: {
                id: null,
                courts: [],
                start: null,
                end: null,
                loading: false,
            },

        cancellation_reason_options: ['Rainout','Other'],
        all_courts: ['1', '2', '3','4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17'],
        times_ranges: [
            {show:"8:00 am", val: 8.0, ind: 0},
            {show:"8:30 am", val: 8.5, ind: 1},
            {show:"9:00 am", val: 9.0, ind: 2},
            {show:"9:30 am", val: 9.5, ind: 3},
            {show:"10:00 am",val: 10.0, ind: 4},
            {show:"10:30 am",val: 10.5, ind: 5},
            {show:"11:00 am",val: 11.0, ind: 6},
            {show:"11:30 am",val: 11.5, ind: 7},
            {show:"12:00 pm",val: 12.0, ind: 8},
            {show:"12:30 pm",val: 12.5, ind: 9},
            {show:"1:00 pm", val: 13.0, ind: 10},
            {show:"1:30 pm", val: 13.5, ind: 11},
            {show:"2:00 pm", val: 14.0, ind: 12},
            {show:"2:30 pm", val: 14.5, ind: 13},
            {show:"3:00 pm", val: 15.0, ind: 14},
            {show:"3:30 pm", val: 15.5, ind: 15},
            {show:"4:00 pm", val: 16.0, ind: 16},
            {show:"4:30 pm", val: 16.5, ind: 17},
            {show:"5:00 pm", val: 17.0, ind: 18},
            {show:"5:30 pm", val: 17.5, ind: 19},
            {show:"6:00 pm", val: 18.0, ind: 20},
            {show:"6:30 pm", val: 18.5, ind: 21},
            {show:"7:00 pm", val: 19.0, ind: 22},
            {show:"7:30 pm", val: 19.5, ind: 23},
            {show:"8:00 pm", val: 20.0, ind: 24},
        ],
        

            // areyousure: false,
            // areyousure_reoccur: false,

        
    }),
    created () {
        this.schedule_date = moment().tz('America/New_York').format('YYYY-MM-DD')
        this.getEvents(this.schedule_date)
        this.getCategories()
        this.getUsers()
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
                return this.users
            } else {
                // dont show host on option list
                var members_no_host = JSON.parse(JSON.stringify(this.users))
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
                if(this.selected_view == 0){
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
                    var inactive = false;
                    for(var evts in this.closed_court_periods){
                        // console.log(this.close_courts[evts].category)
                        // console.log(new Date(this.close_courts[evts].start).toTimeString().split(' ')[0])
                        // console.log(new Date(this.close_courts[evts].start).getHours())
                        if(interval.category.categoryName == this.closed_court_periods[evts].category 
                        && interval.hour+(interval.minute/60) >= new Date(this.closed_court_periods[evts].start).getHours()+(new Date(this.closed_court_periods[evts].start).getMinutes()/60)
                        && interval.hour+(interval.minute/60) <  new Date(this.closed_court_periods[evts].end).getHours()+(new Date(this.closed_court_periods[evts].end).getMinutes()/60)
                        ){
                            inactive = true;
                        }
                    }
                    // console.log(interval)
                    // const inactive = 
                    // (interval.hour < 10 ||
                    // interval.hour >= 20) //&& interval.category.categoryName == "3" )
                    
                    // const inactive = (interval.category.categoryName == "3" && interval.hour < 10) ;
                    // var inactive = false
                    // console.log(inactive)
                    // const inactive = (interval.hour < 10) ;
                    
                    const startOfHour = interval.minute === 0
                    const dark = this.dark
                    const mid = dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'

                    return {
                    backgroundColor: inactive ? (dark ? 'rgba(0,0,0,0.4)' : 'rgba(0,0,0,0.07)') : undefined,
                    borderTop: startOfHour ? undefined : '2px dashed ' + mid,
                    }
                },
            }
            return stylings['workday'].bind(this)
        },
    },
    methods: {
        // calendar functions
        startDrag ({ event, timed }) {
            if (event && timed) {
                console.log("start drag")
                console.log(event)
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
                // console.log(tms.date)
                this.createStart = this.roundTime(mouse)
                this.createEvent = {
                    name: `New Event`,
                    date: this.schedule_date,
                    start: this.createStart,
                    end: this.createStart+30*1000*60,
                    category: tms.category.categoryName,
                    timed: true,
                    method: this.categories[0].name,
                    num_of_members: 0,
                    num_of_guests: 0,
                    host_id: null,
                    reoccur_id: null,
                    timed: 1
                }
                // console.log(tms)
                // console.log(this.createEvent.start)
                // 8   10
                // 9   12
                this.courtOpen = true
                var closure_id = null
                // if no time conflict(event)
                for(var evts in this.closed_court_periods){
                    // time conflict
                    // console.log(evts)
                    // console.log(this.createEvent)
                    if(this.closed_court_periods[evts].start < this.createEvent.end 
                    && this.closed_court_periods[evts].end > this.createEvent.start 
                    && this.closed_court_periods[evts].category == this.createEvent.category){
                        // courts closed
                        closure_id = this.closed_court_periods[evts].reoccur_id
                        this.courtOpen = false
                    }
                }
                if(this.courtOpen){
                    this.events.push(this.createEvent)
                } else {
                    this.openClosureEdit(closure_id)
                }            
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
                
                if(this.courtOpen){
                    console.log("here 4")
                
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
            }
        },
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
                // this.edit_event_dialog = true
                console.log("dialog 1 (are you sure)")
                if(this.dragOriginalStart != this.dragEvent.start || this.dragOriginalEnd != this.dragEvent.end || this.dragOriginalCategory != this.dragEvent.category){
                    console.log("slide date!")
                    this.selectedEvent.orig_category = this.dragOriginalCategory
                    this.selectedEvent.orig_start = this.dragOriginalStart
                    this.selectedEvent.orig_end = this.dragOriginalEnd
                    this.verify_update_dialog = true
                    // console.log("slide date!")
                } else {
                    // console.log(this.checkForCustomMethod(this.selectedEvent.method))
                    if(!this.checkForCustomMethod(this.selectedEvent.method)){
                        this.selectedEvent.custom = this.selectedEvent.method
                        this.selectedEvent.method = "Custom"
                    }
                    this.getParticipants()
                    // this.edit_event_dialog = true
                }
                
            } 
            // Extend could be update or create
            else if(this.createEvent){
                // if not a open closed court dialog
                this.selectedEvent = this.createEvent
                this.selectedEvent.orig_category = this.selectedEvent.category
                this.selectedEvent.orig_start = this.createStart
                this.selectedEvent.orig_end = this.extendOriginal
                
                let item = JSON.parse(JSON.stringify(this.createEvent))

                this.newCompTimePayload = {
                    item
                }
                // new event
                // if(moment(new Date()).valueOf()>this.selectedEvent.orig_start){
                //     this.snackbar_text = "Start Time"
                //     this.snackbar_dialog = true
                // } 
                if(!this.createEvent.id && this.courtOpen ){
                    // console.log(moment(new Date()).valueOf())
                    // console.log(this.selectedEvent.orig_start)
                    
                    this.dialog_load = false
                    axios.post('api/reservation/store', this.newCompTimePayload)
                    .then((response) => {
                        console.log(response);
                        if(response.data=="error"){
                            this.snackbar_text = "Time Conflict"
                            this.snackbar_dialog = true
                            this.reloadPage()
                            // this.refreshLoad = true
                            // this.$emit('refresh-schedule')
                        } else {
                            this.snackbar_text = "Event Created"
                            this.snackbar_dialog = true
                            this.selectedEvent.id = response.data.id
                            this.getParticipants()
                            // this.edit_event_dialog = true
                        }
                        this.dialog_load = true
                        // this.reloadPage()
                        // this.refreshLoad = true
                        // this.$emit('refresh-schedule')
                    }, (error) => {
                        console.log(error);
                    });
                } 
                // extending existing event
                else {
                    if(this.courtOpen){
                        this.newCompTimePayload.item.ordered_participants_ids = -1
                        this.verify_update_dialog = true
                    }
                }
                console.log("dialog 2", this.courtOpen)


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
        getEventColor (event) {
            const colors = this.color_options;
            event.color = colors[6]
            this.categories.forEach(function (item, index) {
                if(event.method==item.name){
                    event.color = colors[item.color]
                }
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
        },
        showIntervalLabel(interval){
            return (interval.minute === 0 && interval.hour != 8)
        }, 
        getParticipants() {
            // this.selectedEvent.host_id = ''
            this.selectedEvent.participants = []
            this.edit_event_dialog = true
            axios.get('api/reservation_users/'+this.selectedEvent.id+'/'+this.selectedEvent.host_id)
            .then(response => {
                console.log(response)
                console.log("response")
                this.selectedEvent.host_id = response.data.res_host
                this.selectedEvent.participants = response.data.res_participants
                // this.edit_event_dialog = true
                this.participantsLoaded = true
                // this.participantsLoaded = false
            })
            .catch(error => {
                console.log(error)
            })
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
                // this.refreshLoad = true
                // this.$emit('refresh-schedule')
                // this.reloadPage()
            }
            this.edit_event_dialog = false
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
                        this.snackbar_text = "Time Conflict"
                        this.snackbar_dialog = true
                        // this.refreshLoad = true
                        // this.$emit('refresh-schedule')
                    } else{
                        this.snackbar_text = "Event Updated"
                        this.snackbar_dialog = true
                        // this.refreshLoad = true
                        // this.$emit('refresh-schedule')
                    }
                    this.reloadPage()
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.snackbar_text = "Event Drag Canceled"
                this.snackbar_dialog = true
                this.reloadPage()
                // this.refreshLoad = true
                // this.$emit('refresh-schedule')
            }
            this.verify_update_dialog = false
        },
        deleteFinal(del_event){
            if(del_event){
                axios.delete('api/reservation/'+this.selectedEvent.id)
                .then((response) => {
                    // console.log(response);
                    this.snackbar_text = "Event Deleted"
                    this.snackbar_dialog = true
                    this.edit_event_dialog = false
                    this.areyousure_dialog = false
                    this.reloadPage()
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.areyousure_dialog = false
            }
        },
        repeat_event(bool) {
            if(bool){
                if(this.reoccur_select == 0){//weekly
                    let item = JSON.parse(JSON.stringify(this.selectedEvent))
                    this.newCompTimePayload = {
                        item
                    }
                    // item.reoccur_type = this.reoccur_select
                    item.reoccur_type = "weekly"
                    // new event
                    // this.refreshLoad = true
                    this.dialog_load = false
                    axios.post('api/reservation/storeReoccur', this.newCompTimePayload)
                    .then((response) => {
                        console.log(response);
                        if(response.data["status"]=="success"){
                            this.reoccur_dialog = false
                            this.saveEvent(false)
                            this.reoccur_err = false
                            this.snackbar_text = response.data["message"]
                            this.snackbar_dialog = true
                            this.reloadPage()
                        } else if(response.data["status"]=="error"){
                            this.reoccur_err_mess = response.data["message"]
                            this.reoccur_err = true
                        } 
                        this.dialog_load = true
                    }, (error) => {
                        console.log(error);
                    });
                } else if(this.reoccur_select == 1){//monthly
                    console.log("hi")
                }
                // console.log(this.selectedEvent)
                // console.log(this.reoccur_select)
                // console.log(this.reoccur_id)
            } else{
                this.reoccur_dialog = false
                this.reoccur_err = false
            }
        },
        deleteReoccur(del_event){
            if(del_event){
                this.dialog_load = false
                axios.delete('api/reservation/deleteReoccur/'+this.selectedEvent.id)
                .then((response) => {
                    console.log(response);
                    this.snackbar_text = "Event Deleted"
                    this.snackbar_dialog = true
                    this.edit_event_dialog = false
                    this.areyousure_reoccur_dialog = false
                    this.reloadPage()
                    this.dialog_load = true
                    // this.refreshLoad = true
                    // this.$emit('refresh-schedule')
                }, (error) => {
                    console.log(error);
                });
            } else {
                this.areyousure_reoccur_dialog = false
            }
        },

        // close court functions
        step1(){
            this.closed_courts.loading = true
            var d = new Date(this.schedule_date);
            var n = d.getTime();
            let item = JSON.parse(JSON.stringify(this.closed_courts))
            if(this.closed_courts.start != null && this.closed_courts.end != null){
                item.start = n+this.times_ranges[this.closed_courts.start].val*60*60*1000
                item.end = n+this.times_ranges[this.closed_courts.end].val*60*60*1000
            }
            this.newCompTimePayload = {
                item
            }
            // this.refreshLoad = true
            axios.post('api/reservation/closeOptions', this.newCompTimePayload)
            .then((response) => {
                console.log(response);
                if(response.data["status"]=="success"){
                    this.closed_courts.conflicting_res = response.data.reservations
                    this.close_step = 2

                    if(this.closed_courts.courts==this.all_courts){
                        this.closed_courts.court_label = "all courts"
                    } else {
                        this.closed_courts.court_label = "the following courts: (" + this.closed_courts.courts.join(', ')+")"
                    }

                    if(this.closed_courts.start == 0 && this.closed_courts.end == 24){
                        this.closed_courts.duration_label = ""
                    } else {
                        this.closed_courts.duration_label = " from " + this.times_ranges[this.closed_courts.start].show +" to " + this.times_ranges[this.closed_courts.end].show
                    }
                    if(this.closed_courts.cancellation_reason == 'Rainout'){
                        this.closed_courts.email_message = 'Due to inclement weather conditions, ' + this.closed_courts.court_label + " will be closed" + this.closed_courts.duration_label + " on " + this.formatDate(this.schedule_date) + "."
                    }
                    // Due to inclement weather conditions, all courts will be closed all day on Saturday June 19th. 
                    // this.repeat_dialog = false
                    // this.saveEvent(false)
                    // this.message_text = response.data["message"]
                    // this.message = true
                } else if(response.data["status"]=="error"){
                    console.log(response.data["message"])
                    this.closed_courts.err_message = response.data["message"]
                    // this.reoccur_err = true
                }
                this.closed_courts.loading = false
            }, (error) => {
                console.log(error);
                this.closed_courts.loading = false
            });
        },
        closeCourts(){
            this.closed_courts.loading = true
            var d = new Date(this.schedule_date);
            var n = d.getTime();

            let item = JSON.parse(JSON.stringify(this.closed_courts))
            if(this.closed_courts.start != null && this.closed_courts.end != null){
                item.start = n+this.times_ranges[this.closed_courts.start].val*60*60*1000+1000*60*60*4
                item.end = n+this.times_ranges[this.closed_courts.end].val*60*60*1000+1000*60*60*4
            }
            item.date = this.schedule_date
            item.reason = this.closed_courts.cancellation_reason
            this.newCompTimePayload = {
                item
            }
            // this.refreshLoad = true
            axios.post('api/reservation/closeCourts', this.newCompTimePayload)
            .then((response) => {
                console.log(response);
                if(response.data["status"]=="success"){
                    this.snackbar_dialog=true
                    this.snackbar_text=response.data["message"]
                    this.cancel_close()
                    this.reloadPage()
                } else if(response.data["status"]=="error"){
                    console.log(response.data["message"])
                }
                this.closed_courts.loading = false
            }, (error) => {
                console.log(error);
                this.closed_courts.loading = false
            });
        },
        cancel_close(){
            this.court_close_dialog=false
            this.close_step = 1
            this.closed_courts = { 
                courts: [],
                start: null,
                end: null,
                loading: false,
                err_message: null,
                conflicting_res: null,
                cancellation_reason: 'Rainout',
                email_message: null
            }
        },
        selectAll(num){
            if(num==1){
                this.closed_courts.courts = this.all_courts;
            } else if (num==2){
                this.closed_courts.start = 0
                this.closed_courts.end = 24
            }
        },
        openClosureEdit(closure_id){
            this.dialog_load = false
            axios.get('api/getClosure/' + closure_id)
            .then((response) => {
                if(response.data.status=="error"){
                    this.snackbar_dialog = true
                    this.snackbar_text = response.data.message
                    this.reloadPage()
                } else {
                    console.log(response.data)
                    console.log(response.data.start)
                    console.log(this.getTimes(response.data.start))
                    this.closure_info.id = closure_id
                    this.closure_info.courts = response.data.courts
                    this.closure_info.start = response.data.start
                    this.closure_info.end = response.data.end
                    this.dialog_load = true
                    this.closure_edit_dialog = true
                }
                
            }, (error) => {
                console.log(error);
            });
        },
        deleteClosure(closure_id){
            this.closure_info.loading = true
            axios.delete('api/deleteClosure/' + closure_id)
            .then((response) => {
                if(response.data.status=="error"){
                    this.snackbar_dialog = true
                    this.snackbar_text = response.data.message
                } else {
                    this.snackbar_dialog = true
                    this.snackbar_text = response.data.message
                    // console.log(response.data)
                    // console.log(response.data.start)
                    // console.log(this.getTimes(response.data.start))
                    this.closure_info.courts = []
                    this.closure_info.start = null
                    this.closure_info.end = null
                    this.closure_info.loading = false
                    this.reloadPage()
                }
                this.closure_edit_dialog = false
            }, (error) => {
                console.log(error);
            });
        },


        // category CRUD functions
        createCategory(){
            this.dialog_load = false
            let item = JSON.parse(JSON.stringify(this.new_category))
            this.newCompTimePayload = {
                item
            }
            axios.post('api/categories/store', this.newCompTimePayload)
            .then((response) => {
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.new_text_field=false
                } 
                else {
                    console.log("unknown repsonse")
                }
                this.dialog_load = true
                this.reloadPage()
            }, (error) => {
                console.log(error);
            });
        },
        updateCategory(id, color){
            this.dialog_load = false
            var categoryInfo = {
                id: id,
                color: color
            }
            let item = JSON.parse(JSON.stringify(categoryInfo))
            this.newCompTimePayload = {
                item
            }
            axios.put('api/categories/update', this.newCompTimePayload)
            .then((response) => {
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.new_text_field=false
                } 
                else {
                    console.log("unknown repsonse")
                }
                this.dialog_load = true
                this.reloadPage()
            }, (error) => {
                console.log(error);
            });
        },
        deleteCategory(id){
            this.dialog_load = false
            axios.delete('api/categories/'+id)
            .then((response) => {
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.new_text_field=false
                } 
                else {
                    console.log("unknown repsonse")
                }
                this.dialog_load = true
                this.reloadPage()
            }, (error) => {
                console.log(error);
            });
        },
        checkForCustomMethod (method){
            var return_val = false
            this.categories.forEach(function (item) {
                if(method==item.name){
                    console.log("return true true")
                    return_val = true
                }
            })
            return return_val ? true : false
        },
        
        // format date/time functions
        formatDate(date){
            return moment(date).format('dddd MMMM Do')
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

        // today, prev, and next date buttons
        setScheduleDate(offset){
            if(offset == 0){
                this.schedule_date = moment().tz('America/New_York').format('YYYY-MM-DD')
            } else {
                this.schedule_date = moment(this.schedule_date).add(offset, 'days').format('YYYY-MM-DD')
            }
            this.reloadPage()
        },

        // reload data
        reloadPage(){
            this.getEvents(this.schedule_date)
            this.getCategories()
            this.getUsers()
        },

        // database functions
        getEvents(date){
            this.load1 = false
            axios.get('api/getEvents/'+date)
            .then(response => {
                console.log(response.data)
                this.events = response.data.events
                this.closed_court_periods = response.data.closed_court_periods
                this.load1 = true
            })
            .catch(error => {
                console.log(error);
            })
        },
        getCategories(){
            this.load2 = false
            axios.get('api/categories')
            .then(response => {
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
        }
    }
}
</script>



<style>
/* Calendar heading padding */
.cal_header {
    padding: 24px 0px 12px 48px;
}
/* Categories CSS */
.list_item_active {
    background-color:rgb(228, 228, 228);
}
.list_item_selected {
    background-color:rgb(228, 228, 228);
}
.categories_style {
    display: inline-block;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
.clickable {
    cursor: pointer;
}

/* Calendar css */
/* SASS variable not working ... */
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