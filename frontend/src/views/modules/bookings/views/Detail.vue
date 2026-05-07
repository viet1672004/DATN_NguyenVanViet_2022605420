<template>
  <div class="detail-page" v-if="data">

    <div class="action-bar">
      <div class="left">
        <span class="back-icon" @click="goBack">
          <i class="arrow"></i>
        </span>
        <span class="title">Chi tiết booking</span>
      </div>
    </div>

    <div class="action-bar">
      <div class="action-box">

        <button 
          :disabled="getStatusValue(data.Status) !== 0"
          class="btn-pay"
          @click="pay(data.ID)"
        >
          Thanh toán
        </button>

        <button 
          :disabled="getStatusValue(data.Status) !== 0"
          class="btn-cancel"
          @click="cancel(data.ID)"
        >
          Hủy
        </button>

      </div>
    </div>

    <div class="top-box">
      <div class="info">
        <h2><b class="indam">Mã Booking: </b>{{ data.BookingCode }}</h2>

        <p><b class="indam">Người đặt:</b> {{ data.UserName || data.UserID }}</p>

        <p><b class="indam">Khu vui chơi:</b> {{ data.ParkName }}</p>

        <p><b class="indam">Thời gian:</b> {{ formatDateTime(data.BookingDateTime) }}</p>

        <p><b class="indam">Tổng tiền:</b> {{ formatPrice(data.TotalPrice) }}</p>

        <p><b class="indam">Trạng thái: </b>
          <span class="status" :class="getStatusClass(data.Status)">
            {{ getStatusIcon(data.Status) }} {{ getStatusText(data.Status) }}
          </span>
        </p>
      </div>
    </div>

    <div class="desc-box">
      <h3>Chi tiết vé</h3>

      <table class="table">
        <thead>
          <tr>
            <th>Vé</th>
            <th>Loại vé</th>
            <th v-if="hasDateTicket">Số ngày sử dụng</th>
            <th v-if="hasComboTicket">Combo</th>
            <th>Số lượng</th>
            <th>Giá</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="d in data.details" :key="d.ID">

            <!-- VÉ -->

            <td>
              <div class="ticket-name">
                {{ d.TicketName }}
              </div>
            </td>

            <!-- LOẠI VÉ -->
            <td class="type-text">
              {{ getTypeText(d.TicketType) }}
            </td>
           
            <!-- SỐ NGÀY -->
            <td v-if="hasDateTicket">
              <span v-if="d.TicketType === 'date'">
                {{ d.NumberOfDays }} ngày
              </span>
              <span v-else>-</span>
            </td>

            <td v-if="hasComboTicket">
              <span v-if="d.TicketType === 'combo'">
                {{ d.ComboInfo }}
              </span>
              <span v-else>-</span>
            </td>

            <!-- SỐ LƯỢNG -->
            <td>{{ d.Quantity }}</td>

            <!-- GIÁ -->
            <td>{{ formatPrice(d.Price) }}</td>

          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../provider/api'
import { useRoute, useRouter } from 'vue-router'
import paymentApi from "../../payments/provider/api";

const route = useRoute()
const router = useRouter()

const data = ref({ details: [] })

const formatDateTime = (date) => {
  if (!date) return ""

  const d = new Date(date)

  const pad = (n) => n.toString().padStart(2, '0')

  const time = `${pad(d.getHours())}:${pad(d.getMinutes())}`
  const day = `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`

  return `${time} - ${day}`
}

const getStatusValue = (s) => {
  if (typeof s === "object") return Number(s.value)
  return Number(s)
}

const pay = (id) => {
  router.push(`/payments/pay?bookingId=${id}`)
}

const cancel = async (id) => {
  if (Number(data.value.Status) !== 0) return

  if (!confirm("Hủy booking này?")) return

  try {
    await api.cancel(id)
    alert("Hủy thành công")

    const res = await api.getDetail(id)
    data.value = res.data

  } catch (e) {
    alert("Hủy thất bại")
  }
}

const getTypeText = (type) => {
  switch (type) {
    case 'adult': return 'Người lớn'
    case 'child': return 'Trẻ em'
    case 'combo': return 'Combo'
    case 'date': return 'Theo ngày'
    default: return ''
  }
}

