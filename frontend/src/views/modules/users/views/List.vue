<template>
  <div class="users-page">

    <!-- FILTER -->
    <div class="filter-box">
      <input
        v-model="search"
        type="text"
        placeholder="🔍 Tìm theo tên, email"
        class="form-control"
        @keyup.enter="loadData"
      />

      <select v-model="role" class="form-select">
        <option value="">Tất cả vai trò</option>
        <option value="admin">Quản trị viên</option>
        <option value="customer">Người dùng</option>
      </select>

      <select v-model="blocked" class="form-select">
        <option value="">Tất cả trạng thái</option>
        <option value="0">Hoạt động</option>
        <option value="1">Đã khóa</option>
      </select>

      <button class="btn-search" @click="loadData">Tìm kiếm</button>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th width="200">Thao tác</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="store.loading">
            <td colspan="7" class="text-center">Đang tải...</td>
          </tr>

          <tr v-else-if="!store.items.length">
            <td colspan="7" class="text-center">Không có dữ liệu</td>
          </tr>

          <tr v-for="(item, idx) in store.items" :key="item.ID">

            <td class="name" @click="goDetail(item.ID)">
              {{ item.Name }}
            </td>

            <td>{{ item.Email }}</td>
            <td>{{ item.Phone || '—' }}</td>

            <td>
              <span
                class="badge"
                :class="item.role?.RoleName?.toLowerCase() === 'admin' ? 'role-admin' : 'role-customer'"
              >
                {{
                  item.role?.RoleName?.toLowerCase() === 'admin'
                    ? 'Quản trị viên'
                    : item.role?.RoleName?.toLowerCase() === 'customer'
                      ? 'Người dùng'
                      : '—'
                }}
              </span>
            </td>

            <td>
              <span
                class="badge"
                :class="Number(item.Status) === 1 ? 'active' : 'blocked'"
              >
                {{ Number(item.Status) === 1 ? 'Hoạt động' : 'Đã khóa' }}
              </span>
            </td>

            <td>

              <div class="action-group">

                <button
                  class="btn-detail"
                  @click="goDetail(item.ID)"
                >
                  Chi tiết
                </button>

                <button
                  v-if="isAdmin"
                  :class="Number(item.Status) === 1 ? 'btn-block' : 'btn-unblock'"
                  @click="handleToggleBlock(item)"
                >
                  {{ Number(item.Status) === 1 ? 'Khóa' : 'Mở khóa' }}
                </button>

              </div>

            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- POPUP -->
    <div
      v-if="showPopup"
      class="popup-overlay"
    >
      <div class="popup-box">

        <div class="popup-header">
          {{ popupTitle }}
        </div>

        <div class="popup-body">
          {{ popupMessage }}
        </div>

        <div class="popup-footer">

          <button
            class="btn-popup-confirm"
            @click="confirmToggle"
          >
            Xác nhận
          </button>

          <button
            class="btn-popup-cancel"
            @click="closePopup"
          >
            Hủy
          </button>

        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useUserStore } from "../provider/store";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { useToast } from "vue-toastification";

const props = defineProps({
  defaultRole:    { type: String,  default: "" },
  defaultBlocked: { type: Number,  default: -1  },
});

const route  = useRoute();
const router = useRouter();
const store  = useUserStore();
const authStore = useAuthStore();
const toast = useToast();

const isAdmin = computed(() =>
  authStore.user?.role?.RoleName?.toLowerCase() === "admin"
);

const search  = ref("");
const role    = ref(props.defaultRole);
const blocked = ref(props.defaultBlocked >= 0 ? String(props.defaultBlocked) : "0");

const showPopup = ref(false);

const popupTitle = ref("");

const popupMessage = ref("");

const selectedUser = ref(null);

watch(
  () => route.path,
  () => {
    search.value  = "";
    role.value    = props.defaultRole;
    blocked.value = props.defaultBlocked >= 0 ? String(props.defaultBlocked) : "";
    loadData();
  }
);

const loadData = async () => {
  await store.getList({
    search:  search.value || undefined,
    role:    role.value || undefined,
    blocked: blocked.value !== "" ? Number(blocked.value) : undefined,
  });
};

const goDetail = (id) => {
  router.push(`/users/${id}`);
};

