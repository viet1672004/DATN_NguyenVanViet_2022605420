<template>
  <div class="register-page">

    <div class="register-box">
      <h2>Register</h2>

      <form @submit.prevent="submit">
        <input v-model="form.name" placeholder="Họ và tên" />
        <input v-model="form.email" placeholder="Email" />
        <input v-model="form.phone" placeholder="Điện thoại" />
        <input v-model="form.password" type="password" placeholder="Mật khẩu" />

        <p v-if="error" style="color:red">{{ error }}</p>

        <button type="submit" :disabled="loading">
          {{ loading ? "Đang đăng ký..." : "Đăng ký" }}
        </button>
      </form>

      <p>
        Đã có tài khoản?
        <span @click="goLogin" style="color:blue; cursor:pointer">
          Login
        </span>
      </p>

    </div>

  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useAuthStore } from "../provider/store";
import { useRouter } from "vue-router";

const store = useAuthStore();
const router = useRouter();

const form = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
});

const loading = ref(false);
const error = ref("");

const submit = async () => {
  error.value = "";

  if (!form.name || !form.email || !form.password) {
    error.value = "Vui lòng nhập đầy đủ thông tin";
    return;
  }

  try {
    loading.value = true;

    await store.register(form);

    // 🔥 đăng ký xong → vào dashboard luôn
    router.replace("/dashboard");

  } catch (err) {
    error.value = err;
  } finally {
    loading.value = false;
  }
};

const goLogin = () => {
  router.push("/login");
};
</script>

<style scoped>
.register-page {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f3f4f6;
}

.register-box {
  width: 400px;
  padding: 30px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

input {
  width: 100%;
  margin: 10px 0;
  padding: 10px;
}
button {
  width: 100%;
  padding: 10px;
}
</style>