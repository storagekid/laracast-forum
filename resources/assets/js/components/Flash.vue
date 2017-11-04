<template>
    <div :class="classes" role="alert" v-show="flashEvent">
      <strong>Success!</strong> {{ body }}
    </div>
</template>

<script>

    export default {

        props: ['message'],
        
        data() {

            return {

                body: '',
                label: '',
                flashEvent: false,
            };
        },
        computed: {
            classes() {
                return 'alert-flash alert alert-' + this.label;
            }
        },
        created() {

            if (this.message) {

                this.showFlash(this.message);
            }

            window.events.$on('flash', data => {

                this.showFlash(data);
            })
        },

        methods: {

            showFlash(data) {

               this.body = data.message,
               this.label = data.label,
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