const handleToggleBlock = (item) => {

  selectedUser.value = item;

  const isActive =
    Number(item.Status) === 1;

  popupTitle.value = isActive
    ? "Xác nhận khóa tài khoản?"
    : "Xác nhận mở khóa tài khoản?";

  popupMessage.value = isActive
    ? `Bạn có chắc muốn khóa tài khoản "${item.Name}" không?`
    : `Bạn có chắc muốn mở khóa tài khoản "${item.Name}" không?`;

  showPopup.value = true;
};

const closePopup = () => {

  showPopup.value = false;
};

const confirmToggle = async () => {

  if (!selectedUser.value) return;

  const isActive =
    Number(selectedUser.value.Status) === 1;

  try {

    await store.toggleBlock(
      selectedUser.value.ID
    );

    toast.success(
      isActive
        ? "Khóa tài khoản thành công!"
        : "Mở khóa tài khoản thành công!"
    );

    closePopup();

    await loadData();

  } catch (error) {

    console.error(error);

    toast.error(
      isActive
        ? "Khóa tài khoản thất bại!"
        : "Mở khóa tài khoản thất bại!"
    );
  }
};

onMounted(async () => {
  await loadData();
});
</script>

<style scoped>
.users-page {
  padding: 20px;
  background: #f4f6f9;
  min-height: 100%;
  box-sizing: border-box;
}

/* FILTER */
.filter-box {
  display: flex;
  gap: 10px;
  align-items: center;

  background: #fff;
  padding: 15px;
  border-radius: 12px;

  margin-bottom: 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);

  flex-wrap: wrap;
}

/* INPUT */
.form-control,
.form-select {
  padding: 10px 12px;

  border-radius: 8px;
  border: 1px solid #dcdfe6;

  min-width: 200px;

  font-size: 14px;
  outline: none;

  transition: 0.2s;
}

.form-control:focus,
.form-select:focus {
  border-color: #3c8dbc;
  box-shadow: 0 0 0 3px rgba(60,141,188,0.1);
}

/* BUTTON SEARCH */
.btn-search {
  padding: 10px 16px;

  background: #3c8dbc;
  color: white;

  border: none;
  border-radius: 8px;

  cursor: pointer;
  font-weight: 500;

  transition: 0.2s;
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
  height: 72px;
}

.table tr:hover {
  background: #f9fbfd;
}

/* NAME */
.name {
  cursor: pointer;

  font-weight: 600;
  color: #3c8dbc;

  text-align: left;

  padding-left: 24px !important;

  transition: 0.2s;
}

.name:hover {
  color: #256d96;
  text-decoration: underline;
}

/* BADGE */
.badge {
  display: inline-flex;

  justify-content: center;
  align-items: center;

  width: 130px;
  height: 34px;

  padding: 0 14px;

  border-radius: 20px;

  font-size: 13px;
  font-weight: 600;
}

/* STATUS */
.active {
  background: #e6f7ee;
  color: #00a65a;
}

.blocked {
  background: #fde8e8;
  color: #dc3545;
}

/* ROLE */
.role-admin {
  background: #fff3cd;
  color: #856404;
}

.role-customer {
  background: #eef2ff;
  color: #4338ca;
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
  gap: 6px;

  min-width: 180px;
}

/* BUTTON CHUNG */
.btn-detail,
.btn-block,
.btn-unblock {
  width: 70px;
  height: 30px;

  border: none;
  border-radius: 6px;

  cursor: pointer;

  font-size: 13px;
  font-weight: 500;

  transition: 0.2s;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  white-space: nowrap;

  box-sizing: border-box;
}

/* DETAIL */
.btn-detail {
  background: #3c8dbc;
  color: white;
}

.btn-detail:hover {
  background: #367fa9;
}

/* BLOCK */
.btn-block {
  background: #dc3545;
  color: white;
}

.btn-block:hover {
  background: #c82333;
}

/* UNBLOCK */
.btn-unblock {
  background: #00a65a;
  color: white;
}

.btn-unblock:hover {
  background: #008d4c;
}

/* EMPTY */
.text-center {
  text-align: center;
  color: #888;
  padding: 30px;
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
.btn-popup-confirm {
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
