<template>
  <div class="bookings-page">

    <!-- FILTER -->
    <div class="filter-box">
      <button
        class="btn-create"
        @click="goCreate"
      >
        + Thêm mới
      </button>

      <input
        v-model="search"
        type="text"
        placeholder="Tìm theo mã, người dùng"
        class="form-control"
        @keyup.enter="loadData"
      />

      <select v-model="status" class="form-select">
        <option value="">Tất cả trạng thái</option>
        <option value="0">Chờ thanh toán</option>
        <option value="1">Đã thanh toán</option>
        <option value="2">Đã hủy</option>
      </select>

      <button class="btn-search" @click="loadData">
        Tìm kiếm
      </button>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>Mã booking</th>
            <th>Người đặt</th>
            <th>Khu vui chơi</th>
            <th>Thời gian</th>
            <th>Số vé</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loading">
            <td colspan="8" class="text-center-align">Đang tải...</td>
          </tr>

          <tr v-else-if="!list.length">
            <td colspan="8" class="text-center-align">Không có dữ liệu</td>
          </tr>

          <tr v-for="item in list" :key="item.ID">

          <td>{{ item.BookingCode }}</td>

          <td class="name" @click="view(item.ID)">
            {{ item.UserName }}
          </td>

          <td class="tieu_de">{{ item.ParkName }}</td>

          <td class="tieu_de">{{ item.BookingDateTime }}</td>

          <td class="tieu_de">{{ item.Quantity }} vé</td>

          <td class="tieu_de">{{ item.TotalPrice }}</td>

          <td>
            <span class="badge" :class="item.Status.color">
              {{ item.Status.icon }} {{ item.Status.text }}
            </span>
          </td>

          <td class="action-cell">
            <div class="action-grid">

              <!-- HÀNG 1 -->
              <button class="btn-view" @click="view(item.ID)">
                Chi tiết
              </button>

              <button 
                v-if="
                  item.Status.text === 'Chờ thanh toán'
                "
                class="btn-pay"
                @click="pay(item.ID)"
              >
                Thanh toán
              </button>

              <!-- HÀNG 2 -->
              <button 
                v-if="
                  item.Status.text === 'Chờ thanh toán'
                "
                class="btn-edit"
                @click="edit(item.ID)"
              >
                Sửa
              </button>

              <button 
                v-if="
                  item.Status.text === 'Chờ thanh toán'
                "
                class="btn-cancel"
                @click="cancel(item.ID)"
              >
                Hủy
              </button>

            </div>
          </td>

        </tr>
        </tbody>
      </table>
    </div>

    <!-- POPUP CANCEL -->
    <div
      v-if="showCancelPopup"
      class="popup-overlay"
    >
      <div class="popup-box">

        <div class="popup-header">
          Xác nhận hủy?
        </div>

        <div class="popup-body">
          Bạn có chắc chắn muốn hủy booking này không?
        </div>

        <div class="popup-footer">

          <button
            class="btn-popup-delete"
            @click="confirmCancel"
          >
            Hủy
          </button>

          <button
            class="btn-popup-cancel"
            @click="closeCancelPopup"
          >
            Đóng
          </button>

        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../provider/api";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { computed } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const authStore = useAuthStore();

const isAdmin = computed(() => {
  return (
    authStore.user?.role?.RoleName?.toLowerCase() === "admin"
  );
});

const isCustomer = computed(() => {
  return (
    authStore.user?.role?.RoleName?.toLowerCase() === "customer"
  );
});

const router = useRouter();

const list = ref([]);
const loading = ref(false);
const search = ref("");
const status = ref("");
const showCancelPopup = ref(false);
const cancelId = ref(null);
const loadData = async () => {
  loading.value = true;

  const res = await api.getList({
    search: search.value,
    status: status.value === "" ? null : Number(status.value),
  });

  list.value = res.data.data;
  loading.value = false;
};

const goCreate = () => router.push("/bookings/create");
const view = (id) => router.push(`/bookings/${id}`);
const edit = (id) => router.push(`/bookings/edit/${id}`);

const pay = (id) => {
  router.push(`/payments/pay?bookingId=${id}`)
}

const cancel = (id) => {
  cancelId.value = id;
  showCancelPopup.value = true;
};

const confirmCancel = async () => {

  try {

    await api.cancel(cancelId.value);

    toast.success("Hủy thành công.");

    showCancelPopup.value = false;

    await loadData();

  } catch (e) {

    toast.error("Hủy thất bại.");
  }
};

const closeCancelPopup = () => {
  showCancelPopup.value = false;
  cancelId.value = null;
};

onMounted(loadData);
</script>

<style scoped>
/* copy gần như 100% từ park */
.bookings-page {
  padding: 24px;
  background: #f4f6f9;
}

