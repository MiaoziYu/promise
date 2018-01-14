<template>
    <div class="habit-create-form o-overlay" v-if="habitForm">
        <div class="o-card o-overlay-content form">
            <div class="title-wrapper">
                <div class="title">Create new habit</div>
                <div @click="clearNewHabitForm" class="close-btn">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="new-habit-name">Name</label>
                <input v-model="habitFormData.name" id="new-habit-name">
            </div>
            <div class="form-group">
                <label for="new-habit-description">Description</label>
                <textarea v-model="habitFormData.description" id="new-habit-description"></textarea>
            </div>
            <div class="form-group">
                <label for="new-habit-credits">Credits</label>
                <input v-model="habitFormData.credits" id="new-habit-credits">
            </div>
            <button @click="createHabit" class="form-submit">Finish and create</button>
        </div>
    </div>
</template>

<script>
    import api from "../../store/api.js";

    export default {
        props: ["habitForm"],

        data() {
            return {
                habitFormData: null
            }
        },

        beforeMount() {
            this.resetFormData();
        },

        methods: {
            createHabit() {
                api.createHabit(this.habitFormData).then(response => {
                    this.clearNewHabitForm();
                });
            },

            clearNewHabitForm() {
                EventBus.$emit("clearNewHabitForm");
                this.resetFormData();
            },

            resetFormData() {
              this.habitFormData = {
                  name: "",
                  description: "",
                  credits: 0
              }
            },
        }
    }
</script>