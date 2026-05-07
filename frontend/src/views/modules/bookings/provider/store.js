import { defineStore } from 'pinia'
import api from './api'

export const useBookingStore = defineStore('booking', {
  state: () => ({
    list: [],
    loading: false,
    pagination: {}
  }),

  actions: {
    async fetchList(params = {}) {
      this.loading = true
      try {
        const res = await api.getList(params)
        this.list = res.data.data
        this.pagination = res.data
      } finally {
        this.loading = false
      }
    },

    async createBooking(data) {
      return await api.create(data)
    },

    async updateBooking(id, data) {
      return await api.update(id, data)
    },

    async deleteBooking(id) {
      return await api.delete(id)
    }
  }
})