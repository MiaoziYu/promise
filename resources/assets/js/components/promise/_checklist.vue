<template>
    <ul v-if="promise.checklists.length > 0">
        <li v-for="checklist in promise.checklists" class="checkbox-wrapper checklist">
            <input @click="updateChecklist(promise.id, checklist.id)"
                   v-model="checklist.status"
                   class="checkbox"
                   type="checkbox">
            <label for="">{{ checklist.text }}</label>
        </li>
    </ul>
</template>

<script>
    import api from '../../store/api'

    export default {
        props: ["promise"],

        methods: {
            updateChecklist(promiseId, checklistId) {
                let data = {
                    status: $(event.target).is(':checked')
                };
                api.updateChecklist(promiseId, checklistId, data).then(response => {
                    this.$emit("updateChecklist");
                });
            }
        }
    }
</script>