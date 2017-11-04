<template>
    <div>
        <div v-if="signedIn">
            <div class="alert alert-danger" v-for="error in errors">
                <p>{{error}}</p>
            </div>
            <div class="form-group">
                <label for="body">Reply</label>
                <textarea   name="body" 
                            id="body" 
                            class="form-control" 
                            placeholder="Have something to say?" 
                            v-model="body"
                            required>      
                </textarea>
            </div>
            <div class="form-group">
                <button type="button" 
                        class="btn btn-default btn-primary"
                        @click="addReply">Reply
                </button>
            </div>
        </div>
        <div class="alert alert-warning" v-else>
            Please <a href="/login">sign in</a> to participate.
        </div>
    </div>
</template>

<script>

    export default {

        data() {

            return  {

                body: '',
                errors: '',
            };
        },

        computed: {

            signedIn() {

                return window.App.signedIn;
            }
        },

        methods: {

            addReply() {

                axios.post(location.pathname + '/replies', { body: this.body })
                    .catch((error) => {
                        flash(error.response.data, 'danger');
                    })
                    .then(response => {
                        this.body = '';
                        this.$emit('replyCreated', response.data);
                        flash('Your Reply has been posted.', 'success');
                    });
            }
        }

    };

</script>