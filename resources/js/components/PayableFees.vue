<template>
   <div class="container">
      <div class="row mt-5" v-if="$gate.isAdmin()">
         <div class="col-md-12">
            <div class="card">
               <div class="card-header">
                  <div class="row">
                     <div class="container">
                        <h3 class="card-title">
                           {{ count }} payable(s) records
                        </h3>
                     </div>
                     <div class="col-sm-2 col-md-2 col-lg-2">
                        <button
                           class="btn btn-success"
                           @click="addNew()"
                           >
                        <i class="fas fa-school fa-fw"></i> Add New
                        </button>
                     </div>
                     <div class="col-sm-2 col-md-2 col-lg-2">
                     </div>
                     <div class="col-sm-4 col-md-4 col-lg-4"></div>
                     <div class="col-sm-2 col-md-2 col-lg-2">
                     </div>
                     <div class="col-sm-2 col-md-2 col-lg-2">
                        <button
                           class="btn btn-secondary"
                           data-toggle="modal"
                           data-target="#modalExportOptions"
                           >
                        <i class="fas fa-file-export fa-fw"></i>
                        Export As
                        </button>
                     </div>
                  </div>
               </div>
               <!-- /.card-header -->
               <div class="card-body table-responsive p-0">
                  <table class="table table-hover" id="table_payables">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Payment Name</th>
                           <th>Amount</th>
                           <th>Upcoming</th>
                           <th>Modify</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr
                           v-for="payable in payables"
                           :key="payable.id"
                           >
                           <td>{{ payable.id }}</td>
                           <td>{{ payable.name }}</td>
                           <td>{{ payable.amount }}</td>
                           <td>{{ payable.upcoming | formatUpcoming }}</td>
                           <td>
                              <a href="#" @click="editModal(payable)">
                              <i class="fa fa-edit blue"></i>
                              </a>
                              /
                              <a
                                 href="#"
                                 @click="deletePayable(payable.id)"
                                 >
                              <i class="fa fa-trash red"></i>
                              </a>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <!-- /.card-body -->
               <div class="card-footer">
               </div>
            </div>
            <!-- /.card -->
         </div>
      </div>
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
         <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5
                     class="modal-title"
                     v-show="!editMode"
                     id="addNewLabel"
                     >
                     <i class="fas fa-plus fa-fw"></i> Add New
                  </h5>
                  <h5
                     class="modal-title"
                     v-show="editMode"
                     id="addNewLabel"
                     >
                     <i class="fa fa-edit"></i> Update
                     <b>{{ form.name }}</b
                        >'s Payment Info
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
               <form
                  @submit.prevent="
                  editMode ? updatePayable() : createPayable()
                  "
                  >
                  <div class="modal-body">
                     <div class="form-group">
                        <input
                           v-model="form.name"
                           type="text"
                           name="name"
                           placeholder="Payment Name e.g. First Term 2022"
                           class="form-control"
                           autocomplete="off"
                           :class="{
                           'is-invalid': form.errors.has('name')
                           }"
                           />
                        <has-error
                           :form="form"
                           field="name"
                           ></has-error>
                     </div>
                     <div class="form-group">
                        <input
                           v-model="form.amount"
                           type="number"
                           name="amount"
                           placeholder="Amount that should be paid."
                           class="form-control"
                           :class="{
                           'is-invalid': form.errors.has(
                           'amount'
                           )
                           }"
                           />
                        <has-error
                           :form="form"
                           field="amount"
                           ></has-error>
                     </div>
                     <div class="form-group">
                        <label for="clas_teacher"
                           >Select Whether If an Upcoming Payment</label
                           >
                        <select
                           v-model="form.upcoming"
                           name="upcoming"
                           class="form-control"
                           :class="{
                           'is-invalid': form.errors.has(
                           'upcoming'
                           )
                           }"
                           >
                           <option v-for="upcoming in [1,0]" :key="upcoming.id" :value="upcoming">{{ upcoming | formatUpcoming  }}</option>
                        </select>
                     </div>
                  </div>
                  <div class="d-flex justify-content-between">
                     <button
                        type="button"
                        class="btn btn-danger ml-3 mb-3"
                        data-dismiss="modal"
                        >
                     <i class="fa fa-window-close"></i> Close
                     </button>
                     <div>
                        <button
                           type="submit"
                           v-show="editMode"
                           class="btn btn-success mr-3 mb-3"
                           >
                        <i class="fa fa-edit"></i> Update
                        </button>
                        <button
                           type="submit"
                           v-show="!editMode"
                           class="btn btn-primary mr-3 mb-3"
                           >
                        <i class="fa fa-plus"></i> Create
                        </button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <!--export options modal-->
      <export-options-modal
      @pdfGen="generatePdf"
      @excelGen="generateExcel"
      @csvGen="generateCsv"
      ></export-options-modal>
      <!--/ export options modal-->
   </div>
