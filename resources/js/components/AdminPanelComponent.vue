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
                Admin Panel
            </div>
            <v-list-item-title class="text-h5 mb-1">
                Members
            </v-list-item-title>
        </v-list-item-content>

        <v-btn @click="club_settings_dialog=true" color="#757575" class="px-2" outlined small>
            Club Rules
            <v-icon color="#757575" class="pl-1">mdi-cog</v-icon>
        </v-btn>
        <!-- <v-list-item-avatar>
        <v-icon @click="club_settings_dialog=true">mdi-cog</v-icon>
        </v-list-item-avatar> -->
    </v-list-item>
    <v-row class="pa-0 ma-0">
        <v-col class="pa-0 mb-4">
            <v-text-field v-model="search" append-icon="mdi-magnify" label="Search"
                single-line hide-details class="mx-4"
            ></v-text-field>
        </v-col>
        <div style="width:130px; justify-content: center; align-items: center; display: flex;" class="pa-0 ma-0">
            <v-btn dense small color="primary" class="mr-4" @click="new_user_dialog=true">Add Member</v-btn>
        </div>
    </v-row>

    <v-data-table
    v-if="load1"
    multi-sort
    :headers="computedHeaders"
    :search="search"
    :items="users"
    :items-per-page="15"
    :footer-props="{ 'items-per-page-options': [15, 30, 45, 60, 75, 100] }"
    dense
    no-data-text="No Active Reservations"
    mobile-breakpoint="0"
    >
    <template v-slot:item.actions="{ item }">
        <v-icon
        small
        class="mr-2"
        @click="selectedUser=JSON.parse(JSON.stringify(item)); edit_user_dialog=true"
        >
        mdi-pencil
        </v-icon>
        <v-icon
        small
        @click="selectedUser=JSON.parse(JSON.stringify(item)); delete_user_dialog=true"
        >
        mdi-delete
        </v-icon>
    </template>
    </v-data-table>
        
    </v-card>



    <!--   DIALOGS  -->

    <v-dialog v-model="club_settings_dialog" content-class="custom-dialog" max-width="400px">
    <v-card class="mx-auto" v-if="load2">
        <v-progress-linear :active="dialog_load" color="grey lighten-2" height="5" indeterminate></v-progress-linear>
        <v-card-title class="grey lighten-2">Club Rules</v-card-title>
        <div class="mx-2 pt-1">
        <v-row v-for="(rule, index) in club_rules" v-bind:key="rule.id">
            <v-col>
                <v-text-field v-model="club_rules[index].value" :label="labels[index]"
                    :hint="hints[index]" :disabled="!club_rules[index].active"></v-text-field>
            </v-col>
            <div style="width:80px; justify-content: center; align-items: center; display: flex;" class="pa-0 ma-0">
                <v-switch v-model="club_rules[index].active" @click="club_rules[index].value=default_rules[index].value"></v-switch>
            </div>
        </v-row>
        </div>

        <v-divider class="pa-0 ma-0"></v-divider>
        <v-card-actions><v-spacer></v-spacer>
            <v-btn color="primary" @click="updateClubRules(club_rules)" :disabled="dialog_load">Update</v-btn>
            <v-btn color="primary" text @click="club_settings_dialog=false">Cancel</v-btn>
        </v-card-actions>
    </v-card>
    </v-dialog>

    <v-dialog v-model="delete_user_dialog" content-class="custom-dialog" max-width="300px">
    <v-card class="mx-auto" v-if="selectedUser">
        <v-progress-linear :active="dialog_load" color="grey lighten-2" height="5" indeterminate></v-progress-linear>
        <v-card-title class="grey lighten-2">Delete Member</v-card-title>

        <v-card-text class="mt-5">
        Are you sure you want to delete <b>{{ selectedUser.name }}</b>?
        </v-card-text>

        <v-divider class="pa-0 ma-0"></v-divider>
        <v-card-actions><v-spacer></v-spacer>
            <v-btn color="primary" @click="deleteItem(selectedUser.id)" :disabled="dialog_load">Yes</v-btn>
            <v-btn color="primary" text @click="delete_user_dialog=false">No</v-btn>
        </v-card-actions>
    </v-card>
    </v-dialog>

    <v-dialog v-model="reset_pass_dialog" content-class="custom-dialog" max-width="300px">
    <v-card class="mx-auto" v-if="selectedUser">
        <v-progress-linear :active="dialog_load" color="grey lighten-2" height="5" indeterminate></v-progress-linear>
        <v-card-title class="grey lighten-2">Reset Password</v-card-title>

        <!-- <v-card-text class="mt-5">
        Are you sure you want to delete <b>{{ selectedUser.name }}</b>?
        </v-card-text> --> 
        <div class="mx-2 pt-1">
        <v-text-field class="mt-5" v-model="pass"  label="New Password"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            :type="show1 ? 'text' : 'password'"
            @click:append="show1 = !show1"
        >
        </v-text-field>
        </div>
        <v-divider class="pa-0 ma-0"></v-divider>
        <v-card-actions><v-spacer></v-spacer>
            <v-btn color="primary" @click="updatePass(selectedUser.id, pass);  pass=null;" :disabled="dialog_load">Submit</v-btn>
            <v-btn color="primary" text @click="reset_pass_dialog=false; pass=null;">Cancel</v-btn>
        </v-card-actions>
    </v-card>
    </v-dialog>


    <v-dialog v-model="edit_user_dialog" persistent max-width="600px" class="pa-0 ma-0" content-class="custom-dialog">
    <v-card class="pa-0 ma-0" v-if="selectedUser">
        <v-progress-linear :active="dialog_load" color="primary" height="5" indeterminate></v-progress-linear>
        <v-toolbar v-if="selectedUser" class="mb-3" color="primary" dark flat>
            <v-card-title class="categories_style">Edit Information</v-card-title>
            <v-spacer></v-spacer>
            <v-btn @click="reset_pass_dialog=true">Reset Password</v-btn>
        </v-toolbar>
        <div class="mx-2 pt-1">
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="selectedUser.f_name" label="First Name" dense class="pr-1"></v-text-field>
            </v-col>
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="selectedUser.l_name" label="Last Name" dense class="pl-1"></v-text-field>
            </v-col>
        </v-row>
        <v-row class="pa-0 ma-0">
            <v-col cols="6" class="pa-0 ma-0">
                <v-select v-model="selectedUser.access" label="Membership Type" dense class="pr-1"
                :items="membership_types" :menu-props="{ top: false, offsetY: true }" >
                </v-select>
            </v-col>
            <v-col cols="6" class="pa-0 ma-0">
                <v-text-field v-model="selectedUser.membership_id" label="Membership ID" dense class="pl-1"></v-text-field>
            </v-col>
        </v-row>
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="selectedUser.email" label="Email" dense></v-text-field>
            </v-col>
        </v-row>
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="selectedUser.phone" label="Phone (optional)" dense></v-text-field>
            </v-col>
        </v-row>
        </div>
        <v-card-actions><v-spacer></v-spacer>
            <v-btn color="primary" @click="editItem(selectedUser)" :disabled="dialog_load">Update</v-btn>
            <v-btn color="primary" text @click="edit_user_dialog=false">Cancel</v-btn>
        </v-card-actions>     
    </v-card>
    </v-dialog>


    <v-dialog v-model="new_user_dialog" persistent max-width="600px" class="pa-0 ma-0" content-class="custom-dialog">
    <v-card class="pa-0 ma-0">
        <v-progress-linear :active="dialog_load" color="primary" height="5" indeterminate></v-progress-linear>
        <v-toolbar class="mb-3" color="primary" dark flat>
            <v-card-title class="categories_style">New Member</v-card-title>
        </v-toolbar>
        <div class="mx-2 pt-1">
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="newUser.f_name" label="First Name" dense class="pr-1"></v-text-field>
            </v-col>
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="newUser.l_name" label="Last Name" dense class="pl-1"></v-text-field>
            </v-col>
        </v-row>
        <v-row class="pa-0 ma-0">
            <v-col cols="6" class="pa-0 ma-0">
                <v-select v-model="newUser.access" label="Membership Type" dense class="pr-1"
                :items="membership_types" :menu-props="{ top: false, offsetY: true }" >
                </v-select>
            </v-col>
            <v-col cols="6" class="pa-0 ma-0">
                <v-text-field v-model="newUser.membership_id" label="Membership ID" dense class="pl-1"></v-text-field>
            </v-col>
        </v-row>
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="newUser.email" label="Email" dense></v-text-field>
            </v-col>
        </v-row>
        <v-row class="pa-0 ma-0">
            <v-col class="pa-0 ma-0">
                <v-text-field v-model="newUser.phone" label="Phone (optional)" dense></v-text-field>
            </v-col>
        </v-row>
        </div>
        <v-card-actions><v-spacer></v-spacer>
            <v-btn color="primary" @click="addItem(newUser)" :disabled="dialog_load">Add</v-btn>
            <v-btn color="primary" text @click="new_user_dialog=false; 
            newUser = { f_name: '', l_name: '', access: 'Single', membership_id: '', email: '', phone: '' }">
            Cancel</v-btn>
        </v-card-actions>     
    </v-card>
    </v-dialog>


