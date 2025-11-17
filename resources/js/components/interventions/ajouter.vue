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

        <!-- Page Header -->
        <div class="add-car-header">
          <router-link to="/interventions/catalogue" class="btn-back">
            <i class="bi bi-arrow-left"></i> {{ t('interventions.backToCatalog') }}
          </router-link>
        </div>

        <div class="stepper-container">
          <!-- Stepper Header -->
          <div class="stepper-header">
            <div
              v-for="(step, index) in steps"
              :key="index"
              class="step-item"
              :class="{ active: currentStep === index + 1, completed: currentStep > index + 1 }"
            >
              <div class="step-circle">
                <i v-if="currentStep > index + 1" class="bi bi-check-lg"></i>
                <span v-else>{{ index + 1 }}</span>
              </div>
              <span class="step-label">{{ step }}</span>
              <div v-if="index < steps.length - 1" class="step-line"></div>
            </div>
          </div>

          <!-- Step Content -->
          <div class="stepper-body">
            <!-- Étape 1: Sélection véhicule et type -->
            <div v-if="currentStep === 1" class="step-content">
              <form class="add-car-form">
                <div class="form-row">
                  <div class="form-group full-width">
                    <label>{{ t('vehicles.vehicle') }} <span class="required">*</span></label>
                    <select 
                      v-model="form.voiture_id" 
                      :class="{ error: errors.voiture_id }"
                      class="vehicle-select"
                    >
                      <option value="">{{ t('interventions.selectVehicle') }}</option>
                      <option 
                        v-for="voiture in voitures" 
                        :key="voiture.idVoiture" 
                        :value="voiture.idVoiture"
                      >
                        {{ voiture.marque }} {{ voiture.modele }} ({{ voiture.annee }}) - {{ voiture.statut }}
                      </option>
                    </select>
                    <span v-if="errors.voiture_id" class="error-message">{{ errors.voiture_id }}</span>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group full-width">
                    <label>{{ t('interventions.interventionType') }} <span class="required">*</span></label>
                    <select 
                      v-model="form.type" 
                      :class="{ error: errors.type }"
                    >
                      <option value="">{{ t('interventions.selectType') }}</option>
                      <option 
                        v-for="type in typesIntervention" 
                        :key="type.value" 
                        :value="type.value"
                      >
                        {{ type.label }}
                      </option>
                    </select>
                    <span v-if="errors.type" class="error-message">{{ errors.type }}</span>
                  </div>
                </div>
              </form>
            </div>

            <!-- Étape 2: Détails de l'intervention -->
            <div v-if="currentStep === 2" class="step-content">
              <form class="add-car-form">
                <div class="form-row">
                  <div class="form-group">
                    <label>{{ t('interventions.interventionDate') }} <span class="required">*</span></label>
                    <input 
                      v-model="form.date" 
                      type="date" 
                      :max="today"
                      :class="{ error: errors.date }"
                    />
                    <span v-if="errors.date" class="error-message">{{ errors.date }}</span>
                  </div>

                  <div class="form-group">
                    <label>{{ t('interventions.cost') }} (€) <span class="required">*</span></label>
                    <input 
                      v-model="form.cout" 
                      type="number" 
                      step="0.01"
                      min="0"
                      :placeholder="t('interventions.costPlaceholder')"
                      :class="{ error: errors.cout }"
                    />
                    <span v-if="errors.cout" class="error-message">{{ errors.cout }}</span>
                  </div>

                  <div class="form-group">
                    <label>{{ t('interventions.garage') }} <span class="required">*</span></label>
                    <input 
                      v-model="form.garage" 
                      type="text" 
                      :placeholder="t('interventions.garagePlaceholder')"
                      :class="{ error: errors.garage }"
                    />
                    <span v-if="errors.garage" class="error-message">{{ errors.garage }}</span>
                  </div>
                </div>
              </form>
            </div>

            <!-- Étape 3: Remarques -->
            <div v-if="currentStep === 3" class="step-content">
              <form class="add-car-form">
                <div class="form-row">
                  <div class="form-group full-width">
                    <label>{{ t('interventions.remarks') }}</label>
                    <textarea 
                      v-model="form.remarques" 
                      rows="6"
                      :placeholder="t('interventions.remarksPlaceholder')"
                      class="remarks-textarea"
                    ></textarea>
                    <small class="field-hint">
                      <i class="bi bi-info-circle"></i>
                      {{ t('interventions.remarksHint') }}
                    </small>
                  </div>
                </div>
              </form>
            </div>

            <!-- Étape 4: Validation -->
            <div v-if="currentStep === 4" class="step-content">
              <div class="validation-section">
                <p class="validation-intro">{{ t('interventions.validationIntro') }}</p>

                <div class="validation-grid">
                  <div class="validation-card">
                    <h3>{{ t('vehicles.vehicle') }}</h3>
                    <div class="info-list">
                      <div class="info-item">
                        <span class="info-label">{{ t('vehicles.vehicle') }}:</span>
                        <span class="info-value">{{ selectedVehicleName }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="validation-card">
                    <h3>{{ t('interventions.interventionType') }}</h3>
                    <div class="info-list">
                      <div class="info-item">
                        <span class="info-label">{{ t('common.type') }}:</span>
                        <span class="info-value intervention-type">
                          <i :class="getTypeIcon(form.type)"></i>
                          {{ getTypeLabel(form.type) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="validation-card">
                    <h3>{{ t('common.details') }}</h3>
                    <div class="info-list">
                      <div class="info-item">
                        <span class="info-label">{{ t('common.date') }}:</span>
                        <span class="info-value">{{ formatDate(form.date) }}</span>
                      </div>
                      <div class="info-item">
                        <span class="info-label">{{ t('interventions.garage') }}:</span>
                        <span class="info-value">{{ form.garage }}</span>
                      </div>
                      <div class="info-item">
                        <span class="info-label">{{ t('interventions.cost') }}:</span>
                        <span class="info-value cost-highlight">{{ formatCurrency(form.cout) }}</span>
                      </div>
                    </div>
                  </div>

                  <div v-if="form.remarques" class="validation-card full-width">
                    <h3>{{ t('interventions.remarks') }}</h3>
                    <div class="info-list">
                      <div class="info-item remarks-display">
                        <p>{{ form.remarques }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stepper Footer -->
          <div class="stepper-footer">
            <button 
              v-if="currentStep > 1" 
              @click="prevStep" 
              class="btn-step btn-secondary"
            >
              <i class="bi bi-arrow-left"></i> {{ t('common.previous') }}
            </button>
            <div class="spacer"></div>
            <button 
              v-if="currentStep < steps.length" 
              @click="nextStep" 
              class="btn-step btn-primary"
            >
              {{ t('common.next') }} <i class="bi bi-arrow-right"></i>
            </button>
            <button 
              v-else 
              @click="submitForm" 
              class="btn-step btn-success"
              :disabled="isSubmitting"
            >
              <i class="bi bi-check-lg"></i> {{ isSubmitting ? t('common.saving') : t('common.confirm') }}
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import { inject, ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from "axios";
import Sidebar from '../sidebar.vue';
import alerts from '@/utils/alerts';
import { useI18n } from 'vue-i18n';

export default {
  name: "AjouterIntervention",
  components: { Sidebar },
  setup() {
    const theme = inject("theme");
    const router = useRouter();
    const { t } = useI18n();
    const isExpanded = ref(false);
    const user = ref(null);
    const currentStep = ref(1);
    const isSubmitting = ref(false);
    const voitures = ref([]);

    const steps = computed(() => [
      t('interventions.steps.vehicle'),
      t('interventions.steps.details'),
      t('interventions.steps.remarks'),
      t('interventions.steps.validation')
    ]);

    const typesIntervention = computed(() => [
      { value: 'Vidange', label: t('interventions.types.oilChange'), icon: 'bi-droplet-fill' },
      { value: 'Révision', label: t('interventions.types.revision'), icon: 'bi-tools' },
      { value: 'Réparation', label: t('interventions.types.repair'), icon: 'bi-wrench-adjustable' },
      { value: 'Pneus', label: t('interventions.types.tires'), icon: 'bi-circle' },
      { value: 'Freins', label: t('interventions.types.brakes'), icon: 'bi-stop-circle-fill' },
      { value: 'Batterie', label: t('interventions.types.battery'), icon: 'bi-lightning-charge-fill' },
      { value: 'Climatisation', label: t('interventions.types.airConditioning'), icon: 'bi-snow' },
      { value: 'Contrôle technique', label: t('interventions.types.technicalControl'), icon: 'bi-clipboard-check-fill' },
      { value: 'Autre', label: t('common.other'), icon: 'bi-gear-fill' }
    ]);

    const form = reactive({
      voiture_id: "",
      type: "",
      date: "",
      cout: "",
      garage: "",
      remarques: "",
    });

    const errors = reactive({});

    const menuItems = computed(() => [
      { label: t('menu.dashboard'), to: "/admindashboard" },
      { label: t('menu.catalog'), to: "/voitures/cataloguevoitures" },
      { label: t('menu.interventions'), to: "/interventions/catalogue" },
      { label: t('menu.alerts'), to: "/alertes" },
    ]);

    const today = computed(() => {
      return new Date().toISOString().split('T')[0];
    });

    const selectedVehicleName = computed(() => {
      if (!form.voiture_id) return t('common.notSelected');
      const voiture = voitures.value.find(v => v.idVoiture == form.voiture_id);
      if (!voiture) return t('common.notSelected');
      return `${voiture.marque} ${voiture.modele} (${voiture.annee})`;
    });

    const getUser = async () => {
      try {
        const token = localStorage.getItem("token");
        if (!token) {
          router.push("/login");
          return;
        }

        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` },
        });

        user.value = res.data;
      } catch (error) {
        console.error("Error fetching user:", error);
        router.push("/login");
      }
    };

    const fetchVoitures = async () => {
      try {
        const token = localStorage.getItem("token");
        const response = await axios.get("http://127.0.0.1:8000/api/voitures", {
          headers: { Authorization: `Bearer ${token}` },
        });

        voitures.value = response.data || [];
      } catch (error) {
        console.error("Error fetching voitures:", error);
        await alerts.alertError(
          t('common.error'),
          t('errors.loadingVehicles')
        );
      }
    };

    const validateStep = (step) => {
      Object.keys(errors).forEach(key => delete errors[key]);

      if (step === 1) {
        if (!form.voiture_id) errors.voiture_id = t('validation.vehicleRequired');
        if (!form.type) errors.type = t('validation.interventionTypeRequired');
      }
      if (step === 2) {
        if (!form.date) errors.date = t('validation.dateRequired');
        if (!form.cout) {
          errors.cout = t('validation.costRequired');
        } else if (parseFloat(form.cout) < 0) {
          errors.cout = t('validation.costPositive');
        }
        if (!form.garage) errors.garage = t('validation.garageRequired');
      }
      return Object.keys(errors).length === 0;
    };

    const nextStep = () => {
      if (validateStep(currentStep.value)) {
        currentStep.value++;
      }
    };

    const prevStep = () => {
      if (currentStep.value > 1) currentStep.value--;
    };

    const formatDate = (dateString) => {
      if (!dateString) return "";
      const date = new Date(dateString);
      return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(amount || 0);
    };

    const getTypeIcon = (type) => {
      const typeObj = typesIntervention.value.find(t => t.value === type);
      return typeObj ? typeObj.icon : 'bi-wrench';
    };

    const getTypeLabel = (type) => {
      const typeObj = typesIntervention.value.find(t => t.value === type);
      return typeObj ? typeObj.label : type;
    };

    const submitForm = async () => {
      if (!validateStep(2)) {
        return;
      }

      isSubmitting.value = true;

      try {
        const payload = {
          voiture_id: form.voiture_id,
          type: form.type,
          date: form.date,
          cout: parseFloat(form.cout),
          garage: form.garage,
          remarques: form.remarques || null
        };

        const response = await axios.post("http://127.0.0.1:8000/api/interventions", payload, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        console.log("Intervention added successfully:", response.data);
        await alerts.alertSuccess(
          t('common.saved'),
          t('interventions.interventionSaved')
        );
        router.push("/interventions/catalogue");
      } catch (err) {
        console.error("Complete error:", err);
        console.error("Error response:", err.response);
        console.error("Error data:", err.response?.data);

        let errorMessage = t('errors.addingIntervention');
        if (err.response?.data?.message) {
          errorMessage = err.response.data.message;
        } else if (err.response?.data?.error) {
          errorMessage = err.response.data.error;
        } else if (err.response?.data) {
          errorMessage = JSON.stringify(err.response.data);
        }

        await alerts.alertError(t('common.error'), errorMessage);
      } finally {
        isSubmitting.value = false;
      }
    };

    const logout = () => {
      localStorage.removeItem("token");
      router.push("/login");
    };

    onMounted(() => {
      getUser();
      fetchVoitures();
    });

    return {
      theme,
      isExpanded,
      user,
      currentStep,
      steps,
      form,
      errors,
      menuItems,
      isSubmitting,
      voitures,
      typesIntervention,
      today,
      selectedVehicleName,
      nextStep,
      prevStep,
      submitForm,
      logout,
      formatDate,
      formatCurrency,
      getTypeIcon,
      getTypeLabel,
      t
    };
  },
};
</script>

<style scoped>
.full-width {
  grid-column: 1 / -1;
}

.vehicle-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 0.95rem;
  background: white;
  cursor: pointer;
  transition: border-color 0.2s;
}

.vehicle-select:hover {
  border-color: #344966;
}

.vehicle-select:focus {
  outline: none;
  border-color: #344966;
  box-shadow: 0 0 0 3px rgba(52, 73, 102, 0.1);
}

.remarks-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 0.95rem;
  font-family: inherit;
  resize: vertical;
  min-height: 120px;
  transition: border-color 0.2s;
}

.remarks-textarea:focus {
  outline: none;
  border-color: #344966;
  box-shadow: 0 0 0 3px rgba(52, 73, 102, 0.1);
}

.field-hint {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 0.5rem;
  font-size: 0.85rem;
  color: #748BAA;
}

.field-hint i {
  color: #B4CDED;
}

.intervention-type {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: #344966;
}

.intervention-type i {
  font-size: 1.2rem;
}

.cost-highlight {
  font-weight: 700;
  color: #27ae60;
  font-size: 1.1rem;
}

.remarks-display {
  display: block;
  width: 100%;
}

.remarks-display p {
  margin: 0;
  padding: 0.75rem;
  background: #F9FBFD;
  border-radius: 8px;
  line-height: 1.6;
  color: #546A88;
  white-space: pre-wrap;
}

.validation-card.full-width {
  grid-column: 1 / -1;
}
</style>
