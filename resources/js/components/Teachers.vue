<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <table-header 
                    :title="'Teachers Records'" 
                    :icon="`fas fa-chalkboard-teacher fa-fw`" 
                    :icon_text="'Add New'"
                    @openModal="addNew()"
                    @pdfGen="generatePdf" 
                    @excelGen="generateExcel"  
                    @csvGen="generateCsv"
                />
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_teachers">
                  <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Modify</th>
                    </tr>

                    </thead>
                    <tbody></tbody>
                </table>
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
                    <h5 class="modal-title" v-show="!editMode" id="addNewLabel"><i class="fas fa-plus fa-fw"></i> Add New</h5>
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update <b>{{ form.name }}</b>'s Teacher</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateTeacher(): createTeacher()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.email" type="text" name="email"
                            placeholder="Email"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email')}">
                        <has-error :form="form" field="email"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.phone" type="number" name="phone"
                            placeholder="Phone"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('phone')}">
                        <has-error :form="form" field="phone"></has-error>
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
import TableHeader from './TableHeader.vue';

    export default {
        components:{
            
        },
        
        data() {
            return {
                editMode:false,
                teachers:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                    email:'',
                    phone:'',
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
                dataTable:''
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_teachers"),
                filename:'teachers.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    data: new TableData("#table_teachers"),
                    filename: 'teachers.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    data: new TableData("#table_teachers"),
                    filename: 'teachers.csv'
                })
            },
            getRecordsCount(){
                axios.get('teachers/count').then(data=>this.count = data.data);
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:teachers');
            },
            searchTeachers:_.debounce(()=>{
                Fire.$emit('searching:teachers');
            }, 1000),
            getTeachers(page=1){
                axios.get('teachers?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.teachers = response.data;
                })
            },
            editModal(teacher){
                this.editMode=true;
                this.form.fill(teacher);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateTeacher(id){
                this.$Progress.start();
                this.form.post('teachers/'+this.form.id+'/update')
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
            loadTeachers(){
                if (this.$gate.isAdmin()) {
                    // return axios.get('teachers?rec_count='+this.active_record_count).then((resp)=>{
                    //     this.teachers = resp.data;
                    //     this.count = resp.data.total;
                    //     return new Promise((resolve, reject)=>resolve(resp));
                    // });

                // datatable ajax refresh
                this.dataTable.ajax? this.dataTable.ajax.reload():'';

                }
            },
            createTeacher(){
                this.$Progress.start();
                this.form.post('teachers')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'Teacher created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteTeacher(id){
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
                                this.form.post('teachers/'+id+'/delete').then((resp)=>{
                                    //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'House deleted successfully.',
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
            this.loadTeachers();
            Fire.$on('afterCreate',()=>{
                this.loadTeachers();
            });
            Fire.$on('searching:teachers',()=>{
                let q = this.search;
                axios.get('teachers/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Teachers table");
                    this.teachers = resp.data;
                });
            });
            Fire.$on('recordCountChange:teachers',()=>{
                this.loadTeachers();
            })
        },
        mounted(){
            this.dataTable = $("#table_teachers").DataTable({
                processing:true,
                select:true,
                pageLength:25,
                scrollY:'500px',
                ajax:{
                    type:'GET',
                    url:'teachers',
                    dataSrc: function(data){
                        let i,teacher, resp=[];
                        for(i=0; i<data.length; i++){
                            teacher = data[i];
                            resp.push({
                                id:teacher.id,
                                name:teacher.name,
                                email: teacher.email,
                                phone: teacher.phone,
                                modify:`
                                    <a @click="editModal(${teacher})">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    /
                                    <a @click="deleteTeacher(${teacher.id})">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                `
                            })
                        }
                        return resp
                    }
                },
                columns:[
                    {data:'id'},
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'modify'}
                ]
            })
        }

    }
</script>
