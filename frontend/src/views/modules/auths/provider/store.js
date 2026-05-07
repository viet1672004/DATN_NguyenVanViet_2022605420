import { defineStore } from "pinia";
import api from "./api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null, // 🔥 FIX
    token: localStorage.getItem("token") || null,
  }),

  actions: {
    async login(data) {
      try {
        const res = await api.login(data);

        this.user = res.data.user;
        this.token = res.data.token;

        // 🔥 lưu cả user + token
        localStorage.setItem("token", this.token);
        localStorage.setItem("user", JSON.stringify(this.user));

        return true;
      } catch (err) {
        throw err.response?.data?.message || "Đăng nhập thất bại";
      }
    },

    async register(data) {
      try {
        const res = await api.register(data);

        this.user = res.data.user;
        this.token = res.data.token;

        localStorage.setItem("token", this.token);
        localStorage.setItem("user", JSON.stringify(this.user));

        return true;
      } catch (err) {
        throw err.response?.data?.message || "Đăng ký thất bại";
      }
    },

    // 🔥 thêm hàm này
    setUser(user) {
      this.user = user;
      localStorage.setItem("user", JSON.stringify(user));
    },

    async logout() {
      try {
        await api.logout();
      } catch (e) {}

      this.token = null;
      this.user = null;

      localStorage.removeItem("token");
      localStorage.removeItem("user"); // 🔥 FIX
    },
  },
});