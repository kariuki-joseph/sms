<template>
    <!-- Content Wrapper. Contains page content -->
  <div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6"></div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3 col-lg-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img v-if="user.documents" class="profile-user-img img-fluid img-circle"
                       :src="getUserProfilePic(user.documents)"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ user.name }}</h3>

                <ul class="list-group list-group-unbordered mb-3" v-if="$attrs.userType == 'students'">
                  <li class="list-group-item">
                    <b>Name</b> <p class="float-right text-muted">{{ user.name }}</p>
                  </li>
                  <li class="list-group-item">
                    <b>Admission No.</b> <p class="float-right text-muted">{{ user.adm_number }}</p>
                  </li>
                  <li class="list-group-item">
                    <b>Class</b> <p class="float-right text-muted" v-if="user.class">{{ user.class.name }}</p>
                  </li>
                </ul>
                <ul class="list-group list-group-unbordered mb-3" v-if="$attrs.userType == 'staffs'">
                  <li class="list-group-item">
                    <b>Name</b> <p class="float-right text-muted">{{ user.name }}</p>
                  </li>
                  <li class="list-group-item">
                    <b>Phone No.</b> <p class="float-right text-muted">{{ user.phone }}</p>
                  </li>
                  <li class="list-group-item">
                    <b>Email</b> <p class="float-right text-muted">{{ user.email }}</p>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>

          <!-- /.col -->
          <div class="col-md-9 col-lg-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#personal" data-toggle="tab">Personal</a></li>
                  <li class="nav-item" v-if="$attrs.userType == 'students'"><a class="nav-link" href="#guardian" data-toggle="tab">Guardian</a></li>
                  <li class="nav-item" v-if="$attrs.userType == 'students'"><a class="nav-link" href="#exams" data-toggle="tab">Exams</a></li>
                  <li class="nav-item" v-if="$attrs.userType == 'students'"><a class="nav-link" href="#fees" data-toggle="tab">Fees</a></li>
                  <li class="nav-item"> <a class="nav-link" href="#documents" data-toggle="tab"> Documents</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="personal">
                    <!-- Post -->
                    <div class="post">
                        <table class="table" v-if="$attrs.userType == 'students'">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Gender</th>
                                    <th>Previous school attended</th>
                                    <th>Medical Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ user.name }}</td>
                                    <td v-if="user.class">{{ user.class.name }}</td>
                                    <td>{{ user.gender }}</td>
                                    <td>{{ user.previous_school }}</td>
                                    <td>{{ user.medical }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table" v-if="$attrs.userType == 'staffs'">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>ID No.</th>
                                    <th>Location</th>
                                    <th>TSC No.</th>
                                    <th>NHIF</th>
                                    <th>KRA Pin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ user.name }}</td>
                                    <td>{{ user.id_number }}</td>
                                    <td>{{ user.location }}</td>
                                    <td>{{ user.tsc }}</td>
                                    <td>{{ user.nhif }}</td>
                                    <table>{{ user.kra }}</table>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.post -->
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="guardian">
                   <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#parents_father" data-toggle="tab">Father</a></li>
                    <li class="nav-item"><a class="nav-link" href="#parents_mother" data-toggle="tab"> Mother</a></li>
                    <li class="nav-item"><a class="nav-link" href="#parents_guardian" data-toggle="tab">Guardian</a></li>
                    <li class="nav-item"><a class="nav-link" href="#relatives" data-toggle="tab">Relatives</a></li>
                 </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="parents_father">
                       <div class="table-responsive p0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="user.parents">
                                        <td>{{ user.parents.father_name }}</td>
                                        <td>{{ user.parents.father_contact }}</td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>
                     </div>
                     <div class="tab-pane" id="parents_mother">
                         <div class="table-responsive p0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="user.parents">
                                        <td>{{ user.parents.mother_name }}</td>
                                        <td>{{ user.parents.mother_contact }}</td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>
                     </div>
                     <div class="tab-pane" id="parents_guardian">
                        <div class="table-responsive p0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="user.parents">
                                        <td>{{ user.parents.guardian_name }}</td>
                                        <td>{{ user.parents.guardian_contact }}</td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>
                     </div>
                     <div class="tab-pane" id="relatives">
                        Relatives informationln will appear here...                     </div>
                </div>
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="exams">

                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="fees">
                      <div class="row">
                        <div class="col-12">
                            <div class="table-responsive p-0" style="height: 300px;">
                                <student-payments :adm="user.adm_number" v-if="user.adm_number"/>
                            </div>
                            <!-- /.card-body -->
                            <!-- /.card -->
                        </div>
                    </div>
        <!-- /.row -->

                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="documents">
                      <div class="table-responsive" style="height: 350px;">
                          <table class="table table-head-fixed text-nowrap">
                              <thead>
                                  <tr>
                                      <th>Name</th>
                                      <th>Link</th>
                                  </tr>
                              </thead>
                              <tbody v-show="user.documents">
                                  <tr v-for="document in user.documents" :key="document.id">
                                      <td>{{ document.name }}</td>
                                      <td><a :href="document.url" target="blank"> Open </a></td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>

                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</template>



<script>
import StudentPayments from './StudentPayments.vue'
    export default {
  components: { StudentPayments },
        data(){
            return {
                 form: new Form({
                    id:'',
                    name : '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                    doc: ''
                }),
                user:{},
            }
        },
        mounted() {

        },
        methods:{
            getUserProfilePic(docs){
                if(docs == '' || null)return '/img/user.png';
                let url= docs.filter(d=>d.name.match(/photo/));
                return (url.length>0) ? url[0].url : '/img/user.png';
            },
            closeOptionsModal(){
                $('#modalExportOptions').modal('hide');
            },
            uploadDocument(){
                let fd = new FormData(document.getElementById('formUploadDocument'));
                fd.append('_token', window.CSRF_TOKEN);
                fetch('/uploadDoc',{
                    method: 'POST',
                    body: fd
                }).then(resp=>resp.json()).then(resp=>{
                    console.log(resp);
                }).catch(err=>console.log(err));
            },
        },

        created() {
            //get usertype information
            axios.get("/"+this.$route.params.userType+"/"+this.$route.params.id)
            .then(({ data }) =>{
                this.user = data;
            })
        },
        watch: {
            '$route' (to, from) {
            console.log("Migration triggered")
            }
        }
    }
</script>
