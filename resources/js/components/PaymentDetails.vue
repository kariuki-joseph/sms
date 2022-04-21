<template>
  <div>
    <div class="row mt-5" v-if="$gate.isAdmin()">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <table-header
              :title="'Fees Payment Information'"
              :hidden="true"
              @pdfGen="generatePdf"
              @excelGen="generateExcel"
              @csvGen="generateCsv"
            />
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table
              class="table table-head-fixed table-hover"
              id="table_payment_details"
            >
              <thead>
                <tr>
                  <th>Payment Type</th>
                  <th>Student Name</th>
                  <th>Adm. No.</th>
                  <th>Class</th>
                  <th>Serial No.</th>
                  <th>Date Received</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer"></div>
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
import { format } from 'date-fns';
export default {
  data() {
    return {
      dataTable: "",
    };
  },
  methods: {
    generatePdf() {
      Fire.$emit("generatePdf", {
        data: new TableData("#table_payment_details"),
        filename: "payment_details.pdf",
      });
    },
    generateExcel() {
      Fire.$emit("generateExcel", {
        data: new TableData("#table_payment_details"),
        filename: "payment_details.xlsx",
      });
    },
    generateCsv() {
      Fire.$emit("generateCsv", {
        data: new TableData("#table_payment_details"),
        filename: "payment_details.csv",
      });
    },
  },
  created() {},
  mounted() {
    const formatPaymentDate = (date)=>{
      return format(new Date(date),'iii, dd/MM/yyyy p')
    }

    this.dataTable = $("#table_payment_details").DataTable({
      processing: true,
      retrieve: true,
      pageLength: 25,
      select: true,
      scrollY: "500px",
      ajax: {
        type: "GET",
        url: "fees",
        dataSrc: function ({ data }) {
          let i,
            fees,
            resp = [];
          for (i = 0; i < data.length; i++) {
            fees = data[i];
            resp.push({
              paymentType: fees.payable.name,
              studentName: `<a href="#/students/${
                fees.student.adm_number ?? "0"
              }/profile">${fees.student.name ?? "Unable to get student"}</a>`,
              admNo: fees.student.adm_number,
              className: fees.student.class.name,
              serialNo: fees.id,
              dateReceived: formatPaymentDate(fees.created_at),
              amount: fees.amount,
            });
          }
          return resp;
        },
      },
      columns: [
        { data: "paymentType" },
        { data: "studentName" },
        { data: "admNo" },
        { data: "className" },
        { data: "serialNo" },
        { data: "dateReceived" },
        { data: "amount" },
      ],
    });
  },
  computed: {},
};
</script>