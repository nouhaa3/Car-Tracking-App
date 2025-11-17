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

<style scoped>
.home-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.layout {
  display: flex;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  margin-left: 280px;
  padding: 2rem;
  transition: margin-left 0.3s ease;
}

.profile-float {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.profile-float:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.profile-float .avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.navbar {
  background: white;
  padding: 1rem 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.nav-link {
  color: #64748b;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.nav-link:hover {
  color: #667eea;
  background: rgba(102, 126, 234, 0.1);
}

.nav-link.active {
  color: #667eea;
  background: rgba(102, 126, 234, 0.15);
}

.privacy-policy-container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  padding: 3rem;
  color: white;
  margin-bottom: 2rem;
  box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
}

.icon-wrapper i {
  font-size: 2.5rem;
}

.header-text h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.header-text .subtitle {
  font-size: 1.1rem;
  opacity: 0.95;
  margin: 0;
}

.last-updated {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.75rem 1.5rem;
  border-radius: 50px;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  backdrop-filter: blur(10px);
}

.privacy-content {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
}

.privacy-section {
  display: flex;
  gap: 2rem;
  padding: 2.5rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.privacy-section:last-child {
  border-bottom: none;
}

.section-icon {
  flex-shrink: 0;
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.section-content {
  flex: 1;
}

.section-content h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 1rem 0;
}

.section-content p {
  font-size: 1.05rem;
  line-height: 1.8;
  color: #4b5563;
  margin-bottom: 1.5rem;
}

.info-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.info-list li {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 0.75rem 0;
  font-size: 1.05rem;
  color: #4b5563;
}

.info-list li i {
  color: #10b981;
  font-size: 1.2rem;
  flex-shrink: 0;
  margin-top: 0.2rem;
}

.contact-section {
  background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
  border-radius: 15px;
  padding: 2.5rem;
  margin-top: 2rem;
  border: none;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.contact-item i {
  color: #667eea;
  font-size: 1.25rem;
}

.contact-item a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.contact-item a:hover {
  color: #764ba2;
}

@media (max-width: 1024px) {
  .content-wrapper {
    margin-left: 0;
  }
}

@media (max-width: 768px) {
  .content-wrapper {
    padding: 1rem;
  }

  .page-header {
    padding: 2rem;
    flex-direction: column;
    align-items: flex-start;
  }

  .header-content {
    flex-direction: column;
    align-items: flex-start;
  }

  .icon-wrapper {
    width: 60px;
    height: 60px;
  }

  .icon-wrapper i {
    font-size: 2rem;
  }

  .header-text h1 {
    font-size: 2rem;
  }

  .privacy-section {
    flex-direction: column;
    gap: 1.5rem;
    padding: 2rem 0;
  }

  .section-icon {
    width: 50px;
    height: 50px;
    font-size: 1.25rem;
  }

  .section-content h2 {
    font-size: 1.5rem;
  }
}
</style>
