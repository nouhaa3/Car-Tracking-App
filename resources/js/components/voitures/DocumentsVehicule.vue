<template>
  <div class="documents-vehicule" :class="{ 'modal-active': showAddModal || showViewModal || showReplaceModal }">
    <div class="documents-page-header">
      <div class="header-left">
        <h2><i class="bi bi-files"></i> {{ t('documents.title') }}</h2>
        <p>{{ t('documents.manageVehicleDocuments') }}</p>
      </div>
      <div class="header-actions">
        <button @click="showAddModal = true" class="add-document-btn">
          <i class="bi bi-plus-circle"></i>
          {{ t('documents.addDocument') }}
        </button>
      </div>
    </div>

    <!-- Statistics Summary -->
    <div v-if="documents.length > 0" class="documents-stats">
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
          <i class="bi bi-files"></i>
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ documents.length }}</span>
          <span class="stat-label">{{ t('documents.totalDocuments') }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ expiringSoonCount }}</span>
          <span class="stat-label">{{ t('documents.expiringSoon') }}</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">
          <i class="bi bi-x-circle-fill"></i>
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ expiredCount }}</span>
          <span class="stat-label">{{ t('documents.expired') }}</span>
        </div>
      </div>
    </div>

    <!-- Liste des documents -->
    <div v-if="documents.length > 0" class="documents-grid-organized">
      <div v-for="doc in documents" :key="doc.idDocument" class="document-card-modern" :class="getDocumentStatusClass(doc)">
        <!-- Status Badge -->
        <div class="doc-status-badge" :class="getStatusBadgeClass(doc)">
          <i :class="getStatusIcon(doc)"></i>
          <span>{{ getStatusText(doc) }}</span>
        </div>

        <!-- Document Type Icon -->
        <div class="doc-type-icon" :style="{ background: getDocumentTypeColor(doc.type) }">
          <i :class="getDocumentTypeIcon(doc.type)"></i>
        </div>

        <!-- Document Info -->
        <div class="doc-info-section">
          <h3 class="doc-type-title">{{ doc.type }}</h3>
          <p class="doc-filename">
            <i class="bi bi-file-earmark"></i>
            {{ doc.nom_fichier }}
          </p>
          
          <!-- Expiration Info -->
          <div v-if="doc.date_expiration" class="doc-expiration-info">
            <i class="bi bi-calendar-event"></i>
            <span>{{ formatDate(doc.date_expiration) }}</span>
          </div>

          <!-- Notes Preview -->
          <p v-if="doc.notes" class="doc-notes-preview">
            <i class="bi bi-sticky"></i>
            {{ truncateNotes(doc.notes) }}
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="doc-actions-row">
          <button @click="viewDocument(doc)" class="doc-btn doc-btn-view">
            <i class="bi bi-eye-fill"></i>
            <span>{{ t('common.view') }}</span>
          </button>
          <button @click="downloadDocument(doc.idDocument, doc.nom_fichier)" class="doc-btn doc-btn-download">
            <i class="bi bi-download"></i>
          </button>
          <button @click="openReplaceModal(doc)" class="doc-btn doc-btn-replace">
            <i class="bi bi-arrow-repeat"></i>
          </button>
          <button @click="deleteDocument(doc.idDocument)" class="doc-btn doc-btn-delete">
            <i class="bi bi-trash3"></i>
          </button>
        </div>
      </div>
    </div>

    <div v-else class="empty-state-modern">
      <div class="empty-illustration">
        <div class="illustration-circle">
          <i class="bi bi-folder2-open"></i>
        </div>
      </div>
      <h3>{{ t('documents.noDocuments') }}</h3>
      <p>{{ t('documents.addDocuments') }}</p>
      <button @click="showAddModal = true" class="empty-action-btn">
        <i class="bi bi-plus-circle"></i>
        {{ t('documents.addFirstDocument') }}
      </button>
    </div>

    <!-- Modals - Teleported to body for full viewport coverage -->
    <Teleport to="body">
      <!-- Modal Visualisation Document -->
      <div v-if="showViewModal" class="modal-overlay" @click.self="closeViewModal">
        <div class="modal-content modal-large">
          <div class="modal-header">
            <h3>{{ viewingDocument?.type }}</h3>
            <button @click="closeViewModal" class="btn-close">&times;</button>
          </div>
          <div class="modal-body">
            <div class="document-preview">
              <iframe v-if="documentUrl" :src="documentUrl" frameborder="0"></iframe>
              <div v-else class="loading-preview">{{ t('common.loadingDocument') }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Remplacement Document -->
      <div v-if="showReplaceModal" class="modal-overlay" @click.self="showReplaceModal = false">
      <div class="modal-content modal-form">
        <div class="modal-header">
          <div class="modal-header-icon" style="background: linear-gradient(135deg, #D4A574 0%, #e6c29a 100%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="23 4 23 10 17 10"></polyline>
              <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
            </svg>
          </div>
          <h3>{{ t('documents.replaceDocument') }}</h3>
          <button @click="showReplaceModal = false" class="btn-close">&times;</button>
        </div>
        
        <form @submit.prevent="submitReplaceDocument" class="document-form">
          <!-- Document actuel info -->
          <div class="current-document-info">
            <div class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
            </div>
            <div class="info-content">
              <p class="info-title">{{ t('documents.currentDocument') }}</p>
              <p class="info-detail"><strong>{{ replacingDocument?.type }}</strong></p>
              <p class="info-filename">{{ replacingDocument?.nom_fichier }}</p>
            </div>
          </div>

          <!-- Upload nouveau fichier -->
          <div class="form-section">
            <label class="form-section-label">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              {{ t('documents.newFile') }} *
            </label>
            <div class="file-upload-area">
              <input type="file" 
                     id="replaceFileInput" 
                     @change="handleReplaceFileChange" 
                     accept=".pdf,.jpg,.jpeg,.png" 
                     required 
                     class="file-input" />
              <label for="replaceFileInput" class="file-upload-label">
                <div class="upload-icon" style="background: linear-gradient(135deg, #D4A574 0%, #e6c29a 100%);">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                  </svg>
                </div>
                <div class="upload-text">
                  <span class="upload-title">{{ replaceDocument.fichier ? replaceDocument.fichier.name : t('documents.selectNewFile') }}</span>
                  <span class="upload-subtitle">{{ t('documents.fileRequirements') }}</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Date d'expiration -->
          <div class="form-section">
            <label class="form-section-label">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
              {{ t('documents.expirationDate') }}
            </label>
            <input type="date" 
                   v-model="replaceDocument.date_expiration" 
                   class="form-input" />
          </div>

          <!-- Notes -->
          <div class="form-section">
            <label class="form-section-label">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="17" y1="10" x2="3" y2="10"></line>
                <line x1="21" y1="6" x2="3" y2="6"></line>
                <line x1="21" y1="14" x2="3" y2="14"></line>
                <line x1="17" y1="18" x2="3" y2="18"></line>
              </svg>
              {{ t('documents.notes') }}
            </label>
            <textarea v-model="replaceDocument.notes" 
                      rows="3" 
                      class="form-input" 
                      :placeholder="t('documents.notesPlaceholder')"></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" @click="showReplaceModal = false" class="btn-secondary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
              {{ t('common.cancel') }}
            </button>
            <button type="submit" class="btn-primary" :disabled="loading">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              {{ loading ? t('documents.replacing') : t('documents.replaceDocument') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Ajout Document - Full Page Overlay -->
    <div v-if="showAddModal" class="modal-overlay-fullpage" @click.self="showAddModal = false">
      <div class="modal-content-fullpage">
        <div class="modal-header-fullpage">
          <div class="modal-header-content">
            <div class="modal-header-icon">
              <i class="bi bi-file-earmark-plus"></i>
            </div>
            <div>
              <h3>{{ t('documents.addDocument') }}</h3>
              <p>{{ t('documents.uploadNewDocument') }}</p>
            </div>
          </div>
          <button @click="showAddModal = false" class="btn-close-fullpage">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        
        <form @submit.prevent="submitDocument" class="document-form-fullpage">
          <!-- Type de document avec cards -->
          <div class="form-section">
            <label class="form-label">
              <i class="bi bi-folder"></i>
              {{ t('documents.documentType') }} *
            </label>
            <div class="document-type-grid">
              <label v-for="type in documentTypes" :key="type.value" 
                     class="type-card" 
                     :class="{ 'active': newDocument.type === type.value }">
                <input type="radio" v-model="newDocument.type" :value="type.value" required />
                <div class="type-icon" :style="{ background: type.color }">
                  <i :class="type.iconClass"></i>
                </div>
                <span class="type-name">{{ type.label }}</span>
                <div class="type-check">
                  <i class="bi bi-check-circle-fill"></i>
                </div>
              </label>
            </div>
          </div>

          <!-- Upload de fichier -->
          <div class="form-section">
            <label class="form-label">
              <i class="bi bi-cloud-upload"></i>
              {{ t('documents.documentFile') }} *
            </label>
            <div class="file-upload-area" :class="{ 'has-file': newDocument.fichier }">
              <input type="file" 
                     id="fileInput" 
                     @change="handleFileChange" 
                     accept=".pdf,.jpg,.jpeg,.png" 
                     required 
                     class="file-input" />
              <label for="fileInput" class="file-upload-label">
                <div class="upload-icon">
                  <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <div class="upload-text">
                  <span class="upload-title" v-if="!newDocument.fichier">{{ t('documents.clickToSelect') }}</span>
                  <span class="upload-title selected" v-else>
                    <i class="bi bi-file-earmark-check"></i>
                    {{ newDocument.fichier.name }}
                  </span>
                  <span class="upload-subtitle">{{ t('documents.fileRequirements') }}</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Date d'expiration -->
          <div class="form-section">
            <label class="form-label">
              <i class="bi bi-calendar-event"></i>
              {{ t('documents.expirationDate') }}
            </label>
            <input type="date" 
                   v-model="newDocument.date_expiration" 
                   class="form-input date-input" 
                   :min="new Date().toISOString().split('T')[0]" />
            <small class="form-hint">
              <i class="bi bi-info-circle"></i>
              {{ t('documents.expirationHint') }}
            </small>
          </div>

          <!-- Notes -->
          <div class="form-section">
            <label class="form-label">
              <i class="bi bi-pencil-square"></i>
              {{ t('documents.notes') }}
            </label>
            <textarea v-model="newDocument.notes" 
                      rows="3" 
                      class="form-input textarea-input" 
                      :placeholder="t('documents.notesPlaceholder')"></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" @click="showAddModal = false" class="btn-secondary">
              <i class="bi bi-x-circle"></i>
              {{ t('common.cancel') }}
            </button>
            <button type="submit" class="btn-primary" :disabled="loading">
              <i :class="loading ? 'bi bi-hourglass-split' : 'bi bi-check-circle'"></i>
              {{ loading ? t('documents.adding') : t('documents.addDocument') }}
            </button>
          </div>
        </form>
      </div>
    </div>
    </Teleport>
  </div>
</template>

<script>
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import alerts from '@/utils/alerts';

export default {
  name: 'DocumentsVehicule',
  props: {
    voitureId: {
      type: [Number, String],
      required: true
    }
  },
  setup() {
    const { t } = useI18n();
    return { t };
  },
  data() {
    return {
      documents: [],
      showAddModal: false,
      showViewModal: false,
      showReplaceModal: false,
      loading: false,
      viewingDocument: null,
      documentUrl: null,
      replacingDocument: null,
      newDocument: {
        type: '',
        fichier: null,
        date_expiration: '',
        notes: ''
      },
      replaceDocument: {
        fichier: null,
        date_expiration: '',
        notes: ''
      },
      documentTypes: [
        { 
          value: 'Carte grise', 
          label: this.t('documents.types.registration'), 
          color: 'linear-gradient(135deg, #344966 0%, #546A88 100%)',
          iconClass: 'bi bi-card-heading'
        },
        { 
          value: 'Assurance', 
          label: this.t('documents.types.insurance'), 
          color: 'linear-gradient(135deg, #C85A54 0%, #e07872 100%)',
          iconClass: 'bi bi-shield-fill-check'
        },
        { 
          value: 'Contrôle technique', 
          label: this.t('documents.types.technicalControl'), 
          color: 'linear-gradient(135deg, #2c8ca8 0%, #4ab5d1 100%)',
          iconClass: 'bi bi-check-circle-fill'
        },
        { 
          value: 'Garantie', 
          label: this.t('documents.types.warranty'), 
          color: 'linear-gradient(135deg, #D4A574 0%, #e6c29a 100%)',
          iconClass: 'bi bi-star-fill'
        },
        { 
          value: 'Facture achat', 
          label: this.t('documents.types.purchaseInvoice'), 
          color: 'linear-gradient(135deg, #546A88 0%, #7a8ea8 100%)',
          iconClass: 'bi bi-receipt'
        },
        { 
          value: 'Autre', 
          label: this.t('documents.types.other'), 
          color: 'linear-gradient(135deg, #9ca8b3 0%, #c4cdd6 100%)',
          iconClass: 'bi bi-file-earmark'
        }
      ]
    };
  },
  mounted() {
    this.fetchDocuments();
  },
  methods: {
    async fetchDocuments() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://localhost:8000/api/voitures/${this.voitureId}/documents`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.documents = response.data;
      } catch (error) {
        console.error('Erreur chargement documents:', error);
        alerts.alertError(this.t('documents.loadError'));
      }
    },

    handleFileChange(event) {
      this.newDocument.fichier = event.target.files[0];
    },

    async submitDocument() {
      if (!this.newDocument.fichier) {
        alerts.alertError(this.t('documents.fileRequired'));
        return;
      }

      this.loading = true;
      const formData = new FormData();
      formData.append('type', this.newDocument.type);
      formData.append('fichier', this.newDocument.fichier);
      if (this.newDocument.date_expiration) {
        formData.append('date_expiration', this.newDocument.date_expiration);
      }
      if (this.newDocument.notes) {
        formData.append('notes', this.newDocument.notes);
      }

      try {
        const token = localStorage.getItem('token');
        await axios.post(`http://localhost:8000/api/voitures/${this.voitureId}/documents`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        alerts.alertSuccess(this.t('documents.addSuccess'));
        this.showAddModal = false;
        this.resetForm();
        this.fetchDocuments();
      } catch (error) {
        console.error('Erreur ajout document:', error);
        alerts.alertError(error.response?.data?.message || this.t('documents.addError'));
      } finally {
        this.loading = false;
      }
    },

    async viewDocument(doc) {
      this.viewingDocument = doc;
      this.showViewModal = true;
      
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://localhost:8000/api/documents-vehicule/${doc.idDocument}/download`, {
          headers: { Authorization: `Bearer ${token}` },
          responseType: 'blob'
        });

        const blob = new Blob([response.data]);
        this.documentUrl = window.URL.createObjectURL(blob);
      } catch (error) {
        console.error('Erreur chargement document:', error);
        alerts.alertError(this.t('documents.loadDocumentError'));
        this.closeViewModal();
      }
    },

    closeViewModal() {
      if (this.documentUrl) {
        window.URL.revokeObjectURL(this.documentUrl);
        this.documentUrl = null;
      }
      this.showViewModal = false;
      this.viewingDocument = null;
    },

    async downloadDocument(id, filename) {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://localhost:8000/api/documents-vehicule/${id}/download`, {
          headers: { Authorization: `Bearer ${token}` },
          responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename || `document_${id}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        alerts.alertSuccess(this.t('documents.downloadSuccess'));
      } catch (error) {
        console.error('Erreur téléchargement:', error);
        alerts.alertError(this.t('documents.downloadError'));
      }
    },

    openReplaceModal(doc) {
      this.replacingDocument = doc;
      this.replaceDocument = {
        fichier: null,
        date_expiration: doc.date_expiration || '',
        notes: doc.notes || ''
      };
      this.showReplaceModal = true;
    },

    handleReplaceFileChange(event) {
      this.replaceDocument.fichier = event.target.files[0];
    },

    async submitReplaceDocument() {
      if (!this.replaceDocument.fichier) {
        alerts.alertError(this.t('documents.fileRequired'));
        return;
      }

      this.loading = true;
      const formData = new FormData();
      formData.append('fichier', this.replaceDocument.fichier);
      formData.append('type', this.replacingDocument.type);
      if (this.replaceDocument.date_expiration) {
        formData.append('date_expiration', this.replaceDocument.date_expiration);
      }
      if (this.replaceDocument.notes) {
        formData.append('notes', this.replaceDocument.notes);
      }
      formData.append('_method', 'PUT');

      try {
        const token = localStorage.getItem('token');
        await axios.post(`http://localhost:8000/api/documents-vehicule/${this.replacingDocument.idDocument}`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        alerts.alertSuccess(this.t('documents.replaceSuccess'));
        this.showReplaceModal = false;
        this.replacingDocument = null;
        this.replaceDocument = { fichier: null, date_expiration: '', notes: '' };
        this.fetchDocuments();
      } catch (error) {
        console.error('Erreur remplacement document:', error);
        alerts.alertError(error.response?.data?.message || this.t('documents.replaceError'));
      } finally {
        this.loading = false;
      }
    },

    async deleteDocument(id) {
      const confirmed = await alerts.confirmDelete(this.t('documents.confirmDelete'));
      if (!confirmed) return;

      this.loading = true;
      try {
        const token = localStorage.getItem('token');
        await axios.delete(`http://localhost:8000/api/documents-vehicule/${id}`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alerts.success(this.t('common.deleted'), this.t('documents.deleteSuccess'));
        this.fetchDocuments();
      } catch (error) {
        console.error('Erreur suppression:', error);
        alerts.error(this.t('common.error'), this.t('documents.deleteError'));
      } finally {
        this.loading = false;
      }
    },

    resetForm() {
      this.newDocument = {
        type: '',
        fichier: null,
        date_expiration: '',
        notes: ''
      };
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR');
    },

    isExpired(doc) {
      if (!doc.date_expiration) return false;
      return new Date(doc.date_expiration) < new Date();
    },

    isExpiringSoon(doc) {
      if (!doc.date_expiration) return false;
      const expDate = new Date(doc.date_expiration);
      const today = new Date();
      const diffDays = Math.ceil((expDate - today) / (1000 * 60 * 60 * 24));
      return diffDays > 0 && diffDays <= 30;
    },

    getDocumentStatusClass(doc) {
      if (this.isExpired(doc)) return 'document-expired';
      if (this.isExpiringSoon(doc)) return 'document-expiring';
      return '';
    },

    getDocumentIconClass(type) {
      const iconClasses = {
        'Carte grise': 'icon-carte-grise',
        'Assurance': 'icon-assurance',
        'Contrôle technique': 'icon-controle',
        'Garantie': 'icon-garantie',
        'Facture achat': 'icon-facture'
      };
      return iconClasses[type] || 'icon-default';
    },

    getStatusBadgeClass(doc) {
      if (this.isExpired(doc)) return 'badge-expired-modern';
      if (this.isExpiringSoon(doc)) return 'badge-warning-modern';
      return 'badge-valid-modern';
    },

    getStatusIcon(doc) {
      if (this.isExpired(doc)) return 'bi bi-x-circle-fill';
      if (this.isExpiringSoon(doc)) return 'bi bi-exclamation-triangle-fill';
      return 'bi bi-check-circle-fill';
    },

    getStatusText(doc) {
      if (this.isExpired(doc)) return this.t('documents.expired');
      if (this.isExpiringSoon(doc)) return this.t('documents.expiringSoon');
      return this.t('documents.valid');
    },

    getDocumentTypeColor(type) {
      const colors = {
        'Carte grise': 'linear-gradient(135deg, #344966 0%, #546A88 100%)',
        'Assurance': 'linear-gradient(135deg, #C85A54 0%, #e07872 100%)',
        'Contrôle technique': 'linear-gradient(135deg, #2c8ca8 0%, #4ab5d1 100%)',
        'Garantie': 'linear-gradient(135deg, #D4A574 0%, #e6c29a 100%)',
        'Facture achat': 'linear-gradient(135deg, #748BAA 0%, #8fa3bd 100%)'
      };
      return colors[type] || 'linear-gradient(135deg, #748BAA 0%, #8fa3bd 100%)';
    },

    getDocumentTypeIcon(type) {
      const icons = {
        'Carte grise': 'bi bi-card-heading',
        'Assurance': 'bi bi-shield-fill-check',
        'Contrôle technique': 'bi bi-check-circle-fill',
        'Garantie': 'bi bi-star-fill',
        'Facture achat': 'bi bi-receipt'
      };
      return icons[type] || 'bi bi-file-earmark';
    },

    truncateNotes(notes) {
      if (!notes) return '';
      return notes.length > 60 ? notes.substring(0, 60) + '...' : notes;
    }
  },

  computed: {
    expiringSoonCount() {
      return this.documents.filter(doc => this.isExpiringSoon(doc)).length;
    },

    expiredCount() {
      return this.documents.filter(doc => this.isExpired(doc)).length;
    }
  }
};
</script>


