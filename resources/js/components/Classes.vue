<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                  <div class="row">
                    <div class="container">
                        <h3 class="card-title">{{ count }} class records</h3>
                    </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                                <button class="btn btn-success" @click="addNew()"><i class="fas fa-school fa-fw"></i> Add New </button>
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
                                <input class="form-control" @keyup="searchClasses" type="search" placeholder="Search" aria-label="Search" v-model="search">
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
                        </div>

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_classes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Capacity</th>
                            <th>Class Teacher</th>
                            <th>Modify</th>
                    </tr>
                    </thead>
                  <tbody>
                  <tr v-for="_class in classes.data" :key="_class.id">

                    <td>{{ _class.id}}</td>
                    <td>{{_class.name}}</td>
                    <td>{{_class.capacity}}</td>
                    <td>{{_class.class_teacher == null ? "Not Yet Assigned" :_class.class_teacher.name}}</td>
                     <td>
                        <a href="#" @click="editModal(_class)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteClass(_class.id)">
                            <i class="fa fa-trash red"></i>
                        </a>

                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="classes"
                  @pagination-change-page="getClasses"
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
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update <b>{{ form.name }}</b>'s Class Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateClass(): createClass()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Class Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.capacity" type="number" name="capacity"
                            placeholder="Class Capacity"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('capacity')}">
                        <has-error :form="form" field="capacity"></has-error>
                    </div>

                    <div class="form-group">
                        <label for="clas_teacher">Select Class Teacher</label>
                        <select v-model="form.class_teacher" name="class_teacher"  class="form-control" :class="{ 'is-invalid': form.errors.has('class_teacher')}">
                            <option value="">--Select Class Teacher--</option>
                            <option v-for="teacher in teachers" :key="teacher.id"> {{ teacher.name }} </option>
                        </select>
                        <has-error :form="form" field="class_teacher"></has-error>
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

            <!--export options modal-->
            <export-options-modal @pdfGen="generatePdf" @excelGen="generateExcel"  @csvGen="generateCsv"></export-options-modal>
            <!--/ export options modal-->
    </div>



</template>

<script>
import ExportOptionsModal from "./ExportOptionsModal.vue";
    export default {
        components:{
            ExportOptionsModal
        },
        data() {
            return {
                editMode:false,
                classes:{},
                teachers:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                    capacity:'',
                    class_teacher:'',
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_classes"),
                filename:'classes.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_classes',
                    filename: 'classes.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_classes',
                    filename: 'classes.csv'
                })
            },
            getAvailableTeachers(){
                axios.get('teachers?tag=available').then(data=>this.teachers=data.data);
            },
            getRecordsCount(){
                axios.get('classes/count').then(data=>this.count = data.data);
            },
            triggerPdfGenerate(){
                this.$parent.generatePDF('classes');
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:classes');
            },
            searchClasses:_.debounce(()=>{
                Fire.$emit('searching:classes');
            }, 1000),
            getClasses(page=1){
                axios.get('/classes?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.classes = response.data;
                })
            },
            editModal(_class){
                this.editMode=true;
                this.form.fill(_class);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateClass(id){
                this.$Progress.start();
                this.form.post('/classes/'+this.form.id+'/update')
                .then((response)=>{
                    //create a log
                    this.$parent.createLog("Updated a record :"+response.data.original_record+":"+response.data.updated_record);

                    Fire.$emit('afterCreate:staffs');
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
            loadClasses(){
                if (this.$gate.isAdmin()) {
                   return  axios.get('classes?rec_count='+this.active_record_count).then((resp)=>{
                       this.classes = resp.data;
                       this.count = resp.data.total;

                       return new Promise((resolve, reject)=>resolve(resp));
                   }),
                    // this.getRecordsCount();
                    this.getAvailableTeachers();
                }
            },
            createClass(){
                this.$Progress.start();
                this.form.post('classes')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'Class created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteClass(id){
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
                                this.form.post('classes/'+id+'/delete').then((resp)=>{
                                       //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'Class deleted successfully.',
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
            this.loadClasses();
            Fire.$on('afterCreate',()=>{
                this.loadClasses();
            });
            Fire.$on('searching:classes',()=>{
                let q = this.search;
                axios.get('classes/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Classes table");
                    this.classes = resp.data;
                });
            });
            Fire.$on('recordCountChange:classes',()=>{
                this.loadClasses();
            })
        }

    }
</script>
