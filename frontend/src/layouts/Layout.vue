<template>
  <div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

      <!-- LOGO -->
      <router-link to="/dashboard" class="logo">
        Back Office
      </router-link>

      <div class="sidebar-menu">

        <!-- PARK -->
        <div 
          class="menu-parent"
          @click="openPark = !openPark"
        >
          <span>🎡 KHU VUI CHƠI</span>
          <span>{{ openPark ? "▾" : "▸" }}</span>
        </div>

        <div
          v-show="openPark"
          class="submenu"
        >
          <router-link to="/parks" class="menu-item">
            Danh sách khu vui chơi
          </router-link>

          <router-link v-if="isAdmin"
            to="/parks/create"
            class="menu-item"
          >
            Thêm khu vui chơi
          </router-link>
        </div>

        <!-- TICKETS -->
        <div
          class="menu-parent"
          @click="openTicket = !openTicket"
        >
          <span>🎫 VÉ</span>
          <span>{{ openTicket ? "▾" : "▸" }}</span>
        </div>

        <div
          v-show="openTicket"
          class="submenu"
        >
          <router-link to="/tickets" class="menu-item">
            Danh sách vé
          </router-link>

          <router-link
            v-if="isAdmin"
            to="/tickets/create"
            class="menu-item"
          >
            Thêm vé
          </router-link>
        </div>

        <!-- BOOKING -->
        <div
          class="menu-parent"
          @click="openBooking = !openBooking"
        >
          <span>🧾 BOOKING</span>
          <span>{{ openBooking ? "▾" : "▸" }}</span>
        </div>

        <div
          v-show="openBooking"
          class="submenu"
        >
          <router-link to="/bookings" class="menu-item">
            Danh sách booking
          </router-link>

          <router-link
            to="/bookings/create"
            class="menu-item"
          >
            Tạo booking
          </router-link>
        </div>

        <!-- PAYMENT -->
        <div
          class="menu-parent"
          @click="openPayment = !openPayment"
        >
          <span>💳 THANH TOÁN</span>
          <span>{{ openPayment ? "▾" : "▸" }}</span>
        </div>

        <div
          v-show="openPayment"
          class="submenu"
        >
          <router-link to="/payments" class="menu-item">
            Danh sách thanh toán
          </router-link>
        </div>

        <!-- PROFILE -->
        <router-link to="/profile" class="menu-item">
          Hồ sơ cá nhân
        </router-link>

      </div>

    </aside>

    <!-- MAIN -->
    <div class="main">

      <header class="topbar">
        <div></div>

        <div>
          <UserDropdown />
        </div>
      </header>

      <div class="content">
        <router-view />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import UserDropdown from "@/components/UserDropdown.vue";

const store = useAuthStore();

/* mở / đóng menu */
const openPark = ref(false);
const openTicket = ref(false);
const openBooking = ref(false);
const openPayment = ref(false);

/* role */
const isAdmin = computed(() => {
  return (
    store.user?.role?.RoleName?.toLowerCase() === "admin"
  );
});

const isCustomer = computed(() => {
  return (
    store.user?.role?.RoleName?.toLowerCase() === "customer"
  );
});

</script>

<style scoped>
.layout {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* SIDEBAR */
.sidebar {
  width: 240px;
  background: #1e282c;
  color: white;
  display: flex;
  flex-direction: column;
  min-width: 240px;
}

/* LOGO */
.logo {
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #367fa9;
  color: white;
  text-decoration: none;
  font-size: 22px;
  font-weight: 700;
}

/* MENU */
.sidebar-menu {
  padding-top: 10px;
}

.menu-parent {
  padding: 14px 18px;
  color: white;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  transition: 0.2s;
}

.menu-parent:hover {
  background: #1e3a46;
  color: white;
}

.submenu {
  background: #22313f;
}

.menu-item {
  display: block;
  padding: 12px 30px;
  color: white;
  text-decoration: none;
  transition: 0.2s;
}

.menu-item:hover {
  background: #1e3a46;
  color: white;
}

.router-link-active {
  color: white;
  border-left: 3px solid #3c8dbc;
}

/* MAIN */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.topbar {
  height: 60px;
  background: #3c8dbc;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
}

.content {
  flex: 1;
  background: #ecf0f5;
  padding: 20px;
  overflow-y: auto;
  box-sizing: border-box;
}
</style>