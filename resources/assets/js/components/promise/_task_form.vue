<template>
    <div class="task-form">
        <!--buttons to create task-->
        <div v-if="!hasTask && taskCreateBtn" class="form-group form-label">
            <div @click="showPunchCardCreateForm"
                 class="label-item">
                <i class="fa fa-tags" aria-hidden="true"></i><span> Punch card</span>
            </div>
            <div @click="showChecklistCreateForm"
                 class="label-item">
                <i class="fa fa-list-ol" aria-hidden="true"></i><span> Checklist</span>
            </div>
        </div>

        <!--button to edit task-->
        <div v-if="hasTask">
            <div @click="showEditForm"
                 v-if="promise.punch_card_total !== null && !punchCardEditForm"
                 class="edit-btn btn-secondary">
                edit punch card
            </div>
            <div @click="showEditForm"
                 v-if="this.promise.checklists.length !== 0 && !checklistEditForm"
                 class="checklist-edit-btn edit-btn btn-secondary">
                add checklist element
            </div>
        </div>
        
        <!--form to create tasks-->
        <div v-if="punchCardCreateForm " class="form-group">
            <input v-model="punchCardValue"
                   class="u-margin-b-1"
                   placeholder="How many tasks do you want to have?">
            <div class="form-label">
                <span @click="updatePunchCard(promise.id)" class="label-item active">add</span>
                <span @click="showTaskBtn" class="label-item">cancel</span>
            </div>
        </div>
        <div v-if="checklistCreateForm" class="form-group">
            <input v-model="checklistValue"
                   class="u-margin-b-1"
                   placeholder="add checklist element">
            <div class="form-label">
                <span @click="addChecklist(promise.id)" class="label-item active">add</span>
                <span @click="showTaskBtn" class="label-item">cancel</span>
            </div>
        </div>
        
        <!--form to edit tasks-->
        <div v-if="punchCardEditForm" class="form-group">
            <input v-model="punchCardValue"
                   class="u-margin-b-1"
                   placeholder="new punch card number">
            <div class="form-label">
                <span @click="updatePunchCard(promise.id)" class="label-item active">update</span>
                <span @click="hidePunchCardEditForm" class="label-item">cancel</span>
            </div>
        </div>
        <div v-if="checklistEditForm" class="form-group">
            <input v-model="checklistValue"
                   class="u-margin-b-1"
                   placeholder="add checklist element">
            <div class="form-label">
                <span @click="addChecklist(promise.id)" class="label-item active">add</span>
                <span @click="hideChecklistEditForm" class="label-item">cancel</span>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../../store/api'

    export default {
        props: ["promise"],

        data() {
            return {
                hasTask: true,
                taskCreateBtn: true,
                punchCardCreateForm: false,
                punchCardEditForm: false,
                punchCardValue: "",
                checklistCreateForm: false,
                checklistEditForm: false,
                checklistValue: "",
            }
        },

        beforeMount() {
            this.setTaskBtnDefaultStatus();
        },

        methods: {
            setTaskBtnDefaultStatus() {
                this.hasTask = this.promise.punch_card_total !== null || this.promise.checklists.length !== 0;
            },

            showTaskBtn() {
                this.taskCreateBtn = true;
                this.punchCardCreateForm = false;
                this.checklistCreateForm = false;
            },

            hideTaskBtn() {
                this.taskCreateBtn = false;
            },

            showEditForm() {
                if (this.promise.punch_card_total !== null) {
                    this.punchCardEditForm = true;
                } else if (this.promise.checklists.length !== 0) {
                    this.checklistEditForm = true;
                }
            },

            showPunchCardCreateForm() {
                this.punchCardCreateForm = true;
                this.hideTaskBtn();
            },

            showChecklistCreateForm() {
                this.checklistCreateForm = true;
                this.hideTaskBtn();
            },

            hidePunchCardEditForm() {
                this.punchCardEditForm = false;
            },

            hideChecklistEditForm() {
                this.checklistEditForm = false;
            },

            updatePunchCard(id) {
                let data = {
                    punch_card_total: this.punchCardValue
                };
                api.updatePromise(id, data).then(response => {
                    this.punchCardCreateForm = false;
                    this.punchCardEditForm = false;
                    this.punchCardValue = "";
                    this.hasTask = true;
                    EventBus.$emit("addTask");
                });
            },

            addChecklist(id) {
                let data = {
                    text: this.checklistValue
                };
                api.createChecklist(id, data).then(response => {
                    this.checklistCreateForm = false;
                    this.checklistEditForm = true;
                    this.checklistValue = "";
                    this.hasTask = true;
                    EventBus.$emit("addTask");
                });
            },
        }
    }
</script>