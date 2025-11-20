<template>
  <div class="home-page">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />
      
      <div class="main-content">
        <!-- Profile Float -->
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" alt="User" class="avatar" />
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

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('common.loading') }}</p>
        </div>

        <!-- Settings Content -->
        <div v-else class="profile-wrapper">
          <div class="page-header">
            <div class="header-left">
              <h1>{{ t('settings.title') }}</h1>
              <p>{{ t('settings.subtitle') }}</p>
            </div>
          </div>

          <!-- Settings Sections -->
          <div class="settings-container">
            
            <!-- 1. Paramètres Généraux -->
            <div class="settings-section card">
              <div class="section-header" @click="toggleSection('general')">
                <div class="section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                  <h3>{{ t('settings.general.title') }}</h3>
                </div>
                <svg class="collapse-icon" :class="{ expanded: expandedSections.general }" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              <div v-show="expandedSections.general" class="section-content">
                <div class="form-grid">
                  <div class="form-group">
                    <label>{{ t('settings.general.language') }}</label>
                    <select v-model="settings.language" class="form-input">
                      <option value="fr">{{ t('settings.general.french') }}</option>
                      <option value="en">{{ t('settings.general.english') }}</option>
                      <option value="ar">{{ t('settings.general.arabic') }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.dateFormat') }}</label>
                    <div class="radio-group">
                      <label class="radio-label">
                        <input type="radio" v-model="settings.dateFormat" value="DD/MM/YYYY" />
                        <span>DD/MM/YYYY</span>
                      </label>
                      <label class="radio-label">
                        <input type="radio" v-model="settings.dateFormat" value="MM/DD/YYYY" />
                        <span>MM/DD/YYYY</span>
                      </label>
                      <label class="radio-label">
                        <input type="radio" v-model="settings.dateFormat" value="YYYY-MM-DD" />
                        <span>YYYY-MM-DD</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.timeFormat') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="timeFormat" v-model="settings.time24h" />
                      <label for="timeFormat" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.time24h ? '24h' : '12h' }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.currency') }}</label>
                    <select v-model="settings.currency" class="form-input">
                      <option value="TND">TND ({{ t('settings.general.tunisianDinar') }})</option>
                      <option value="EUR">EUR ({{ t('settings.general.euro') }})</option>
                      <option value="USD">USD ({{ t('settings.general.dollar') }})</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.distanceUnit') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="distanceUnit" v-model="settings.useMiles" />
                      <label for="distanceUnit" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.useMiles ? t('settings.general.miles') : t('settings.general.kilometers') }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.fuelUnit') }}</label>
                    <select v-model="settings.fuelUnit" class="form-input">
                      <option value="L/100km">L/100km</option>
                      <option value="MPG">MPG</option>
                      <option value="km/L">km/L</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.volumeUnit') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="volumeUnit" v-model="settings.useGallons" />
                      <label for="volumeUnit" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.useGallons ? t('settings.general.gallons') : t('settings.general.liters') }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.general.defaultVehicle') }}</label>
                    <select v-model="settings.defaultVehicle" class="form-input">
                      <option value="">{{ t('settings.general.none') }}</option>
                      <option v-for="vehicle in vehicles" :key="vehicle.idVoiture" :value="vehicle.idVoiture">
                        {{ vehicle.marque }} {{ vehicle.modele }} ({{ vehicle.immatriculation }})
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. Affichage et Thème -->
            <div class="settings-section card">
              <div class="section-header" @click="toggleSection('display')">
                <div class="section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                  </svg>
                  <h3>{{ t('settings.display.title') }}</h3>
                </div>
                <svg class="collapse-icon" :class="{ expanded: expandedSections.display }" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              <div v-show="expandedSections.display" class="section-content">
                <div class="form-grid">
                  <div class="form-group">
                    <label>{{ t('settings.display.fontSize') }}</label>
                    <select v-model="settings.fontSize" class="form-input">
                      <option value="small">{{ t('settings.display.fontSmall') }}</option>
                      <option value="medium">{{ t('settings.display.fontMedium') }}</option>
                      <option value="large">{{ t('settings.display.fontLarge') }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.display.viewDensity') }}</label>
                    <div class="radio-group">
                      <label class="radio-label">
                        <input type="radio" v-model="settings.viewDensity" value="compact" />
                        <span>{{ t('settings.display.compact') }}</span>
                      </label>
                      <label class="radio-label">
                        <input type="radio" v-model="settings.viewDensity" value="comfortable" />
                        <span>{{ t('settings.display.comfortable') }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 3. Notifications -->
            <div class="settings-section card">
              <div class="section-header" @click="toggleSection('notifications')">
                <div class="section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                  </svg>
                  <h3>{{ t('settings.notifications.title') }}</h3>
                </div>
                <svg class="collapse-icon" :class="{ expanded: expandedSections.notifications }" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              <div v-show="expandedSections.notifications" class="section-content">
                <div class="form-grid">
                  <div class="form-group full-width">
                    <label>{{ t('settings.notifications.enable') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="notificationsEnabled" v-model="settings.notificationsEnabled" />
                      <label for="notificationsEnabled" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.notificationsEnabled ? t('settings.notifications.enabled') : t('settings.notifications.disabled') }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group" :class="{ disabled: !settings.notificationsEnabled }">
                    <label>{{ t('settings.notifications.expirationNotice') }}</label>
                    <select v-model="settings.expirationNoticeDays" class="form-input" :disabled="!settings.notificationsEnabled">
                      <option value="7">{{ t('settings.notifications.daysBefore', { days: 7 }) }}</option>
                      <option value="15">{{ t('settings.notifications.daysBefore', { days: 15 }) }}</option>
                      <option value="30">{{ t('settings.notifications.daysBefore', { days: 30 }) }}</option>
                    </select>
                  </div>

                  <div class="form-group" :class="{ disabled: !settings.notificationsEnabled }">
                    <label>{{ t('settings.notifications.sound') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="notificationSound" v-model="settings.notificationSound" :disabled="!settings.notificationsEnabled" />
                      <label for="notificationSound" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.notificationSound ? t('settings.notifications.on') : t('settings.notifications.off') }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group full-width" :class="{ disabled: !settings.notificationsEnabled }">
                    <label>{{ t('settings.notifications.quietHours') }}</label>
                    <div class="time-range">
                      <input type="time" v-model="settings.quietHoursStart" class="form-input" :disabled="!settings.notificationsEnabled" />
                      <span>{{ t('settings.notifications.to') }}</span>
                      <input type="time" v-model="settings.quietHoursEnd" class="form-input" :disabled="!settings.notificationsEnabled" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 4. Gestion des Données -->
            <div class="settings-section card">
              <div class="section-header" @click="toggleSection('data')">
                <div class="section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                  </svg>
                  <h3>{{ t('settings.data.title') }}</h3>
                </div>
                <svg class="collapse-icon" :class="{ expanded: expandedSections.data }" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              <div v-show="expandedSections.data" class="section-content">
                <div class="form-grid">
                  <div class="form-group">
                    <label>{{ t('settings.data.autoBackup') }}</label>
                    <select v-model="settings.autoBackupFrequency" class="form-input">
                      <option value="daily">{{ t('settings.data.daily') }}</option>
                      <option value="weekly">{{ t('settings.data.weekly') }}</option>
                      <option value="monthly">{{ t('settings.data.monthly') }}</option>
                      <option value="never">{{ t('settings.data.never') }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.data.storageUsed') }}</label>
                    <div class="storage-info">
                      <div class="storage-bar">
                        <div class="storage-used" :style="{ width: storagePercentage + '%' }"></div>
                      </div>
                      <p class="storage-text">{{ storageUsed }} / {{ storageTotal }}</p>
                    </div>
                  </div>

                  <div class="form-group full-width">
                    <button @click="clearCache" class="btn-secondary" :disabled="clearingCache">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="1 4 1 10 7 10"></polyline>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                      </svg>
                      {{ clearingCache ? t('settings.data.clearing') : t('settings.data.clearCache') }}
                    </button>
                  </div>

                  <div class="form-group full-width">
                    <button @click="manageStorage" class="btn-secondary">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                      </svg>
                      {{ t('settings.data.manageStorage') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 5. Confidentialité et Sécurité -->
            <div class="settings-section card">
              <div class="section-header" @click="toggleSection('security')">
                <div class="section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  </svg>
                  <h3>{{ t('settings.security.title') }}</h3>
                </div>
                <svg class="collapse-icon" :class="{ expanded: expandedSections.security }" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              <div v-show="expandedSections.security" class="section-content">
                <div class="form-grid">
                  <div class="form-group">
                    <label>{{ t('settings.security.autoLock') }}</label>
                    <select v-model="settings.autoLockTimeout" class="form-input">
                      <option value="0">{{ t('settings.security.immediately') }}</option>
                      <option value="1">{{ t('settings.security.minute', { count: 1 }) }}</option>
                      <option value="5">{{ t('settings.security.minute', { count: 5 }) }}</option>
                      <option value="15">{{ t('settings.security.minute', { count: 15 }) }}</option>
                      <option value="30">{{ t('settings.security.minute', { count: 30 }) }}</option>
                      <option value="-1">{{ t('settings.data.never') }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.security.passwordOnLaunch') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="requirePassword" v-model="settings.requirePasswordOnLaunch" />
                      <label for="requirePassword" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.requirePasswordOnLaunch ? t('settings.notifications.on') : t('settings.notifications.off') }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>{{ t('settings.security.dataSharing') }}</label>
                    <div class="toggle-switch">
                      <input type="checkbox" id="dataSharing" v-model="settings.dataSharing" />
                      <label for="dataSharing" class="switch-label">
                        <span class="switch-slider"></span>
                        <span class="switch-text">{{ settings.dataSharing ? t('settings.security.allowed') : t('settings.security.refused') }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group full-width">
                    <button @click="viewPrivacyPolicy" class="btn-link">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                      </svg>
                      <span>{{ t('settings.security.privacyPolicy') }}</span>
                    </button>
                  </div>

                  <div class="form-group full-width">
                    <button @click="viewTermsOfService" class="btn-link">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                      </svg>
                      <span>{{ t('settings.security.termsOfService') }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 6. À Propos -->
            <div class="settings-section card">
              <div class="section-header" @click="toggleSection('about')">
                <div class="section-title">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                  </svg>
                  <h3>{{ t('settings.about.title') }}</h3>
                </div>
                <svg class="collapse-icon" :class="{ expanded: expandedSections.about }" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
              
              <div v-show="expandedSections.about" class="section-content">
                <div class="form-grid">
                  <div class="form-group">
                    <label>{{ t('settings.about.version') }}</label>
                    <p class="info-value">{{ appVersion }}</p>
                  </div>

                  <div class="form-group full-width">
                    <button @click="checkForUpdates" class="btn-secondary" :disabled="checkingUpdates">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                      </svg>
                      {{ checkingUpdates ? t('settings.about.checking') : t('settings.about.checkUpdates') }}
                    </button>
                  </div>

                  <div class="form-group full-width">
                    <button @click="contactSupport" class="btn-link">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                      </svg>
                      <span>{{ t('settings.about.contactSupport') }}</span>
                    </button>
                  </div>

                  <div class="form-group full-width">
                    <button @click="rateApp" class="btn-link">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                      </svg>
                      <span>{{ t('settings.about.rateApp') }}</span>
                    </button>
                  </div>

                  <div class="form-group full-width">
                    <button @click="viewLicense" class="btn-link">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                      </svg>
                      <span>{{ t('settings.about.license') }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- Save Button -->
          <div class="settings-actions">
            <button @click="saveSettings" class="btn-primary" :disabled="saving">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              {{ saving ? t('settings.saving') : t('settings.saveSettings') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Sidebar from './sidebar.vue';
import alerts from '@/utils/alerts';
import { useSettings } from '@/composables/useSettings';

export default {
  components: {
    Sidebar,
  },
  setup() {
    const { t } = useI18n();
    const { settings: globalSettings, updateSettings } = useSettings();
    
    const isExpanded = ref(false);
    const user = ref(null);
    const loading = ref(true);
    const saving = ref(false);
    const clearingCache = ref(false);
    const checkingUpdates = ref(false);
    const vehicles = ref([]);
    
    const appVersion = ref('1.0.0');
    const storageUsed = ref('45.2 MB');
    const storageTotal = ref('100 MB');
    const storagePercentage = ref(45);

    const menuItems = computed(() => [
      { label: t('nav.dashboard'), to: "/admindashboard" },
      { label: t('nav.catalog'), to: "/voitures/cataloguevoitures" },
      { label: t('nav.interventions'), to: "/interventions/catalogue" },
      { label: t('nav.alerts'), to: "/alertes" },
    ]);

    const expandedSections = reactive({
      general: true,
      display: false,
      notifications: false,
      data: false,
      security: false,
      about: false,
    });

    // Use reactive copy of global settings
    const settings = reactive({ ...globalSettings });

    const toggleSection = (section) => {
      expandedSections[section] = !expandedSections[section];
    };

    const fetchUserData = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: { Authorization: `Bearer ${token}` },
        });
        user.value = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement de l\'utilisateur:', error);
      }
    };

    const fetchVehicles = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/voitures', {
          headers: { Authorization: `Bearer ${token}` },
        });
        vehicles.value = response.data.filter(v => !v.deleted_at);
      } catch (error) {
        console.error('Erreur lors du chargement des véhicules:', error);
      }
    };

    const loadSettings = async () => {
      loading.value = true;
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/settings', {
          headers: { Authorization: `Bearer ${token}` },
        });
        
        if (response.data) {
          Object.assign(settings, response.data);
          // Also update global settings
          updateSettings(response.data);
        }
      } catch (error) {
        console.error('Erreur lors du chargement des paramètres:', error);
        // Settings are already loaded from localStorage by useSettings composable
        Object.assign(settings, globalSettings);
      } finally {
        loading.value = false;
      }
    };

    const saveSettings = async () => {
      saving.value = true;
      try {
        const token = localStorage.getItem('token');
        await axios.post(
          'http://127.0.0.1:8000/api/settings',
          settings,
          {
            headers: { Authorization: `Bearer ${token}` },
          }
        );
        
        // Update global settings (this applies them immediately)
        updateSettings(settings);
        
        await alerts.alertSuccess(t('common.success'), t('settings.settingsSaved'));
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement:', error);
        
        // Update global settings even if API fails
        updateSettings(settings);
        
        await alerts.alertWarning(t('common.warning'), t('settings.settingsSaved'));
      } finally {
        saving.value = false;
      }
    };

    const clearCache = async () => {
      const confirmed = await alerts.confirm(
        t('settings.data.clearCache'),
        t('settings.data.confirmClearCache')
      );
      
      if (confirmed) {
        clearingCache.value = true;
        try {
          // Simulate cache clearing
          await new Promise(resolve => setTimeout(resolve, 1500));
          await alerts.alertSuccess(t('common.success'), t('settings.data.cacheCleared'));
        } catch (error) {
          await alerts.alertError(t('common.error'), 'Erreur lors du nettoyage du cache');
        } finally {
          clearingCache.value = false;
        }
      }
    };

    const manageStorage = () => {
      alerts.alertInfo(t('settings.data.manageStorage'), 'Fonctionnalité de gestion du stockage à venir');
    };

    const checkForUpdates = async () => {
      checkingUpdates.value = true;
      try {
        await new Promise(resolve => setTimeout(resolve, 2000));
        await alerts.alertSuccess(t('common.success'), t('settings.about.upToDate'));
      } catch (error) {
        await alerts.alertError(t('common.error'), 'Impossible de vérifier les mises à jour');
      } finally {
        checkingUpdates.value = false;
      }
    };

    const viewPrivacyPolicy = () => {
      window.open('/privacy-policy', '_blank');
    };

    const viewTermsOfService = () => {
      window.open('/terms-of-service', '_blank');
    };

    const contactSupport = () => {
      window.location.href = 'mailto:support@cartracking.com';
    };

    const rateApp = () => {
      alerts.alertInfo(t('settings.about.rateApp'), 'Merci pour votre soutien!');
    };

    const viewLicense = () => {
      window.open('/license', '_blank');
    };

    onMounted(async () => {
      await fetchUserData();
      await fetchVehicles();
      await loadSettings();
    });

    return {
      t,
      isExpanded,
      user,
      loading,
      saving,
      clearingCache,
      checkingUpdates,
      vehicles,
      menuItems,
      expandedSections,
      settings,
      appVersion,
      storageUsed,
      storageTotal,
      storagePercentage,
      toggleSection,
      saveSettings,
      clearCache,
      manageStorage,
      checkForUpdates,
      viewPrivacyPolicy,
      viewTermsOfService,
      contactSupport,
      rateApp,
      viewLicense,
    };
  },
};
</script>


