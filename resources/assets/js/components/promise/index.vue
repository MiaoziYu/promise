<template>
    <div class="promise-page page-wrapper">

        <!--action buttons-->
        <div class="action-btns">
            <!--<button @click="getPromises()" class="btn" :class="{ active: currentList === 'ongoing'}">-->
                <!--<i class="fa fa-car" aria-hidden="true"></i> ongoing-->
            <!--</button>-->
            <!--<button @click="getPromises('finished')" class="btn" :class="{ active: currentList === 'finished'}">-->
                <!--<i class="fa fa-check-square-o" aria-hidden="true"></i> finished-->
            <!--</button>-->
            <button @click="toggleHabitForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> add habit
            </button>
            <button @click="togglePromiseForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> add promise
            </button>
        </div>

        <div class="task-list">
            <!--habit list-->
            <div class="habit-list">
                <h2 class="task-title">Habits</h2>
                <ul>
                    <li v-for="habit in habits" class="o-card habit-item">
                        <!--<div class="o-card-delete-btn" @click="deleteHabit(habit.id)"><i class="fa fa-trash-o" aria-hidden="true"></i></div>-->
                        <div class="habit-content">
                            <div class="habit-text">
                                <p class="o-card-title">{{ habit.name }}</p>
                                <p v-if="habit.description" class="o-card-description">{{ habit.description }}</p>
                                <p class="habit-credits-wrapper">
                                    <span class="habit-credits"><i class="fa fa-diamond" aria-hidden="true"></i>{{ habit.credits }}</span>
                                    <span v-if="habit.streak >= 7" class="habit-bonus">+ {{ habit.credits }}</span>
                                </p>
                            </div>
                            <div v-if="!hasCheckedToday(habit)" class="habit-btn">
                                <button @click="checkHabit(habit.id)">Check</button>
                            </div>
                            <div v-if="hasCheckedToday(habit)" class="habit-btn checked">
                                <button>Done</button>
                            </div>
                        </div>
                        <ul class="habit-streak" :class="{ streak: habit.streak >= 7 }">
                            <li v-for="(steak, index) in habit.streak" v-if="index < 7" class="streak-item"></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!--promise list-->
            <div class="promise-list">
                <h2 class="task-title">Promise</h2>
                <ul class="o-list-4">
                    <li v-for="promise in promises"
                        @click="getPromise(promise.id)"
                        class="o-list-item">
                        <div class="o-card promise-item">
                            <p class="o-card-title">{{ promise.name }}</p>
                            <p v-if="promise.description" class="o-card-description">{{ promise.description }}</p>
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
            </div>
        </div>

        <!--new habit form-->
        <new-habit-form :habitForm="habitForm"></new-habit-form>

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
                               name="name">
                    </div>
                    <div @click="resetPromise" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <textarea v-model="promise.description"
                              @blur="updateText(promise.id)"
                              class="description"
                              name="description"
                              placeholder="add description">
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
                        <button v-if="!hasTasks(promise)" @click="finishPromise(promise)" class="form-submit">Finish promise</button>
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
                    <!--<div class="description">{{ promise.description }}</div>-->
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

        <div v-if="successMsg !== null" class="o-overlay">
            <div class="promise-success o-card o-overlay-content">
                <div class="success-msg">
                    <p>Congrats! you've finished a promise!!</p>
                    <p>and you got</p>
                </div>
                <div v-if="successMsg.reward_type === 'points'" class="success-points">
                    <i class="fa fa-diamond" aria-hidden="true"></i><span>{{ successMsg.reward_credits }}</span>
                </div>
                <div v-if="successMsg.reward_type === 'gift'" class="success-img o-card-img">
                    <img :src="successMsg.reward_image_link" alt="">
                </div>
                <div class="success-name" v-if="successMsg.reward_type === 'gift'">{{ successMsg.reward_name }}</div>
                <div @click="successMsg = null" class="success-btn"><i class="fa fa-smile-o" aria-hidden="true"></i> YAY!</div>
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
                habits: null,
                habitForm: false,
                successMsg: null,
            }
        },

        computed: {
        },

        beforeMount() {
            this.getHabits();
            this.getPromises();
            EventBus.$emit("setPageName", "promises");
        },

        created() {
            EventBus.$on("clearNewHabitForm", () => {
                this.getHabits();
                this.toggleHabitForm();
            });
            EventBus.$on("clearNewPromiseForm", () => {
                this.getPromises();
                this.togglePromiseForm();
            });
            EventBus.$on(["updateChecklist", "updatePunchCard", "addTask"], this.getPromises);
            EventBus.$on(["deleteChecklist", "addTask"], () => {
                this.getPromise(this.promise.id);
            });
            EventBus.$on(["finishChecklist", "finishPunchCard"], () => {
                this.finishPromise(this.promise);
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

            getHabits() {
                api.getHabits().then(data => {
                    this.habits = data;
                })
            },

            hasTasks: function(promise) {
                return this.currentList === 'ongoing' && (promise.checklists.length > 0 || promise.punch_card_total > 0);
            },

            hasCheckedToday(habit) {
                let today = new Date(),
                    formattedDate = `${today.getFullYear()}-${today.getMonth()}-${today.getDate()}`,
                    habitCheckedDate = new Date(habit.checked_at),
                    formattedHabitCheckedDate = `${habitCheckedDate.getFullYear()}-${habitCheckedDate.getMonth()}-${habitCheckedDate.getDate()}`;

                return formattedDate === formattedHabitCheckedDate;
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

            toggleHabitForm() {
                this.habitForm = !this.habitForm;
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

            checkHabit(id) {
                api.checkHabit(id).then(response => {
                    this.getHabits();
                    EventBus.$emit("checkHabit");
                });
            },

            finishPromise(promise) {
                let data = {
                    finished: "true"
                };
                api.finishPromise(promise.id).then(response => {
                    this.resetPromise();
                    this.getPromises();
                    this.successMsg = {
                        reward_type: promise.reward_type,
                        reward_name: promise.reward_name,
                        reward_credits: promise.reward_credits,
                        reward_image_link: promise.reward_image_link,
                    };
                    EventBus.$emit("finishPromise");
                });
            },

            deleteHabit(id) {
                api.deleteHabit(id).then(response => {
                    if (response.status == 200) {
                        this.getHabits();
                    }
                })
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
