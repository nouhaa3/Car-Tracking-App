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

        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
          <router-link to="/voitures/cataloguevoitures" class="breadcrumb-link">
            {{ t('cars.backToCatalog') }}
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('cars.loadingDetails') }}</p>
        </div>

        <!-- Car Details -->
        <div v-else-if="voiture" class="car-details-wrapper">
          <!-- Main Content -->
          <div class="car-details-container">
            <!-- Left: Image and Header Section -->
            <div class="car-details-left">
              <div class="image-card card">
                <img 
                  :src="voiture.image ? `http://127.0.0.1:8000/storage/${voiture.image}` : '/images/default.png'" 
                  :alt="`${voiture.marque} ${voiture.modele}`" 
                  class="car-details-img" 
                />
              </div>

              <!-- Header Card -->
              <div class="car-details-header card">
                <div class="header-main">
                  <h1 class="car-title">{{ voiture.marque }} {{ voiture.modele }}</h1>
                  <p class="car-year">{{ t('cars.year') }} {{ voiture.annee }}</p>
                </div>
                <div class="header-badge">
                  <span 
                    class="status-badge-large" 
                    :class="{
                      'status-available': voiture.statut === 'En boutique',
                      'status-rented': voiture.statut === 'En location',
                      'status-maintenance': voiture.statut === 'En maintenance'
                    }"
                  >
                    {{ voiture.statut }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Right: Details Section -->
            <div class="car-details-right">
              <!-- Info Card (View Mode) -->
              <div v-if="!editMode" class="info-card card">
                <div class="card-header-section">
                  <h3>{{ t('cars.vehicleInfo') }}</h3>
                  <div class="header-actions">
                    <button class="btn-action btn-edit" @click="editMode = true" :disabled="saving">
                      <i class="bi bi-pencil-square"></i>
                      <span>{{ t('common.edit') }}</span>
                    </button>
                    <button class="btn-action btn-delete" @click="deleteVoiture" :disabled="deleting">
                      <i :class="deleting ? 'bi bi-hourglass-split' : 'bi bi-trash3'"></i>
                      <span>{{ deleting ? t('common.deleting') : t('common.delete') }}</span>
                    </button>
                  </div>
                </div>

                <div class="info-grid">
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.vehicleId') }}</span>
                    <span class="info-value">{{ voiture.idVoiture }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.brand') }}</span>
                    <span class="info-value">{{ voiture.marque }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.model') }}</span>
                    <span class="info-value">{{ voiture.modele }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.year') }}</span>
                    <span class="info-value">{{ voiture.annee }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.mileage') }}</span>
                    <span class="info-value">{{ formatNumber(voiture.kilometrage) }} km</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.condition') }}</span>
                    <span class="info-value">
                      <span :class="['badge-etat', `badge-${voiture.etat?.toLowerCase()}`]">
                        {{ voiture.etat }}
                      </span>
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.status') }}</span>
                    <span class="info-value">
                      <span :class="['badge-statut', getStatusClass(voiture.statut)]">
                        {{ voiture.statut }}
                      </span>
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('cars.userId') }}</span>
                    <span class="info-value">{{ voiture.user_id || t('cars.notAssigned') }}</span>
                  </div>
                </div>
              </div>

              <!-- Edit Form -->
              <div v-else class="edit-card card">
                <div class="card-header-section">
                  <h3>{{ t('cars.editVehicle') }}</h3>
                </div>

                <form @submit.prevent="updateVoiture" class="edit-form">
                  <div class="form-grid">
                    <div class="form-group">
                      <label>{{ t('cars.brand') }} *</label>
                      <input 
                        v-model="form.marque" 
                        class="form-input" 
                        :placeholder="t('cars.brandPlaceholder')"
                        required
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('cars.model') }} *</label>
                      <input 
                        v-model="form.modele" 
                        class="form-input" 
                        :placeholder="t('cars.modelPlaceholder')"
                        required
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('cars.year') }}</label>
                      <input 
                        type="number" 
                        v-model="form.annee" 
                        class="form-input" 
                        :placeholder="t('cars.yearPlaceholder')"
                        min="1900"
                        :max="new Date().getFullYear()"
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('cars.mileage') }}</label>
                      <input 
                        type="number" 
                        v-model="form.kilometrage" 
                        class="form-input" 
                        :placeholder="t('cars.mileagePlaceholder')"
                        min="0"
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('cars.condition') }}</label>
                      <select v-model="form.etat" class="form-input">
                        <option value="">{{ t('common.select') }}</option>
                        <option value="Neuf">{{ t('cars.conditionNew') }}</option>
                        <option value="Bon">{{ t('cars.conditionGood') }}</option>
                        <option value="Usagé">{{ t('cars.conditionUsed') }}</option>
                        <option value="Endommagé">{{ t('cars.conditionDamaged') }}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>{{ t('cars.status') }}</label>
                      <select v-model="form.statut" class="form-input">
                        <option value="">{{ t('common.select') }}</option>
                        <option value="En boutique">{{ t('cars.statusInShop') }}</option>
                        <option value="En location">{{ t('cars.statusRented') }}</option>
                        <option value="En maintenance">{{ t('cars.statusMaintenance') }}</option>
                      </select>
                    </div>

                    <div class="form-group full-width">
                      <label>{{ t('cars.userId') }}</label>
                      <input 
                        type="number" 
                        v-model="form.user_id" 
                        class="form-input" 
                        :placeholder="t('cars.userIdPlaceholder')"
                      />
                    </div>
                  </div>

                  <div class="form-actions">
                    <button type="submit" class="btn-primary" :disabled="saving">
                      {{ saving ? t('common.saving') : t('common.save') }}
                    </button>
                    <button type="button" class="btn-secondary" @click="cancelEdit" :disabled="saving">
                      {{ t('common.cancel') }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Tabs Section -->
          <div class="tabs-section card">
            <div class="tabs-header">
              <button 
                @click="activeTab = 'documents'" 
                :class="{ active: activeTab === 'documents' }"
                class="tab-btn"
              >
                {{ t('nav.documents') }}
              </button>
              <button 
                @click="activeTab = 'historique'" 
                :class="{ active: activeTab === 'historique' }"
                class="tab-btn"
              >
                {{ t('nav.history') }}
              </button>
            </div>

            <div class="tab-content">
              <DocumentsVehicule v-if="activeTab === 'documents'" :voitureId="idVoiture" />
              <HistoriqueVehicule v-if="activeTab === 'historique'" :voitureId="idVoiture" />
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else class="error-state card">
          <i class="bi bi-exclamation-triangle"></i>
          <h3>{{ t('cars.vehicleNotFound') }}</h3>
          <p>{{ t('cars.vehicleNotFoundMessage') }}</p>
          <router-link to="/voitures/cataloguevoitures" class="btn-primary">
            <i class="bi bi-arrow-left"></i>
            {{ t('cars.backToCatalog') }}
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Sidebar from '../sidebar.vue';
import DocumentsVehicule from './DocumentsVehicule.vue';
import HistoriqueVehicule from './HistoriqueVehicule.vue';
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';
import alerts from '@/utils/alerts';

export default {
  name: "DetailsVoiture",
  props: ["idVoiture"],
  components: { 
    Sidebar,
    DocumentsVehicule,
    HistoriqueVehicule
  },
  setup() {
    const { t } = useI18n();
    const theme = inject("theme");
    return { t, theme };
  },
  data() {
    return {
      voiture: null,
      editMode: false,
      loading: false,
      saving: false,
      deleting: false,
      user: null,
      username: "",
      activeTab: 'documents',
      form: {
        idVoiture: "",
        marque: "",
        modele: "",
        annee: "",
        kilometrage: "",
        etat: "",
        statut: "",
        user_id: "",
        image: ""
      },
      menuItems: [
        { label: this.t('nav.home'), to: "/" },
        { label: this.t('nav.dashboard'), to: "/admindashboard" },
        { label: this.t('nav.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('nav.interventions'), to: "/interventions/catalogue" },
        { label: this.t('nav.alerts'), to: "/alertes" }
      ],
    };
  },
  methods: {
    async getUser() {
      try {
        const token = localStorage.getItem("token");
        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` }
        });

        console.log("Utilisateur connecté :", res.data);
        this.user = res.data;
        this.userName = `${res.data.prenom}`;
      } catch (error) {
        console.error("Erreur récupération user:", error);
        this.$router.push("/login");
      }
    },
    async fetchVoiture() {
      this.loading = true;
      try {
        const token = localStorage.getItem("token") || "";
        const res = await axios.get(`http://127.0.0.1:8000/api/voitures/${this.idVoiture}`, {
          headers: {
            Authorization: token ? `Bearer ${token}` : "",
            Accept: "application/json"
          }
        });
        this.voiture = res.data;

        this.form = {
          marque: res.data.marque ?? "",
          modele: res.data.modele ?? "",
          annee: res.data.annee ?? "",
          kilometrage: res.data.kilometrage ?? 0,
          etat: res.data.etat ?? res.data.state ?? "",
          statut: res.data.statut ?? res.data.status ?? "",
          user_id: res.data.user_id ?? res.data.userId ?? ""
        };
      } catch (err) {
        console.error("fetchVoiture error:", err);
        const serverMsg = err.response?.data ?? err.response?.data?.message ?? err.message;
        await alerts.alertError(this.t('common.error'), this.t('cars.loadError') + (typeof serverMsg === "string" ? serverMsg : JSON.stringify(serverMsg)));
      } finally {
        this.loading = false;
      }
    },

    async updateVoiture() {
      if (!this.form.marque || !this.form.modele) {
        await alerts.alertWarning(this.t('common.requiredFields'), this.t('cars.brandModelRequired'));
        return;
      }

      const token = localStorage.getItem("token");
      if (!token) {
        await alerts.alertError(this.t('common.unauthorized'), this.t('cars.mustBeLoggedIn'));
        return;
      }

      this.saving = true;
      try {
        const payload = {
          marque: this.form.marque,
          modele: this.form.modele,
          annee: this.form.annee,
          kilometrage: this.form.kilometrage,
          etat: this.form.etat,
          statut: this.form.statut,
          user_id: this.form.user_id
        };

        const res = await axios.put(`http://127.0.0.1:8000/api/voitures/${this.idVoiture}`, payload, {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
            "Content-Type": "application/json"
          }
        });

        this.voiture = res.data ?? { ...payload };
        this.form = { ...this.voiture };
        this.editMode = false;
        await alerts.alertSuccess(this.t('common.updated'), this.t('cars.updateSuccess'));
      } catch (err) {
        console.error("updateVoiture error:", err);
        const msg = err.response?.data?.message || err.response?.data || err.message;
        await alerts.alertError(this.t('common.error'), this.t('cars.updateError') + (typeof msg === "string" ? msg : JSON.stringify(msg)));
      } finally {
        this.saving = false;
      }
    },

    cancelEdit() {
      this.editMode = false;
      // Reset form to original values
      this.form = {
        marque: this.voiture.marque ?? "",
        modele: this.voiture.modele ?? "",
        annee: this.voiture.annee ?? "",
        kilometrage: this.voiture.kilometrage ?? 0,
        etat: this.voiture.etat ?? "",
        statut: this.voiture.statut ?? "",
        user_id: this.voiture.user_id ?? ""
      };
    },

    formatNumber(num) {
      return new Intl.NumberFormat('fr-FR').format(num || 0);
    },

    getStatusClass(statut) {
      const statusMap = {
        'En boutique': 'badge-available',
        'En location': 'badge-rented',
        'En maintenance': 'badge-maintenance'
      };
      return statusMap[statut] || 'badge-default';
    },

    async deleteVoiture() {
      const confirmed = await alerts.confirmDelete(this.t('cars.confirmDelete'));
      if (!confirmed) return;

      const token = localStorage.getItem("token");
      if (!token) {
        await alerts.alertError(this.t('common.unauthorized'), this.t('cars.mustBeLoggedIn'));
        return;
      }

      this.deleting = true;
      try {
        await axios.delete(`http://127.0.0.1:8000/api/voitures/${this.idVoiture}`, {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json"
          }
        });

        alerts.success(this.t('common.deleted'), this.t('cars.deleteSuccess'));
        this.$router.push({ name: "CatalogueVoitures" });
      } catch (err) {
        console.error("deleteVoiture error:", err);
        const msg = err.response?.data?.message || err.response?.data || err.message;
        await alerts.alertError(this.t('common.error'), this.t('cars.deleteError') + (typeof msg === "string" ? msg : JSON.stringify(msg)));
      } finally {
        this.deleting = false;
      }
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
    }
  },
  async mounted() {
    await this.getUser();
    this.fetchVoiture();
  }
};
</script>

