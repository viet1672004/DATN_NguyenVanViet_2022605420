import { defineStore } from "pinia";
import api from "./api";

export const useChatBotStore = defineStore("chatbot", {
  state: () => ({
    messages: [],
    loading: false,
  }),

  actions: {
    async sendMessage(text) {
      try {
        this.loading = true;

        // USER MESSAGE
        this.messages.push({
          type: "user",
          text,
        });

        const res = await api.sendMessage({
          message: text,
        });

        // AI MESSAGE
        this.messages.push({
          type: "bot",
          text: res.data.reply,
        });

      } catch (err) {

        this.messages.push({
          type: "bot",
          text: "AI hiện đang bận.",
        });

        console.error(err);

      } finally {
        this.loading = false;
      }
    },
  },
});