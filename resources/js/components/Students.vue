<template>
  <div class="container">
    <!--students records-->
    <div class="row mt-5" v-if="$gate.isAdmin()">
      <div class="col-md-12">
        <div class="card">
          <table-header
            :title="'Students'"
            :icon="`fas fa-graduation-cap fa-fw`"
            :icon_text="'Add New'"
            @openModal="addNew()"
            @pdfGen="generatePdf"
            @excelGen="generateExcel"
            @csvGen="generateCsv"
          />
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table
              class="table table-hover table-head-fixed text-nowrap"
              id="table_students"
            >
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Student Name</th>
                  <th>Admission Number</th>
                  <th>Class</th>
                  <th>Modify</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <pagination
              :data="students"
              @pagination-change-page="getResults"
            ></pagination>
          </div>
        </div>
        <!-- /.card -->
      </div>
    </div>
    <!--/ students records-->
    <div v-if="!$gate.isAdmin()">
      <not-found></not-found>
    </div>

    <!-- Modal -->
    <div
      class="modal fade"
      id="addNew"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addNewLabel"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-dialog-centered"
        role="document"
        :class="!editMode ? 'modal-lg' : ''"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" v-show="!editMode" id="addNewLabel">
              <i class="fa fa-plus"></i> New Student Registration
            </h5>
            <h5 class="modal-title" v-show="editMode" id="addNewLabel">
              <i class="fa fa-edit"></i> Update <b>{{ form.name }}</b
              >'s student info
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editMode ? updateStudent() : createStudent()">
            <div class="modal-body">
              <section id="section_bio">
                <div class="form-group text-lg">
                  <label>Student's Basic Information</label>
                </div>
                <div class="form-group">
                  <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    placeholder="Student's Full Names"
                    required
                    minlength="3"
                    maxlength="40"
                    autocomplete="off"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }"
                  />
                  <has-error :form="form" field="name"></has-error>
                </div>

                <div class="form-group">
                  <input
                    type="date"
                    name="dob"
                    id=""
                    v-model="form.dob"
                    required
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('dob') }"
                  />
                  <has-error :form="form" field="dob"></has-error>
                </div>

                <div class="form-group">
                  <input
                    v-model="form.birth_cert_number"
                    type="number"
                    name="birth_cert_number"
                    placeholder="Birth Certificate Number"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('birth_cert_number'),
                    }"
                  />
                  <has-error :form="form" field="birth_cert_number"></has-error>
                </div>

                <div
                  class="
                    form-group
                    radio-linline-flex
                    d-
                    justjsify-content-around
                  "
                >
                  <label for="gender">
                    <input
                      type="radio"
                      name="gender"
                      v-model="form.gender"
                      value="Male"
                      checked="true"
                      :class="{ 'is-invalid': form.errors.has('gender') }"
                    />Male
                    <has-error :form="form" field="gender"></has-error> </label
                  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <label for="gender">
                    <input
                      type="radio"
                      name="gender"
                      v-model="form.gender"
                      value="Female"
                      :class="{ 'is-invalid': form.errors.has('gender') }"
                    />Female
                    <has-error :form="form" field="gender"></has-error>
                  </label>
                </div>

                <div class="form-group" v-if="editMode">
                  <input
                    v-model="form.adm_number"
                    type="text"
                    name="adm_number"
                    placeholder="Admission Number"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('adm_number') }"
                    disabled
                  />
                  <has-error :form="form" field="adm_number"></has-error>
                </div>
              </section>

              <section id="section_education" style="display: none">
                <div class="form-group text-lg">
                  <label>Students Education Information</label>
                </div>
                <div class="form-group">
                  <label for="class">Choose class to be admitted to</label>
                  <select
                    v-model="form.class_id"
                    name="class"
                    id=""
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('class.name') }"
                  >
                    <option value="">--Choose Class To Be Admitted--</option>
                    <option
                      v-for="_class in classes"
                      :key="_class.id"
                      :value="_class.id"
                    >
                      {{ _class.name }}
                    </option>
                  </select>
                  <has-error :form="form" field="class.name"></has-error>
                </div>

                <div class="form-group">
                  <input
                    v-model="form.nemis_number"
                    type="text"
                    name="nemis_number"
                    placeholder="NEMIS Number"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('nemis_number') }"
                  />
                  <has-error :form="form" field="nemis_number"></has-error>
                </div>

                <div class="form-group">
                  <textarea
                    rows="2"
                    v-model="form.previous_school"
                    name="previous_school"
                    placeholder="Previous School"
                    required
                    minlength="5"
                    maxlength="50"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('previous_school'),
                    }"
                  ></textarea>
                  <has-error :form="form" field="previous_school"></has-error>
                </div>
              </section>

              <section id="section_location" style="display: none">
                <div class="form-group text-lg">
                  <label>Students Location Information</label>
                </div>
                <div class="form-group">
                  <textarea
                    rows="2"
                    v-model="form.location"
                    name="location"
                    placeholder="Home Location"
                    required
                    maxlength="20"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('location') }"
                  ></textarea>
                  <has-error :form="form" field="location"></has-error>
                </div>
              </section>

              <section id="section_medical" style="display: none">
                <div class="form-group text-lg">
                  <label>Students Health Information</label>
                </div>
                <div class="form-group">
                  <textarea
                    rows="4"
                    v-model="form.medical"
                    name="medical"
                    placeholder="Any allergy or medical problem? State it here, briefly. Skip if none."
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('medical') }"
                  ></textarea>
                  <has-error :form="form" field="medical"></has-error>
                </div>
              </section>

              <section id="section_documents" style="display: none">
                <div class="form-group text-lg">
                  <label for="other documents">Provide Support Documents</label>
                </div>
                <div class="form-group">
                  <label for="passport"
                    >Select a Recent Colored Passport Phhoto</label
                  ><br />
                  <input
                    @change="loadPassport"
                    type="file"
                    name="passport"
                    id="passport"
                    :class="{ 'is-invalid': form.errors.has('passport_photo') }"
                  />
                  <has-error :form="form" field="passport_photo"></has-error>
                  <div class="passport-photo">
                    <img
                      src=""
                      id="passport_photo"
                      alt="No Passport Photo Selected"
                    />
                  </div>
                </div>
                <div class="form-group">
                  <label for="birth certificate"
                    >Select a Birth Certificate: pdf format</label
                  ><br />
                  <input
                    @change="loadBirthCertificate"
                    type="file"
                    name="birth_certificate"
                    id="birth_certificate"
                    :class="{
                      'is-invalid': form.errors.has('birth_certificate'),
                    }"
                  />
                  <has-error :form="form" field="birth_certificate"></has-error>
                </div>
              </section>

              <section id="section_parents_father" style="display: none">
                <div class="form-group text-lg">
                  <label>Father's Information</label>
                </div>
                <div class="form-group">
                  <textarea
                    rows="2"
                    v-model="form.father_name"
                    type="text"
                    name="father_name"
                    minlength="3"
                    maxlength="40"
                    placeholder="Father's Name"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('father_name') }"
                  ></textarea>
                  <has-error :form="form" field="father_name"></has-error>
                </div>

                <div class="form-group">
                  <input
                    v-model="form.father_contact"
                    type="tel"
                    name="father_contact"
                    placeholder="Father's Phone Number"
                    minlength="10"
                    maxlength="13"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('father_contact') }"
                  />
                  <has-error :form="form" field="father_contact"></has-error>
                </div>
              </section>

              <section
                id="section_parents_mother"
                style="display: none"
                v-if="form"
              >
                <div class="form-group text-lg">
                  <label for="">Mother's Information</label>
                </div>
                <div class="form-group">
                  <textarea
                    rows="2"
                    v-model="form.mother_name"
                    type="text"
                    name="mother_name"
                    placeholder="Mother's Name"
                    minlength="3"
                    maxlength="40"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('mother_name') }"
                  ></textarea>
                  <has-error :form="form" field="mother_name"></has-error>
                </div>

                <div class="form-group">
                  <input
                    v-model="form.mother_contact"
                    type="tel"
                    name="mother_contact"
                    placeholder="Mother's Phone Number"
                    minlength="10"
                    maxlength="13"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('mother_contact') }"
                  />
                  <has-error :form="form" field="mother_contact"></has-error>
                </div>
              </section>

              <section
                id="section_parents_guardian"
                style="display: none"
                v-if="form"
              >
                <div class="form-group text-lg">
                  <label for="">Guardian's Information</label>
                </div>
                <div class="form-group">
                  <textarea
                    rows="2"
                    v-model="form.guardian_name"
                    type="text"
                    name="guardian_name"
                    placeholder="Guardian's Name"
                    minlength="3"
                    maxlength="40"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('guardian_name') }"
                  ></textarea>
                  <has-error :form="form" field="guardian_name"></has-error>
                </div>

                <div class="form-group">
                  <input
                    v-model="form.guardian_contact"
                    type="tel"
                    name="guardian_contact"
                    placeholder="Guadian's Phone Number"
                    minlength="10"
                    maxlength="13"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('guardian_contact'),
                    }"
                  />
                  <has-error
                    :form="form"
                    field="parents.guardian_contact"
                  ></has-error>
                </div>
              </section>
            </div>
            <div class="d-flex justify-content-between">
              <button
                v-show="registration.isFirstSection"
                type="button"
                class="btn btn-danger ml-3 mb-3"
                data-dismiss="modal"
              >
                <i class="fa fa-window-close"></i> Cancel
                {{ editMode ? "Editing" : "Registration" }}
              </button>
              <button
                @click="previous()"
                v-show="!registration.isFirstSection"
                type="button"
                class="btn btn-danger ml-3 mb-3"
              >
                <i class="fa fa-arrow-circle-left"></i> Back
              </button>
              <div>
                <button
                  @click="next()"
                  type="button"
                  v-show="(!editMode || editMode) && !registration.isComplete"
                  class="btn btn-primary mr-3 mb-3"
                >
                  <i class="fa fa-arrow-circle-right"></i> Next
                </button>
                <button
                  type="submit"
                  v-show="!editMode && registration.isComplete"
                  class="btn btn-primary mr-3 mb-3"
                >
                  <i class="fa fa-paper-plane"></i> Submit
                </button>
                <button
                  type="submit"
                  v-show="editMode && registration.isComplete"
                  class="btn btn-success mr-3 mb-3"
                >
                  <i class="fa fa-edit"></i> Update
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TableHeader from "./TableHeader.vue";

