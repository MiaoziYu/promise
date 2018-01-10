<template>
    <div class="challenge-create-form o-overlay" v-if="challengeForm">
        <div class="o-card o-overlay-content form">
            <div class="title-wrapper">
                <div class="title">Create new weekly challenge</div>
                <div @click="clearNewChallengeForm" class="close-btn">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="new-challenge-name">Name</label>
                <input v-model="challengeFormData.name" id="new-challenge-name">
            </div>
            <div class="form-group">
                <label for="new-challenge-description">Description</label>
                <textarea v-model="challengeFormData.description" id="new-challenge-description"></textarea>
            </div>
            <div class="form-group">
                <label for="new-challenge-credits">Credits</label>
                <input v-model="challengeFormData.credits" id="new-challenge-credits">
            </div>
            <div class="form-group">
                <label for="new-challenge-goal">week goal</label>
                <input v-model="challengeFormData.goal" id="new-challenge-goal">
            </div>
            <button @click="createChallenge" class="form-submit">Finish and create</button>
        </div>
    </div>
</template>

<script>
    import api from "../../store/api.js";

    export default {
        props: ["challengeForm"],

        data() {
            return {
                challengeFormData: null
            }
        },

        beforeMount() {
            this.resetFormData();
        },

        methods: {
            createChallenge() {
                api.createChallenge(this.challengeFormData).then(response => {
                    this.clearNewChallengeForm();
                });
            },

            clearNewChallengeForm() {
                EventBus.$emit("clearNewChallengeForm");
                this.resetFormData();
            },

            resetFormData() {
                this.challengeFormData = {
                    name: "",
                    description: "",
                    credits: 0,
                    goal: 0
                }
            },
        }
    }
</script>