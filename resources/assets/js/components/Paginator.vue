<template>
    
    <ul class="pagination" v-show="withPagination">
        <li v-show="previousPage">
          <a href="#" aria-label="Previous" rel="prev" @click.prevent="page--">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li v-show="nextPage">
          <a href="#" aria-label="Next" rel="next" @click.prevent="page++">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
    </ul>

</template>

<script>

    export default {

        props: ['dataSet'],

        data() {

            return {

                page: 1,
                previousPage: false,
                nextPage: false,
                lastPage: false,
            }
        },

        watch: {

            dataSet() {

                this.page = this.dataSet.current_page;
                this.previousPage = this.dataSet.prev_page_url;
                this.nextPage = this.dataSet.next_page_url;
                this.lastPage = this.dataSet.last_page;

                if (this.page > this.lastPage) {
                    this.page = this.lastPage;
                    this.broadcast().updateUrl();
                }
            },

            page() {

                this.broadcast().updateUrl();
            }
        },

        computed: {

            withPagination() {

                return (this.previousPage || this.nextPage) ? true : false;
            }
        },

        methods: {

            broadcast(page = this.page) {

                return this.$emit('changed', page);
            },

            updateUrl(page = this.page) {

                history.pushState(null, null, '?page='+page);
            }

        }
    };

</script>
