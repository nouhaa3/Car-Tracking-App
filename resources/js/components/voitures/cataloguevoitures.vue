<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.user')" class="avatar" />
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
                  :placeholder="t('catalog.searchPlaceholder')"
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
                  {{ t('catalog.filters') }}
                </button>
              </div>
            </div>

            <!-- Loading state -->
            <div v-if="isLoading" class="loading-container">
              <div class="spinner"></div>
              <p>{{ t('catalog.loading') }}</p>
            </div>

            <!-- Error state -->
            <div v-else-if="error" class="error-container">
              <i class="bi bi-exclamation-triangle"></i>
              <p>{{ error }}</p>
              <button @click="fetchVoitures" class="retry-btn">{{ t('common.retry') }}</button>
            </div>

            <!-- Empty state -->
            <div v-else-if="filteredVoitures.length === 0" class="empty-state">
              <h3>{{ t('catalog.noVehicles') }}</h3>
              <p v-if="searchQuery || isFilterActive">
                {{ t('catalog.adjustSearch') }}
              </p>
              <p v-else>
                {{ t('catalog.addFirstVehicle') }}
              </p>
              <router-link to="/voitures/ajouter" class="add-car-btn">
                {{ t('catalog.addVehicle') }}
              </router-link>
            </div>

            <!-- Catalogue grid -->
            <div v-else class="catalogue-grid">
              <div
                v-for="voiture in filteredVoitures"
                :key="voiture.idVoiture"
                class="car-card"
              >
                <!-- Status badge -->
                <span 
                  class="status-badge" 
                  :class="{
                    'status-available': voiture.statut === 'En boutique',
                    'status-rented': voiture.statut === 'En location',
                    'status-maintenance': voiture.statut === 'En maintenance'
                  }"
                >
                  {{ voiture.statut }}
                </span>

                <img 
                  :src="voiture.image ? `http://127.0.0.1:8000/storage/${voiture.image}` : '/images/default.png'" 
                  :alt="`${voiture.marque} ${voiture.modele}`"
                  @error="handleImageError"
                />

                <div class="car-info">
                  <div class="car-info-left">
                    <p class="marque">{{ voiture.marque }}</p>
                    <p class="modele">{{ voiture.modele }}</p>
                  </div>
                  <span class="car-info-right">{{ voiture.annee }}</span>
                </div>

                <!-- Additional info -->
                <div class="car-meta">
                  <span class="meta-item" v-if="voiture.couleur">
                    {{ voiture.couleur }}
                  </span>
                  <span class="meta-item" v-if="voiture.etat">
                    <i class="bi bi-speedometer2"></i>
                    {{ voiture.etat }}
                  </span>
                </div>

                <button class="car-btn-voir-plus" @click="voirPlus(voiture)">
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
                <!-- Status & État Filters -->
                <div class="filter-section">
                <h4>
                  <i class="bi bi-circle-fill status-icon"></i>
                  {{ t('catalog.status') }}
                </h4>
                <div class="filter-options">
                  <label class="filter-option">
                    <input type="radio" value="" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-grid-3x3-gap"></i>
                      {{ t('common.all') }}
                    </span>
                  </label>
                  <label class="filter-option status-boutique">
                    <input type="radio" value="En boutique" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-shop"></i>
                      {{ t('catalog.statusInShop') }}
                    </span>
                  </label>
                  <label class="filter-option status-location">
                    <input type="radio" value="En location" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-key-fill"></i>
                      {{ t('catalog.statusRented') }}
                    </span>
                  </label>
                  <label class="filter-option status-maintenance">
                    <input type="radio" value="En maintenance" v-model="filters.statut" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-wrench"></i>
                      {{ t('catalog.statusMaintenance') }}
                    </span>
                  </label>
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-star-fill state-icon"></i>
                  {{ t('catalog.condition') }}
                </h4>
                <div class="filter-options">
                  <label class="filter-option">
                    <input type="radio" value="" v-model="filters.etat" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-grid-3x3-gap"></i>
                      {{ t('common.all') }}
                    </span>
                  </label>
                  <label class="filter-option">
                    <input type="radio" value="Neuf" v-model="filters.etat" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-stars"></i>
                      {{ t('catalog.conditionNew') }}
                    </span>
                  </label>
                  <label class="filter-option">
                    <input type="radio" value="Bon" v-model="filters.etat" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-star-fill"></i>
                      {{ t('catalog.conditionGood') }}
                    </span>
                  </label>
                  <label class="filter-option">
                    <input type="radio" value="Usagé" v-model="filters.etat" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-star-half"></i>
                      {{ t('catalog.conditionUsed') }}
                    </span>
                  </label>
                  <label class="filter-option">
                    <input type="radio" value="Endommagé" v-model="filters.etat" @change="applyFilters" />
                    <span class="filter-label">
                      <i class="bi bi-exclamation-triangle"></i>
                      {{ t('catalog.conditionDamaged') }}
                    </span>
                  </label>
                </div>
              </div>

              <!-- Search Filters -->
              <div class="filter-section">
                <h4>
                  <i class="bi bi-tag-fill vehicle-icon"></i>
                  {{ t('catalog.brand') }}
                </h4>
                <div class="filter-input-wrapper">
                  <i class="bi bi-search input-icon"></i>
                  <input 
                    type="text" 
                    v-model="filters.marque" 
                    :placeholder="t('catalog.brandPlaceholder')"
                    @input="applyFilters"
                    class="filter-input"
                  />
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-car-front-fill vehicle-icon"></i>
                  {{ t('catalog.model') }}
                </h4>
                <div class="filter-input-wrapper">
                  <i class="bi bi-search input-icon"></i>
                  <input 
                    type="text" 
                    v-model="filters.modele" 
                    :placeholder="t('catalog.modelPlaceholder')"
                    @input="applyFilters"
                    class="filter-input"
                  />
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-calendar3 year-icon"></i>
                  {{ t('catalog.year') }}
                </h4>
                <div class="filter-input-wrapper">
                  <i class="bi bi-calendar3 input-icon"></i>
                  <input 
                    type="number" 
                    v-model="filters.annee" 
                    :placeholder="t('catalog.yearPlaceholder')"
                    min="1900"
                    :max="new Date().getFullYear()"
                    @input="applyFilters"
                    class="filter-input"
                  />
                </div>
              </div>

              <div class="filter-section">
                <h4>
                  <i class="bi bi-palette-fill color-icon"></i>
                  {{ t('catalog.color') }}
                </h4>
                <div class="filter-input-wrapper">
                  <i class="bi bi-search input-icon"></i>
                  <input 
                    type="text" 
                    v-model="filters.couleur" 
                    :placeholder="t('catalog.colorPlaceholder')"
                    @input="applyFilters"
                    class="filter-input"
                  />
                </div>
              </div>
              </div>

              <!-- Filter summary -->
              <div v-if="isFilterActive" class="filter-summary">
                <p><strong>{{ filteredVoitures.length }}</strong> {{ t('catalog.results', { count: filteredVoitures.length }) }}</p>
              </div>
            </aside>
          </div>
        </div>

        <router-link to="/voitures/ajouter" class="quick-action-btn">
          <i class="bi bi-plus-lg"></i>
          <span>{{ t('catalog.addNewCar') }}</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../sidebar.vue';
