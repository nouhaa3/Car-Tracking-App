<template>
    <div :class="['home-page', { dark: isDark }]">
        <div class="layout">
            <!-- Sidebar -->
            <Sidebar :class="{ expanded: isExpanded }" />
            <div class="main-content">

                <!-- Theme Toggle Button 
                <button @click="toggleTheme" class="theme-btn">
                    <i v-if="isDark" class="bi bi-sun"></i>
                    <i v-else class="bi bi-moon-stars"></i>
                </button>-->

                <router-link to="/profile" class="profile-float" v-if="user">
                    <img :src="user.avatar || '/images/avatar.png'" alt="User" class="avatar" />
                </router-link>


                <!-- Bootstrap Icons -->
                <link
                rel="stylesheet"
                href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
                />

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

                <!-- Dashboard -->
                <div class="dashboard-container">
                <!-- Header -->
                    <header class="dashboard-header">
                        <h4>Bonjour {{ userName }},</h4>
                    </header>

                    <!-- Top stats -->
                    <section class="dashboard-top">
                        <div class="card chart-card">
                        <h5>Total des véhicules</h5>
                        <div class="chart-wrapper">
                            <canvas ref="carsChart"></canvas>
                        </div>
                        </div>
                        <div class="card chart-card">
                        <h5>Utilisateurs actifs</h5>
                        <div class="chart-wrapper">
                            <canvas ref="usersChart"></canvas>
                        </div>
                        </div>
                        <div class="card chart-card">
                        <h5>Coût global de maintenance</h5>
                        </div>
                        <div class="card chart-card">
                        <h5>Coût de maintenance par mois</h5>
                        </div>
                    </section>

                    <!-- Middle + Bottom -->
                    <section class="dashboard-main">
                        <!-- Left -->
                        <div class="card">
                        <h5>Exporter le rapport</h5>
                        </div>

                        <!-- Center -->
                        <div class="center-panel">
                        <div class="card large">
                            Top 5 des véhicules les plus coûteux en entretien
                        </div>
                        </div>

                        <!-- Right -->
                        <div class="right-panel">
                        <div class="card large">
                            <h3>Alertes globales</h3>
                            <p>Total non traitées</p>
                            <p>Liste des plus urgentes</p>
                        </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- Logout button -->
            <button @click="logout" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            </button>
        </div>
    </div>
</template>

<script>
import Sidebar from './sidebar.vue';
import axios from "axios";
import { Chart, PieController, ArcElement, Tooltip, Legend } from "chart.js";

Chart.register(PieController, ArcElement, Tooltip, Legend);

export default {
  name: "AdminDashboard",
  components: { Sidebar },
  data() {
    return {
      userName: "",
      chart: null,
      user:null,
      usersChart: null,
      isDark: false,
      isExpanded: false,
      menuItems: [
        { label: "Acceuil", to: "/" },
        { label: "Tableau de bord", to: "/admindashboard" },
        { label: "Catalogue", to: "/voitures/cataloguevoitures" },
        { label: "Interventions", to: "/interventions" },
        { label: "Alertes", to: "/alertes" },
      ],
    };
  },
  async mounted() {
    // Get logged user
    try {
        const token = localStorage.getItem("token");
        const res = await axios.get("http://127.0.0.1:8000/api/me", {
        headers: { Authorization: `Bearer ${token}` }
        });

        console.log("Utilisateur connecté :", res.data);
        this.user = res.data; // si tu veux stocker dans data()
        this.userName = `${res.data.prenom}`;
    } catch (error) {
        console.error("Erreur récupération user:", error);
        this.$router.push("/login"); // si token invalide -> retour login
    }

    // Get cars stats
    try {
      const res = await axios.get("http://127.0.0.1:8000/api/voitures/count-by-status", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });

      console.log("Car stats response:", res.data);

      const labels = res.data.map(item => item.statut);
      const values = res.data.map(item => item.total);

      this.$nextTick(() => {
        this.chart = new Chart(this.$refs.carsChart, {
          type: "doughnut",
          data: {
            labels,
            datasets: [
              {
                data: values,
                backgroundColor: ["#6d3316", "#e46e25", "#FF6384"],
                cutout: "60%",
              },
            ],
          },
          options: {
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: (ctx) => ctx.label,
                },
              },
            },
          },
        });
      });
    } catch (err) {
      console.error("Failed to load car stats", err);
    }

    // Get users stats
    try {
      const usersRes = await axios.get("http://127.0.0.1:8000/api/users/count-by-role", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });

      console.log("Users stats response:", usersRes.data);

      const userLabels = usersRes.data.map(item => item.role);
      const userValues = usersRes.data.map(item => item.total);

      this.$nextTick(() => {
        this.usersChart = new Chart(this.$refs.usersChart, {
          type: "doughnut",
          data: {
            labels: userLabels,
            datasets: [
              {
                data: userValues,
                backgroundColor: ["#6d3316", "#e46e25", "#FF6384", "#36A2EB"],
                cutout: "60%",
              },
            ],
          },
          options: {
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: (ctx) => ctx.label,
                },
              },
            },
          },
        });
      });
    } catch (err) {
      console.error("Failed to load users stats", err);
    }

    // Load theme
    const saved = localStorage.getItem("theme");
    if (saved) this.isDark = saved === "dark";
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem("theme", this.isDark ? "dark" : "light");
    },
    async logout() {
      try {
        await axios.post(
          "http://127.0.0.1:8000/api/logout",
          {},
          { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
        );
      } catch (error) {
        console.warn("Logout failed or token expired:", error);
      } finally {
        localStorage.removeItem("token");
        this.$router.push({ name: "Login" });
      }
    },
  },
};
</script>
