<template>
    <div class="dashboard">

        <!--action buttons-->
        <div class="promise-action-btns">
            <button @click="getPromises()" class="btn" :class="{ active: promiseListStatus === 'ongoing'}">
                <i class="fa fa-car" aria-hidden="true"></i> ongoing
            </button>
            <button @click="getPromises('finished')" class="btn" :class="{ active: promiseListStatus === 'finished'}">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> finished
            </button>
            <button @click="togglePromiseForm" class="btn create-promise">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>

        <!--promise list-->
        <ul class="promise-list o-list-4">
            <li v-for="promise in promises"
                @click="getPromise(promise.id)"
                class="o-list-item">
                <div class="card promise-item">
                    <p class="title">{{ promise.title }}</p>
                    <p class="description">{{ promise.description }}</p>
                    <div class="progress-bar" v-if="promiseListStatus === 'ongoing' && promise.checklists.length > 0">
                        <div class="progress-bar-current" :style="calculateProgressBarWidth(promise)"></div>
                    </div>
                    <div class="progress-bar" v-if="promiseListStatus === 'ongoing' && promise.punch_card_total > 0">
                        <div class="progress-bar-current" :style="calculateProgressBarWidth(promise)"></div>
                    </div>
                    <i v-if="promiseListStatus === 'finished'" class="stamp-completed fa fa-check" aria-hidden="true"></i>
                </div>
            </li>
        </ul>

        <!--new promise form-->
        <new-promise-form
                :promiseForm="promiseForm"
                v-on:clearNewPromiseForm="getPromises(); togglePromiseForm();">
        </new-promise-form>

        <!--promise detail-->
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
                <div class="content form">
                    <textarea v-model="promise.description"
                              v-if="promise.finished_at == null"
                              @blur="updateText(promise.id)"
                              class="description"
                              name="description"></textarea>
                    <div v-if="promise.finished_at" class="description">{{ promise.description }}</div>

                    <punch-card :promise="promise" v-on:updatePunchCard="getPromises"></punch-card>

                    <checklist :promise="promise" v-on:updateChecklist="getPromises"></checklist>

                    <task-form :promise="promise" v-on:addTask="getPromise(promise.id)"></task-form>

                    <p v-if="promise.finished_at == null" class="date">created at {{ promise.created_at }}</p>
                    <p v-if="promise.finished_at" class="date">finished at {{ promise.finished_at }}</p>
                    <div class="form-btns">
                        <button v-if="promise.finished_at == null" class="form-submit">Finish promise</button>
                        <div @click="deletePromise(promise.id)" class="delete-btn btn-secondary">delete promise</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../../store/api'

    export default {
        data() {
            return {
                promises: null,
                promiseListStatus: "ongoing",
                promise: null,
                promiseForm: false,
            }
        },

        computed: {
        },

        beforeMount() {
            this.getPromises();
        },

        methods: {
            getPromises(status = "ongoing") {
                api.getPromises(status !== "ongoing").then(data => {
                    this.promises = data;
                    this.promiseListStatus = status;
                })
            },

            getPromise(id) {
                api.getPromise(id).then(data => {
                    this.promise = data;
                })
            },

            calculateProgressBarWidth(promise) {
                if (promise.punch_card_total > 0) {
                    return {
                        width: (promise.punch_card_finished / promise.punch_card_total) * 100 + "%"
                    };
                } else if (promise.checklists.length > 0) {
                    let checkListFinished = promise.checklists.filter((element, index) => {
                        return element.status === 1;
                    }).length;
                    return {
                        width: (checkListFinished / promise.checklists.length) * 100 + "%"
                    };
                }
            },

            togglePromiseForm() {
                this.promiseForm = !this.promiseForm;
            },

            resetPromise() {
                this.promise = null;
            },

            updateText(id) {
                let data = {};
                data[$(event.target).attr("name")] = $(event.target).val();
                api.updatePromise(id, data).then(response => {
                    this.getPromises();
                });
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
