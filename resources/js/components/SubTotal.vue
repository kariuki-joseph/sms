<template>
   <td>{{ sum }}</td>
</template>
<script>
export default {
    props:{
        adm:'',
        payable_id:'',
        payables:{
            type: Array,
            default: ()=>[]
        },
        fees: {
            type: Array,
            default: ()=>[]
        },
    },
    data(){
        return {
            sum:0,
            subTotals:{
                
            }
        }
    },
    methods:{

    },
    created(){
        let feeEntry, filtered,i,paid=0,payableAmount;

        filtered = this.fees.filter(fee=>fee.payable_id == this.payable_id);
        for(i=0; i<filtered.length; i++){
            feeEntry = filtered[i];
            paid += parseInt(feeEntry.amount);
        }
        filtered = this.payables.filter(payable=>payable.id == this.payable_id);
        payableAmount = filtered[0].amount;
        this.sum = paid;
        
        this.$emit('onTotalling',{
            adm_number:this.adm,
            payable_id:this.payable_id,
            subtotal:paid,
            payableAmount: payableAmount,
            balance:payableAmount-paid
        });
    },
    mounted(){

    },
    computed:{

    }
}
</script>