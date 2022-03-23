<template>
    <div class="card-header">
        <div class="row">
        <div class="container">
            <h3 class="card-title">{{ title }}</h3>
        </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                    <button class="btn btn-success" @click="emitOpenModal()"><i :class="icon"></i> {{ icon_text }} </button>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2">
                    
            </div>

            <div class="col-sm-4 col-md-4 col-lg-4">

            </div>

            <div class="col-sm-2 col-md-2 col-lg-2">
               
            </div>

            <div class="col-sm-2 col-md-2 col-lg-2">
                <button class="btn btn-secondary" data-toggle="modal" data-target="#modalExportOptions"><i class="fas fa-file-export fa-fw"></i> Export As </button>
            </div>

        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                
            }
        },
        props:{
            title:{
                type: String,
            },

            icon_text: {
                type: String,
            },

            icon: {
                default: "Add New"
            }
        },
        methods: {
            emitOpenModal(){
                return this.$emit("openModal");
            }
        },
        created() {

        }

    }
</script>

