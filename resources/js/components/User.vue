<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                  <table-header 
                        :title="'Users Records'" 
                        :icon="`fas fa-users fa-fw`" 
                        :icon_text="'Add New'"
                        @openModal="addNew()"
                        @pdfGen="generatePdf" 
                        @excelGen="generateExcel"  
                        @csvGen="generateCsv"
                    />
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_users">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Registered At</th>
                            <th>Modify</th>
                         </tr>
                    </thead>
                  <tbody>
                  <tr v-for="user in users.data" :key="user.id">

                        <td>{{ user.id}}</td>
                        <td>{{user.name}}</td>
                        <td>{{user.email}}</td>
                        <td>{{user.type | upText}}</td>
                        <td>{{user.created_at|myDate }}</td>

                        <td>
                            <a @click="editModal(user)">
                                <i class="fa fa-edit blue"></i>
                            </a>
                            /
                            <a @click="deleteUser(user.id)">
                                <i class="fa fa-trash red"></i>
                            </a>

                        </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <div v-if="!$gate.isAdmin()">
               <not-found></not-found>
        </div>

    <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editMode" id="addNewLabel"><i class="fa fa-user-plus"></i> Add New</h5>
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-user-edit"></i> Update <b>{{ form.name }}'s</b> User Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateUser(): createUser()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.email" type="email" name="email"
                            placeholder="Email Address"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email')}">
                        <has-error :form="form" field="email"></has-error>
                    </div>

                     <div class="form-group">
                        <select v-model="form.user_type" name="user_type" id="user_type" class="form-control" :class="{'is-invalid': form.errors.has('user_type')}">
                            <option value="">Select User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard User</option>
                            <option value="author">Author</option>
                        </select>
                        <has-error :form="form" field="user_type"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.password" type="password" name="password" id="password"
                        placeholder="Password"
                        class="form-control" :class="{'is-invalid': form.errors.has('password')}">
                        <has-error :form="form" field="password"></has-error>
                    </div>

                </div>
                <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-danger ml-3 mb-3" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                        <div>
                            <button type="submit" v-show="editMode" class="btn btn-success mr-3 mb-3"><i class="fa fa-user-edit"></i> Update</button>
                            <button  type="submit" v-show="!editMode" class="btn btn-primary mr-3 mb-3"><i class="fa fa-user-plus"></i> Create</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</template>

<script>
  export default {
    components: {
            
            },
        data() {
            return {
                editMode:false,
                users:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                    email:'',
                    password:'',
                    user_type:'',
                    bio:'',
                    photo:'',
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_users"),
                filename:'users.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    data: new TableData("#table_users"),
                    filename: 'users.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    data: new TableData("#table_users"),
                    filename: 'users.csv'
                })
            },
            getRecordsCount(){
                axios.get('api/count').then(data=>this.count = data.data);
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:users');
            },
            searchUsers:_.debounce(()=>{
                Fire.$emit('searching:users');
            },1000),
            getResults(page=1){
                axios.get('api/user?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.users = response.data;
                })
            },
            editModal(user){
                this.editMode=true;
                this.form.fill(user);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateUser(id){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id)
                .then((response)=>{
                    //create a log
                    this.$parent.createLog("Updated a record :"+response.data.original_record+":"+response.data.updated_record);
                    $('#addNew').modal('hide');
                    this.$Progress.finish();

                    Swal.fire(
                        'Updated!',
                        'Information has been updated!',
                        'success'
                    );
                    Fire.$emit('afterCreate');
                }).catch(err=>{
                    this.$Progress.fail();
                    console.log(err);
                })

            },
            loadUsers(){
                if (this.$gate.isAdmin()) {
                   return  axios.get('api/user?rec_count='+this.active_record_count).then((resp)=>{
                        this.users = resp.data;
                        this.count = resp.data.total;
                        console.log("Total Records: "+JSON.stringify(resp.data.total))
                        return new Promise((resolve, reject)=>resolve(resp));
                        });
                }
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/user')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'User created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        // Send request to the server
                         if (result.value) {
                                this.form.delete('api/user/'+id).then((resp)=>{
                                    //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        )
                                    Fire.$emit('afterCreate');
                                }).catch(()=> {
                                    Swal.fire("Failed!", "There was something wrong.", "warning");
                                });
                         }
                    })
            },
        },
        created() {
            console.log("User component has been created")
            this.loadUsers();
            Fire.$on('afterCreate',()=>{
                this.loadUsers();
            });
            Fire.$on('searching:users',()=>{
                let q = this.search;
                axios.get('api/findUser?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Teachers table");
                    this.users = resp.data;
                });
            });
            Fire.$on('recordCountChange:users',()=>{
                this.loadUsers();
            });

          },
        mounted(){
        }

    }
</script>
