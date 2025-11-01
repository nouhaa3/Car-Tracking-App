<template>
  <div class="historique-vehicule">
    <div class="historique-header">
      <h2>{{ t('history.title') }}</h2>
      <div class="vehicle-summary" v-if="vehicleInfo">
        <p><strong>{{ vehicleInfo.marque }} {{ vehicleInfo.modele }}</strong></p>
        <p>{{ t('history.year') }}: {{ vehicleInfo.annee }} | {{ t('history.mileage') }}: {{ vehicleInfo.kilometrage }} km</p>
      </div>
    </div>

    <div v-if="loading" class="loading">{{ t('history.loading') }}</div>

    <div v-else-if="timeline.length > 0" class="timeline">
      <div v-for="(item, index) in timeline" :key="index" class="timeline-item" :class="`timeline-${item.type}`">
        <div class="timeline-marker">
          <span v-if="item.type === 'intervention'">🔧</span>
          <span v-else>⚠️</span>
        </div>
        
        <div class="timeline-content">
          <div class="timeline-date">{{ formatDate(item.date) }}</div>
          <h3>{{ item.title }}</h3>
          
          <div v-if="item.type === 'intervention'" class="intervention-details">
            <p v-if="item.description" class="description">{{ item.description }}</p>
            <div class="meta-info">
              <span class="meta-item">💰 {{ item.cout }} {{ t('history.currency') }}</span>
              <span class="meta-item">🏢 {{ item.garage }}</span>
            </div>
          </div>
          
          <div v-else class="alerte-details">
            <p class="description">{{ item.description }}</p>
            <span class="status-badge" :class="`status-${item.status}`">
              {{ item.status === 'en_attente' ? t('history.pending') : item.status === 'resolue' ? t('history.resolved') : item.status }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <p>{{ t('history.noHistory') }}</p>
      <p class="empty-subtitle">{{ t('history.historySubtitle') }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import alerts from '@/utils/alerts';

export default {
  name: 'HistoriqueVehicule',
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
      timeline: [],
      vehicleInfo: null,
      loading: true
    };
  },
  mounted() {
    this.fetchHistorique();
  },
  methods: {
    async fetchHistorique() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://localhost:8000/api/voitures/${this.voitureId}/historique`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        this.vehicleInfo = response.data.voiture;
        this.timeline = response.data.timeline;
      } catch (error) {
        console.error('Erreur chargement historique:', error);
        alerts.alertError(this.t('history.loadError'));
      } finally {
        this.loading = false;
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }
  }
};
</script>

<style scoped>
.historique-vehicule {
  padding: 20px;
  background: #f8f9fa;
  border-radius: 12px;
}

.historique-header {
  margin-bottom: 24px;
}

.historique-header h2 {
  color: #344966;
  font-size: 24px;
  margin: 0 0 12px 0;
}

.vehicle-summary {
  background: white;
  padding: 16px;
  border-radius: 8px;
  border-left: 4px solid #344966;
}

.vehicle-summary p {
  margin: 4px 0;
  color: #666;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
  font-size: 16px;
}

.timeline {
  position: relative;
  padding-left: 40px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 19px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #ddd;
}

.timeline-item {
  position: relative;
  margin-bottom: 24px;
  padding-bottom: 24px;
}

.timeline-marker {
  position: absolute;
  left: -40px;
  top: 0;
  width: 40px;
  height: 40px;
  background: white;
  border: 3px solid #ddd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  z-index: 1;
}

.timeline-intervention .timeline-marker {
  border-color: #344966;
  background: #e8edf4;
}

.timeline-alerte .timeline-marker {
  border-color: #D4A574;
  background: #fff3e0;
}

.timeline-content {
  background: white;
  padding: 16px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
}

.timeline-content:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  transform: translateX(4px);
}

.timeline-date {
  color: #999;
  font-size: 13px;
  margin-bottom: 8px;
  font-weight: 600;
}

.timeline-content h3 {
  margin: 0 0 12px 0;
  color: #344966;
  font-size: 16px;
}

.description {
  color: #666;
  margin: 0 0 12px 0;
  line-height: 1.5;
}

.meta-info {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  color: #666;
  background: #f8f9fa;
  padding: 6px 12px;
  border-radius: 6px;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
}

.status-en_attente {
  background: #fff3e0;
  color: #D4A574;
}

.status-resolue {
  background: #e8f5e9;
  color: #4caf50;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.empty-state p {
  font-size: 18px;
  margin: 0 0 8px 0;
}

.empty-subtitle {
  font-size: 14px;
  color: #999;
}
</style>
