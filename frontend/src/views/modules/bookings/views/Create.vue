<template>
  <div class="form-page">

    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>
        <span class="title">Tạo booking</span>
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

      <div class="grid-2">

        <!-- KHU -->
        <div class="form-group">
          <label>Khu vui chơi</label>
          <select v-model="form.ParkID">
            <option value="">-- Chọn khu vui chơi--</option>
            <option v-for="p in parks" :key="p.ID" :value="p.ID">
              {{ p.ParkName }}
            </option>
          </select>
        </div>

        <!-- THỜI GIAN -->
        <div class="form-group">
          <label>Thời gian</label>
          <input v-model="form.BookingDateTime" type="datetime-local" />
        </div>

      </div>

      <!-- DETAILS -->
      <h3 class="detail-title">Chi tiết vé</h3>

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
            <tr v-for="(d, i) in form.details" :key="i">

              <!-- SELECT -->
              <td>
                <select
                  v-model="d.TicketID"
                  :disabled="!form.ParkID"
                >
                  <option value="">
                    {{
                      form.ParkID
                        ? '-- Chọn vé --'
                        : '-- Vui lòng chọn khu vui chơi trước --'
                    }}
                  </option>
                  <option v-for="t in filteredTickets" :key="t.ID" :value="t.ID">
                    {{ t.TicketName }}
                  </option>
                </select>
              </td>

              <!-- QUANTITY -->
              <td>
                <input v-model="d.Quantity" type="number" min="1" />
              </td>

              <!-- PRICE -->
              <td class="price">
                {{ getPrice(d.TicketID) }}
              </td>

              <!-- ACTION -->
              <td>
                <button class="btn-delete" @click="removeDetail(i)">
                  Xóa
                </button>
              </td>

            </tr>

            <tr v-if="!form.details.length">
              <td colspan="4" class="empty">
                Chưa có vé
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <button class="btn-add" @click="addDetail">
        + Thêm vé
      </button>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, computed, watch } from 'vue'
import api from '../provider/api'
import { useRouter, useRoute } from 'vue-router'
import { useParkStore } from "../../parks/provider/store";
import { useToast } from "vue-toastification";

const toast = useToast();
const router = useRouter()
const route = useRoute()
const loading = ref(false)
const tickets = ref([])
const parkStore = useParkStore()
const parks = ref([])

const form = reactive({
  BookingDateTime: '',
  ParkID: '',
  Status: 1,
  details: [
    {
      TicketID: '',
      Quantity: 1
    }
  ]
})

const filteredTickets = computed(() => {
  console.log("FILTER RUN >>>", {
    park: form.ParkID,
    tickets: tickets.value
  })

  if (!form.ParkID) return []

  const result = tickets.value.filter(t => {
    console.log("COMPARE >>>", t.ParkID, form.ParkID)
    return String(t.ParkID) === String(form.ParkID)
  })

  console.log("FILTER RESULT >>>", result)

  return result
})

watch(() => form.ParkID, (val, oldVal) => {
  console.log("PARK SELECTED >>>", val)

  // Không reset lần đầu auto từ query
  if (!oldVal) return

  // reset lại vé khi đổi khu
  form.details = [
    {
      TicketID: '',
      Quantity: 1
    }
  ]
})

