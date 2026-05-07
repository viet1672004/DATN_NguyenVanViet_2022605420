<template>
  <div class="table-box">
    <h2>Booking mới nhất</h2>

    <table>
      <thead>
        <tr>
          <th>Mã đơn</th>
          <th>Khách hàng</th>
          <th>Khu vui chơi</th>
          <th>Tổng tiền</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="item in dashboard.latest_bookings || []"
          :key="item.ID"
        >
          <td>{{ item.BookingCode }}</td>
          <td>{{ item.user?.Name }}</td>
          <td>{{ item.park?.ParkName }}</td>
          <td>{{ formatPrice(item.TotalPrice) }}</td>
        </tr>
      </tbody>
    </table>
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
.table-box {
  background: #fff;
  margin-top: 24px;
  padding: 24px;
  border-radius: 18px;
  border: 1px solid #ddd;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.table-box h2 {
  margin-bottom: 20px;
  color: #000;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #f8fafc;
  color: #000;
  font-weight: 600;
}

th,
td {
  padding: 16px;
  border-bottom: 1px solid #eee;
  text-align: left;
  color: #000;
}

tbody tr:hover {
  background: #f8fafc;
}
</style>