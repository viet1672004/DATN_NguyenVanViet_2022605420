import { defineStore } from "pinia";
import api from "./api";

export const useProfileStore = defineStore("profiles", {
  state: () => ({
    user: null,
    loading: false,
    error: null,
  }),

  actions: {

    // lấy thông tin user
    async fetchProfile() {
      try {
        this.loading = true;
        const res = await api.me();
        this.user = res.data;
      } catch (err) {
        this.error = err.response?.data?.message;
      } finally {
        this.loading = false;
      }
    },

    // update profile
    async updateProfile(data) {
      try {
        this.loading = true;
        const res = await api.update(data);
        return res;
      } catch (err) {
        throw err;
      } finally {
        this.loading = false;
      }
    },

    // đổi mật khẩu
    async changePassword(data) {
      try {
        this.loading = true;
        const res = await api.changePassword(data);
        return res;
      } catch (err) {
        throw err;
      } finally {
        this.loading = false;
      }
    }

  }
});