<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('user.avatarAlt')" class="avatar" />
        </router-link>

        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <nav class="navbar mb-4">
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

        <div class="catalogue-container">
          <div class="catalogue-left">
            <!-- Header with search and actions -->
            <div class="catalogue-header">
              <div class="search-wrapper">
                <input
                  v-model="searchQuery"
                  type="text"
                  :placeholder="t('alerts.searchPlaceholder')"
                  class="search-input"
                />
                <button 
                  v-if="searchQuery" 
                  @click="searchQuery = ''"
                  class="clear-search-btn"
                >
                  <i class="bi bi-x"></i>
                </button>
              </div>
              <div class="header-actions">
                <button @click="toggleFilter" class="filter-toggle-btn" :class="{ active: showFilter }">
                  <i class="bi bi-funnel-fill"></i>
                  {{ t('common.filters') }}
                </button>
              </div>
            </div>

            <!-- Statistics Summary -->
            <div v-if="!isLoading && alertes.length > 0" class="stats-summary">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="bi bi-bell"></i>
                </div>
                <div class="stat-content">
                  <span class="stat-value">{{ alertes.length }}</span>
                  <span class="stat-label">{{ t('alerts.totalAlerts') }}</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stat-content">
                  <span class="stat-value">{{ stats.byPriority.critique }}</span>
                  <span class="stat-label">{{ t('alerts.critical') }}</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="bi bi-exclamation-circle-fill"></i>
                </div>
                <div class="stat-content">
                  <span class="stat-value">{{ stats.byPriority.haute }}</span>
                  <span class="stat-label">{{ t('alerts.highPriority') }}</span>
                </div>
              </div>
            </div>

            <!-- Loading state -->
            <div v-if="isLoading" class="loading-container">
              <div class="spinner"></div>
              <p>{{ t('common.loading') }}</p>
            </div>

            <!-- Error state -->
            <div v-else-if="error" class="error-container">
              <i class="bi bi-exclamation-triangle"></i>
              <p>{{ error }}</p>
              <button @click="fetchAlertes" class="retry-btn">{{ t('common.retry') }}</button>
            </div>

            <!-- Empty state -->
            <div v-else-if="filteredAlertes.length === 0" class="empty-state">
              <i class="bi bi-bell-slash"></i>
              <h3>{{ t('alerts.noAlertsFound') }}</h3>
              <p v-if="searchQuery || isFilterActive">
                {{ t('alerts.tryAdjustingFilters') }}
              </p>
              <p v-else>
                {{ t('alerts.noPendingAlerts') }}
              </p>
              <button @click="generateAlerts" class="add-car-btn" :disabled="isGenerating">
                {{ isGenerating ? t('alerts.generating') : t('alerts.generateAlerts') }}
              </button>
            </div>

            <!-- Alertes grid -->
            <div v-else class="catalogue-grid">
              <div
                v-for="alerte in filteredAlertes"
                :key="alerte.idAlerte"
                class="car-card"
              >
                <!-- Status badge - Shows on hover -->
                <span 
                  class="status-badge-hover"
                  :class="alerte.statut === 'En attente' ? 'status-pending' : 'status-treated'"
                >
                  <i class="bi bi-circle-fill"></i>
                  {{ alerte.statut === 'En attente' ? t('alerts.pending') : t('alerts.treated') }}
                </span>

                <!-- Type badge at top -->
                <div class="alerte-type-header">
                  <i class="bi" :class="getTypeIcon(alerte.type)"></i>
                  <span class="type-label">{{ getTypeLabel(alerte.type) }}</span>
                </div>

                <!-- Vehicle info -->
                <div class="alerte-vehicle">
                  <i class="bi bi-car-front"></i>
                  <span>{{ alerte.voiture ? `${alerte.voiture.marque} ${alerte.voiture.modele}` : t('common.notAvailable') }}</span>
                </div>

                <!-- Info details -->
                <div class="alerte-details">
                  <div class="detail-item">
                    <i class="bi bi-calendar-event"></i>
                    <span>{{ formatDate(alerte.dateEchance) }}</span>
                  </div>
                  <div class="detail-item" v-if="alerte.kilometrageEchance">
                    <i class="bi bi-speedometer2"></i>
                    <span>{{ formatNumber(alerte.kilometrageEchance) }} km</span>
                  </div>
                </div>

                <!-- Urgency badge if applicable -->
                <div v-if="getDaysMessage(alerte)" class="urgency-message" :class="getUrgencyClass(alerte)">
                  <i class="bi bi-clock-history"></i>
                  <span>{{ getDaysMessage(alerte) }}</span>
                </div>

                <!-- Voir plus button - Bottom right -->
                <button class="car-btn-voir-plus" @click.stop="voirDetails(alerte)">
                  {{ t('common.seeMore') }}
                </button>
              </div>
            </div>
          </div>

          <div v-if="showFilter" class="catalogue-right">
            <aside class="filter-sidebar">
              <div class="filter-header">
                <div class="filter-title-wrapper">
                </div>
                <button
                  v-if="isFilterActive"
                  class="clear-filters-btn"
                  @click="resetFilters"
                >
                  <i class="bi bi-x-circle"></i>
                  {{ t('common.clear') }}
                </button>
              </div>

              <div class="filter-sections-wrapper">
                <div class="filter-section">
                <h4>
                  <i class="bi bi-circle-fill status-icon"></i>
                  {{ t('alerts.status') }}
                </h4>
                <div class="filter-options">
                  <label class="filter-option">
                    <input type="radio" value="" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-grid-3x3-gap"></i>
                      {{ t('common.all') }}
                    </span>
                  </label>
                  <label class="filter-option">
                    <input type="radio" value="En attente" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-clock-fill"></i>
                      {{ t('alerts.pending') }}
                    </span>
                  </label>
                  <label class="filter-option">
                    <input type="radio" value="Traité" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-check-circle-fill"></i>
                      {{ t('alerts.treated') }}
                    </span>
                  </label>
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-exclamation-triangle-fill priority-icon"></i>
                  {{ t('alerts.priority') }}
                </h4>
                <div class="filter-options">
                  <label class="filter-option">
                    <input type="radio" value="" v-model="filters.priorite" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-grid-3x3-gap"></i>
                      {{ t('common.all') }}
                    </span>
                  </label>
                  <label class="filter-option priority-critique">
                    <input type="radio" value="critique" v-model="filters.priorite" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-exclamation-octagon-fill"></i>
                      {{ t('alerts.critical') }}
                    </span>
                  </label>
                  <label class="filter-option priority-haute">
                    <input type="radio" value="haute" v-model="filters.priorite" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-exclamation-triangle-fill"></i>
                      {{ t('alerts.high') }}
                    </span>
                  </label>
                  <label class="filter-option priority-moyenne">
                    <input type="radio" value="moyenne" v-model="filters.priorite" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-exclamation-circle-fill"></i>
                      {{ t('alerts.medium') }}
                    </span>
                  </label>
                  <label class="filter-option priority-faible">
                    <input type="radio" value="faible" v-model="filters.priorite" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-info-circle-fill"></i>
                      {{ t('alerts.low') }}
                    </span>
                  </label>
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-tag-fill type-icon"></i>
                  {{ t('alerts.alertType') }}
                </h4>
                <div class="filter-options">
                  <label class="filter-option">
                    <input type="radio" value="" v-model="filters.type" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-grid-3x3-gap"></i>
                      {{ t('common.all') }}
                    </span>
                  </label>
                  <label 
                    v-for="type in alertTypes" 
                    :key="type.value" 
                    class="filter-option"
                  >
                    <input type="radio" :value="type.value" v-model="filters.type" @change="applyFilters" />
                    <span class="filter-label">
                      <i :class="type.icon"></i>
                      {{ type.label }}
                    </span>
                  </label>
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-car-front-fill vehicle-icon"></i>
                  {{ t('vehicles.vehicle') }}
                </h4>
                <div class="filter-select-wrapper">
                  <select v-model="filters.voiture_id" @change="applyFilters" class="filter-select">
                    <option value="">{{ t('vehicles.allVehicles') }}</option>
                    <option v-for="voiture in voitures" :key="voiture.idVoiture" :value="voiture.idVoiture">
                      {{ voiture.marque }} {{ voiture.modele }} ({{ voiture.annee }})
                    </option>
                  </select>
                  <i class="bi bi-chevron-down select-icon"></i>
                </div>
              </div>
              </div>

              <!-- Filter summary -->
              <div v-if="isFilterActive" class="filter-summary">
                <p><strong>{{ filteredAlertes.length }}</strong> {{ t('common.result', filteredAlertes.length) }}</p>
              </div>
            </aside>
          </div>
        </div>

        <button @click="generateAlerts" class="quick-action-btn" :disabled="isGenerating">
          <i class="bi bi-plus-lg"></i>
          <span>{{ isGenerating ? t('alerts.generating') : t('alerts.addNewAlerts') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../sidebar.vue';
import axios from "axios";
import { inject } from 'vue';
import alerts from '@/utils/alerts';
import { useI18n } from 'vue-i18n';

export default {
  name: "CatalogueAlertes",
  components: { Sidebar },
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  data() {
    return {
      isExpanded: false,
      showFilter: false,
      searchQuery: "",
      user: null,
      isLoading: false,
      error: null,
      deleting: null,
      resolving: null,
      isGenerating: false,
      filters: {
        statut: "",
        priorite: "",
        type: "",
        voiture_id: ""
      },
      menuItems: [
        { label: this.t('menu.dashboard'), to: "/admindashboard" },
        { label: this.t('menu.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('menu.interventions'), to: "/interventions/catalogue" },
        { label: this.t('menu.alerts'), to: "/alertes" },
      ],
      alertes: [],
      voitures: [],
      stats: {
        total: 0,
        treated: 0,
        byPriority: {
          critique: 0,
          haute: 0,
          moyenne: 0,
          faible: 0,
        },
      },
    };
  },
  computed: {
    filteredAlertes() {
      if (!this.alertes || this.alertes.length === 0) {
        return [];
      }

      return this.alertes.filter((alerte) => {
        // Search query filter
        const matchSearch =
          !this.searchQuery ||
          alerte.type?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (alerte.voiture && `${alerte.voiture.marque} ${alerte.voiture.modele}`.toLowerCase().includes(this.searchQuery.toLowerCase()));

        return matchSearch;
      });
    },

    isFilterActive() {
      return (
        this.filters.statut ||
        this.filters.priorite ||
        this.filters.type ||
        this.filters.voiture_id
      );
    },

    alertTypes() {
      return [
        { value: 'Vidange', label: this.t('alerts.types.oilChange'), icon: 'bi-droplet-fill' },
        { value: 'Révision', label: this.t('alerts.types.revision'), icon: 'bi-wrench-adjustable' },
        { value: 'Contrôle technique', label: this.t('alerts.types.technicalControl'), icon: 'bi-clipboard-check' },
        { value: 'Changement pneus', label: this.t('alerts.types.tireChange'), icon: 'bi-circle' },
        { value: 'Contrôle freins', label: this.t('alerts.types.brakeCheck'), icon: 'bi-stop-circle' },
        { value: 'Remplacement batterie', label: this.t('alerts.types.batteryReplacement'), icon: 'bi-battery-charging' },
        { value: 'État véhicule critique', label: this.t('alerts.types.criticalState'), icon: 'bi-shield-fill-exclamation' },
        { value: 'Maintenance prolongée', label: this.t('alerts.types.extendedMaintenance'), icon: 'bi-hourglass-split' },
        { value: 'Coûts élevés', label: this.t('alerts.types.highCosts'), icon: 'bi-currency-dollar' },
        { value: 'Problème récurrent', label: this.t('alerts.types.recurringIssue'), icon: 'bi-arrow-repeat' },
        { value: 'Révision annuelle', label: this.t('alerts.types.annualRevision'), icon: 'bi-calendar-check' },
      ];
    }
  },

  methods: {
    toggleFilter() {
      this.showFilter = !this.showFilter;
    },

    async getUser() {
      try {
        const token = localStorage.getItem("token");
        if (!token) {
          this.$router.push("/login");
          return;
        }

        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` },
        });

        this.user = res.data;
      } catch (error) {
        console.error("Error fetching user:", error);
        this.$router.push("/login");
      }
    },

    async fetchStats() {
      try {
        const token = localStorage.getItem("token");
        const res = await axios.get("http://127.0.0.1:8000/api/alertes/stats", {
          headers: { Authorization: `Bearer ${token}` },
        });
        this.stats = res.data;
      } catch (err) {
        console.error("Error fetching stats:", err);
      }
    },

    async fetchAlertes() {
      this.isLoading = true;
      this.error = null;

      try {
        const token = localStorage.getItem("token");
        if (!token) {
          throw new Error(this.t('errors.missingToken'));
        }

        const params = new URLSearchParams();
        if (this.filters.statut) params.append("statut", this.filters.statut);
        if (this.filters.priorite) params.append("priorite", this.filters.priorite);
        if (this.filters.type) params.append("type", this.filters.type);
        if (this.filters.voiture_id) params.append("voiture_id", this.filters.voiture_id);

        const url = `http://127.0.0.1:8000/api/alertes${params.toString() ? '?' + params.toString() : ''}`;

        const response = await axios.get(url, {
          headers: { Authorization: `Bearer ${token}` },
        });

        this.alertes = response.data || [];
      } catch (error) {
        console.error("Error fetching alertes:", error);

        if (error.response?.status === 401) {
          this.error = this.t('errors.sessionExpired');
          setTimeout(() => this.$router.push("/login"), 2000);
        } else if (error.response?.status === 403) {
          this.error = this.t('errors.noPermission');
        } else {
          this.error = this.t('errors.loadingAlerts');
        }
      } finally {
        this.isLoading = false;
      }
    },

    async fetchVoitures() {
      try {
        const token = localStorage.getItem("token");
        const response = await axios.get("http://127.0.0.1:8000/api/voitures", {
          headers: { Authorization: `Bearer ${token}` },
        });

        this.voitures = response.data || [];
      } catch (error) {
        console.error("Error fetching voitures:", error);
      }
    },

    async generateAlerts() {
      const confirmed = await alerts.confirmAction(
        this.t('alerts.generateAlerts'),
        this.t('alerts.generateConfirmation')
      );
      if (!confirmed) return;

      this.isGenerating = true;

      try {
        const token = localStorage.getItem("token");
        const res = await axios.post(
          "http://127.0.0.1:8000/api/alertes/generate",
          {},
          { headers: { Authorization: `Bearer ${token}` } }
        );

        await alerts.alertSuccess(
          this.t('alerts.generated'),
          this.t('alerts.alertsGenerated', { count: res.data.alertsGenerated || 0 })
        );
        await this.fetchAlertes();
        await this.fetchStats();
      } catch (err) {
        console.error("Error generating alerts:", err);
        await alerts.alertError(
          this.t('common.error'),
          this.t('errors.generatingAlerts')
        );
      } finally {
        this.isGenerating = false;
      }
    },

    voirDetails(alerte) {
      if (!alerte || !alerte.idAlerte) {
        console.error("Invalid alert");
        return;
      }
      this.$router.push({
        name: "DetailsAlerte",
        params: { id: alerte.idAlerte }
      });
    },

    async resolveAlert(id) {
      const confirmed = await alerts.confirmAction(
        this.t('alerts.markAsTreated'),
        this.t('alerts.markAsTreatedConfirmation')
      );
      if (!confirmed) return;

      this.resolving = id;

      try {
        const token = localStorage.getItem("token");
        await axios.patch(
          `http://127.0.0.1:8000/api/alertes/${id}/resolve`,
          {},
          { headers: { Authorization: `Bearer ${token}` } }
        );

        await alerts.alertSuccess(
          this.t('alerts.treated'),
          this.t('alerts.markedAsTreated')
        );
        await this.fetchAlertes();
        await this.fetchStats();
      } catch (err) {
        console.error("Error resolving alert:", err);
        await alerts.alertError(
          this.t('common.error'),
          this.t('errors.resolvingAlert')
        );
      } finally {
        this.resolving = null;
      }
    },

    async supprimerAlerte(alerte) {
      const confirmed = await alerts.confirmDelete(
        this.t('alerts.deleteConfirmation', { type: alerte.type })
      );
      if (!confirmed) return;

      this.deleting = alerte.idAlerte;

      try {
        const token = localStorage.getItem("token");
        await axios.delete(`http://127.0.0.1:8000/api/alertes/${alerte.idAlerte}`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        // Remove from local array
        this.alertes = this.alertes.filter(a => a.idAlerte !== alerte.idAlerte);

        await alerts.alertSuccess(
          this.t('common.deleted'),
          this.t('alerts.alertDeleted')
        );
        await this.fetchStats();
      } catch (error) {
        console.error("Error deleting alerte:", error);
        await alerts.alertError(
          this.t('common.error'),
          this.t('errors.deletingAlert')
        );
      } finally {
        this.deleting = null;
      }
    },

    resetFilters() {
      this.filters = {
        statut: "",
        priorite: "",
        type: "",
        voiture_id: ""
      };
      this.searchQuery = "";
      this.fetchAlertes();
    },

    applyFilters() {
      this.fetchAlertes();
    },

    formatDate(dateString) {
      if (!dateString) return this.t('common.unknownDate');
      const date = new Date(dateString);
      return date.toLocaleDateString('fr-FR', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
    },

    formatNumber(num) {
      return new Intl.NumberFormat('fr-FR').format(num || 0);
    },

    getDaysMessage(alerte) {
      if (!alerte.dateEchance) return null;
      
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const dueDate = new Date(alerte.dateEchance);
      dueDate.setHours(0, 0, 0, 0);
      
      const diffTime = dueDate - today;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays < 0) {
        return this.t('alerts.overdueBy', { days: Math.abs(diffDays) });
      } else if (diffDays === 0) {
        return this.t('alerts.dueToday');
      } else if (diffDays <= 7) {
        return this.t('alerts.urgentDaysLeft', { days: diffDays });
      } else {
        return this.t('alerts.daysLeft', { days: diffDays });
      }
    },

    getPriorityIcon(priority) {
      const icons = {
        'critique': 'bi-exclamation-triangle-fill',
        'haute': 'bi-exclamation-circle-fill',
        'moyenne': 'bi-info-circle-fill',
        'faible': 'bi-info-circle'
      };
      return icons[priority] || 'bi-info-circle';
    },

    getPriorityClass(priority) {
      const classes = {
        'critique': 'priority-critique',
        'haute': 'priority-haute',
        'moyenne': 'priority-moyenne',
        'faible': 'priority-faible'
      };
      return classes[priority] || '';
    },

    getTypeIcon(type) {
      const typeObj = this.alertTypes.find(t => t.value === type);
      return typeObj ? typeObj.icon : 'bi-bell';
    },

    getTypeLabel(type) {
      const typeObj = this.alertTypes.find(t => t.value === type);
      return typeObj ? typeObj.label : type;
    },

    getUrgencyClass(alerte) {
      if (!alerte.dateEchance) return '';
      
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const dueDate = new Date(alerte.dateEchance);
      dueDate.setHours(0, 0, 0, 0);
      
      const diffTime = dueDate - today;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays < 0) return 'urgency-overdue';
      if (diffDays === 0) return 'urgency-today';
      if (diffDays <= 7) return 'urgency-urgent';
      return 'urgency-normal';
    },

    async logout() {
      try {
        const token = localStorage.getItem("token");
        if (token) {
          await axios.post(
            "http://127.0.0.1:8000/api/logout",
            {},
            { headers: { Authorization: `Bearer ${token}` } }
          );
        }
      } catch (error) {
        console.warn("Logout failed or token expired:", error);
      } finally {
        localStorage.removeItem("token");
        this.$router.push({ name: "Login" });
      }
    },
  },

  async mounted() {
    await this.getUser();
    await this.fetchVoitures();
    await this.fetchStats();
    await this.fetchAlertes();
  },
};
</script>