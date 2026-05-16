<template>
  <div class="page">

    <!-- HEADER -->
    <div class="page-header">

      <div>
        <h1>🧾 Quản lý Booking</h1>

        <p>
          Danh sách các đơn booking mới nhất trong hệ thống
        </p>
      </div>

      <button
        class="excel-btn"
        @click="exportExcel"
      >
        📊 Xuất Excel
      </button>

    </div>

    <!-- LOADING -->
    <div
      v-if="dashboardStore.loading"
      class="loading-box"
    >
      Đang tải dữ liệu booking...
    </div>

    <!-- CONTENT -->
    <template v-else>

      <!-- SUMMARY -->
      <DashboardSummary />

      <!-- FILTER BOX -->
      <div class="filter-box">

        <div class="filter-header">

          <h3>
            🔎 Bộ lọc booking
          </h3>

          <p>
            Lọc danh sách booking theo khoảng thời gian
          </p>

        </div>

        <div class="filter-actions">

          <!-- FROM DATE -->
          <div class="date-group">

            <label>
              Từ ngày
            </label>

            <input
              v-model="fromDate"
              type="date"
              class="date-input"
            />

          </div>

          <!-- TO DATE -->
          <div class="date-group">

            <label>
              Đến ngày
            </label>

            <input
              v-model="toDate"
              type="date"
              class="date-input"
            />

          </div>

          <!-- FILTER -->
          <button
            class="filter-btn"
            @click="loadDashboard"
          >
            🔍 Lọc
          </button>

          <!-- RESET -->
          <button
            class="refresh-btn"
            @click="resetFilter"
          >
            🔄 Làm mới
          </button>

        </div>

      </div>

      <!-- BOOKINGS TABLE -->
      <div class="mt">
        <DashboardLatestBookings />
      </div>

    </template>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

import { useDashboardStore } from "../provider/store";

import DashboardSummary from "./DashboardSummary.vue";
import DashboardLatestBookings from "./DashboardLatestBookings.vue";
import { exportToExcel } from "@/utils/export/exportExcel";
import { useToast } from "vue-toastification";

const toast = useToast();

const dashboardStore = useDashboardStore();

/*
|------------------------------------------------------------------
| Format Local Date (VN)
|------------------------------------------------------------------
*/

const formatLocalDate = (date) => {

  const year = date.getFullYear();

  const month = String(
    date.getMonth() + 1
  ).padStart(2, "0");

  const day = String(
    date.getDate()
  ).padStart(2, "0");

  return `${year}-${month}-${day}`;
};

/*
|------------------------------------------------------------------
| Default Date
|------------------------------------------------------------------
*/

const today = new Date();

const lastMonth = new Date();

lastMonth.setDate(
  today.getDate() - 30
);

/*
|------------------------------------------------------------------
| Filter
|------------------------------------------------------------------
*/

const fromDate = ref(
  formatLocalDate(lastMonth)
);

const toDate = ref(
  formatLocalDate(today)
);

/*
|------------------------------------------------------------------
| Load Dashboard
|------------------------------------------------------------------
*/

const loadDashboard = async () => {

  if (
    fromDate.value &&
    toDate.value &&
    fromDate.value > toDate.value
  ) {

    toast.error(
      "Ngày bắt đầu không được lớn hơn ngày kết thúc."
    );

    return;
  }

  await dashboardStore.getDashboard({

    from_date: fromDate.value,

    to_date: toDate.value,

  });
};

/*
|------------------------------------------------------------------
| Reset
|------------------------------------------------------------------
*/

const resetFilter = async () => {

  fromDate.value =
    formatLocalDate(lastMonth);

  toDate.value =
    formatLocalDate(today);

  await loadDashboard();
};

/*
|--------------------------------------------------------------------------
| Export Excel
|--------------------------------------------------------------------------
*/

