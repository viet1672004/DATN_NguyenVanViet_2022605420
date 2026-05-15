<template>
  <div class="form-page">

    <!-- HEADER -->
    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>
        <span class="title">Cập nhật booking</span>
      </div>
    </div>

    <div class="action-bar">
      <div class="right">
        <button class="btn-submit" @click="submit" :disabled="loading">
          {{ loading ? 'Đang lưu...' : 'Lưu lại' }}
        </button>
      </div>
    </div>

    <div class="form-card">

      <!-- INFO -->
      <div class="grid-2">
        <div class="form-group">
          <label>Mã booking</label>
          <input v-model="form.BookingCode" disabled />
        </div>

        <div class="form-group">
          <label>Người đặt</label>
          <input :value="form.UserName || form.UserID" disabled />
        </div>

        <div class="form-group">
          <label>Khu vui chơi</label>
          <select :value="form.ParkID" @change="onChangePark">
            <option value="">-- Chọn khu --</option>
            <option v-for="p in parks" :key="p.ID" :value="p.ID">
              {{ p.ParkName }}
            </option>
          </select>
        </div>

        <div class="form-group">
          <label>Thời gian</label>
          <input v-model="form.BookingDateTime" type="datetime-local" />
        </div>

        <div class="form-group">
          <label>Trạng thái</label>
          <select v-model="form.Status" disabled>
            <option :value="0">Chờ thanh toán</option>
            <option :value="1">Đã thanh toán</option>
            <option :value="2">Đã hủy</option>
          </select>
        </div>
      </div>

      <!-- TABLE DETAIL -->
      <h3 class="title_2">Chi tiết vé</h3>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th width="40%">Vé</th>
              <th width="20%">Số lượng</th>
              <th width="25%">Giá</th>
              <th width="15%">Thao tác</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(d, i) in form.details" :key="d.ID || i">

              <!-- SELECT -->
              <td>
                <select v-model="d.TicketID">
                  <option value="">-- Chọn vé --</option>
                  <option
                    v-for="t in tickets"
                    :key="t.ID"
                    :value="t.ID"
                    v-show="String(t.ParkID) === String(form.ParkID)"
                  >
                    {{ t.TicketName }}
                  </option>
                </select>
              </td>

              <!-- QUANTITY -->
              <td>
                <input v-model="d.Quantity" type="number" min="1" />
              </td>

              <!-- PRICE -->
              <td>
                {{ getPrice(d.TicketID) }}
              </td>

              <!-- DELETE -->
              <td>
                <button class="btn-delete" @click="removeDetail(i)">
                  Xóa
                </button>
              </td>

            </tr>

            <tr v-if="!form.details.length">
              <td colspan="4">Chưa có vé</td>
            </tr>

          </tbody>
        </table>
      </div>

      <button class="btn-create" @click="addDetail">
        + Thêm vé
      </button>

    </div>
  </div>
</template>

<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import api from "../provider/api";
import { useRoute, useRouter } from "vue-router";
import { useParkStore } from "../../parks/provider/store";
import { useToast } from "vue-toastification";

const toast = useToast();
const route = useRoute();
const router = useRouter();

const loading = ref(false);
const tickets = ref([]);
const parks = ref([]);
const parkStore = useParkStore();

let isInit = true; // 🔥 chặn reset lần đầu

const form = reactive({
  BookingCode: "",
  UserID: "",
  UserName: "",
  ParkID: "",
  BookingDateTime: "", 
  Status: 1,
  details: []
});

const filteredTickets = computed(() => {
  if (!form.ParkID) return [];

  return tickets.value.filter(
    t => String(t.ParkID) === String(form.ParkID)
  );
});

import { nextTick } from "vue";

const onChangePark = async (e) => {
  const newVal = e.target.value;
  const oldVal = form.ParkID;

  // lần đầu chọn
  if (!oldVal) {
    form.ParkID = newVal;
    return;
  }

  // confirm
  if (!confirm("Đổi khu sẽ reset danh sách vé, tiếp tục?")) {
    // 🔥 giữ nguyên UI tuyệt đối (fix nháy)
    await nextTick();
    e.target.value = oldVal;
    return;
  }

  // ✅ OK → đổi park
  form.ParkID = newVal;

  // 🔥 reset chuẩn như màn Create
  form.details = [];
};

