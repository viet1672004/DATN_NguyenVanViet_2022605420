<template>
  <div class="detail-page">

    <!-- HEADER -->
    <div class="action-bar">

      <div class="left">

        <span class="back-icon" @click="router.back()">
          <i class="arrow"></i>
        </span>

        <span class="title">
          Chi tiết người dùng
        </span>

      </div>

    </div>

    <!-- ACTION -->
    <div
      v-if="isAdmin && user"
      class="action-bar"
    >
      <div class="action-box">

        <button
          class="btn-toggle"
          :class="Number(user.Status) === 0 ? 'btn-unblock' : 'btn-block'"
          @click="handleToggleBlock"
        >
          {{
            Number(user.Status) === 0
              ? 'Mở khóa tài khoản'
              : 'Khóa tài khoản'
          }}
        </button>

      </div>
    </div>

    <!-- LOADING -->
    <div v-if="store.loading" class="loading">
      Đang tải...
    </div>

    <!-- DETAIL -->
    <div v-else-if="user">

      <!-- INFO -->
      <div class="info-card">

        <div class="avatar">
          {{ initials }}
        </div>

        <div class="info-body">

          <h2>{{ user.Name }}</h2>

          <p class="info-text">
            <b class="label">Email:</b>
            {{ user.Email || "—" }}
          </p>

          <p class="info-text">
            <b class="label">Số điện thoại:</b>
            {{ user.Phone || "—" }}
          </p>

          <p class="info-text">
            <b class="label">Vai trò:</b>

            <span
              class="badge"
              :class="
                user.role?.RoleName?.toLowerCase() === 'admin'
                  ? 'role-admin'
                  : 'role-customer'
              "
            >
              {{
                user.role?.RoleName?.toLowerCase() === "admin"
                  ? "Quản trị"
                  : "Người dùng"
              }}
            </span>
          </p>

          <p class="info-text">
            <b class="label">Trạng thái:</b>

            <span
              class="badge"
              :class="Number(user.Status) === 0 ? 'blocked' : 'active'"
            >
              {{
                Number(user.Status) === 0
                  ? "Đã khóa"
                  : "Hoạt động"
              }}
            </span>
          </p>

          <p class="info-text">
            <b class="label">Ngày tạo:</b>
            {{ formatDate(user.created_at) }}
          </p>

        </div>

      </div>

      <!-- BOOKING -->
      <div
        class="desc-box"
        v-if="user.bookings?.length"
      >

        <h3 class="section-title">
          📋 Lịch sử booking
        </h3>

        <table class="table">

          <thead>
            <tr>
              <th>Mã booking</th>
              <th>Khu vui chơi</th>
              <th>Thời gian đặt</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
            </tr>
          </thead>

          <tbody>

            <tr
              v-for="b in user.bookings"
              :key="b.ID"
            >

              <td>{{ b.BookingCode }}</td>

              <td>
                {{ b.park?.ParkName || "—" }}
              </td>

              <td>
                {{ formatDateTime(b.BookingDateTime) }}
              </td>

              <td>
                {{ formatPrice(b.TotalPrice) }}
              </td>

              <td>

                <span
                  class="badge"
                  :class="bookingStatusClass(b.Status)"
                >
                  {{ formatBookingStatus(b.Status) }}
                </span>

              </td>

            </tr>

          </tbody>

        </table>

      </div>

      <!-- EMPTY -->
      <div
        v-else
        class="desc-box empty-box"
      >
        Người dùng chưa có booking nào.
      </div>

    </div>

    <!-- NOT FOUND -->
    <div
      v-else
      class="loading"
    >
      Không tìm thấy người dùng.
    </div>

    <!-- POPUP BLOCK -->
    <div
      v-if="showTogglePopup"
      class="popup-overlay"
    >
      <div class="popup-box">

        <div class="popup-header">
          {{
            Number(user?.Status) === 0
              ? "Xác nhận mở khóa?"
              : "Xác nhận khóa?"
          }}
        </div>

        <div class="popup-body">
          {{
            Number(user?.Status) === 0
              ? "Bạn có chắc chắn muốn mở khóa tài khoản này không?"
              : "Bạn có chắc chắn muốn khóa tài khoản này không?"
          }}
        </div>

        <div class="popup-footer">

          <button
            class="btn-popup-delete"
            :class="
              Number(user?.Status) === 0
                ? 'btn-popup-success'
                : ''
            "
            @click="confirmToggleBlock"
          >
            {{
              Number(user?.Status) === 0
                ? "Mở khóa"
                : "Khóa"
            }}
          </button>

          <button
            class="btn-popup-cancel"
            @click="closeTogglePopup"
          >
            Đóng
          </button>

        </div>

      </div>
    </div>

  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useUserStore } from "../provider/store";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { useToast } from "vue-toastification";

const toast = useToast();
const route = useRoute();
const router = useRouter();

const store = useUserStore();
const authStore = useAuthStore();

const isAdmin = computed(() => {
  return (
    authStore.user?.role?.RoleName?.toLowerCase() === "admin"
  );
});

const user = computed(() => store.detail);

const showTogglePopup = ref(false);

