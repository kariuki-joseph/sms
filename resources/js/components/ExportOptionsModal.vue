<template>

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
</template>
<script>

</script>
