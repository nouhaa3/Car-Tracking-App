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

        <!-- Page Header -->
        <div class="add-car-header">
          <router-link to="/voitures/cataloguevoitures" class="btn-back">
            <i class="bi bi-arrow-left"></i> {{ t('cars.backToCatalog') }}
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
            <!-- Étape 1: Détails -->
            <div v-if="currentStep === 1" class="step-content">
              <form class="add-car-form">
                <div class="form-row">
                  <div class="form-group">
                    <label>{{ t('cars.brand') }} <span class="required">*</span></label>
                    <input 
                      v-model="form.marque" 
                      type="text" 
                      :placeholder="t('cars.brandPlaceholder')"
                      :class="{ error: errors.marque }"
                    />
                    <span v-if="errors.marque" class="error-message">{{ errors.marque }}</span>
                  </div>

                  <div class="form-group">
                    <label>{{ t('cars.model') }} <span class="required">*</span></label>
                    <input 
                      v-model="form.modele" 
                      type="text" 
                      :placeholder="t('cars.modelPlaceholder')"
                      :class="{ error: errors.modele }"
                    />
                    <span v-if="errors.modele" class="error-message">{{ errors.modele }}</span>
                  </div>

                  <div class="form-group">
                    <label>{{ t('cars.year') }} <span class="required">*</span></label>
                    <input 
                      v-model="form.annee" 
                      type="number" 
                      :placeholder="t('cars.yearPlaceholder')"
                      :class="{ error: errors.annee }"
                    />
                    <span v-if="errors.annee" class="error-message">{{ errors.annee }}</span>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group">
                    <label>{{ t('cars.mileage') }} <span class="required">*</span></label>
                    <input 
                      v-model="form.kilometrage" 
                      type="number" 
                      :placeholder="t('cars.mileagePlaceholder')"
                      :class="{ error: errors.kilometrage }"
                    />
                    <span v-if="errors.kilometrage" class="error-message">{{ errors.kilometrage }}</span>
                  </div>

                  <div class="form-group">
                    <label>{{ t('cars.condition') }} <span class="required">*</span></label>
                    <select v-model="form.etat" :class="{ error: errors.etat }">
                      <option value="">{{ t('cars.selectCondition') }}</option>
                      <option value="Neuf">{{ t('cars.conditionNew') }}</option>
                      <option value="Bon">{{ t('cars.conditionGood') }}</option>
                      <option value="Usagé">{{ t('cars.conditionUsed') }}</option>
                      <option value="Endommagé">{{ t('cars.conditionDamaged') }}</option>
                    </select>
                    <span v-if="errors.etat" class="error-message">{{ errors.etat }}</span>
                  </div>

                  <div class="form-group">
                    <label>{{ t('cars.status') }} <span class="required">*</span></label>
                    <select v-model="form.statut" :class="{ error: errors.statut }">
                      <option value="">{{ t('cars.selectStatus') }}</option>
                      <option value="En boutique">{{ t('cars.statusInShop') }}</option>
                      <option value="En location">{{ t('cars.statusRented') }}</option>
                      <option value="En maintenance">{{ t('cars.statusMaintenance') }}</option>
                    </select>
                    <span v-if="errors.statut" class="error-message">{{ errors.statut }}</span>
                  </div>
                </div>
              </form>
            </div>

            <!-- Étape 2: Photo -->
            <div v-if="currentStep === 2" class="step-content">
              <div class="upload-section">
                <div v-if="!form.image" class="upload-box" @click="triggerFileInput">
                  <div class="upload-icon">
                    <i class="bi bi-cloud-upload"></i>
                  </div>
                  <h3>{{ t('cars.uploadPhoto') }}</h3>
                  <p>{{ t('cars.uploadInstructions') }}</p>
                  <span class="upload-hint">{{ t('cars.uploadHint') }}</span>
                  <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" hidden />
                </div>

                <div v-else class="image-preview-container">
                  <div class="image-preview">
                    <img :src="form.image" :alt="t('cars.preview')" />
                  </div>
                  <div class="image-actions">
                    <button @click="triggerFileInput" class="image-action-btn change-btn" type="button">
                      <i class="bi bi-arrow-repeat"></i> {{ t('common.change') }}
                    </button>
                    <button @click="removeImage" class="image-action-btn cancel-btn" type="button">
                      <i class="bi bi-x-circle"></i> {{ t('common.cancel') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Étape 3: Validation -->
            <div v-if="currentStep === 3" class="step-content">
              <div class="validation-section">
                <p class="validation-intro">{{ t('cars.validationIntro') }}</p>

                <div class="validation-grid">
                  <div class="validation-card">
                    <h3>{{ t('cars.generalInfo') }}</h3>
                    <div class="info-list">
                      <div class="info-item">
                        <span class="info-label">{{ t('cars.brand') }}:</span>
                        <span class="info-value">{{ form.marque }}</span>
                      </div>
                      <div class="info-item">
                        <span class="info-label">{{ t('cars.model') }}:</span>
                        <span class="info-value">{{ form.modele }}</span>
                      </div>
                      <div class="info-item">
                        <span class="info-label">{{ t('cars.year') }}:</span>
                        <span class="info-value">{{ form.annee }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="validation-card">
                    <h3>{{ t('cars.conditionAndStatus') }}</h3>
                    <div class="info-list">
                      <div class="info-item">
                        <span class="info-label">{{ t('cars.mileage') }}:</span>
                        <span class="info-value">{{ form.kilometrage }} km</span>
                      </div>
                      <div class="info-item">
                        <span class="info-label">{{ t('cars.condition') }}:</span>
                        <span class="info-value">{{ form.etat }}</span>
                      </div>
                      <div class="info-item">
                        <span class="info-label">{{ t('cars.status') }}:</span>
                        <span class="info-value">{{ form.statut }}</span>
                      </div>
                    </div>
                  </div>

                  <div v-if="form.image" class="validation-image-card">
                    <h3>{{ t('cars.photo') }}</h3>
                    <div class="validation-image">
                      <img :src="form.image" :alt="t('cars.preview')" />
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
              <i class="bi bi-check-lg"></i> {{ isSubmitting ? t('cars.adding') : t('common.confirm') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { inject, ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from "axios";
import Sidebar from '../sidebar.vue';
import alerts from '@/utils/alerts';

export default {
  name: "VoitureForm",
  components: { Sidebar },
  setup() {
    const { t } = useI18n();
    const theme = inject("theme");
    const router = useRouter();
    const isExpanded = ref(false);
    const user = ref(null);
    const currentStep = ref(1);
    const fileInput = ref(null);
    const isSubmitting = ref(false);

    const steps = [t('cars.stepDetails'), t('cars.stepPhoto'), t('cars.stepValidation')];

    const form = reactive({
      marque: "",
      modele: "",
      annee: "",
      kilometrage: "",
      etat: "",
      statut: "",
      image: null,
      imageFile: null,
    });

    const errors = reactive({});

    const menuItems = [
      { label: t('nav.home'), to: "/" },
      { label: t('nav.dashboard'), to: "/admindashboard" },
      { label: t('nav.catalog'), to: "/voitures/cataloguevoitures" },
      { label: t('nav.interventions'), to: "/interventions/catalogue" },
      { label: t('nav.alerts'), to: "/alertes" },
    ];

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
        console.error("Erreur récupération user:", error);
        router.push("/login");
      }
    };

    const validateStep = async (step) => {
      Object.keys(errors).forEach(key => delete errors[key]);

      if (step === 1) {
        if (!form.marque) errors.marque = t('cars.errors.brandRequired');
        if (!form.modele) errors.modele = t('cars.errors.modelRequired');
        if (!form.annee) errors.annee = t('cars.errors.yearRequired');
        if (!form.kilometrage) errors.kilometrage = t('cars.errors.mileageRequired');
        if (!form.etat) errors.etat = t('cars.errors.conditionRequired');
        if (!form.statut) errors.statut = t('cars.errors.statusRequired');
      }
      if (step === 2) {
        if (!form.imageFile) {
          errors.image = t('cars.errors.photoRequired');
          await alerts.alertWarning(t('cars.photoWarning'));
          return false;
        }
      }
      return Object.keys(errors).length === 0;
    };

    const nextStep = async () => {
      if (await validateStep(currentStep.value)) {
        currentStep.value++;
      }
    };

    const prevStep = () => {
      if (currentStep.value > 1) currentStep.value--;
    };

    const triggerFileInput = () => {
      fileInput.value.click();
    };

    const handleFileUpload = (e) => {
      const file = e.target.files[0];
      if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
          alerts.alertWarning(t('cars.fileSizeWarning'));
          return;
        }
        form.image = URL.createObjectURL(file);
        form.imageFile = file;
      }
    };

    const removeImage = () => {
      form.image = null;
      form.imageFile = null;
      if (fileInput.value) {
        fileInput.value.value = '';
      }
    };

    const submitForm = async () => {
      if (!await validateStep(3)) {
        return;
      }

      isSubmitting.value = true;

      try {
        const formData = new FormData();
        formData.append("marque", form.marque);
        formData.append("modele", form.modele);
        formData.append("annee", form.annee);
        formData.append("kilometrage", form.kilometrage);
        formData.append("etat", form.etat);
        formData.append("statut", form.statut);

        if (form.imageFile) {
          formData.append("image", form.imageFile);
        }

        const response = await axios.post("http://127.0.0.1:8000/api/voitures", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        console.log("Voiture ajoutée avec succès:", response.data);
        router.push("/voitures/cataloguevoitures");
      } catch (err) {
        console.error("Erreur complète:", err);
        console.error("Erreur response:", err.response);
        console.error("Erreur data:", err.response?.data);
        console.error("Erreur status:", err.response?.status);
        
        let errorMessage = t('cars.addError');
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
    });

    return {
      t,
      theme,
      isExpanded,
      user,
      currentStep,
      steps,
      form,
      errors,
      menuItems,
      fileInput,
      isSubmitting,
      nextStep,
      prevStep,
      triggerFileInput,
      handleFileUpload,
      removeImage,
      submitForm,
      logout,
    };
  },
};
</script>