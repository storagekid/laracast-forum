<template>
    <div>
        <div class="col-xs-2">
          <img :src="avatar" width="50px" height="50px">
          <form v-if="canUpdate" enctype="multipart/form-data">
            <image-upload @loaded="onLoad"></image-upload>
          </form>
        </div>
        <div class="col-xs-10">
            <p>Name: <strong v-text="user.name"></strong></p>
            <p>Email: <strong v-text="user.email"></strong></p>
        </div>
    </div>
</template>

<script>
    import ImageUpload from './ImageUpload.vue';
    export default {
        props: ['user'],
        components: { ImageUpload },
        data() {
            return {
                avatar: this.user.avatar_path,
            };
        },
        computed: {
            canUpdate() {
                // Use Global Method defined in bootstrap.js
                return this.authorize(user => user.id === this.user.id)
            },
        },
        created() {
        },
        methods: {
            onLoad(data) {
                this.persist(data.file);
                this.avatar = data.src;
            },
            persist(file) {
                let data = new FormData();
                data.append('avatar', file);
                axios.post(`/api/users/${this.user.name}/avatar`, data)
                    .then(()=> {
                        flash('Avatar Uploaded','info');
                    }
                );
            }
        }
    };

</script>