<template>
   <td>{{ balance }}</td>
</template>
<script>
export default {
    props:{
        adm:'',
        subtotals:{
            type: Array,
            default: ()=>[]
        }
    },
    data(){
        return {
            balance:''
        }
    },
    methods:{
        getBalance(){
            this.balance = this.subtotals.filter(obj=>obj.adm_number == this.adm)[0].balance
        }
    },
    created(){
    this.getBalance();
    },
}
</script>