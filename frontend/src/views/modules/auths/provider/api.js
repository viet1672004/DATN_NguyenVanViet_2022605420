import axios from "@/api/axios";

export default {
  login(data) {
    return axios.post("/login", data);
  },

  register(data) {
    return axios.post("/register", data);
  },

  logout() {
    return axios.post("/logout");
  },

  me() {
    return axios.get("/me");
  }
};