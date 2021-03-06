
/**
 * First we will load all of this project"s JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

window.EventBus = new Vue();

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import store from "./store"

Vue.component("promise", require("./components/dashboard/index.vue"));
Vue.component("top-nav", require("./components/top_nav.vue"));
Vue.component("punch-card", require("./components/dashboard/_punch_card.vue"));
Vue.component("checklist", require("./components/dashboard/_checklist.vue"));
Vue.component("task-form", require("./components/dashboard/_task_form.vue"));
Vue.component("new-promise-form", require("./components/dashboard/_new_promise_form.vue"));

Vue.component("new-habit-form", require("./components/dashboard/_new_habit_form"));

Vue.component("new-weekly-challenge-form", require("./components/dashboard/_new_weekly_challenge_form"));

Vue.component("wishes", require("./components/wish/index.vue"));
Vue.component("new-wish-form", require("./components/wish/_new_wish_form.vue"));

Vue.component("wish-tickets", require("./components/wish_tickets/index.vue"));

Vue.component("statistic", require("./components/statistic/index.vue"));

const app = new Vue({
    el: "#app",
    store
});
