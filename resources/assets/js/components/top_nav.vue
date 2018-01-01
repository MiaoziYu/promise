<template>
    <nav class="top-nav">
        <div class="nav-wrapper container">
            <div class="nav-menu-left">
                <div class="brand menu-item">
                    <i class="logo fa fa-check-square-o" aria-hidden="true"></i>
                    <span class="text">Promise</span>
                </div>
                <div class="menu-item"><a class="menu-link" href="/wishes/">Wish market</a></div>
            </div>
            <div v-if="user" class="nav-menu-right">
                <div class="menu-item">{{ user.name }}</div>
                <div class="menu-item">
                    <i class="fa fa-diamond" aria-hidden="true"></i>
                    {{ user.user_profile.credits }}
                </div>
                <div class="menu-item"><i class="fa fa-bell" aria-hidden="true"></i></div>
            </div>
        </div>
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