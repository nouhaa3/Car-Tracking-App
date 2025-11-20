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

        <div class="profile-wrapper">
          <!-- Page Header -->
          <div class="page-header">
          <div class="header-left">
            <h1>{{ t('trash.title') }}</h1>
            <p>{{ t('trash.subtitle') }}</p>
          </div>
        </div>

        <!-- Tabs -->
        <div class="trash-tabs">
          <button 
            @click="activeTab = 'voitures'" 
            :class="['trash-tab', { active: activeTab === 'voitures' }]"
          >
            <i class="bi bi-car-front"></i>
            <span>{{ t('trash.vehicles') }}</span>
            <span class="count-badge">{{ voituresTrashed.length }}</span>
          </button>
          <button 
            @click="activeTab = 'interventions'" 
            :class="['trash-tab', { active: activeTab === 'interventions' }]"
          >
            <i class="bi bi-tools"></i>
            <span>{{ t('trash.interventions') }}</span>
            <span class="count-badge">{{ interventionsTrashed.length }}</span>
          </button>
        </div>

        <!-- Véhicules Tab -->
        <div v-if="activeTab === 'voitures'" class="trash-content">
          <div v-if="voituresTrashed.length > 0" class="trash-grid">
            <div v-for="voiture in voituresTrashed" :key="voiture.idVoiture" class="trash-card">
              <div class="trash-card-header">
                <div class="trash-icon vehicle-icon">
                  <i class="bi bi-car-front-fill"></i>
                </div>
                <div class="trash-card-title">
                  <h3>{{ voiture.marque }} {{ voiture.modele }}</h3>
                  <span class="trash-card-badge">{{ voiture.statut }}</span>
                </div>
              </div>
              
              <div class="trash-card-body">
                <div class="trash-info-row">
                  <i class="bi bi-calendar3"></i>
                  <span>{{ t('trash.year') }}: {{ voiture.annee }}</span>
                </div>
                <div class="trash-info-row">
                  <i class="bi bi-speedometer2"></i>
                  <span>{{ voiture.kilometrage?.toLocaleString() }} km</span>
                </div>
                <div class="trash-info-row">
                  <i class="bi bi-person"></i>
                  <span>{{ voiture.user?.nom }} {{ voiture.user?.prenom }}</span>
                </div>
                <div class="trash-info-row deleted-info">
                  <i class="bi bi-trash3"></i>
                  <span>{{ formatDate(voiture.deleted_at) }}</span>
                </div>
              </div>

              <div class="trash-card-actions">
                <button @click="restoreVoiture(voiture.idVoiture)" class="btn-restore-new">
                  <i class="bi bi-arrow-counterclockwise"></i>
                  {{ t('trash.actions.restore') }}
                </button>
                <button @click="forceDeleteVoiture(voiture.idVoiture)" class="btn-delete-new">
                  <i class="bi bi-x-circle"></i>
                </button>
              </div>
            </div>
          </div>
          <div v-else class="trash-empty">
            <div class="empty-icon">
              <i class="bi bi-inbox"></i>
            </div>
            <h3>{{ t('trash.empty.vehicles') }}</h3>
            <p>{{ t('trash.empty.vehiclesDesc') }}</p>
          </div>
        </div>

        <!-- Interventions Tab -->
        <div v-if="activeTab === 'interventions'" class="trash-content">
          <div v-if="interventionsTrashed.length > 0" class="trash-grid">
            <div v-for="intervention in interventionsTrashed" :key="intervention.idIntervention" class="trash-card">
              <div class="trash-card-header">
                <div class="trash-icon intervention-icon">
                  <i class="bi bi-tools"></i>
                </div>
                <div class="trash-card-title">
                  <h3>{{ intervention.type }}</h3>
                  <span class="trash-card-badge cost-badge">{{ intervention.cout }} DT</span>
                </div>
              </div>
              
              <div class="trash-card-body">
                <div class="trash-info-row">
                  <i class="bi bi-car-front"></i>
                  <span>{{ intervention.voiture?.marque }} {{ intervention.voiture?.modele }}</span>
                </div>
                <div class="trash-info-row">
                  <i class="bi bi-calendar-event"></i>
                  <span>{{ formatDate(intervention.date) }}</span>
                </div>
                <div class="trash-info-row">
                  <i class="bi bi-shop"></i>
                  <span>{{ intervention.garage }}</span>
                </div>
                <div class="trash-info-row deleted-info">
                  <i class="bi bi-trash3"></i>
                  <span>{{ formatDate(intervention.deleted_at) }}</span>
                </div>
              </div>

              <div class="trash-card-actions">
                <button @click="restoreIntervention(intervention.idIntervention)" class="btn-restore-new">
                  <i class="bi bi-arrow-counterclockwise"></i>
                  {{ t('trash.actions.restore') }}
                </button>
                <button @click="forceDeleteIntervention(intervention.idIntervention)" class="btn-delete-new">
                  <i class="bi bi-x-circle"></i>
                </button>
              </div>
            </div>
          </div>
          <div v-else class="trash-empty">
            <div class="empty-icon">
              <i class="bi bi-inbox"></i>
            </div>
            <h3>{{ t('trash.empty.interventions') }}</h3>
            <p>{{ t('trash.empty.interventionsDesc') }}</p>
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
        { label: this.t('nav.dashboard'), to: '/admindashboard' },
        { label: this.t('nav.catalog'), to: '/voitures/cataloguevoitures' },
        { label: this.t('nav.interventions'), to: '/interventions/catalogue' },
        { label: this.t('nav.alerts'), to: '/alertes' },
      ],
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

