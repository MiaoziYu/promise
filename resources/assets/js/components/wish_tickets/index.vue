<template>
    <div class="wish-tickets-page page-wrapper">
        <ul class="wish-ticket-list o-list-4">
            <li v-for="ticket in wishTickets"
                class="wish-ticket-item o-list-item">
                <div class="o-card wish-item">
                    <div class="o-card-delete-btn" @click="deleteWishTicket(ticket.id)"><i class="fa fa-trash-o" aria-hidden="true"></i></div>
                    <div v-if="ticket.image_link !== null" class="o-card-img">
                        <img :src="ticket.image_link" alt="">
                    </div>
                    <p class="o-card-title">{{ ticket.name }}</p>
                    <button @click="claimWishTicket(ticket.id)"
                            class="claim-btn o-card-btn">
                        claim ticket
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
    import api from "../../store/api.js";

    export default {
        data() {
            return {
                wishTickets: null
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
                    this.getWishTickets()
                });
            }
        }
    }
</script>