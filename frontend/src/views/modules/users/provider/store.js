import { defineStore } from "pinia";
import api from "./api";

export const useUserStore = defineStore("users", {
  state: () => ({
    items: [],
    detail: null,
    loading: false,
  }),

  actions: {
    async getList(params = {}) {
      try {
        this.loading = true;
        const res = await api.getList(params);
        this.items = res.data.items || res.data || [];
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async getDetail(id) {
      try {
        this.loading = true;
        const res = await api.getDetail(id);
        this.detail = res.data;
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async toggleBlock(id) {
      try {
        await api.toggleBlock(id);
        return true;
      } catch (err) {
        throw err.response?.data?.message || "Thao tác thất bại";
      }
    },

    async delete(id) {
      try {
        await api.delete(id);
      } catch (err) {
        console.error(err);
      }
    },
  },
});
