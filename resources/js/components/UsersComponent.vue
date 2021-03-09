<template>

    <v-container
        class="pa-6 ma-auto"
        fluid
    >

        <v-container>
            <p>Welcome.</p>

            <p>Added phone number and basic vuetify validation. Still needs database validation to check that email is unique if email is changed. Also maybe add are you sure for email and phone changes. </p>
            <p>{{ this.editedItem[0].access }}</p>


        </v-container>

    <v-data-table
        :headers="computedHeaders"
        :items="users"
        :footer-props="{
            'items-per-page-options': [25, 50, 75, 100, 125, 150]
        }"
        :items-per-page="50"
        :search="search"
        dense
        sort-by="updated_at"
        sort-desc
        class="elevation-1"
    >
        <template v-slot:top>
            <v-toolbar
                flat
            >
                <v-toolbar-title>Club Members</v-toolbar-title>
                <v-divider
                    class="mx-4"
                    inset
                    vertical
                ></v-divider>
                <v-text-field
                    v-model="search"
                    class="pr-7"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
                <v-dialog
                    v-model="dialog"
                    max-width="600px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            color="primary"
                            dark
                            class="mb-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            New User
                        </v-btn>
                    </template>
                    <v-form
                        ref="form"
                        v-model="valid"
                        lazy-validation
                    >
                    <v-card>
                        <v-card-title
                            class="ma-0 pa-0"
                        >
                            <span class="headline">{{ formTitle }}</span>
                            <v-spacer></v-spacer>
                            <v-overflow-btn
                                v-if="editedItem[0].access"
                                class="t-5"
                                :items="membership_types"
                                messages="Access Level"
                                v-model="editedItem[0].access"
                                dense
                            ></v-overflow-btn>
                        </v-card-title>
                        
                        <v-card-text
                            class="ma-0 pa-0"
                        >
                            <v-container 
                            fluid
                            class="ma-0 pa-0"
                            >
                                <v-header
                                    class="h4"
                                >Member 1</v-header>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :error-messages="nameRules"
                                            v-model="editedItem[0].name"
                                            @keydown="nameMsg(0)"
                                            label="Name"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                          :error-messages="phoneRules"
                                          v-model="editedItem[0].phone"
                                          @keydown="phoneMsg(0)"
                                          label="Phone"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :error-messages="emailRules"
                                            v-model="editedItem[0].email"
                                            @keydown="emailMsg(0)"
                                            label="Email"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <div v-if="editedItem[0].access==='Family'"
                                class="ma-0 pa-0"
                                >
                                <v-header
                                    class="h4"
                                >Member 2</v-header>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :error-messages="nameRules1"
                                            v-model="editedItem[1].name"
                                            @keydown="nameMsg(1)"
                                            label="Name"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                          :error-messages="phoneRules1"
                                          v-model="editedItem[1].phone"
                                          @keydown="phoneMsg(1)"
                                          label="Phone"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :error-messages="emailRules1"
                                            v-model="editedItem[1].email"
                                            @keydown="emailMsg(1)"
                                            label="Email"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                </div>

                            </v-container>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="blue darken-1"
                                text
                                @click="close"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                :disabled="!valid"
                                color="primary"
                                class="mr-4 px-6"   
                                @click="validateForm"
                            >
                                Save
                            </v-btn>
                        </v-card-actions>
                        
                        
                    </v-card>
                    </v-form>
                </v-dialog>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                            <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
            <v-icon
                small
                class="mr-2"
                @click="editItem(item)"
            >
                mdi-pencil
            </v-icon>
            <v-icon
                small
                @click="deleteItem(item)"
            >
                mdi-delete
            </v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn
                color="primary"
            >
                Reset
            </v-btn>
        </template>
    </v-data-table>
    </v-container>
</template>

