<template>
    <li class="dropdrow" v-if="notifications">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <button type="button" class="btn btn-danger ">
            <span class="glyphicon glyphicon-bell"></span> 
            <span class="badge" v-text="notificationsCount"></span>
        </button>
        </a>
        <ul class="dropdown-menu">
            <li v-for="notification in notifications">
                <a :href="notification.data.link" 
                    v-text="notification.data.message" 
                    @click="markAsRead(notification)"> 
                </a>
            </li>
        </ul>
    </li>
</template>

<script>

    export default {
        data() {
            return {
                notifications: false,
            };
        },
        watch: {
            notifications() {
                if (this.notifications.length == 0) this.notifications = false;
            }
        },
        computed: {
            notificationsCount() {
                return this.notifications.length;
            }
        },
        created() {
            axios.get("/profiles/" + window.App.user.name + "/notifications")
                .then(response => {
                    this.notifications = response.data;
                });
        },
        methods: {
            markAsRead(notification) {
                axios.delete("/profiles/" + window.App.user.name + "/notifications/"+notification.id);
            }
        }
    };

</script>