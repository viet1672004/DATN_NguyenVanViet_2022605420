<template>
  <div class="profile-page">

    <div class="card">

      <!-- TAB -->
      <div class="tabs">
        <div 
          class="tab" 
          :class="{ active: activeTab === 'profile' }"
          @click="activeTab = 'profile'"
        >
          Hồ sơ
        </div>

        <div 
          class="tab" 
          :class="{ active: activeTab === 'password' }"
          @click="activeTab = 'password'"
        >
          Mật khẩu
        </div>
      </div>

      <!-- HEADER ACTION -->
      <div class="box-header">
        <button class="btn-save" @click="handleSubmit">💾 Lưu lại</button>
      </div>

      <!-- FORM PROFILE -->
      <form v-if="activeTab === 'profile'" class="box-body">

        <div class="form-group">
          <label>Họ và tên</label>
          <input type="text" v-model="form.name" class="form-control" />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="text" v-model="form.email" class="form-control" />
        </div>

        <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" v-model="form.phone" class="form-control" />
        </div>

      </form>

      <!-- FORM PASSWORD -->
      <form v-else class="box-body">

        <div class="form-group">
          <label>Mật khẩu hiện tại</label>
          <input
            type="password"
            v-model="password.current"
            class="form-control"

          />

          <span v-if="errors.current" class="error">
            {{ errors.current }}
          </span>
        </div>

        <div class="form-group">
          <label>Mật khẩu mới</label>
          <input
            type="password"
            v-model="password.new"
            class="form-control"

          />

          <span v-if="errors.new" class="error">
            {{ errors.new }}
          </span>
        </div>

        <div class="form-group">
          <label>Xác nhận mật khẩu</label>
          <input
            type="password"
            v-model="password.confirm"
            class="form-control"

          />

          <span v-if="errors.confirm" class="error">
            {{ errors.confirm }}
          </span>
        </div>

      </form>

    </div>

  </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch } from "vue";
import { useAuthStore } from "@/views/modules/auths/provider/store";
import { useRouter } from "vue-router";
import profileApi from "@/views/modules/profiles/provider/api";
import { useToast } from "vue-toastification";

const toast = useToast();
const store = useAuthStore();
const router = useRouter();

/* TAB */
const activeTab = ref("profile");

/* PROFILE FORM */
const form = reactive({
  name: "",
  email: "",
  phone: "",
  description: "",
});

/* PASSWORD FORM */
const password = reactive({
  current: "",
  new: "",
  confirm: ""
});

const errors = reactive({
  current: "",
  new: "",
  confirm: ""
});

/* LOAD USER */
onMounted(() => {
  if (store.user) {
    form.name = store.user.Name;
    form.email = store.user.Email;
    form.phone = store.user.Phone;
  }
});

/*
|--------------------------------------------------------------------------
| REALTIME VALIDATE
|--------------------------------------------------------------------------
*/

// CURRENT PASSWORD
watch(() => password.current, (value) => {

  if (!value.trim()) {

    errors.current =
      "Mật khẩu hiện tại không được để trống";

  } else {

    errors.current = "";
  }

});

// NEW PASSWORD
watch(() => password.new, (value) => {

  if (!value.trim()) {

    errors.new =
      "Mật khẩu mới không được để trống";

    return;
  }

  if (
    !/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{6,}$/.test(value)
  ) {

    errors.new =
      "Mật khẩu phải có chữ hoa, số và ký tự đặc biệt";

  } else {

    errors.new = "";
  }

});

// CONFIRM PASSWORD
watch(() => password.confirm, (value) => {

  if (!value.trim()) {

    errors.confirm =
      "Vui lòng xác nhận mật khẩu";

    return;
  }

  if (value !== password.new) {

    errors.confirm =
      "Mật khẩu xác nhận không khớp";

  } else {

    errors.confirm = "";
  }

});

/* UPDATE PROFILE */
const update = async () => {
  try {
    await profileApi.updateProfile(form);

    const res = await profileApi.me(); 
    store.setUser(res.data);

    toast.success("Cập nhật thành công");
  } catch (err) {
    console.error(err.response?.data);
  }
};

