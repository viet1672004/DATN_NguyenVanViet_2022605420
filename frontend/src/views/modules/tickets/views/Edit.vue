<template>
  <div class="form-page">

    <!-- HEADER -->
    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>
        <span class="title">Cập nhật vé</span>
      </div>
    </div>

    <!-- SAVE -->
    <div class="action-bar">
      <div class="right">
        <button class="btn-submit" @click="handleSubmit">
          Lưu lại
        </button>
      </div>
    </div>

    <!-- FORM -->
    <div class="form-card">

      <!-- ROW 1 -->
      <div class="grid-2">
        <div class="form-group">
          <label>Tên vé</label>
          <input v-model="form.TicketName" placeholder="Nhập tên vé"/>
        </div>

        <div class="form-group">
          <label>Giá</label>
          <input
            :value="formatPrice(form.Price)"
            @input="handlePriceInput"
            @keypress="onlyNumber"
            placeholder="Nhập giá"
          />
        </div>
      </div>

      <!-- ROW 2 -->
      <div class="grid-2">

        <!-- ✅ PARK -->
        <div class="form-group">
          <label>Khu vui chơi</label>
          <select v-model="form.ParkID">
            <option value="">-- Chọn khu vui chơi --</option>
            <option
              v-for="item in parks"
              :key="item.ID"
              :value="item.ID"
            >
              {{ item.ParkName }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label>Loại vé</label>
          <select v-model="form.TicketType">
            <option value="adult">Người lớn</option>
            <option value="child">Trẻ em</option>
            <option value="combo">Combo</option>
            <option value="date">Theo ngày</option>
          </select>
        </div>

      </div>

      <!-- STATUS -->
      <div class="grid-2">
        <div class="form-group">
          <label>Trạng thái</label>
          <select v-model="form.Status">
            <option :value="1">Hoạt động</option>
            <option :value="0">Ngừng hoạt động</option>
          </select>
        </div>

        <!-- DATE -->
        <div
          class="form-group"
          v-if="form.TicketType === 'date'"
        >
          <label>Số ngày</label>
          <input v-model="form.NumberOfDays" type="number" placeholder="Nhập số ngày"/>
        </div>

        <!-- COMBO -->
        <div
          class="form-group"
          v-if="form.TicketType === 'combo'"
        >
          <label>Thông tin combo</label>
          <input v-model="form.ComboInfo" placeholder="Nhập thông tin gói combo"/>
        </div>
      </div>

      <!-- DESCRIPTION -->
      <div class="form-group">
        <label>Mô tả</label>
        <textarea v-model="form.Description"></textarea>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTicketStore } from "../provider/store";
import parkApi from "../../parks/provider/api"; 

const store = useTicketStore();
const route = useRoute();
const router = useRouter();

const parks = ref([]);

const form = ref({
  TicketName: "",
  Price: "",
  ParkID: "",
  TicketType: "adult",
  NumberOfDays: "",
  ComboInfo: "",
  Description: "",
  Status: 1
});

// ================= FORMAT PRICE =================
const formatPrice = (value) => {
  if (!value) return "";
  return Number(value).toLocaleString("vi-VN");
};

// ❗ CHỈ NHẬP SỐ
const onlyNumber = (e) => {
  const char = String.fromCharCode(e.which);
  if (!/[0-9]/.test(char)) {
    e.preventDefault();
  }
};

// xử lý nhập giá
const handlePriceInput = (e) => {
  let raw = e.target.value.replace(/\./g, "");

  if (!raw) {
    form.value.Price = "";
    return;
  }

  raw = raw.replace(/\D/g, "");
  form.value.Price = raw;

  e.target.value = formatPrice(raw);
};

// ================= LOAD PARK =================
const loadParks = async () => {
  try {
    const res = await parkApi.getList();
    parks.value = res.data.items || [];
  } catch (e) {
    console.error("LOAD PARK ERROR:", e);
  }
};

// ================= LOAD DETAIL =================
const loadDetail = async () => {
  await store.getDetail(route.params.id);

  const data = store.detail;
  console.log("DATA BIND:", data);

  if (!data) return;

  form.value = {
    TicketName: data.TicketName || "",
    Price: data.Price || "",
    ParkID: data.ParkID || "",
    TicketType: data.TicketType || "adult",
    NumberOfDays: data.NumberOfDays || "",
    ComboInfo: data.ComboInfo || "",
    Description: data.Description || "",
    Status: data.Status ?? 1
  };
};

// ================= SUBMIT =================
const handleSubmit = async () => {
  // 🔥 VALIDATE CHUNG
  if (!form.value.TicketName?.trim()) {
    alert("Vui lòng nhập tên vé");
    return;
  }

  if (!form.value.Price) {
    alert("Vui lòng nhập giá");
    return;
  }

  if (!form.value.ParkID) {
    alert("Vui lòng chọn khu vui chơi");
    return;
  }

  // 🔥 VALIDATE THEO TYPE
  if (form.value.TicketType === "date") {
    if (!form.value.NumberOfDays || form.value.NumberOfDays <= 0) {
      alert("Vui lòng nhập số ngày hợp lệ");
      return;
    }
  }

  if (form.value.TicketType === "combo") {
    if (!form.value.ComboInfo?.trim()) {
      alert("Vui lòng nhập thông tin combo");
      return;
    }
  }

  // 🔥 CLEAN DATA
  const payload = { ...form.value };

  if (payload.TicketType !== "date") {
    payload.NumberOfDays = null;
  }

  if (payload.TicketType !== "combo") {
    payload.ComboInfo = null;
  }

  try {
    await store.update(route.params.id, payload);
    router.push("/tickets");
  } catch (e) {
    alert(e);
  }
};

const goBack = () => router.push("/tickets");

onMounted(async () => {
  await loadParks();
  await loadDetail();
});
</script>

<style scoped>
.form-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;

  background: #fff;
  padding: 15px 20px;
  border-radius: 12px;

  margin-bottom: 20px;

  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.form-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 6px;
  font-size: 14px;
  color: #555;
  font-weight: 500;
}

input,
textarea,
select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  font-size: 14px;
}

textarea {
  min-height: 140px;
}

.btn-submit {
  padding: 10px 18px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-submit:hover {
  background: #008d4c;
}

.back-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #333;

  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.arrow {
  width: 8px;
  height: 8px;
  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;
  transform: rotate(45deg);
}

.title {
  font-size: 22px;
  font-weight: 700;
  color: #454242;
}

.left {
  display: flex;
  align-items: center;
  gap: 10px;
}
</style>
