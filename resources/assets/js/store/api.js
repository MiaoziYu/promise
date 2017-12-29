const axios = require('axios')

function get(path, param) {
    return axios.get(`/api/${path}?api_token=${API_TOKEN}&${param}`)
        .then(response => {
            return response.data;
        })
        .catch(err => {
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

    updatePromise(id, data) {
        return put(`promises/${id}`, data)
    },

    updateChecklist(promiseId, checklistId, data) {
        return put(`promises/${promiseId}/checklists/${checklistId}`, data);
    },

    createPromise(data) {
        return post(`promises/`, data)
    },

    createChecklist(promiseId, data) {
        return post(`promises/${promiseId}/checklists`, data);
    },

    deletePromise(id) {
        return remove(`promises/${id}`);
    },
}
