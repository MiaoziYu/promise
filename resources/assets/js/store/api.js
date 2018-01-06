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
        return get(`promises`, `finished=${param}`);
    },

    getPromise(id) {
        return get(`promises/${id}`);
    },

    getWishes() {
        return get(`wishes/`);
    },

    getUserInfo() {
        return get(`profile`);
    },

    getWishTickets() {
        return get(`wish-tickets`);
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

    purchaseWish(id) {
        return put(`wishes/${id}/purchase`, [])
    },

    claimWishTicket(id) {
        return put(`wish-tickets/${id}/claim`, [])
    },

    createPromise(data) {
        return post(`promises/`, data);
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

    deleteWish(id) {
        return remove(`wishes/${id}`);
    },

    deleteWishTicket(id) {
        return remove(`wish-tickets/${id}`);
    },
}
