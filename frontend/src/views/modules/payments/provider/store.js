import { defineStore } from "pinia";
import api from "./api";

export const usePaymentStore = defineStore("payment", {
  state: () => ({
    list: [],
    loading: false,
    pagination: {}
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true;
      try {
        const res = await api.getList(params);
        this.list = res.data.data;
        this.pagination = res.data;
      } finally {
        this.loading = false;
      }
    }
  }
});