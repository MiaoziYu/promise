<template>
    <div class="promise-create-form o-overlay" v-if="promiseForm">
        <div class="o-card o-overlay-content form">
            <div class="title-wrapper">
                <div class="title">Create new promise</div>
                <div @click="clearNewPromiseForm" class="close-btn">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="new-promise-name">Name</label>
                <input v-model="promiseFormData.name" id="new-promise-name">
            </div>
            <div class="form-group">
                <label for="new-promise-description">Description</label>
                <textarea v-model="promiseFormData.description" id="new-promise-description"></textarea>
            </div>
            <div class="form-group">
                <label for="new-promise-credits">Credits</label>
                <input v-model="promiseFormData.credits" id="new-promise-credits">
            </div>
            <button @click="createPromise" class="form-submit">Finish and create</button>
        </div>
    </div>
</template>

<script>
    import api from '../../store/api'

    export default {
        props: ["promiseForm"],

        data() {
            return {
                promiseFormData: {},
            }
        },

        beforeMount() {
            this.resetFormData();
        },

        methods: {
            createPromise() {
                api.createPromise(this.promiseFormData).then(response => {
                    if (response.status == 201) {
                        this.clearNewPromiseForm();
                    }
                })
            },

            clearNewPromiseForm() {
                this.resetFormData();
                EventBus.$emit("clearNewPromiseForm");
            },

            resetFormData() {
                this.promiseFormData = {
                    name: "",
                    description: "",
                    credits: "",
                };
            },
        }
    }
</script>
