<template>
  <div class="form-page">

    <!-- HEADER -->
    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>
        <span class="title">Thêm vé</span>
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
        <div class="form-group">
          <label>Khu vui chơi</label>
          <select v-model="form.ParkID">
            <option value="">-- Chọn khu vui chơi --</option>
            <option v-for="p in parks" :key="p.ID" :value="p.ID">
              {{ p.ParkName }}
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

      <!-- DATE / COMBO -->
      <div class="grid-2">
        <div class="form-group" v-if="form.TicketType === 'date'">
          <label>Số ngày</label>
          <input v-model="form.NumberOfDays" type="number" placeholder="Nhập số ngày"/>
        </div>

        <div class="form-group" v-if="form.TicketType === 'combo'">
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
import { useRouter } from "vue-router";
import { useTicketStore } from "../provider/store";
import { useParkStore } from "../../parks/provider/store";

const router = useRouter();
const store = useTicketStore();
const parkStore = useParkStore();

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

// chỉ nhập số
const onlyNumber = (e) => {
  const char = String.fromCharCode(e.which);
  if (!/[0-9]/.test(char)) {
    e.preventDefault();
  }
};

// xử lý nhập giá
const handlePriceInput = (e) => {
  let raw = e.target.value.replace(/\./g, "");
  raw = raw.replace(/\D/g, "");

  form.value.Price = raw;
  e.target.value = formatPrice(raw);
};

// ================= LOAD PARK =================
onMounted(async () => {
  await parkStore.getList();
  parks.value = parkStore.items;
});

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

  // 🔥 CLEAN DATA (QUAN TRỌNG)
  const payload = { ...form.value };

  if (payload.TicketType !== "date") {
    payload.NumberOfDays = null;
  }

  if (payload.TicketType !== "combo") {
    payload.ComboInfo = null;
  }

  try {
    await store.create(payload);
    router.push("/tickets");
  } catch (e) {
    alert(e);
  }
};

const goBack = () => router.push("/tickets");
</script>

<style scoped>
.form-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

/* ACTION BAR */
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

/* FORM */
.form-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* GRID */
.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 15px;
}

/* FORM GROUP */
.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 6px;
  font-size: 14px;
  color: #555;
}

/* INPUT */
input,
textarea,
select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
  font-size: 14px;
}

textarea {
  min-height: 120px;
}

/* BUTTON */
button {
  padding: 10px 18px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

button:hover {
  background: #008d4c;
}

/* BACK ICON */
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
  display: block;
  width: 8px;
  height: 8px;
  border-left: 2px solid #fff;
  border-bottom: 2px solid #fff;
  transform: rotate(45deg);
}

.back-icon:hover {
  background: #ddd;
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

/* RESPONSIVE */
@media (max-width: 768px) {
  .grid-2 {
    grid-template-columns: 1fr;
  }

  .action-bar {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}

</style>