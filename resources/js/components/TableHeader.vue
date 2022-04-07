<template>
    <div class="card-header">
        <div class="row">
        <div class="container">
            <h3 class="card-title">{{ title }}</h3>
        </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                    <button class="btn btn-success" :class="{'d-none':hidden}" @click="emitOpenModal()"><i :class="icon"></i> {{ icon_text }} </button>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                    
            </div>

            <div class="col-sm-4 col-md-4 col-lg-4">

            </div>

            <div class="col-sm-2 col-md-2 col-lg-2">
               
            </div>

            <div class="col-sm-2 col-md-2 col-lg-2">
                <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
            </div>
            <!-- Export Options modal -->
            <div class="modal fade" id="modalExportOptions" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="modal-title">Choose One</div>
                            <ul class="list-unstyled">
                                <a  @click="$emit('pdfGen')" class="text-decoration-none"><li class="w-100 text-center py-1 my-1 btn btn-success"><i class="fa fa-file-pdf fa-lg"></i> PDF</li></a>
                                <a @click="$emit('excelGen')" class="text-decoration-none"><li class="w-100 text-center py-1 my-1 btn btn-secondary"><i class="fa fa-file-excel fa-lg"></i> EXCEL</li></a>
                                <a  @click="$emit('csvGen')" class="text-decoration-none"><li class="w-100 text-center py-1 my-1 btn btn-danger"><i class="fa fa-file-csv fa-lg"></i> CSV</li></a>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger text-white" data-toggle="close" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Export options modal -->
        </div>
    </div>
</template>
<script>
export default {
        data() {
            return {
         
                       
            }
        },
        props:{
            title:{
                type: String,
            },

            icon_text: {
                type: String,
            },

            icon: {
                default: "Add New"
            },
            hidden:{
                type:Boolean,
                default:false
            }
        },
        methods: {
            emitOpenModal(){
                return this.$emit("openModal");
            }
        },
        created() {

        }

    }
</script>

