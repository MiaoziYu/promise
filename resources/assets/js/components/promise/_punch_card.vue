<template>
    <div class="checkbox-wrapper">
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
        <i v-for="n in promise.punch_card_total"
           v-if="promise.finished_at"
           class="checkbox-static fa fa-check-square-o" aria-hidden="true"></i>
    </div>
</template>

<script>
    import api from '../../store/api'

    export default {
        props: ["promise"],

        methods: {
            updatePunchCard(promise) {
                let punchCardFinished = $("input[type=checkbox]:checked", $(event.target).parent()).length;
                let data = {
                    "punch_card_finished": $("input[type=checkbox]:checked", $(event.target).parent()).length
                };
                if (punchCardFinished === promise.punch_card_total) {
                    data["finished"] = "true";
                }
                api.updatePromise(promise.id, data);
                this.$emit("updatePunchCard");
            },
        }
    }
</script>