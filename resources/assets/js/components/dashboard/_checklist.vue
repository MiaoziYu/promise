<template>
    <ul v-if="promise.checklists.length > 0">
        <li v-for="checklist in promise.checklists" class="checkbox-wrapper checklist checklist-flex">
            <div class="">
                <input @click="updateChecklist(promise.id, checklist.id)"
                       v-model="checklist.status"
                       class="checkbox"
                       type="checkbox">
                <label for="">{{ checklist.text }}</label>
            </div>
            <i @click="deleteChecklist(promise.id, checklist.id)" class="delete-btn fa fa-times" aria-hidden="true"></i>
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
                    if (this.checklistFinished()) {
                        EventBus.$emit("finishChecklist");
                    }
                    EventBus.$emit("updateChecklist");
                });
            },

            deleteChecklist(promiseId, checklistId) {
                api.deleteChecklist(promiseId, checklistId).then(response => {
                    EventBus.$emit("deleteChecklist");
                });
            },

            checklistFinished() {
                let checkListFinished = this.promise.checklists.filter((element, index) => {
                    return element.status === 1 || element.status === true;
                }).length;
                return this.promise.checklists.length === checkListFinished;
            }
        }
    }
</script>