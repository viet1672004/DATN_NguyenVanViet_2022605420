<template>
  <div class="result success">

    <h2>🎉 Thanh toán thành công</h2>

    <p>
      Booking của bạn đã được thanh toán thành công.
    </p>

    <div class="actions">

      <router-link
        to="/bookings"
        class="btn"
      >
        Xem booking
      </router-link>

      <router-link
        v-if="isAdmin"
        to="/payments"
        class="btn secondary"
      >
        Lịch sử thanh toán
      </router-link>

    </div>

  </div>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/views/modules/auths/provider/store";

const authStore = useAuthStore();

const isAdmin = computed(() => {
  return (
    authStore.user?.role?.RoleName?.toLowerCase() === "admin"
  );
});
</script>

<style scoped>
.result {
  text-align: center;
  margin-top: 120px;
}

.success {
  color: #16a34a;
}

.actions {
  margin-top: 30px;

  display: flex;
  justify-content: center;
  gap: 15px;
}

.btn {
  padding: 10px 18px;
  border-radius: 8px;
  background: #3c8dbc;
  color: white;
  text-decoration: none;
}

.btn:hover {
  opacity: 0.9;
}

.secondary {
  background: #6366f1;
}
</style>