const formatDateTime = (value) => {
  if (!value) return "";

  const d = new Date(value);
  const pad = (n) => n.toString().padStart(2, "0");

  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} `
       + `${pad(d.getHours())}:${pad(d.getMinutes())}:00`;
};


// 🔥 CONVERT DATETIME BACKEND → INPUT
const convertToInputDateTime = (value) => {
  if (!value) return "";

  return value.replace(" ", "T").slice(0, 16);
};


// LOAD DATA
onMounted(async () => {
  try {
    // 🔥 load tickets trước
    const ticketRes = await api.getTickets();
    tickets.value = ticketRes.data.data || ticketRes.data || [];

    // 🔥 load parks
    await parkStore.getList();
    parks.value = parkStore.items;

    // 🔥 load detail
    const res = await api.getDetail(route.params.id);

    Object.assign(form, res.data);

    form.ParkID =
      res.data.ParkID ||
      res.data.park_id ||
      res.data.Park?.ID ||
      "";

    form.BookingDateTime = convertToInputDateTime(res.data.BookingDateTime);

    form.Status = res.data.Status?.value ?? res.data.Status ?? 0;

    // 🔥 set details SAU KHI có tickets
    form.details = (res.data.details || []).map(d => ({
      ID: d.ID,
      TicketID: d.TicketID,
      Quantity: Number(d.Quantity)
    }));

  } catch (e) {
    console.error(e);
    toast.error("Lỗi load dữ liệu.");
  }
});

// ADD
const addDetail = () => {
  form.details.push({
    TicketID: "",
    Quantity: 1
  });
};

// REMOVE
const removeDetail = (i) => {
  form.details.splice(i, 1);
};

// PRICE
const getPrice = (id) => {
  const t = tickets.value.find(x => x.ID === id);
  return t ? formatPrice(t.Price) : "";
};

const formatPrice = (price) => {
  return Number(price || 0).toLocaleString("vi-VN") + " đ";
};


// SUBMIT
const submit = async () => {
  if (loading.value) return;

  try {
    loading.value = true;

    const payload = {
      ParkID: form.ParkID,
      BookingDateTime: formatDateTime(form.BookingDateTime),
      Status: form.Status,
      details: form.details.map(d => ({
        ID: d.ID,
        TicketID: d.TicketID,
        Quantity: Number(d.Quantity)
      }))
    };

    console.log("UPDATE PAYLOAD >>>", payload);

    await api.update(route.params.id, payload);
    toast.success("Cập nhật thành công.");
    router.push("/bookings");

  } catch (e) {
    console.error(e);
    toast.error("Cập nhật thất bại.");
  } finally {
    loading.value = false;
  }
};

const goBack = () => router.push("/bookings");
</script>

<style scoped>
/* giữ nguyên style của bạn */
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
}

.left {
  display: flex;
  align-items: center;
  gap: 10px;
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
  color: black;
}

.form-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 15px;
  color: rgb(33, 32, 32);
}

input,
select {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.table-wrapper {
  margin-top: 10px;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #eee;
}

.table {
  width: 100%;
  border-collapse: collapse;
  color: rgb(35, 33, 33);
}

.table th {
  background: #f8fafc;
  padding: 12px;
  text-align: center;
}

.table td {
  padding: 10px;
  text-align: center;
}

.table select,
.table input {
  width: 100%;
}

.btn-submit {
  padding: 10px 18px;
  background: #00a65a;
  color: white;
  border: none;
  border-radius: 8px;
}

.btn-create {
  margin-top: 10px;
  padding: 8px 14px;
  background: #3c8dbc;
  color: white;
  border-radius: 6px;
  border: none;
}

.btn-delete {
  background: #dc3545;
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  border: none;
}

.title_2 {
  color: rgb(33, 32, 32);
}
</style>