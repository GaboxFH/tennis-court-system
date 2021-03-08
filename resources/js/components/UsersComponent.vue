<template>

    <v-container
        class="pa-6 ma-auto"
        fluid
    >

        <v-container>
            <p>Welcome.</p>

            <p>Added phone number and basic vuetify validation. Still needs database validation to check that email is unique if email is changed. Also maybe add are you sure for email and phone changes. </p>



        </v-container>

    <v-data-table
        :headers="headers"
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
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
                <v-spacer></v-spacer>
                <v-dialog
                    v-model="dialog"
                    max-width="500px"
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
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-container>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :error-messages="nameRules"
                                            v-model="editedItem.name"
                                            @keydown="nameMsg"
                                            label="Name"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                          :error-messages="phoneRules"
                                          v-model="editedItem.phone"
                                          @keydown="phoneMsg"
                                          label="Phone"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :error-messages="emailRules"
                                            v-model="editedItem.email"
                                            @keydown="emailMsg"
                                            label="Email"
                                        ></v-text-field>
                                    </v-col>

                                </v-row>
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
        rowsPerPageItems: [10, 20, 30, 40],
        pagination: {
            rowsPerPage: 20
        },
        dialog: false,
        dialogDelete: false,
        valid: true,
        nameError: false,
        phoneError: false,
        emailError: -1,
        submitForm: true,
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
        editedItem: {
            name: '',
            phone: '',
            email: '',

        },
        defaultItem: {
            name: '',
            phone: '',
            email: '',
        },
    }),

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Add a Member' : 'Edit Member Info'
        },
        nameRules () {
            return this.nameError ? ['Field is required'] : undefined
        },
        phoneRules () {
            return this.phoneError ? ['Invalid phone number'] : undefined
        },
        emailRules () {
            if(this.emailError == 2){
                return ['Invalid email']
            }
            else if(this.emailError == 1){
                return ['Field is required']
            }
            else if(this.emailError == 0){
                return ['Email already exists']
            } 
            return undefined
        },
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
            // check name
            if(!this.editedItem.name){
                this.nameError = true
                this.submitForm = false
            }
            // check phone
            if(this.editedItem.phone){
                const validNumRegExp = new RegExp('^[0-9]*$')
                if(!validNumRegExp.test(this.editedItem.phone)){
                    this.phoneError = true
                    this.submitForm = false
                }
            }
            // check email is valid and unique
            if(this.editedItem.email){
                if(this.editedIndex > -1){// edit
                    for (var i = 0; i < this.users.length; i++) {
                        if(this.users[i].email === this.editedItem.email && i!=this.editedIndex){
                            this.emailError = 0
                            this.submitForm = false
                        }
                    }                    
                } else{//new email
                    const validEmailRegExp = new RegExp('@.*?\.')
                    if(!validEmailRegExp.test(this.editedItem.email)){
                        this.emailError = 2
                        this.submitForm = false
                    }
                    for (var i = 0; i < this.users.length; i++) {
                        if(this.users[i].email === this.editedItem.email){
                            this.emailError = 0
                            this.submitForm = false
                        }
                    }   
                }
            }
            else{
                this.emailError = 1
                this.submitForm = false
            }
            if(!this.submitForm){return}
            // save if no errors
            this.save()
        },
        nameMsg() { this.nameError = false },
        phoneMsg() { this.phoneError = false },
        emailMsg() { this.emailError = -1 },
        resetErrorMsgs() {
            this.nameError = false
            this.phoneError = false
            this.emailError = -1
        },
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
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.users.indexOf(item)
            this.editedItem = Object.assign({}, item)
            console.log(this.editedIndex)
            console.log(item.id)
            this.dialogDelete = true
        },

        deleteItemConfirm () {
            this.users.splice(this.editedIndex, 1)
            axios.delete('api/user/' + this.editedItem.id)
            this.closeDelete()
        },

        close () {
            this.resetErrorMsgs()
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
            if (this.editedIndex > -1) {
                Object.assign(this.users[this.editedIndex], this.editedItem)
                let item = JSON.parse(JSON.stringify(this.editedItem))
                let editUserPayload = {
                    item
                }
                axios.put('api/user/' + item.id, editUserPayload)
            } else {
                let item = JSON.parse(JSON.stringify(this.editedItem))
                let newCompTimePayload = {
                    item
                }
                item.password = "password123"
                axios.post('api/user/store', newCompTimePayload)
            }
            this.close()
        },
    },
}
</script>
