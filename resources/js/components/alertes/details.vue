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
          <router-link to="/alertes" class="breadcrumb-link">
            <i class="bi bi-arrow-left"></i>
            {{ t('alerts.backToCatalog') }}
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('alerts.loadingDetails') }}</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <p>{{ error }}</p>
          <button @click="fetchAlerte" class="retry-btn">{{ t('common.retry') }}</button>
        </div>

        <!-- Alerte Details -->
        <div v-else-if="alerte" class="car-details-wrapper">
          <div class="car-details-container">
            <!-- Left Side -->
            <div class="car-details-left">
              <!-- Header Card -->
              <div class="car-details-header card">
                <div class="header-content">
                  <div class="alert-type-display">
                    <div class="type-icon-large" :class="getPriorityClass(alerte.priorite)">
                      <i class="bi" :class="getTypeIcon(alerte.type)"></i>
                    </div>
                    <div>
                      <h1 class="car-title">{{ getTypeLabel(alerte.type) }}</h1>
                      <p class="car-year">{{ t('alerts.alertNumber', { id: alerte.idAlerte }) }}</p>
                    </div>
                  </div>
                  <span 
                    class="status-text-simple" 
                    :class="alerte.statut === 'En attente' ? 'status-pending-text' : 'status-treated-text'"
                  >
                    <i class="bi" :class="alerte.statut === 'En attente' ? 'bi-clock-fill' : 'bi-check-circle-fill'"></i>
                    {{ alerte.statut === 'En attente' ? t('alerts.pending') : t('alerts.treated') }}
                  </span>
                </div>
              </div>

              <!-- Vehicle Card -->
              <div class="card" v-if="alerte.voiture">
                <div class="card-header-section">
                  <h3>
                    <i class="bi bi-car-front-fill"></i>
                    {{ t('vehicles.concernedVehicle') }}
                  </h3>
                </div>
                <div class="vehicle-details-section">
                  <div class="vehicle-main-info">
                    <h4 class="vehicle-title">{{ vehicleName }}</h4>
                    <div class="vehicle-specs-grid">
                      <div class="spec-item">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ alerte.voiture.annee }}</span>
                      </div>
                      <div class="spec-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>{{ formatNumber(alerte.voiture.kilometrage) }} km</span>
                      </div>
                      <div class="spec-item">
                        <i class="bi bi-gear-fill"></i>
                        <span>{{ alerte.voiture.etat }}</span>
                      </div>
                      <div class="spec-item">
                        <i class="bi bi-bookmark-fill"></i>
                        <span>{{ alerte.voiture.statut }}</span>
                      </div>
                    </div>
                    <router-link 
                      :to="`/voitures/${alerte.voiture.idVoiture}`" 
                      class="btn-secondary"
                      style="margin-top: 1rem; display: inline-flex;"
                    >
                      <i class="bi bi-eye"></i>
                      {{ t('vehicles.viewVehicle') }}
                    </router-link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Side -->
            <div class="car-details-right">
              <!-- Alert Information -->
              <div class="info-card card">
                <div class="card-header-section">
                  <h3>
                    <i class="bi bi-info-circle-fill"></i>
                    {{ t('alerts.alertInformation') }}
                  </h3>
                  <div class="header-actions">
                    <button 
                      v-if="alerte.statut === 'En attente'"
                      class="btn-action btn-success" 
                      @click="resolveAlert" 
                      :disabled="isResolving"
                    >
                      <i :class="isResolving ? 'bi bi-hourglass-split' : 'bi bi-check-circle-fill'"></i>
                      <span>{{ isResolving ? t('common.processing') : t('alerts.resolve') }}</span>
                    </button>
                    <button class="btn-action btn-edit" @click="editAlert" :disabled="isResolving || isDeleting">
                      <i class="bi bi-pencil-square"></i>
                      <span>{{ t('common.edit') }}</span>
                    </button>
                    <button class="btn-action btn-delete" @click="deleteAlert" :disabled="isDeleting">
                      <i :class="isDeleting ? 'bi bi-hourglass-split' : 'bi bi-trash3'"></i>
                      <span>{{ isDeleting ? t('common.deleting') : t('common.delete') }}</span>
                    </button>
                  </div>
                </div>
                <div class="info-grid">
                  <div class="info-row">
                    <span class="info-label">
                      <i class="bi bi-calendar-event"></i>
                      {{ t('alerts.dueDate') }}
                    </span>
                    <span class="info-value">
                      {{ formatDate(alerte.dateEchance) }}
                    </span>
                  </div>

                  <div class="info-row" v-if="getDaysMessage(alerte)">
                    <span class="info-label">
                      <i class="bi bi-clock-history"></i>
                      {{ t('alerts.urgency') }}
                    </span>
                    <span class="info-value">
                      <span 
                        class="urgency-inline-badge" 
                        :class="getUrgencyClass(alerte)"
                      >
                        {{ getDaysMessage(alerte) }}
                      </span>
                    </span>
                  </div>

                  <div class="info-row" v-if="alerte.kilometrageEchance">
                    <span class="info-label">
                      <i class="bi bi-speedometer2"></i>
                      {{ t('alerts.mileageDue') }}
                    </span>
                    <span class="info-value">{{ formatNumber(alerte.kilometrageEchance) }} km</span>
                  </div>

                  <div class="info-row">
                    <span class="info-label">
                      {{ t('alerts.priority') }}
                    </span>
                    <span class="info-value">
                      <span 
                        class="badge-priority inline" 
                        :class="getPriorityClass(alerte.priorite)"
                      >
                        {{ getPriorityLabel(alerte.priorite) }}
                      </span>
                    </span>
                  </div>

                  <div class="info-row">
                    <span class="info-label">
                      <i class="bi bi-calendar-plus"></i>
                      {{ t('common.createdAt') }}
                    </span>
                    <span class="info-value">{{ formatDateTime(alerte.created_at) }}</span>
                  </div>

                  <div class="info-row" v-if="alerte.updated_at !== alerte.created_at">
                    <span class="info-label">
                      <i class="bi bi-pencil-square"></i>
                      {{ t('common.lastModified') }}
                    </span>
                    <span class="info-value">{{ formatDateTime(alerte.updated_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Sidebar from "../sidebar.vue";
import alerts from '@/utils/alerts';
import { useI18n } from 'vue-i18n';

export default {
  name: "DetailsAlerte",
  components: { Sidebar },

  setup() {
    const router = useRouter();
    const route = useRoute();
    const { t } = useI18n();
    const theme = ref("light");
    const isExpanded = ref(true);
    const user = ref(null);
    const alerte = ref(null);
    const isLoading = ref(true);
    const error = ref(null);
    const isResolving = ref(false);
    const isDeleting = ref(false);

    const alerteId = route.params.id;

    const menuItems = computed(() => {
      const role = user.value?.role?.nomRole?.toLowerCase();
      
      if (role === "admin") {
        return [
          { label: t('menu.dashboard'), to: "/admindashboard", icon: "speedometer2" },
          { label: t('menu.catalog'), to: "/voitures/cataloguevoitures", icon: "car-front" },
          { label: t('menu.interventions'), to: "/interventions/catalogue", icon: "tools" },
          { label: t('menu.alerts'), to: "/alertes", icon: "bell" },
        ];
      } else if (role === "agent") {
        return [
          { label: t('menu.dashboard'), to: "/agentdashboard", icon: "speedometer2" },
          { label: t('vehicles.vehicles'), to: "/voitures/cataloguevoitures", icon: "car-front" },
          { label: t('menu.interventions'), to: "/interventions/catalogue", icon: "tools" },
          { label: t('menu.alerts'), to: "/alertes", icon: "bell" },
          { label: t('menu.messages'), to: "/chats", icon: "chat" },
        ];
      } else {
        return [
          { label: t('menu.dashboard'), to: "/techniciendashboard", icon: "speedometer2" },
          { label: t('menu.interventions'), to: "/interventions/catalogue", icon: "tools" },
          { label: t('menu.alerts'), to: "/alertes", icon: "bell" },
        ];
      }
    });

    const vehicleName = computed(() => {
      if (!alerte.value?.voiture) return t('common.notAvailable');
      return `${alerte.value.voiture.marque} ${alerte.value.voiture.modele}`;
    });

    const alertTypes = computed(() => [
      { value: 'Vidange', label: t('alerts.types.oilChange'), icon: 'bi-droplet-fill' },
      { value: 'Révision', label: t('alerts.types.revision'), icon: 'bi-wrench-adjustable' },
      { value: 'Contrôle technique', label: t('alerts.types.technicalControl'), icon: 'bi-clipboard-check' },
      { value: 'Changement pneus', label: t('alerts.types.tireChange'), icon: 'bi-circle' },
      { value: 'Contrôle freins', label: t('alerts.types.brakeCheck'), icon: 'bi-stop-circle' },
      { value: 'Remplacement batterie', label: t('alerts.types.batteryReplacement'), icon: 'bi-battery-charging' },
      { value: 'État véhicule critique', label: t('alerts.types.criticalState'), icon: 'bi-exclamation-triangle-fill' },
      { value: 'Maintenance prolongée', label: t('alerts.types.extendedMaintenance'), icon: 'bi-clock-history' },
      { value: 'Coûts élevés', label: t('alerts.types.highCosts'), icon: 'bi-currency-dollar' },
      { value: 'Révision annuelle', label: t('alerts.types.annualRevision'), icon: 'bi-calendar-check' },
    ]);

    const getUser = async () => {
      try {
        const token = localStorage.getItem("token");
        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` },
        });
        user.value = res.data;
      } catch (err) {
        console.error("Error fetching user:", err);
        router.push("/login");
      }
    };

    const fetchAlerte = async () => {
      isLoading.value = true;
      error.value = null;

      try {
        const token = localStorage.getItem("token");
        const res = await axios.get(`http://127.0.0.1:8000/api/alertes/${alerteId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        alerte.value = res.data;

        // Fetch the vehicle data if voiture_id exists
        if (res.data.voiture_id) {
          try {
            const voitureRes = await axios.get(`http://127.0.0.1:8000/api/voitures/${res.data.voiture_id}`, {
              headers: {
                Authorization: token ? `Bearer ${token}` : "",
                Accept: "application/json"
              },
              validateStatus: function (status) {
                return status < 500; // Don't throw for 404
              }
            });
            // Add the vehicle object to the alert only if found
            if (voitureRes.status === 200) {
              alerte.value.voiture = voitureRes.data;
            }
          } catch (voitureErr) {
            // Only log unexpected errors
            console.error("Unexpected error fetching vehicle:", voitureErr);
          }
        }
      } catch (err) {
        console.error("Error fetching alerte:", err);
        error.value = t('errors.loadingAlert');
      } finally {
        isLoading.value = false;
      }
    };

    const resolveAlert = async () => {
      const confirmed = await alerts.confirmAction(
        t('alerts.markAsTreated'),
        t('alerts.markAsTreatedConfirmation')
      );
      if (!confirmed) return;

      isResolving.value = true;
      try {
        const token = localStorage.getItem("token");
        await axios.patch(
          `http://127.0.0.1:8000/api/alertes/${alerteId}/resolve`,
          {},
          { headers: { Authorization: `Bearer ${token}` } }
        );

        await alerts.alertSuccess(
          t('alerts.treated'),
          t('alerts.markedAsTreated')
        );
        await fetchAlerte();
      } catch (err) {
        console.error("Error resolving alert:", err);
        await alerts.alertError(
          t('common.error'),
          t('errors.resolvingAlert')
        );
      } finally {
        isResolving.value = false;
      }
    };

    const editAlert = () => {
      // Navigate to edit alert page (when implemented)
      // For now, show a message
      if (alerte.value.statut !== 'En attente') {
        alerts.alertWarning(
          t('common.notAvailable'),
          t('alerts.cannotEditTreated')
        );
        return;
      }
      alerts.alertInfo(
        t('common.comingSoon'),
        t('alerts.editFeatureComing')
      );
    };

    const generateIntervention = () => {
      if (!alerte.value?.voiture) return;
      
      // Navigate to add intervention page with pre-filled vehicle
      router.push({
        path: "/interventions/ajouter",
        query: { voiture_id: alerte.value.voiture.idVoiture }
      });
    };

    const deleteAlert = async () => {
      const confirmed = await alerts.confirmDelete(t('alerts.thisAlert'));
      if (!confirmed) return;

      isDeleting.value = true;
      try {
        const token = localStorage.getItem("token");
        await axios.delete(`http://127.0.0.1:8000/api/alertes/${alerteId}`, {
          headers: { Authorization: `Bearer ${token}` },
        });

        await alerts.alertSuccess(
          t('common.deleted'),
          t('alerts.alertDeletedSuccess')
        );
        router.push("/alertes");
      } catch (err) {
        console.error("Error deleting alert:", err);
        await alerts.alertError(
          t('common.error'),
          t('errors.deletingAlert')
        );
      } finally {
        isDeleting.value = false;
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    };

    const formatDateTime = (date) => {
      return new Date(date).toLocaleDateString("fr-FR", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    };

    const formatNumber = (num) => {
      return new Intl.NumberFormat("fr-FR").format(num);
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat("fr-TN", {
        style: "currency",
        currency: "TND",
      }).format(amount);
    };

    const getVehicleImage = (voiture) => {
      if (voiture.image) {
        return `http://127.0.0.1:8000/storage/${voiture.image}`;
      }
      return "http://127.0.0.1:8000/images/default.png";
    };

    const getPriorityIcon = (priority) => {
      return {
        critique: "bi-exclamation-triangle-fill",
        haute: "bi-exclamation-circle-fill",
        moyenne: "bi-info-circle-fill",
        faible: "bi-info-circle",
      }[priority] || "bi-info-circle";
    };

    const getTypeIcon = (type) => {
      const typeObj = alertTypes.value.find(t => t.value === type);
      return typeObj ? typeObj.icon : "bi-bell";
    };

    const getTypeLabel = (type) => {
      const typeObj = alertTypes.value.find(t => t.value === type);
      return typeObj ? typeObj.label : type;
    };

    const getPriorityClass = (priority) => {
      return `priority-${priority.toLowerCase()}`;
    };

    const getPriorityLabel = (priority) => {
      const labels = {
        'critique': t('alerts.critical'),
        'haute': t('alerts.high'),
        'moyenne': t('alerts.medium'),
        'faible': t('alerts.low')
      };
      return labels[priority] || priority.toUpperCase();
    };

    const getUrgencyClass = (alerte) => {
      const daysUntil = getDaysUntilDue(alerte.dateEchance);
      if (daysUntil < 0) return "urgency-overdue";
      if (daysUntil === 0) return "urgency-today";
      if (daysUntil <= 7) return "urgency-urgent";
      return "urgency-normal";
    };

    const getDaysUntilDue = (dateEchance) => {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const dueDate = new Date(dateEchance);
      dueDate.setHours(0, 0, 0, 0);
      const diffTime = dueDate - today;
      return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    };

    const getDaysMessage = (alerte) => {
      const daysUntil = getDaysUntilDue(alerte.dateEchance);
      
      if (daysUntil < 0) {
        return t('alerts.overdueBy', { days: Math.abs(daysUntil) });
      } else if (daysUntil === 0) {
        return t('alerts.dueToday');
      } else if (daysUntil <= 7) {
        return t('alerts.daysLeftUrgent', { days: daysUntil });
      } else if (daysUntil <= 30) {
        return t('alerts.daysLeft', { days: daysUntil });
      }
      return null;
    };

    onMounted(async () => {
      await getUser();
      await fetchAlerte();
    });

    return {
      theme,
      isExpanded,
      user,
      menuItems,
      alerte,
      isLoading,
      error,
      isResolving,
      isDeleting,
      vehicleName,
      alertTypes,
      fetchAlerte,
      resolveAlert,
      editAlert,
      generateIntervention,
      deleteAlert,
      formatDate,
      formatDateTime,
      formatNumber,
      formatCurrency,
      getVehicleImage,
      getPriorityIcon,
      getTypeIcon,
      getTypeLabel,
      getPriorityClass,
      getPriorityLabel,
      getUrgencyClass,
      getDaysMessage,
      t
    };
  },
};
</script>

