<template>
    <div class="dashboard">

        <div class="promise-btns">
            <button @click="getPromises(false)" class="btn" :class="{ active: !showFinishedPromises}">
                <i class="fa fa-car" aria-hidden="true"></i> ongoing
            </button>
            <button @click="getPromises(true)" class="btn" :class="{ active: showFinishedPromises}">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> finished
            </button>
            <button @click="togglePromiseForm" class="btn create-promise">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>

        <ul class="promise-list o-list-4">
            <li v-for="promise in promises"
                @click="getPromise(promise.id)"
                class="o-list-item">
                <div class="card promise-item">
                    <p class="title">{{ promise.title }}</p>
                    <p class="description">{{ promise.description }}</p>
                    <div class="progress-bar" v-if="!showFinishedPromises">
                        <div class="progress-bar-current" :style="calculateProgressBarWidth(promise)"></div>
                    </div>
                    <i v-if="showFinishedPromises" class="stamp-completed fa fa-check" aria-hidden="true"></i>
                </div>
            </li>
        </ul>

        <div class="promise-form o-overlay" v-if="promiseForm">
            <div class="card o-overlay-content form">
                <div class="title-wrapper">
                    <div class="title">Create new promise</div>
                    <div @click="togglePromiseForm" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new-promise-name">Name</label>
                    <input v-model="promiseFormData.title" id="new-promise-name">
                </div>
                <div class="form-group">
                    <label for="new-promise-description">Description</label>
                    <textarea v-model="promiseFormData.description" id="new-promise-description"></textarea>
                </div>
                <div class="form-group form-label">
                    <label>Options</label>
                    <div class="label-item">
                        <i class="fa fa-list-ol" aria-hidden="true"></i><span> Checklist</span>
                    </div>
                    <div class="label-item">
                        <i class="fa fa-check-square-o" aria-hidden="true"></i><span> Count</span>
                    </div>
                </div>
                <div class="form-group">
                    <input v-model="promiseFormData.check_list_quantity" placeholder="the amount of the tasks">
                </div>
                <div class="form-group form-label form-reward">
                    <label>Select a reward</label>
                    <div class="label-item">
                        <i class="fa fa-gift" aria-hidden="true"></i><span>Gift</span>
                    </div>
                    <div class="label-item">
                        <i class="fa fa-usd" aria-hidden="true"></i><span>Points</span>
                    </div>
                </div>
                <button @click="createPromise" class="form-submit">Finish and create</button>
            </div>
        </div>

        <div class="promise o-overlay" v-if="promise">
            <div class="card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">
                        <input v-model="promise.title"
                               v-if="promise.finished_at == null"
                               @blur="updateText(promise.id)"
                               class="title"
                               name="title">
                        <div v-if="promise.finished_at">{{ promise.title }}</div>
                    </div>
                    <div @click="resetPromise" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content">
                    <textarea v-model="promise.description"
                              v-if="promise.finished_at == null"
                              @blur="updateText(promise.id)"
                              class="description"
                              name="description"></textarea>
                    <div v-if="promise.finished_at" class="description">{{ promise.description }}</div>
                    <div class="checkbox-wrapper">
                        <input v-for="n in promise.check_list_finished"
                               v-if="promise.finished_at == null"
                               @click="updateCheckbox(promise)"
                               class="checkbox"
                               :class="{ last: n == promise.check_list_finished }"
                               type="checkbox"
                               checked>
                        <input v-for="n in (promise.check_list_quantity - promise.check_list_finished)"
                               v-if="promise.finished_at == null"
                               @click="updateCheckbox(promise)"
                               class="checkbox"
                               type="checkbox">
                        <i v-for="n in promise.check_list_quantity"
                           v-if="promise.finished_at"
                           class="checkbox-static fa fa-check-square-o" aria-hidden="true"></i>
                    </div>
                    <p v-if="promise.finished_at == null" class="date">created at {{ promise.created_at }}</p>
                    <p v-if="promise.finished_at" class="date">finished at {{ promise.finished_at }}</p>
                    <div @click="deletePromise(promise.id)" class="delete-btn">delete promise</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../store/api'

    export default {
        data() {
            return {
                promises: null,
                promise: null,
                promiseForm: false,
                promiseFormData: {
                    title: "",
                    description: "",
                    task_type: null,
                    check_list_quantity: "",
                    check_list: [],
                    reward_type: null
                },
                showFinishedPromises: null
            }
        },

        computed: {
        },

        beforeMount() {
            this.getPromises();
        },

        methods: {
            getPromises(finished = false) {
                api.getPromises(finished).then(data => {
                    this.promises = data;
                    this.showFinishedPromises = finished;
                })
            },

            getPromise(id) {
                api.getPromise(id).then(data => {
                    this.promise = data;
                })
            },

            calculateProgressBarWidth(promise) {
                return {
                    width: (promise.check_list_finished / promise.check_list_quantity) * 100 + "%"
                };
            },

            resetPromise() {
                this.promise = null;
            },

            togglePromiseForm() {
                this.promiseForm = !this.promiseForm;
            },

            updateText(id) {
                let data = {};
                data[$(event.target).attr("name")] = $(event.target).val();
                api.updatePromise(id, data).then(response => {
                    this.getPromises();
                });
            },

            updateCheckbox(promise) {
                let data = {
                    "check_list_finished": $("input[type=checkbox]:checked", $(event.target).parent()).length
                };
                api.updatePromise(promise.id, data);
                this.getPromises();
            },

            createPromise() {
                api.createPromise(this.promiseFormData).then(response => {
                    if (response.status == 201) {
                        this.getPromises();
                        this.promiseFormData = {
                            title: "",
                            description: "",
                            check_list_quantity: 0
                        };
                        this.togglePromiseForm();
                    }
                })
            },

            deletePromise(id) {
                api.deletePromise(id).then(response => {
                    if (response.status == 200) {
                        this.promise = null;
                        this.getPromises();
                    }
                })
            }
        }
    }
</script>
