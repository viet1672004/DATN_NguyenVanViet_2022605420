<template>
  <div class="page">

    <!-- FILTER -->
    <div class="filter-box">
      <input v-model="search" placeholder="Mã Booking / Hình thức thanh toán" />

      <select v-model="status">
        <option value="">Tất cả</option>
        <option value="PAID">Đã thanh toán</option>
        <option value="PENDING">Đang xử lý</option>
        <option value="FAILED">Thất bại</option>
      </select>

      <div class="date-filter">
        <label>Từ ngày thanh toán</label>
        <input type="date" v-model="from_date" />
      </div>

      <div class="date-filter">
        <label>Đến ngày thanh toán</label>
        <input type="date" v-model="to_date" />
      </div>

      <button @click="loadData">Tìm kiếm</button>
    </div>

    <!-- TABLE -->
    <table class="table">
      <thead>
        <tr>
          <th>Mã Booking</th>
          <th>Phương thức thanh toán</th>
          <th>Số tiền</th>
          <th>Trạng thái</th>
          <th>Ngày</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <tr v-if="loading">
          <td colspan="6">Đang tải...</td>
        </tr>

        <tr v-else-if="!list.length">
          <td colspan="6">Không có dữ liệu</td>
        </tr>

        <tr v-for="item in list" :key="item.ID">
          <td>{{ item.booking?.BookingCode }}</td>
          <td>{{ item.PaymentMethod }}</td>
          <td>{{ formatPrice(item.Amount) }}</td>

          <td>
            <span :class="['badge', item.PaymentStatus]">
              {{ getStatusText(item.PaymentStatus) }}
            </span>
          </td>

          <td>{{ formatDate(item.PaymentDate) }}</td>

          <td>
            <button @click="view(item.ID)">
              Chi tiết
            </button>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../provider/api";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { computed } from "vue";

const authStore = useAuthStore();

// const isAdmin = computed(() => {
//   return (
//     authStore.user?.role?.RoleName?.toLowerCase() === "admin"
//   );
// });

// const isCustomer = computed(() => {
//   return (
//     authStore.user?.role?.RoleName?.toLowerCase() === "customer"
//   );
// });

const router = useRouter();

const list = ref([]);
const loading = ref(false);

const search = ref("");
const status = ref("");
const from_date = ref("");
const to_date = ref("");

const loadData = async () => {
  loading.value = true;

  const res = await api.getList({
    search: search.value,
    status: status.value || null,
    from_date: from_date.value || null,
    to_date: to_date.value || null,
  });

  list.value = res.data.data;
  loading.value = false;
};

const view = (id) => router.push(`/payments/${id}`);

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

onMounted(loadData);
</script>

<style scoped>
.page {
  padding: 24px;
  background: #f4f6f9;
}

/* FILTER */
.filter-box {
  display: flex;
  align-items: center;
  gap: 20px;

  background: #fff;
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 20px;

  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.filter-box input,
.filter-box select {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.filter-box button {
  padding: 8px 14px;
  background: #3c8dbc;
  color: white;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.filter-box button:hover {
  background: #367fa9;
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;

  border-radius: 12px;
  overflow: hidden;

  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.table th {
  background: #f8fafc;
  padding: 14px;
  text-align: center;
  border-bottom: 1px solid #eee;
  color: #333;
}

.table td {
  padding: 12px;
  text-align: center;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
  color: #333;
}

/* HOVER */
.table tbody tr:hover {
  background: #f9fbfd;
}

/* BADGE */
.badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

.badge.PAID {
  background: #d4edda;
  color: #155724;
}

.badge.PENDING {
  background: #fff3cd;
  color: #856404;
}

.badge.FAILED {
  background: #f8d7da;
  color: #721c24;
}

/* BUTTON */
.table button {
  padding: 6px 12px;
  border-radius: 6px;
  background: #4999df;
  color: white;
  border: none;
  cursor: pointer;
}

.table button:hover {
  background: #357ab8;
}

.date-filter {
  display: flex;
  align-items: center;
  gap: 8px;
}

.date-filter label {
  white-space: nowrap;
  font-size: 14px;
  color: #333;
}
</style>