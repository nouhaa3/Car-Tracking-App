<template>
  <div class="home-page">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />
      
      <div class="main-content">
        <!-- Profile Float -->
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.userAvatar')" class="avatar" />
        </router-link>

        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <!-- Navbar -->
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
      <div class="corbeille-page">
        <div class="page-header">
          <div class="header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
          </div>
          <div class="header-content">
            <h1>{{ t('trash.title') }}</h1>
            <p class="subtitle">{{ t('trash.subtitle') }}</p>
          </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
          <button 
            @click="activeTab = 'voitures'" 
            :class="{ active: activeTab === 'voitures' }"
            class="tab-button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 17h14v2H5v-2zm10-8.5l1.5 1.5 5-5-1.5-1.5-5 5zM12 11L9.5 8.5 4 14l1.5 1.5L9.5 11.5 12 14v-3z"></path>
              <path d="M18 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM6 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"></path>
              <path d="M19 9l-1.5-1.5-5 5-2.5-2.5L4 14"></path>
            </svg>
            {{ t('trash.vehicles') }} ({{ voituresTrashed.length }})
          </button>
          <button 
            @click="activeTab = 'interventions'" 
            :class="{ active: activeTab === 'interventions' }"
            class="tab-button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
            </svg>
            {{ t('trash.interventions') }} ({{ interventionsTrashed.length }})
          </button>
        </div>

        <!-- Véhicules Tab -->
        <div v-if="activeTab === 'voitures'" class="tab-content">
          <div v-if="voituresTrashed.length > 0" class="items-grid">
            <div v-for="voiture in voituresTrashed" :key="voiture.idVoiture" class="item-card">
              <div class="item-icon voiture-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M5 17h14v2H5v-2zm10-8.5l1.5 1.5 5-5-1.5-1.5-5 5zM12 11L9.5 8.5 4 14l1.5 1.5L9.5 11.5 12 14v-3z"></path>
                  <path d="M18 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM6 18.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"></path>
                </svg>
              </div>
              <div class="item-info">
                <h3>{{ voiture.marque }} {{ voiture.modele }}</h3>
                <p class="item-meta">{{ t('trash.year') }}: {{ voiture.annee }} | {{ voiture.kilometrage }} km</p>
                <p class="item-meta">{{ t('trash.owner') }}: {{ voiture.user?.nom }} {{ voiture.user?.prenom }}</p>
                <p class="deleted-date">{{ t('trash.deletedOn') }} {{ formatDate(voiture.deleted_at) }}</p>
              </div>
              <div class="item-actions">
                <button @click="restoreVoiture(voiture.idVoiture)" class="btn-restore">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                  </svg>
                  {{ t('trash.actions.restore') }}
                </button>
                <button @click="forceDeleteVoiture(voiture.idVoiture)" class="btn-delete">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                  </svg>
                  {{ t('trash.actions.deletePermanently') }}
                </button>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18"></path>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            <p>{{ t('trash.empty.vehicles') }}</p>
          </div>
        </div>

        <!-- Interventions Tab -->
        <div v-if="activeTab === 'interventions'" class="tab-content">
          <div v-if="interventionsTrashed.length > 0" class="items-grid">
            <div v-for="intervention in interventionsTrashed" :key="intervention.idIntervention" class="item-card">
              <div class="item-icon intervention-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
              </div>
              <div class="item-info">
                <h3>{{ intervention.type }}</h3>
                <p class="item-meta">{{ t('trash.vehicle') }}: {{ intervention.voiture?.marque }} {{ intervention.voiture?.modele }}</p>
                <p class="item-meta">{{ t('trash.date') }}: {{ formatDate(intervention.date) }} | {{ t('trash.cost') }}: {{ intervention.cout }} MAD</p>
                <p class="item-meta">{{ t('trash.garage') }}: {{ intervention.garage }}</p>
                <p class="deleted-date">{{ t('trash.deletedOn') }} {{ formatDate(intervention.deleted_at) }}</p>
              </div>
              <div class="item-actions">
                <button @click="restoreIntervention(intervention.idIntervention)" class="btn-restore">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                  </svg>
                  {{ t('trash.actions.restore') }}
                </button>
                <button @click="forceDeleteIntervention(intervention.idIntervention)" class="btn-delete">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                  </svg>
                  {{ t('trash.actions.deletePermanently') }}
                </button>
              </div>
            </div>
          </div>
          <div v-else class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18"></path>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            <p>{{ t('trash.empty.interventions') }}</p>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';
