<template>
     <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
            <th>Payment Type</th>
            <th>Serial No.</th>
            <th>Date Received</th>
            <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="payment in feesDetails" :key="payment.id">
            <td v-if="payment.payable">{{ payment.payable.name }}</td>
            <td>{{ payment.id }}</td>
            <td>{{ payment.created_at }}</td>
            <td>Ksh. {{ payment.amount }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props:{
        adm:{}
    },
    data(){
        return {
            feesDetails:''
        }
    },
    methods:{
        getPayments(){
            axios.get('students/'+this.adm+'/fees').then(({data})=>{
                this.feesDetails = data;
            })
        }
    },
    created(){
        this.getPayments();
        console.log(this.adm)
    },
    mounted(){
        
    },
    computed:{

    }
}
</script>