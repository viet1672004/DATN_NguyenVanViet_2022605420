<template>
  <div class="result fail">
    <h2>❌ Thanh toán thất bại</h2>
    <router-link to="/payments">Quay lại</router-link>
  </div>
</template>

<style scoped>
.result {
  text-align: center;
  margin-top: 100px;
}
.fail {
  color: red;
}
</style>