<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <table-header 
                            :title="'Class Records'" 
                            :icon="`fas fa-school fa-fw`" 
                            :icon_text="'Add New'"
                            @openModal="addNew()"
                            @pdfGen="generatePdf"
                            @excelGen="generateExcel"
                            @csvGen="generateCsv"
                        />
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover" id="table_classes">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Capacity</th>
                                    <th>Class Teacher</th>
                                    <th>Modify</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
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
                            >'s Class Info
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
                            editMode ? updateClass() : createClass()
                        "
                    >
                        <div class="modal-body">
                            <div class="form-group">
                                <input
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    placeholder="Class Name"
                                    class="form-control"
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
                                    v-model="form.capacity"
                                    type="number"
                                    name="capacity"
                                    placeholder="Class Capacity"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'capacity'
                                        )
                                    }"
                                />
                                <has-error
                                    :form="form"
                                    field="capacity"
                                ></has-error>
                            </div>

                            <div class="form-group">
                                <label for="clas_teacher"
                                    >Select Class Teacher</label
                                >
                                <select
                                    v-model="form.class_teacher_id"
                                    name="class_teacher"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.has(
                                            'class_teacher'
                                        )
                                    }"
                                >
                                    <option value=""
                                        >--Select Class Teacher--</option
                                    >
                                    <option
                                        v-for="teacher in teachers"
                                        :key="teacher.id"
                                        :value="teacher.id"
                                    >
                                        {{ teacher.name }}
                                    </option>
                                </select>
                                <has-error
                                    :form="form"
                                    field="class_teacher"
                                ></has-error>
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
    </div>
</template>

<script>

