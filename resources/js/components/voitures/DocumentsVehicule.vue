<template>
  <div class="documents-vehicule" :class="{ 'modal-active': showAddModal || showViewModal || showReplaceModal }">
    <div class="documents-header">
      <h2>{{ t('documents.title') }}</h2>
      <button @click="showAddModal = true" class="btn-add">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        {{ t('documents.addDocument') }}
      </button>
    </div>

    <!-- Liste des documents -->
    <div v-if="documents.length > 0" class="documents-grid">
      <div v-for="doc in documents" :key="doc.idDocument" class="document-card" :class="getDocumentStatusClass(doc)">
        <div class="document-header">
          <div class="document-icon" :class="getDocumentIconClass(doc.type)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
              <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
          </div>
          
          <div class="document-info">
            <h3>{{ doc.type }}</h3>
            <p class="document-filename">{{ doc.nom_fichier }}</p>
          </div>
        </div>

        <div class="document-body">
          <p v-if="doc.date_expiration" class="document-expiration">
            <span v-if="isExpired(doc)" class="badge-expired">{{ t('documents.expiredOn', { date: formatDate(doc.date_expiration) }) }}</span>
            <span v-else-if="isExpiringSoon(doc)" class="badge-expiring">{{ t('documents.expiresOn', { date: formatDate(doc.date_expiration) }) }}</span>
            <span v-else class="badge-valid">{{ t('documents.validUntil', { date: formatDate(doc.date_expiration) }) }}</span>
          </p>
          <p v-if="doc.notes" class="document-notes">{{ doc.notes }}</p>
        </div>

        <div class="document-actions">
          <button @click="viewDocument(doc)" class="btn-action btn-view" :title="t('common.view')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <span>{{ t('common.view') }}</span>
          </button>
          <button @click="downloadDocument(doc.idDocument, doc.nom_fichier)" class="btn-action btn-download" :title="t('common.download')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>{{ t('common.download') }}</span>
          </button>
          <button @click="openReplaceModal(doc)" class="btn-action btn-replace" :title="t('documents.replace')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="23 4 23 10 17 10"></polyline>
              <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
            </svg>
            <span>{{ t('documents.replace') }}</span>
          </button>
          <button @click="deleteDocument(doc.idDocument)" class="btn-action btn-delete" :title="t('common.delete')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            <span>{{ t('common.delete') }}</span>
          </button>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <p>{{ t('documents.noDocuments') }}</p>
      <p class="empty-subtitle">{{ t('documents.addDocuments') }}</p>
    </div>

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

    <!-- Modal Ajout Document -->
    <div v-if="showAddModal" class="modal-overlay" @click.self="showAddModal = false">
      <div class="modal-content modal-form">
        <div class="modal-header">
          <div class="modal-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="12" y1="18" x2="12" y2="12"></line>
              <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
          </div>
          <h3>{{ t('documents.addDocument') }}</h3>
          <button @click="showAddModal = false" class="btn-close">&times;</button>
        </div>
        
        <form @submit.prevent="submitDocument" class="document-form">
          <!-- Type de document avec cards -->
          <div class="form-section">
            <label class="form-section-label">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              {{ t('documents.documentType') }} *
            </label>
            <div class="document-type-grid">
              <label v-for="type in documentTypes" :key="type.value" 
                     class="type-card" 
                     :class="{ 'active': newDocument.type === type.value }">
                <input type="radio" v-model="newDocument.type" :value="type.value" required />
                <div class="type-icon" :style="{ background: type.color }">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path :d="type.icon"></path>
                  </svg>
                </div>
                <span class="type-name">{{ type.label }}</span>
                <div class="type-check">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                </div>
              </label>
            </div>
          </div>

          <!-- Upload de fichier -->
          <div class="form-section">
            <label class="form-section-label">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              {{ t('documents.documentFile') }} *
            </label>
            <div class="file-upload-area">
              <input type="file" 
                     id="fileInput" 
                     @change="handleFileChange" 
                     accept=".pdf,.jpg,.jpeg,.png" 
                     required 
                     class="file-input" />
              <label for="fileInput" class="file-upload-label">
                <div class="upload-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                </div>
                <div class="upload-text">
                  <span class="upload-title">{{ newDocument.fichier ? newDocument.fichier.name : t('documents.clickToSelect') }}</span>
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
                   v-model="newDocument.date_expiration" 
                   class="form-input" 
                   :placeholder="t('documents.datePlaceholder')" />
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
            <textarea v-model="newDocument.notes" 
                      rows="3" 
                      class="form-input" 
                      :placeholder="t('documents.notesPlaceholder')"></textarea>
          </div>

          <div class="modal-actions">
            <button type="button" @click="showAddModal = false" class="btn-secondary">
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
              {{ loading ? t('documents.adding') : t('documents.addDocument') }}
            </button>
          </div>
        </form>
      </div>
    </div>
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
          icon: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6'
        },
        { 
          value: 'Assurance', 
          label: this.t('documents.types.insurance'), 
          color: 'linear-gradient(135deg, #C85A54 0%, #e07872 100%)',
          icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'
        },
        { 
          value: 'Contrôle technique', 
          label: this.t('documents.types.technicalControl'), 
          color: 'linear-gradient(135deg, #2c8ca8 0%, #4ab5d1 100%)',
          icon: 'M22 11.08V12a10 10 0 1 1-5.93-9.14 M22 4L12 14.01l-3-3'
        },
        { 
          value: 'Garantie', 
          label: this.t('documents.types.warranty'), 
          color: 'linear-gradient(135deg, #D4A574 0%, #e6c29a 100%)',
          icon: 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'
        },
        { 
          value: 'Facture achat', 
          label: this.t('documents.types.purchaseInvoice'), 
          color: 'linear-gradient(135deg, #546A88 0%, #7a8ea8 100%)',
          icon: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8 M10 9H8'
        },
        { 
          value: 'Autre', 
          label: this.t('documents.types.other'), 
          color: 'linear-gradient(135deg, #9ca8b3 0%, #c4cdd6 100%)',
          icon: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6'
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
    }
  }
};
</script>