</template>

<script>
import ExportOptionsModal from "./ExportOptionsModal.vue";
export default {
    components: {
        ExportOptionsModal
    },
    data() {
        return {
            editMode: false,
            count: 0,
            form: new Form({
                id: "",
                name: "",
                amount: "",
                upcoming: 0
            }),
            search: "",
            payables:[],
            count:''
        };
    },
    methods: {
        generatePdf() {
            Fire.$emit("generatePdf", {
                data: new TableData("#table_payables"),
                filename: "payables.pdf"
            });
        },
        generateExcel() {
            Fire.$emit("generateExcel", {
                table: "#table_payables",
                filename: "payables.xlsx"
            });
        },
        generateCsv() {
            Fire.$emit("generateCsv", {
                table: "#table_payables",
                filename: "payables.csv"
            });
        },
        closeOptionsModal() {
            $("#modalExportOptions").modal("hide");
        },
        searchPayables: _.debounce(() => {
            Fire.$emit("searching:payables");
        }, 1000),
        editModal(_class) {
            this.editMode = true;
            this.form.fill(_class);
            $("#addNew").modal("show");
        },
        addNew() {
            this.editMode = false;
            this.form.reset();
            $("#addNew").modal("show");
        },
        updatePayable(id) {
            this.$Progress.start();
            this.form.post("payables/" + this.form.id + "/update")
                .then(response => {
                    //create a log
                    // this.$parent.createLog(
                    //     "Updated a record :" +
                    //         response.data.original_record +
                    //         ":" +
                    //         response.data.updated_record
                    // );

                    
                    $("#addNew").modal("hide");
                    this.$Progress.finish();

                    Swal.fire(
                        "Updated!",
                        "Information has been updated!",
                        "success"
                    );
                    Fire.$emit("afterCreate:payables");
                })
                .catch(err => {
                    this.$Progress.fail();
                    console.log(err);
                });
        },
        loadPayables() {
            if (this.$gate.isAdmin()) {
                return (
                    axios.get("payables")
                        .then(({data}) => {
                            this.payables = data;
                            this.count = data.length;
                        })
                );
            }
        },
        createPayable() {
            this.$Progress.start();
            this.form
                .post("payables")
                .then(response => {
                    Fire.$emit("afterCreate:payables");
                    //creaate a new log of this
                    // this.$parent.createLog(
                    //     "Created a new record:" +
                    //         JSON.stringify(response.data.last_record)
                    // );
                    $("#addNew").modal("hide");

                    Toast.fire({
                        icon: "success",
                        title: "Payable created successfully."
                    });
                    this.$Progress.finish();
                })
                .catch(err => {
                    this.$Progress.finish();
                    console.log(err);
                });
        },
        deletePayable(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(result => {
                // Send request to the server
                if (result.value) {
                    this.form.post("payables/" + id + "/delete")
                        .then(resp => {
                            // //create a log
                            // this.$parent.createLog(
                            //     "Deleted a record :" + resp.data.deleted_record
                            // );
                            Swal.fire(
                                "Deleted!",
                                "Payable deleted successfully.",
                                "success"
                            );
                            Fire.$emit("afterCreate:payables");
                        })
                        .catch(() => {
                            Swal.fire(
                                "Failed!",
                                "There was something wrong.",
                                "warning"
                            );
                        });
                }
            });
        }
    },
    created() {
        this.loadPayables();
        Fire.$on("afterCreate:payables", () => {
            this.loadPayables();
        });

        Fire.$on("searching:payables", () => {
            //do something on search
        });
    }
};
</script>
