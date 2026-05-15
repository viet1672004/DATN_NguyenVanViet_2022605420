<template>
  <div class="parks-page">
    <!-- SEARCH + FILTER -->
    <div class="filter-box">
      <button v-if="isAdmin" class="btn-create" @click="goCreate">
        + Thêm mới
      </button>

      <input
        type="text"
        placeholder="Tìm theo tên, địa điểm"
        class="form-control"
        @keyup.enter="loadData"
      />

      <select v-model="status" class="form-select">
        <option value="1">Hoạt động</option>
        <option value="0">Ngừng hoạt động</option>
        <option value="">Tất cả trạng thái</option>
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
            <th width="100">Ảnh</th>
            <th width="250">Tên khu vui chơi</th>
            <th width="200">Địa điểm</th>
            <th width="180">Giờ hoạt động</th>
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
            
            <!-- IMAGE -->
            <td>
                <img
                    :src="getImage(item.Image)"
                    class="thumb"
                    @error="handleError"
                />
            </td>

            <!-- NAME -->
            <td class="name" @click="goDetail(item.ID)">
              {{ item.ParkName }}
            </td>

            <!-- LOCATION -->
            <td>{{ item.Location }}</td>

            <!-- TIME -->
            <td>
              {{ item.OpenTime?.slice(0,5) }} - {{ item.CloseTime?.slice(0,5) }}
            </td>

            <!-- STATUS -->
            <td>
              <span
                class="badge"
                :class="Number(item.Status) === 1 ? 'active' : 'inactive'"
                >
                {{ Number(item.Status) === 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}
            </span>
            </td>

            <!-- ACTION -->
            <td>

              <div class="action-group">

                <template v-if="isAdmin">

                  <button
                    class="btn-detail"
                    @click="goDetail(item.ID)"
                  >
                    Chi tiết
                  </button>

                  <button
                    class="btn-edit"
                    @click="goEdit(item)"
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
          Bạn có chắc chắn muốn xóa khu vui chơi này không?
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
import { ref, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { useParkStore } from "../provider/store";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { computed } from "vue";
import { useToast } from "vue-toastification";

const router = useRouter();
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
const store = useParkStore();

const search = ref("");
const status = ref("1");
const showDeletePopup = ref(false);
const deleteId = ref(null);
const getImage = (path) => {
  if (!path) return "";

  if (path.startsWith("http")) return path; // 👈 FIX

  return "http://localhost:8000" + path;
};

const handleError = (e) => {
  e.target.src = "/images/default.jpg";
};
const loadData = () => {
    store.getList({
        search: search.value,
        status: status.value === "" ? null : Number(status.value) 
  });
};

const goCreate = () => {
    router.push("/parks/create");
};

const goEdit = (item) => {
  console.log("ITEM:", item);
  console.log("EDIT ID:", item.ID);

  router.push(`/parks/edit/${item.ID}`);
};

const goDetail = (id) => {
    router.push(`/parks/${id}`);
};

const handleDelete = (id) => {
  deleteId.value = id;
  showDeletePopup.value = true;
};

const confirmDelete = async () => {

  try {

    await store.delete(deleteId.value);

    toast.success("Xóa khu vui chơi thành công.");

    showDeletePopup.value = false;

    await loadData();

  } catch (e) {

    toast.error("Xóa khu vui chơi thất bại.");
  }
};

const closeDeletePopup = () => {
  showDeletePopup.value = false;
  deleteId.value = null;
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
.parks-page {
  padding: 20px;
  background: #f4f6f9;
  height: 100%;
  box-sizing: border-box;
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
  padding: 0 12px;

  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;

  table-layout: auto;
}

/* HEADER TABLE */
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

/* HOVER */
.table tr:hover {
  background: #f9fbfd;
}

/* FIX ROW HEIGHT */
.table tr {
  height: 78px;
}

/* IMAGE */
.thumb {
  width: 78px;
  height: 56px;

  object-fit: cover;
  border-radius: 8px;

  display: block;
  margin: 0 auto;
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

  padding-right: 24px;
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

/* ACTION CENTER */
.table td:last-child {
  text-align: center;
   white-space: nowrap;
}

/* FIX ROW HEIGHT ĐỀU */
.table tr {
  height: 70px;
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
