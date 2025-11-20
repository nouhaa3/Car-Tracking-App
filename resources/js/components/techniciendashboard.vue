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
                        <!-- Interventions assignées -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.assignedInterventions') }}</h5>
                            <div class="stat-value" v-if="assignedInterventions > 0">
                                <span>{{ assignedInterventions }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-wrench"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>

                        <!-- Interventions complétées -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.completedInterventions') }}</h5>
                            <div class="stat-value" v-if="completedInterventions > 0">
                                <span>{{ completedInterventions }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-check-circle"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>

                        <!-- Interventions en cours -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.inProgressInterventions') }}</h5>
                            <div class="stat-value" v-if="inProgressInterventions > 0">
                                <span>{{ inProgressInterventions }}</span>
                            </div>
                            <div class="no-data" v-else>
                                <i class="bi bi-hourglass-split"></i>
                                <p>{{ t('dashboard.noData') }}</p>
                            </div>
                        </div>

                        <!-- Alertes techniques -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.technicalAlerts') }}</h5>
                            <div class="stat-value" v-if="technicalAlerts > 0">
                                <span>{{ technicalAlerts }}</span>
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
                            <h5>{{ t('dashboard.myInterventionsByType') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="interventionsByTypeChart"></canvas>
                            </div>
                        </div>

                        <!-- Statut des interventions -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.interventionStatus') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="interventionStatusChart"></canvas>
                            </div>
                        </div>

                        <!-- Tendance mensuelle -->
                        <div class="card chart-card">
                            <h5>{{ t('dashboard.monthlyTrend') }}</h5>
                            <div class="chart-wrapper">
                                <canvas ref="monthlyTrendChart"></canvas>
                            </div>
                        </div>
                    </section>

                    <!-- Row 3: 2 cards -->
                    <section class="dashboard-row-3">
                        <!-- Interventions urgentes -->
                        <div class="card chart-card urgent-alerts">
                            <h5>{{ t('dashboard.urgentInterventions') }}</h5>
                            <div class="alerts-list" v-if="urgentInterventions.length > 0">
                                <div 
                                    v-for="intervention in urgentInterventions" 
                                    :key="intervention.id"
                                    class="alert-item"
                                >
                                    <div class="alert-icon">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>
                                    <div class="alert-details">
                                        <span class="alert-type">{{ intervention.type }}</span>
                                        <span class="alert-vehicle">{{ intervention.vehicule }}</span>
                                        <span class="alert-meta">
                                            {{ formatDate(intervention.date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="no-data">
                                <i class="bi bi-check-circle"></i>
                                <p>{{ t('dashboard.noUrgentInterventions') }}</p>
                            </div>
                        </div>

                        <!-- Historique récent -->
                        <div class="card chart-card intervention-history">
                            <h5>{{ t('dashboard.myRecentWork') }}</h5>
                            <div class="history-list" v-if="recentWork.length > 0">
                                <div 
                                    v-for="work in recentWork" 
                                    :key="work.id"
                                    class="history-item"
                                >
                                    <div class="history-icon">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <div class="history-details">
                                        <span class="history-type">{{ work.type }}</span>
                                        <span class="history-vehicle">{{ work.vehicule }}</span>
                                        <span class="history-meta">
                                            {{ formatDate(work.date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="no-data">
                                <i class="bi bi-inbox"></i>
                                <p>{{ t('dashboard.noRecentWork') }}</p>
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
  name: "TechnicienDashboard",
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
      interventionsByTypeChart: null,
      interventionStatusChart: null,
      monthlyTrendChart: null,
      
      // Data
      assignedInterventions: 0,
      completedInterventions: 0,
      inProgressInterventions: 0,
      technicalAlerts: 0,
      urgentInterventions: [],
      recentWork: [],
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
          this.loadAssignedInterventions(),
          this.loadCompletedInterventions(),
          this.loadInProgressInterventions(),
          this.loadTechnicalAlerts(),
          this.loadInterventionsByType(),
          this.loadInterventionStatus(),
          this.loadMonthlyTrend(),
          this.loadUrgentInterventions(),
          this.loadRecentWork()
        ]);
      } catch (error) {
        console.error('Error loading dashboard stats:', error);
      }
    },

    async loadAssignedInterventions() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/assigned-count", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.assignedInterventions = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadInterventionsError'), err);
        this.assignedInterventions = 0;
      }
    },

    async loadCompletedInterventions() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/completed-count", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.completedInterventions = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadInterventionsError'), err);
        this.completedInterventions = 0;
      }
    },

    async loadInProgressInterventions() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/in-progress-count", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.inProgressInterventions = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadInterventionsError'), err);
        this.inProgressInterventions = 0;
      }
    },

    async loadTechnicalAlerts() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/alertes/technical-count", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.technicalAlerts = res.data.total || 0;
      } catch (err) {
        console.error(this.t('errors.loadAlertsStatsError'), err);
        this.technicalAlerts = 0;
      }
    },

    async loadInterventionsByType() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/my-count-by-type", {
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

    async loadInterventionStatus() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/my-status-count", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.statut);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (!this.$refs.interventionStatusChart) return;
          if (this.interventionStatusChart) this.interventionStatusChart.destroy();
          this.interventionStatusChart = new Chart(this.$refs.interventionStatusChart, {
            type: "bar",
            data: {
              labels,
              datasets: [{
                label: this.t('charts.interventionsLabel'),
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
        console.error(this.t('errors.loadInterventionsError'), err);
      }
    },

    async loadMonthlyTrend() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/my-monthly-trend", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        const labels = res.data.map(item => item.month);
        const values = res.data.map(item => item.total);

        this.$nextTick(() => {
          if (!this.$refs.monthlyTrendChart) return;
          if (this.monthlyTrendChart) this.monthlyTrendChart.destroy();
          this.monthlyTrendChart = new Chart(this.$refs.monthlyTrendChart, {
            type: "line",
            data: {
              labels,
              datasets: [{
                label: this.t('charts.interventionsLabel'),
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
        console.error(this.t('errors.loadInterventionsError'), err);
      }
    },

    async loadUrgentInterventions() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/my-urgent", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.urgentInterventions = res.data || [];
      } catch (err) {
        console.error(this.t('errors.loadInterventionsError'), err);
        this.urgentInterventions = [];
      }
    },

    async loadRecentWork() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/interventions/my-recent-work", {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        this.recentWork = res.data || [];
      } catch (err) {
        console.error(this.t('errors.loadInterventionsError'), err);
        this.recentWork = [];
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
    if (this.interventionsByTypeChart) this.interventionsByTypeChart.destroy();
    if (this.interventionStatusChart) this.interventionStatusChart.destroy();
    if (this.monthlyTrendChart) this.monthlyTrendChart.destroy();
  }
};
</script>