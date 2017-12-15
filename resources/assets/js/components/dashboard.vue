<template>
    <div class="dashboard">
        <div class="promise-form">
            <input v-model="promiseFormData.title">
            <textarea v-model="promiseFormData.description"></textarea>
            <input v-model="promiseFormData.check_list_quantity" type="number">
            <button @click="createPromise()">add now promise</button>
        </div>
        <ul class="promise-ongoing o-list-4">
            <li v-for="promise in promises" v-if="promise.finished_at == null" class="card promise-item o-list-item">
                <input v-model="promise.title"
                       @blur="updateText(promise.id)"
                       class="title"
                       name="title">
                <textarea v-model="promise.description"
                          @blur="updateText(promise.id)"
                          class="description"
                          name="description"></textarea>
                <!--<p class="date">{{ promise.created_at }}</p>-->
                <div class="checkbox-wrapper">
                    <input v-for="n in promise.check_list_finished"
                           @click="updateCheckbox(promise)"
                           class="checkbox"
                           type="checkbox" checked>
                    <input v-for="n in (promise.check_list_quantity - promise.check_list_finished)"
                           @click="updateCheckbox(promise)"
                           class="checkbox"
                           type="checkbox">
                </div>
                <button @click="deletePromise(promise.id)" class="delete-btn">delete promise</button>
            </li>
        </ul>

        <ul class="promise-finished o-list-4">
            <li v-for="promise in promises" v-if="promise.finished_at"  class="card promise-item o-list-item">
                <p>{{ promise.title }}</p>
                <p>{{ promise.description }}</p>
                <p>finished at {{ promise.finished_at }}</p>
                <button @click="deletePromise(promise.id)">delete promise</button>
            </li>
        </ul>
    </div>
</template>

<script>
    import api from '../store/api'

    export default {
        data() {
            return {
                promises: [],
                promiseFormData: {
                    title: "",
                    description: "",
                    check_list_quantity: 0
                }
            }
        },

        computed: {
        },

        beforeMount() {
            this.getPromises();
        },

        methods: {
            getPromises() {
                api.getPromises().then(data => {
                    this.promises = data;
                })
            },

            updateText(id) {
                let data = {};
                data[$(event.target).attr("name")] = $(event.target).val();
                api.updatePromise(id, data).then(response => {
                    this.getPromises();
                });
            },

            updateCheckbox(promise) {
                let data = {
                    "check_list_finished": $("input[type=checkbox]:checked", $(event.target).parent()).length
                };
                api.updatePromise(promise.id, data);
                if (data.check_list_finished === promise.check_list_quantity) {
                    this.getPromises();
                }
            },

            createPromise() {
                console.log(this.promiseFormData)
                api.createPromise(this.promiseFormData).then(response => {
                    this.getPromises();
                    this.promiseFormData = {
                        title: "",
                        description: "",
                        check_list_quantity: 0
                    };
                })
            },

            deletePromise(id) {
                api.deletePromise(id).then(response => {
                    this.getPromises();
                })
            }
        }
    }
</script>