<script>
export default {

    props: ['users'],

    data: () => ({
        membership_types: ['Single', 'Family', 'Tennis Pro', 'Admin'],
        dialog: false,
        dialogDelete: false,
        valid: true,
        search: '',
        headers: [
            {
                text: 'Name',
                align: 'start',
                sortable: true,
                value: 'name',
            },
            { text: 'Phone', value: 'phone' },
            { text: 'Email', value: 'email' },
            { text: 'Actions', value: 'actions', sortable: false },
        ],
        editedIndex: -1,
        submitForm: true,
        editedItem: [{
            name: '',
            phone: '',
            email: '',
            access: 'Single',
            nameError: undefined,
            phoneError: undefined,
            emailError: undefined,
        },
        {
            name: '',
            phone: '',
            email: '',
            access: 'Single',
            nameError: undefined,
            phoneError: undefined,
            emailError: undefined,
        }],
        defaultItem: [{
            name: '',
            phone: '',
            email: '',
            access: 'Single',
            nameError: undefined,
            phoneError: undefined,
            emailError: undefined,
        },
        {
            name: '',
            phone: '',
            email: '',
            access: 'Single',
            nameError: undefined,
            phoneError: undefined,
            emailError: undefined,
        }],
    }),

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Add a Member' : 'Edit Member Info'
        },
        //computed cant accecpt parameters :(
        nameRules () { return this.editedItem[0].nameError },
        nameRules1 () { return this.editedItem[1].nameError },
        phoneRules () { return this.editedItem[0].phoneError },
        phoneRules1 () { return this.editedItem[1].phoneError },
        emailRules () { return this.editedItem[0].emailError },
        emailRules1 () { return this.editedItem[1].emailError },
        computedHeaders () {
            // logic for hiding columns
            return this.headers
        }
    },

    watch: {
        dialog (val) {
            val || this.close()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
    },

    created () {
        this.getUsers();
    },

    methods: {
        validateForm() {
            this.submitForm = true
            var numOfFields = this.editedItem[0].access === 'Family' ? 2 : 1
            for(var i=0; i<numOfFields; i++){
                // check name
                if(!this.editedItem[i].name){
                    this.editedItem[i].nameError = "Field is required"
                    this.submitForm = false
                }
                // check phone
                if(this.editedItem[i].phone){
                    const validNumRegExp = new RegExp('^[0-9]*$')
                    if(!validNumRegExp.test(this.editedItem[i].phone)){
                        this.editedItem[i].phoneError = "Invalid phone"
                        this.submitForm = false
                    }
                }
                // check email is valid and unique
                if(this.editedItem[i].email){
                    const validEmailRegExp = new RegExp('@.*?\.')
                    if(!validEmailRegExp.test(this.editedItem[i].email)){
                        this.editedItem[i].emailError = "Invalid email"
                        this.submitForm = false
                    }
                    else if(this.editedIndex > -1){// edit
                        for (var j = 0; j< this.users.length; j++) {
                            if(this.users[j].email === this.editedItem[i].email && j!=this.editedIndex){
                                this.editedItem[i].emailError = "Email already exists"
                                this.submitForm = false
                            }
                        }                    
                    } else{//new email
                        for (var j = 0; j < this.users.length; j++) {
                            if(this.users[j].email === this.editedItem[i].email){
                                this.editedItem[i].emailError = "Email already exists"
                                this.submitForm = false
                            }
                        }   
                    }
                }
                else{
                    this.editedItem[i].emailError = "Field is required"
                    this.submitForm = false
                }
                if(i==1 && this.editedItem[0].email === this.editedItem[1].email){
                    this.editedItem[0].emailError = "Email must be unique"
                    this.editedItem[1].emailError = "Email must be unique"
                    this.submitForm = false
                }
            }
            if(!this.submitForm){return}
            // save if no errors
            this.save()
        },
        nameMsg(x) { this.editedItem[x].nameError = undefined },
        phoneMsg(x) { this.editedItem[x].phoneError = undefined },
        emailMsg(x) { this.editedItem[x].emailError = undefined },
        getUsers() {
            axios.get('api/users')
                .then(response => {
                    this.users = response.data
                })
                .catch(error => {
                    console.log(error);
                })
        },
        editItem (item) {
            this.editedIndex = this.users.indexOf(item)
            this.editedItem[0] = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.users.indexOf(item)
            this.editedItem[0] = Object.assign({}, item)
            // console.log(this.editedIndex)
            // console.log(item.id)
            this.dialogDelete = true
        },

        deleteItemConfirm () {
            this.users.splice(this.editedIndex, 1)
            axios.delete('api/user/' + this.editedItem[0].id)
            this.closeDelete()
        },

        close () {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
            this.$emit('refresh-users')
        },

        closeDelete () {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            if (this.editedIndex > -1) { //edit
                Object.assign(this.users[this.editedIndex], this.editedItem[0])
                let item = JSON.parse(JSON.stringify(this.editedItem[0]))
                let editUserPayload = {
                    item
                }
                axios.put('api/user/' + item.id, editUserPayload)
            } else { //new user(s)
                //random unique member_id
                var numIsNew = false
                var randNum = 0
                while(!numIsNew){
                    numIsNew = true
                    randNum = Math.floor(Math.random() * Math.floor(100000)) + 1   
                    for (var i = 0; i< this.users.length; i++) {
                        if(this.users[i].membership_id==randNum){
                            numIsNew = false
                        }
                    }
                }
                var numOfFields = this.editedItem[0].access === 'Family' ? 2 : 1
                for(var i=0; i<numOfFields; i++){
                    let item = JSON.parse(JSON.stringify(this.editedItem[i]))
                    let newCompTimePayload = {
                        item
                    }
                    item.membership_id = randNum
                    item.password = "password123"
                    axios.post('api/user/store', newCompTimePayload)
                }
            }
            this.close()
        },
    },
}
</script>
