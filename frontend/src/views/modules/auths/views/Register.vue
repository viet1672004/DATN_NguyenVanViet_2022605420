<template>
  <div class="register-page">

    <div class="register-box">

      <!-- LOGO -->
      <img
        class="logo"
        src="@/assets/logo2.png"
        alt="logo"
      />

      <!-- FORM -->
      <form @submit.prevent="submit">

        <!-- NAME -->
        <input
          v-model="form.name"
          type="text"
          placeholder="Họ và tên"
        />

        <!-- PHONE -->
        <input
          v-model="form.phone"
          type="text"
          placeholder="Số điện thoại"
        />

        <!-- EMAIL -->
        <input
          v-model="form.email"
          type="email"
          placeholder="Email"
        />

        <!-- PASSWORD -->
        <input
          v-model="form.password"
          type="password"
          placeholder="Mật khẩu"
        />

        <!-- CONFIRM -->
        <input
          v-model="form.password_confirmation"
          type="password"
          placeholder="Nhập lại mật khẩu"
        />

        <!-- BUTTON -->
        <button
          type="submit"
          :disabled="loading"
        >
          {{ loading ? "Đang đăng ký..." : "Đăng ký" }}
        </button>

      </form>

      <!-- LOGIN -->
      <p class="login-text">
        Đã có tài khoản?
        <span @click="goLogin">
          Đăng nhập
        </span>
      </p>

    </div>

  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useAuthStore } from "../provider/store";
import { useRouter } from "vue-router";

import { useToast } from "vue-toastification";

const toast = useToast();

const store = useAuthStore();
const router = useRouter();

const form = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

const loading = ref(false);

const submit = async () => {

  /*
  |--------------------------------------------------------------------------
  | VALIDATE
  |--------------------------------------------------------------------------
  */

  if (!form.name.trim()) {
    toast.error("Họ và tên không được để trống");
    return;
  }

  if (!form.phone.trim()) {
    toast.error("Số điện thoại không được để trống");
    return;
  }

  if (!form.email.trim()) {
    toast.error("Email không được để trống");
    return;
  }

  // EMAIL FORMAT
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!emailRegex.test(form.email)) {
    toast.error("Email không đúng định dạng");
    return;
  }

  if (!form.password.trim()) {
    toast.error("Mật khẩu không được để trống");
    return;
  }

  if (form.password.length < 6) {
    toast.error("Mật khẩu tối thiểu 6 ký tự");
    return;
  }

  if (!form.password_confirmation.trim()) {
    toast.error("Vui lòng nhập lại mật khẩu");
    return;
  }

  if (form.password !== form.password_confirmation) {
    toast.error("Mật khẩu nhập lại không khớp");
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | REGISTER
  |--------------------------------------------------------------------------
  */

  try {

    loading.value = true;

    await store.register(form);

    toast.success("Đăng ký thành công");

    router.replace("/dashboard");

  } catch (err) {

    toast.error(err || "Đăng ký thất bại");

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

  background-image: url("@/assets/bg-login.jpg");
  background-size: cover;
  background-position: center;
}

/* BOX */
.register-box {
  width: 420px;

  padding: 40px;

  border-radius: 16px;

  background: rgba(255,255,255,0.55);

  backdrop-filter: blur(8px);

  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* LOGO */
.logo {
  width: 120px;
  display: block;
  margin: 0 auto 30px;
}

/* INPUT */
input {
  width: 100%;

  height: 52px;

  margin-bottom: 16px;

  padding: 0 16px;

  border: none;
  border-radius: 10px;

  outline: none;

  background: rgba(255,255,255,0.8);

  font-size: 15px;

  box-sizing: border-box;
}

/* BUTTON */
button {
  width: 100%;

  height: 52px;

  border: none;
  border-radius: 10px;

  background: #3c8dbc;

  color: white;

  font-size: 16px;
  font-weight: 600;

  cursor: pointer;

  transition: 0.2s;
}

button:hover {
  background: #367fa9;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* LOGIN */
.login-text {
  margin-top: 20px;
  text-align: center;
  color: white;
}

.login-text span {
  color: #0d6efd;
  cursor: pointer;
  font-weight: 600;
}
</style>