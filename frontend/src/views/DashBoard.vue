<template>
  <div class="dashboard">
    <!-- HEADER -->
    <div class="dashboard-header">
      <div>
        <h1>Dashboard</h1>
        <p>Hệ thống thống kê FunTicket</p>
      </div>
    </div>

    <!-- SUMMARY -->
    <DashboardSummary />

    <!-- CHART + TOP TICKETS -->
    <div class="dashboard-grid">
      <DashboardRevenue
        :revenueChart="dashboardStore.dashboard.revenue_chart"
      />

      <DashboardTopTickets />
    </div>

    <!-- TOP PARKS + PAYMENTS -->
    <div class="dashboard-grid">
      <DashboardTopParks />
      <DashboardLatestPayments />
    </div>

    <!-- BOOKINGS -->
    <div class="dashboard-full">
      <DashboardLatestBookings />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";

import { useDashboardStore } from "@/views/modules/dashboard/provider/store";

import DashboardSummary from "@/views/modules/dashboard/views/DashboardSummary.vue";
import DashboardRevenue from "@/views/modules/dashboard/views/DashboardRevenue.vue";
import DashboardTopTickets from "@/views/modules/dashboard/views/DashboardTopTickets.vue";
import DashboardTopParks from "@/views/modules/dashboard/views/DashboardTopParks.vue";
import DashboardLatestPayments from "@/views/modules/dashboard/views/DashboardLatestPayments.vue";
import DashboardLatestBookings from "@/views/modules/dashboard/views/DashboardLatestBookings.vue";

const dashboardStore = useDashboardStore();

onMounted(async () => {
  await dashboardStore.getDashboard();
});
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  padding: 24px;
  background: #f1f5f9;
}

/* HEADER */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;

  margin-bottom: 28px;
}

.dashboard-header h1 {
  font-size: 36px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.dashboard-header p {
  margin-top: 6px;
  font-size: 15px;
  color: #6b7280;
}

/* GRID */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;

  margin-top: 24px;
}

.dashboard-full {
  margin-top: 24px;
}

/* CARD COMMON */
:deep(.dashboard-card) {
  background: #ffffff;
  border-radius: 18px;
  padding: 20px;

  box-shadow:
    0 2px 6px rgba(15, 23, 42, 0.05),
    0 10px 25px rgba(15, 23, 42, 0.06);

  border: 1px solid #e5e7eb;

  transition: all 0.25s ease;
}

:deep(.dashboard-card:hover) {
  transform: translateY(-2px);

  box-shadow:
    0 8px 20px rgba(15, 23, 42, 0.08),
    0 15px 35px rgba(15, 23, 42, 0.08);
}

:deep(.dashboard-card-title) {
  font-size: 20px;
  font-weight: 700;
  color: #111827;

  margin-bottom: 18px;
}

/* TABLE */
:deep(table) {
  width: 100%;
  border-collapse: collapse;
}

:deep(table th) {
  background: #f8fafc;
  color: #374151;

  font-size: 14px;
  font-weight: 600;

  padding: 14px;
  text-align: left;

  border-bottom: 1px solid #e5e7eb;
}

:deep(table td) {
  padding: 14px;
  color: #111827;
  border-bottom: 1px solid #f1f5f9;
}

:deep(table tr:hover) {
  background: #f8fafc;
}

/* SCROLL */
:deep(.table-wrapper) {
  overflow-x: auto;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .dashboard {
    padding: 16px;
  }

  .dashboard-header h1 {
    font-size: 28px;
  }

  .dashboard-grid {
    gap: 16px;
  }
}
</style>