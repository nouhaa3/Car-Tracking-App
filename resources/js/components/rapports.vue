<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />
      
      <div class="main-content">
        <!-- Profile Float -->
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.userAvatar')" class="avatar" />
        </router-link>

        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <!-- Navbar -->
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

        <div class="profile-wrapper">
          <!-- Header -->
          <div class="page-header">
          <div class="header-left">
            <h1>{{ t('reports.title') }}</h1>
            <p>{{ t('reports.subtitle') }}</p>
          </div>
          <div class="header-actions">
            <div class="stat-badge">
              {{ totalVoitures }} {{ t('reports.vehicles') }}
            </div>
            <div class="stat-badge">
              {{ totalInterventions }} {{ t('reports.interventions') }}
            </div>
            <div class="stat-badge">
              {{ totalUsers }} {{ t('reports.users') }}
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('common.loading') }}</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <h3>{{ t('errors.loadError') }}</h3>
          <p>{{ error }}</p>
          <button @click="fetchData" class="retry-btn">
            <i class="bi bi-arrow-clockwise"></i> {{ t('common.retry') }}
          </button>
        </div>

        <!-- Reports Grid -->
        <div v-else class="reports-container">
          <!-- Quick Stats Cards -->
          <div class="stats-overview">
            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #748BAA, #546A88);">
                <i class="bi bi-car-front-fill"></i>
              </div>
              <div class="stat-details">
                <h3>{{ totalVoitures }}</h3>
                <p>{{ t('reports.totalVehicles') }}</p>
                <span class="stat-breakdown">
                  {{ stats.voitures.disponibles }} {{ t('reports.available') }}, 
                  {{ stats.voitures.maintenance }} {{ t('reports.inMaintenance') }}
                </span>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #344966, #0D1821);">
                <i class="bi bi-tools"></i>
              </div>
              <div class="stat-details">
                <h3>{{ totalInterventions }}</h3>
                <p>{{ t('reports.interventions') }}</p>
                <span class="stat-breakdown">
                  {{ stats.interventions.enCours }} {{ t('reports.inProgress') }}, 
                  {{ stats.interventions.terminees }} {{ t('reports.completed') }}
                </span>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #B4CDED, #93B7DB);">
                <i class="bi bi-cash-stack"></i>
              </div>
              <div class="stat-details">
                <h3>{{ formatCurrency(stats.coutTotal) }}</h3>
                <p>{{ t('reports.totalCost') }}</p>
                <span class="stat-breakdown">{{ t('reports.thisMonthInterventions') }}</span>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-icon" style="background: linear-gradient(135deg, #546A88, #344966);">
                <i class="bi bi-people-fill"></i>
              </div>
              <div class="stat-details">
                <h3>{{ totalUsers }}</h3>
                <p>{{ t('reports.users') }}</p>
                <span class="stat-breakdown">
                  {{ stats.users.admins }} {{ t('reports.admins') }}, 
                  {{ stats.users.agents }} {{ t('reports.agents') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Report Types -->
          <div class="reports-grid">
            <!-- Rapport Véhicules -->
            <div class="report-card">
              <div class="report-header">
                <div class="report-icon" style="background: linear-gradient(135deg, #748BAA, #546A88);">
                  <i class="bi bi-car-front-fill"></i>
                </div>
                <div class="report-title-section">
                  <h3>{{ t('reports.types.vehicles.title') }}</h3>
                  <p>{{ t('reports.types.vehicles.description') }}</p>
                </div>
              </div>
              <div class="report-content">
                <div class="report-info">
                  <div class="info-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ t('reports.format') }}: PDF / CSV</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-clock"></i>
                    <span>{{ t('reports.updated') }}: {{ t('reports.realTime') }}</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-card-list"></i>
                    <span>{{ totalVoitures }} {{ t('reports.entries') }}</span>
                  </div>
                </div>
                <div class="report-actions">
                  <button 
                    @click="downloadReport('voitures', 'pdf')" 
                    class="download-btn pdf-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </button>
                  <button 
                    @click="downloadReport('voitures', 'excel')" 
                    class="download-btn excel-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                  </button>
                  <button 
                    @click="previewReport('voitures')" 
                    class="download-btn preview-btn"
                  >
                    <i class="bi bi-eye"></i> {{ t('reports.preview') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Rapport Interventions -->
            <div class="report-card">
              <div class="report-header">
                <div class="report-icon" style="background: linear-gradient(135deg, #344966, #0D1821);">
                  <i class="bi bi-tools"></i>
                </div>
                <div class="report-title-section">
                  <h3>{{ t('reports.types.interventions.title') }}</h3>
                  <p>{{ t('reports.types.interventions.description') }}</p>
                </div>
              </div>
              <div class="report-content">
                <div class="report-info">
                  <div class="info-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ t('reports.format') }}: PDF / CSV</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-clock"></i>
                    <span>{{ t('reports.updated') }}: {{ t('reports.realTime') }}</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-card-list"></i>
                    <span>{{ totalInterventions }} {{ t('reports.entries') }}</span>
                  </div>
                </div>
                <div class="report-actions">
                  <button 
                    @click="downloadReport('interventions', 'pdf')" 
                    class="download-btn pdf-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </button>
                  <button 
                    @click="downloadReport('interventions', 'excel')" 
                    class="download-btn excel-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                  </button>
                  <button 
                    @click="previewReport('interventions')" 
                    class="download-btn preview-btn"
                  >
                    <i class="bi bi-eye"></i> {{ t('reports.preview') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Rapport Utilisateurs -->
            <div class="report-card">
              <div class="report-header">
                <div class="report-icon" style="background: linear-gradient(135deg, #546A88, #344966);">
                  <i class="bi bi-people-fill"></i>
                </div>
                <div class="report-title-section">
                  <h3>{{ t('reports.types.users.title') }}</h3>
                  <p>{{ t('reports.types.users.description') }}</p>
                </div>
              </div>
              <div class="report-content">
                <div class="report-info">
                  <div class="info-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ t('reports.format') }}: PDF / CSV</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-clock"></i>
                    <span>{{ t('reports.updated') }}: {{ t('reports.realTime') }}</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-card-list"></i>
                    <span>{{ totalUsers }} {{ t('reports.entries') }}</span>
                  </div>
                </div>
                <div class="report-actions">
                  <button 
                    @click="downloadReport('users', 'pdf')" 
                    class="download-btn pdf-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </button>
                  <button 
                    @click="downloadReport('users', 'excel')" 
                    class="download-btn excel-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                  </button>
                  <button 
                    @click="previewReport('users')" 
                    class="download-btn preview-btn"
                  >
                    <i class="bi bi-eye"></i> {{ t('reports.preview') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Rapport Financier -->
            <div class="report-card">
              <div class="report-header">
                <div class="report-icon" style="background: linear-gradient(135deg, #B4CDED, #93B7DB);">
                  <i class="bi bi-cash-stack"></i>
                </div>
                <div class="report-title-section">
                  <h3>{{ t('reports.types.financial.title') }}</h3>
                  <p>{{ t('reports.types.financial.description') }}</p>
                </div>
              </div>
              <div class="report-content">
                <div class="report-info">
                  <div class="info-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ t('reports.format') }}: PDF / CSV</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-clock"></i>
                    <span>{{ t('reports.period') }}: {{ t('reports.monthly') }}</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-currency-dollar"></i>
                    <span>{{ formatCurrency(stats.coutTotal) }}</span>
                  </div>
                </div>
                <div class="report-actions">
                  <button 
                    @click="downloadReport('financier', 'pdf')" 
                    class="download-btn pdf-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </button>
                  <button 
                    @click="downloadReport('financier', 'excel')" 
                    class="download-btn excel-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                  </button>
                  <button 
                    @click="previewReport('financier')" 
                    class="download-btn preview-btn"
                  >
                    <i class="bi bi-eye"></i> {{ t('reports.preview') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Rapport Complet -->
            <div class="report-card featured">
              <div class="featured-badge">
                <i class="bi bi-star-fill"></i> {{ t('reports.types.complete.title') }}
              </div>
              <div class="report-header">
                <div class="report-icon" style="background: linear-gradient(135deg, #748BAA, #344966);">
                  <i class="bi bi-file-earmark-bar-graph-fill"></i>
                </div>
                <div class="report-title-section">
                  <h3>{{ t('reports.types.complete.title') }}</h3>
                  <p>{{ t('reports.types.complete.description') }}</p>
                </div>
              </div>
              <div class="report-content">
                <div class="report-info">
                  <div class="info-item">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>{{ t('reports.format') }}: PDF</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-layers"></i>
                    <span>{{ t('reports.multiSection') }}</span>
                  </div>
                  <div class="info-item">
                    <i class="bi bi-graph-up"></i>
                    <span>{{ t('reports.withCharts') }}</span>
                  </div>
                </div>
                <div class="report-actions">
                  <button 
                    @click="downloadReport('complet', 'pdf')" 
                    class="download-btn featured-btn"
                    :disabled="downloading"
                  >
                    <i class="bi bi-download"></i> {{ t('reports.downloadComplete') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Rapport Personnalisé -->
            <div class="report-card custom">
              <div class="report-header">
                <div class="report-icon" style="background: linear-gradient(135deg, #546A88, #344966);">
                  <i class="bi bi-sliders"></i>
                </div>
                <div class="report-title-section">
                  <h3>{{ t('reports.types.custom.title') }}</h3>
                  <p>{{ t('reports.types.custom.description') }}</p>
                </div>
              </div>
              <div class="report-content">
                <div class="custom-filters">
                  <div class="filter-group">
                    <label><i class="bi bi-calendar"></i> {{ t('reports.period') }}</label>
                    <select v-model="customReport.period" class="filter-select">
                      <option value="7days">{{ t('reports.periods.last7Days') }}</option>
                      <option value="30days">{{ t('reports.periods.last30Days') }}</option>
                      <option value="3months">{{ t('reports.periods.last3Months') }}</option>
                      <option value="year">{{ t('reports.periods.thisYear') }}</option>
                      <option value="all">{{ t('reports.periods.allData') }}</option>
                    </select>
                  </div>
                  <div class="filter-group">
                    <label><i class="bi bi-funnel"></i> {{ t('reports.dataType') }}</label>
                    <div class="checkbox-group">
                      <label class="checkbox-label">
                        <input type="checkbox" v-model="customReport.includeVoitures" />
                        <span>{{ t('reports.types.vehicles.title') }}</span>
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" v-model="customReport.includeInterventions" />
                        <span>{{ t('reports.types.interventions.title') }}</span>
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" v-model="customReport.includeUsers" />
                        <span>{{ t('reports.types.users.title') }}</span>
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" v-model="customReport.includeFinancier" />
                        <span>{{ t('reports.financialData') }}</span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="report-actions">
                  <button 
                    @click="downloadCustomReport('pdf')" 
                    class="download-btn pdf-btn"
                    :disabled="downloading || !isCustomReportValid"
                  >
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                  </button>
                  <button 
                    @click="downloadCustomReport('excel')" 
                    class="download-btn excel-btn"
                    :disabled="downloading || !isCustomReportValid"
                  >
                    <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Download Progress Modal -->
        <div v-if="downloading" class="download-modal">
          <div class="download-modal-content">
            <div class="download-spinner"></div>
            <h3>{{ t('reports.generating') }}</h3>
            <p>{{ t('reports.generatingMessage') }}</p>
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: downloadProgress + '%' }"></div>
            </div>
            <span class="progress-text">{{ downloadProgress }}%</span>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { inject, ref, reactive, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from 'vue-i18n';
import Sidebar from "./Sidebar.vue";
import axios from "axios";
import alerts from '@/utils/alerts';

export default {
  name: "Rapports",
  components: {
    Sidebar,
  },
  setup() {
    const theme = inject("theme");
    const router = useRouter();
    const { t } = useI18n();
    const user = ref(null);
    const isExpanded = ref(false);

    const loading = ref(true);
    const error = ref(null);
    const downloading = ref(false);
    const downloadProgress = ref(0);

    // Menu items for navbar
    const menuItems = [
      { label: t('nav.dashboard'), to: '/admindashboard' },
      { label: t('nav.catalog'), to: '/voitures/cataloguevoitures' },
      { label: t('nav.interventions'), to: '/interventions/catalogue' },
      { label: t('nav.alerts'), to: '/alertes' },
    ];

    const totalVoitures = ref(0);
    const totalInterventions = ref(0);
    const totalUsers = ref(0);

    const stats = reactive({
      voitures: {
        disponibles: 0,
        maintenance: 0,
        loues: 0,
      },
      interventions: {
        enCours: 0,
        terminees: 0,
        planifiees: 0,
      },
      coutTotal: 0,
      users: {
        admins: 0,
        agents: 0,
        techniciens: 0,
      },
    });

    const customReport = reactive({
      period: "30days",
      includeVoitures: true,
      includeInterventions: true,
      includeUsers: false,
      includeFinancier: true,
    });

    const isCustomReportValid = computed(() => {
      return (
        customReport.includeVoitures ||
        customReport.includeInterventions ||
        customReport.includeUsers ||
        customReport.includeFinancier
      );
    });

    const fetchData = async () => {
      loading.value = true;
      error.value = null;

      try {
        const token = localStorage.getItem("token");
        if (!token) {
          console.error(t('errors.noToken'));
          router.push("/login");
          return;
        }

        console.log("Token found, fetching user data...");

        // Récupérer l'utilisateur
        const userResponse = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` },
        });
        user.value = userResponse.data;
        
        const userName = user.value.nom && user.value.prenom 
          ? `${user.value.prenom} ${user.value.nom}` 
          : user.value.email;
        const userRole = (user.value.role?.nomRole || "unknown").toLowerCase();
        
        console.log("User loaded:", userName, "| Role:", userRole);

        // Vérifier si l'utilisateur est admin (insensible à la casse)
        if (userRole !== "admin") {
          console.warn(t('errors.accessDenied'));
          // Rediriger vers le dashboard approprié selon le rôle
          if (userRole === "agent") {
            router.push("/agentdashboard");
          } else if (userRole === "technicien") {
            router.push("/techniciendashboard");
          } else {
            router.push("/login");
          }
          return;
        }

        console.log("Admin access granted, loading reports data...");

        // Récupérer les données des véhicules
        const voituresResponse = await axios.get("http://127.0.0.1:8000/api/voitures", {
          headers: { Authorization: `Bearer ${token}` },
        });
        const voitures = voituresResponse.data;
        totalVoitures.value = voitures.length;
        stats.voitures.disponibles = voitures.filter(v => v.statut === "En boutique").length;
        stats.voitures.maintenance = voitures.filter(v => v.statut === "En maintenance").length;
        stats.voitures.loues = voitures.filter(v => v.statut === "Loué").length;

        // Récupérer les données des interventions
        const interventionsResponse = await axios.get("http://127.0.0.1:8000/api/interventions", {
          headers: { Authorization: `Bearer ${token}` },
        });
        const interventions = interventionsResponse.data;
        totalInterventions.value = interventions.length;
        // Interventions don't have a statut column - count by date or assigned status
        const today = new Date();
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();
        stats.interventions.enCours = interventions.filter(i => {
          const interventionDate = new Date(i.date);
          return interventionDate.getMonth() === currentMonth && interventionDate.getFullYear() === currentYear;
        }).length;
        stats.interventions.terminees = interventions.filter(i => {
          const interventionDate = new Date(i.date);
          return interventionDate < today;
        }).length;
        stats.interventions.planifiees = interventions.filter(i => {
          const interventionDate = new Date(i.date);
          return interventionDate > today;
        }).length;

        // Calculer le coût total
        stats.coutTotal = interventions.reduce((sum, i) => sum + (parseFloat(i.cout) || 0), 0);

        // Récupérer les données des utilisateurs
        const usersResponse = await axios.get("http://127.0.0.1:8000/api/users", {
          headers: { Authorization: `Bearer ${token}` },
        });
        const users = usersResponse.data;
        totalUsers.value = users.length;
        stats.users.admins = users.filter(u => u.role?.nomRole?.toLowerCase() === "admin").length;
        stats.users.agents = users.filter(u => u.role?.nomRole?.toLowerCase() === "agent").length;
        stats.users.techniciens = users.filter(u => u.role?.nomRole?.toLowerCase() === "technicien").length;

        loading.value = false;
        console.log("All data loaded successfully!");
      } catch (err) {
        console.error(t('errors.loadDataError'), err);
        console.error("Error details:", err.response?.data || err.message);
        
        // Si erreur 401 (non autorisé), rediriger vers login
        if (err.response?.status === 401) {
          console.error(t('errors.sessionExpired'));
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          router.push("/login");
          return;
        }
        
        error.value = t('errors.loadDataError');
        loading.value = false;
      }
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat("fr-TN", {
        style: "currency",
        currency: "TND",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(amount);
    };

    const downloadReport = async (type, format) => {
      downloading.value = true;
      downloadProgress.value = 0;

      // Simuler le progrès du téléchargement
      const progressInterval = setInterval(() => {
        if (downloadProgress.value < 90) {
          downloadProgress.value += 10;
        }
      }, 200);

      try {
        const token = localStorage.getItem("token");
        const response = await axios.get(
          `http://localhost:8000/api/rapports/${type}/${format}`,
          {
            headers: { Authorization: `Bearer ${token}` },
            responseType: "blob",
          }
        );

        clearInterval(progressInterval);
        downloadProgress.value = 100;

        // Déterminer le type MIME et l'extension appropriés
        let mimeType = 'text/csv';
        let extension = 'csv';
        
        // Pour l'instant, le backend retourne toujours du CSV
        // Donc on garde csv comme extension même si format demandé est excel
        if (format === 'excel') {
          // Le fichier est en fait un CSV, pas un vrai Excel
          extension = 'csv';
        } else if (format === 'pdf') {
          mimeType = 'application/pdf';
          extension = 'pdf';
        }

        // Créer un blob avec le bon type MIME
        const blob = new Blob([response.data], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        
        const date = new Date().toISOString().split("T")[0];
        link.setAttribute("download", `${t('reports.filenamePrefix')}_${type}_${date}.${extension}`);
        
        document.body.appendChild(link);
        link.click();
        link.remove();
        
        // Libérer l'URL après un court délai
        setTimeout(() => {
          window.URL.revokeObjectURL(url);
          downloading.value = false;
          downloadProgress.value = 0;
        }, 500);
      } catch (err) {
        clearInterval(progressInterval);
        console.error(t('errors.downloadError'), err);
        await alerts.alertError(t('common.error'), t('errors.downloadReportError'));
        downloading.value = false;
        downloadProgress.value = 0;
      }
    };

    const downloadCustomReport = async (format) => {
      if (!isCustomReportValid.value) {
        await alerts.alertWarning(t('common.warning'), t('reports.errors.selectData'));
        return;
      }

      downloading.value = true;
      downloadProgress.value = 0;

      const progressInterval = setInterval(() => {
        if (downloadProgress.value < 90) {
          downloadProgress.value += 10;
        }
      }, 200);

      try {
        const token = localStorage.getItem("token");
        const response = await axios.post(
          `http://localhost:8000/api/rapports/custom/${format}`,
          customReport,
          {
            headers: { Authorization: `Bearer ${token}` },
            responseType: "blob",
          }
        );

        clearInterval(progressInterval);
        downloadProgress.value = 100;

        // Déterminer le type MIME et l'extension appropriés
        let mimeType = 'text/csv';
        let extension = 'csv';
        
        if (format === 'excel') {
          extension = 'csv';
        } else if (format === 'pdf') {
          mimeType = 'application/pdf';
          extension = 'pdf';
        }

        const blob = new Blob([response.data], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        
        const date = new Date().toISOString().split("T")[0];
        link.setAttribute("download", `${t('reports.filenamePrefix')}_custom_${date}.${extension}`);
        
        document.body.appendChild(link);
        link.click();
        link.remove();

        setTimeout(() => {
          window.URL.revokeObjectURL(url);
          downloading.value = false;
          downloadProgress.value = 0;
        }, 500);
      } catch (err) {
        clearInterval(progressInterval);
        console.error(t('errors.downloadError'), err);
        await alerts.alertError(t('common.error'), t('errors.downloadCustomReportError'));
        downloading.value = false;
        downloadProgress.value = 0;
      }
    };

    const previewReport = (type) => {
      alerts.alertInfo(t('common.comingSoon'), t('reports.previewComingSoon', { type: type }));
    };

    onMounted(() => {
      fetchData();
    });

    return {
      theme,
      t,
      user,
      isExpanded,
      menuItems,
      loading,
      error,
      downloading,
      downloadProgress,
      totalVoitures,
      totalInterventions,
      totalUsers,
      stats,
      customReport,
      isCustomReportValid,
      fetchData,
      formatCurrency,
      downloadReport,
      downloadCustomReport,
      previewReport,
    };
  },
};
</script>