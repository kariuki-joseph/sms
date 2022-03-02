<template>
   <div>
      <div class="row">
         <div class="col-md-3 mb-2">
            <select class="form-control" @change="onSelectPayable">
               <option value="*">All</option>
               <option v-for="payable in payables"
                  :key="payable.id"
                  :value="payable.id"
                  >
                  {{ payable.name }}
               </option
                  >
            </select>
         </div>
      </div>
      <table
         class="table table-head-fixed text-nowrap"
         id="table_payment_details"
         >
         <thead>
            <tr>
               <th>Payment Type</th>
               <th>Student Name</th>
               <th>Adm. No.</th>
               <th> Class</th>
               <th> Serial No.</th>
               <th>Date Received</th>
               <th>Subtotal</th>
            </tr>
         </thead>
         <tbody>
            <tr v-for="payment in filteredFees" :key="payment.id">
               <td v-show="payment.payable">{{ payment.payable.name }}</td>
               <td v-if="payment.student">{{ payment.student.name }}</td>
               <td v-if="payment.student">{{ payment.student.adm_number }}</td>
               <td v-if="payment.student.class">{{ payment.student.class.name || 'and' }}</td>
               <td>{{ payment.id }}</td>
               <td>{{ payment.created_at }}</td>
               <td>{{ payment.amount }}</td>
            </tr>
         </tbody>
      </table>
   </div>
</template>

<script>
export default {
      props:{
         feesInfo:{

         },
         payables:{

         }
      },
    data(){
        return{
          fees:'',
          filteredFees:''
        }
    },
    methods:{
    onSelectPayable(e){
    let payableId = e.target.value;
    let filtered = (payableId == '*') ? this.fees : this.fees.filter(fee=>fee.payable.id == payableId);

    this.filteredFees = filtered;
    },
       generatePdf(){
            Fire.$emit('generatePdf', {
                data: new TableData("#table_payment_details"),
                filename:'payment_details.pdf',
            });
            },
            generateExcel(){
                Fire.$emit('generateExcel',{
                    table: '#table_payment_details',
                    filename: 'payment_details.xlsx'
                });

            },
            generateCsv(){
                Fire.$emit('generateCsv', {
                    table: '#table_payment_details',
                    filename: 'payment_details.csv'
                })
            },
    },
    created(){
      Fire.$on('loadComplete:feesInfo', fees=>{
        this.fees=this.filteredFees = fees;
      })
    },
    mounted(){
         
    },
    computed:{
    }
}
</script>