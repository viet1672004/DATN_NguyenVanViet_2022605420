<script setup>
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { useAuthStore } from '@/views/modules/auths/provider/store'
import profileApi from '@/views/modules/profiles/provider/api'

const store = useAuthStore()

// 🔥 load lại user khi F5
onMounted(async () => {
  if (store.token) {
    try {
      const res = await profileApi.me()
      store.setUser(res.data)
    } catch (e) {
      store.logout()
    }
  }
})
</script>

<template>
  <RouterView />
</template>