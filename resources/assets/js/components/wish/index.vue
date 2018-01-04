<template>
    <div class="wishes-page">
        <ul class="wish-list o-list-4">
            <li v-for="wish in wishes"
                class="o-list-item">
                <div class="card wish-item">
                    <p class="card-title">{{ wish.name }}</p>
                    <p class="card-description">{{ wish.description }}</p>
                    <div v-if="wish.image_link !== null" class="card-img">
                        <img :src="wish.image_link" alt="">
                    </div>
                    <div @click="purchaseWish(wish.id)" class="wish-purchase-btn">
                        Get for <i class="fa fa-diamond" aria-hidden="true"></i><span>{{ wish.credits }}</span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    import api from "../../store/api.js"

    export default {
        data() {
            return {
                wishes: []
            }
        },

        beforeMount() {
            this.getWishes();
        },

        methods: {
            getWishes() {
                api.getWishes().then(data => {
                    this.wishes = data;
                });
            },

            purchaseWish(id) {
                api.purchaseWish(id).then(response => {
                    EventBus.$emit("purchaseWish");
                });
            },
        }
    }
</script>