/* ===== FILTER ===== */
.filter-box {
  display: flex;
  align-items: center;
  gap: 12px;

  background: #fff;
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 20px;

  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.form-control,
.form-select {
  height: 40px;
  padding: 0 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  min-width: 200px;
}

/* BUTTON */
.btn-create {
  background: #00a65a;
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
  font-weight: 500;
}

.btn-search {
  background: #3c8dbc;
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  border: none;
}

/* ===== TABLE ===== */
.table-wrapper {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;

  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.table {
  width: 100%;
  border-collapse: collapse;
}

/* HEADER */
.table th {
  text-align: center;
  background: #f8fafc;
  padding: 16px;
  font-weight: 600;
  color: #333;
  border-bottom: 1px solid #e5e7eb;
}

.table th:nth-child(1),
.table th:nth-child(2) {
  text-align: left;
}

/* BODY */
.table td {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  text-align: left;
  vertical-align: middle;
}

/* HOVER */
.table tbody tr:hover {
  background: #f9fbfd;
}

/* ===== ALIGN ===== */

/* center: số vé, tổng tiền, status, action */
.table td:nth-child(5),
.table td:nth-child(6),
.table td:nth-child(7),
.table td:nth-child(8) {
  text-align: center;
}

/* TEXT */
.table td {
  white-space: nowrap;
}

/* park xuống dòng */
.table td:nth-child(3) {
  white-space: normal;
}

/* booking code đậm */
.table td:first-child {
  font-weight: 600;
  color: #111;
}

/* user clickable */
.name {
  cursor: pointer;
  color: #3c8dbc;
  font-weight: 500;
}

/* ===== BADGE ===== */
.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;

  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
}

.badge.warning {
  background: #fff3cd;
  color: #856404;
}

.badge.success {
  background: #d4edda;
  color: #155724;
}

.badge.danger {
  background: #f8d7da;
  color: #721c24;
}

/* ===== ACTION BUTTON ===== */
.btn-view {
  padding: 6px 12px;
  background: #4999df;
  color: #fff;
  border: none;
  border-radius: 6px;
  margin-right: 6px;
  cursor: pointer;
}

.btn-cancel {
  padding: 6px 12px;
  background: rgb(236, 16, 16);
  color: #fff;
  border: none;
  border-radius: 6px;
  margin-right: 6px;
  cursor: pointer;
}

/* hover nhẹ */
.btn-view:hover {
  background: #5a6268;
}

.btn-cancel:hover {
  background: #c82333;
}

.text-center-align{
  text-align: center !important;
  padding: 30px 0;
  font-weight: 500;
  color: #666;
}

.tieu_de {
  color: black;
}

.btn-edit {
  padding: 6px 12px;
  background: #ffc107;
  color: #000;
  border: none;
  border-radius: 6px;
  margin-right: 6px;
  cursor: pointer;
}

.btn-edit:hover {
  background: #e0a800;
}

.btn-pay {
  padding: 6px 12px;
  background: #28a745;
  color: #fff;
  border: none;
  border-radius: 6px;
  margin-right: 6px;
  cursor: pointer;
}

.btn-pay:hover {
  background: #218838;
}

.action-cell {
  min-width: 180px;
}

/* GRID 2x2 */
.action-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
}

/* button full width cho đều */
.action-grid button {
  width: 100;
}

.action-grid button:only-child {
  grid-column: span 2;
  justify-self: center;
}

/* OVERLAY */
.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);

  display: flex;
  align-items: flex-start;
  justify-content: center;

  padding-top: 100px;

  z-index: 9999;
}

/* BOX */
.popup-box {
  width: 340px;
  background: #fff;
  border-radius: 4px;
  overflow: hidden;

  animation: popupFade .2s ease;
}

/* HEADER */
.popup-header {
  padding: 18px 20px;
  font-size: 16px;
  font-weight: 700;
  color: #444;
  border-bottom: 1px solid #eee;
}

/* BODY */
.popup-body {
  padding: 22px 20px;
  color: #444;
  font-size: 14px;
}

/* FOOTER */
.popup-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;

  padding: 14px 20px;
  border-top: 1px solid #eee;
}

/* BUTTON DELETE */
.btn-popup-delete {
  min-width: 72px;
  height: 34px;

  border: none;
  border-radius: 6px;

  background: #e74c3c;
  color: #fff;

  cursor: pointer;
  font-weight: 600;
  font-size: 13px;
}

/* BUTTON CANCEL */
.btn-popup-cancel {
  min-width: 72px;
  height: 34px;

  border: 1px solid #dcdfe6;
  border-radius: 6px;

  background: #fff;
  color: #666;

  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
}

.btn-popup-cancel:hover {
  background: #f5f5f5;
}

@keyframes popupFade {
  from {
    transform: scale(.95);
    opacity: 0;
  }

  to {
    transform: scale(1);
    opacity: 1;
  }
}
</style>