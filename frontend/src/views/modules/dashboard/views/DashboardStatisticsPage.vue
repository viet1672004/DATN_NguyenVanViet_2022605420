<template>
  <div class="page">

    <!-- HEADER -->
    <div class="page-header">

      <div>
        <h1>🎯 Thống kê hoạt động</h1>

        <p>
          Phân tích khu vui chơi và vé bán chạy
        </p>
      </div>

      <!-- <button
        class="pdf-btn"
        @click="exportPdf"
      >
        📄 Xuất PDF
      </button> -->

    </div>

    <!-- LOADING -->
    <div
      v-if="dashboardStore.loading"
      class="loading-box"
    >
      Đang tải dữ liệu thống kê...
    </div>

    <!-- CONTENT -->
    <template v-else>

      <div ref="pdfRef">

        <!-- TOP -->
        <div class="grid-2">
          
          <DashboardTopParks />

          <DashboardTopTickets />

        </div>
      </div>
    </template>

  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import { useDashboardStore } from "../provider/store";

import DashboardTopParks from "./DashboardTopParks.vue";
import DashboardTopTickets from "./DashboardTopTickets.vue";
import { exportToPdf } from "@/utils/export/exportPdf";

const dashboardStore = useDashboardStore();
const pdfRef = ref(null);
const toast = useToast();

/*
|--------------------------------------------------------------------------
| Export PDF
|--------------------------------------------------------------------------
*/

const exportPdf = async () => {

  try {

    const images =
      pdfRef.value.querySelectorAll("img");

    await Promise.all(

      Array.from(images).map((img) => {

        if (img.complete)
          return Promise.resolve();

        return new Promise((resolve) => {

          img.onload = resolve;

          img.onerror = resolve;
        });
      })
    );

    await exportToPdf(

      pdfRef.value,

      "Bao-cao-thong-ke-hoat-dong.pdf",

      "BAO CAO THONG KE HOAT DONG",

      "Phan tich khu vui choi va ve ban chay"

    );

    toast.success(
      "Xuất file PDF thành công!"
    );

  } catch (error) {

    console.error(error);

    toast.error(
      "Xuất file PDF thất bại!"
    );
  }
};

/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(async () => {

  await dashboardStore.getDashboard();

});
</script>

<style scoped>
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

/* GRID */

.grid-2 {
  display: grid;

  grid-template-columns: 1fr 1fr;

  gap: 24px;

  margin-top: 24px;
}

/* RESPONSIVE */

@media (max-width: 1200px) {

  .grid-2 {
    grid-template-columns: 1fr;
  }
}

.pdf-btn {
  height: 44px;

  padding: 0 20px;

  border: none;

  border-radius: 12px;

  background: #dc2626;

  color: white;

  font-weight: 700;

  cursor: pointer;

  transition: 0.2s;
}

.pdf-btn:hover {
  background: #b91c1c;
}

@media (max-width: 768px) {

  .page-header {
    flex-direction: column;

    align-items: flex-start;
  }

  .pdf-btn {
    width: 100%;
  }
}
</style>