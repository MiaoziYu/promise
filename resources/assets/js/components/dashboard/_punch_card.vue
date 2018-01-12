<template>
    <div class="punch-card checkbox-wrapper">
        <input v-for="n in promise.punch_card_finished"
               v-if="promise.finished_at == null"
               @click="updatePunchCard(promise)"
               class="checkbox"
               :class="{ last: n == promise.punch_card_finished }"
               type="checkbox"
               checked>
        <input v-for="n in (promise.punch_card_total - promise.punch_card_finished)"
               v-if="promise.finished_at == null"
               @click="updatePunchCard(promise)"
               class="checkbox"
               type="checkbox">
    </div>
</template>

<script>
    import api from '../../store/api'

    export default {
        props: ["promise"],

        methods: {
            updatePunchCard(promise) {
                let data = {
                    punch_card_finished: $("input[type=checkbox]:checked", $(event.target).parent()).length
                };
                api.updatePromise(promise.id, data).then(response => {
                    if (this.punchCardFinished(data)) {
                        EventBus.$emit("finishPunchCard");
                    }
                    EventBus.$emit("updatePunchCard");
                });
            },

            punchCardFinished(data) {
                return data.punch_card_finished === this.promise.punch_card_total;
            }
        }
    }
</script>