<style scoped>
.documents-vehicule {
  padding: 24px;
  background: #f8f9fa;
  border-radius: 12px;
  min-height: 400px;
}

.documents-vehicule.modal-active {
  pointer-events: none;
  overflow: hidden;
}

.documents-vehicule.modal-active * {
  transition: none !important;
  transform: none !important;
}

.documents-vehicule.modal-active .modal-overlay,
.documents-vehicule.modal-active .modal-overlay * {
  pointer-events: all;
  transition: all 0.3s !important;
}

.documents-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.documents-header h2 {
  color: #344966;
  font-size: 24px;
  margin: 0;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #344966;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-add:hover {
  background: #546A88;
  transform: translateY(-2px);
}

.documents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

@media (max-width: 768px) {
  .documents-grid {
    grid-template-columns: 1fr;
  }
}

.document-card {
  background: white;
  border-radius: 12px;
  padding: 0;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.document-card:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  transform: translateY(-4px);
  z-index: 2;
}

.documents-vehicule.modal-active .document-card {
  pointer-events: none !important;
  transform: none !important;
  transition: none !important;
}

.documents-vehicule.modal-active .document-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
  transform: none !important;
  z-index: 1 !important;
}

.documents-vehicule.modal-active .document-card * {
  pointer-events: none !important;
}

.document-expired {
  border-left: 4px solid #C85A54;
}

.document-expiring {
  border-left: 4px solid #D4A574;
}

.document-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
}

.document-body {
  padding: 16px;
  flex: 1;
  min-height: 60px;
}

.document-icon {
  width: 48px;
  height: 48px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: #f5f5f5;
}

.document-icon svg {
  width: 28px;
  height: 28px;
}

.icon-carte-grise {
  background: #e3f2fd;
  color: #1976d2;
}

.icon-assurance {
  background: #fff3e0;
  color: #f57c00;
}

.icon-controle {
  background: #e8f5e9;
  color: #388e3c;
}

