<template>
<div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                    <div class="container">
                        <h3 class="card-title">{{ count }} marks records</h3>
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
                                <input class="form-control" @keyup="searchMarks" type="search" placeholder="Search" aria-label="Search" v-model="search">
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
                        </div>

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_marks">
                    <thead>
                        <tr>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Modify</th>
                         </tr>
                    </thead>
                  <tbody>
                  <tr v-for="mark in marks.data" :key="mark.id">
                        <td>{{ mark.score }}</td>
                        <td>{{mark.grade}}</td>
                        <td>{{mark.remark | upText}}</td>
                        <td>
                            <a href="#" @click="editModal(mark)">
                                <i class="fa fa-edit blue"></i>
                            </a>
                            /
                            <a href="#" @click="deleteMark(mark.id)">
                                <i class="fa fa-trash red"></i>
                            </a>

                        </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="marks"
                  @pagination-change-page="getResults"
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
                    <h5 class="modal-title" v-show="!editMode" id="addNewLabel"><i class="fa fa-mark-plus"></i> Add New</h5>
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-mark-edit"></i> Update <b>{{ form.name }}'s</b> mark Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateMarks(): createMarks()">
                <div class="modal-body">
                     <div class="form-group">
                       <div class="slider-green">
                            <input type="text" value="" class="slider form-control"
                                data-slider-min="0"
                                data-slider-max="100"
                                data-slider-step="1"
                                :data-slider-value="`[${form.min_score},${form.max_score}]`"
                                data-slider-orientation="horizontal"
                                data-slider-selection="before"
                                data-slider-tooltip="show">
                        </div>
                         <div class="text-center">{{ form.min_score }} - {{ form.max_score }} marks</div>
                    </div>

                     <div class="form-group">
                        <input v-model="form.grade" type="text" name="grade"
                            placeholder="Enter Grade e.g. A"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('grade')}">
                        <has-error :form="form" field="grade"></has-error>
                    </div>
                    <div class="form-group">
                        <input v-model="form.remark" type="text" name="remark" id="remark"
                        placeholder="Enter Remark e.g. Excellent"
                        class="form-control" :class="{'is-invalid': form.errors.has('remark')}">
                        <has-error :form="form" field="remark"></has-error>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-danger ml-3 mb-3" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                        <div>
                            <button type="submit" v-show="editMode" class="btn btn-success mr-3 mb-3"><i class="fa fa-mark-edit"></i> Update</button>
                            <button  type="submit" v-show="!editMode" class="btn btn-primary mr-3 mb-3"><i class="fa fa-mark-plus"></i> Create</button>
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
    components: {
            ExportOptionsModal
            },
        data() {
            return {
                editMode:false,
                marks:{},
                count: 0,
                form: new Form({
                    id:'',
                    min_score:'0',
                    max_score:'40',
                    grade:'',
                    remark:'',
                }),

                max_score_taken:0,
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_marks"),
                filename:'marks.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_marks',
                    filename: 'marks.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_marks',
                    filename: 'marks.csv'
                })
            },
            getResults(page=1){
                axios.get('/marks?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.marks = response.data;
                })
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:marks');
            },
            searchMarks:_.debounce(()=>{
                Fire.$emit('searching:marks');
            },1000),
            editModal(mark){
                this.editMode=true;
                this.form.fill(mark);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateMarks(id){
                this.$Progress.start();
                this.form.post('marks/'+this.form.id+"/update")
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
                    Fire.$emit('afterCreate:marks');
                }).catch(err=>{
                    this.$Progress.fail();
                    console.log(err);
                })

            },
            loadMarks(){
                if (this.$gate.isAdmin()) {
                   return  axios.get('marks?rec_count='+this.active_record_count).then((resp)=>{
                        this.marks = resp.data;
                        this.count = resp.data.total;
                        console.log("Total Records: "+JSON.stringify(resp.data.total))
                        return new Promise((resolve, reject)=>resolve(resp));
                        });
                }
            },
            createMarks(){
                this.$Progress.start();
                this.form.post('marks')
                .then((response)=>{
                    Fire.$emit('afterCreate:marks');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'mark created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteMark(id){
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
                                this.form.post('marks/'+id+"/delete").then((resp)=>{
                                    //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        )
                                    Fire.$emit('afterCreate:marks');
                                }).catch(()=> {
                                    Swal.fire("Failed!", "There was something wrong.", "warning");
                                });
                         }
                    })
            },
        },
        created() {
            this.loadMarks();
            Fire.$on('afterCreate:marks',()=>{
                this.loadMarks();
            });
            Fire.$on('searching:marks',()=>{
                let q = this.search;
                axios.get('marks/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Marks table");
                    this.marks = resp.data;
                });
            });
            Fire.$on('recordCountChange:marks',()=>{
                this.loadMarks();
            });

          },
        mounted(){
            //add scores to choose from
            let slider = $('.slider').bootstrapSlider();
            let value;
            slider.on('slide', ()=>{
                value = slider.bootstrapSlider('getValue');
                this.form.min_score = value[0];
                this.form.max_score = value[1];
                })
        },
        computed:{

        }

    }
</script>
