<template>
  <div class="container">
    <div class="row mt-5" v-if="$gate.isAdmin()">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="container">
                <h3 class="card-title">Upload Documents</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h4>Select The User to Upload document</h4>
            <form @submit.prevent="upload()">
              <div class="form-group">
                <label for="user_type">Select User Category</label>
                <select
                  v-model="form.user_type"
                  @change="filterUserTypes"
                  name="user_type"
                  id="user_type"
                  class="custom-select"
                  :class="{ 'is-invalid': form.errors.has('user_type') }"
                >
                  <option selected>--Select User Category--</option>
                  <option 
                  v-for="user_type in user_types"
                  :key="user_type.id">
                    {{ user_type.name }}
                  </option>
                </select>
                <has-error :form="form" field="user_type"></has-error>
              </div>
              <div class="form-group">
                <label for="user_name"
                  >Select
                  <strong>{{ userCategory | singularize }}</strong></label
                >
                Selected <b>{{ userCategory | singularize }}</b>: {{ selectedUser.name }}
                <table class="table" id="table_students_uploads">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Adm No.</th>
                      <th>Class</th>
                      <th>Gender</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <div class="form-group">
                <div class="custom-file">
                  <input
                    @change="loadFile"
                    type="file"
                    name="document"
                    id="customFile"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('document') }"
                  />
                  <has-error :form="form" field="document"></has-error>
                </div>
              </div>
              <div class="form-group">
                <input
                  v-model="form.document_name"
                  type="text"
                  name="document_name"
                  id="document_name"
                  placeholder="Name of the document..."
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('document_name') }"
                />
                <has-error :form="form" field="document_name"></has-error>
              </div>
              <div class="form-group d-flex justify-content-between">
                <button type="submit" class="btn btn-danger">
                  Cancel <i class="fa fa-window-close"></i>
                </button>
                <button type="submit" class="btn btn-success">
                  Upload <i class="fa fa-paper-plane"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
        <!-- /.card -->
      </div>
    </div>
    <div v-if="!$gate.isAdmin()">
      <not-found></not-found>
    </div>
  </div>
</template>

<script>
$(function () {
  //Initialize Select2 Elements
  $(".select2").select2();

  //Initialize Select2 Elements
  $(".select2bs4").select2({
    theme: "bootstrap4",
  });
});

export default {
  data() {
    return {
      user_types: {},
      students_datatable: "",
      isStudent: true,
      userCategory: "User",
      classes: {},
      form: new Form({
        document: "",
        document_name: "",
        user_name: "",
        user_type: "",
        user_id: "",
      }),
      validations: {
        document: "Please select document to upload",
        document_name:
          "Please enter the name of the document you want to upload.",
        user_name: "Select the user to upload the document for.",
        user_type:
          "You need to select the category of the user you wish to upload the document for.",
      },
      search: "",
      selectedUser: {},
    };
  },
  methods: {
    initReactiveProperties(){
        this.isStudent = true;
        let students_datatable = this.students_datatable;

        this.students_datatable.ajax.url("students").load();
        let Parent = this;

        $("#table_students_uploads tbody").on("click", "tr", function () {
          if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
          } else {
            students_datatable.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
          }

          let sel_stud = students_datatable.row(".selected").data();
          if (!sel_stud) {
            Parent.selectedUser=""
            Parent.form.clear();
          } else {
            Parent.selectedUser = sel_stud
            Parent.form.user_type = "students";
            Parent.form.user_id = sel_stud.id;
            Parent.form.user_name = sel_stud.name;
          }
        });
        //load students
      
    },
    upload() {
      //empty fields
      this.form.errors.clear();
      this.form.keys().map((key) => {
        if (this.form[key] == (undefined || "")) {
          this.form.errors.set(key, this.validations[key]);
        }
      });

      if (!this.form.errors.any()) {
        this.form.post("uploads").then((resp) => {
          if (resp.data.status == "success") {
            Swal.fire(
              "Success",
              "The file has been uploaded successfully.",
              "success"
            );
          } else if (resp.data.status == "fail") {
            Swal.fire(
              "Failed!",
              "There was a problem uploading your file. Please try again.",
              "danger"
            );
          }
        });
      } else {
        return;
      }
    },
    loadUserTypes() {
      if (this.$gate.isAdmin()) {
        return axios.get("users/types").then(({data}) => {
          this.user_types = data.data;
        });
      }
    },
    filterUserTypes(e) {
      let userCategory = e.target.value;
      this.userCategory = userCategory;
      if (userCategory.match(/^stud*/i)) {
        this.initReactiveProperties()
       } else {
        //staff upload - omitted in this version
      }
    },
    loadFile() {
      let f = document.querySelector("#customFile").files[0];
      let reader = new FileReader();
      reader.onload = () => {
        this.form.clear();
        this.form.document = reader.result;
      };
      reader.readAsDataURL(f);
    },
  },
  created() {
    this.loadUserTypes();
  },

  mounted() {
    $(document).ready(() => {
      this.students_datatable = $("#table_students_uploads").DataTable({
        retrieve: true,
        select: true,
        pageLength: 25,
        scrollY: "300px",
        ajax: {
          type: "GET",
          url: "students",
          dataSrc: function (json) {
            let resp = [];
            json.forEach(student=>{
              resp.push({
                id: student.id,
                name: student.name,
                adm: student.adm_number,
                class: student.class ? student.class.name:"Null",
                gender: student.gender,
              });
            });
            return resp;
          },
        },
        columns: [
          { data: "id" },
          { data: "name" },
          { data: "adm" },
          { data: "class" },
          { data: "gender" },
        ],
      });
      this.initReactiveProperties();
    });
  },
};
</script>
