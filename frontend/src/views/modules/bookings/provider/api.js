import axios from "@/api/axios";

export default {
  getList(params = {}) {
    return axios.get("/bookings", { params });
  },

  getDetail(id) {
    return axios.get(`/bookings/${id}`);
  },

  create(data) {
    return axios.post("/bookings", data);
  },

  update(id, data) {
    return axios.put(`/bookings/${id}`, data);
  },

  delete(id) {
    return axios.delete(`/bookings/${id}`);
  },

  getTickets() {
    return axios.get('/tickets-dropdown')
  },

    cancel(id) {
    return axios.post(`/bookings/${id}/cancel`)
  },

  getParks() {
    return axios.get('/parks-dropdown')
  }
};