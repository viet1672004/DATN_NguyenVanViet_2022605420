<template>
  <div class="summary-grid">
    <div class="card">
      <h3>Users</h3>
      <h1>{{ dashboard.summary?.total_users || 0 }}</h1>
    </div>

    <div class="card">
      <h3>Bookings</h3>
      <h1>{{ dashboard.summary?.total_bookings || 0 }}</h1>
    </div>

    <div class="card">
      <h3>Revenue</h3>
      <h1>{{ formatPrice(dashboard.summary?.total_revenue) }}</h1>
    </div>

    <div class="card">
      <h3>Tickets</h3>
      <h1>{{ dashboard.summary?.total_tickets || 0 }}</h1>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useDashboardStore } from "../provider/store";

const store = useDashboardStore();

const dashboard = computed(() => store.dashboard);

const formatPrice = (value) => {
  return Number(value || 0).toLocaleString("vi-VN") + " đ";
};
</script>

<style scoped>
.summary-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.card {
  background: #fff;
  padding: 24px;
  border-radius: 18px;
  border: 1px solid #ddd;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.card h3 {
  font-size: 16px;
  color: #000;
  margin-bottom: 12px;
}

.card h1 {
  font-size: 34px;
  font-weight: 700;
  color: #000;
}

@media (max-width: 992px) {
  .summary-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>