.trash-tab {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  padding: 0.875rem 1.5rem;
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  font-size: 0.9375rem;
  font-weight: 500;
  color: #6b7280;
  transition: all 0.2s ease;
  position: relative;
  bottom: -2px;
}

.trash-tab i {
  font-size: 1.125rem;
}

.trash-tab:hover {
  color: #374151;
  background: #f9fafb;
}

.trash-tab.active {
  color: #748BAA;
  border-bottom-color: #748BAA;
  background: transparent;
}

.count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 1.5rem;
  height: 1.5rem;
  padding: 0 0.5rem;
  background: #e5e7eb;
  border-radius: 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #374151;
}

.trash-tab.active .count-badge {
  background: #748BAA;
  color: white;
}

/* Trash Content */
.trash-content {
  margin-top: 2rem;
}

.trash-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 1.5rem;
}

/* Trash Card */
.trash-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  overflow: hidden;
  transition: all 0.2s ease;
}

.trash-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.trash-card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.trash-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border-radius: 0.625rem;
  flex-shrink: 0;
}

.trash-icon i {
  font-size: 1.5rem;
  color: white;
}

.vehicle-icon {
  background: linear-gradient(135deg, #BFCC94 0%, #a8b87f 100%);
}

.intervention-icon {
  background: linear-gradient(135deg, #748BAA 0%, #5d728f 100%);
}

.trash-card-title {
  flex: 1;
  min-width: 0;
}

.trash-card-title h3 {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.trash-card-badge {
  display: inline-block;
  margin-top: 0.25rem;
  padding: 0.25rem 0.625rem;
  background: #e5e7eb;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #374151;
  text-transform: uppercase;
}

.cost-badge {
  background: #BFCC94;
  color: #4a5d3a;
}

.trash-card-body {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.875rem;
}

.trash-info-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.875rem;
  color: #374151;
}

.trash-info-row i {
  font-size: 1rem;
  color: #6b7280;
  width: 1.25rem;
  flex-shrink: 0;
}

.trash-info-row.deleted-info {
  margin-top: 0.5rem;
  padding-top: 0.875rem;
  border-top: 1px solid #e5e7eb;
  color: #9ca3af;
  font-size: 0.8125rem;
}

.trash-info-row.deleted-info i {
  color: #d1d5db;
}

.trash-card-actions {
  display: flex;
  gap: 0.5rem;
  padding: 1rem 1.25rem;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
}

.btn-restore-new {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  background: #748BAA;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-restore-new:hover {
  background: #5d728f;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(116, 139, 170, 0.3);
}

.btn-restore-new i {
  font-size: 1rem;
}

.btn-delete-new {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  padding: 0;
  background: white;
  color: #ef4444;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-delete-new:hover {
  background: #fef2f2;
  border-color: #ef4444;
  transform: scale(1.05);
}

.btn-delete-new i {
  font-size: 1.125rem;
}

/* Empty State */
.trash-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.empty-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 6rem;
  height: 6rem;
  margin-bottom: 1.5rem;
  background: #f3f4f6;
  border-radius: 50%;
}

.empty-icon i {
  font-size: 3rem;
  color: #9ca3af;
}

.trash-empty h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
}

.trash-empty p {
  margin: 0;
  font-size: 0.9375rem;
  color: #6b7280;
  max-width: 28rem;
}

/* Responsive */
@media (max-width: 768px) {
  .trash-grid {
    grid-template-columns: 1fr;
  }

  .trash-tabs {
    flex-direction: column;
    gap: 0;
    border-bottom: none;
  }

  .trash-tab {
    border-bottom: 1px solid #e5e7eb;
    border-left: 3px solid transparent;
    justify-content: space-between;
    bottom: 0;
  }

  .trash-tab.active {
    border-bottom-color: #e5e7eb;
    border-left-color: #748BAA;
    background: #f9fafb;
  }

  .trash-card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }

