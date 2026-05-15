<template>
  <div class="table-box">

    <!-- HEADER -->
    <div class="table-header">

      <div>
        <h2>Booking mới nhất</h2>

        <p>
          Danh sách booking gần đây trong hệ thống
        </p>
      </div>

      <div class="total-badge">
        {{ bookings.length }} bookings
      </div>

    </div>

    <!-- EMPTY -->
    <div
      v-if="!bookings.length"
      class="empty-box"
    >
      Không có dữ liệu booking
    </div>

    <!-- TABLE -->
    <div
      v-else
      class="table-wrapper"
    >

      <table>

        <thead>
          <tr>
            <th>Mã booking</th>
            <th>Khách hàng</th>
            <th>Khu vui chơi</th>
            <th>Tổng tiền</th>
            <th>Ngày đặt</th>
          </tr>
        </thead>

        <tbody>

          <tr
            v-for="item in bookings"
            :key="item.ID"
          >

            <!-- BOOKING CODE -->
            <td>
              <span class="booking-code">
                {{ item.BookingCode }}
              </span>
            </td>

            <!-- USER -->
            <td>
              {{ item.user?.Name || "N/A" }}
            </td>

            <!-- PARK -->
            <td>
              {{ item.park?.ParkName || "N/A" }}
            </td>

            <!-- PRICE -->
            <td>
              <strong class="price">
                {{ formatPrice(item.TotalPrice) }}
              </strong>
            </td>

            <!-- DATE -->
            <td>
              {{ formatDate(item.BookingDateTime) }}
            </td>

          </tr>

        </tbody>

      </table>

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

const bookings = computed(
  () => dashboard.value.latest_bookings || []
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

const formatDate = (value) => {

  if (!value) return "N/A";

  return new Date(value)
    .toLocaleString("vi-VN", {

      timeZone: "Asia/Ho_Chi_Minh",

      year: "numeric",

      month: "2-digit",

      day: "2-digit",

      hour: "2-digit",

      minute: "2-digit",

      second: "2-digit",

    });
};
</script>

<style scoped>
/* BOX */

.table-box {
  background: #ffffff;

  padding: 24px;

  border-radius: 20px;

  border: 1px solid #e5e7eb;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.04),
    0 8px 24px rgba(0,0,0,0.06);
}

/* HEADER */

.table-header {
  display: flex;

  justify-content: space-between;

  align-items: center;

  margin-bottom: 24px;

  gap: 16px;
}

.table-header h2 {
  margin: 0;

  font-size: 24px;

  font-weight: 700;

  color: #111827;
}

.table-header p {
  margin-top: 6px;

  color: #6b7280;

  font-size: 14px;
}

/* BADGE */

.total-badge {
  background: #eff6ff;

  color: #2563eb;

  padding: 10px 16px;

  border-radius: 999px;

  font-size: 13px;

  font-weight: 700;
}

/* EMPTY */

.empty-box {
  padding: 40px;

  text-align: center;

  border-radius: 14px;

  background: #f9fafb;

  color: #6b7280;

  font-size: 15px;
}

/* TABLE */

.table-wrapper {
  overflow-x: auto;
}

table {
  width: 100%;

  border-collapse: collapse;

  min-width: 700px;
}

/* HEADER */

th {
  background: #f8fafc;

  color: #111827;

  font-size: 14px;

  font-weight: 700;

  text-align: left;

  padding: 16px;

  border-bottom: 1px solid #e5e7eb;
}

/* BODY */

td {
  padding: 16px;

  border-bottom: 1px solid #f1f5f9;

  color: #374151;

  font-size: 14px;
}

tbody tr {
  transition: 0.2s;
}

tbody tr:hover {
  background: #f8fafc;
}

/* BOOKING CODE */

.booking-code {
  background: #eef2ff;

  color: #4338ca;

  padding: 6px 12px;

  border-radius: 999px;

  font-size: 13px;

  font-weight: 700;
}

/* PRICE */

.price {
  color: #059669;

  font-weight: 700;
}

/* RESPONSIVE */

@media (max-width: 768px) {

  .table-header {
    flex-direction: column;

    align-items: flex-start;
  }
}
</style>