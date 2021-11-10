<template>
    <div class="container">
        <div class="row justify-content-center" v-if="$gate.isAdmin()">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Developer Component</div>

                    <div class="card-body">
                        You are now in the developer component!
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {

        }
    }
</script>
