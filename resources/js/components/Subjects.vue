<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                    <div class="container">
                        <h3 class="card-title">{{ count }} subject records</h3>
                    </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                                <button class="btn btn-success" @click="addNew()"><i class="fas fa-book-open fa-fw"></i> Add New </button>
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
                                <input class="form-control" @keyup="searchSubjects" type="search" placeholder="Search" aria-label="Search" v-model="search">
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
                        </div>

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_subjects">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject Name</th>
                            <th>Subject Id</th>
                            <th>Modify</th>
                    </tr>

                    </thead>
                  <tbody>


                  <tr v-for="subject in subjects.data" :key="subject.id">

                    <td>{{ subject.id}}</td>
                    <td>{{subject.name}}</td>
                    <td>{{subject.subject_id}}</td>
                     <td>
                        <a href="#" @click="editModal(subject)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteSubject(subject.id)">
                            <i class="fa fa-trash red"></i>
                        </a>

                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="subjects"
                  @pagination-change-page="getSubjects"
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
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update <b>{{ form.name }}</b>'s  Subject Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateSubject(): createSubject()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Subject Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                     <div class="form-group">
                        <input v-model="form.subject_id" type="text" name="subject_id"
                            placeholder="Subject ID"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('subject_id')}">
                        <has-error :form="form" field="subject_id"></has-error>
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
import ExportOptionsModal from './ExportOptionsModal.vue';

    export default {
        components:{
            ExportOptionsModal
        },
        data() {
            return {
                editMode:false,
                subjects:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                    subject_id:''
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data:new TableData("#table_subjects") ,
                filename:'subjects.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_subjects',
                    filename: 'subjects.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_subjects',
                    filename: 'subjects.csv'
                })
            },
            getRecordsCount(){
                axios.get('subjects/count').then(data=>this.count = data.data);
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:subjects');
            },
            searchSubjects:_.debounce(()=>{
                Fire.$emit('searching:subjects');
            }, 1000),
            getSubjects(page=1){
                axios.get('/subjects?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.subjects = response.data;
                })
            },
            editModal(subject){
                this.editMode=true;
                this.form.fill(subject);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateSubject(id){
                this.$Progress.start();
                this.form.post('/subjects/'+this.form.id+'/update')
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
                    Swal.fire(
                        'Failed!',
                        'Something was wrong when updating information!',
                        'warning'
                    );
                    console.log(err);
                })

            },
            loadSubjects(){
                if (this.$gate.isAdmin()) {
                    return axios.get('subjects?rec_count='+this.active_record_count).then((resp)=>{
                        this.subjects = resp.data;
                        this.count = resp.data.total;
                        return new Promise((resolve, reject)=>resolve(resp));
                    });
                }
            },
            createSubject(){
                this.$Progress.start();
                this.form.post('subjects')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'Subject created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteSubject(id){
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
                                this.form.delete('subjects/'+id+'/delete').then((resp)=>{
                                    //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'Subject deleted successfully.',
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
            this.loadSubjects();

            Fire.$on('afterCreate',()=>{
                this.loadSubjects();
            });
            Fire.$on('searching:subjects',()=>{
                let q = this.search;
                axios.get('subjects/find?q='+q)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Subjects table");
                    this.subjects = resp.data;
                });
            });
            Fire.$on('recordCountChange:subjects',()=>{
                this.loadSubjects();
            })
        }

    }
</script>
