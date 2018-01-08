<template>
    <div class="wishes-page page-wrapper">
        <!--action buttons-->
        <div class="action-btns">
            <button @click="toggleWishForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> add new wish
            </button>
        </div>

        <!-- ========== wish list ========== -->
        <ul class="wish-list o-list-4">
            <li v-for="wish in wishes"
                @click="getWish(wish.id)"
                class="o-list-item">
                <div class="o-card wish-item">
                    <div v-if="wish.image_link !== null" class="o-card-img">
                        <img :src="wish.image_link" alt="">
                    </div>
                    <p class="title o-card-title">{{ wish.name }}</p>
                    <button @click="purchaseWish(wish)"
                            :class="{ active: hasEnoughCredits(wish) }"
                            class="wish-purchase-btn">
                        <i class="fa fa-diamond" aria-hidden="true"></i><span>{{ wish.credits }}</span>
                    </button>
                </div>
            </li>
        </ul>

        <!-- ========== wish detail ========== -->
        <div v-if="wish" class="wish o-overlay">
            <div class="o-card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">
                        <input v-model="wish.name"
                               @blur="wish ? updateWish(wish.id) : null"
                               @keyup.enter="updateWish(wish.id)"
                               class="title"
                               name="name">
                    </div>
                    <div @click="wish = null" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <div class="form-group">
                        <label for="">image link</label>
                        <input v-model="wish.image_link"
                               @blur="wish ? updateWish(wish.id) : null"
                               @keyup.enter="updateWish(wish.id)"
                               class="image_link"
                               name="image_link">
                    </div>
                    <i class="fa fa-diamond" aria-hidden="true"></i>
                    <input v-model="wish.credits"
                           @blur="wish ? updateWish(wish.id) : null"
                           @keyup.enter="updateWish(wish.id)"
                           class="credits"
                           name="credits">

                    <!--wish created date-->
                    <p class="date">created at {{ wish.created_at }}</p>

                    <!--buttons to finish or delete wish-->
                    <div class="form-btns">
                        <div @click="deleteWish(wish.id)" class="delete-btn btn-secondary">delete wish</div>
                    </div>
                </div>
            </div>
        </div>

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
                wish: null,
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

            $(document).keyup(even => {
                if (even.keyCode === 27) {
                    this.wish = null;
                }
            })
        },

        methods: {
            getWishes() {
                api.getWishes().then(data => {
                    this.wishes = data;
                });
            },

            getWish(id) {
                api.getWish(id).then(data => {
                    this.wish = data;
                });
            },

            getUserInfo() {
                api.getUserInfo().then(data => {
                    this.user = data;
                });
            },

            updateWish(id) {
                let data = {},
                    eventTarget = $(event.target);
                data[eventTarget.attr("name")] = $(event.target).val();
                api.updateWish(id, data).then(response => {
                    this.getWishes();
                    eventTarget.blur();
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
                    return this.user.user_profile.credits >= wish.credits;
                }
            },

            deleteWish(id) {
                api.deleteWish(id).then(response => {
                    this.wish = null;
                    this.getWishes();
                });
            }
        }
    }
</script>
