<template>
    <div class="container">
        <div class="row mt-2" v-if="$gate.isAdmin()">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-pills">
                        <li class="nav-item mr-1"><a href="#general_settings" class="nav-link active" data-toggle="tab">General Settings</a></li>
                        <li class="nav-item"><a href="#update_logo" class="nav-link" data-toggle="tab"> Update Logo</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!--General settings tab-->
                            <div class="tab-pane active show" id="general_settings">
                                <div class="row" v-if="$gate.isAdmin()">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <table-header 
                                                    :title="'School Information'" 
                                                    :icon="`fas fa-cog fa-fw`" 
                                                    :icon_text="'New Settings'"
                                                    @openModal="addNew()"
                                                    @pdfGen="generatePdf" 
                                                    @excelGen="generateExcel"  
                                                    @csvGen="generateCsv"
                                                />
                                            </div>
                                        <!-- /.card-header -->
                                            <div class="card-body table-responsive p-0">
                                                <table class="table table-hover" id="table_settings">
                                                    <thead>
                                                        <tr>
                                                            <th>School Name</th>
                                                            <th>School Motto</th>
                                                            <th>School Vission</th>
                                                            <th>Misssion Statement</th>
                                                            <th>Email Address</th>
                                                            <th>Post Office Address</th>
                                                            <th>Phone No.</th>
                                                            <th>Modify</th>
                                                        </tr>

                                                    </thead>
                                                    <tbody>


                                                    <tr v-for="record in settings.data" :key="record.id">
                                                        <td>{{record.sch_name}}</td>
                                                        <td>{{record.sch_motto}}</td>
                                                        <td>{{record.sch_vission}}</td>
                                                        <td>{{record.sch_mission}}</td>
                                                        <td>{{record.email}}</td>
                                                        <td>{{ record.po_address}}</td>
                                                        <td>{{ record.phone}}</td>

                                                        <td>
                                                            <a @click="editModal(record)">
                                                                <i class="fa fa-edit blue"></i>
                                                            </a>
                                                            /
                                                            <a @click="deleteSettings(record.id)">
                                                                <i class="fa fa-trash red"></i>
                                                            </a>

                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <!-- /.card-body -->
                                         </div>
                                        <!-- /.card -->
                                    </div>
                                </div>
                            </div>
                            <!--/ General settings tab-->

                            <!-- Update logo tab -->
                            <div class="tab-pane" id="update_logo">
                                <div class="row">
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <img id="school_logo" :src="getLogo()" alt="School Logo" class="w-100 h-100">
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <form @submit.prevent="submitUpdatedLogo()" id="logoForm">
                                            <div class="form-group">
                                                <label for="logo">Pick a new Logo</label>
                                                <input type="file" name="logo" id="" @change="updateLogo">
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" id="" class="btn btn-primary"><i class="fa fa-edit"></i> Confirm and Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--/ Update logo tab -->
                        </div>
                    </div>

                </div>
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
                    <h5 class="modal-title" v-show="!editMode" id="addNewLabel"><i class="fa fa-plus"></i> Create Setting</h5>
                    <h5 class="modal-title" v-show="editMode" id="addNewLabel"><i class="fa fa-edit"></i> Update Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode ? updateSetting(): createSetting()">
                <div class="modal-body">
                     <div class="form-group">
                        <input v-model="form.sch_name" type="text" name="sch_name"
                            placeholder="School Name"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('sch_name') }">
                        <has-error :form="form" field="sch_name"></has-error>
                    </div>

                    <div class="form-group">
                        <textarea rows="3" v-model="form.sch_motto" name="sch_motto"
                            placeholder="School Motto"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('sch_motto') }"></textarea>
                        <has-error :form="form" field="sch_motto"></has-error>
                    </div>

                    <div class="form-group">
                        <textarea rows="5" v-model="form.sch_vission" type="text" name="sch_vission"
                            placeholder="School Vission"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('sch_vission') }"></textarea>
                        <has-error :form="form" field="sch_vission"></has-error>
                    </div>

                    <div class="form-group">
                        <textarea rows="5" v-model="form.sch_mission" type="text" name="sch_mission"
                            placeholder="Mission Statement"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('sch_mission') }"></textarea>
                        <has-error :form="form" field="sch_mission"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.email" type="email" name="email"
                            placeholder="Email Address"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('email')}">
                        <has-error :form="form" field="email"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.phone" type="tel" name="phone"
                            placeholder="Phone"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('phone')}">
                        <has-error :form="form" field="phone"></has-error>
                    </div>

                    <div class="form-group">
                        <input v-model="form.po_address" type="text" name="po_address"
                            placeholder="Post Office Address"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('po_address') }">
                        <has-error :form="form" field="po_address"></has-error>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-danger ml-3 mb-3" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                        <div>
                            <button type="submit" v-show="editMode" class="btn btn-success mr-3 mb-3"><i class="fa fa-edit"></i> Update</button>
                            <button  type="submit" v-show="!editMode" class="btn btn-primary mr-3 mb-3"><i class="fa fa-paper-plane"></i> Submit</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>



