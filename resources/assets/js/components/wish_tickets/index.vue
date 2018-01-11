<template>
    <div class="wish-tickets-page page-wrapper">
        <ul class="wish-ticket-list o-list-4">
            <li v-for="ticket in wishTickets"
                class="wish-ticket-item o-list-item">
                <div class="o-card">
                    <div v-if="ticket.image_link !== null" class="o-card-img">
                        <img :src="ticket.image_link" alt="">
                    </div>
                    <p class="title o-card-title">{{ ticket.name }}</p>
                    <button @click="claimConfirmMessage = ticket"
                            class="claim-btn o-card-btn">
                        claim ticket
                    </button>
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
                claimConfirmMessage: null
            }
        },

        beforeMount() {
            this.getWishTickets();
            EventBus.$emit("setPageName", "wishTickets");
        },

        methods: {
            getWishTickets() {
                api.getWishTickets().then(data => {
                    this.wishTickets = data;
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
