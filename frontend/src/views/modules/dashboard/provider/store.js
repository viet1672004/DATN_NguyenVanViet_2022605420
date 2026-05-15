import { defineStore } from "pinia";

import dashboardApi from "./api";

export const useDashboardStore = defineStore(
  "dashboard",

  {
    state: () => ({

      dashboard: {},

      loading: false,

    }),

    actions: {

      /*
      |--------------------------------------------------------------------------
      | Get Dashboard
      |--------------------------------------------------------------------------
      */

      async getDashboard(params = {}) {

        try {

          this.loading = true;
          this.dashboard = {};

          const res =
            await dashboardApi.getDashboard(
              params
            );

          this.dashboard = res.data;

        } catch (error) {

          console.log(error);

        } finally {

          this.loading = false;

        }
      }

    }
  }
);