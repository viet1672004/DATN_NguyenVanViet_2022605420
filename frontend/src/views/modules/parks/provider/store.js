import { defineStore } from "pinia";
import api from "./api";

export const useParkStore = defineStore("parks", {
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

        console.log("API:", res.data);

        this.items = res.data.items || [];

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

    async create(data) {
    try {
        await api.create(data); // 🔥 gửi JSON thẳng

        return true;
    } catch (err) {
        throw err.response?.data?.message || "Thêm thất bại";
    }
    },

    async update(id, data) {
      try {
        await api.update(id, data); // 👈 dùng api.js

        return true;
      } catch (err) {
        console.error("UPDATE ERROR:", err.response);
        throw err.response?.data?.message || "Cập nhật thất bại";
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