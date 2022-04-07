<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                  <table-header 
                    :title="`${ count } user types records`" 
                    :icon="`fas fa-user fa-fw`" 
                    :icon_text="'Add New'"
                    @openModal="addNew()"
                    @pdfGen="generatePdf" 
                    @excelGen="generateExcel"  
                    @csvGen="generateCsv"
                />
                  <div class="row">
                    <div class="container">
                        <h3 class="card-title"></h3>
                    </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                                <button class="btn btn-success" @click="addNew()"><i class="fas fa-chalkboard-teacher fa-fw"></i> Add New </button>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                                <p class="tec font-weight-bold">Show:
                                    <select name="records" @change="updateRecordsToShow">
                                            <option v-for="record in records" :key="record">{{ record }}</option>
                                    </select>
                                </p>
                        </div>

                        <div class="col-sm-4 col-md-4 col-lg-4">

                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="input-group input-group-sm my-2">
                                <input class="form-control" @keyup="searchUserTypes" type="search" placeholder="Search" aria-label="Search" v-model="search">
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
                        </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_user_types">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Type</th>
                            <th>Modify</th>
                    </tr>

                    </thead>
                  <tbody>


                  <tr v-for="user_type in user_types.data" :key="user_type.id">

                    <td>{{ user_type.id}}</td>
                    <td>{{user_type.name}}</td>
                     <td>
                        <a @click="editModal(user_type)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a @click="deleteUserType(user_type.id)">
                            <i class="fa fa-trash red"></i>
                        </a>

                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="user_types"
                  @pagination-change-page="getUserTypes"
                  ></pagination>
              </div>
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
                    <h5 class="modal-title" v-show="!editMode" id="addNewLabel"><i class="fas fa-plus fa-fw"></i> Add New</h5>
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update <b>{{ form.name }}</b>'s UserType</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateUserType(): createUserType()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="User Type e.g. Teacher"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-danger ml-3 mb-3" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                        <div>
                            <button type="submit" v-show="editMode" class="btn btn-success mr-3 mb-3"><i class="fa fa-edit"></i> Update</button>
                            <button  type="submit" v-show="!editMode" class="btn btn-primary mr-3 mb-3"><i class="fa fa-plus"></i> Create</button>
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
        components:{
            
        },
        data() {
            return {
                editMode:false,
                user_types:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_user_types"),
                filename:'user_types.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    data: new TableData("#table_user_types"),
                    filename: 'user_types.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                   data: new TableData("#table_user_types"),
                    filename: 'user_types.csv'
                })
            },
            getRecordsCount(){
                axios.get('user_types/count').then(data=>this.count = data.data);
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:user_types');
            },
            searchUserTypes:_.debounce(()=>{
                Fire.$emit('searching:user_types');
            }, 1000),
            getUserTypes(page=1){
                axios.get('/user_types?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.user_types = response.data;
                })
            },
            editModal(user_type){
                this.editMode=true;
                this.form.fill(user_type);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateUserType(id){
                this.$Progress.start();
                this.form.post('/user_types/'+this.form.id+'/update')
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
            loadUserTypes(){
                if (this.$gate.isAdmin()) {
                   return  axios.get('user_types?rec_count='+this.active_record_count).then((resp)=>{
                        this.user_types = resp.data;
                        this.count = resp.data.total;
                        return new Promise((resolve, reject)=>resolve(resp));
                    });
                    // this.getRecordsCount();
                }
            },
            createUserType(){
                this.$Progress.start();
                this.form.post('user_types')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'UserType created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteUserType(id){
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
                                this.form.post('user_types/'+id+'/delete').then((resp)=>{
                                    //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'UserType deleted successfully.',
                                        'success'
                                        )
                                    Fire.$emit('afterCreate');
                                }).catch(()=> {
                                    Swal.fire("Failed!", "There was something wrong.", "warning");
                                });
                         }
                    })
            }
        },
        created() {
            this.loadUserTypes();
            Fire.$on('afterCreate',()=>{
                this.loadUserTypes();
            });
            Fire.$on('searching:user_types',()=>{
                let q = this.search;
                axios.get('user_types/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    this.user_types = resp.data;
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the UserTypes table");
                });
            });
            Fire.$on('recordCountChange:user_types',()=>{
                this.loadUserTypes();
            })
        }

    }
</script>
