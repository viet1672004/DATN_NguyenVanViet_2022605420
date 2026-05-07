<template>
  <div class="box">
    <h2>Khu vui chơi nổi bật</h2>

    <div
      class="park-item"
      v-for="item in dashboard.top_parks || []"
      :key="item.ID"
    >
      <img :src="item.Image" />

      <div>
        <h4>{{ item.ParkName }}</h4>
        <p>{{ item.bookings_count }} bookings</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useDashboardStore } from "../provider/store";

const store = useDashboardStore();

const dashboard = computed(() => store.dashboard);
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

.park-item {
  display: flex;
  gap: 16px;
  padding: 14px 0;
  border-bottom: 1px solid #eee;
}

.park-item img {
  width: 100px;
  height: 75px;
  object-fit: cover;
  border-radius: 12px;
}

.park-item h4 {
  color: #000;
  margin-bottom: 6px;
}

.park-item p {
  color: #000;
}
</style>