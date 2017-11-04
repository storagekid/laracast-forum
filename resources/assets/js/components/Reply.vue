<template>
    
    <div class="panel panel-default">
        <div class="panel-heading level">
            <div class="flex">
                <a :href="'/profiles/'+data.owner.name" v-text="data.owner.name"></a> - 
                <span v-text="ago"></span>
            </div>
            
            <div v-show="signedIn">
                <favorite :reply="data">
                </favorite>
            </div>

        </div>
        <div class="panel-body">
            <div class="level" :id="'reply-'+data.id">
                <form class="flex">
                    <div class="form-group">
                        <div v-if="editing">
                            <textarea rows="4" placeholder="Editing..." class="form-control" v-model="replyBody"></textarea>
                        </div>
                        <div v-else="" v-text="replyBody">
                        </div>
                    </div>
                </form>
            </div>
       </div>

           <div class="panel-footer level" v-if="canUpdate">
                <div style="margin-right: 1em;" v-show="editing">
                    <button type="submit" class="btn btn-xs btn-primary"
                        @click="updateReply">Save Changes
                    </button>
                </div>
                <div class="flex">
                    <button type="submit" class="btn btn-xs btn-warning"
                        @click="toggleEdit" v-text="editButtonText">
                    </button>
                </div>
                <button type="submit" class="btn btn-xs btn-danger"
                        @click="deleteReply">Delete Reply
                </button>
           </div>

    </div>

</template>

<script>

import Favorite from './Favorite.vue';
import moment from 'moment';

    export default {

        props: ['data'],

        components: { Favorite },

        data() {

            return {

                editing: false,
                editButtonText: 'Edit Reply',
                replyBody: this.data.body,
            };
        },

        computed: {

            signedIn() {
                return window.App.signedIn;
            },
            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id);
            },
            ago() {
                return moment(this.data.created_at).fromNow();
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
                if(this.data.body == this.replyBody) {
                    flash('Nothing changed!!');
                    return;
                }
                axios.patch('/replies/'+this.data.id, {
                    body: this.replyBody,
                });

                this.editing = false;
                this.editButtonText = 'Edit Reply';
                flash('The reply has been updated!!')
            },

            deleteReply() {
                axios.delete('/replies/'+this.data.id);
                this.$emit('deleted', this.data.id);
            }
        },

    };

</script>

<style>

</style>