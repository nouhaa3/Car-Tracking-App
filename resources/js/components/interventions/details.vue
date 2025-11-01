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

        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
          <router-link to="/interventions/catalogue" class="breadcrumb-link">
            {{ t('interventions.backToCatalog') }}
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('interventions.loadingDetails') }}</p>
        </div>

        <!-- Intervention Details -->
        <div v-else-if="intervention" class="car-details-wrapper">
          <!-- Main Content -->
          <div class="car-details-container">
            <!-- Left: Header and Vehicle Info -->
            <div class="car-details-left">
              <!-- Header Card -->
              <div class="car-details-header card intervention-header">
                <div class="header-content">
                  <div class="header-left">
                    <div class="intervention-type-display">
                      <i :class="getTypeIcon(intervention.type)" class="type-icon-large"></i>
                      <div>
                        <h1 class="car-title">{{ getTypeLabel(intervention.type) }}</h1>
                        <p class="car-subtitle">{{ formatDate(intervention.date) }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="header-right">
                    <span class="cost-badge-large">
                      {{ formatCurrency(intervention.cout) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Vehicle Card -->
              <div class="vehicle-info-card card">
                <h3 class="card-title">
                  <i class="bi bi-car-front"></i>
                  {{ t('vehicles.concernedVehicle') }}
                </h3>
                <div class="vehicle-details" v-if="intervention.voiture">
                  <div class="vehicle-main-info">
                    <h4>{{ intervention.voiture.marque }} {{ intervention.voiture.modele }}</h4>
                    <span class="vehicle-year">{{ intervention.voiture.annee }}</span>
                  </div>
                  <div class="vehicle-meta">
                    <div class="meta-item">
                      <i class="bi bi-speedometer2"></i>
                      <span>{{ formatNumber(intervention.voiture.kilometrage) }} km</span>
                    </div>
                    <div class="meta-item">
                      <span class="badge-etat">{{ intervention.voiture.etat }}</span>
                    </div>
                    <div class="meta-item">
                      <span :class="['badge-statut', getStatusClass(intervention.voiture.statut)]">
                        {{ intervention.voiture.statut }}
                      </span>
                    </div>
                  </div>
                  <router-link 
                    :to="`/voitures/${intervention.voiture.idVoiture}`" 
                    class="view-vehicle-btn"
                  >
                    <i class="bi bi-arrow-right-circle"></i>
                    {{ t('vehicles.viewVehicleDetails') }}
                  </router-link>
                </div>
                <div v-else class="vehicle-not-found">
                  <i class="bi bi-exclamation-circle"></i>
                  <span>{{ t('vehicles.vehicleNotFound') }}</span>
                </div>
              </div>

              <!-- Garage Card -->
              <div class="garage-card card">
                <h3 class="card-title">
                  <i class="bi bi-shop"></i>
                  {{ t('interventions.garage') }}
                </h3>
                <p class="garage-name">{{ intervention.garage }}</p>
              </div>
            </div>

            <!-- Right: Details Section -->
            <div class="car-details-right">
              <!-- Info Card (View Mode) -->
              <div v-if="!editMode" class="info-card card">
                <div class="card-header-section">
                  <h3>{{ t('interventions.interventionDetails') }}</h3>
                  <div class="header-actions">
                    <button class="icon-btn edit-btn" @click="editMode = true" :disabled="saving">
                      <i class="bi bi-pencil-square"></i>
                      {{ t('common.edit') }}
                    </button>
                    <button class="icon-btn delete-btn" @click="deleteIntervention" :disabled="deleting">
                      <i :class="deleting ? 'bi bi-hourglass-split' : 'bi bi-trash3'"></i>
                      {{ deleting ? t('common.deleting') : t('common.delete') }}
                    </button>
                  </div>
                </div>

                <div class="info-grid">
                  <div class="info-row">
                    <span class="info-label">{{ t('interventions.interventionId') }}</span>
                    <span class="info-value">{{ intervention.idIntervention }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('common.type') }}</span>
                    <span class="info-value">
                      <span class="type-badge-small" :class="getTypeClass(intervention.type)">
                        <i :class="getTypeIcon(intervention.type)"></i>
                        {{ getTypeLabel(intervention.type) }}
                      </span>
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('common.date') }}</span>
                    <span class="info-value">{{ formatDate(intervention.date) }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('interventions.cost') }}</span>
                    <span class="info-value cost-value-display">{{ formatCurrency(intervention.cout) }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('interventions.garage') }}</span>
                    <span class="info-value">{{ intervention.garage }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('vehicles.vehicleId') }}</span>
                    <span class="info-value">{{ intervention.voiture_id }}</span>
                  </div>
                </div>

                <div v-if="intervention.remarques" class="remarks-section">
                  <h4 class="remarks-title">
                    <i class="bi bi-chat-left-text"></i>
                    {{ t('interventions.remarks') }}
                  </h4>
                  <div class="remarks-content">
                    {{ intervention.remarques }}
                  </div>
                </div>

                <div v-else class="no-remarks">
                  <i class="bi bi-chat-left-text"></i>
                  <span>{{ t('interventions.noRemarks') }}</span>
                </div>
              </div>

              <!-- Edit Form -->
              <div v-else class="edit-card card">
                <div class="card-header-section">
                  <h3>{{ t('interventions.editIntervention') }}</h3>
                </div>

                <form @submit.prevent="updateIntervention" class="edit-form">
                  <div class="form-grid">
                    <div class="form-group full-width">
                      <label>{{ t('vehicles.vehicle') }} *</label>
                      <select v-model="form.voiture_id" class="form-input" required>
                        <option value="">{{ t('common.select') }}</option>
                        <option 
                          v-for="voiture in voitures" 
                          :key="voiture.idVoiture" 
                          :value="voiture.idVoiture"
                        >
                          {{ voiture.marque }} {{ voiture.modele }} ({{ voiture.annee }})
                        </option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>{{ t('interventions.interventionType') }} *</label>
                      <select v-model="form.type" class="form-input" required>
                        <option value="">{{ t('common.select') }}</option>
                        <option value="Vidange">{{ t('interventions.types.oilChange') }}</option>
                        <option value="Révision">{{ t('interventions.types.revision') }}</option>
                        <option value="Réparation">{{ t('interventions.types.repair') }}</option>
                        <option value="Pneus">{{ t('interventions.types.tires') }}</option>
                        <option value="Freins">{{ t('interventions.types.brakes') }}</option>
                        <option value="Batterie">{{ t('interventions.types.battery') }}</option>
                        <option value="Climatisation">{{ t('interventions.types.airConditioning') }}</option>
                        <option value="Contrôle technique">{{ t('interventions.types.technicalControl') }}</option>
                        <option value="Autre">{{ t('common.other') }}</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>{{ t('common.date') }} *</label>
                      <input 
                        type="date" 
                        v-model="form.date" 
                        class="form-input" 
                        :max="today"
                        required
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('interventions.cost') }} (€) *</label>
                      <input 
                        type="number" 
                        v-model="form.cout" 
                        step="0.01"
                        min="0"
                        class="form-input" 
                        placeholder="0.00"
                        required
                      />
                    </div>

                    <div class="form-group full-width">
                      <label>{{ t('interventions.garage') }} *</label>
                      <input 
                        v-model="form.garage" 
                        class="form-input" 
                        :placeholder="t('interventions.garageName')"
                        required
                      />
                    </div>

                    <div class="form-group full-width">
                      <label>{{ t('interventions.remarks') }}</label>
                      <textarea 
                        v-model="form.remarques" 
                        class="form-input remarks-textarea" 
                        rows="5"
                        :placeholder="t('interventions.remarksPlaceholder')"
                      ></textarea>
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
        </div>

        <!-- Error State -->
        <div v-else class="error-state card">
          <i class="bi bi-exclamation-triangle"></i>
          <h3>{{ t('interventions.interventionNotFound') }}</h3>
          <p>{{ t('interventions.interventionNotFoundMessage') }}</p>
          <router-link to="/interventions/catalogue" class="btn-primary">
            <i class="bi bi-arrow-left"></i>
            {{ t('interventions.backToCatalog') }}
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Sidebar from '../sidebar.vue';
import { inject } from 'vue';
import alerts from '@/utils/alerts';
import { useI18n } from 'vue-i18n';