import Sidebar from './sidebar.vue';
import axios from 'axios';
import alerts from '@/utils/alerts';

export default {
  name: 'Corbeille',
  components: {
    Sidebar
  },
  setup() {
    const { t } = useI18n();
    return { t };
  },
  data() {
    return {
      activeTab: 'voitures',
      voituresTrashed: [],
      interventionsTrashed: [],
      isExpanded: false,
      user: null,
      menuItems: [
        { label: this.t('nav.dashboard'), to: '/' },
        { label: this.t('nav.vehicles'), to: '/voitures' },
        { label: this.t('nav.interventions'), to: '/interventions' },
        { label: this.t('nav.alerts'), to: '/alertes' },
        { label: this.t('nav.documents'), to: '/documents' }
      ]
    };
  },
  mounted() {
    this.fetchTrashedItems();
    // Restore user from localStorage
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
      this.user = JSON.parse(savedUser);
    }
  },
  methods: {
    async fetchTrashedItems() {
      try {
        const token = localStorage.getItem('token');
        
        const [voituresRes, interventionsRes] = await Promise.all([
          axios.get('http://localhost:8000/api/corbeille/voitures', {
            headers: { Authorization: `Bearer ${token}` }
          }),
          axios.get('http://localhost:8000/api/corbeille/interventions', {
            headers: { Authorization: `Bearer ${token}` }
          })
        ]);

        this.voituresTrashed = voituresRes.data;
        this.interventionsTrashed = interventionsRes.data;
      } catch (error) {
        console.error(this.t('errors.loadTrashError'), error);
        alerts.alertError(this.t('common.error'), this.t('errors.loadTrashError'));
      }
    },

    async restoreVoiture(id) {
      const confirmed = await alerts.confirm(this.t('trash.confirmations.restoreVehicle.title'), this.t('trash.confirmations.restoreVehicle.message'));
      if (!confirmed) return;

      try {
        const token = localStorage.getItem('token');
        await axios.post(`http://localhost:8000/api/corbeille/voitures/${id}/restore`, {}, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alerts.alertSuccess(this.t('common.success'), this.t('trash.messages.vehicleRestored'));
        this.fetchTrashedItems();
      } catch (error) {
        console.error(this.t('errors.restoreError'), error);
        alerts.alertError(this.t('common.error'), this.t('errors.restoreVehicleError'));
      }
    },

    async restoreIntervention(id) {
      const confirmed = await alerts.confirm(this.t('trash.confirmations.restoreIntervention.title'), this.t('trash.confirmations.restoreIntervention.message'));
      if (!confirmed) return;

      try {
        const token = localStorage.getItem('token');
        await axios.post(`http://localhost:8000/api/corbeille/interventions/${id}/restore`, {}, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alerts.alertSuccess(this.t('common.success'), this.t('trash.messages.interventionRestored'));
        this.fetchTrashedItems();
      } catch (error) {
        console.error(this.t('errors.restoreError'), error);
        alerts.alertError(this.t('common.error'), this.t('errors.restoreInterventionError'));
      }
    },

    async forceDeleteVoiture(id) {
      const confirmed = await alerts.confirmDelete(this.t('trash.confirmations.deleteVehicle.title'), this.t('trash.confirmations.deleteVehicle.message'));
      if (!confirmed) return;

      try {
        const token = localStorage.getItem('token');
        await axios.delete(`http://localhost:8000/api/corbeille/voitures/${id}/force`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alerts.alertSuccess(this.t('common.success'), this.t('trash.messages.vehicleDeleted'));
        this.fetchTrashedItems();
      } catch (error) {
        console.error(this.t('errors.deleteError'), error);
        alerts.alertError(this.t('common.error'), this.t('errors.deleteVehicleError'));
      }
    },

    async forceDeleteIntervention(id) {
      const confirmed = await alerts.confirmDelete(this.t('trash.confirmations.deleteIntervention.title'), this.t('trash.confirmations.deleteIntervention.message'));
      if (!confirmed) return;

      try {
        const token = localStorage.getItem('token');
        await axios.delete(`http://localhost:8000/api/corbeille/interventions/${id}/force`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alerts.alertSuccess(this.t('common.success'), this.t('trash.messages.interventionDeleted'));
        this.fetchTrashedItems();
      } catch (error) {
        console.error(this.t('errors.deleteError'), error);
        alerts.alertError(this.t('common.error'), this.t('errors.deleteInterventionError'));
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  }
};
</script>