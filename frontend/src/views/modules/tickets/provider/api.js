import axios from "@/api/axios";

export default {
  getList(params = {}) {
    return axios.get("/tickets", { params });
  },

  getDetail(id) {
    return axios.get(`/tickets/${id}`);
  },

  create(data) {
    return axios.post("/tickets", data);
  },

  update(id, data) {
    return axios.put(`/tickets/${id}`, data);
  },

  delete(id) {
    return axios.delete(`/tickets/${id}`);
  },

  getDropdown() {
    return axios.get("/tickets-dropdown");
  },
};