<style scoped>
/* Modern Action Buttons - Improved Size & Spacing */
.btn-action {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  font-size: 0.9rem;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  white-space: nowrap;
  min-width: 120px;
  justify-content: center;
}

.btn-action i {
  font-size: 1rem;
  transition: transform 0.3s;
}

.btn-action:hover i {
  transform: scale(1.1);
}

.btn-edit {
  background: #748BAA;
  color: white;
  box-shadow: 0 2px 8px rgba(116, 139, 170, 0.2);
}

.btn-edit:hover {
  background: #546A88;
  box-shadow: 0 4px 12px rgba(116, 139, 170, 0.3);
  transform: translateY(-2px);
}

.btn-edit:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(116, 139, 170, 0.25);
}

.btn-delete {
  background: #C85A54;
  color: white;
  box-shadow: 0 2px 8px rgba(200, 90, 84, 0.2);
}

.btn-delete:hover {
  background: #B04944;
  box-shadow: 0 4px 12px rgba(200, 90, 84, 0.3);
  transform: translateY(-2px);
}

.btn-delete:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
}

.btn-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.btn-action span {
  font-weight: 600;
  letter-spacing: 0.3px;
}

/* Improved Header Card Layout */
.car-details-header {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border-radius: 16px;
  padding: 24px 28px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  border: 1px solid #e8edf4;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  margin-top: 16px;
  transition: all 0.3s;
}

