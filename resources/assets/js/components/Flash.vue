<template>
    <div class="alert alert-success alert-flash" role="alert" v-show="flashEvent">
      <strong>Success!</strong> {{ body }}
    </div>
</template>

<script>

    export default {

        props: ['message'],
        
        data() {

            return {

                body: '',
                flashEvent: false,
            };
        },

        created() {

            if (this.message) {

                this.showFlash(this.message);
            }

            window.events.$on('flash', message => {

                this.showFlash(message);
            })
        },

        methods: {

            showFlash(message) {

               this.body = message,
               this.flashEvent = true;   

               this.hideFlash();  

            },

            hideFlash() {

                setTimeout(()=> {

                    this.flashEvent = false;

                }, 3000);
            }
        }
    };

</script>

<style>

    .alert-flash{
        position: fixed;
        right: 25px;
        bottom: 25px;    
    }

</style>