export default {
    data() {
        return {
            editMode: false,
            classes: {},
            teachers: {},
            count: 0,
            form: new Form({
                id: "",
                name: "",
                capacity: "",
                class_teacher: "",
                class_teacher_id:""
            }),
            search: "",
            active_record_count: 10,
            records: this.$parent.records,
            dataTable:''
        };
    },
    methods: {
        generatePdf() {
            Fire.$emit("generatePdf", {
                data: new TableData("#table_classes"),
                filename: "classes.pdf"
            });
        },
        generateExcel() {
            Fire.$emit("generateExcel", {
                table: "#table_classes",
                filename: "classes.xlsx"
            });
        },
        generateCsv() {
            Fire.$emit("generateCsv", {
                table: "#table_classes",
                filename: "classes.csv"
            });
        },
        getAvailableTeachers() {
            axios
                .get("teachers?tag=available")
                .then(data => (this.teachers = data.data));
        },
        getRecordsCount() {
            axios.get("classes/count").then(data => (this.count = data.data));
        },
        triggerPdfGenerate() {
            this.$parent.generatePDF("classes");
        },
        closeOptionsModal() {
            $("#modalExportOptions").modal("hide");
        },
        updateRecordsToShow(e) {
            let newRecordCount = e.target.value;
            this.active_record_count = newRecordCount;
            Fire.$emit("recordCountChange:classes");
        },
        searchClasses: _.debounce(() => {
            Fire.$emit("searching:classes");
        }, 1000),
        getClasses(page = 1) {
            axios
                .get(
                    "/classes?page=" +
                        page +
                        "&rec_count=" +
                        this.active_record_count
                )
                .then(response => {
                    this.classes = response.data;
                });
        },
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
        updateClass(id) {
            this.$Progress.start();
            this.form
                .post("/classes/" + this.form.id + "/update")
                .then(response => {
                    //create a log
                    this.$parent.createLog(
                        "Updated a record :" +
                            response.data.original_record +
                            ":" +
                            response.data.updated_record
                    );

                   
                    $("#addNew").modal("hide");
                    this.$Progress.finish();

                    Swal.fire(
                        "Updated!",
                        "Information has been updated!",
                        "success"
                    );
                     Fire.$emit("afterCreate:classes");
                })
                .catch(err => {
                    this.$Progress.fail();
                    console.log(err);
                });
        },
        loadClasses() {
            if (this.$gate.isAdmin()) {
                 
                // axios
                //     .get("classes?rec_count=" + this.active_record_count)
                //     .then(resp => {
                //         this.classes = resp.data;
                //         this.count = resp.data.total;

                //         return new Promise((resolve, reject) =>
                //             resolve(resp)
                //         );
                //     }),
                // this.getRecordsCount();
        
                //load class info using datatables ajax instead
                this.dataTable? this.dataTable.ajax.reload():''
                this.getAvailableTeachers()
                
            }
        },
        createClass() {
            this.$Progress.start();
            this.form
                .post("classes")
                .then(response => {
                    Fire.$emit("afterCreate:classes");
                    //creaate a new log of this
                    this.$parent.createLog(
                        "Created a new record:" +
                            JSON.stringify(response.data.last_record)
                    );
                    $("#addNew").modal("hide");

                    Toast.fire({
                        icon: "success",
                        title: "Class created successfully."
                    });
                    this.$Progress.finish();
                })
                .catch(err => {
                    this.$Progress.finish();
                    console.log(err);
                });
        },
        deleteClass(id) {
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
                    this.form
                        .post("classes/" + id + "/delete")
                        .then(resp => {
                            //create a log
                            this.$parent.createLog(
                                "Deleted a record :" + resp.data.deleted_record
                            );
                            Swal.fire(
                                "Deleted!",
                                "Class deleted successfully.",
                                "success"
                            );
                            Fire.$emit("afterCreate:classes");
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
        this.loadClasses();
        Fire.$on("afterCreate:classes", () => {
            this.loadClasses();
        });
        Fire.$on("searching:classes", () => {
            let q = this.search;
            axios
                .get(
                    "classes/find?q=" +
                        q +
                        "&rec_count=" +
                        this.active_record_count
                )
                .then(resp => {
                    //create a log
                    this.$parent.createLog(
                        "Searched for: " + q + " in the Classes table"
                    );
                    this.classes = resp.data;
                });
        });
        Fire.$on("recordCountChange:classes", () => {
            this.loadClasses();
        });
    },
    mounted(){
        const Parent = this;
        this.dataTable = $("#table_classes").DataTable({
            processing:true,
            retrieve:true,
            select:true,
            pageLength:25,
            scrollY:'500px',
            ajax:{
                type:'GET',
                url:'classes',
                dataSrc: function(data){
                    let i, _class, resp=[];
                    for(i=0; i<data.length; i++){
                        _class = data[i];
                        resp.push({
                            id:_class.id,
                            name:_class.name,
                            capacity:_class.capacity,
                            classTeacher:(_class.class_teacher != null)? _class.class_teacher.name:'Not yet assigned',
                            modify:`<a class="btn-edit" data-info='${JSON.stringify(_class)}'>
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                         /
                                    <a class ="btn-delete" data-info=${_class.id} >
                                        <i class="fa fa-trash red"></i>
                                    </a>`
                        })
                    }
                    return resp;
                }
            },
            columns:[
                {data: 'id'},
                {data:'name'},
                {data:'capacity'},
                {data:'classTeacher'},
                {data:'modify'}
            ]
        })

    //edit and delete
    $("#table_classes tbody").on('click', 'tr td a', function(){
        if($(this).hasClass('btn-edit')){
            //handle edit 
            let data = JSON.parse($(this).attr('data-info'));
            Parent.form.fill(data);
            Parent.editMode=true;
             $("#addNew").modal("show");

        }else if($(this).hasClass('btn-delete')){
            //handle delete
            let id = $(this).attr('data-info');
            Parent.deleteClass(id)
        }

        // let sel_staff = staffs_datatable.row(".selected").data();
        // if(sel_staff){
        //     Parent.form.clear();
        //     Parent.form.user_type = 'staffs';
        //     Parent.form.user_id= sel_staff.id;
        //     Parent.form.user_name = sel_staff.name;
        // }else{
        //     Parent.form.clear();
        //     Parent.form.user_id= '';
        //     Parent.form.user_name = '';
        // }

    })
    }
};
</script>
