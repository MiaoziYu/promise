<template>
    <nav class="top-nav">
        <div class="brand">
            <i class="fa fa-check-square-o" aria-hidden="true"></i>
        </div>
        <ul v-if="user" class="nav-menu">
            <li>{{ user.name }}</li>
            <li>{{ user.user_profile.credits }}</li>
        </ul>
    </nav>
</template>

<script>
    import api from '../store/api'

    export default {
        data() {
            return {
                user: null
            }
        },

        beforeMount() {
            this.getUserInfo();
        },

        created() {
            EventBus.$on("finishPromise", this.getUserInfo);
        },

        methods: {
            getUserInfo() {
                api.getUserInfo().then(data => {
                    this.user = data;
                });
            }
        }
    }
</script>