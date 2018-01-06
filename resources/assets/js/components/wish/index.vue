<template>
    <div class="wishes-page page-wrapper">
        <!--action buttons-->
        <div class="action-btns">
            <button @click="toggleWishForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> add new wish
            </button>
        </div>

        <ul class="wish-list o-list-4">
            <li v-for="wish in wishes"
                class="wish-item o-list-item">
                <div class="o-card wish-item">
                    <div class="o-card-delete-btn" @click="deleteWish(wish.id)"><i class="fa fa-trash-o" aria-hidden="true"></i></div>
                    <div v-if="wish.image_link !== null" class="o-card-img">
                        <img :src="wish.image_link" alt="">
                    </div>
                    <p class="title o-card-title">{{ wish.name }}</p>
                    <button @click="purchaseWish(wish)"
                            :class="{ active: hasEnoughCredits(wish) }"
                            class="wish-purchase-btn">
                        Get for <i class="fa fa-diamond" aria-hidden="true"></i><span>{{ wish.credits }}</span>
                    </button>
                </div>
            </li>
        </ul>

        <new-wish-form :wishForm="wishForm"></new-wish-form>
    </div>
</template>

<script>
    import api from "../../store/api.js"

    export default {
        data() {
            return {
                user: null,
                wishes: [],
                wishForm: false,
                currentList: "market",
            }
        },

        beforeMount() {
            this.getUserInfo();
            this.getWishes();
            EventBus.$emit("setPageName", "wishes");
        },

        created() {
            EventBus.$on("clearNewWishForm", () => {
                this.toggleWishForm();
                this.getWishes();
            })
        },

        methods: {
            getWishes() {
                api.getWishes().then(data => {
                    this.wishes = data;
                });
            },

            getUserInfo() {
                api.getUserInfo().then(data => {
                    this.user = data;
                });
            },

            purchaseWish(wish) {
                if(this.hasEnoughCredits(wish)) {
                    api.purchaseWish(wish.id).then(response => {
                        EventBus.$emit("purchaseWish");
                        this.getWishes();
                        this.getUserInfo();
                    });
                }
            },

            toggleWishForm() {
                this.wishForm = !this.wishForm;
            },

            hasEnoughCredits(wish) {
                if(this.user) {
                    return this.user.user_profile.credits > wish.credits;
                }
            },

            deleteWish(id) {
                api.deleteWish(id).then(response => {
                    this.getWishes();
                });
            }
        }
    }
</script>