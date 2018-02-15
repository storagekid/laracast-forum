<template>
    
    <div class="panel" :class="isBest ? 'panel-success' : 'panel-default'">
        <div class="panel-heading level">
            <div class="flex">
                <a :href="'/profiles/'+reply.owner.name" v-text="reply.owner.name"></a> - 
                <span v-text="ago"></span>
            </div>
            
            <div v-show="signedIn">
                <favorite :reply="reply">
                </favorite>
            </div>

        </div>
        <form @submit.prevent="updateReply">
            <div class="panel-body">
                <div class="level" :id="'reply-'+reply.id">
                        <div class="form-group">
                            <div v-if="editing">
                                <textarea rows="4" placeholder="Editing..." class="form-control" v-model="replyBody" required></textarea>
                            </div>
                            <div v-else="" v-html="replyBody">
                            </div>
                        </div>
                </div>
           </div>
           <div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
                <div class="flex" v-if="authorize('owns', reply)">
                    <div style="margin-right: 1em;" v-show="editing">
                        <button class="btn btn-xs btn-primary">Save Changes
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-xs btn-warning"
                            @click.prevent="toggleEdit" v-text="editButtonText">
                        </button>
                        <button type="submit" class="btn btn-xs btn-danger"
                                @click="deleteReply">Delete Reply
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-xs btn-info" v-show="!isBest" v-if="authorize('owns', reply.thread)"
                    @click.prevent="markBestReply" v-text="">Best Reply
                </button>
           </div>
       </form>

    </div>

</template>

<script>

import Favorite from './Favorite.vue';
import moment from 'moment';

    export default {
        props: ['reply'],
        components: { Favorite },
        data() {
            return {
                isBest: this.reply.isBest,
                editing: false,
                editButtonText: 'Edit Reply',
                replyBody: this.reply.body,
            };
        },
        computed: {
            ago() {
                return moment(this.reply.created_at).fromNow();
            }
        },
        methods: {
            toggleEdit() {
                if (!this.editing) {
                    this.editing = true;
                    this.editButtonText = 'Cancel Edit';
                }
                else {
                    this.editing = false;
                    this.editButtonText = 'Edit Reply';
                }
             },
            updateReply() {
                if(this.reply.body == this.replyBody) {
                    flash('Nothing changed!!');
                    return;
                }
                axios.patch('/replies/'+this.reply.id, {
                    body: this.replyBody,
                }).catch(error => {
                    flash(error.response.data, 'danger');
                });

                this.editing = false;
                this.editButtonText = 'Edit Reply';
                flash('The reply has been updated!!')
            },
            deleteReply() {
                axios.delete('/replies/'+this.reply.id);
                this.$emit('deleted', this.reply.id);
            },
            markBestReply() {
                window.events.$emit('best-selected', this.reply.id);
                axios.post('/replies/'+this.reply.id+'/best');
            },
        },
        created() {
            window.events.$on('best-selected', id => {
                console.log(id);
                this.isBest = (id === this.reply.id);
            });
        }
    };

</script>

<style>

</style>