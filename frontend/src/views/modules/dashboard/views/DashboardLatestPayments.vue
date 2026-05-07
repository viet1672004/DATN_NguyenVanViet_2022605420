<template>
  <div class="box">
    <h2>Thanh toán mới nhất</h2>

    <div
      class="payment-item"
      v-for="item in dashboard.latest_payments || []"
      :key="item.ID"
    >
      <div>
        <h4>{{ item.booking?.BookingCode }}</h4>
        <p>{{ item.PaymentMethod }}</p>
      </div>

      <strong>
        {{ formatPrice(item.Amount) }}
      </strong>
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

.payment-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 0;
  border-bottom: 1px solid #eee;
}

.payment-item h4,
.payment-item p,
.payment-item strong {
  color: #000;
}
</style>