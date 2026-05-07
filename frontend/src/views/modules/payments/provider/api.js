import axios from "@/api/axios";

export default {
  pay(data) {
    return axios.post("/payments/pay", data);
  },

  getList(params = {}) {
    return axios.get("/payments", { params });
  },

  getDetail(id) {
    return axios.get(`/payments/${id}`);
  },

  createVnpay(data) {
    return axios.post("/payments/vnpay/create", data);
  }
};