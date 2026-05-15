<template>
  <div class="box">

    <!-- HEADER -->
    <div class="box-header">

      <div>
        <h2>Thanh toán mới nhất</h2>

        <p>
          Danh sách giao dịch gần đây
        </p>
      </div>

      <div class="payment-count">
        {{ payments.length }} giao dịch
      </div>

    </div>

    <!-- EMPTY -->
    <div
      v-if="!payments.length"
      class="empty-box"
    >
      Không có dữ liệu thanh toán
    </div>

    <!-- PAYMENTS -->
    <div v-else>

      <div
        class="payment-item"
        v-for="item in payments"
        :key="item.ID"
      >

        <!-- LEFT -->
        <div class="payment-left">

          <div class="payment-icon">
            💳
          </div>

          <div>

            <h4>
              {{ item.booking?.BookingCode || "N/A" }}
            </h4>

            <p>
              {{ item.PaymentMethod || "N/A" }}
            </p>

            <span class="payment-date">
              {{ formatDate(item.PaymentDate) }}
            </span>

          </div>

        </div>

        <!-- RIGHT -->
        <div class="payment-right">

          <strong class="price">
            {{ formatPrice(item.Amount) }}
          </strong>

          <span
            class="status"
            :class="getStatusClass(item.PaymentStatus)"
          >
            {{ translateStatus(item.PaymentStatus) }}
          </span>

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

const payments = computed(
  () => dashboard.value.latest_payments || []
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
    .toLocaleDateString("vi-VN");
};

const translateStatus = (status) => {

  switch (status) {

    case "PAID":
      return "Đã thanh toán";

    case "PENDING":
      return "Chờ thanh toán";

    case "FAILED":
      return "Thanh toán lỗi";

    case "CANCEL":
      return "Đã hủy";

    default:
      return "Không xác định";
  }
};

const getStatusClass = (status) => {

  switch (status) {

    case "PAID":
      return "success";

    case "PENDING":
      return "pending";

    case "FAILED":
    case "CANCEL":
      return "failed";

    default:
      return "default";
  }
};
</script>

<style scoped>
/* BOX */

.box {
  background: #ffffff;

  padding: 24px;

  border-radius: 20px;

  border: 1px solid #e5e7eb;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.04),
    0 8px 24px rgba(0,0,0,0.06);
}

/* HEADER */

.box-header {
  display: flex;

  justify-content: space-between;

  align-items: center;

  margin-bottom: 24px;

  gap: 16px;
}

.box-header h2 {
  margin: 0;

  font-size: 24px;

  font-weight: 700;

  color: #111827;
}

.box-header p {
  margin-top: 6px;

  font-size: 14px;

  color: #6b7280;
}

/* COUNT */

.payment-count {
  background: #ecfeff;

  color: #0891b2;

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

/* ITEM */

.payment-item {
  display: flex;

  justify-content: space-between;

  align-items: center;

  gap: 16px;

  padding: 18px 0;

  border-bottom: 1px solid #f1f5f9;
}

/* LEFT */

.payment-left {
  display: flex;

  align-items: center;

  gap: 14px;
}

.payment-icon {
  width: 50px;

  height: 50px;

  border-radius: 14px;

  background: #eff6ff;

  display: flex;

  align-items: center;

  justify-content: center;

  font-size: 22px;
}

.payment-left h4 {
  margin: 0;

  color: #111827;

  font-size: 15px;

  font-weight: 700;
}

.payment-left p {
  margin: 4px 0;

  color: #6b7280;

  font-size: 13px;
}

.payment-date {
  font-size: 12px;

  color: #9ca3af;
}

/* RIGHT */

.payment-right {
  display: flex;

  flex-direction: column;

  align-items: flex-end;

  gap: 8px;
}

.price {
  color: #059669;

  font-size: 16px;

  font-weight: 700;
}

/* STATUS */

.status {
  padding: 6px 12px;

  border-radius: 999px;

  font-size: 12px;

  font-weight: 700;
}

.success {
  background: #dcfce7;

  color: #166534;
}

.pending {
  background: #fef9c3;

  color: #854d0e;
}

.failed {
  background: #fee2e2;

  color: #991b1b;
}

.default {
  background: #f3f4f6;

  color: #374151;
}

/* RESPONSIVE */

@media (max-width: 768px) {

  .box-header {
    flex-direction: column;

    align-items: flex-start;
  }

  .payment-item {
    flex-direction: column;

    align-items: flex-start;
  }

  .payment-right {
    align-items: flex-start;
  }
}
</style>