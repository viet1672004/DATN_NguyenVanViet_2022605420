import { defineStore } from "pinia";

import api from "./api";

export const useChatBotStore = defineStore(
  "chatbot",
  {
    state: () => ({
      messages: [],
      loading: false,
    }),

    actions: {

      async sendMessage(text) {

        /*
        |--------------------------------------------------------------------------
        | BLOCK SPAM
        |--------------------------------------------------------------------------
        */

        if (this.loading) {
          return;
        }

        this.loading = true;

        /*
        |--------------------------------------------------------------------------
        | USER MESSAGE
        |--------------------------------------------------------------------------
        */

        this.messages.push({

          type: "user",

          text,
        });

        try {

          const res = await api.sendMessage({

            message: text,
          });

          /*
          |--------------------------------------------------------------------------
          | BOT MESSAGE
          |--------------------------------------------------------------------------
          */

          this.messages.push({

            type: "bot",

            text:
              res.data.reply ||
              "Không có phản hồi.",
          });

        } catch (err) {

          console.error(err);

          this.messages.push({

            type: "bot",

            text:
              "Hệ thống AI đang bận.",
          });

        } finally {

          this.loading = false;
        }
      },

      /*
      |--------------------------------------------------------------------------
      | RESET CHAT
      |--------------------------------------------------------------------------
      */

      resetChat(defaultMessage) {

        this.messages = [
          defaultMessage,
        ];
      }
    }
  }
);