// 🔥 FORMAT DATETIME CHUẨN BACKEND
const formatDateTime = (value) => {
  if (!value) return ''

  const d = new Date(value)
  const pad = (n) => n.toString().padStart(2, '0')

  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} `
       + `${pad(d.getHours())}:${pad(d.getMinutes())}:00`
}

onMounted(async () => {
  try {

    // LOAD TICKETS
    const ticketRes = await api.getTickets()

    tickets.value = ticketRes?.data ?? []

    console.log("TICKETS >>>", tickets.value)

    // LOAD PARKS
    await parkStore.getList()

    parks.value = parkStore.items

    if (!form.BookingDateTime) {
      const now = new Date()

      now.setMinutes(
        now.getMinutes() - now.getTimezoneOffset()
      )

      form.BookingDateTime =
        now.toISOString().slice(0, 16)
    }

    const parkId = route.query.park_id

    console.log("PARK ID FROM URL >>>", parkId)

    if (parkId) {

      form.ParkID = parkId

    }

  } catch (e) {

    console.error(e)

    toast.error("Không load được dữ liệu.")

  }
})

const addDetail = () => {
  form.details.push({
    TicketID: '',
    Quantity: 1
  })
}

const removeDetail = (i) => form.details.splice(i, 1)

const getPrice = (id) => {
  const t = tickets.value.find(x => x.ID === id)
  return t ? formatPrice(t.Price) : ''
}

const formatPrice = (price) => {
  return Number(price || 0).toLocaleString("vi-VN") + " đ"
}

const submit = async () => {
  if (loading.value) return

  if (!form.ParkID) {
    return toast.error("Vui lòng chọn khu vui chơi")
  }

  if (!form.BookingDateTime) {
    return toast.error("Vui lòng chọn thời gian")
  }

  if (!form.details.length) {
    return toast.error("Phải có ít nhất 1 vé")
  }

  for (const d of form.details) {

    if (!d.TicketID) {
      return toast.error("Chưa chọn vé")
    }

    d.Quantity = Number(d.Quantity)

    if (isNaN(d.Quantity) || d.Quantity <= 0) {
      return toast.error("Số lượng vé phải lớn hơn 0")
    }
  }

  try {
    loading.value = true

    const payload = {
  ParkID: form.ParkID,
  BookingDateTime: formatDateTime(form.BookingDateTime),

  details: form.details.map(d => ({
    TicketID: d.TicketID,
    Quantity: Number(d.Quantity)
  }))
}

    console.log("PAYLOAD >>>", payload)

    await api.create(payload)

    toast.success("Tạo booking thành công.")

    router.push("/bookings")

  } catch (err) {
    console.error(err)
    toast.error("Có lỗi xảy ra.")
  } finally {
    loading.value = false
  }
}

const goBack = () => router.push("/bookings")
</script>

<style scoped>
.form-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

/* 🔝 ACTION BAR */
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

/* 🔽 FORM */
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

.el-time-picker {
  width: 100%;
}

/* GROUP */
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

/* ✅ FILE BAR */
.file-name {
  font-size: 14px;
}

.file-bar {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* BOX CHỨA TEXT + BUTTON */
/* FILE DISPLAY (1 BOX DUY NHẤT) */
.file-display {
  display: flex;
  align-items: center;

  border: 1px solid #dcdfe6;
  padding: 8px 12px;
  border-radius: 8px;
  background: #fff;
}

/* TEXT */
.file-display span {
  font-size: 14px;
  color: #333;

  flex: none; /* 👈 KHÔNG cho nó giãn */
}

/* BUTTON XÓA */
.btn-remove {
  padding: 5px 10px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  flex-shrink: 0;
  margin-left: auto;
}

.btn-remove:hover {
  background: #c0392b;
}

/* BUTTON CHỌN FILE */
.btn-choose {
  margin-right: 8px; 
}

.btn-choose:hover {
  background: #eaeaea;
}

/* IMAGE */
.preview-large {
  margin-top: 10px;
  width: 100%;
  max-height: 350px;
  object-fit: cover;
  border-radius: 8px;
}

/* BUTTON */
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

.left {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* 🔥 mũi tên to + đậm hơn */
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

/* tạo mũi tên bằng CSS */
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

/* 🔥 chữ đậm + rõ hơn */
.title {
  font-size: 22px;      
  font-weight: 700;     
  color: #454242;
}

.detail-box {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

.detail {
    color: black;
}

.select {
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #dcdfe6;
}

.price {
  min-width: 120px;
  font-weight: 600;
  color: #232423;
}

.detail-title {
  margin: 20px 0 10px;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

/* TABLE WRAPPER */
.table-wrapper {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;
  color: black;
}

/* HEADER */
.table th {
  background: #f8fafc;
  padding: 12px;
  font-weight: 600;
  border-bottom: 1px solid #e5e7eb;
  text-align: center;
}

/* BODY */
.table td {
  padding: 10px;
  text-align: center;
}

/* INPUT + SELECT */
.table select,
.table input {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #dcdfe6;
}

/* EMPTY */
.empty {
  text-align: center;
  color: #999;
}

/* ADD BUTTON */
.btn-add {
  margin-top: 10px;
  padding: 8px 14px;
  background: #3c8dbc;
  color: white;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}

.btn-add:hover {
  background: #367fa9;
}

/* DELETE */
.btn-delete {
  padding: 6px 10px;
  background: #dc3545;
  color: white;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}

.btn-delete:hover {
  background: #c82333;
}
</style>