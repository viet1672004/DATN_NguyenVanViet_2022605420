<template>
  <div class="detail-page" v-if="store.detail">

    <!-- HEADER -->
    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>
        <span class="title">Chi tiết vé</span>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="desc-box">

      <h2 class="tenve">{{ store.detail.TicketName }}</h2>

      <p>💰 Giá: <b>{{ formatPrice(store.detail.Price) }}</b></p>

      <p>🎟️ Loại: {{ getType(store.detail.TicketType) }}</p>

      <p>🏷️ Trạng thái:
        <span
          class="badge"
          :class="store.detail.Status == 1 ? 'active' : 'inactive'"
        >
          {{ store.detail.Status == 1 ? "Hoạt động" : "Ngừng hoạt động" }}
        </span>
      </p>

      <!-- DATE -->
      <p v-if="store.detail.TicketType === 'date'">
        📅 Số ngày: {{ store.detail.NumberOfDays }}
      </p>

      <!-- COMBO -->
      <p v-if="store.detail.TicketType === 'combo'">
        👨‍👩‍👧 Combo: {{ store.detail.ComboInfo }}
      </p>

      <p v-if="store.detail.Description">
        📝 Mô tả: {{ store.detail.Description }}
      </p>

    </div>

  </div>

  <!-- LOADING -->
  <div v-else class="loading">
    Đang tải dữ liệu...
  </div>
</template>

<script setup>
import { useRoute, useRouter } from "vue-router";
import { watch } from "vue";
import { useTicketStore } from "../provider/store";

const route = useRoute();
const router = useRouter();
const store = useTicketStore();

/* ================= LOAD DETAIL ================= */
watch(
  () => route.params.id,
  (id) => {
    if (!id) return;
    store.getDetail(id);
  },
  { immediate: true }
);

/* ================= FORMAT PRICE ================= */
const formatPrice = (value) => {
  if (!value) return "0";
  return Number(value).toLocaleString("vi-VN");
};

const goBack = () => router.push("/tickets");

const getType = (type) => {
  return {
    adult: "Người lớn",
    child: "Trẻ em",
    combo: "Combo",
    date: "Theo ngày"
  }[type];
};
</script>

<style scoped>
.detail-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

.tenve {
    color: #000;
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

.left {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* BACK */
.back-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #333;

  display: flex;
  align-items: center;
  justify-content: center;

  cursor: pointer;
}

.arrow {
  width: 8px;
  height: 8px;
  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;
  transform: rotate(45deg);
}

.back-icon:hover {
  background: #ddd;
}

.title {
  font-size: 22px;
  font-weight: 700;
  color: #454242;
}

/* BOX */
.desc-box {
  background: #fff;
  padding: 25px;
  border-radius: 12px;

  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.desc-box h2 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 15px;
}

.desc-box p {
  font-size: 15px;
  color: #555;
  margin-bottom: 12px;
  line-height: 1.6;
}

/* BADGE */
.badge {
  display: inline-flex;
  justify-content: center;
  align-items: center;

  width: 130px;
  height: 30px;

  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
}

.badge.active {
  background: #e6f7ee;
  color: #00a65a;
}

.badge.inactive {
  background: #e8d9ba;
  color: #bb6e16;
}

/* LOADING */
.loading {
  padding: 30px;
  text-align: center;
  font-size: 16px;
}
</style>
