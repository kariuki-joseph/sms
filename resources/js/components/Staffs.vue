<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="row">
                    <div class="container">
                        <h3 class="card-title">{{ count }} staff records</h3>
                    </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                                <button class="btn btn-success" @click="addNew()"><i class="fas fa-user fa-fw"></i> Add New </button>
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
                                <input class="form-control" @keyup="searchStaffs" type="search" placeholder="Search" aria-label="Search" v-model="search">
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
                        </div>

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover" id="table_staffs">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Staff Type
                                <select @change="filterByStaffType">
                                    <option value="">--Filter by Staff Type--</option>
                                    <option v-for="staff_type in staff_types" :key="staff_type.id"> {{ staff_type.name }}</option>
                                </select>
                            </th>
                            <th>Modify</th>
                    </tr>

                    </thead>
                  <tbody>


                  <tr v-for="staff in staffs.data" :key="staff.id">

                    <td>{{ staff.id}}</td>
                    <td><router-link :to="{path:'/staffs/'+staff.id+'/profile'}">{{staff.name}} </router-link></td>
                    <td>{{staff.phone}}</td>
                    <td>{{staff.email}}</td>
                    <td>{{staff.staff_types[0].name | upText}}</td>
                    <td>
                        <a href="#" @click="editModal(staff)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteStaff(staff.id)">
                            <i class="fa fa-trash red"></i>
                        </a>

                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="staffs"
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
                    <h5 class="modal-title" v-show="!editMode" id="addNewLabel"><i class="fa fa-staff-plus"></i> Add New</h5>
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-staff-edit"></i> Update <b>{{ form.name }}'s</b> Staff Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateStaff(): createStaff()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.name" type="text" name="name"
                            placeholder="Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>


                    <div class="form-group">
                        <input v-model="form.birth_certificate_number" type="number" name="birth_certificate_number"
                            placeholder="Birth Certificate Number"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('birth_certificate_number')}">
                        <has-error :form="form" field="birth_certificate_number"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.id_number" type="number" name="id_number"
                            placeholder="ID Number"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('id_number')}">
                        <has-error :form="form" field="id_number"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.phone" type="number" name="phone"
                            placeholder="Phone Number"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('phone')}">
                        <has-error :form="form" field="phone"></has-error>
                    </div>

                     <div class="form-group">
                        <input v-model="form.email" type="email" name="email"
                            placeholder="Email Address"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email')}">
                        <has-error :form="form" field="email"></has-error>
                    </div>

                     <div class="form-group">
                         <label for="staff_type">Staff Type</label>
                        <select v-model="form.staff_type" name="staff_type" id="staffType" class="form-control" :class="{'is-invalid': form.errors.has('staff_type')}">
                            <option value="">Select Staff Type</option>
                            <option v-for="staff_type in staff_types" :key="staff_type.id">{{ staff_type.name }}</option>
                        </select>
                        <has-error :form="form" field="staff_type"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.kra" type="text" name="kra"
                            placeholder="KRA PIN"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('kra')}">
                        <has-error :form="form" field="kra"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.gross_salary" type="number" name="gross_salary"
                            placeholder="Gross Salary"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('gross_salary')}">
                        <has-error :form="form" field="gross_salary"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.nhif" type="text" name="nhif"
                            placeholder="NHIF"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('nhif')}">
                        <has-error :form="form" field="nhif"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.tsc" type="text" name="tsc"
                            placeholder="TSC Number"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('tsc')}">
                        <has-error :form="form" field="tsc"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.location" type="text" name="location"
                            placeholder="Location"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('location')}">
                        <has-error :form="form" field="location"></has-error>
                    </div>

                    <div class="form-group">
                        <label for="documents">Other Documents</label>
                    </div>

                    <div class="form-group">
                        <label for="passport">Recent Colored Passport</label><br>
                        <input @change="loadDocument" id="passport" type="file" name="passport"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('passport')}">
                        <has-error :form="form" field="passport"></has-error>
                    </div>

                    <div class="form-group">
                        <label for="is_card">National Id : pfd format</label><br>
                        <input @change="loadDocument" id="id_card" type="file" name="id_card" class="form-control"
                         :class="{ 'is-invalid': form.errors.has('id_card')}">
                        <has-error :form="form" field="id_card"></has-error>
                    </div>

                    <div class="form-group">
                        <label for="birth_certificate">Birth Certificate : pdf format</label><br>
                        <input @change="loadDocument" id="birth_certificate" type="file" name="birth_certificate"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('birth_certificate')}">
                        <has-error :form="form" field="birth_certificate"></has-error>
                    </div>

                </div>
                <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-danger ml-3 mb-3" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                        <div>
                            <button type="submit" v-show="editMode" class="btn btn-success mr-3 mb-3"><i class="fa fa-staff-edit"></i> Update</button>
                            <button  type="submit" v-show="!editMode" class="btn btn-primary mr-3 mb-3"><i class="fa fa-staff-plus"></i> Create</button>
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
                staffs:{},
                count: 0,
                staff_types:{},
                form: new Form({
                    id:'',
                    name:'',
                    email:'',
                    birth_certificate_number:'',
                    id_number:'',
                    phone:'',
                    staff_type:'',
                    kra:'',
                    gross_salary:'',
                    nhif:'',
                    tsc:'',
                    'passport':'',
                    'id_card':'',
                    'birth_certificate':'',
                    location:''
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_staffs"),
                filename:'staffs.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_staffs',
                    filename: 'staffs.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_staffs',
                    filename: 'staffs.csv'
                })
            },
            loadDocument(e){
                let documentName = e.target.name;
                let file = e.target.files[0];
                let parsedFile;
                let reader = new FileReader();

                reader.onloadend = () =>{
                    parsedFile = reader.result;
                    switch(documentName){
                        case 'id_card':
                            this.form.id_card=parsedFile;
                            break;
                        case 'passport':
                            this.form.passport=parsedFile;
                            break;
                        case 'birth_certificate':
                            this.form.birth_certificate=parsedFile;
                            break;
                    }
                }

                reader.readAsDataURL(file);

            },
            filterByStaffType(e){
                let staffType = e.target.value;
                axios.get('staffs?rec_count='+this.active_record_count+'&staff_type='+staffType).then((resp)=>{
                    this.staffs = resp.data;
                    this.loadStaffTypes();
                    });
            },
            loadStaffTypes(){
            axios.get('user_types').then(data=>this.staff_types = data.data.data);
            },
            getRecordsCount(staffType=null){
                if(staffType != null){
                    axios.get('staffs/count?staff_type='+staffType).then(data=>this.count = data.data);
                    return;
                }else{
                    axios.get('staffs/count').then(data=>this.count = data.data);
                }
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:staffs');
            },
            searchStaffs:_.debounce(()=>{
                Fire.$emit('searching:staffs');
            },1000),
            getResults(page=1){
                axios.get('staffs?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.staffs = response.data;
                })
            },
            editModal(staff){
                this.editMode=true;
                this.form.fill(staff);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateStaff(id){
                this.$Progress.start();
                this.form.post('staffs/'+this.form.id+'/update')
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
                    Fire.$emit('afterCreate:staffs');
                }).catch(err=>{
                    this.$Progress.fail();
                    console.log(err);
                })

            },
            loadStaffs(){
                if (this.$gate.isAdmin()) {
                  this.loadStaffTypes();
                  return  axios.get('staffs?rec_count='+this.active_record_count).then((resp)=>{
                      this.staffs = resp.data
                      this.count = resp.data.total;
                      return new Promise((resolve, reject)=>resolve(resp));
                  });
                }
            },
            createStaff(){
                this.$Progress.start();
                this.form.post('staffs')
                .then((response)=>{
                    Fire.$emit('afterCreate:staffs');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));

                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'Staff created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            deleteStaff(id){
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
                                this.form.delete('staffs/'+id).then((resp)=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        );
                                        //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                    Fire.$emit('afterCreate:staffs');
                                }).catch(()=> {
                                    Swal.fire("Failed!", "There was something wrong.", "warning");
                                });
                         }
                    })
            }
        },
        created() {
            this.loadStaffs();
            Fire.$on('afterCreate:staffs',()=>{
                this.loadStaffs();
            });
            Fire.$on('searching:staffs',()=>{
                let q = this.search;
                axios.get('staffs/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    this.staffs = resp.data;
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the staffs table");
                });
            });
            Fire.$on('recordCountChange:staffs',()=>{
                this.loadStaffs();
            });
        }
    }
</script>
