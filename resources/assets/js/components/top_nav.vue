<template>
    <nav class="top-nav">
        <div class="nav-wrapper container">
            <div class="nav-menu-left">
                <div class="brand menu-item">
                    <i class="logo fa fa-check-square-o" aria-hidden="true"></i>
                    <a href="/" class="menu-link text">Promise</a>
                </div>
                <div v-if="user" class="menu-item"
                     :class="{ active:pageName === 'promises' }">
                    <a class="menu-link" href="/">tasks</a>
                </div>
                <div v-if="user" class="menu-item"
                     :class="{ active:pageName === 'wishes' }">
                    <a class="menu-link" href="/wishes/">market</a>
                </div>
                <div v-if="user" class="menu-item"
                     :class="{ active:pageName === 'wishTickets' }">
                    <a class="menu-link" href="/wish-tickets/">wish tickets</a>
                </div>
                <div v-if="user" class="menu-item"
                     :class="{ active:pageName === 'statistic' }">
                    <a class="menu-link" href="/statistic/">statistic</a>
                </div>
            </div>
            <div v-if="user" class="nav-menu-right">
                <div class="menu-item menu-user">
                    <img class="avatar" :src="user.user_profile.picture" alt="">
                    <span class="name">{{ user.name }}</span>
                </div>
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
                user: null,
                pageName: null
            }
        },

        beforeMount() {
            if (typeof API_TOKEN !== "undefined") {
                this.getUserInfo();
            }
        },

        created() {
            EventBus.$on(["finishPromise", "purchaseWish", "contributeWish","checkHabit", "checkChallenge"], this.getUserInfo);
            EventBus.$on("setPageName", name => {
                this.pageName = name;
            })
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
