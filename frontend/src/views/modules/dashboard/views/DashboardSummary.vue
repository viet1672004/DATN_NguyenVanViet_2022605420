<template>
  <div class="summary-grid">

    <!-- USERS -->
    <div class="card users">

      <div class="card-top">

        <div>
          <p>Người dùng</p>

          <h2>
            {{ summary.total_users || 0 }}
          </h2>
        </div>

        <div class="icon">
          👤
        </div>

      </div>

    </div>

    <!-- BOOKINGS -->
    <div class="card bookings">

      <div class="card-top">

        <div>
          <p>Tổng số Bookings</p>

          <h2>
            {{ summary.total_bookings || 0 }}
          </h2>
        </div>

        <div class="icon">
          🧾
        </div>

      </div>

    </div>

    <!-- REVENUE -->
    <div class="card revenue">

      <div class="card-top">

        <div>
          <p>Tổng Doanh thu</p>

          <h2>
            {{ formatPrice(summary.total_revenue) }}
          </h2>
        </div>

        <div class="icon">
          💰
        </div>

      </div>

    </div>

    <!-- TICKETS -->
    <div class="card tickets">

      <div class="card-top">

        <div>
          <p>Số lượng Vé</p>

          <h2>
            {{ summary.total_tickets || 0 }}
          </h2>
        </div>

        <div class="icon">
          🎫
        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { computed } from "vue";

import { useDashboardStore } from "../provider/store";

const store = useDashboardStore();

/*
|--------------------------------------------------------------------------
| Dashboard Data
|--------------------------------------------------------------------------
*/

const dashboard = computed(
  () => store.dashboard
);

const summary = computed(
  () => dashboard.value.summary || {}
);

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const formatPrice = (value) => {

  return Number(value || 0)
    .toLocaleString("vi-VN") + " đ";
};
</script>

<style scoped>
.summary-grid {
  display: grid;

  grid-template-columns: repeat(4, 1fr);

  gap: 24px;
}

/* CARD */

.card {
  position: relative;

  overflow: hidden;

  background: #ffffff;

  padding: 24px;

  border-radius: 20px;

  border: 1px solid #e5e7eb;

  transition: 0.25s;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.04),
    0 8px 24px rgba(0,0,0,0.06);
}

.card:hover {
  transform: translateY(-4px);
}

/* TOP */

.card-top {
  display: flex;

  justify-content: space-between;

  align-items: center;
}

.card p {
  margin: 0;

  font-size: 14px;

  font-weight: 600;

  color: #6b7280;
}

.card h2 {
  margin-top: 10px;

  font-size: 32px;

  font-weight: 700;

  color: #111827;

  line-height: 1.3;

  white-space: nowrap;

  overflow: hidden;

  text-overflow: ellipsis;
}

.revenue h2 {
  font-size: 28px;

  white-space: nowrap;
}

/* ICON */

.icon {
  width: 58px;

  height: 58px;

  border-radius: 16px;

  display: flex;

  align-items: center;

  justify-content: center;

  font-size: 28px;
}

/* COLORS */

.users .icon {
  background: #dbeafe;
}

.bookings .icon {
  background: #ede9fe;
}

.revenue .icon {
  background: #dcfce7;
}

.tickets .icon {
  background: #fef3c7;
}

/* RESPONSIVE */

@media (max-width: 1200px) {

  .summary-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {

  .summary-grid {
    grid-template-columns: 1fr;
  }

  .card h2 {
    font-size: 26px;
  }
}
</style>