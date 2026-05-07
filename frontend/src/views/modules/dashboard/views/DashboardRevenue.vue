<template>
  <div class="card">
    <div class="card-header">
      <h3>Doanh thu</h3>
    </div>

    <div class="chart-container">
      <Line
        v-if="chartData.labels.length"
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
import { computed } from "vue";

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

const chartData = computed(() => ({
  labels: props.revenueChart.map((i) => i.date),

  datasets: [
    {
      label: "Doanh thu (VNĐ)",
      data: props.revenueChart.map((i) => Number(i.revenue)),

      borderColor: "#2563eb",
      backgroundColor: "rgba(37, 99, 235, 0.15)",

      borderWidth: 3,
      tension: 0.4,
      fill: true,
      pointRadius: 4,
      pointHoverRadius: 6,
    },
  ],
}));

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
    },
  },

  scales: {
    x: {
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

    y: {
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
  },
};
</script>

<style scoped>
.card {
  background: #ffffff;
  border-radius: 18px;
  padding: 24px;
  height: 420px;

  border: 1px solid #e5e7eb;

  box-shadow:
    0 2px 8px rgba(0, 0, 0, 0.04),
    0 8px 24px rgba(0, 0, 0, 0.06);
}

.card-header {
  margin-bottom: 20px;
}

.card-header h3 {
  font-size: 22px;
  font-weight: 700;
  color: #111827;

  margin: 0;
}

.chart-container {
  position: relative;
  height: 320px;
}
</style>