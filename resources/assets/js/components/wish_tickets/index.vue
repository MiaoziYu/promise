<template>
    <div class="wish-tickets-page page-wrapper">

        <!-- ========== action buttons ========== -->
        <div class="action-btns">
            <button @click="getWishTickets()" class="btn create-btn" :class="{ active: currentList === 'unclaimed' }">unclaimed</button>
            <button @click="getWishTickets('true')" class="btn create-btn" :class="{ active: currentList === 'claimed' }">claimed</button>
        </div>

        <ul class="wish-ticket-list o-list-4">
            <li v-for="ticketGroup in wishTickets"
                class="wish-ticket-item o-list-item">
                <div class="o-card">
                    <div v-if="ticketGroup[0].wish.image_link !== null" class="o-card-img">
                        <img :src="ticketGroup[0].wish.image_link" alt="">
                        <div v-if="ticketGroup[0].owners.length > 1" class="user-list">
                            <div v-for="owner in ticketGroup[0].owners" class="user">
                                <img :src="owner.user_profile.picture" :title="owner.name">
                            </div>
                        </div>
                    </div>
                    <p class="title o-card-title"><span>{{ ticketGroup[0].wish.name }}</span> <span class="o-card-label">x {{ ticketGroup.length }}</span></p>
                    <button v-if="currentList === 'unclaimed'" @click="claimConfirmMessage = ticketGroup[0]"
                            class="claim-btn o-card-btn">
                        claim ticket
                    </button>
                    <p v-if="currentList === 'claimed'" class="claimed-date o-card-description">claimed at {{ ticketGroup[0].formatted_claimed_date }}</p>
                </div>
            </li>
        </ul>

        <!-- ========== claim confirmation ========== -->
        <div v-if="claimConfirmMessage !== null" class="o-overlay">
            <div class="action-confirmation o-card o-overlay-content">
                <div class="confirmation-msg">
                    <p>Do you want to claim this ticket?</p>
                </div>
                <div @click="claimWishTicket(claimConfirmMessage.id)" class="confirmation-btn">I'm sure</div>
                <div @click="claimConfirmMessage = null" class="cancel-btn">maybe not</div>
            </div>
        </div>
    </div>
</template>

<script>
    import api from "../../store/api.js";

    export default {
        data() {
            return {
                wishTickets: null,
                currentList: "unclaimed",
                claimConfirmMessage: null
            }
        },

        beforeMount() {
            this.getWishTickets();
            EventBus.$emit("setPageName", "wishTickets");
        },

        methods: {
            getWishTickets(claimed) {
                api.getWishTickets(claimed).then(data => {
                    this.wishTickets = data;
                    this.currentList = claimed ? "claimed" : "unclaimed";
                });
            },

            deleteWishTicket(id) {
                api.deleteWishTicket(id).then(response => {
                    this.getWishTickets()
                });
            },

            claimWishTicket(id) {
                api.claimWishTicket(id).then(response => {
                    this.claimConfirmMessage = null;
                    this.getWishTickets()
                });
            }
        }
    }
</script>