export default {
  components: {
    TableHeader,
  },
  data() {
    return {
      editMode: false,
      registration: {
        isFirstSection: true,
        sections: [
          "#section_bio",
          "#section_education",
          "#section_medical",
          "#section_documents",
          "#section_location",
          "#section_parents_father",
          "#section_parents_mother",
          "#section_parents_guardian",
        ],
        activeSection: "#section_bio",
        nextSection: "",
        previousSection: "",
        isComplete: false,
      },
      students: {},
      classes: {},
      count: 0,
      form: new Form({
        id: "",
        name: "",
        birth_cert_number: "",
        nemis_number: "",
        medical: "",
        adm_number: "",
        gender: "",
        dob: "",
        location: "",
        previous_school: "",
        admission_class: "",
        passport_photo: "",
        birth_certificate: "",
        class_id: "",
        father_name: "",
        father_contact: "",
        mother_name: "",
        mother_contact: "",
        guardian_name: "",
        guardian_contact: "",
      }),
      search: "",
      next_adm_no: "",
      active_student_count: 10,
      records: this.$parent.records,
      dataTable: "",
    };
  },
  methods: {
    setSection(section) {
      if (typeof section == Number) {
        this.registration.activeSection = this.registration.sections[section];
        return (this.registration.nextSection = this.nextSection());
      }
      this.registration.activeSection = section;
      this.registration.nextSection = this.nextSection();
    },
    nextSection() {
      let reg = this.registration;
      return this.registration.sections[
        this.registration.sections.indexOf(this.activeSection()) + 1
      ];
    },
    previousSection(){
      return this.setSection(this.registration.sections[this.registration.sections.indexOf(this.activeSection())-1])
    },
    activeSection() {
      return this.registration.activeSection;
    },
    isLastSection() {
      return (
        this.activeSection() ==
        this.registration.sections[this.registration.sections.length - 1]
      );
    },
    isFirstSection() {
      return this.registration.sections.indexOf(this.activeSection()) == 0;
    },

    next() {
      //next registration section
      this.registration.isFirstSection = false;
      this.setSection(this.nextSection());
      //last page of registration
      this.isLastSection() && (this.registration.isComplete = true);
      //automatically move to the next section if already in the first section
      $("#addNew").find("section").not(this.activeSection()).hide();
      $("#addNew").find(this.activeSection()).show();
    },
    previous() {
      //previous registration section
      this.previousSection();
      this.registration.isComplete = false;
      //first page
      this.isFirstSection() && (this.registration.isFirstSection = true)
        

      $("#addNew").find("section").not(this.activeSection()).hide();
      $("#addNew").find(this.activeSection()).show();
    },

    generatePdf() {
      Fire.$emit("generatePdf", {
        data: new TableData("#table_students"),
        filename: "students.pdf",
      });
    },
    generateExcel() {
      Fire.$emit("generateExcel", {
        data: new TableData("#table_students"),
        filename: "students.xlsx",
      });
    },
    generateCsv() {
      Fire.$emit("generateCsv", {
        data: new TableData("#table_students"),
        filename: "students.csv",
      });
    },
    loadPassport(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      let limit = 1024 * 1024 * 2;

      if (file["size"] > limit) {
        Swal.fire(
          "Failed!",
          "You are uploading a large file. Maximum upload size limit is 2 MB!",
          "warning"
        );
      } else {
        //
        //file valid
        reader.onloadend = (result) => {
          $("#passport_photo").attr("src", reader.result);
          this.form.passport_photo = reader.result;
        };

        reader.readAsDataURL(file);
      }
    },
    loadBirthCertificate(e) {
      let file = e.target.files[0];
      let reader = new FileReader();
      let limit = 1024 * 1024 * 2;

      if (file["size"] > limit) {
        Swal.fire(
          "Failed!",
          "You are uploading a large file. Maximum upload size limit is 2 MB!",
          "warning"
        );
      } else {
        //
        //file valid
        reader.onloadend = (result) => {
          this.form.birth_certificate = reader.result;
        };

        reader.readAsDataURL(file);
      }
    },
    loadClasses() {
      axios.get("classes").then(({ data }) => (this.classes = data));
    },
    getNewAdmissionNumber() {
      axios.get("students/last-adm").then((data) => {
        this.next_adm_no = data.data;
      });
    },
    getstudentsCount() {
      axios.get("students/count").then((data) => (this.count = data.data));
    },
    closeOptionsModal() {
      $("#modalExportOptions").modal("hide");
    },

    updateRecordsToShow(record) {
      this.active_student_count = record;
      Fire.$emit("studentCountChange:students");
    },
    searchStudents: _.debounce(() => {
      Fire.$emit("searching:students");
    }, 1000),
    getResults(page = 1) {
      axios
        .get(
          "students?page=" + page + "&rec_count=" + this.active_student_count
        )
        .then((response) => {
          this.students = response.data;
        });
    },
    addNew() {
      this.editMode = false;
      this.form.reset();
      $("#addNew").modal({
        backdrop: "static",
      });
    },
    updateStudent(id) {
      this.$Progress.start();
      this.form
        .post("students/" + this.form.id + "/update")
        .then((response) => {
          //create a log
          this.$parent.createLog(
            "Updated a record :" +
              response.data.original_record +
              ":" +
              response.data.updated_record
          );
          this.isFirstSection = true;
          $("#addNew").modal("hide");
          this.$Progress.finish();

          Swal.fire("Updated!", "Information has been updated!", "success");
          Fire.$emit("afterCreate:students");
        })
        .catch((err) => {
          this.$Progress.fail();
          console.log(err);
        });
    },
    loadStudents() {
      if (this.$gate.isAdmin()) {
        this.getNewAdmissionNumber();
        //load students via datatables ajax
        this.dataTable ? this.dataTable.ajax.reload() : "";
      }
    },
    createStudent() {
      this.$Progress.start();
      this.form
        .post("students")
        .then((response) => {
          Fire.$emit("afterCreate:students");
          //create a new log of this
          this.$parent.createLog(
            "Created a new record:" + JSON.stringify(response.data.last_record)
          );
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: "Student registered successfully.",
          });
          this.$Progress.finish();
        })
        .catch((err) => {
          this.$Progress.finish();
          console.log(err);
        });
    },
    deleteStudent(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("students/" + id + "/delete")
            .then((resp) => {
              //create a log
              this.$parent.createLog(
                "Deleted a record :" + JSON.stringify(resp.data.deleted_record)
              );
              Swal.fire("Deleted!", "Record deleted successfully.", "success");
              Fire.$emit("afterCreate:students");
            })
            .catch(() => {
              Swal.fire("Failed!", "There was something wrong.", "warning");
            });
        }
      });
    },
  },
  created() {
    this.loadClasses();
    Fire.$on("afterCreate:students", () => {
      this.loadStudents();
    });
  },
  mounted() {
    this.dataTable = $("#table_students").DataTable({
      processing: true,
      select: true,
      retrieve: true,
      pageLength: "25",
      scrollY: "500px",
      ajax: {
        type: "GET",
        url: "students",
        dataSrc: function (data) {
          // return console.log(data)
          let i,
            student,
            resp = [];
          for (i = 0; i < data.length; i++) {
            student = data[i];
            resp.push({
              id: student.id,
              name: `<a href="#/students/${student.id}/profile">${student.name}</a>`,
              adm_number: student.adm_number,
              className: student.class ? student.class.name : "Not Found",
              modify: `<a class="btn-edit" data-info='${JSON.stringify({
                ...student.parents,
                ...student,
              })}'>
                    <i class="fa fa-edit blue"></i>
                    </a>
                        /
                    <a class="btn-delete" data-info='${student.id}'>
                        <i class="fa fa-trash red"></i>
                    </a>`,
            });
          }
          return resp;
        },
      },
      columns: [
        { data: "id" },
        { data: "name" },
        { data: "adm_number" },
        { data: "className" },
        { data: "modify" },
      ],
    });

    //bind edit and delete to vue instance
    const Parent = this;
    $("#table_students tbody").on("click", "tr td a", function () {
      if ($(this).hasClass("btn-edit")) {
        let data = JSON.parse($(this).attr("data-info"));
        //fill form with data and edit
        console.log(data);
        Parent.form.fill(data);
        Parent.editMode = true;
        $("#addNew").modal("show");
      } else if ($(this).hasClass("btn-delete")) {
        let id = $(this).attr("data-info");
        Parent.deleteStudent(id);
      }
    });
  },
  watch: {
    $route(to, from) {
      //shift in url params
    },
  },
};
</script>
