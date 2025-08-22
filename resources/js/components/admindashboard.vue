<template>
  <div :class="['home-page', { dark: isDark }]">
    <!-- Theme Toggle Button -->
    <button @click="toggleTheme" class="theme-btn">
      <i v-if="isDark" class="bi bi-sun"></i>
      <i v-else class="bi bi-moon"></i>
    </button>

    <!-- Navbar -->
    <nav class="navbar">
      <router-link
        v-for="(item, index) in menuItems"
        :key="index"
        :to="item.to"
        class="nav-link"
        :class="{ active: $route.path === item.to }"
      >
        {{ item.label }}
      </router-link>
    </nav>
    <br />

    <!-- Title -->
    <h1>Hello !</h1>
    <br />

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Vehicles Overview -->
      <div class="bg-black rounded-2xl shadow p-4" style="height: 300px;">
        <h2>Vehicles Overview</h2>
        <PieChart :chart-data="vehicleChartData" :chart-options="chartOptions" />
      </div>

      <!-- Upcoming Reminders -->
      <div class="bg-black rounded-2xl shadow p-4">
        <h2>Upcoming Reminders</h2>
        <ul class="divide-y text-sm">
          <li class="py-2">
            Oil change for Car #12 - <span class="font-bold">25 Aug</span>
          </li>
          <li class="py-2">
            Insurance renewal for Car #45 - <span class="font-bold">28 Aug</span>
          </li>
          <li class="py-2">
            Inspection for Car #77 - <span class="font-bold">30 Aug</span>
          </li>
        </ul>
      </div>

      <!-- Recent Logs -->
      <div class="bg-black rounded-2xl shadow p-4" style="height: 300px;">
        <h2>Recent Logs</h2>
        <BarChart :chart-data="logsChartData" :chart-options="chartOptions" />
      </div>
    </div>
  </div>
</template>

<script>
import PieChart from "./PieChart.vue";
import BarChart from "./BarChart.vue";

export default {
  name: "AdminDashboard",
  components: { PieChart, BarChart },
  data() {
    return {
      isDark: false,
      menuItems: [
        { label: "Acceuil", to: "/" },
        { label: "Tableau de board", to: "/admindashboard" },
        { label: "Catalogue", to: "/cataloguevoitures" },
        { label: "Interventions", to: "/interventions" },
        { label: "Alertes", to: "/alertes" },
      ],
      // Vehicles chart data
      vehicleChartData: {
        labels: ["En boutique", "En location"],
        datasets: [
          {
            data: [95, 25],
            backgroundColor: ["#6d3316", "#e46e25"],
          },
        ],
      },
      // Logs chart data
      logsChartData: {
        labels: ["19 Aug", "20 Aug", "21 Aug"],
        datasets: [
          {
            label: "Logs",
            data: [1, 1, 1],
            backgroundColor: "#3b82f6",
          },
        ],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
      },
    };
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem("theme", this.isDark ? "dark" : "light");
    },
  },
  mounted() {
    const saved = localStorage.getItem("theme");
    if (saved) this.isDark = saved === "dark";
  },
};
</script>


