<template>
  <div class="box">

    <!-- HEADER -->
    <div class="box-header">

      <div>
        <h2>🎫 Vé bán chạy nhất</h2>

        <p>
          Các loại vé được đặt nhiều nhất
        </p>
      </div>

      <div class="badge">
        {{ tickets.length }} vé
      </div>

    </div>

    <!-- EMPTY -->
    <div
      v-if="!tickets.length"
      class="empty-box"
    >
      Không có dữ liệu vé
    </div>

    <!-- LIST -->
    <div
      v-else
      class="ticket-list"
    >

      <div
        class="ticket-item"
        v-for="(item, index) in tickets"
        :key="item.ID"
      >

        <!-- LEFT -->
        <div class="left">

          <div class="rank">
            #{{ index + 1 }}
          </div>

          <div>

            <h4>
              {{ item.TicketName }}
            </h4>

            <p>
              Vé được đặt nhiều
            </p>

          </div>

        </div>

        <!-- RIGHT -->
        <div class="sold-box">

          <strong>
            {{ item.total_sold }}
          </strong>

          <span>
            lượt bán
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

const tickets = computed(
  () => dashboard.value.top_tickets || []
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
  background: #fef3c7;

  color: #d97706;

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

.ticket-list {
  display: flex;

  flex-direction: column;

  gap: 16px;
}

/* ITEM */

.ticket-item {
  display: flex;

  justify-content: space-between;

  align-items: center;

  gap: 16px;

  padding: 16px;

  border-radius: 16px;

  border: 1px solid #f1f5f9;

  transition: 0.25s;
}

.ticket-item:hover {
  background: #f8fafc;

  transform: translateY(-2px);
}

/* LEFT */

.left {
  display: flex;

  align-items: center;

  gap: 14px;
}

/* RANK */

.rank {
  width: 42px;

  height: 42px;

  min-width: 42px;

  border-radius: 12px;

  background: #fef3c7;

  color: #d97706;

  font-weight: 700;

  display: flex;

  align-items: center;

  justify-content: center;
}

/* TEXT */

.ticket-item h4 {
  margin: 0;

  font-size: 16px;

  font-weight: 700;

  color: #111827;
}

.ticket-item p {
  margin-top: 6px;

  color: #6b7280;

  font-size: 14px;
}

/* SOLD */

.sold-box {
  text-align: right;
}

.sold-box strong {
  display: block;

  font-size: 22px;

  font-weight: 700;

  color: #111827;
}

.sold-box span {
  font-size: 13px;

  color: #6b7280;
}

/* RESPONSIVE */

@media (max-width: 768px) {

  .box-header {
    flex-direction: column;

    align-items: flex-start;
  }

  .ticket-item {
    flex-direction: column;

    align-items: flex-start;
  }

  .sold-box {
    text-align: left;
  }
}
</style>