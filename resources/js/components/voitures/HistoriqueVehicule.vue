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