const exportExcel = async () => {

  try {

    const bookings =
      dashboardStore.dashboard
        ?.latest_bookings || [];

    const data = bookings.map((item) => ({

      /*
      |--------------------------------------------------------------------------
      | Ngày đặt
      |--------------------------------------------------------------------------
      */

      "Ngày đặt":
        item.BookingDateTime
          ? new Date(item.BookingDateTime)
              .toLocaleString("vi-VN")
          : "N/A",

      /*
      |--------------------------------------------------------------------------
      | Mã booking
      |--------------------------------------------------------------------------
      */

      "Mã booking":
        item.BookingCode || "N/A",

      /*
      |--------------------------------------------------------------------------
      | Khách hàng
      |--------------------------------------------------------------------------
      */

      "Khách hàng":
        item.user?.Name || "N/A",

      /*
      |--------------------------------------------------------------------------
      | Khu vui chơi
      |--------------------------------------------------------------------------
      */

      "Khu vui chơi":
        item.park?.ParkName || "N/A",

      /*
      |--------------------------------------------------------------------------
      | Trạng thái
      |--------------------------------------------------------------------------
      */

      "Trạng thái":
        Number(item.Status) === 0
          ? "Chờ thanh toán"
          : Number(item.Status) === 1
          ? "Đã thanh toán"
          : Number(item.Status) === 2
          ? "Đã hủy"
          : "N/A",

      /*
      |--------------------------------------------------------------------------
      | Tổng tiền hiển thị
      |--------------------------------------------------------------------------
      */

      "Tổng tiền":
        Number(item.TotalPrice || 0)
          .toLocaleString("vi-VN") + " đ",

      /*
      |--------------------------------------------------------------------------
      | Chỉ tính booking đã thanh toán
      |--------------------------------------------------------------------------
      */

      total_price:
        Number(item.Status) === 2
          ? 0
          : Number(item.TotalPrice || 0),

    }));

    await exportToExcel(

      data,

      "Danh-sach-bookings.xlsx",

      "BÁO CÁO BOOKING",

      `Ngày xuất: ${new Date()
        .toLocaleDateString("vi-VN")}`

    );

    toast.success(
      "Xuất file Excel thành công!"
    );

  } catch (error) {

    console.error(error);

    toast.error(
      "Xuất file Excel thất bại!"
    );
  }
};

/*
|------------------------------------------------------------------
| Mounted
|------------------------------------------------------------------
*/

onMounted(async () => {

  await loadDashboard();

});
</script>
<style scoped>
/* PAGE */

.page {
  min-height: 100vh;

  padding: 24px;

  background: #f1f5f9;
}

/* HEADER */

.page-header {
  display: flex;

  justify-content: space-between;

  align-items: center;

  gap: 16px;

  margin-bottom: 28px;
}

.page-header h1 {
  margin: 0;

  font-size: 32px;

  font-weight: 700;

  color: #111827;
}

.page-header p {
  margin-top: 6px;

  font-size: 15px;

  color: #6b7280;
}

/* FILTER BOX */

.filter-box {
  margin-top: 24px;

  padding: 24px;

  background: white;

  border-radius: 20px;

  border: 1px solid #e5e7eb;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.04),
    0 8px 24px rgba(0,0,0,0.06);
}

.filter-header {
  margin-bottom: 20px;
}

.filter-header h3 {
  margin: 0;

  font-size: 22px;

  font-weight: 700;

  color: #111827;
}

.filter-header p {
  margin-top: 6px;

  font-size: 14px;

  color: #6b7280;
}

/* FILTER ACTIONS */

.filter-actions {
  display: flex;

  align-items: flex-end;

  gap: 14px;

  flex-wrap: wrap;
}

/* DATE GROUP */

.date-group {
  display: flex;

  flex-direction: column;

  gap: 6px;
}

.date-group label {
  font-size: 13px;

  font-weight: 600;

  color: #374151;
}

/* INPUT */

.date-input {
  height: 42px;

  padding: 0 14px;

  border-radius: 12px;

  border: 1px solid #d1d5db;

  background: white;

  color: #111827;

  font-size: 14px;

  outline: none;

  transition: 0.2s;
}

.date-input:focus {
  border-color: #2563eb;

  box-shadow:
    0 0 0 3px rgba(37, 99, 235, 0.15);
}

/* BUTTON */

.filter-btn,
.refresh-btn {
  height: 42px;

  padding: 0 18px;

  border: none;

  border-radius: 12px;

  color: white;

  font-weight: 600;

  cursor: pointer;

  transition: 0.2s;
}

.filter-btn {
  background: #10b981;
}

.filter-btn:hover {
  background: #059669;
}

.refresh-btn {
  background: #2563eb;
}

.refresh-btn:hover {
  background: #1d4ed8;
}

/* LOADING */

.loading-box {
  background: white;

  padding: 40px;

  border-radius: 18px;

  text-align: center;

  font-size: 18px;

  color: #374151;

  border: 1px solid #e5e7eb;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.04),
    0 8px 24px rgba(0,0,0,0.06);
}

/* SPACING */

.mt {
  margin-top: 24px;
}

/* RESPONSIVE */

@media (max-width: 768px) {

  .filter-actions {
    flex-direction: column;

    align-items: stretch;
  }

  .date-group,
  .filter-btn,
  .refresh-btn {
    width: 100%;
  }

  .date-input {
    width: 100%;
  }
}

.excel-btn {
  height: 44px;

  padding: 0 20px;

  border: none;

  border-radius: 12px;

  background: #059669;

  color: white;

  font-weight: 700;

  cursor: pointer;

  transition: 0.2s;
}

.excel-btn:hover {
  background: #047857;
}

@media (max-width: 768px) {

  .page-header {
    flex-direction: column;

    align-items: stretch;
  }

  .excel-btn {
    width: 100%;
  }
}
</style>