export default {
  name: "DetailsIntervention",
  props: ["idIntervention"],
  components: { Sidebar },
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  data() {
    return {
      intervention: null,
      editMode: false,
      loading: false,
      saving: false,
      deleting: false,
      user: null,
      voitures: [],
      form: {
        voiture_id: "",
        type: "",
        date: "",
        cout: "",
        garage: "",
        remarques: ""
      },
      menuItems: [
        { label: this.t('menu.home'), to: "/" },
        { label: this.t('menu.dashboard'), to: "/admindashboard" },
        { label: this.t('menu.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('menu.interventions'), to: "/interventions/catalogue" },
        { label: this.t('menu.alerts'), to: "/alertes" }
      ],
    };
  },
  computed: {
    today() {
      return new Date().toISOString().split('T')[0];
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
    async getUser() {
      try {
        const token = localStorage.getItem("token");
        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` }
        });

        this.user = res.data;
      } catch (error) {
        console.error("Error fetching user:", error);
        this.$router.push("/login");
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

    async fetchIntervention() {
      this.loading = true;
      try {
        const token = localStorage.getItem("token") || "";
        const res = await axios.get(`http://127.0.0.1:8000/api/interventions/${this.idIntervention}`, {
          headers: {
            Authorization: token ? `Bearer ${token}` : "",
            Accept: "application/json"
          }
        });
        this.intervention = res.data;

        this.form = {
          voiture_id: res.data.voiture_id ?? "",
          type: res.data.type ?? "",
          date: res.data.date ?? "",
          cout: res.data.cout ?? "",
          garage: res.data.garage ?? "",
          remarques: res.data.remarques ?? ""
        };
      } catch (err) {
        console.error("fetchIntervention error:", err);
        const serverMsg = err.response?.data ?? err.response?.data?.message ?? err.message;
        await alerts.alertError(
          this.t('errors.loadingError'),
          this.t('errors.loadingIntervention') + (typeof serverMsg === "string" ? serverMsg : JSON.stringify(serverMsg))
        );
      } finally {
        this.loading = false;
      }
    },

    async updateIntervention() {
      if (!this.form.type || !this.form.date || !this.form.cout || !this.form.garage) {
        await alerts.alertWarning(
          this.t('validation.requiredFields'),
          this.t('validation.allRequiredFields')
        );
        return;
      }

      const token = localStorage.getItem("token");
      if (!token) {
        await alerts.alertError(
          this.t('errors.unauthorized'),
          this.t('errors.mustBeLoggedIn')
        );
        return;
      }

      this.saving = true;
      try {
        const payload = {
          voiture_id: this.form.voiture_id,
          type: this.form.type,
          date: this.form.date,
          cout: parseFloat(this.form.cout),
          garage: this.form.garage,
          remarques: this.form.remarques || null
        };

        const res = await axios.put(`http://127.0.0.1:8000/api/interventions/${this.idIntervention}`, payload, {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
            "Content-Type": "application/json"
          }
        });

        this.intervention = res.data ?? { ...payload };
        this.form = { ...this.intervention };
        this.editMode = false;
        await alerts.alertSuccess(
          this.t('common.updated'),
          this.t('interventions.interventionUpdated')
        );
        
        // Refresh to get updated voiture data
        await this.fetchIntervention();
      } catch (err) {
        console.error("updateIntervention error:", err);
        const msg = err.response?.data?.message || err.response?.data || err.message;
        await alerts.alertError(
          this.t('common.error'),
          this.t('errors.updatingIntervention') + (typeof msg === "string" ? msg : JSON.stringify(msg))
        );
      } finally {
        this.saving = false;
      }
    },

    cancelEdit() {
      this.editMode = false;
      this.form = {
        voiture_id: this.intervention.voiture_id ?? "",
        type: this.intervention.type ?? "",
        date: this.intervention.date ?? "",
        cout: this.intervention.cout ?? "",
        garage: this.intervention.garage ?? "",
        remarques: this.intervention.remarques ?? ""
      };
    },

    async deleteIntervention() {
      const confirmed = await alerts.confirmDelete(this.t('interventions.thisIntervention'));
      if (!confirmed) return;

      const token = localStorage.getItem("token");
      if (!token) {
        await alerts.alertError(
          this.t('errors.unauthorized'),
          this.t('errors.mustBeLoggedInToDelete')
        );
        return;
      }

      this.deleting = true;
      try {
        await axios.delete(`http://127.0.0.1:8000/api/interventions/${this.idIntervention}`, {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json"
          }
        });

        await alerts.alertSuccess(
          this.t('common.deleted'),
          this.t('interventions.interventionDeleted')
        );
        this.$router.push({ name: "CatalogueInterventions" });
      } catch (err) {
        console.error("deleteIntervention error:", err);
        const msg = err.response?.data?.message || err.response?.data || err.message;
        await alerts.alertError(
          this.t('common.error'),
          this.t('errors.deletingIntervention') + (typeof msg === "string" ? msg : JSON.stringify(msg))
        );
      } finally {
        this.deleting = false;
      }
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
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    },

    formatNumber(num) {
      return new Intl.NumberFormat('fr-FR').format(num || 0);
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

    getStatusClass(statut) {
      const statusMap = {
        'En boutique': 'badge-available',
        'En location': 'badge-rented',
        'En maintenance': 'badge-maintenance'
      };
      return statusMap[statut] || 'badge-default';
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
    await this.fetchVoitures();
    await this.fetchIntervention();
  }
};
</script>

<style scoped>
.intervention-header {
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  color: white;
}

.intervention-header .car-title,
.intervention-header .car-subtitle {
  color: white;
}

.intervention-type-display {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.type-icon-large {
  font-size: 2.5rem;
  color: #B4CDED;
}

.cost-badge-large {
  background: white;
  color: #344966;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-size: 1.5rem;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.vehicle-info-card {
  margin-top: 1rem;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  font-size: 1.1rem;
  color: #344966;
}

.card-title i {
  font-size: 1.3rem;
}

.vehicle-main-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #E8F0F7;
}

.vehicle-main-info h4 {
  margin: 0;
  font-size: 1.2rem;
  color: #344966;
}

.vehicle-year {
  background: #E8F0F7;
  color: #344966;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-weight: 600;
}

.vehicle-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.9rem;
  color: #546A88;
}