const changePassword = async () => {

  /*
  |--------------------------------------------------------------------------
  | RESET ERRORS
  |--------------------------------------------------------------------------
  */

  Object.keys(errors).forEach(key => {
    errors[key] = "";
  });

  let valid = true;

  /*
  |--------------------------------------------------------------------------
  | CURRENT PASSWORD
  |--------------------------------------------------------------------------
  */

  if (!password.current.trim()) {

    errors.current =
      "Mật khẩu hiện tại không được để trống";

    valid = false;
  }

  /*
  |--------------------------------------------------------------------------
  | NEW PASSWORD
  |--------------------------------------------------------------------------
  */

  if (!password.new.trim()) {

    errors.new =
      "Mật khẩu mới không được để trống";

    valid = false;

  } else if (
    !/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{6,}$/.test(password.new)
  ) {

    errors.new =
      "Mật khẩu phải có chữ hoa, số và ký tự đặc biệt";

    valid = false;
  }

  /*
  |--------------------------------------------------------------------------
  | CONFIRM PASSWORD
  |--------------------------------------------------------------------------
  */

  if (!password.confirm.trim()) {

    errors.confirm =
      "Vui lòng xác nhận mật khẩu";

    valid = false;

  } else if (
    password.new !== password.confirm
  ) {

    errors.confirm =
      "Mật khẩu xác nhận không khớp";

    valid = false;
  }

  /*
  |--------------------------------------------------------------------------
  | VALIDATE FAILED
  |--------------------------------------------------------------------------
  */

  if (!valid) {

    toast.error(
      "Vui lòng kiểm tra thông tin"
    );

    return;
  }

  /*
  |--------------------------------------------------------------------------
  | CHANGE PASSWORD
  |--------------------------------------------------------------------------
  */

  try {

    await profileApi.changePassword(password);

    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    toast.success(
      "Đổi mật khẩu thành công, vui lòng đăng nhập lại"
    );

    /*
    |--------------------------------------------------------------------------
    | RESET ERRORS
    |--------------------------------------------------------------------------
    */

    Object.keys(errors).forEach(key => {
      errors[key] = "";
    });

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    setTimeout(async () => {

      await store.logout();

      router.replace("/login");

    }, 1500);

  } catch (err) {

    console.error(
      err.response?.data
    );

    toast.error(

      err.response?.data?.message ||

      "Đổi mật khẩu thất bại"

    );
  }
};

/* HANDLE SUBMIT */
const handleSubmit = () => {
  if (activeTab.value === "profile") {
    update();
  } else {
    changePassword();
  }
};

/* LOGOUT (giữ nguyên) */
const logout = async () => {
  await store.logout();
  router.replace("/login");
};
</script>

<style scoped>
.profile-page {
  background: #f4f6f9;
  padding: 20px;
}

/* card */
.card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
}

/* tabs */
.tabs {
  display: flex;
  border-bottom: 1px solid #ddd;
}

.tab {
  padding: 10px 20px;
  cursor: pointer;
  color: #000;
  background: #fff;
}

.tab.active {
  border-bottom: 3px solid #3c8dbc;
  font-weight: 700;
}

/* header */
.box-header {
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
}

/* button */
.btn-save {
  background: #f4f4f4;
  border: 1px solid #ccc;
  padding: 6px 12px;
  cursor: pointer;
}

/* form */
.box-body {
  padding: 15px;

  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 15px;
}

/* full width */
.full {
  grid-column: span 2;
}

/* input */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 400;
  margin-bottom: 5px;
  color: #000;
}

.form-control {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  color: #000;
  background: #fff;
}
.btn-save {
  background: #3c8dbc;
  color: white;
  border: none;
  padding: 8px 16px;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.2s ease;
}

/* hover nổi lên */
.btn-save:hover {
  background: #367fa9;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  transform: translateY(-1px);
}

/* click */
.btn-save:active {
  transform: scale(0.96);
}

.error {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
}

.error-input {
  border: 1px solid #ef4444 !important;
}
</style>npm run 