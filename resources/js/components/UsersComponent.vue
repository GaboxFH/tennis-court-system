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
        sort-by="calories"
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
                                            :rules="nameRules"
                                            v-model="editedItem.name"
                                            label="Name"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                          :rules="phoneRules"
                                          v-model="editedItem.phone"
                                          label="Phone"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row no-gutters>
                                    <v-col>
                                        <v-text-field
                                            :rules="emailRules"
                                            v-model="editedItem.email"
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
        dialog: false,
        dialogDelete: false,
        valid: true,
        nameRules: [
          v => !!v || 'Field is required',
        ],
        phoneRules: [
            v => /((^(\+\d{1,2}\s?)?1?\-?\.?\s?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$)|^$)/.test(v) || 'Phone number must be valid'
        ],
        emailRules: [
            v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        ],
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
    },

    watch: {
        dialog (val) {
            val || this.close()
            this.$refs.form.resetValidation()
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
            if(this.$refs.form.validate()){
                this.save()
            }
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
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
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

                // console.log(item)
                item.password = "password123"

                axios.post('api/user/store', newCompTimePayload)

            }
            
            this.close()
            this.$emit('refresh-users')

        },

    },
}
</script>
