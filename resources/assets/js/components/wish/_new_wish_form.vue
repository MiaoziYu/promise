<template>
    <div class="wish-create-form o-overlay" v-if="wishForm">
        <div class="o-card o-overlay-content form">
            <div class="title-wrapper">
                <div class="title">Create new wish</div>
                <div @click="clearNewWishForm" class="close-btn">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="new-wish-name">Name</label>
                <input v-model="wishFormData.name" id="new-wish-name">
            </div>
            <div class="form-group">
                <label for="new-wish-image">image link</label>
                <input v-model="wishFormData.image_link" id="new-wish-image">
            </div>
            <div class="form-group">
                <label for="new-wish-credit">Price</label>
                <input v-model="wishFormData.credits" id="new-wish-credit">
            </div>
            <button @click="createWish" class="form-submit">Finish and create</button>
        </div>
    </div>
</template>

<script>
    import api from '../../store/api'

    export default {
        props: ["wishForm"],

        data() {
            return {
                wishFormData: {},
            }
        },

        beforeMount() {
            this.clearFormData();
        },

        methods: {
            createWish() {
                api.createWish(this.wishFormData).then(response => {
                    if (response.status == 201) {
                        this.clearNewWishForm()
                    }
                })
            },

            clearFormData() {
                this.wishFormData = {
                    name: "",
                    description: "",
                    credits: null,
                    image_link: ""
                };
            },

            clearNewWishForm() {
                this.clearFormData();
                EventBus.$emit("clearNewWishForm");
            }
        }
    }
</script>