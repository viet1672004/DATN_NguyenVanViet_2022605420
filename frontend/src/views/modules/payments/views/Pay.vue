<template>
  <div class="pay-container" v-if="booking && booking.ID">

    <h2 class="title">Thanh toán booking</h2>

    <!-- BOOKING INFO -->
    <div class="box">
      <div class="box-title">Thông tin booking</div>

      <div class="info-row">
        <span>Mã booking:</span>  
        <b>{{ booking.BookingCode }}</b>
      </div>

      <div class="info-row">
        <span>Khách hàng:</span>
        <b>{{ booking.UserName }}</b>
      </div>

      <div class="info-row">
        <span>Khu vui chơi:</span>
        <b>{{ booking.ParkName }}</b>
      </div>

      <div class="info-row total">
        <span>Tổng tiền:</span>
        <b>{{ formatPrice(booking.TotalPrice) }}</b>
      </div>
    </div>

    <!-- PAYMENT METHOD -->
    <div class="box">
      <div class="box-title">Phương thức thanh toán</div>

      <div class="method-list">
        <label 
          v-for="m in methods"
          :key="m.value"
          class="method-item"
          :class="{ active: method === m.value }"
        >
          <input type="radio" :value="m.value" v-model="method" />

          <img :src="m.icon" class="icon" />

          <span>{{ m.label }}</span>
        </label>
      </div>
    </div>

    <!-- PAYMENT INFO -->
    <div class="box">
      <div class="box-title">Thông tin thanh toán</div>

      <div class="info-rows">
        <span>Số tiền:</span>
        <b>{{ formatPrice(booking.TotalPrice) }}</b>
      </div>

      <div class="info-rows total">
        <span>Tổng tiền:</span>
        <b>{{ formatPrice(booking.TotalPrice) }}</b>
      </div>
    </div>

    <!-- QR / PAYMENT GUIDE -->
    <div class="box" v-if="method">
      <div class="box-title">Thực hiện thanh toán</div>

      <!-- MOMO -->
      <div v-if="method === 'MOMO'" class="qr-section">
        <img :src="qrMomo" class="qr-img" />
        <p class="indam">Quét QR bằng MoMo</p>
        <p class="indam">Nội dung: {{ booking.ID }}</p>
      </div>

      <!-- VNPAY -->
      <div v-if="method === 'VNPAY'" class="vnpay-box">
        <!-- VNPAY -->
        <button 
          class="btn-pay" 
          @click="submitVnpay"
          :disabled="loading || booking.Status == 1 || booking.Status === 'PAID'"
        >
          Thanh toán với VNPay
        </button>
      </div>

      <!-- BANK -->
      <div v-if="method === 'BANK'" class="bank-info">
        <p class="indam"><b class="indam">Ngân hàng:</b> Vietcombank</p>
        <p class="indam"><b class="indam">Số TK:</b> 1031429811</p>
        <p class="indam"><b class="indam">Chủ TK:</b> NGUYEN VAN VIET</p>
        <p class="indam"><b class="indam">Nội dung:</b> {{ booking.ID }}</p>

        <img :src="chuyenkhoan" class="qr-img" />
      </div>
    </div>

    <!-- BUTTON -->
    <button 
      v-if="method !== 'VNPAY'"
      class="btn-pay" 
      @click="submit"
      :disabled="loading || booking.Status == 1 || booking.Status === 'PAID'"
    >
      {{
        booking.Status == 1 || booking.Status === 'PAID'
          ? "Đã thanh toán"
          : loading
          ? "Đang xử lý..."
          : method === 'MOMO'
            ? "Thanh toán bằng MoMo"
            : "Xác nhận chuyển khoản"
      }}
    </button>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import paymentApi from "../provider/api"
import bookingApi from "@/views/modules/bookings/provider/api"
import momoLogo from "@/assets/momo.png"
import vnpayLogo from "@/assets/vnpay.png"
import chuyenkhoan from "@/assets/ck.jpg"

const route = useRoute()
const router = useRouter()
const loading = ref(false)
const booking = ref(null)
const method = ref("MOMO")
const bookingId = route.query.bookingId 
const methods = [
  {
    value: "MOMO",
    label: "Ví MoMo",
    icon: momoLogo
  },
  {
    value: "VNPAY",
    label: "VNPay",
    icon: vnpayLogo
  },
  {
    value: "BANK",
    label: "Chuyển khoản ngân hàng",
    icon: "https://cdn-icons-png.flaticon.com/512/2830/2830284.png"
  }
]

// QR
const qrMomo = ref("")
const qrVnpay = ref("")
const qrBank = ref("")

onMounted(async () => {
  if (!bookingId) {
    alert("Thiếu BookingID")
    router.push("/bookings")
    return
  }

  const res = await bookingApi.getDetail(bookingId)
  booking.value = res.data

  const b = booking.value

  qrMomo.value = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MOMO_${b.BookingCode}_${b.TotalPrice}`

  qrBank.value = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=VCB_123456789_FUN_TICKET_${b.ID}_${b.TotalPrice}`
})

const submitVnpay = async () => {
  if (loading.value) return

  try {
    loading.value = true

    const res = await paymentApi.createVnpay({
      BookingID: bookingId
    })

    window.location.href = res.data.url
  } catch (e) {
    alert("Không tạo được link VNPay")
  } finally {
    loading.value = false
  }
}

const submit = async () => {
  if (booking.value.Status == 1 || booking.value.Status === "PAID") {
    alert("Booking đã thanh toán")
    return
  }
  try {
    loading.value = true

    // 🔥 MOMO / BANK
    await paymentApi.pay({
      BookingID: bookingId,
      PaymentMethod: method.value
    })

    alert("Thanh toán thành công")
    router.push("/payments")

  } catch (e) {
    alert(e.response?.data?.message || "Thanh toán thất bại")
  } finally {
    loading.value = false
  }
}

const formatPrice = (v) =>
  Number(v || 0).toLocaleString("vi-VN") + " đ"
</script>

<style scoped>
.pay-container {
  max-width: 650px;
  margin: auto;
  background: #f4f6f9;
  padding: 20px;
}

.title {
  margin-bottom: 20px;
  color: black;
}

/* BOX */
.box {
  background: white;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 15px;
}

.box-title {
  font-weight: bold;
  margin-bottom: 10px;
  color: black;
}

.info-rows {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  color: black;
}

/* INFO */
.info-row {
  display: flex;
  align-items: center;
  gap: 8px;

  padding: 6px 0;
  color: black;
}

.total {
  color: green;
  font-size: 18px;
}

/* METHOD */
.method-list {
  display: flex;
  gap: 10px;
}

.method-item {
  flex: 1;
  border: 1px solid #dcdfe6;
  padding: 14px;
  border-radius: 10px;
  cursor: pointer;

  display: flex;
  align-items: center;
  gap: 12px;

  background: white;

  transition: all 0.2s ease;

  color: #222; /* FIX CHỮ MỜ */
}

.method-item input {
  display: none;
}

.method-item.active {
  border: 2px solid #007bff;
  background: #eef6ff;
}

.icon {
  width: 30px;
  height: 30px;
}

/* QR */
.qr-section {
  text-align: center;
}

.qr-img {
  width: 200px;
  display: block;
  margin: 10px auto;
}

.bank-info p {
  margin: 5px 0;
}

/* BUTTON */
.btn-pay {
  width: 100%;
  padding: 12px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-pay:hover {
  background: #0056b3;
}

.indam {
  color: black;
}
</style>