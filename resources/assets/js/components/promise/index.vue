<template>
    <div class="dashboard page-wrapper">

        <!-- ========== action buttons ========== -->
        <div class="action-btns">
            <button @click="toggleHabitForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> habit
            </button>
            <button @click="toggleChallengeForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> weekly challenge
            </button>
            <button @click="togglePromiseForm" class="btn create-btn">
                <i class="fa fa-plus" aria-hidden="true"></i> promise
            </button>
        </div>

        <div class="task-wrapper">

            <!-- ========== habit list ========== -->
            <div class="task-block">
                <h2 class="task-title">Habits</h2>
                <ul id="habit-list" class="habit-list task-list">
                    <li v-for="habit in habits" class="habit-item task-item" :data-id="habit.id">
                        <div class="o-card">
                            <div class="habit-content">
                                <div @click="getHabit(habit.id)" class="habit-text">
                                    <p class="o-card-title">{{ habit.name }}</p>
                                    <p v-if="habit.description" class="o-card-description">{{ habit.description }}</p>
                                    <p class="habit-credits-wrapper">
                                        <span class="habit-credits"><i class="fa fa-diamond" aria-hidden="true"></i>{{ habit.credits }}</span>
                                        <span v-if="hasStreak(habit)" class="habit-bonus">+ {{ habit.credits }}</span>
                                        <span class="check-count">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>{{ habit.count }}
                                        </span>
                                        <span class="streak-count">
                                            <i class="fa fa-bolt" aria-hidden="true"></i>{{ habit.streak }}
                                        </span>
                                    </p>
                                </div>
                                <div v-if="!hasCheckedToday(habit)" class="habit-btn" :class="{streak: hasStreak(habit)}">
                                    <button @click="checkHabit(habit.id)">Check</button>
                                </div>
                                <div v-if="hasCheckedToday(habit)" class="habit-btn checked" :class="{streak: hasStreak(habit)}">
                                    <button>Done</button>
                                </div>
                            </div>
                            <ul @click="getHabit(habit.id)" class="habit-streak" :class="{ streak: habit.streak >= 7 }">
                                <li v-for="n in 7" class="streak-item" :class="{ active: habit.streak >= n }"></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- ========== weekly challenge list ========== -->
            <div class="task-block">
                <h2 class="task-title">Weekly challenges</h2>
                <ul id="challenge-list" class="challenge-list habit-list task-list">
                    <li v-for="challenge in challenges" class="habit-item task-item" :data-id="challenge.id">
                        <div class="o-card">
                            <div v-if="!challenge.failed" class="habit-content">
                                <div @click="getChallenge(challenge.id)" class="habit-text">
                                    <p class="o-card-title">{{ challenge.name }}</p>
                                    <p v-if="challenge.description" class="o-card-description">{{ challenge.description }}</p>
                                    <p class="habit-credits-wrapper">
                                        <span class="habit-credits">
                                            <i class="fa fa-diamond" aria-hidden="true"></i>
                                           {{ getCurrentObtainedChallengeCredit(challenge) }} / {{ challenge.credits }}
                                        </span>
                                        <span v-if="challenge.count > challenge.goal" class="habit-bonus">
                                            + {{ getBonusChallengeCredit(challenge) }}
                                        </span>
                                        <span class="check-count">
                                            <i class="fa fa-check-circle" aria-hidden="true"></i>{{ challenge.count }}
                                        </span>
                                    </p>
                                </div>
                                <div class="habit-btn" :class="{ streak: isSuccess(challenge)}">
                                    <button @click="checkChallenge(challenge.id)">
                                        <span v-if="!isSuccess(challenge)">Check</span>
                                        <span v-if="isSuccess(challenge)">Bonus</span>
                                    </button>
                                </div>
                            </div>
                            <ul v-if="!challenge.failed" @click="getChallenge(challenge.id)" class="habit-streak challenge-progress" :class="{ streak: isSuccess(challenge) }">
                                <li v-for="(goal, index) in challenge.goal" class="streak-item" :class="{ active: index < challenge.count}"></li>
                            </ul>
                            <div v-if="challenge.failed" class="challenge-failed-content habit-content">
                                <div class="habit-text">
                                    <p class="o-card-title">{{ challenge.name }}</p>
                                    <div class="failed-msg">Challenge failed <i class="fa fa-frown-o" aria-hidden="true"></i></div>
                                    <div class="failed-credits">- <i class="fa fa-diamond" aria-hidden="true"></i>{{ challenge.credits / 2 }}</div>
                                </div>
                                <div class="habit-btn failed-btn">
                                    <button @click="restartChallenge(challenge.id)">Restart</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- ========== promise list ========== -->
            <div class="task-block">
                <h2 class="task-title">Promises</h2>
                <ul id="promise-list" class="promise-list task-list">
                    <li v-for="promise in promises"
                        @click="getPromise(promise.id)"
                        class="promise-item task-item"
                        :data-id="promise.id">
                        <div class="o-card">
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

        <!-- ========== new habit form ========== -->
        <new-habit-form :habitForm="habitForm"></new-habit-form>

        <!-- ========== new weekly challenge form ========== -->
        <new-weekly-challenge-form :challengeForm="challengeForm"></new-weekly-challenge-form>

        <!-- ========== new promise form ========== -->
        <new-promise-form :promiseForm="promiseForm"></new-promise-form>

        <!-- ========== habit detail ========== -->
        <div v-if="habit" class="habit o-overlay">
            <div class="o-card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">
                        <input v-model="habit.name"
                               @blur="habit ? updateHabit(habit.id) : null"
                               @keyup.enter="updateHabit(habit.id)"
                               class="title"
                               name="name">
                    </div>
                    <div @click="resetHabit" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <textarea v-model="habit.description"
                              @blur="habit ? updateHabit(habit.id) : null"
                              @keyup.enter="updateHabit(habit.id)"
                              class="description"
                              name="description"
                              placeholder="add description">
                    </textarea>
                    <i class="fa fa-diamond" aria-hidden="true"></i>
                    <input v-model="habit.credits"
                           @blur="habit ? updateHabit(habit.id) : null"
                           @keyup.enter="updateHabit(habit.id)"
                           class="credits"
                           name="credits">

                    <!--habit created date-->
                    <p class="date">created at {{ habit.created_at }}</p>

                    <!--buttons to finish or delete habit-->
                    <div class="form-btns">
                        <div @click="deleteHabit(habit.id)" class="delete-btn btn-secondary">delete habit</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== challenge detail ========== -->
        <div v-if="challenge" class="habit o-overlay">
            <div class="o-card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">
                        <input v-model="challenge.name"
                               @blur="challenge ? updateChallenge(challenge.id) : null"
                               @keyup.enter="updateChallenge(challenge.id)"
                               class="title"
                               name="name">
                    </div>
                    <div @click="resetChallenge" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <textarea v-model="challenge.description"
                              @blur="challenge ? updateChallenge(challenge.id) : null"
                              @keyup.enter="updateChallenge(challenge.id)"
                              class="description"
                              name="description"
                              placeholder="add description">
                    </textarea>
                    <div class="form-group">
                        <label for="">Change challenge goal</label>
                        <input v-model="challenge.goal"
                               @blur="challenge ? updateChallenge(challenge.id) : null"
                               @keyup.enter="updateChallenge(challenge.id)"
                               class="credits"
                               name="goal">
                    </div>                    <i class="fa fa-diamond" aria-hidden="true"></i>
                    <input v-model="challenge.credits"
                           @blur="challenge ? updateChallenge(challenge.id) : null"
                           @keyup.enter="updateChallenge(challenge.id)"
                           class="credits"
                           name="credits">
                    <!--habit created date-->
                    <p class="date">created at {{ challenge.created_at }}</p>

                    <!--buttons to finish or delete habit-->
                    <div class="form-btns">
                        <div @click="deleteChallenge(challenge.id)" class="delete-btn btn-secondary">delete challenge</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== promise detail ========== -->
        <div class="promise o-overlay" v-if="promise && promise.finished_at === null">
            <div class="o-card o-overlay-content">
                <div class="title-wrapper">
                    <div class="title">
                        <input v-model="promise.name"
                               @blur="promise ? updatePromiseText(promise.id) : null"
                               @keyup.enter="updatePromiseText(promise.id)"
                               class="title"
                               name="name">
                    </div>
                    <div @click="resetPromise" class="close-btn">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="content form">
                    <textarea v-model="promise.description"
                              @blur="promise ? updatePromiseText(promise.id) : null"
                              @keyup.enter="updatePromiseText(promise.id)"
                              class="description"
                              name="description"
                              placeholder="add description">
                    </textarea>

                    <i class="fa fa-diamond" aria-hidden="true"></i>
                    <input v-model="promise.reward_credits"
                           @blur="promise ? updatePromiseText(promise.id) : null"
                           @keyup.enter="updatePromiseText(promise.id)"
                           class="credits"
                           name="reward_credits">

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

        <!-- ========== promise success message ========== -->
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
                habit: null,
                habitForm: false,
                challenge: null,
                challenges: null,
                challengeForm: false,
                successMsg: null,
            }
        },

        computed: {
        },

        beforeMount() {
            this.getHabits();
            this.getChallenges();
            this.getPromises();
            EventBus.$emit("setPageName", "promises");
        },

        created() {
            EventBus.$on("clearNewHabitForm", () => {
                this.getHabits();
                this.toggleHabitForm();
            });
            EventBus.$on("clearNewChallengeForm", () => {
                this.getChallenges();
                this.toggleChallengeForm();
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

            $(document).keyup(even => {
                if (even.keyCode === 27) {
                    this.resetPromise();
                    this.resetHabit();
                    this.resetChallenge();
                    this.promiseForm = null;
                    this.habitForm = null;
                }
            });
        },

        mounted() {
            this.setUpSortable();
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

            getHabit(id) {
                api.getHabit(id).then(data => {
                    this.habit = data;
                })
            },

            getChallenges() {
                api.getChallenges().then(data => {
                    this.challenges = data;
                })
            },

            getChallenge(id) {
                api.getChallenge(id).then(data => {
                    this.challenge = data;
                })
            },

            hasTasks(promise) {
                return this.currentList === 'ongoing' && (promise.checklists.length > 0 || promise.punch_card_total > 0);
            },

            hasStreak(habit) {
                return habit.streak >= 7;
            },

            isSuccess(challenge) {
                return challenge.goal <= challenge.count;
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

            getCurrentObtainedChallengeCredit(challenge) {
                return challenge.count < challenge.goal ? Math.floor(challenge.credits / challenge.goal) * challenge.count : challenge.credits;
            },

            getBonusChallengeCredit(challenge) {
                return Math.floor(challenge.credits / challenge.goal) * (challenge.count - challenge.goal) * 2;
            },

            toggleHabitForm() {
                this.habitForm = !this.habitForm;
            },

            toggleChallengeForm() {
                this.challengeForm = !this.challengeForm;
            },

            togglePromiseForm() {
                this.promiseForm = !this.promiseForm;
            },

            resetPromise() {
                this.promise = null;
            },

            resetHabit() {
                this.habit = null;
            },

            resetChallenge() {
                this.challenge = null;
            },

            updateHabit(id) {
                let data = {},
                    eventTarget = $(event.target);
                data[eventTarget.attr("name")] = $(event.target).val();
                api.updateHabit(id, data).then(response => {
                    this.getHabits();
                    eventTarget.blur();
                });
            },

            checkHabit(id) {
                api.checkHabit(id).then(response => {
                    this.getHabits();
                    EventBus.$emit("checkHabit");
                });
            },

            updateChallenge(id) {
                let data = {},
                    eventTarget = $(event.target);
                data[eventTarget.attr("name")] = $(event.target).val();
                api.updateChallenge(id, data).then(response => {
                    this.getChallenges();
                    eventTarget.blur();
                });
            },

            checkChallenge(id) {
                api.checkChallenge(id).then(response => {
                    this.getChallenges();
                    EventBus.$emit("checkChallenge");
                });
            },

            restartChallenge(id) {
                let data = {
                    failed: "false"
                };

                api.updateChallenge(id, data).then(response => {
                    this.getChallenges();
                });
            },

            updatePromiseText(id) {
                let data = {},
                    eventTarget = $(event.target);
                data[eventTarget.attr("name")] = $(event.target).val();
                api.updatePromise(id, data).then(response => {
                    this.getPromises();
                    eventTarget.blur();
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
                        this.resetHabit();
                        this.getHabits();
                    }
                })
            },

            deleteChallenge(id) {
                api.deleteChallenge(id).then(response => {
                    this.resetChallenge();
                    this.getChallenges();
                })
            },

            deletePromise(id) {
                api.deletePromise(id).then(response => {
                    if (response.status == 200) {
                        this.resetPromise();
                        this.getPromises(this.currentList);
                    }
                })
            },

            setUpSortable() {
                let lists = [
                    {
                        id: 'habit-list',
                        path: "habits/reorder"
                    },
                    {
                        id: 'challenge-list',
                        path: ""
                    },
                    {
                        id: 'promise-list',
                        path: ""
                    },
                ];

                $(lists).each(function(index, value) {
                    Sortable.create(document.getElementById(value.id), {
                        onUpdate(event) {
                            let data = [];
                            $('.task-item', $(event.target)).each((index, value) => {
                                data.push({
                                    id: $(value).attr('data-id'),
                                    order: index + 1
                                });
                            });
                            console.log(data);

                            api.updateOrder(value.path, data);
                        },
                    });
                });
            },
        }
    }
</script>
