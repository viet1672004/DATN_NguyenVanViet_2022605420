<template>
  <div class="card">

    <!-- HEADER -->
    <div class="card-header">

      <div>
        <h3>Biểu đồ doanh thu</h3>

        <p>
          Thống kê doanh thu theo ngày
        </p>
      </div>

      <div class="chart-badge">
        {{ revenueChart.length }} ngày
      </div>

      <button
        class="png-btn no-pdf"
        @click="exportChart"
      >
        📷 Xuất PNG
      </button>

    </div>  

    <!-- EMPTY -->
    <div
      v-if="!chartData.labels.length"
      class="empty-box"
    >
      Không có dữ liệu doanh thu
    </div>

    <!-- CHART -->
    <div
      v-else
      class="chart-container"
    >

      <Line
        ref="chartRef"
        :data="chartData"
        :options="chartOptions"
      />

    </div>

  </div>
</template>

<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

import { Line } from "vue-chartjs";

import { computed, ref } from "vue";
import { useToast } from "vue-toastification";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
);

const props = defineProps({

  revenueChart: {
    type: Array,
    default: () => [],
  },

});

const chartRef = ref(null);
const toast = useToast();
/*
|--------------------------------------------------------------------------
| Export PNG
|--------------------------------------------------------------------------
*/

const exportChart = () => {

  try {
  const chart =
    chartRef.value?.chart;

  if (!chart) return;

  /*
  |--------------------------------------------------------------------------
  | Original Canvas
  |--------------------------------------------------------------------------
  */

  const originalCanvas =
    chart.canvas;

  /*
  |--------------------------------------------------------------------------
  | Create New Canvas
  |--------------------------------------------------------------------------
  */

  const canvas =
    document.createElement("canvas");

  const extraHeight = 100;

  canvas.width =
    originalCanvas.width;

  canvas.height =
    originalCanvas.height + extraHeight;

  const ctx =
    canvas.getContext("2d");

  /*
  |--------------------------------------------------------------------------
  | Background
  |--------------------------------------------------------------------------
  */

  ctx.fillStyle = "#ffffff";

  ctx.fillRect(
    0,
    0,
    canvas.width,
    canvas.height
  );

  /*
  |--------------------------------------------------------------------------
  | Title
  |--------------------------------------------------------------------------
  */

  ctx.fillStyle = "#111827";

  ctx.font =
    "bold 28px Arial";

  ctx.textAlign = "center";

  ctx.fillText(

    "BIỂU ĐỒ DOANH THU",

    canvas.width / 2,

    40

  );

  /*
  |--------------------------------------------------------------------------
  | Export Date
  |--------------------------------------------------------------------------
  */

  const today =
    new Date()
      .toLocaleDateString("vi-VN");

  ctx.font =
    "18px Arial";

  ctx.fillStyle = "#6b7280";

  ctx.fillText(

    `Ngày xuất: ${today}`,

    canvas.width / 2,

    75

  );

  /*
  |--------------------------------------------------------------------------
  | Draw Chart
  |--------------------------------------------------------------------------
  */

  ctx.drawImage(
    originalCanvas,
    0,
    extraHeight
  );

  /*
  |--------------------------------------------------------------------------
  | Export
  |--------------------------------------------------------------------------
  */

  const image =
    canvas.toDataURL("image/png");

  const link =
    document.createElement("a");

  link.href = image;

  link.download =
    "bieu-do-doanh-thu.png";

  link.click();

  toast.success(
      "Xuất PNG thành công!"
    );
  } catch (error) {

    console.error(error);

    toast.error(
      "Xuất PNG thất bại!"
    );
  }
};

/*
|--------------------------------------------------------------------------
| Chart Data
|--------------------------------------------------------------------------
*/

