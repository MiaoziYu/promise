<template>
    <div class="wishes-page page-wrapper">
        <!--action buttons-->
        <div class="action-btns">
            <button @click="toggleWishForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> add new wish
            </button>
        </div>

        <!-- ========== wish list ========== -->
        <div class="wish-list-wrapper">
            <div class="wish-list-self">
                <ul id="wish-list" class="wish-list o-list-4">
                    <li v-for="wish in wishes"
                        v-if="!isShared(wish)"
                        :data-id="wish.id"
                        class="js-wish-item wish-item o-list-item">
                        <div class="o-card">
                            <div v-if="wish.image_link !== null"
                                 @click="getWish(wish.id)"
                                 class="wish-img o-card-img">
                                <img :src="wish.image_link" alt="">
                                <div v-if="wish.owners.length > 1" class="user-list">
                                    <div v-for="owner in wish.owners" class="user">
                                        <img :src="owner.user_profile.picture" :title="owner.name">
                                    </div>
                                </div>
                            </div>
                            <p @click="getWish(wish.id)" class="title o-card-title">{{ wish.name }}</p>
                            <button @click="purchaseConfirmMessage = wish"
                                    :class="{ active: hasEnoughCredits(wish)}"
                                    class="wish-purchase-btn">
                                <i class="fa fa-diamond" aria-hidden="true"></i><span>{{ wish.credits }}</span>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="wish-list-shared">
                <ul id="wish-list-shared" class="wish-list js-wish-list-shared">
                    <li v-for="wish in wishes"
                        v-if="isShared(wish)"
                        :data-id="wish.id"
                        class="js-wish-item wish-item o-list-item">
                        <div class="o-card">
                            <div v-if="wish.image_link !== null"
                                 @click="getWish(wish.id)"
                                 class="wish-img o-card-img">
                                <img :src="wish.image_link" alt="">
                                <div v-if="wish.owners.length > 1" class="user-list">
                                    <div v-for="owner in wish.owners" class="user">
                                        <img :src="owner.user_profile.picture" :title="owner.name">
                                    </div>
                                </div>
                            </div>
                            <p @click="getWish(wish.id)" class="title o-card-title">{{ wish.name }}</p>
                            <button @click="contributeMessage = wish"
                                    class="wish-contribute-btn">
                                <div :style="calculateProgressBarWidth(wish)" class="background"></div>
                                <div class="content"><i class="fa fa-diamond" aria-hidden="true"></i>{{ wish.credits }}</div>
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

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

                    <div class="wish-users form-group">
                        <div class="u-margin-b-1">owned by:</div>
                        <div v-for="owner in wish.owners" class="user">
                            <img :src="owner.user_profile.picture" :title="owner.name">
                        </div>
                        <div @click="sharedUserForm = true"  class="share">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </div>
                    </div>

                    <div class="wish-share form-group">
                        <input v-if="sharedUserForm" v-model="sharedUserEmail" class="u-margin-b-1" placeholder="email of the user you want to share to">
                        <div v-if="sharedUserForm" class="form-label">
                            <span @click="shareWish(wish.id)" class="label-item active">share</span>
                            <span @click="sharedUserForm = false" class="label-item">cancel</span>
                        </div>
                    </div>

                    <!--wish created date-->
                    <p class="date">created at {{ wish.created_at }}</p>

                    <!--buttons to finish or delete wish-->
                    <div class="form-btns">
                        <div @click="deleteWish(wish.id)" class="delete-btn btn-secondary">delete wish</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== purchase confirmation ========== -->
        <div v-if="purchaseConfirmMessage" class="o-overlay">
            <div v-if="hasEnoughCredits(purchaseConfirmMessage)" class="action-confirmation o-card o-overlay-content">
                <div class="confirmation-msg">
                    <p>Do you want to buy {{ purchaseConfirmMessage.name }}?</p>
                </div>
                <div @click="purchaseWish(purchaseConfirmMessage)" class="confirmation-btn">definitely</div>
                <div @click="hidePurchaseConfirmationMsg" class="cancel-btn">no thanks</div>
            </div>
            <div v-if="!hasEnoughCredits(purchaseConfirmMessage)" class="action-confirmation o-card o-overlay-content">
                <div class="confirmation-msg">
                    <p>Not enough credits</p>
                    <p>Earn credits by finishing more tasks :D</p>
                </div>
                <div @click="hidePurchaseConfirmationMsg" class="cancel-btn">Good idea <i class="fa fa-smile-o" aria-hidden="true"></i></div>
            </div>
        </div>

        <!-- ========== contribute message ========== -->
        <div v-if="contributeMessage" class="wish-contribution o-overlay">
            <div class="action-confirmation o-card o-overlay-content">
                <div class="contributor-list">
                    <div v-for="owner in contributeMessage.owners" class="contributor-wrapper">
                        <div class="contributor">
                            <img :src="owner.user_profile.picture" :title="contributeMessage.name" class="user">
                            <div class="name">{{ owner.name }}</div>
                        </div>
                        <div class="credits"><span class="credits-item"><i class="fa fa-diamond" aria-hidden="true"></i>{{ owner.credits_contributed }}</span><span  class="credits-item">{{ (owner.credits_contributed / contributeMessage.credits * 100).toFixed(2) }}%</span></div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-current" :style="calculateProgressBarWidth(contributeMessage)">
                        <i class="fa fa-diamond" aria-hidden="true"></i>
                        <span>{{ calculateCollectedCredits(contributeMessage) }}</span>
                    </div>
                    <div class="progress-bar-remaining">
                        <i class="fa fa-diamond" aria-hidden="true"></i>
                        <span>{{ contributeMessage.credits - calculateCollectedCredits(contributeMessage) }}</span>
                    </div>
                </div>
                <div class="confirmation-msg">
                    <p>How much do you want to contribute?</p>
                    <input v-model="contributeAmount" class="confirmation-input">
                </div>
                <div @click="contributeWish(contributeMessage)" class="confirmation-btn">Confirm</div>
                <div @click="contributeMessage = null" class="cancel-btn">Cancel</div>
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
                sharedUserForm: false,
                sharedUserEmail: null,
                wishForm: false,
                purchaseConfirmMessage: null,
                contributeMessage: null,
                contributeAmount: 0,
                currentList: "market",
            }
        },

        beforeMount() {
            this.getUserInfo();
            this.getWishes();
            EventBus.$emit("setPageName", "wishes");
        },

        mounted() {
            this.setUpSortable();
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
                    console.log(this.wish)
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

            shareWish(id) {
                api.shareWish(id, {
                    shared_user_email: this.sharedUserEmail
                }).then(response => {
                    this.sharedUserEmail = null;
                    this.sharedUserForm = false;
                });
            },

            purchaseWish(wish) {
                if(this.hasEnoughCredits(wish)) {
                    api.purchaseWish(wish.id).then(response => {
                        EventBus.$emit("purchaseWish");
                        this.getWishes();
                        this.getUserInfo();
                        this.hidePurchaseConfirmationMsg();
                    });
                }
            },

            contributeWish(wish) {
                let data = {
                    credits: this.contributeAmount
                };

                api.contributeWish(wish.id, data).then(response => {
                    this.contributeMessage = null;
                    this.contributeAmount = 0;
                    this.getWishes();
                    EventBus.$emit("contributeWish");
                });
            },

            toggleWishForm() {
                this.wishForm = !this.wishForm;
            },

            hasEnoughCredits(wish) {
                if(this.user) {
                    return this.user.user_profile.credits >= wish.credits;
                }
            },

            isShared(wish) {
                return wish.owners.length > 1;
            },

            hidePurchaseConfirmationMsg() {
                this.purchaseConfirmMessage = null;
            },

            deleteWish(id) {
                api.deleteWish(id).then(response => {
                    this.wish = null;
                    this.getWishes();
                });
            },


            calculateCollectedCredits(wish) {
                let collectedCredits = 0;

                wish.owners.forEach(owner => {
                    collectedCredits += owner.credits_contributed;
                });

                return collectedCredits;
            },

            calculateProgressBarWidth(wish) {
                return {
                    width: (this.calculateCollectedCredits(wish) / wish.credits) * 100 + "%"
                };
            },

            setUpSortable() {
                let lists = ['#wish-list', '#wish-list-shared'];

                $(lists).each(function(index, value) {
                    Sortable.create($(value)[0], {
                        onUpdate(event) {
                            let data = [];
                            $('.js-wish-item', $(event.target)).each((index, value) => {
                                data.push({
                                    id: $(value).attr('data-id'),
                                    order: index + 1
                                });
                            });
                            api.updateOrder("wishes/reorder", data);
                        },
                    });
                });
            },
        }
    }
</script>
