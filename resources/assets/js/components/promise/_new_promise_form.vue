<template>
    <div class="promise-create-form o-overlay" v-if="promiseForm">
        <div class="card o-overlay-content form">
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
            <div class="form-group form-label form-reward">
                <label>Select a reward</label>
                <div @click="promiseFormData.reward_type = 'gift'"
                     :class="{ active: promiseFormData.reward_type == 'gift' }"
                     class="label-item">
                    <i class="fa fa-gift" aria-hidden="true"></i><span>Gift</span>
                </div>
                <div @click="promiseFormData.reward_type = 'points'"
                     :class="{ active: promiseFormData.reward_type == 'points' }"
                     class="label-item">
                    <i class="fa fa-diamond" aria-hidden="true"></i><span>Points</span>
                </div>
            </div>
            <div v-if="promiseFormData.reward_type != null" class="form-group">
                <input v-if="promiseFormData.reward_type == 'gift'"
                       v-model="promiseFormData.reward_content"
                       placeholder="a image link for your gift">
                <input v-if="promiseFormData.reward_type == 'points'"
                       v-model="promiseFormData.reward_content"
                       placeholder="how much points does it worth?">
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
                promiseFormData: {
                    name: "",
                    description: "",
                    reward_type: null,
                    reward_content: ''
                },
            }
        },

        methods: {
            createPromise() {
                api.createPromise(this.promiseFormData).then(response => {
                    console.log(response)
                    if (response.status == 201) {
                        this.clearFormData();
                        this.clearNewPromiseForm();
                    }
                })
            },

            clearNewPromiseForm() {
                this.clearFormData();
                this.$emit("clearNewPromiseForm");
            },

            clearFormData() {
                this.promiseFormData = {
                    name: "",
                    description: "",
                    reward_type: null,
                    reward_content: ""
                };
            },
        }
    }
</script>