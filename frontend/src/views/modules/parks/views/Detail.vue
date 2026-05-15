<template>
  <div class="detail-page" v-if="store.detail">

    <!-- 🔝 HEADER -->
    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
            <i class="arrow"></i>
        </span>
        <span class="title">Chi tiết khu vui chơi</span>
      </div>
    </div>

    <!-- 🔷 TOP BOX -->
    <div class="top-box">

      <!-- IMAGE -->
      <img
        :src="store.detail.Image || 'https://via.placeholder.com/500x320'"
        class="thumb"
      />

      <!-- INFO -->
      <div class="info">

        <h2 class="parkname">{{ store.detail.ParkName }}</h2>

        <p class="location">📍 {{ store.detail.Location }}</p>

        <p class="time">
          🕒 {{ store.detail.OpenTime?.slice(0,5) }} - {{ store.detail.CloseTime?.slice(0,5) }}
        </p>

        <span
          class="status"
          :class="store.detail.Status ? 'active' : 'inactive'"
        >
          {{ store.detail.Status ? 'Hoạt động' : 'Ngừng hoạt động' }}
        </span>

        <button
          class="btn-book"
          @click="goBooking"
        >
          Đặt vé ngay
        </button>

      </div>

    </div>

    <!-- 🔶 DESCRIPTION -->
    <div class="desc-box">
      <h3 class="mota">Mô tả</h3>
      <p>{{ store.detail.Description || 'Không có mô tả' }}</p>
    </div>

  </div>
</template>

<script setup>
import { watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useParkStore } from "../provider/store";

const route = useRoute();
const router = useRouter();
const store = useParkStore();

watch(
  () => route.params.id,
  (id) => {
    if (!id) return;
    store.getDetail(id);
  },
  { immediate: true }
);

const goBack = () => {
  router.push("/parks");
};

const goBooking = () => {
  router.push({
    path: "/bookings/create",
    query: {
      park_id: store.detail.ID
    }
  });
};
</script>

<style scoped>
.detail-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

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

/* 🔥 mũi tên to + đậm hơn */
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

/* tạo mũi tên bằng CSS */
.arrow {
  display: block;
  width: 8px;
  height: 8px;
  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;

  transform: rotate(45deg); /* tạo mũi tên */
}

.back-icon:hover {
  background: #ddd;
}

.title {
  font-size: 22px;      
  font-weight: 700;     
  color: #454242;
}

.left {
  display: flex;
  align-items: center;
  gap: 10px; 
}

/* TOP BOX */
.top-box {
  display: flex;
  gap: 30px;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.thumb {
  width: 500px;
  height: 320px;
  object-fit: cover;
  border-radius: 10px;
}

/* INFO */
.info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.info h2 {
  font-size: 26px;
  font-weight: 700;
  margin-bottom: 15px;
}
.parkname,
.location,
.time,
.mota {
  font-size: 16px;
  margin-bottom: 10px;
  color: #555;
}

/* STATUS */
.status {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 15px;
  width: fit-content;
}

.status.active {
  background: #e6f7ee;
  color: #00a65a;
}

.status.inactive {
  background: #f1f1f1;
  color: #999;
}

/* BUTTON */
.btn-book {
  margin-top: 10px;
  padding: 12px 20px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  width: fit-content;
}

.btn-book:hover {
  background: #008d4c;
}

/* DESCRIPTION */
.desc-box {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.desc-box h3 {
  margin-bottom: 10px;
  font-size: 18px;
  font-weight: 600;
}

.desc-box p {
  color: #555;
  line-height: 1.6;
}
</style>