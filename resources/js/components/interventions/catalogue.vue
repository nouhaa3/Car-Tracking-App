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

        <div class="catalogue-container">
          <div class="catalogue-left">
            <!-- Header with search and actions -->
            <div class="catalogue-header">
              <div class="search-wrapper">
                <input
                  v-model="searchQuery"
                  type="text"
                  :placeholder="t('interventions.searchPlaceholder')"
                  class="search-input"
                />
                <button 
                  v-if="searchQuery" 
                  @click="searchQuery = ''"
                  class="clear-search-btn"
                >
                  ×
                </button>
              </div>
              <div class="header-actions">
                <button @click="toggleFilter" class="filter-toggle-btn" :class="{ active: showFilter }">
                  {{ t('common.filters') }}
                </button>
              </div>
            </div>

            <!-- Statistics Summary -->
            <div v-if="!isLoading && interventions.length > 0" class="stats-summary">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="bi bi-wrench"></i>
                </div>
                <div class="stat-content">
                  <span class="stat-value">{{ interventions.length }}</span>
                  <span class="stat-label">{{ t('interventions.totalInterventions') }}</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-content">
                  <span class="stat-value">{{ formatCurrency(totalCost) }}</span>
                  <span class="stat-label">{{ t('interventions.totalCost') }}</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="bi bi-calculator"></i>
                </div>
                <div class="stat-content">
                  <span class="stat-value">{{ formatCurrency(averageCost) }}</span>
                  <span class="stat-label">{{ t('interventions.averageCost') }}</span>
                </div>
              </div>
            </div>

            <!-- Loading state -->
            <div v-if="isLoading" class="loading-container">
              <div class="spinner"></div>
              <p>{{ t('interventions.loadingInterventions') }}</p>
            </div>

            <!-- Error state -->
            <div v-else-if="error" class="error-container">
              <p>{{ error }}</p>
              <button @click="fetchInterventions" class="retry-btn">{{ t('common.retry') }}</button>
            </div>

            <!-- Empty state -->
            <div v-else-if="filteredInterventions.length === 0" class="empty-state">
              <i class="bi bi-tools"></i>
              <h3>{{ t('interventions.noInterventionsFound') }}</h3>
              <p v-if="searchQuery || isFilterActive">
                {{ t('interventions.tryAdjustingFilters') }}
              </p>
              <p v-else>
                {{ t('interventions.startAddingFirst') }}
              </p>
            </div>

            <!-- Interventions grid -->
            <div v-else class="catalogue-grid">
              <div
                v-for="intervention in filteredInterventions"
                :key="intervention.idIntervention"
                class="car-card clickable-card"
                @click="voirDetails(intervention)"
              >
                <!-- Status badge - Shows on hover -->
                <span 
                  class="status-badge-hover"
                  :class="isUpcoming(intervention.date) ? 'status-upcoming' : 'status-completed'"
                >
                  <i class="bi bi-circle-fill"></i>
                  {{ isUpcoming(intervention.date) ? t('interventions.upcoming') : t('interventions.completed') }}
                </span>

                <!-- Type header -->
                <div class="intervention-type-header">
                  <i :class="getTypeIcon(intervention.type)"></i>
                  <span class="type-label">{{ getTypeLabel(intervention.type) }}</span>
                </div>

                <!-- Vehicle info -->
                <div class="intervention-vehicle-info">
                  <i class="bi bi-car-front"></i>
                  <span>{{ intervention.voiture?.marque }} {{ intervention.voiture?.modele }}</span>
                </div>

                <!-- Details -->
                <div class="intervention-details">
                  <div class="detail-item">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ formatDate(intervention.date) }}</span>
                  </div>
                  <div class="detail-item">
                    <i class="bi bi-shop"></i>
                    <span>{{ intervention.garage }}</span>
                  </div>
                  <div class="detail-item cost-item">
                    <i class="bi bi-cash-coin"></i>
                    <span class="cost-value">{{ formatCurrency(intervention.cout) }}</span>
                  </div>
                </div>

                <!-- Remarks if available -->
                <div v-if="intervention.remarques" class="intervention-remarks-box">
                  <i class="bi bi-chat-left-text"></i>
                  <span>{{ truncateText(intervention.remarques, 80) }}</span>
                </div>
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
                  <i class="bi bi-wrench-adjustable type-icon"></i>
                  {{ t('interventions.interventionType') }}
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
                    v-for="type in interventionTypes" 
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
                  <i class="bi bi-building garage-icon"></i>
                  {{ t('interventions.garage') }}
                </h4>
                <div class="filter-input-wrapper">
                  <i class="bi bi-search input-icon"></i>
                  <input 
                    type="text" 
                    v-model="filters.garage" 
                    :placeholder="t('interventions.garageName')"
                    @input="applyFilters"
                    class="filter-input"
                  />
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

              <div class="filter-section">
                <h4>
                  <i class="bi bi-calendar-range period-icon"></i>
                  {{ t('interventions.period') }}
                </h4>
                <div class="date-filter-group">
                  <div class="filter-input-wrapper">
                    <i class="bi bi-calendar3 input-icon"></i>
                    <input 
                      type="date" 
                      v-model="filters.dateDebut" 
                      @change="applyFilters"
                      class="filter-input"
                    />
                  </div>
                </div>
                <div class="date-filter-group">
                  <div class="filter-input-wrapper">
                    <i class="bi bi-calendar3 input-icon"></i>
                    <input 
                      type="date" 
                      v-model="filters.dateFin" 
                      @change="applyFilters"
                      class="filter-input"
                    />
                  </div>
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-currency-euro cost-icon"></i>
                  {{ t('interventions.cost') }}
                </h4>
                <div class="cost-filter-group">
                  <div class="filter-input-wrapper cost-input">
                    <i class="bi bi-currency-euro input-icon"></i>
                    <input 
                      type="number" 
                      v-model="filters.coutMin" 
                      :placeholder="t('common.min')"
                      @input="applyFilters"
                      min="0"
                      class="filter-input"
                    />
                  </div>
                  <span class="filter-separator">-</span>
                  <div class="filter-input-wrapper cost-input">
                    <i class="bi bi-currency-euro input-icon"></i>
                    <input 
                      type="number" 
                      v-model="filters.coutMax" 
                      :placeholder="t('common.max')"
                      @input="applyFilters"
                      min="0"
                      class="filter-input"
                    />
                  </div>
                </div>
              </div>
              </div>

              <!-- Filter summary -->
              <div v-if="isFilterActive" class="filter-summary">
                <p><strong>{{ filteredInterventions.length }}</strong> {{ t('common.result', filteredInterventions.length) }}</p>
              </div>
            </aside>
          </div>
        </div>

        <router-link to="/interventions/ajouter" class="quick-action-btn">
          <i class="bi bi-plus-lg"></i>
          <span>{{ t('interventions.addNewIntervention') }}</span>
        </router-link>
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
  name: "CatalogueInterventions",
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
      filters: {
        type: "",
        garage: "",
        voiture_id: "",
        dateDebut: "",
        dateFin: "",
        coutMin: "",
        coutMax: ""
      },
      menuItems: [
        { label: this.t('menu.dashboard'), to: "/admindashboard" },
        { label: this.t('menu.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('menu.interventions'), to: "/interventions/catalogue" },
        { label: this.t('menu.alerts'), to: "/alertes" },
      ],
      interventions: [],
      voitures: [],
    };
  },
  computed: {
    filteredInterventions() {
      if (!this.interventions || this.interventions.length === 0) {
        return [];
      }

      return this.interventions.filter((intervention) => {
        // Search query filter
        const matchSearch =
          !this.searchQuery ||
          intervention.type?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          intervention.garage?.toLowerCase().includes(this.searchQuery.toLowerCase());

        // Type filter
        const matchType = !this.filters.type || intervention.type === this.filters.type;

        // Garage filter
        const matchGarage =
          !this.filters.garage ||
          intervention.garage?.toLowerCase().includes(this.filters.garage.toLowerCase());

        // Voiture filter
        const matchVoiture =
          !this.filters.voiture_id ||
          intervention.voiture_id == this.filters.voiture_id;

        // Date filters
        const matchDateDebut =
          !this.filters.dateDebut ||
          new Date(intervention.date) >= new Date(this.filters.dateDebut);

        const matchDateFin =
          !this.filters.dateFin ||
          new Date(intervention.date) <= new Date(this.filters.dateFin);

        // Cost filters
        const matchCoutMin =
          !this.filters.coutMin ||
          parseFloat(intervention.cout) >= parseFloat(this.filters.coutMin);

        const matchCoutMax =
          !this.filters.coutMax ||
          parseFloat(intervention.cout) <= parseFloat(this.filters.coutMax);

        return (
          matchSearch &&
          matchType &&
          matchGarage &&
          matchVoiture &&
          matchDateDebut &&
          matchDateFin &&
          matchCoutMin &&
          matchCoutMax
        );
      });
    },

    isFilterActive() {
      return (
        this.filters.type ||
        this.filters.garage ||
        this.filters.voiture_id ||
        this.filters.dateDebut ||
        this.filters.dateFin ||
        this.filters.coutMin ||
        this.filters.coutMax
      );
    },

    totalCost() {
      return this.filteredInterventions.reduce((sum, i) => sum + parseFloat(i.cout || 0), 0);
    },

    averageCost() {
      if (this.filteredInterventions.length === 0) return 0;
      return this.totalCost / this.filteredInterventions.length;
    },

    interventionTypes() {
      return [
        { value: 'Vidange', label: this.t('interventions.types.oilChange'), icon: 'bi-droplet-fill' },
        { value: 'Révision', label: this.t('interventions.types.revision'), icon: 'bi-tools' },
        { value: 'Réparation', label: this.t('interventions.types.repair'), icon: 'bi-wrench-adjustable' },
        { value: 'Pneus', label: this.t('interventions.types.tires'), icon: 'bi-circle' },
        { value: 'Freins', label: this.t('interventions.types.brakes'), icon: 'bi-stop-circle-fill' },
        { value: 'Batterie', label: this.t('interventions.types.battery'), icon: 'bi-lightning-charge-fill' },
        { value: 'Climatisation', label: this.t('interventions.types.airConditioning'), icon: 'bi-snow' },
        { value: 'Contrôle technique', label: this.t('interventions.types.technicalControl'), icon: 'bi-clipboard-check-fill' },
        { value: 'Autre', label: this.t('common.other'), icon: 'bi-gear-fill' }
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

    async fetchInterventions() {
      this.isLoading = true;
      this.error = null;

      try {
        const token = localStorage.getItem("token");
        if (!token) {
          throw new Error(this.t('errors.missingToken'));
        }

        const response = await axios.get("http://127.0.0.1:8000/api/interventions", {
          headers: { Authorization: `Bearer ${token}` },
        });

        this.interventions = response.data || [];
      } catch (error) {
        console.error("Error fetching interventions:", error);

        if (error.response?.status === 401) {
          this.error = this.t('errors.sessionExpired');
          setTimeout(() => this.$router.push("/login"), 2000);
        } else if (error.response?.status === 403) {
          this.error = this.t('errors.noPermission');
        } else {
          this.error = this.t('errors.loadingInterventions');
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

    voirDetails(intervention) {
      if (!intervention || !intervention.idIntervention) {
        console.error("Invalid intervention");
        return;
      }
      this.$router.push({
        name: "DetailsIntervention",
        params: { idIntervention: intervention.idIntervention }
      });
    },

    modifierIntervention(intervention) {
      this.$router.push({
        name: "DetailsIntervention",
        params: { idIntervention: intervention.idIntervention }
      });
    },

    async supprimerIntervention(intervention) {
      const confirmed = await alerts.confirmDelete(
        this.t('interventions.deleteConfirmation', { 
          type: intervention.type, 
          date: this.formatDate(intervention.date) 
        })
      );
      if (!confirmed) return;

      this.deleting = intervention.idIntervention;

      try {
        const token = localStorage.getItem("token");
        await axios.delete(`http://127.0.0.1:8000/api/interventions/${intervention.idIntervention}`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        // Remove from local array
        this.interventions = this.interventions.filter(i => i.idIntervention !== intervention.idIntervention);

        await alerts.alertSuccess(this.t('interventions.interventionDeleted'));
      } catch (error) {
        console.error("Error deleting intervention:", error);
        await alerts.alertError(
          this.t('common.error'),
          this.t('errors.deletingIntervention')
        );
      } finally {
        this.deleting = null;
      }
    },

    resetFilters() {
      this.filters = {
        type: "",
        garage: "",
        voiture_id: "",
        dateDebut: "",
        dateFin: "",
        coutMin: "",
        coutMax: ""
      };
      this.searchQuery = "";
    },

    applyFilters() {
      // Debounce logic could be added here for performance
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

    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-TN', {
        style: 'currency',
        currency: 'TND'
      }).format(amount || 0);
    },

    truncateText(text, maxLength) {
      if (!text) return "";
      if (text.length <= maxLength) return text;
      return text.substring(0, maxLength) + "...";
    },

    getTypeIcon(type) {
      const typeObj = this.interventionTypes.find(t => t.value === type);
      return typeObj ? typeObj.icon : 'bi-wrench';
    },

    getTypeLabel(type) {
      const typeObj = this.interventionTypes.find(t => t.value === type);
      return typeObj ? typeObj.label : type;
    },

    getTypeClass(type) {
      const classes = {
        'Vidange': 'type-vidange',
        'Révision': 'type-revision',
        'Réparation': 'type-reparation',
        'Pneus': 'type-pneus',
        'Freins': 'type-freins',
        'Batterie': 'type-batterie',
        'Climatisation': 'type-climatisation',
        'Contrôle technique': 'type-controle',
        'Autre': 'type-autre'
      };
      return classes[type] || '';
    },

    isUpcoming(date) {
      const interventionDate = new Date(date);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return interventionDate >= today;
    },

    async logout() {
      const interventionDate = new Date(date);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return interventionDate >= today;
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
    await this.fetchInterventions();
  },
};
</script>


