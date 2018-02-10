<template>
    <div class="statistic-page page-wrapper">
        <div v-if="statistic" class="statistic o-list-4">
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Credits earned</h2>
                <p>{{ statistic.credits_earned }}</p>
            </div>
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Credits contributed</h2>
                <p>{{ statistic.credits_contributed }}</p>
            </div>
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Habits checked</h2>
                <p>{{ statistic.habits_checked }}</p>
            </div>
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Habits max streak</h2>
                <p>{{ statistic.max_streak }}</p>
            </div>
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Promises finished</h2>
                <p>{{ statistic.promises_finished }}</p>
            </div>
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Weekly challenge checked</h2>
                <p>{{ statistic.weekly_challenges_checked }}</p>
            </div>
            <div class="o-card statistic-item o-list-item">
                <h2 class="o-card-title">Wishes purchased</h2>
                <p>{{ statistic.wishes_purchased }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    import api from "../../store/api.js";

    export default {
        data() {
            return {
                statistic: null,
            }
        },

        beforeMount() {
            this.getStatistic();
            EventBus.$emit("setPageName", "statistic");
        },

        methods: {
            getStatistic() {
                api.getStatistic().then(data => {
                    this.statistic = data;
                    console.log(data)
                });
            },
        }
    }
</script>