</template>

<script>
import TableHeader  from "./TableHeader";
    export default {
        components:{
            
        },
        data() {
            return {
                editMode:false,
                settings:{},
                count: 0,
                form: new Form({
                    id: '',
                    sch_name:'',
                    sch_motto:'',
                    sch_vission:'',
                    sch_mission:'',
                    email:'',
                    po_address:'',
                    phone:'',
                    logo:'',
                }),
                formLogo: new Form({
                    id: '',
                    logo: ''
                }),
                search: '',
                active_record_count: 10,
                records:this.$parent.records,
            }
        },
        methods: {
            generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_settings"),
                filename:'settings.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    data: new TableData("#table_settings"),
                    filename: 'settings.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    data: new TableData("#table_settings"),
                    filename: 'settings.csv'
                })
            },
            getRecordsCount(){
                axios.get('settings/count').then(data=>this.count = data.data);
            },
            submitUpdatedLogo(){
                this.$Progress.start();
                this.formLogo.post('settings/updateLogo').then(data=>{
                    //create a log
                     this.$parent.createLog("Updated the School Logo");

                    Fire.$emit('afterCreate:staffs');
                    Swal.fire(
                        'Updated!',
                        'Logo has been updated!',
                        'success'
                    );
                }).catch(err=>{
                    console.log(err);
                    this.$Progress.fail();
                });
                this.$Progress.finish();
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            updateRecordsToShow(e){
                let newRecordCount = e.target.value;
                this.active_record_count = newRecordCount;
                Fire.$emit('recordCountChange:settings');
            },
            searchSettings:_.debounce(()=>{
                Fire.$emit('searching:settings');
            },1000),
            getResults(page=1){
                axios.get('settings?page='+page+'&rec_count='+this.active_record_count)
                .then(response=>{
                    this.settings = response.data;
                })
            },
            editModal(setting){
                this.editMode=true;
                this.form.fill(setting);
                $('#addNew').modal('show');
            },
            addNew(){
                this.editMode=false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            updateSetting(id){
                this.$Progress.start();
                this.form.post('settings/'+this.form.id+'/update')
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
                    Fire.$emit('afterCreate::settings');
                }).catch(err=>{
                    this.$Progress.fail();
                    console.log(err);
                })

            },
            loadSettings(){
                if (this.$gate.isAdmin()) {
                    return axios.get('settings?rec_count='+this.active_record_count).then((resp)=>{
                        this.settings = resp.data;
                        this.formLogo.id = resp.data.data[0].id;
                        this.formLogo.logo = resp.data.data[0].logo;
                        this.count = resp.data.total;
                        return new Promise((resolve, reject)=>resolve(resp));
                        });
                }
            },
            createSetting(){
                this.$Progress.start();
                this.form.post('settings')
                .then((response)=>{
                    Fire.$emit('afterCreate::settings');
                    //creaate a new log of this
                    this.$parent.createLog("Created a new record:"+JSON.stringify(response.data.last_record));
                    $('#addNew').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: 'Setting created successfully.'
                    });
                    this.$Progress.finish();
                })
                .catch(err=>{
                    this.$Progress.finish();
                    console.log(err);
                });
            },
            updateLogo(e){
                let file = e.target.files[0];
                let reader = new FileReader();

                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file',
                    })
                    return false;
                }

                reader.onloadend = (file) => {
                    this.formLogo.logo = reader.result;
                }
                reader.readAsDataURL(file);
            },
            getLogo(){
                let logo = (this.formLogo.logo.length > 200) ? this.formLogo.logo : this.formLogo.logo;
                return logo;
            },
            deleteSettings(id){
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
                                this.form.delete('settings/'+id+'/delete').then((resp)=>{
                                    //create a log
                                    this.$parent.createLog("Deleted a record :"+resp.data.deleted_record);
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        )
                                    Fire.$emit('afterCreate::settings');
                                }).catch(()=> {
                                    Swal.fire("Failed!", "There was something wrong.", "warning");
                                });
                         }
                    })
            }
        },
        created() {
            this.loadSettings();
            Fire.$on('afterCreate::settings',()=>{
                this.loadSettings();
            });
            Fire.$on('searching:settings',()=>{
                let q = this.search;
                axios.get('settings/find?q='+q+'&rec_count='+this.active_record_count)
                .then(resp=>{
                    //create a log
                 this.$parent.createLog("Searched for: "+q+" in the Settings table");
                    this.settings = resp.data;
                });
            });
            Fire.$on('recordCountChange:settings',()=>{
                this.loadSettings();
            })
        }

    }
</script>
