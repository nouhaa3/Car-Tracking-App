<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, CategoryScale, LinearScale, BarElement } from 'chart.js'

// Register chart.js components
ChartJS.register(Title, Tooltip, Legend, CategoryScale, LinearScale, BarElement)

// Props from parent
defineProps({
  chartData: Object,
  chartOptions: Object
})
</script>

<template>
  <Bar :data="chartData" :options="chartOptions" />
</template>