const initials = computed(() => {
  const name = user.value?.Name || "";

  return name
    .split(" ")
    .map(w => w[0])
    .slice(-2)
    .join("")
    .toUpperCase();
});

const formatDate = (value) => {

  if (!value) return "—";

  const date = new Date(value);

  const pad = (n) => String(n).padStart(2, "0");

  const hours = pad(date.getHours());
  const minutes = pad(date.getMinutes());

  const day = pad(date.getDate());
  const month = pad(date.getMonth() + 1);
  const year = date.getFullYear();

  return `${day}/${month}/${year} - ${hours}:${minutes}`;
};

const formatDateTime = (value) => {

  if (!value) return "—";

  const date = new Date(value);

  const pad = (n) => String(n).padStart(2, "0");

  const hours = pad(date.getHours());
  const minutes = pad(date.getMinutes());

  const day = pad(date.getDate());
  const month = pad(date.getMonth() + 1);
  const year = date.getFullYear();

  return `${day}/${month}/${year} - ${hours}:${minutes}`;
};

const formatPrice = (value) => {
  return Number(value || 0).toLocaleString("vi-VN") + " đ";
};

const bookingStatusClass = (status) => {

  switch (Number(status)) {

    case 0:
      return "pending";

    case 1:
      return "active";

    case 2:
      return "blocked";

    default:
      return "pending";
  }
};

const formatBookingStatus = (status) => {

  switch (Number(status)) {

    case 0:
      return "Chờ thanh toán";

    case 1:
      return "Đã thanh toán";

    case 2:
      return "Đã hủy";

    default:
      return "Đang xử lý";
  }
};

const handleToggleBlock = () => {
  showTogglePopup.value = true;
};

const confirmToggleBlock = async () => {

  const isBlocked = Number(user.value.Status) === 0;

  try {

    await store.toggleBlock(user.value.ID);

    await store.getDetail(route.params.id);

    showTogglePopup.value = false;

    toast.success(
      isBlocked
        ? "Mở khóa tài khoản thành công!"
        : "Khóa tài khoản thành công!"
    );

  } catch (error) {

    console.error(error);

    toast.error(
      isBlocked
        ? "Mở khóa tài khoản thất bại!"
        : "Khóa tài khoản thất bại!"
    );
  }
};

const closeTogglePopup = () => {
  showTogglePopup.value = false;
};

onMounted(() => {
  store.getDetail(route.params.id);
});
</script>

<style scoped>
.detail-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
  box-sizing: border-box;
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

/* LEFT */
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

  transition: 0.2s;
}

.back-icon:hover {
  background: #555;
}

.arrow {
  width: 8px;
  height: 8px;

  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;

  transform: rotate(45deg);
}

/* TITLE */
.title {
  font-size: 22px;
  font-weight: 700;
  color: #454242;
}

/* ACTION */
.action-box {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* BUTTON */
.btn-toggle {
  padding: 10px 16px;

  border: none;
  border-radius: 8px;

  cursor: pointer;

  font-size: 14px;
  font-weight: 600;

  transition: 0.2s;
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

/* INFO CARD */
.info-card {
  display: flex;
  gap: 24px;
  align-items: flex-start;

  background: #fff;
  padding: 24px;

  border-radius: 12px;

  margin-bottom: 20px;

  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* AVATAR */
.avatar {
  width: 80px;
  height: 80px;

  border-radius: 50%;

  background: #3c8dbc;
  color: #fff;

  font-size: 28px;
  font-weight: 700;

  display: flex;
  align-items: center;
  justify-content: center;

  flex-shrink: 0;
}

/* BODY */
.info-body {
  flex: 1;
}

/* NAME */
.info-body h2 {
  font-size: 26px;
  font-weight: 700;

  color: #000;

  margin-bottom: 18px;
}

/* TEXT */
.info-text {
  margin-bottom: 12px;

  font-size: 15px;
  color: #000;

  line-height: 1.7;
}

/* LABEL */
.label {
  font-weight: 700;
  color: #000;
}

/* BADGE */
.badge {
  display: inline-flex;
  justify-content: center;
  align-items: center;

  min-width: 120px;
  height: 30px;

  padding: 0 12px;

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

.pending {
  background: #fff3cd;
  color: #856404;
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

/* BOX */
.desc-box {
  background: #fff;
  padding: 20px;

  border-radius: 12px;

  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* EMPTY */
.empty-box {
  text-align: center;
  color: #666;
  font-size: 15px;
}

/* SECTION */
.section-title {
  font-size: 18px;
  font-weight: 700;

  color: #111827;

  margin-bottom: 16px;
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;
}

/* HEAD */
.table th {
  background: #f8fafc;

  padding: 12px;

  font-weight: 600;

  border-bottom: 1px solid #e5e7eb;

  text-align: center;
  color: #333;
}

/* BODY */
.table td {
  padding: 12px;

  border-bottom: 1px solid #f0f0f0;

  text-align: center;
  color: #333;
}

/* HOVER */
.table tbody tr:hover {
  background: #f9fbfd;
}

/* LOADING */
.loading {
  padding: 30px;
  text-align: center;

  font-size: 16px;
  color: #666;
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