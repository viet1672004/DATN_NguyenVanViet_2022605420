<template>
  <div class="tickets-page">

    <!-- FILTER -->
    <div class="filter-box">
      <button v-if="isAdmin" class="btn-create" @click="goCreate">
        + Thêm vé
      </button>

      <input
        v-model="search"
        placeholder="Tìm tên vé..."
        class="form-control"
        @keyup.enter="loadData"
      />

      <!-- TYPE -->
      <select v-model="type" class="form-select">
        <option value="">Tất cả loại</option>
        <option value="adult">Người lớn</option>
        <option value="child">Trẻ em</option>
        <option value="combo">Combo</option>
        <option value="date">Theo ngày</option>
      </select>

      <!-- STATUS 👈 NEW -->
      <select v-model="status" class="form-select">
        <option value="">Tất cả trạng thái</option>
        <option value="1">Hoạt động</option>
        <option value="0">Ngừng hoạt động</option>
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
            <th width="150">Tên vé</th>
            <th width="200">Giá</th>
            <th width="220">Khu vui chơi</th>
            <th width="200">Loại</th>
            <th width="150">Trạng thái</th>
            <th width="150">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="store.loading">
            <td colspan="6" class="text-center">Đang tải...</td>
          </tr>

          <tr v-else-if="!store.items.length">
            <td colspan="6" class="text-center">Không có dữ liệu</td>
          </tr>

          <tr v-for="item in store.items" :key="item.ID">

            <!-- NAME -->
            <td class="name" @click="goDetail(item.ID)">
              {{ item.TicketName }}
            </td>

            <!-- PRICE -->
            <td>{{ formatPrice(item.Price) }}</td>

            <!-- PARK -->
            <td>
              {{ item.park?.ParkName || item.Park?.ParkName || "-" }}
            </td>

            <!-- TYPE -->
            <td>{{ getType(item.TicketType) }}</td>

            <!-- STATUS -->
            <td>
              <span
                class="badge"
                :class="item.Status == 1 ? 'active' : 'inactive'"
              >
                {{ item.Status == 1 ? "Hoạt động" : "Ngừng hoạt động" }}
              </span>
            </td>

            <!-- ACTION -->
            <td>

              <div class="action-group">

                <!-- ADMIN -->
                <template v-if="isAdmin">

                  <button
                    class="btn-detail"
                    @click="goDetail(item.ID)"
                  >
                    Chi tiết
                  </button>

                  <button
                    class="btn-edit"
                    @click="goEdit(item.ID)"
                  >
                    Sửa
                  </button>

                  <button
                    class="btn-delete"
                    @click="handleDelete(item.ID)"
                  >
                    Xóa
                  </button>

                </template>

                <!-- USER -->
                <template v-else>

                  <button
                    class="btn-detail"
                    @click="goDetail(item.ID)"
                  >
                    Chi tiết
                  </button>

                </template>

              </div>

            </td>

          </tr>
        </tbody>
      </table>
    </div>

    <!-- POPUP DELETE -->
    <div
      v-if="showDeletePopup"
      class="popup-overlay"
    >
      <div class="popup-box">

        <div class="popup-header">
          Xác nhận xóa?
        </div>

        <div class="popup-body">
          Bạn có chắc chắn muốn xóa vé này không?
        </div>

        <div class="popup-footer">

          <button
            class="btn-popup-delete"
            @click="confirmDelete"
          >
            Xóa
          </button>

          <button
            class="btn-popup-cancel"
            @click="closeDeletePopup"
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
import { useRouter } from "vue-router";
import { useTicketStore } from "../provider/store";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { computed } from "vue";
import { useToast } from "vue-toastification";

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

const store = useTicketStore();
const router = useRouter();
const toast = useToast();
const search = ref("");
const type = ref("");
const status = ref(""); 
const showDeletePopup = ref(false);
const deleteId = ref(null);
const loadData = () => {
  store.getList({
    search: search.value,
    type: type.value,
    status: status.value 
  });
};

