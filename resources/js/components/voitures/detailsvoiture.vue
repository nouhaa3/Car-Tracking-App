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

        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
          <router-link to="/voitures/cataloguevoitures" class="breadcrumb-link">
            <i class="bi bi-arrow-left"></i>
            {{ t('vehicles.backToCatalog') }}
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
              <div class="image-card card" :class="{ 'edit-mode-image': editMode }">
                <img 
                  :src="voiture.image ? `http://127.0.0.1:8000/storage/${voiture.image}` : '/images/default.png'" 
                  :alt="`${voiture.marque} ${voiture.modele}`" 
                  class="car-details-img"
                />
                
                <!-- Edit Mode Hover Overlay -->
                <div v-if="editMode" class="image-hover-overlay" @click="$refs.imageInput.click()">
                  <span>{{ t('cars.changeImage') }}</span>
                </div>
                
                <input 
                  type="file" 
                  ref="imageInput" 
                  name="car_image"
                  @change="handleImageSelect" 
                  accept="image/*"
                  style="display: none"
                />
              </div>

              <!-- Header Card -->
              <div class="car-details-header card">
                <div class="header-main">
                  <h1 class="car-title">{{ voiture.marque }} {{ voiture.modele }}</h1>
                  <p class="car-year" style="text-align: center;">{{ t('cars.year') }} {{ voiture.annee }}</p>
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
                      <span>{{ t('common.edit') }}</span>
                    </button>
                    <button class="btn-action btn-delete" @click="deleteVoiture" :disabled="deleting">
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
                  <div class="info-grid">
                    <div class="info-row">
                      <label class="info-label">{{ t('cars.vehicleId') }}</label>
                      <span class="info-value">{{ voiture.idVoiture }}</span>
                    </div>
                    
                    <div class="info-row">
                      <label class="info-label">{{ t('cars.brand') }} *</label>
                      <input 
                        v-model="form.marque" 
                        name="marque"
                        class="form-input-inline" 
                        :placeholder="t('cars.brandPlaceholder')"
                        required
                      />
                    </div>

                    <div class="info-row">
                      <label class="info-label">{{ t('cars.model') }} *</label>
                      <input 
                        v-model="form.modele" 
                        name="modele"
                        class="form-input-inline" 
                        :placeholder="t('cars.modelPlaceholder')"
                        required
                      />
                    </div>

                    <div class="info-row">
                      <label class="info-label">{{ t('cars.year') }}</label>
                      <input 
                        type="number" 
                        v-model="form.annee" 
                        name="annee"
                        class="form-input-inline" 
                        :placeholder="t('cars.yearPlaceholder')"
                        min="1900"
                        :max="new Date().getFullYear()"
                      />
                    </div>

                    <div class="info-row">
                      <label class="info-label">{{ t('cars.mileage') }}</label>
                      <input 
                        type="number" 
                        v-model="form.kilometrage" 
                        name="kilometrage"
                        class="form-input-inline" 
                        :placeholder="t('cars.mileagePlaceholder')"
                        min="0"
                      />
                    </div>

                    <div class="info-row">
                      <label class="info-label">{{ t('cars.condition') }}</label>
                      <select v-model="form.etat" name="etat" class="form-input-inline">
                        <option value="">{{ t('common.select') }}</option>
                        <option value="Neuf">{{ t('cars.conditionNew') }}</option>
                        <option value="Bon">{{ t('cars.conditionGood') }}</option>
                        <option value="Usagé">{{ t('cars.conditionUsed') }}</option>
                        <option value="Endommagé">{{ t('cars.conditionDamaged') }}</option>
                      </select>
                    </div>

                    <div class="info-row">
                      <label class="info-label">{{ t('cars.status') }}</label>
                      <select v-model="form.statut" name="statut" class="form-input-inline">
                        <option value="">{{ t('common.select') }}</option>
                        <option value="En boutique">{{ t('cars.statusInShop') }}</option>
                        <option value="En location">{{ t('cars.statusRented') }}</option>
                        <option value="En maintenance">{{ t('cars.statusMaintenance') }}</option>
                      </select>
                    </div>

                    <div class="info-row">
                      <label class="info-label">{{ t('cars.userId') }}</label>
                      <input 
                        type="number" 
                        v-model="form.user_id" 
                        name="user_id"
                        class="form-input-inline" 
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

          <!-- Documents Section -->
          <div class="documents-section card">
            <DocumentsVehicule :voitureId="idVoiture" />
          </div>
        </div>

        <!-- Error State -->
        <div v-else class="error-state card">
          <h3>{{ t('cars.vehicleNotFound') }}</h3>
          <p>{{ t('cars.vehicleNotFoundMessage') }}</p>
          <router-link to="/voitures/cataloguevoitures" class="btn-primary">
            {{ t('cars.backToCatalog') }}
          </router-link>
        </div>

      </div>
    </div>
    
    <!-- Image Cropper Modal -->
    <div v-if="showCropperModal" class="modal-overlay" @click.self="closeCropper">
      <div class="cropper-modal">
        <div class="cropper-header">
          <h3>{{ t('cars.adjustImage') }}</h3>
          <button class="btn-close" @click="closeCropper">
            ×
          </button>
        </div>
        
        <div class="cropper-container">
          <div class="image-cropper-wrapper">
            <img 
              ref="cropperImage" 
              :src="selectedImagePreview" 
              alt="Crop"
              class="cropper-image"
              @mousedown="startDrag"
              @touchstart="startDrag"
              :style="{
                transform: `translate(${imagePosition.x}px, ${imagePosition.y}px) scale(${imageScale})`,
                cursor: isDragging ? 'grabbing' : 'grab'
              }"
            />
          </div>
        </div>
        
        <div class="cropper-controls">
          <div class="zoom-control">
            <button @click="zoomOut" class="btn-zoom">
              −
            </button>
            <input 
              type="range" 
              v-model="imageScale" 
              name="image_zoom"
              min="0.5" 
              max="3" 
              step="0.1"
              class="zoom-slider"
            />
            <button @click="zoomIn" class="btn-zoom">
              +
            </button>
          </div>
          
          <div class="action-buttons">
            <button @click="closeCropper" class="btn-cancel">
              {{ t('common.cancel') }}
            </button>
            <button @click="applyCrop" class="btn-apply" :disabled="uploadingImage">
              {{ uploadingImage ? t('common.uploading') : t('common.apply') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Sidebar from '../Sidebar.vue';
import DocumentsVehicule from './DocumentsVehicule.vue';
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';
import alerts from '@/utils/alerts';

export default {
  name: "DetailsVoiture",
  props: ["idVoiture"],
  components: { 
    Sidebar,
    DocumentsVehicule
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
      uploadingImage: false,
      deletingImage: false,
      showCropperModal: false,
      selectedImageFile: null,
      selectedImagePreview: null,
      imageScale: 1,
      imagePosition: { x: 0, y: 0 },
      isDragging: false,
      dragStart: { x: 0, y: 0 },
      user: null,
      username: "",
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

    async handleImageSelect(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file type
      if (!file.type.startsWith('image/')) {
        await alerts.alertError(this.t('common.error'), this.t('cars.invalidImageType'));
        return;
      }

      // Validate file size (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        await alerts.alertError(this.t('common.error'), this.t('cars.imageTooLarge'));
        return;
      }

      // Store file and create preview
      this.selectedImageFile = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.selectedImagePreview = e.target.result;
        this.showCropperModal = true;
        this.resetCropperState();
      };
      reader.readAsDataURL(file);
    },

    resetCropperState() {
      this.imageScale = 1;
      this.imagePosition = { x: 0, y: 0 };
      this.isDragging = false;
    },

    startDrag(e) {
      e.preventDefault();
      this.isDragging = true;
      
      const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
      const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
      
      this.dragStart = {
        x: clientX - this.imagePosition.x,
        y: clientY - this.imagePosition.y
      };

      const moveHandler = (e) => {
        if (!this.isDragging) return;
        
        const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
        const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
        
        this.imagePosition = {
          x: clientX - this.dragStart.x,
          y: clientY - this.dragStart.y
        };
      };

      const endHandler = () => {
        this.isDragging = false;
        document.removeEventListener('mousemove', moveHandler);
        document.removeEventListener('mouseup', endHandler);
        document.removeEventListener('touchmove', moveHandler);
        document.removeEventListener('touchend', endHandler);
      };

      document.addEventListener('mousemove', moveHandler);
      document.addEventListener('mouseup', endHandler);
      document.addEventListener('touchmove', moveHandler);
      document.addEventListener('touchend', endHandler);
    },

    zoomIn() {
      this.imageScale = Math.min(3, this.imageScale + 0.1);
    },

    zoomOut() {
      this.imageScale = Math.max(0.5, this.imageScale - 0.1);
    },

    closeCropper() {
      this.showCropperModal = false;
      this.selectedImageFile = null;
      this.selectedImagePreview = null;
      this.$refs.imageInput.value = '';
    },

    async applyCrop() {
      if (!this.selectedImageFile) return;

      this.uploadingImage = true;
      try {
        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append('image', this.selectedImageFile);

        const res = await axios.post(
          `http://127.0.0.1:8000/api/voitures/${this.idVoiture}/image`,
          formData,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data'
            }
          }
        );

        this.voiture.image = res.data.image;
        await alerts.alertSuccess(this.t('common.success'), this.t('cars.imageUpdateSuccess'));
        this.closeCropper();
      } catch (err) {
        console.error("Upload image error:", err);
        await alerts.alertError(this.t('common.error'), err.response?.data?.message || this.t('cars.imageUpdateError'));
      } finally {
        this.uploadingImage = false;
      }
    },

    async deleteCarImage() {
      const confirmed = await alerts.alertConfirm(
        this.t('cars.deleteImageConfirm'),
        this.t('cars.deleteImageMessage')
      );
      if (!confirmed) return;

      this.deletingImage = true;
      try {
        const token = localStorage.getItem("token");
        await axios.delete(
          `http://127.0.0.1:8000/api/voitures/${this.idVoiture}/image`,
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        );

        this.voiture.image = null;
        await alerts.alertSuccess(this.t('common.success'), this.t('cars.imageDeleteSuccess'));
      } catch (err) {
        console.error("Delete image error:", err);
        await alerts.alertError(this.t('common.error'), err.response?.data?.message || this.t('cars.imageDeleteError'));
      } finally {
        this.deletingImage = false;
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

.car-details-left {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.car-details-right {
  height: 100%;
}

.info-card,
.edit-card {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.info-grid {
  flex: 1;
}

/* Image Card with Edit Mode */
.image-card {
  position: relative;
  overflow: hidden;
}

.edit-mode-image {
  cursor: pointer;
}

.image-hover-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(220, 220, 220, 0);
  backdrop-filter: blur(0px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  opacity: 0;
  transition: all 0.4s ease;
  cursor: pointer;
}

.edit-mode-image:hover .image-hover-overlay {
  background: rgba(220, 220, 220, 0.3);
  backdrop-filter: blur(8px);
  opacity: 1;
}

.image-hover-overlay i {
  font-size: 3rem;
  color: #344966;
  text-shadow: 0 2px 4px rgba(255, 255, 255, 0.5);
}

.image-hover-overlay span {
  color: #344966;
  font-size: 1.1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-shadow: 0 2px 4px rgba(255, 255, 255, 0.5);
}

/* Cropper Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.cropper-modal {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.cropper-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #dee2e6;
}

.cropper-header h3 {
  margin: 0;
  color: #344966;
  font-size: 1.25rem;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6c757d;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: #f8f9fa;
  color: #344966;
}

.cropper-container {
  flex: 1;
  padding: 1.5rem;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
}

.image-cropper-wrapper {
  width: 100%;
  height: 400px;
  position: relative;
  overflow: hidden;
  background: white;
  border-radius: 12px;
  border: 2px dashed #dee2e6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cropper-image {
  max-width: 100%;
  max-height: 100%;
  user-select: none;
  -webkit-user-drag: none;
  touch-action: none;
}

.cropper-controls {
  padding: 1.5rem;
  border-top: 1px solid #dee2e6;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.zoom-control {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btn-zoom {
  background: #344966;
  color: white;
  border: none;
  border-radius: 8px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.btn-zoom:hover {
  background: #546A88;
  transform: translateY(-2px);
}

.zoom-slider {
  flex: 1;
  height: 6px;
  border-radius: 5px;
  background: #dee2e6;
  outline: none;
  -webkit-appearance: none;
}

.zoom-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.zoom-slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.btn-cancel,
.btn-apply {
  flex: 1;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-cancel {
  background: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.btn-apply {
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  color: white;
}

.btn-apply:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.3);
}

.btn-apply:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Inline Form Inputs for Edit Mode */
.form-input-inline {
  padding: 0.5rem 0.75rem;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  background: white;
  width: 100%;
}

.form-input-inline:focus {
  outline: none;
  border-color: #344966;
  box-shadow: 0 0 0 3px rgba(52, 73, 102, 0.1);
}

.edit-form .info-row {
  display: grid;
  grid-template-columns: 35% 65%;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #f0f0f0;
}

.edit-form .info-row:last-child {
  border-bottom: none;
}

@media (max-width: 1024px) {