.meta-item i {
  color: #748BAA;
}

.view-vehicle-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  background: #344966;
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: background 0.2s;
}

.view-vehicle-btn:hover {
  background: #546A88;
}

.vehicle-not-found {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  background: #FEF5E7;
  border: 1px solid #F8C471;
  border-radius: 8px;
  color: #DC7633;
}

.garage-card {
  margin-top: 1rem;
}

.garage-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #344966;
  margin: 0;
}

.type-badge-small {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
  color: white;
}

.type-vidange { background: #3498db; }
.type-revision { background: #9b59b6; }
.type-reparation { background: #e74c3c; }
.type-pneus { background: #34495e; }
.type-freins { background: #c0392b; }
.type-batterie { background: #f39c12; }
.type-climatisation { background: #1abc9c; }
.type-controle { background: #27ae60; }
.type-autre { background: #95a5a6; }

.cost-value-display {
  font-weight: 700;
  color: #27ae60;
  font-size: 1.1rem;
}

.remarks-section {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #E8F0F7;
}

.remarks-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 1rem;
  color: #344966;
}

.remarks-content {
  padding: 1rem;
  background: #F9FBFD;
  border-radius: 8px;
  line-height: 1.6;
  color: #546A88;
  white-space: pre-wrap;
}

.no-remarks {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  background: #F9FBFD;
  border-radius: 8px;
  color: #95a5a6;
  font-style: italic;
  margin-top: 1.5rem;
}

.remarks-textarea {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
}

.badge-etat {
  padding: 0.3rem 0.7rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
  background: #E8F0F7;
  color: #344966;
}

.badge-statut {
  padding: 0.3rem 0.7rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
}

.badge-available {
  background: #d4edda;
  color: #155724;
}

.badge-rented {
  background: #fff3cd;
  color: #856404;
}

.badge-maintenance {
  background: #f8d7da;
  color: #721c24;
}

.full-width {
  grid-column: 1 / -1;
}
</style>