const chartData = computed(() => ({

  labels: props.revenueChart.map((i) =>
    formatDate(i.date)
  ),

  datasets: [

    {
      label: "Doanh thu (VNĐ)",

      data: props.revenueChart.map(
        (i) => Number(i.revenue)
      ),

      borderColor: "#2563eb",

      backgroundColor:
        "rgba(37, 99, 235, 0.15)",

      borderWidth: 3,

      tension: 0.4,

      fill: true,

      pointRadius: 4,

      pointHoverRadius: 6,
    },

  ],

}));

/*
|--------------------------------------------------------------------------
| Chart Options
|--------------------------------------------------------------------------
*/

const chartOptions = {

  responsive: true,

  maintainAspectRatio: false,

  plugins: {

    legend: {

      labels: {

        color: "#111827",

        font: {
          size: 14,
          weight: "600",
        },

      },

    },

    tooltip: {

      titleColor: "#111827",

      bodyColor: "#111827",

      backgroundColor: "#ffffff",

      borderColor: "#e5e7eb",

      borderWidth: 1,

      callbacks: {

        label: (context) => {

          return (
            "Doanh thu: " +
            Number(context.raw)
              .toLocaleString("vi-VN") +
            " đ"
          );
        },

      },

    },

  },

  scales: {

    /*
    |--------------------------------------------------------------------------
    | X AXIS
    |--------------------------------------------------------------------------
    */

    x: {

      title: {

        display: true,

        text: "Ngày",

        color: "#111827",

        font: {
          size: 14,
          weight: "700",
        },

      },

      ticks: {

        color: "#111827",

        font: {
          size: 13,
          weight: "500",
        },

      },

      grid: {
        color: "#e5e7eb",
      },

    },

    /*
    |--------------------------------------------------------------------------
    | Y AXIS
    |--------------------------------------------------------------------------
    */

    y: {

      beginAtZero: true,

      title: {

  display: true,

  text: "Doanh thu (VNĐ)",

  color: "#111827",

  rotation: 0,

  padding: {
    bottom: 10,
  },

  font: {
    size: 14,
    weight: "700",
  },

},

      ticks: {

        color: "#111827",

        callback: (value) => {

          return (
            Number(value)
              .toLocaleString("vi-VN")
          );

        },

      },

      grid: {
        color: "#e5e7eb",
      },

    },

  },

};

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const formatDate = (value) => {

  if (!value) return "";

  return new Date(value)
  .toLocaleDateString("vi-VN", {

    timeZone: "Asia/Ho_Chi_Minh",

    year: "numeric",

    month: "2-digit",

    day: "2-digit",

  });
};
</script>

<style scoped>
.card {
  background: #ffffff;

  border-radius: 20px;

  padding: 24px;

  min-height: 450px;

  border: 1px solid #e5e7eb;

  box-shadow:
    0 2px 8px rgba(0,0,0,0.04),
    0 8px 24px rgba(0,0,0,0.06);
}

/* HEADER */

.card-header {
  display: flex;

  justify-content: space-between;

  align-items: center;

  flex-wrap: wrap;

  gap: 16px;

  margin-bottom: 24px;
}

.card-header h3 {
  margin: 0;

  font-size: 24px;

  font-weight: 700;

  color: #111827;
}

.card-header p {
  margin-top: 6px;

  color: #6b7280;

  font-size: 14px;
}

/* BADGE */

.chart-badge {
  background: #eff6ff;

  color: #2563eb;

  padding: 10px 16px;

  border-radius: 999px;

  font-size: 13px;

  font-weight: 700;
}

/* EMPTY */

.empty-box {
  height: 320px;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 14px;

  background: #f9fafb;

  color: #6b7280;
}

/* CHART */

.chart-container {
  position: relative;

  height: 340px;
}

/* RESPONSIVE */

@media (max-width: 768px) {

  .card-header {
    flex-direction: column;

    align-items: flex-start;
  }
}

.png-btn {
  height: 42px;

  padding: 0 18px;

  border: none;

  border-radius: 12px;

  background: #7c3aed;

  color: white;

  font-weight: 700;

  cursor: pointer;

  transition: 0.2s;
}

.png-btn:hover {
  background: #6d28d9;
}
</style>