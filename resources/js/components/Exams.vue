<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_exams">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Exam Name</th>
                        <th>Exam Id</th>
                        <th>Modify</th>
                  </tr>
                </thead>
                  <tbody>
                  <tr v-for="exam in exams.data" :key="exam.id">

                    <td>{{ exam.id}}</td>
                    <td>{{exam.name}}</td>
                    <td>{{exam.exam_id}}</td>
                     <td>
                        <a href="#" @click="editModal(exam)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteExam(exam.id)">
                            <i class="fa fa-trash red"></i>
                        </a>

                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="exams"
                  @pagination-change-page="getExams"
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
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update <b>{{ form.name }}</b>' Exam Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateExam(): createExam()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Exam Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.exam_id" type="text" name="exam_id"
                            placeholder="Exam ID"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('exam_id')}">
                        <has-error :form="form" field="exam_id"></has-error>
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
                exams:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                    exam_id:''
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_exams"),
                filename:'exams.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_exams',
                    filename: 'exams.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_exams',
                    filename: 'exams.csv'
                })
            },
            getRecordsCount(){
                axios.get('exams/count').then(data=>this.count = data.data);
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:exams');
            },
            searchExams:_.debounce(()=>{
                Fire.$emit('searching:exams');
            }, 1000),
            getExams(page=1){
                axios.get('/exams?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.exams = response.data;
                })
            },
            editModal(exam){
                this.editMode=true;
                this.form.fill(exam);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateExam(id){
                this.$Progress.start();
                this.form.post('/exams/'+this.form.id+'/update')
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
            loadExams(){
                if (this.$gate.isAdmin()) {
                  return   axios.get('exams?rec_count='+this.active_record_count).then((resp)=>{
                      this.exams = resp.data;
                      this.count = resp.data.total;
                      return new Promise((resolve, reject)=>resolve(resp));
                  });
                }
            },
            createExam(){
                this.$Progress.start();
                this.form.post('exams')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'Exam created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteExam(id){
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
                                this.form.post('exams/'+id+'/delete').then((resp)=>{
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
            this.loadExams();

            Fire.$on('afterCreate',()=>{
                this.loadExams();
            });
            Fire.$on('searching:exams',()=>{
                let q = this.search;
                axios.get('exams/find?q='+q)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Exams table");
                    this.exams = resp.data;
                });
            });
            Fire.$on('recordCountChange:exams',()=>{
                this.loadExams();
            })
        },
        mounted(){
            $("#table_exams").DataTable({
                dom:'<"toolbar">frtip'
            });
            $('div.toolbar').html(`
                <div class="row">
                    <div class="container">
                        <h3 class="card-title">{{ count }} exam records</h3>
                    </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                                <button class="btn btn-success" @click="addNew()"><i class="fas fa-book fa-fw"></i> Add New </button>
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
                                <input class="form-control" @keyup="searchExams" type="search" placeholder="Search" aria-label="Search" v-model="search">
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
                        </div>

                </div>
            `)
        }

    }
</script>
