<script setup>
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

// Register chart.js components
ChartJS.register(Title, Tooltip, Legend, ArcElement)

// Props from parent
defineProps({
  chartData: Object,
  chartOptions: Object
})
</script>

<template>
  <Pie :data="chartData" :options="chartOptions" />
</template>