<v-snackbar v-model="snackbar_dialog" :timeout="3000" top>
    {{ snackbar_text }}
    <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar_dialog = false"> Close</v-btn>
    </template>
</v-snackbar>

<v-overlay :value="!(load1&&load2)">
    <v-progress-circular indeterminate size="64"></v-progress-circular>
</v-overlay>

</v-container>
</template>

<script>
import moment from 'moment-timezone'

export default {
    data: () => ({
        users: null,
        club_rules: null,
        default_rules: null,
        search: '',
        user_headers: [
            { text: 'ID', value: 'membership_id' },
            { text: 'Access', value: 'access' },
            { text: 'First Name', value: 'f_name' },
            { text: 'Last Name', value: 'l_name' },
            { text: 'Phone', value: 'phone' },
            { text: 'Email', value: 'email' },
            { text: 'Actions', value: 'actions', sortable: false },
        ],
        membership_types: ['Single', 'Family', 'Tennis Pro', 'Admin'],
        labels: [
            "Max duration (hours)", 
            "Max number of reservations", 
            "Time in Advance (hours)", 
            "Guest Policy (days)", 
            "Guest Price (price)", 
        ],
        hints: [
            "Increments of 0.5", 
            "Whole Numbers Only", 
            "Increments of 0.5", 
            "Whole Numbers Only", 
            "Increments of 0.01", 
        ],

        load1: false,
        load2: false,
        dialog_load: false,

        new_user_dialog: false,
        edit_user_dialog: false,
        delete_user_dialog: false,
        club_settings_dialog: false,
        reset_pass_dialog: false,
            pass: null,
            show1: false,
        snackbar_dialog: false,
        snackbar_text: null,

        selectedUser: null,
        newUser: {
            f_name: '',
            l_name: '',
            access: 'Single',
            membership_id: '',
            email: '',
            phone: '',
        }


    }),
    created(){
        this.loadPage()    
    },
    computed: {
        computedHeaders(){
            return this.user_headers;
        }
    },
    methods: {
        updatePass(id, pass){            
            // let item = JSON.parse(JSON.stringify(id))
            // let item = null
            let item = {
                id: id,
                pass: pass
            }
            // item.id = id
            // item.pass = pass
            let editUserPayload = { item }
            axios.post('api/user/updatePass', editUserPayload)
            .then(response => {
                console.log(response.data)
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.loadPage()
                }
                this.dialog_load=false
            })
            .catch(error => {
                console.log(error);
                this.dialog_load=false
            })  
        },
        loadPage(){
            this.newUser = {
                f_name: '',
                l_name: '',
                access: 'Single',
                membership_id: '',
                email: '',
                phone: '',
            }
            this.new_user_dialog = false
            this.edit_user_dialog = false
            this.delete_user_dialog = false
            this.club_settings_dialog = false
            this.reset_pass_dialog = false
            this.getUsers()
            this.getRules()
        },
        updateClubRules(newRules){
            this.dialog_load=true
            let item = JSON.parse(JSON.stringify(newRules))
            let editUserPayload = { item }
            axios.put('api/updateRules', editUserPayload)
            .then(response => {
                console.log(response.data)
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.loadPage()
                }
                this.dialog_load=false
            })
            .catch(error => {
                console.log(error);
                this.dialog_load=false
            })  
        },
        addItem(user){
            this.dialog_load=true
            let item = JSON.parse(JSON.stringify(user))
            let newCompTimePayload = { item }
            axios.post('api/user/store', newCompTimePayload)
            .then(response => {
                console.log(response.data)
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.loadPage()
                }
                this.dialog_load=false
            })
            .catch(error => {
                console.log(error);
                this.dialog_load=false
            })      
        },
        editItem(user){
            this.dialog_load=true
            let item = JSON.parse(JSON.stringify(user))
            let editUserPayload = { item }
            axios.put('api/user/' + item.id, editUserPayload)
            .then(response => {
                console.log(response.data)
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.loadPage()
                }
                this.dialog_load=false
            })
            .catch(error => {
                console.log(error);
                this.dialog_load=false
            })            
        },
        deleteItem(id){
            this.dialog_load=true
            axios.delete('api/user/' + id)
            .then(response => {
                console.log(response.data)
                if(response.data.status=="error"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                } else if(response.data.status=="success"){
                    this.snackbar_text = response.data.message
                    this.snackbar_dialog = true
                    this.loadPage()
                }
                this.dialog_load=false
            })
            .catch(error => {
                console.log(error);
                this.dialog_load=false
            })  
        },
        getUsers(){
            this.load1 = false
            axios.get('api/users')
            .then(response => {
                console.log(response.data)
                this.users = response.data
                this.load1 = true
            })
            .catch(error => {
                console.log(error);
            })
        },
        getRules() {
            this.load2 = false
            axios.get('api/rules')
            .then(response => {
                console.log(response)
                this.club_rules = response.data
                this.default_rules = JSON.parse(JSON.stringify(response.data))
                this.load2 = true
            })
            .catch(error => {
                console.log(error);
            })
        },
    }
}
    </script>
