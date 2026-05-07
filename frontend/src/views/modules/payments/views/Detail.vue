<template>
  <div class="page" v-if="data">

    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>

        <span class="title">
          Chi tiết thanh toán
        </span>
      </div>
    </div>

    <div class="card">
      <p><b>Mã thanh toán:</b> {{ data.ID }}</p>
      <p><b>Mã booking:</b> {{ data.booking?.BookingCode }}</p>
      <p><b>Phương thức thanh toán:</b> {{ data.PaymentMethod }}</p>
      <p><b>Số tiền:</b> {{ formatPrice(data.Amount) }}</p>
      <p>
        <b>Trạng thái:</b>
        <span :class="['status', data.PaymentStatus]">
            {{ getStatusText(data.PaymentStatus) }}
        </span>
      </p>
      <p><b>Ngày thanh toán:</b> {{ formatDate(data.PaymentDate) }}</p>
    </div>

    <div class="card" v-if="data.booking">
      <h3>Thông tin booking</h3>
      <p><b>Người đặt:</b> {{ data.booking.user?.Name }}</p>
      <p><b>Khu vui chơi:</b> {{ data.booking.park?.ParkName }}</p>
      <p><b>Tổng tiền:</b> {{ formatPrice(data.booking.TotalPrice) }}</p>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../provider/api";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const data = ref(null);

onMounted(async () => {
  const res = await api.getDetail(route.params.id);
  data.value = res.data;
});

const goBack = () => router.push("/payments");

const formatPrice = (v) =>
  Number(v || 0).toLocaleString("vi-VN") + " đ";

const formatDate = (d) =>
  new Date(d).toLocaleString("vi-VN");

const getStatusText = (s) => {
  switch (s) {
    case "PAID": return "🟢 Thành công";
    case "PENDING": return "🟡 Đang xử lý";
    case "FAILED": return "🔴 Thất bại";
    default: return s;
  }
};
</script>

<style scoped>
.page {
  padding: 24px;
  background: #f4f6f9;
  min-height: 100vh;
}

/* TITLE */
h2 {
  margin-bottom: 15px;
  color: #333;
}

/* CARD */
.card {
  background: #fff;
  padding: 20px;
  margin-top: 15px;
  border-radius: 12px;

  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* TEXT */
.card p {
  margin-bottom: 8px;
  color: #333;
}

.card b {
  color: #000;
}

/* SECTION TITLE */
.card h3 {
  margin-bottom: 10px;
  color: #111;
}

/* STATUS */
.status {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

/* COLOR STATUS */
.status.PAID {
  background: #d4edda;
  color: #155724;
}

.status.PENDING {
  background: #fff3cd;
  color: #856404;
}

.status.FAILED {
  background: #f8d7da;
  color: #721c24;
}

/* HEADER */
.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;

  background: #fff;
  padding: 15px 20px;
  border-radius: 12px;

  margin-bottom: 20px;

  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

/* LEFT */
.left {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* BACK ICON */
.back-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;

  background: #333;

  display: flex;
  align-items: center;
  justify-content: center;

  cursor: pointer;

  transition: all 0.2s ease;
}

.back-icon:hover {
  background: #555;
}

/* ARROW */
.arrow {
  width: 8px;
  height: 8px;

  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;

  transform: rotate(45deg);
}

/* TITLE */
.title {
  font-size: 22px;
  font-weight: 700;
  color: #454242;
}
</style>
