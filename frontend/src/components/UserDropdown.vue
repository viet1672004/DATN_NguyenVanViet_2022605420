<template>
  <div class="user-wrapper" @click="toggleMenu">
    <!-- TOP USER -->
    <div class="user-box">
      <img 
        src="https://cdn-icons-png.flaticon.com/512/149/149071.png" 
        class="avatar"
      />
      <span v-if="store.user">
        {{ store.user.Name }}
      </span>
    </div>

    <!-- DROPDOWN -->
    <div v-if="showMenu" class="user-dropdown">
      <div class="user-header">
        <img 
          src="https://cdn-icons-png.flaticon.com/512/149/149071.png" 
          class="avatar-large"
        />
        <p>{{ store.user?.Name }}</p>
      </div>

      <div class="user-footer">
        <button class="profile-btn" @click.stop="goProfile">
          Hồ sơ
        </button>

        <button class="logout-btn" @click.stop="logout">
          Đăng xuất
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/views/modules/auths/provider/store";

const router = useRouter();
const store = useAuthStore();

const showMenu = ref(false);

const toggleMenu = () => {
  showMenu.value = !showMenu.value;
};

const goProfile = () => {
    showMenu.value = false;
    router.push("/profile");
};

const logout = async () => {
    showMenu.value = false;
    await store.logout();
    router.push("/login");
};

/* click ra ngoài thì đóng */
const handleClickOutside = (e) => {
  if (!e.target.closest(".user-wrapper")) {
    showMenu.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
.user-wrapper {
  position: relative;

  display: flex;
  align-items: center;

  z-index: 99999;
}

/* top user */
.user-box {
  display: flex;
  align-items: center;

  gap: 10px;

  padding: 8px 12px;

  cursor: pointer;

  color: white;
}

/* dropdown */
.user-dropdown {
  position: absolute;

  right: -17px;
  top: 100%;

  margin-top: 7px;

  width: 260px;

  background: white;

  border: 1px solid #ddd;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.2);

  border-radius: 10px;

  overflow: hidden;

  z-index: 100000;
}

/* topbar */
.topbar > div:last-child {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

/* header */
.user-header {
  background: #3c8dbc;

  color: white;

  text-align: center;

  padding: 20px;
}

.avatar-large {
  width: 80px;
  height: 80px;

  border-radius: 50%;

  margin-bottom: 10px;
}

/* footer */
.user-footer {
  display: flex;

  justify-content: space-between;

  gap: 10px;

  padding: 14px;
}

.user-footer button {
  flex: 1;

  padding: 10px 14px;

  border: none;

  border-radius: 10px;

  font-weight: 600;

  cursor: pointer;

  transition: all 0.25s ease;
  
}

/* hồ sơ */
.profile-btn {
  background: linear-gradient(135deg, #3c8dbc, #5dade2);

  color: white;
}

.profile-btn:hover {
  transform: translateY(-2px);

  box-shadow: 0 4px 10px rgba(60, 141, 188, 0.35);
}

/* đăng xuất */
.logout-btn {
  background: linear-gradient(135deg, #e74c3c, #ff6b6b);

  color: white;
}

.logout-btn:hover {
  transform: translateY(-2px);

  box-shadow: 0 4px 10px rgba(231, 76, 60, 0.35);
}

/* avatar nhỏ */
.avatar {
  width: 32px;
  height: 32px;

  border-radius: 50%;
}
</style>