<template>
  <div class="box">
    <h2>Top Tickets</h2>

    <div
      class="item"
      v-for="item in dashboard.top_tickets || []"
      :key="item.ID"
    >
      <span>{{ item.TicketName }}</span>

      <strong>{{ item.total_sold }}</strong>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useDashboardStore } from "../provider/store";

const store = useDashboardStore();

const dashboard = computed(() => store.dashboard);
</script>

<style scoped>
.box {
  background: #fff;
  padding: 24px;
  border-radius: 18px;
  border: 1px solid #ddd;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.box h2 {
  margin-bottom: 20px;
  color: #000;
}

.item {
  display: flex;
  justify-content: space-between;
  padding: 14px 0;
  border-bottom: 1px solid #eee;
  color: #000;
}
</style>