import axios from "axios";
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';

export default {
  name: "CatalogueVoitures",
  components: { Sidebar },
  setup() {
    const { t } = useI18n();
    const theme = inject("theme");
    return { t, theme };
  },
  data() {
    return {
      isExpanded: false,
      showFilter: false,
      searchQuery: "",
      user: null,
      username: "",
      isLoading: false,
      error: null,
      filters: {
        marque: "",
        modele: "",
        couleur: "",
        annee: "",
        statut: "",
        etat: ""
      },
      menuItems: [
        { label: this.t('nav.home'), to: "/" },
        { label: this.t('nav.dashboard'), to: "/admindashboard" },
        { label: this.t('nav.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('nav.interventions'), to: "/interventions/catalogue" },
        { label: this.t('nav.alerts'), to: "/alertes" }
      ],
      voitures: [],
    };
  },
  computed: {
    filteredVoitures() {
      if (!this.voitures || this.voitures.length === 0) {
        return [];
      }

      return this.voitures.filter((v) => {
        // Search query filter
        const matchSearch =
          !this.searchQuery ||
          v.marque?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          v.modele?.toLowerCase().includes(this.searchQuery.toLowerCase());

        // Individual filters
        const matchMarque =
          !this.filters.marque ||
          v.marque?.toLowerCase().includes(this.filters.marque.toLowerCase());
        
        const matchModele =
          !this.filters.modele ||
          v.modele?.toLowerCase().includes(this.filters.modele.toLowerCase());
        
        const matchCouleur =
          !this.filters.couleur ||
          v.couleur?.toLowerCase().includes(this.filters.couleur.toLowerCase());
        
        const matchAnnee = !this.filters.annee || v.annee == this.filters.annee;
        const matchStatut = this.filters.statut === "" || v.statut === this.filters.statut;
        const matchEtat = this.filters.etat === "" || v.etat === this.filters.etat;

        return (
          matchSearch &&
          matchMarque &&
          matchModele &&
          matchCouleur &&
          matchAnnee &&
          matchStatut &&
          matchEtat
        );
      });
    },
    
    isFilterActive() {
      return (
        this.filters.marque ||
        this.filters.modele ||
        this.filters.couleur ||
        this.filters.annee ||
        this.filters.statut ||
        this.filters.etat
      );
    },
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
        this.username = res.data.prenom;
      } catch (error) {
        console.error("Erreur récupération user:", error);
        this.$router.push("/login");
      }
    },

    async fetchVoitures() {
      this.isLoading = true;
      this.error = null;
      
      try {
        const token = localStorage.getItem("token");
        if (!token) {
          throw new Error("Token manquant");
        }

        const response = await axios.get("http://127.0.0.1:8000/api/voitures", {
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.voitures = response.data || [];
      } catch (error) {
        console.error("Error fetching voitures:", error);
        
        if (error.response?.status === 401) {
          this.error = this.t('catalog.sessionExpired');
          setTimeout(() => this.$router.push("/login"), 2000);
        } else if (error.response?.status === 403) {
          this.error = this.t('catalog.noPermission');
        } else {
          this.error = this.t('catalog.loadError');
        }
      } finally {
        this.isLoading = false;
      }
    },

    voirPlus(voiture) {
      if (!voiture || !voiture.idVoiture) {
        console.error("Véhicule invalide");
        return;
      }
      this.$router.push({ 
        name: "DetailsVoiture", 
        params: { idVoiture: voiture.idVoiture } 
      });
    },

    resetFilters() {
      this.filters = {
        marque: "",
        modele: "",
        couleur: "",
        annee: "",
        statut: "",
        etat: ""
      };
      this.searchQuery = "";
    },

    applyFilters() {
      // Debounce logic could be added here for performance
      // This method is called on input/change events
    },

    handleImageError(event) {
      event.target.src = '/images/default.png';
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
    console.log('CatalogueVoitures: Component mounted');
    try {
      await this.getUser();
      console.log('CatalogueVoitures: User fetched');
      await this.fetchVoitures();
      console.log('CatalogueVoitures: Voitures fetched', this.voitures.length);
    } catch (error) {
      console.error('CatalogueVoitures: Mounting error', error);
    }
  },
};
</script>