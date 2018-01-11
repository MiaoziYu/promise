const axios = require('axios')

function get(path, param) {
    return axios.get(`/api/${path}?api_token=${API_TOKEN}&${param}`)
        .then(response => {
            return response.data;
        })
        .catch(err => {
            console.log(err);
            return err
        });
}

function post(path, data, param) {
    return axios.post(`/api/${path}?api_token=${API_TOKEN}&${param}`, data)
        .then(response => {
            return response;
        })
        .catch(error => {
            console.log(error);
        });
}

function put(path, data, param) {
    return axios.put(`/api/${path}?api_token=${API_TOKEN}&${param}`, data)
        .then(response => {
            return response;
        })
        .catch(error => {
            console.log(error);
        });
}

function remove(path, param) {
    return axios.delete(`/api/${path}?api_token=${API_TOKEN}&${param}`)
        .then(response => {
            return response;
        })
        .catch(error => {
            console.log(error);
        });
}

export default {
    getPromises(param) {
        return get(`promises/`, `finished=${param}`);
    },

    getPromise(id) {
        return get(`promises/${id}`);
    },

    getHabits() {
        return get(`habits/`);
    },

    getHabit(id) {
        return get(`habits/${id}`);
    },

    getChallenges() {
        return get(`weekly-challenges/`);
    },

    getChallenge(id) {
        return get(`weekly-challenges/${id}`);
    },

    getWishes() {
        return get(`wishes/`);
    },

    getWish(id) {
        return get(`wishes/${id}`);
    },

    getUserInfo() {
        return get(`profile`);
    },

    getWishTickets(claimed) {
        return get(`wish-tickets`, `claimed=${claimed}`);
    },

    updateHabit(id, data) {
        return put(`habits/${id}`, data);
    },

    updateChallenge(id, data) {
        return put(`weekly-challenges/${id}`, data)
    },

    updatePromise(id, data) {
        return put(`promises/${id}`, data);
    },

    finishPromise(id) {
        return put(`promises/${id}/finish`, []);
    },

    updateChecklist(promiseId, checklistId, data) {
        return put(`promises/${promiseId}/checklists/${checklistId}`, data);
    },

    updateWish(id, data) {
        return put(`wishes/${id}`, data)
    },

    purchaseWish(id) {
        return put(`wishes/${id}/purchase`, [])
    },

    checkHabit(id) {
        return put(`habits/${id}/check`, [])
    },

    checkChallenge(id) {
        return put(`weekly-challenges/${id}/check`, [])
    },

    claimWishTicket(id) {
        return put(`wish-tickets/${id}/claim`, [])
    },

    createPromise(data) {
        return post(`promises/`, data);
    },

    createHabit(data) {
        return post(`habits/`, data);
    },

    createChallenge(data) {
        return post(`weekly-challenges/`, data);
    },

    createChecklist(promiseId, data) {
        return post(`promises/${promiseId}/checklists`, data);
    },

    createWish(data) {
        return post(`wishes/`, data);
    },

    deletePromise(id) {
        return remove(`promises/${id}`);
    },

    deleteChecklist(promiseId, checklistId) {
        return remove(`promises/${promiseId}/checklists/${checklistId}`);
    },

    deleteHabit(id) {
        return remove(`habits/${id}`);
    },

    deleteChallenge(id) {
        return remove(`weekly-challenges/${id}`);
    },

    deleteWish(id) {
        return remove(`wishes/${id}`);
    },

    deleteWishTicket(id) {
        return remove(`wish-tickets/${id}`);
    },
}