.car-details-header:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}

.header-main {
  flex: 1;
}

.car-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #344966;
  margin: 0 0 8px 0;
  line-height: 1.2;
  letter-spacing: -0.3px;
}

.car-year {
  font-size: 0.9rem;
  color: #748BAA;
  margin: 0;
  font-weight: 400;
}

.header-badge {
  display: flex;
  align-items: center;
}

.status-badge-large {
  padding: 10px 20px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s;
}

.status-badge-large:hover {
  transform: scale(1.03);
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
}

.status-available {
  background: linear-gradient(135deg, #10B981 0%, #059669 100%);
  color: white;
}

.status-rented {
  background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
  color: white;
}

.status-maintenance {
  background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
  color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .car-details-header {
    flex-direction: column;
    align-items: flex-start;
    padding: 20px;
  }
  
  .header-main {
    width: 100%;
  }
  
  .header-badge {
    width: 100%;
  }
  
  .status-badge-large {
    width: 100%;
    text-align: center;
  }
  
  .btn-action {
    font-size: 14px;
    padding: 9px 16px;
    min-width: 110px;
  }
  
  .btn-action span {
    display: none;
  }
  
  .btn-action i {
    margin: 0;
  }
}

/* Tabs Section */
.tabs-section {
  margin-top: 24px;
  background: white;
  border-radius: 12px;
  overflow: hidden;
}

.tabs-header {
  display: flex;
  gap: 0;
  border-bottom: 2px solid #eee;
  background: #f8f9fa;
}

.tab-btn {
  flex: 1;
  padding: 16px 24px;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-size: 16px;
  font-weight: 600;
  color: #666;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.tab-btn:hover {
  background: #e8edf4;
  color: #344966;
}

.tab-btn.active {
  background: white;
  color: #344966;
  border-bottom-color: #344966;
}

.tab-content {
  padding: 0;
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>