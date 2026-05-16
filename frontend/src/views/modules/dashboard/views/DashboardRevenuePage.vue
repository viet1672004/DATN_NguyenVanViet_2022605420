<template>
  <div class="page">

    <!-- HEADER -->
    <div class="page-header">

      <div>
        <h1>💰 Quản lý doanh thu</h1>

        <p>
          Thống kê doanh thu và thanh toán mới nhất
        </p>
      </div>

      <div class="header-actions">

        <button
          class="excel-btn"
          @click="exportExcel"
        >
          📊 Xuất Excel
        </button>

        <button
          class="pdf-btn"
          @click="exportPdf"
        >
          📄 Xuất PDF
        </button>

      </div>

    </div>

    <!-- LOADING -->
    <div
      v-if="dashboardStore.loading"
      class="loading-box"
    >
      Đang tải dữ liệu doanh thu...
    </div>

    <!-- CONTENT -->
    <template v-else>

      <div ref="pdfRef">

        <!-- CHART -->
        <div class="mt">
          <DashboardRevenue
            :revenueChart="
              dashboardStore.dashboard.revenue_chart
            "
          />
        </div>

        <!-- PAYMENTS -->
        <div class="mt">
          <DashboardLatestPayments />
        </div>
      </div>
    </template>

  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";

import { useDashboardStore } from "../provider/store";
import DashboardRevenue from "./DashboardRevenue.vue";
import DashboardLatestPayments from "./DashboardLatestPayments.vue";
import { exportToPdf } from "@/utils/export/exportPdf";
import { exportToExcel } from "@/utils/export/exportExcel";
import { useToast } from "vue-toastification";

const toast = useToast();
const dashboardStore = useDashboardStore();
const pdfRef = ref(null);

/*
|--------------------------------------------------------------------------
| Export PDF
|--------------------------------------------------------------------------
*/

const exportPdf = async () => {

  try {

    await exportToPdf(

      pdfRef.value,

      "Bao-cao-doanh-thu.pdf",

      "BAO CAO DOANH THU",

      "Thong ke doanh thu va thanh toan"

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
| Export Excel
|--------------------------------------------------------------------------
*/

    const exportExcel = async () => {

      try {

        const payments =
          dashboardStore.dashboard
            ?.latest_payments || [];

        const data = payments.map((item) => ({

      "Mã booking":
        item.booking?.BookingCode || "N/A",

      "Phương thức":
        item.PaymentMethod || "N/A",

      "Trạng thái":
        item.PaymentStatus === "PAID"
          ? "Đã thanh toán"
          : item.PaymentStatus === "PENDING"
          ? "Chờ thanh toán"
          : item.PaymentStatus === "CANCELLED"
          ? "Đã hủy"
          : item.PaymentStatus || "N/A",

      "Ngày thanh toán":
        item.PaymentDate
          ? new Date(item.PaymentDate)
              .toLocaleString("vi-VN")
          : "N/A",

      "Số tiền":
        Number(item.Amount || 0)
          .toLocaleString("vi-VN") + " đ",

      total_price:
        Number(item.Amount || 0),
    }));

    await exportToExcel(

      data,

      "quan-ly-doanh-thu.xlsx",

      "BÁO CÁO DOANH THU",

      `Ngày xuất: ${new Date()
        .toLocaleDateString("vi-VN")}`

    );

    toast.success("Xuất file Excel thành công!");

  } catch (error) {

    console.error(error);

    toast.error("Xuất file Excel thất bại!");

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

/* SPACING */

.mt {
  margin-top: 24px;
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

.header-actions {
  display: flex;

  gap: 12px;

  flex-wrap: wrap;
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

  .header-actions {
    width: 100%;
  }

  .excel-btn,
  .pdf-btn {
    width: 100%;
  }
}
</style>