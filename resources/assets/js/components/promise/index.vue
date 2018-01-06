<template>
    <div class="promise-page page-wrapper">

        <!--action buttons-->
        <div class="action-btns">
            <button @click="getPromises()" class="btn" :class="{ active: currentList === 'ongoing'}">
                <i class="fa fa-car" aria-hidden="true"></i> ongoing
            </button>
            <button @click="getPromises('finished')" class="btn" :class="{ active: currentList === 'finished'}">
                <i class="fa fa-check-square-o" aria-hidden="true"></i> finished
            </button>
            <button @click="togglePromiseForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>

        <!--promise list-->
        <ul class="promise-list o-list-4">
            <li v-for="promise in promises"
                @click="getPromise(promise.id)"
                class="o-list-item">
                <div class="o-card promise-item">
                    <p class="o-card-title">{{ promise.name }}</p>
                    <p class="o-card-description">{{ promise.description }}</p>
                    <div v-if="promise.reward_type === 'points'" class="reward-points">
                        <div class="reward-content">
                            <i class="fa fa-diamond" aria-hidden="true"></i><span>{{ promise.reward_credits }}</span>
                        </div>
                    </div>
                    <div v-if="promise.reward_type === 'gift'" class="o-card-img">
                        <img :src="promise.reward_image_link" alt="">
                    </div>
                    <div class="progress-bar" v-if="hasTasks(promise)">
                        <div class="progress-bar-current" :style="calculateProgressBarWidth(promise)"></div>
                    </div>
                    <i v-if="currentList === 'finished'" class="stamp-completed fa fa-check" aria-hidden="true"></i>
                </div>
            </li>
        </ul>

        <!--new promise form-->
        <new-promise-form :promiseForm="promiseForm"></new-promise-form>

        <!--ongoing promise detail-->
        <div class="promise o-overlay" v-if="promise && promise.finished_at === null">
            <div class="o-card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">
                        <input v-model="promise.name"
                               @blur="updateText(promise.id)"
                               class="title"
                               name="title">
                    </div>
                    <div @click="resetPromise" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <textarea v-model="promise.description"
                              @blur="updateText(promise.id)"
                              class="description"
                              name="description">
                    </textarea>

                    <!--component to show and update punch card-->
                    <punch-card :promise="promise"></punch-card>

                    <!--component to show and update checklist-->
                    <checklist :promise="promise"></checklist>

                    <!--component to add and edit tasks-->
                    <task-form :promise="promise"></task-form>

                    <!--promise created date-->
                    <p class="date">created at {{ promise.created_at }}</p>

                    <!--buttons to finish or delete promise-->
                    <div class="form-btns">
                        <button v-if="!hasTasks(promise)" @click="finishPromise(promise.id)" class="form-submit">Finish promise</button>
                        <div @click="deletePromise(promise.id)" class="delete-btn btn-secondary">delete promise</div>
                    </div>
                </div>
            </div>
        </div>

        <!--finished promise detail-->
        <div class="promise o-overlay" v-if="promise && promise.finished_at">
            <div class="o-card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">{{ promise.name }}</div>
                    <div @click="resetPromise" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <div class="description">{{ promise.description }}</div>
                    <div class="checkbox-wrapper">
                        <i v-for="n in promise.punch_card_total"
                           class="checkbox-static fa fa-check-square-o" aria-hidden="true"></i>
                    </div>
                    <ul v-if="promise.checklists.length > 0">
                        <li v-for="checklist in promise.checklists" class="checkbox-wrapper checklist">
                            <i class="checkbox-static fa fa-check-square-o" aria-hidden="true"></i>
                            <label class="label-static" for="">{{ checklist.text }}</label>
                        </li>
                    </ul>
                    <p class="date">finished at {{ promise.finished_at }}</p>
                    <div class="form-btns">
                        <div @click="deletePromise(promise.id)" class="delete-btn btn-secondary">delete promise</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="successMsg" class="o-overlay">
            <div class="promise-success card o-overlay-content">
                <p class="success-msg">Congrats! you've finished a promise!!</p>
                <div class="success-icon">
                    <i class="fa fa-smile-o" aria-hidden="true"></i>
                    <i class="fa fa-heart" aria-hidden="true"></i>
                </div>
                <div @click="successMsg = false" class="success-btn">YAY!</div>
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
                currentList: "ongoing",
                promise: null,
                promiseForm: false,
                successMsg: false,
            }
        },

        computed: {
        },

        beforeMount() {
            this.getPromises();
            EventBus.$emit("setPageName", "promises");
        },

        created() {
            EventBus.$on("clearNewPromiseForm", () => {
                this.getPromises();
                this.togglePromiseForm();
            });
            EventBus.$on(["updateChecklist", "updatePunchCard", "addTask"], this.getPromises);
            EventBus.$on(["deleteChecklist", "addTask"], () => {
                this.getPromise(this.promise.id);
            });
            EventBus.$on(["finishChecklist", "finishPunchCard"], () => {
                this.finishPromise(this.promise.id);
            });
        },

        methods: {
            getPromises(status = "ongoing") {
                api.getPromises(status !== "ongoing").then(data => {
                    this.promises = data;
                    this.currentList = status;
                })
            },

            getPromise(id) {
                api.getPromise(id).then(data => {
                    this.promise = data;
                })
            },

            hasTasks: function(promise) {
                return this.currentList === 'ongoing' && (promise.checklists.length > 0 || promise.punch_card_total > 0);
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

            finishPromise(id) {
                let data = {
                    finished: "true"
                };
                api.finishPromise(id).then(response => {
                    this.resetPromise();
                    this.getPromises();
                    this.successMsg = true;
                    EventBus.$emit("finishPromise");
                });
            },

            deletePromise(id) {
                api.deletePromise(id).then(response => {
                    if (response.status == 200) {
                        this.resetPromise();
                        this.getPromises(this.currentList);
                    }
                })
            }
        }
    }
</script>