.icon-garantie {
  background: #f3e5f5;
  color: #7b1fa2;
}

.icon-facture {
  background: #fce4ec;
  color: #c2185b;
}

.icon-default {
  background: #f5f5f5;
  color: #666;
}

.document-info {
  flex: 1;
}

.document-info {
  flex: 1;
  min-width: 0;
}

.document-info h3 {
  margin: 0 0 4px 0;
  color: #344966;
  font-size: 16px;
  font-weight: 600;
}

.document-filename {
  color: #666;
  font-size: 13px;
  margin: 0;
  word-break: break-word;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.document-expiration {
  margin: 8px 0 4px 0;
}

.badge-expired,
.badge-expiring,
.badge-valid {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.badge-expired {
  background: #fde8e8;
  color: #C85A54;
}

.badge-expiring {
  background: #fff3e0;
  color: #D4A574;
}

.badge-valid {
  background: #e8f5e9;
  color: #4caf50;
}

.document-notes {
  color: #666;
  font-size: 12px;
  margin: 4px 0 0 0;
  font-style: italic;
}

.document-actions {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
}

.btn-action {
  background: transparent;
  border: none;
  border-right: 1px solid #f0f0f0;
  padding: 12px 8px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 500;
}

.btn-action:last-child {
  border-right: none;
}

.btn-action:hover {
  background: white;
  transform: translateY(-2px);
}

.documents-vehicule.modal-active .btn-action:hover {
  background: transparent !important;
  transform: none !important;
}

.btn-action svg {
  flex-shrink: 0;
}

.btn-action span {
  white-space: nowrap;
}

.btn-view {
  color: #1976d2;
}

.btn-view:hover {
  background: #e3f2fd;
  color: #1565c0;
}

.btn-download {
  color: #388e3c;
}

.btn-download:hover {
  background: #e8f5e9;
  color: #2e7d32;
}

.btn-replace {
  color: #f57c00;
}

.btn-replace:hover {
  background: #fff3e0;
  color: #ef6c00;
}

.btn-delete {
  color: #d32f2f;
}

.btn-delete:hover {
  background: #ffebee;
  color: #c62828;
}

@media (max-width: 480px) {
  .btn-action span {
    font-size: 10px;
  }
  
  .btn-action svg {
    width: 14px;
    height: 14px;
  }
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: #666;
  background: white;
  border-radius: 12px;
  border: 2px dashed #dee2e6;
}

.empty-state p {
  font-size: 18px;
  margin: 0 0 8px 0;
  color: #344966;
  font-weight: 600;
}

.empty-subtitle {
  font-size: 14px;
  color: #999;
  margin: 0;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  pointer-events: all;
  overflow: hidden;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  pointer-events: all;
  position: relative;
  z-index: 10000;
}

.modal-large {
  max-width: 1200px;
  width: 95%;
  height: 95vh;
  max-height: 95vh;
}

.modal-body {
  padding: 0;
  height: calc(100% - 70px);
}

.document-preview {
  height: 100%;
  background: #2c3e50;
  display: flex;
  align-items: center;
  justify-content: center;
}

.document-preview iframe {
  width: 100%;
  height: 100%;
  border: none;
}

.loading-preview {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #fff;
  font-size: 16px;
  flex-direction: column;
  gap: 12px;
}

.loading-preview::before {
  content: '';
  width: 40px;
  height: 40px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.form-info {
  background: #f8f9fa;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 16px;
}

.form-info p {
  margin: 4px 0;
  color: #344966;
  font-size: 14px;
}

.form-info strong {
  font-weight: 600;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
  color: #344966;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.3s;
}

.btn-close:hover {
  background: #f0f0f0;
  color: #344966;
}

.document-form {
  padding: 24px;
  max-height: calc(90vh - 140px);
  overflow-y: auto;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #344966;
  font-weight: 600;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #344966;
  box-shadow: 0 0 0 3px rgba(52, 73, 102, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e9ecef;
}

.btn-primary,
.btn-secondary {
  padding: 12px 28px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #344966;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #546A88;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #f8f9fa;
  color: #344966;
  border: 2px solid #e9ecef;
}

.btn-secondary:hover {
  background: #e9ecef;
  border-color: #dee2e6;
  transform: translateY(-2px);
}

/* Enhanced Form Styles */
.modal-form {
  max-width: 650px;
}

.modal-header-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

/* Current Document Info */
.current-document-info {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  background: linear-gradient(135deg, rgba(200, 90, 84, 0.08) 0%, rgba(224, 120, 114, 0.08) 100%);
  border-radius: 12px;
  border-left: 4px solid #C85A54;
  margin-bottom: 28px;
}

.info-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, #C85A54 0%, #e07872 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.info-content {
  flex: 1;
}

.info-title {
  font-size: 12px;
  color: #6c757d;
  margin: 0 0 6px 0;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.info-detail {
  font-size: 16px;
  color: #344966;
  margin: 0 0 4px 0;
  font-weight: 600;
}

.info-filename {
  font-size: 13px;
  color: #6c757d;
  margin: 0;
  word-break: break-word;
}

.modal-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 24px;
  border-bottom: 2px solid #f0f0f0;
  background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
}

.form-section {
  margin-bottom: 28px;
}

.form-section-label {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
  color: #344966;
  font-weight: 600;
  font-size: 14px;
}

.form-section-label svg {
  color: #344966;
}

/* Document Type Cards */
.document-type-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

@media (max-width: 600px) {
  .document-type-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.type-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px 12px;
  border: 2px solid #e9ecef;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s;
  background: white;
}

.type-card:hover {
  border-color: #344966;
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(52, 73, 102, 0.15);
}

.type-card.active {
  border-color: #344966;
  background: linear-gradient(135deg, rgba(52, 73, 102, 0.05) 0%, rgba(84, 106, 136, 0.05) 100%);
  box-shadow: 0 4px 16px rgba(52, 73, 102, 0.2);
}

.type-card input[type="radio"] {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.type-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin-bottom: 8px;
  transition: all 0.3s;
}

.type-card:hover .type-icon {
  transform: scale(1.1) rotate(5deg);
}

.type-name {
  font-size: 13px;
  font-weight: 600;
  color: #344966;
  text-align: center;
  line-height: 1.3;
}

.type-check {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 24px;
  height: 24px;
  background: #344966;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transform: scale(0);
  transition: all 0.3s;
}

.type-card.active .type-check {
  opacity: 1;
  transform: scale(1);
}

/* File Upload Area */
.file-upload-area {
  position: relative;
}

.file-input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.file-upload-label {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border: 2px dashed #d0d5dd;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s;
  background: #f8f9fa;
}

.file-upload-label:hover {
  border-color: #344966;
  background: linear-gradient(135deg, rgba(52, 73, 102, 0.05) 0%, rgba(84, 106, 136, 0.05) 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.1);
}

.upload-icon {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.upload-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.upload-title {
  font-size: 14px;
  font-weight: 600;
  color: #344966;
  word-break: break-word;
}

.upload-subtitle {
  font-size: 12px;
  color: #6c757d;
}

/* Form Input Styles */
.form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 14px;
  transition: all 0.3s;
  font-family: inherit;
  background: white;
}

.form-input:focus {
  outline: none;
  border-color: #344966;
  box-shadow: 0 0 0 4px rgba(52, 73, 102, 0.1);
}

.form-input::placeholder {
  color: #adb5bd;
}

textarea.form-input {
  resize: vertical;
  min-height: 90px;
  line-height: 1.5;
}

/* Enhanced Modal Actions */
.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 2px solid #f0f0f0;
}

.btn-primary svg,
.btn-secondary svg {
  width: 16px;
  height: 16px;
}

/* Loading State for File Upload */
.file-upload-label.has-file {
  border-color: #344966;
  background: linear-gradient(135deg, rgba(52, 73, 102, 0.08) 0%, rgba(84, 106, 136, 0.08) 100%);
}

.file-upload-label.has-file .upload-icon {
  background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
}
</style>
