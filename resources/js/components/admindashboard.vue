<template>
    <div :class="['home-page', { dark: theme.isDark }]">
        <div class="layout">
            <Sidebar :class="{ expanded: isExpanded }" />
            <div class="main-content">
                <router-link to="/profile" class="profile-float" v-if="user">
                    <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.userAvatar')" class="avatar" />
                </router-link>

                <link
                rel="stylesheet"
                href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
                />

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

                <div class="dashboard-container">
                    <header class="dashboard-header">
                        <h4>{{ t('dashboard.hello') }} {{ userName }},</h4>
                    </header>

                    <!-- Row 1: 4 cards -->
                    <section class="dashboard-row-1">
                        <!-- Total véhicules -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.totalVehicles') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="carsChart"></canvas>
                            </div>
                        </div>

                        <!-- Utilisateurs actifs -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.activeUsers') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="usersChart"></canvas>
                            </div>
                        </div>

                        <!-- Coût global -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.globalMaintenanceCost') }}</h5>
                            <div class="stat-value" v-if="maintenanceTotalCost > 0">
                                <span>{{ formatCurrency(maintenanceTotalCost) }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-coin"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>

                        <!-- Taux de disponibilité -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.availabilityRate') }}</h5>
                            <div class="stat-value" v-if="availabilityRate > 0">
                                <span>{{ availabilityRate }}%</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-check-circle"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Row 2: 3 cards -->
                    <section class="dashboard-row-2">
                        <!-- Véhicules par année -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.distributionByYear') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="vehiclesByYearChart"></canvas>
                            </div>
                        </div>

                        <!-- Top 5 coûteux -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.top5Expensive') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="topExpensiveCarsChart"></canvas>
                            </div>
                        </div>

                        <!-- Coût par mois -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.maintenanceCost6Months') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="maintenanceCostChart"></canvas>
                            </div>
                        </div>
                    </section>

                    <!-- Row 3: 2 cards -->
                    <section class="dashboard-row-3">
                        <!-- Alertes urgentes -->
                        <div class="card chart-card urgent-alerts">
                            <h5>{{ t('dashboard.urgentAlerts') }}</h5>
                            <div class="alerts-list" v-if="urgentAlerts.length > 0">
                                <div 
                                    v-for="alert in urgentAlerts" 
                                    :key="alert.idAlerte"
                                    class="alert-item"
                                >
                                    <div class="alert-icon">
                                        <i class="bi bi-bell-fill"></i>
                                    </div>
                                    <div class="alert-details">
                                        <span class="alert-type">{{ alert.type }}</span>
                                        <span class="alert-vehicle">{{ alert.vehicule }}</span>
                                        <span class="alert-meta">
                                            {{ formatDate(alert.date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="no-data">
                                <i class="bi bi-check-circle"></i>
                                <p>{{ t('dashboard.noUrgentAlerts') }}</p>
                            </div>
                        </div>

                        <!-- Historique interventions -->
                        <div class="card chart-card intervention-history">
                            <h5>{{ t('dashboard.recentInterventions') }}</h5>
                            <div class="history-list" v-if="interventionsHistory.length > 0">
                                <div 
                                    v-for="intervention in interventionsHistory" 
                                    :key="intervention.id"
                                    class="history-item"
                                >
                                    <div class="history-icon">
                                        <i class="bi bi-wrench"></i>
                                    </div>
                                    <div class="history-details">
                                        <span class="history-type">{{ intervention.type }}</span>
                                        <span class="history-vehicle">{{ intervention.vehicule }}</span>
                                        <span class="history-meta">
                                            {{ formatDate(intervention.date) }} • {{ formatCurrency(intervention.cout) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="no-data">
                                <i class="bi bi-inbox"></i>
                                <p>{{ t('dashboard.noRecentInterventions') }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from './sidebar.vue';
import axios from "axios";
import { 
  Chart, 
  PieController, 
  BarController, 
  LineController, 
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement, 
  Tooltip, 
  Legend 
} from "chart.js";
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';

Chart.register(
  PieController, 
  BarController, 
  LineController,
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement, 
  Tooltip, 
  Legend
);

export default {
  name: "AdminDashboard",
  components: { Sidebar },
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  computed: {
    menuItems() {
      return [
        { label: this.t('nav.home'), to: "/" },
        { label: this.t('nav.dashboard'), to: "/admindashboard" },
        { label: this.t('nav.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('nav.interventions'), to: "/interventions/catalogue" },
        { label: this.t('nav.alerts'), to: "/alertes" },
      ];
    }
  },
  data() {
    return {
      userName: "",
      user: null,
      isExpanded: false,
      
      // Charts
      carsChart: null,
      usersChart: null,
      maintenanceCostChart: null,
      vehiclesByYearChart: null,
      topExpensiveCarsChart: null,
      
      // Data
      maintenanceTotalCost: 0,
      availabilityRate: 0,
      interventionsMonthTotal: 0,
      alertsTotal: 0,
      urgentAlerts: [],
      interventionsHistory: [],
    };
  },
  async mounted() {
    await this.getUser();
    await this.loadAllStats();
  },
  methods: {
    async getUser() {
      try {
        const token = localStorage.getItem("token");
        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.user = res.data;
        this.userName = res.data.prenom;
      } catch (error) {
        console.error(this.t('errors.getUserError'), error);
        this.$router.push("/login");
      }
    },

    async loadAllStats() {
      await Promise.all([
        this.loadCarsStats(),
        this.loadInterventionsMonthTotal(),
        this.loadUsersStats(),
        this.loadMaintenanceTotalCost(),
        this.loadMaintenanceCostByMonth(),
        this.loadVehiclesByYear(),
        this.loadTopExpensiveCars(),
        this.loadAvailabilityRate(),
        this.loadAlertsStats(),
        this.loadInterventionsHistory()
      ]);
    },

    async loadInterventionsMonthTotal() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/count-by-month", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.interventionsMonthTotal = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadInterventionsError'), err);
        this.interventionsMonthTotal = 0;
      }
    },

    async loadCarsStats() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/voitures/count-by-status", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.statut);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (this.carsChart) this.carsChart.destroy();
          this.carsChart = new Chart(this.$refs.carsChart, {
            type: "doughnut",
            data: {
              labels,
              datasets: [{
                data: values,
                backgroundColor: ["#BFCC94", "#D2E1EE", "#748BAA"],
                cutout: "65%",
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: { 
                  callbacks: { 
                    label: (ctx) => `${ctx.label}: ${ctx.parsed}`
                  } 
                },
              },
            },
          });
        });
      } catch (err) {
        console.error(this.t('errors.loadCarsStatsError'), err);
      }
    },

    async loadUsersStats() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/users/count-by-role", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.role);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (this.usersChart) this.usersChart.destroy();
          this.usersChart = new Chart(this.$refs.usersChart, {
            type: "doughnut",
            data: {
              labels,
              datasets: [{
                data: values,
                backgroundColor: ["#BFCC94", "#D2E1EE", "#748BAA"],
                cutout: "65%",
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { display: false },
                tooltip: { 
                  callbacks: { 
                    label: (ctx) => `${ctx.label}: ${ctx.parsed}`
                  } 
                },
              },
            },
          });
        });
      } catch (err) {
        console.error(this.t('errors.loadUsersStatsError'), err);
      }
    },

    async loadMaintenanceTotalCost() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/maintenance/total-cost", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.maintenanceTotalCost = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadMaintenanceCostError'), err);
      }
    },

    async loadMaintenanceCostByMonth() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/maintenance/cost-by-month", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.month);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (this.maintenanceCostChart) this.maintenanceCostChart.destroy();
          this.maintenanceCostChart = new Chart(this.$refs.maintenanceCostChart, {
            type: "line",
            data: {
              labels,
              datasets: [{
                label: this.t('charts.costLabel'),
                data: values,
                borderColor: "#748BAA",
                backgroundColor: "rgba(116, 139, 170, 0.1)",
                fill: true,
                tension: 0.3,
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } },
            },
          });
        });
      } catch (err) {
        console.error(this.t('errors.loadCostByMonthError'), err);
      }
    },

    async loadVehiclesByYear() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/voitures/by-year", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.annee);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (this.vehiclesByYearChart) this.vehiclesByYearChart.destroy();
          this.vehiclesByYearChart = new Chart(this.$refs.vehiclesByYearChart, {
            type: "bar",
            data: {
              labels,
              datasets: [{
                label: this.t('charts.vehiclesLabel'),
                data: values,
                backgroundColor: "#BFCC94",
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } },
            },
          });
        });
      } catch (err) {
        console.error(this.t('errors.loadVehiclesByYearError'), err);
      }
    },

    async loadTopExpensiveCars() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/voitures/top-expensive", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => `${item.marque} ${item.modele}`);
        const values = res.data.map(item => item.total_cost);

        this.$nextTick(() => {
          if (this.topExpensiveCarsChart) this.topExpensiveCarsChart.destroy();
          this.topExpensiveCarsChart = new Chart(this.$refs.topExpensiveCarsChart, {
            type: "bar",
            data: {
              labels,
              datasets: [{
                label: this.t('charts.costCurrency'),
                data: values,
                backgroundColor: ["#748BAA", "#BFCC94", "#D2E1EE", "#546A88", "#B4CDED"],
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              indexAxis: 'y',
              plugins: { legend: { display: false } },
              scales: { x: { beginAtZero: true } },
            },
          });
        });
      } catch (err) {
        console.error(this.t('errors.loadTopCarsError'), err);
      }
    },

    async loadAvailabilityRate() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/voitures/availability-rate", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.availabilityRate = res.data.rate || 0;
      } catch (err) {
        console.error(this.t('errors.loadAvailabilityRateError'), err);
      }
    },

    async loadAlertsStats() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/alertes/stats", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.alertsTotal = res.data.total || 0;
        this.urgentAlerts = res.data.urgentes || [];
      } catch (err) {
        console.error(this.t('errors.loadAlertsStatsError'), err);
      }
    },

    async loadInterventionsHistory() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/recent-history", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.interventionsHistory = res.data || [];
      } catch (err) {
        console.error(this.t('errors.loadInterventionsHistoryError'), err);
      }
    },

    formatCurrency(value) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
      }).format(value);
    },

    formatDate(date) {
      if (!date) return "—";
      const d = new Date(date);
      return d.toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },

    async logout() {
      try {
        await axios.post(
          "http://127.0.0.1:8000/api/logout",
          {},
          { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
        );
      } catch (error) {
        console.warn(this.t('errors.logoutError'), error);
      } finally {
        localStorage.removeItem("token");
        this.$router.push({ name: "Login" });
      }
    },
  },
  beforeUnmount() {
    // Clean up charts
    if (this.carsChart) this.carsChart.destroy();
    if (this.usersChart) this.usersChart.destroy();
    if (this.maintenanceCostChart) this.maintenanceCostChart.destroy();
    if (this.vehiclesByYearChart) this.vehiclesByYearChart.destroy();
    if (this.topExpensiveCarsChart) this.topExpensiveCarsChart.destroy();
  }
};
</script>