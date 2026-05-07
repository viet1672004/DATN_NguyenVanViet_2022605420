import { defineStore } from "pinia";
import api from "./api";

export const useTicketStore = defineStore("tickets", {
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

        this.items = res.data.data || [];
      } catch (err) {
        console.error("GET LIST ERROR:", err);
      } finally {
        this.loading = false;
      }
    },

    async getDetail(id) {
  try {
    this.loading = true;

    const res = await api.getDetail(id);

    console.log("FULL RESPONSE:", res);       // 👈 thêm dòng này
    console.log("DATA RAW:", res.data);       // 👈 thêm dòng này

    this.detail = res.data?.data || res.data || null;

  } catch (err) {
    console.error("GET DETAIL ERROR:", err);
  } finally {
    this.loading = false;
  }
},

    async create(data) {
      try {
        await api.create(data);
        return true;
      } catch (err) {
        throw err.response?.data?.message || "Thêm vé thất bại";
      }
    },

    async update(id, data) {
      try {
        await api.update(id, data);
        return true;
      } catch (err) {
        throw err.response?.data?.message || "Cập nhật thất bại";
      }
    },

    async delete(id) {
      try {
        await api.delete(id);
      } catch (err) {
        console.error("DELETE ERROR:", err);
      }
    },
  },
});