const goCreate = () => router.push("/tickets/create");
const goEdit = (id) => router.push(`/tickets/edit/${id}`);
const goDetail = (id) => router.push(`/tickets/${id}`);

const handleDelete = (id) => {
  deleteId.value = id;
  showDeletePopup.value = true;
};

const confirmDelete = async () => {

  try {

    await store.delete(deleteId.value);

    toast.success("Xóa vé thành công.");

    showDeletePopup.value = false;

    await loadData();

  } catch (e) {

    toast.error("Xóa vé thất bại.");
  }
};

const closeDeletePopup = () => {
  showDeletePopup.value = false;
  deleteId.value = null;
};

const getType = (type) => {
  return {
    adult: "Người lớn",
    child: "Trẻ em",
    combo: "Combo",
    date: "Theo ngày"
  }[type];
};

const formatPrice = (value) => {
  if (!value) return "0";

  return Number(value).toLocaleString("vi-VN");
};

onMounted(loadData);
</script>

<style scoped>
.tickets-page {
  padding: 20px;
  background: #f4f6f9;
  height: 100%;
  box-sizing: border-box;
}

/* FILTER BOX */
.filter-box {
  display: flex;
  gap: 10px;
  align-items: center;

  background: #fff;
  padding: 15px;
  border-radius: 12px;

  margin-bottom: 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

/* INPUT */
.form-control,
.form-select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  min-width: 200px;
  font-size: 14px;
}

/* BUTTON CREATE */
.btn-create {
  padding: 10px 16px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.btn-create:hover {
  background: #008d4c;
}

/* BUTTON SEARCH */
.btn-search {
  padding: 10px 16px;
  background: #3c8dbc;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-search:hover {
  background: #367fa9;
}

/* TABLE WRAPPER */
.table-wrapper {
  background: #fff;
  border-radius: 16px;
  overflow-x: auto;

  padding: 0 12px; /* 👈 thêm padding 2 bên */

  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;

  table-layout: auto; /* 👈 bỏ fixed để tự co giãn đẹp hơn */
}

/* HEADER */
.table th {
  background: #f8fafc;
  padding: 18px 16px;

  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;

  color: #333;
  text-align: center;
  vertical-align: middle;

  white-space: nowrap;
}

/* BODY */
.table td {
  padding: 18px 16px;

  border-bottom: 1px solid #f0f0f0;

  color: #333;
  text-align: center;
  vertical-align: middle;
}

/* ROW */
.table tr {
  height: 78px;
}

.table tr:hover {
  background: #f9fbfd;
}

/* NAME */
.name {
  cursor: pointer;
  font-weight: 500;
  color: #3c8dbc;
  text-align: left;

  padding-left: 24px !important;
}

/* BADGE */
.badge {
  display: inline-flex;
  justify-content: center;
  align-items: center;

  min-width: 140px;
  height: 34px;

  padding: 0 14px;

  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
}

.badge.active {
  background: #e6f7ee;
  color: #00a65a;
}

.badge.inactive {
  background: #e8d9ba;
  color: #bb6e16;
}

/* ACTION COLUMN */
.table td:last-child {
  text-align: center;
  white-space: nowrap;

  padding-right: 24px; /* 👈 cách viền phải */
}

/* ACTION GROUP */
.action-group {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
}

/* BUTTON CHUNG */
.btn-detail,
.btn-edit,
.btn-delete {
  padding: 6px 10px;

  border: none;
  border-radius: 6px;

  cursor: pointer;

  font-size: 13px;
  font-weight: 500;

  transition: 0.2s;
}

/* DETAIL */
.btn-detail {
  background: #3c8dbc;
  color: white;
}

.btn-detail:hover {
  background: #367fa9;
}

/* EDIT */
.btn-edit {
  background: #ffc107;
  color: #000;
}

.btn-edit:hover {
  background: #e0a800;
}

/* DELETE */
.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-delete:hover {
  background: #c82333;
}

/* ACTION COLUMN */
.table td:last-child {
  text-align: center;
  white-space: nowrap;
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
