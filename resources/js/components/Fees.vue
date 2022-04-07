<template>
  <!-- Content Wrapper. Contains page content -->
  <div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col -->
          <div class="col-md-12 col-lg-12">
            <div class="card">
              <div class="card-header">Payable Fees</div>
              <!-- /.card-header -->
              <div class="card-body">
                <payment-details />
                <!-- <payable-fees/> -->
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>
        <!-- /.card -->
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</template>

<script>
import PayableFees from "./PayableFees.vue";
import PaymentDetails from "./PaymentDetails.vue";
export default {
  components: { PayableFees, PaymentDetails },
  data() {
    return {
      payables: [],
    };
  },

  methods: {
    loadPayables() {
      axios.get("payables").then(({ data }) => {
        this.payables = data;
        Fire.$emit("loadComplete:payables", data);
      });
    },
  },

  created() {
    this.loadPayables();
    Fire.$on("afterCreate:payables", () => {
      this.loadPayables();
    });
  },
  watch: {
    $route(to, from) {
      console.log("Migration triggered");
    },
  },
};
</script>
