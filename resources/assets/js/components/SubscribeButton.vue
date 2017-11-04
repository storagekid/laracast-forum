<template>
    <button :class="classes"  
            v-text="buttonText"
            @click="toggleSubscription"></button>
</template>

<script>

    export default {
        props: ['active'],
        data() {
            return {
                isSubscribe: this.active,
            };
        },
        watch: {
            active() {
                this.isSubscribe = active;
            },
        },
        computed: {
            buttonText() {
                return this.isSubscribe ? 'Unsubscribe' : 'Subscribe';
            },
            classes() {
                return ['btn','btn-block',this.isSubscribe ? 'btn-info' : 'btn-success'];
            }
        },
        methods: {
            toggleSubscription() {
                if ( ! this.isSubscribe) {
                    axios.post(location.pathname + '/subscriptions')
                        .then(response => {
                            flash('Subscribed!!');
                        }).catch((error) => {
                            console.log(error.response.data.errors);
                        });
                    this.isSubscribe = true;
                } else {
                    axios.delete(location.pathname + '/subscriptions')
                        .then(response => {
                            flash('Unsubscribe!!!');
                        }).catch((error) => {
                            console.log(error.response.data.errors);
                        });
                    this.isSubscribe = false;
                }
            }
        }
    }

</script>
