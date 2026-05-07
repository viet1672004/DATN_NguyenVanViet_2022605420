<template>
  <div>
    <h1>Trang chủ</h1>

    <h2 v-if="store.user">
      Xin chào {{ store.user.Name }}
    </h2>

    <h2 v-else>
      Chưa đăng nhập
    </h2>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/views/modules/auths/provider/store'

const store = useAuthStore();

onMounted(() => {
  if (store.token && !store.user) {
    store.fetchUser();
  }
});
</script>
