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

                <nav class="navbar mb-5">
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

                        <!-- Interventions du mois -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.interventionsThisMonth') }}</h5>
                            <div class="stat-value" v-if="interventionsMonthTotal > 0">
                                <span>{{ interventionsMonthTotal }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-wrench"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>

                        <!-- Coût du mois -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.maintenanceCostThisMonth') }}</h5>
                            <div class="stat-value" v-if="maintenanceCostMonth > 0">
                                <span>{{ formatCurrency(maintenanceCostMonth) }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-coin"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>

                        <!-- Alertes actives -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.activeAlerts') }}</h5>
                            <div class="stat-value" v-if="alertsTotal > 0">
                                <span>{{ alertsTotal }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-bell"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Row 2: 3 cards -->
                    <section class="dashboard-row-2">
                        <!-- Interventions par type -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.interventionsByType') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="interventionsByTypeChart"></canvas>
                            </div>
                        </div>

                        <!-- Véhicules par statut -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.vehiclesByStatus') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="vehiclesByStatusChart"></canvas>
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
  DoughnutController,
  BarController, 
  LineController, 
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement, 
  Tooltip, 
  Legend,
  Filler
} from "chart.js";
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';
import { getDashboardRoute } from '../utils/navigation.js';

Chart.register(
  PieController,
  DoughnutController, 
  BarController, 
  LineController,
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement, 
  Tooltip, 
  Legend,
  Filler
);

export default {
  name: "AgentDashboard",
  components: { Sidebar },
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  computed: {
    menuItems() {
      return [
        { label: this.t('nav.dashboard'), to: getDashboardRoute() },
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
      interventionsByTypeChart: null,
      vehiclesByStatusChart: null,
      maintenanceCostChart: null,
      
      // Data
      interventionsMonthTotal: 0,
      maintenanceCostMonth: 0,
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
        if (!token) {
          this.$router.push("/login");
          return;
        }
        
        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        if (res.data) {
          this.user = res.data;
          this.userName = res.data.prenom || res.data.nom || 'Utilisateur';
        }
      } catch (error) {
        console.error('Error loading user:', error.response?.data || error.message);
        localStorage.removeItem("token");
        this.$router.push("/login");
      }
    },

    async loadAllStats() {
      try {
        await Promise.allSettled([
          this.loadCarsStats(),
          this.loadInterventionsMonthTotal(),
          this.loadMaintenanceCostMonth(),
          this.loadMaintenanceCostByMonth(),
          this.loadInterventionsByType(),
          this.loadVehiclesByStatus(),
          this.loadAlertsStats(),
          this.loadInterventionsHistory()
        ]);
      } catch (error) {
        console.error('Error loading dashboard stats:', error);
      }
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

    async loadMaintenanceCostMonth() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/maintenance/cost-current-month", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.maintenanceCostMonth = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadMaintenanceCostError'), err);
        this.maintenanceCostMonth = 0;
      }
    },

    async loadCarsStats() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/voitures/count-total", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const total = res.data.total || 0;

        this.$nextTick(() => {
          if (!this.$refs.carsChart) return;
          if (this.carsChart) this.carsChart.destroy();
          this.carsChart = new Chart(this.$refs.carsChart, {
            type: "doughnut",
            data: {
              labels: [this.t('dashboard.vehicles')],
              datasets: [{
                data: [total, Math.max(0, 100 - total)],
                backgroundColor: ["#BFCC94", "#E5E5E5"],
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
                    label: (ctx) => `${this.t('dashboard.total')}: ${total}`
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

    async loadInterventionsByType() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/count-by-type", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.type || 'Autre');
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (!this.$refs.interventionsByTypeChart) return;
          if (this.interventionsByTypeChart) this.interventionsByTypeChart.destroy();
          this.interventionsByTypeChart = new Chart(this.$refs.interventionsByTypeChart, {
            type: "doughnut",
            data: {
              labels,
              datasets: [{
                data: values,
                backgroundColor: ["#BFCC94", "#D2E1EE", "#748BAA", "#546A88", "#B4CDED"],
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
        console.error(this.t('errors.loadInterventionsError'), err);
      }
    },

    async loadVehiclesByStatus() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/voitures/count-by-status", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.statut);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (!this.$refs.vehiclesByStatusChart) return;
          if (this.vehiclesByStatusChart) this.vehiclesByStatusChart.destroy();
          this.vehiclesByStatusChart = new Chart(this.$refs.vehiclesByStatusChart, {
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
        console.error(this.t('errors.loadVehiclesStatsError'), err);
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
          if (!this.$refs.maintenanceCostChart) return;
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
      return new Intl.NumberFormat('fr-TN', {
        style: 'currency',
        currency: 'TND',
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
    if (this.interventionsByTypeChart) this.interventionsByTypeChart.destroy();
    if (this.vehiclesByStatusChart) this.vehiclesByStatusChart.destroy();
    if (this.maintenanceCostChart) this.maintenanceCostChart.destroy();
  }
};
</script>