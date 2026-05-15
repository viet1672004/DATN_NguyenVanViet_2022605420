import axios from "@/api/axios";

export default {
  getList(params = {}) {
    return axios.get("/admin/users", { params });
  },

  getDetail(id) {
    return axios.get(`/admin/users/${id}`);
  },

  toggleBlock(id) {
    return axios.put(`/admin/users/${id}/toggle-block`);
  },

  delete(id) {
    return axios.delete(`/admin/users/${id}`);
  },
};