<template>
    
    <button type="submit" :class="classes" @click="toggleFavorite">
        <span class="glyphicon glyphicon-heart"></span>
        <span v-text="count"></span>
    </button>

</template>

<script>

    export default {

        props: ['reply'],

        data() {

            return {

                count: this.reply.favoritesCount,
                isFavorite: this.reply.isFavorited,
            };
        },

        computed: {

            classes() {

                return ['btn', this.isFavorite ? 'btn-primary' : 'btn-default'];
            },

            endpoint() {

                return '/replies/' + this.reply.id + '/favorite';
            }

        },

        methods: {

            toggleFavorite() {

                this.isFavorite ? this.unfavorite() : this.favorite();

            },

            unfavorite() {

                axios.delete(this.endpoint);
                this.count--;
                this.isFavorite = false;
            },

            favorite() {

                axios.post(this.endpoint);
                this.count++;
                this.isFavorite = true;
            }
            
        },

    };

</script>

<style>

</style>