<template>
   <div>
      <div class="row mb-2">
         <div class="col-md-4">
            <span class="text-center">Select Payment Type</span>
            <select name="payment" class="form-control" @change="onSelectPayable">
               <option v-for="payable in payables" :key="payable.id" 
               :value="payable.id">
                  {{ payable.name }}
               </option>
            </select>
         </div>
         <div class="col-md-4">
            <span class="text-center">Select Class</span>
            <select name="_class" class="form-control" @change="onSelectClass">
               <option value="*">All</option>
               <option :value="cls.id" v-for="cls in classes" :key="cls.id">
                  {{ cls.name }}
               </option>
            </select>
         </div>
         <div class="col-md-4"></div>
      </div>
      <div class="row">
         <div class="col-12">
            <div
               class="table-responsive p-0"
               style="height: 300px;"
               >
               <table
                  class="table table-head-fixed text-nowrap"
                  >
                  <thead>
                     <tr>
                        <th>Student Name</th>
                        <th>Adm. No.</th>
                        <th>Class</th>
                        <th>Payment</th>
                        <th>Subtotal</th>
                        <th>Balance</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="student in filteredFees" :key="student.id">
                        <td>{{ student.name }}</td>
                        <td>{{ student.adm_number }}</td>
                        <td v-if="student.class">{{ student.class.name }}</td>
                        <td>{{ "payable" }} </td>
                        <sub-total :adm="student.adm_number" :fees="student.fees" :payable_id="filters.payable" :payables="payables" v-if="student.fees" @onTotalling="updataTotals"/>
                        <balance :adm="student.adm_number" :subtotals="subtotals"/>
                     </tr>
                  </tbody>
                  <tfoot>
                     <tr>
                        <td colspan="4">Total Number of Students:</td>
                        <td><input type="number" value="40"></td>
                     </tr>
                     <tr>
                        <td colspan="2">Expected Total Amount:</td>
                        <td colspan="2">40x300</td>payableAmount
                        <td>Total: Ksh. 1200</td>
                     </tr>
                      <tr>
                        <td colspan="4">Total Amount Paid:</td>
                        <td>Ksh. 1200</td>
                     </tr>
                  </tfoot>
               </table>
            </div>
            <!-- /.card-body -->
            <!-- /.card -->
         </div>
      </div>
   </div>
</template>

<script>
import SubTotal from './SubTotal.vue'
import Balance from './Balance.vue'
export default {
   components:{SubTotal,Balance},
    data(){
        return{
          payables:[],
          classes:[],
          filters:{
            payable:'',
            class:'*'
          },
          fees:'',
          filteredFees:'',
          subtotals:[]
        }
    },
    methods:{
      filterPayments(){
          let payableId = this.filters.payable;
          let classId = this.filters.class;
          let fees,filteredStudents=[],student,i;

        if(classId == '*'){
            this.fees.forEach(student=>{
              fees = student.fees.filter(fee=>fee.payable_id == payableId)
              filteredStudents.push({...student, fees})
            })
          }else if(classId != '*'){
            this.fees.forEach(student=>{
              if(student.class_id == classId){
              fees = student.fees.filter(fee=>fee.payable_id == payableId),

              filteredStudents.push({...student, fees})
              }
            })
          }
          console.log("Filtered Records", filteredStudents.length);
          this.filteredFees = filteredStudents;
      },

       onSelectPayable(e){
          this.filters.payable=e.target.value
          this.filterPayments()
       },
       onSelectClass(e){
        this.filters.class=e.target.value;
        this.filterPayments()
       },
       updataTotals(data){
          this.subtotals.push(data);
       }
    },
    created(){
        Fire.$on('loadComplete:feesSummary',data=>{
          this.fees=this.filteredFees = data;
        });
        Fire.$on('loadComplete:payables',data=>{
          this.payables = data;
          this.filters.payable = data[0].id;
        });
        Fire.$on('loadComplete:classes',data=>{
          this.classes = data;
        })

    },
    computed:{
       
   }
}
</script>