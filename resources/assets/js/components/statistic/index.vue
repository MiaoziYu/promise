<template>
    <div class="statistic">

    </div>
</template>

<script>
    import api from "../../store/api.js";

    export default {
        data() {
            return {
                statistic: null
            }
        },

        beforeMount() {
            this.getStatistic();
        },

        methods: {
            getStatistic() {
                api.getStatistic().then(data => {
                    this.statistic = data;
                });
            },
        }
    }
</script>
