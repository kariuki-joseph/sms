<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <!--Card header -->
              <table-header :title="`${count} Houses Records`" :icon="`fas fa-house-user fa-fw`" :icon_text="'Add New'"
               @openModal="addNew()"
               @msearching="searchHouses"
               @recordsCountChanged="updateRecordsToShow"></table-header>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0 ">
                <table class="table table-hover" id="table_houses">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Capacity</th>
                            <th>Modify</th>
                    </tr>

                    </thead>
                  <tbody>


                  <tr v-for="house in houses.data" :key="house.id">

                    <td>{{ house.id}}</td>
                    <td>{{house.name}}</td>
                    <td>{{house.capacity}}</td>
                     <td>
                        <a href="#" @click="editModal(house)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteHouse(house.id)">
                            <i class="fa fa-trash red"></i>
                        </a>

                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="houses"
                  @pagination-change-page="getHouses"
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
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update <b>{{ form.name }}</b>'s House Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateHouse(): createHouse()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="House Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.capacity" type="number" name="capacity"
                            placeholder="House Capacity"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('capacity')}">
                        <has-error :form="form" field="capacity"></has-error>
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
                houses:{},
                count: 0,
                form: new Form({
                    id:'',
                    name:'',
                    capacity:''
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_houses"),
                filename:'houses.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_houses',
                    filename: 'houses.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_houses',
                    filename: 'houses.csv'
                })
            },
            getRecordsCount(){
                axios.get('houses/count').then(data=>this.count = data.data);
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(records){
                this.active_record_count = records;
                Fire.$emit('recordCountChange:houses');
            },
            searchHouses:_.debounce((search)=>{
                Fire.$emit('searching:houses', search);
                 }, 1000),
            getHouses(page=1){
                axios.get('/houses?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.houses = response.data;
                })
            },
            editModal(house){
                this.editMode=true;
                this.form.fill(house);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateHouse(id){
                this.$Progress.start();
                this.form.post('/houses/'+this.form.id+'/update')
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
            loadHouses(){
                if (this.$gate.isAdmin()) {
                   return  axios.get('houses?rec_count='+this.active_record_count).then((resp)=>{
                       this.houses = resp.data;
                       this.count = resp.data.total;
                      return new Promise((resolve, reject)=>resolve(resp));
                   });
                    this.getRecordsCount();
                }
            },
            createHouse(){
                this.$Progress.start();
                this.form.post('houses')
                .then((response)=>{
                    Fire.$emit('afterCreate');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'House created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteHouse(id){
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
                                this.form.post('houses/'+id+'/delete').then((resp)=>{
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
            this.loadHouses();
            Fire.$on('afterCreate',()=>{
                this.loadHouses();
            });
            Fire.$on('searching:houses',(search)=>{
                let q = search;
                axios.get('houses/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Houses table");
                    this.houses = resp.data;
                });
            });
            Fire.$on('recordCountChange:houses',()=>{
                this.loadHouses();
            });
        }

    }
</script>
