<template>
    <!-- Content Wrapper. Contains page content -->
    <div>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Fees Information</h1>
                    </div>
                    <div class="col-sm-6"></div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- /.col -->
                    <div class="col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a
                                            class="nav-link active"
                                            href="#pay_details"
                                            data-toggle="tab"
                                            >Payment Details</a
                                        >
                                    </li> 
                                    <li class="nav-item">
                                        <a
                                            class="nav-link"
                                            href="#pay_summary"
                                            data-toggle="tab"
                                        >
                                            Payments Summary</a
                                        >
                                    </li>
                                    <li class="nav-item">
                                        <a
                                            class="nav-link"
                                            href="#pay_payables"
                                            data-toggle="tab"
                                            >Payable Fees</a
                                        >
                                    </li>
                                </ul>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <!-- /.tab-pane -->
                                    <div
                                        class="tab-pane active"
                                        id="pay_details"
                                    >
                                        <payment-details :feesInfo="feesInfo" :payables="payables"/>
                                    </div>
                                    <div class="tab-pane" id="pay_payables">
                                        <payable-fees/>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="pay_summary">
                                        <payment-summary :feesInfo="feesSummary" :payables="payables" :classes="classes"/>
                                    <!-- /.row -->
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</template>

<script>
import PayableFees from './PayableFees.vue';
import PaymentSummary from './PaymentSummary.vue';
import PaymentDetails from './PaymentDetails.vue';
export default {
  components: { PayableFees, PaymentSummary,PaymentDetails },
    data() {
        return {
            payables: [],
            classes:[],
            feesInfo:[],
            feesSummary:[]
        };
    },

    methods: {
        loadPayables(){
            axios.get("payables").then(({ data }) => {
                    this.payables = data;
                    Fire.$emit('loadComplete:payables',data);
                });
        },
        loadClasses(){
            axios.get('classes').then(({data})=>{
              this.classes = data.data;
              Fire.$emit('loadComplete:classes',data.data);
          });
        },
        loadFeesInfo(){
            axios.get('fees').then(({data})=>{
              this.feesInfo = data.data;
              Fire.$emit("loadComplete:feesInfo",data.data)
          }) 
        },
        loadFeesSummary(){
             axios.get('fees/summary').then(({data})=>{
              this.feesSummary = data;
              Fire.$emit("loadComplete:feesSummary",data)
          }) 
        }
    },

    created() {
          this.loadClasses();
          this.loadFeesInfo();
         this.loadPayables();
         this.loadFeesSummary();
        Fire.$on("afterCreate:payables", () => {
            this.loadPayables();
        });
         Fire.$on("afterCreate:classes", () => {
            this.loadClasses();
        });
    },
    watch: {
        $route(to, from) {
            console.log("Migration triggered");
        }
    }
};
</script>