const getStatusText = (s) => {
  switch (Number(s)) {
    case 0: return "Chờ thanh toán"
    case 1: return "Đã thanh toán"
    case 2: return "Đã hủy"
    default: return ""
  }
}

const getStatusIcon = (s) => {
  switch (Number(s)) {
    case 0: return "🟡"
    case 1: return "🟢"
    case 2: return "🔴"
    default: return "⚪"
  }
}

const getStatusClass = (s) => {
  switch (Number(s)) {
    case 0: return "warning"
    case 1: return "success"
    case 2: return "danger"
    default: return ""
  }
}

const hasDateTicket = computed(() =>
  (data.value.details || []).some(d => d.TicketType === 'date')
)

const hasComboTicket = computed(() =>
  (data.value.details || []).some(d => d.TicketType === 'combo')
)

onMounted(async () => {
  const res = await api.getDetail(route.params.id)
  data.value = res.data
})

const goBack = () => router.push("/bookings")

const formatDate = (date) => {
  if (!date) return ""
  return new Date(date).toLocaleDateString("vi-VN")
}

const formatPrice = (price) => {
  return Number(price || 0).toLocaleString("vi-VN") + " đ"
}
</script>

<style scoped>
.detail-page {
  padding: 20px 30px;
  background: #f4f6f9;
  min-height: 100vh;
}

/* HEADER */
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

/* BACK */
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

.back-icon:hover {
  background: #ddd;
}

.title {
  font-size: 22px;
  font-weight: 700;
  color: #454242;
}

/* TOP BOX */
.top-box {
  display: flex;
  gap: 30px;
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;

  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* INFO */
.info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  color: black;
}

.info h2 {
  font-size: 26px;
  margin-bottom: 15px;
}

.info p {
  margin-bottom: 8px;
  color: #555;
}

/* STATUS */
.status {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  width: fit-content;
}

.status.active {
  background: #e6f7ee;
  color: #00a65a;
}

.status.inactive {
  background: #f1f1f1;
  color: #999;
}

/* TABLE */
.desc-box {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  color: black;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th {
  background: #f8fafc;
  padding: 12px;
}

.table td {
  padding: 12px;
  text-align: center;
}

.table tr:hover {
  background: #f9fbfd;
}

.table thead {
  background: #f8fafc;
}

.table tbody tr:hover {
  background: #f9fbfd;
}

.status.warning {
  background: #fff3cd;
  color: #856404;
}

.status.success {
  background: #d4edda;
  color: #155724;
}

.status.danger {
  background: #f8d7da;
  color: #721c24;
}

.ticket-name {
  font-weight: 600;
  color: #222;
}

.sub {
  font-size: 13px;
  color: #666;
  margin-top: 3px;
}

/* BADGE */
.badge {
  margin-left: 6px;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 500;
}

/* COMBO */
.badge.combo {
  background: #e7f1ff;
  color: #2f80ed;
}

/* DAYS */
.badge.days {
  background: #fff3cd;
  color: #856404;
}

.hide-col {
  display: none;
} 

/* TYPE COLORS */
.type-text {
  font-weight: 600;
  color: #333;
}

.info p {
  color: #000; /* 🔥 tất cả value màu đen */
}

.indam {
  font-weight: 700;
  color: #000; /* 🔥 label cũng đen */
}

/* ACTION RIGHT SIDE */
.top-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* box chứa nút */
.action-box {
  display: flex;
  flex-direction: row;   /* 🔥 đổi sang ngang */
  gap: 10px;            /* khoảng cách giữa 2 nút */
  justify-content: flex-end; /* đẩy sang phải (optional) */
  align-items: center;
}

/* nút thanh toán */
.btn-pay {
  padding: 10px 16px;
  background: #28a745;
  color: #fff;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.btn-pay:hover {
  background: #218838;
}

/* nút hủy */
.btn-cancel {
  padding: 10px 16px;
  background: #dc3545;
  color: #fff;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.btn-cancel:hover {
  background: #c82333;
}

button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}
</style>