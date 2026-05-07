import axios from "@/api/axios";

export default {
    getList(params = {}) {
        return axios.get("/parks", { params });
    },

    getDetail(id) {
        return axios.get(`/parks/${id}`);
    },

    create(data) {
        return axios.post("/parks", data, {
            headers: {
            "Content-Type": "multipart/form-data"
            }
        });
    },

    update(id, data) {
        return axios.post(`/parks/${id}?_method=PUT`, data, {
            headers: {
            "Content-Type": "multipart/form-data"
            }
        });
    },

    delete(id) {
        return axios.delete(`/parks/${id}`);
    },
};