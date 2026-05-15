<template>
  <div class="box">

    <!-- HEADER -->
    <div class="box-header">

      <div>
        <h2>🎡 Khu vui chơi nổi bật</h2>

        <p>
          Các khu vui chơi có nhiều booking nhất
        </p>
      </div>

      <div class="badge">
        {{ parks.length }} khu
      </div>

    </div>

    <!-- EMPTY -->
    <div
      v-if="!parks.length"
      class="empty-box"
    >
      Không có dữ liệu khu vui chơi
    </div>

    <!-- LIST -->
    <div
      v-else
      class="park-list"
    >

      <div
        class="park-item"
        v-for="(item, index) in parks"
        :key="item.ID"
      >

        <!-- RANK -->
        <div class="rank">
          #{{ index + 1 }}
        </div>

        <!-- IMAGE -->
        <img
          :src="item.Image"
          :alt="item.ParkName"
        />

        <!-- INFO -->
        <div class="park-info">

          <h4>
            {{ item.ParkName }}
          </h4>

          <p>
            {{ item.bookings_count }} bookings
          </p>

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

const parks = computed(
  () => dashboard.value.top_parks || []
);
</script>

<style scoped>
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

  gap: 16px;

  margin-bottom: 24px;
}

.box-header h2 {
  margin: 0;

  font-size: 22px;

  font-weight: 700;

  color: #111827;
}

.box-header p {
  margin-top: 6px;

  color: #6b7280;

  font-size: 14px;
}

/* BADGE */

.badge {
  background: #eff6ff;

  color: #2563eb;

  padding: 10px 16px;

  border-radius: 999px;

  font-size: 13px;

  font-weight: 700;
}

/* EMPTY */

.empty-box {
  height: 220px;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 14px;

  background: #f9fafb;

  color: #6b7280;
}

/* LIST */

.park-list {
  display: flex;

  flex-direction: column;

  gap: 16px;
}

/* ITEM */

.park-item {
  display: flex;

  align-items: center;

  gap: 16px;

  padding: 14px;

  border-radius: 16px;

  border: 1px solid #f1f5f9;

  transition: 0.25s;

  background: #ffffff;
}

.park-item:hover {
  transform: translateY(-2px);

  background: #f8fafc;
}

/* RANK */

.rank {
  width: 42px;

  height: 42px;

  min-width: 42px;

  border-radius: 12px;

  background: #dbeafe;

  color: #2563eb;

  font-weight: 700;

  display: flex;

  align-items: center;

  justify-content: center;
}

/* IMAGE */

.park-item img {
  width: 110px;

  height: 80px;

  object-fit: cover;

  border-radius: 14px;

  border: 1px solid #e5e7eb;
}

/* INFO */

.park-info {
  flex: 1;
}

.park-info h4 {
  margin: 0;

  font-size: 16px;

  font-weight: 700;

  color: #111827;
}

.park-info p {
  margin-top: 8px;

  color: #6b7280;

  font-size: 14px;
}

/* RESPONSIVE */

@media (max-width: 768px) {

  .box-header {
    flex-direction: column;

    align-items: flex-start;
  }

  .park-item {
    flex-direction: column;

    align-items: flex-start;
  }

  .park-item img {
    width: 100%;

    height: 180px;
  }
}
</style>