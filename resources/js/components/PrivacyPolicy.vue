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

        <div class="privacy-policy-container">
          <!-- Header -->
          <div class="page-header">
            <div class="header-content">
              <div class="icon-wrapper">
                <i class="bi bi-shield-lock-fill"></i>
              </div>
              <div class="header-text">
                <h1>{{ t('privacy.title') }}</h1>
                <p class="subtitle">{{ t('privacy.subtitle') }}</p>
              </div>
            </div>
            <div class="last-updated">
              <i class="bi bi-calendar-check"></i>
              {{ t('privacy.lastUpdated') }}: {{ lastUpdated }}
            </div>
          </div>

          <!-- Content -->
          <div class="privacy-content">
            <!-- Introduction -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-info-circle-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.introduction.title') }}</h2>
                <p>{{ t('privacy.sections.introduction.content') }}</p>
              </div>
            </section>

            <!-- Data Collection -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-database-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.dataCollection.title') }}</h2>
                <p>{{ t('privacy.sections.dataCollection.content') }}</p>
                <ul class="info-list">
                  <li v-for="(item, index) in t('privacy.sections.dataCollection.items')" :key="index">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ item }}</span>
                  </li>
                </ul>
              </div>
            </section>

            <!-- Data Usage -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-gear-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.dataUsage.title') }}</h2>
                <p>{{ t('privacy.sections.dataUsage.content') }}</p>
                <ul class="info-list">
                  <li v-for="(item, index) in t('privacy.sections.dataUsage.items')" :key="index">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ item }}</span>
                  </li>
                </ul>
              </div>
            </section>

            <!-- Data Security -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-lock-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.dataSecurity.title') }}</h2>
                <p>{{ t('privacy.sections.dataSecurity.content') }}</p>
              </div>
            </section>

            <!-- Data Sharing -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-people-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.dataSharing.title') }}</h2>
                <p>{{ t('privacy.sections.dataSharing.content') }}</p>
              </div>
            </section>

            <!-- Your Rights -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-hand-thumbs-up-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.yourRights.title') }}</h2>
                <p>{{ t('privacy.sections.yourRights.content') }}</p>
                <ul class="info-list">
                  <li v-for="(item, index) in t('privacy.sections.yourRights.items')" :key="index">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ item }}</span>
                  </li>
                </ul>
              </div>
            </section>

            <!-- Cookies -->
            <section class="privacy-section">
              <div class="section-icon">
                <i class="bi bi-cookie"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.cookies.title') }}</h2>
                <p>{{ t('privacy.sections.cookies.content') }}</p>
              </div>
            </section>

            <!-- Contact -->
            <section class="privacy-section contact-section">
              <div class="section-icon">
                <i class="bi bi-envelope-fill"></i>
              </div>
              <div class="section-content">
                <h2>{{ t('privacy.sections.contact.title') }}</h2>
                <p>{{ t('privacy.sections.contact.content') }}</p>
                <div class="contact-info">
                  <div class="contact-item">
                    <i class="bi bi-envelope"></i>
                    <a :href="'mailto:' + t('privacy.sections.contact.email')">
                      {{ t('privacy.sections.contact.email') }}
                    </a>
                  </div>
                  <div class="contact-item">
                    <i class="bi bi-telephone"></i>
                    <a :href="'tel:' + t('privacy.sections.contact.phone')">
                      {{ t('privacy.sections.contact.phone') }}
                    </a>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>

    <Profile ref="profile" />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import Sidebar from './sidebar.vue';
import Profile from './profile.vue';

export default {
  name: 'PrivacyPolicy',
  components: {
    Sidebar,
    Profile,
  },
  setup() {
    const { t } = useI18n();
    const user = ref(null);
    const isExpanded = ref(false);
    
    const lastUpdated = new Date().toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });

    const menuItems = computed(() => [
      { label: t('navbar.dashboard'), to: '/admindashboard' },
      { label: t('navbar.interventions'), to: '/interventions' },
      { label: t('navbar.alerts'), to: '/alertes' },
      { label: t('navbar.vehicles'), to: '/voitures' },
      { label: t('navbar.reports'), to: '/rapports' },
    ]);

    onMounted(async () => {
      try {
        const response = await fetch('/api/user', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Accept': 'application/json'
          }
        });
        if (response.ok) {
          user.value = await response.json();
        }
      } catch (error) {
        console.error('Error fetching user:', error);
      }
    });

    return {
      t,
      lastUpdated,
      user,
      isExpanded,
      menuItems,
    };
  },
};
</script>


