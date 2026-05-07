<template>
  <div class="login-page">

    <!-- Title -->
    <div class="login-header">
      <h1>FunTicket | Back Office</h1>
    </div>

    <!-- Box -->
    <div class="login-box">
      <img class="login-logo" src="@/assets/logo2.png" alt="logo" />

      <form class="login-form" @submit.prevent="submit">

      <!-- Register fields -->
      <input v-if="isRegister" v-model="form.name" placeholder="Họ và tên" />
<p v-if="isRegister && errors.name" class="error">
  {{ errors.name }}
</p>

<input v-if="isRegister" v-model="form.phone" placeholder="Số điện thoại" @input="form.phone = form.phone.replace(/[^0-9]/g, '')" />
<p v-if="isRegister && errors.phone" class="error">
  {{ errors.phone }}
</p>

<input v-model="form.email" placeholder="Email" />
<p v-if="errors.email" class="error">{{ errors.email }}</p>

<input v-model="form.password" type="password" placeholder="Mật khẩu" />
<p v-if="errors.password" class="error">{{ errors.password }}</p>

<input
  v-if="isRegister"
  v-model="form.password_confirmation"
  type="password"
  placeholder="Nhập lại mật khẩu"
/>
<p v-if="errors.password_confirmation" class="error">
  {{ errors.password_confirmation }}
</p>
      <!-- Error chung -->
      <p v-if="error" class="error">
        {{ error }}
      </p>

      <!-- Button -->
      <button type="submit" :disabled="loading">
        {{ loading ? "Đang xử lý..." : (isRegister ? "Đăng ký" : "Đăng nhập") }}
      </button>

    </form>
      <!-- Toggle -->
      <p class="switch">
        <span v-if="!isRegister">
          Chưa có tài khoản?
          <b @click="toggleMode">Đăng ký</b>
        </span>

        <span v-else>
          Đã có tài khoản?
          <b @click="toggleMode">Đăng nhập</b>
        </span>
      </p>

    </div>

  </div>
</template>

<script setup>
import { reactive, ref, watch } from "vue";
import { useAuthStore } from "../provider/store";
import { useRouter } from "vue-router";

const store = useAuthStore();
const router = useRouter();

const isRegister = ref(false);
const error = ref("");

const form = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

const loading = ref(false);
const errors = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

// 🔁 đổi mode
const toggleMode = () => {
  isRegister.value = !isRegister.value;
};

// 🔄 reset form khi chuyển mode
watch(isRegister, () => {
  // reset form
  form.name = "";
  form.phone = "";
  form.email = "";
  form.password = "";
  form.password_confirmation = "";

  // 🔥 reset errors
  Object.keys(errors).forEach(k => errors[k] = "");

  error.value = "";
});

const validate = () => {
  let valid = true;

  // reset
  Object.keys(errors).forEach(k => errors[k] = "");

  // name
  if (isRegister.value && !form.name) {
    errors.name = "Vui lòng nhập tên";
    valid = false;
  }

  // email
  if (!form.email) {
    errors.email = "Vui lòng nhập email";
    valid = false;
  } else if (!/^\S+@\S+\.\S+$/.test(form.email)) {
    errors.email = "Email không hợp lệ";
    valid = false;
  }

  // phone (chỉ validate khi register)
  if (isRegister.value) {
    if (!form.phone) {
      errors.phone = "Vui lòng nhập SĐT";
      valid = false;
    } else if (form.phone.length < 8 || form.phone.length > 17) {
      errors.phone = "SĐT không hợp lệ";
      valid = false;
    }
  }

  // password
  if (!form.password) {
    errors.password = "Vui lòng nhập mật khẩu";
    valid = false;
  } else if (form.password.length < 6) {
    errors.password = "Mật khẩu ≥ 6 ký tự";
    valid = false;
  }

  // confirm
  if (isRegister.value) {
    if (!form.password_confirmation) {
      errors.password_confirmation = "Nhập lại mật khẩu";
      valid = false;
    } else if (!form.password_confirmation) {
      error.value = "Vui lòng nhập lại mật khẩu";
      return false;
    } else if (form.password !== form.password_confirmation) {
      errors.password_confirmation = "Không khớp";
      valid = false;
    }
  }

  return valid;
};

// 🚀 submit
const submit = async () => {
  if (!validate()) return;

  try {
    loading.value = true;

    if (isRegister.value) {
      await store.register(form);
    } else {
      await store.login(form);
    }

    router.replace("/dashboard");

  } catch (err) {
    // backend error
    errors.email = err;
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-page {
  position: fixed;
  inset: 0;

  background-image: url('@/assets/bg.jpg');
  background-size: cover;
  background-position: center;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* overlay */
.login-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.1);
  pointer-events: none;
}

/* header */
.login-header {
  position: relative;
  z-index: 1;
  margin-bottom: 20px;
  color: #1f2937;
}

/* box */
.login-box {
  position: relative;
  z-index: 1;

  width: 420px;
  padding: 25px 30px 30px;
  border-radius: 14px;

  /* 🔥 glass effect */
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(12px);

  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  text-align: center;
}

/* logo */
.login-logo {
  width: 160px;
  margin: 0 auto 15px;
  display: block;
}

/* form */
.login-form input {
  width: 100%;
  margin: 8px 0;
  padding: 12px;
  font-size: 13px;

  border: none;
  border-radius: 8px;
  outline: none;

  background: rgba(255,255,255,0.8);
}

/* error */
.error {
  color: red;
  font-size: 12px;
  text-align: left;
  margin: -5px 0 5px;
}

/* button */
.login-form button {
  width: 100%;
  padding: 12px;
  margin-top: 10px;

  border: none;
  border-radius: 6px;
  background: #3c8dbc;
  color: white;

  cursor: pointer;
  transition: 0.2s;
}

.login-form button:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

/* switch */
.switch {
  margin-top: 10px;
  font-size: 14px;
}

.switch b {
  color: #3c8dbc;
  cursor: pointer;
}
</style>