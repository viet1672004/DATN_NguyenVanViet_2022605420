import { defineStore } from "pinia";
import dashboardApi from "./api";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        dashboard: {}
    }),

    actions: {
        async getDashboard() {
            try {
                const res = await dashboardApi.getDashboard();
                this.dashboard = res.data;
            } catch (error) {
                console.log(error);
            }
        }
    }
});