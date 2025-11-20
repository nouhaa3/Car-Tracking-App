<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <!-- Profile floating icon -->
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.user')" class="avatar" />
        </router-link>

        <!-- Bootstrap Icons -->
        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <!-- Navigation -->
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

        <!-- Main Content -->
        <div class="terms-container">
          <!-- Header with navigation -->
          <div class="content-header">
            <div class="header-left">
              <h1 class="page-title">{{ t('terms.title') }}</h1>
              <p class="page-subtitle">{{ t('terms.subtitle') }}</p>
            </div>
            <div class="header-actions">
              <button class="btn-secondary" @click="downloadTerms">
                <i class="bi bi-download"></i>
                {{ t('terms.downloadPDF') }}
              </button>
              <button class="btn-primary" @click="printTerms">
                <i class="bi bi-printer"></i>
                {{ t('common.print') }}
              </button>
            </div>
          </div>

          <!-- Quick Navigation -->
          <nav class="quick-nav">
            <button 
              v-for="section in sections" 
              :key="section.id"
              class="nav-item"
              :class="{ active: activeSection === section.id }"
              @click="scrollToSection(section.id)"
            >
              <i :class="section.icon"></i>
              {{ section.title }}
            </button>
          </nav>

          <!-- Main Content -->
          <div class="terms-wrapper">
            <!-- Summary Card -->
            <div class="summary-card">
              <div class="summary-header">
                <i class="bi bi-info-circle-fill"></i>
                <h3>{{ t('terms.summary.title') }}</h3>
              </div>
              <div class="summary-content">
                <p>{{ t('terms.summary.content') }}</p>
                <div class="summary-meta">
                  <div class="meta-item">
                    <i class="bi bi-calendar-check"></i>
                    <span><strong>{{ t('terms.lastUpdated') }}:</strong> {{ formatDate(lastUpdated) }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="bi bi-clock"></i>
                    <span><strong>{{ t('terms.readingTime') }}:</strong> {{ readingTime }}</span>
                  </div>
                  <div class="meta-item">
                    <i class="bi bi-file-text"></i>
                    <span><strong>{{ t('terms.version') }}:</strong> {{ version }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Terms Content -->
            <div class="terms-content" ref="termsContent">
              <!-- Acceptance Section -->
              <section id="acceptance" class="terms-section">
                <div class="section-header">
                  <div class="section-icon acceptance">
                    <i class="bi bi-check-circle-fill"></i>
                  </div>
                  <div class="section-title">
                    <h2>{{ t('terms.sections.acceptance.title') }}</h2>
                    <span class="section-badge required">{{ t('terms.required') }}</span>
                  </div>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.acceptance.content') }}</p>
                  <div class="notice-box important">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                      <strong>{{ t('terms.important') }}</strong>
                      {{ t('terms.sections.acceptance.notice') }}
                    </div>
                  </div>
                </div>
              </section>

              <!-- Service Description -->
              <section id="description" class="terms-section">
                <div class="section-header">
                  <div class="section-icon description">
                    <i class="bi bi-box-seam-fill"></i>
                  </div>
                  <h2>{{ t('terms.sections.description.title') }}</h2>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.description.content') }}</p>
                  <div class="features-grid">
                    <div v-for="(feature, index) in serviceFeatures" :key="index" class="feature-item">
                      <i class="bi bi-check-lg"></i>
                      <span>{{ feature }}</span>
                    </div>
                  </div>
                </div>
              </section>

              <!-- User Accounts -->
              <section id="accounts" class="terms-section">
                <div class="section-header">
                  <div class="section-icon accounts">
                    <i class="bi bi-person-fill"></i>
                  </div>
                  <h2>{{ t('terms.sections.accounts.title') }}</h2>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.accounts.content') }}</p>
                  <div class="requirements-list">
                    <div v-for="(requirement, index) in accountRequirements" :key="index" class="requirement-item">
                      <div class="requirement-number">{{ index + 1 }}</div>
                      <div class="requirement-content">
                        <strong>{{ requirement.title }}</strong>
                        <p>{{ requirement.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

              <!-- Acceptable Use -->
              <section id="acceptable-use" class="terms-section warning">
                <div class="section-header">
                  <div class="section-icon warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                  </div>
                  <div class="section-title">
                    <h2>{{ t('terms.sections.acceptable.title') }}</h2>
                    <span class="section-badge restricted">{{ t('terms.restricted') }}</span>
                  </div>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.acceptable.content') }}</p>
                  <div class="prohibited-list">
                    <div v-for="(item, index) in prohibitedActivities" :key="index" class="prohibited-item">
                      <i class="bi bi-x-circle-fill"></i>
                      <div>
                        <strong>{{ item.title }}</strong>
                        <p>{{ item.description }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="notice-box warning">
                    <i class="bi bi-shield-exclamation"></i>
                    <div>
                      <strong>{{ t('terms.warning') }}</strong>
                      {{ t('terms.sections.acceptable.warning') }}
                    </div>
                  </div>
                </div>
              </section>

              <!-- Intellectual Property -->
              <section id="intellectual-property" class="terms-section">
                <div class="section-header">
                  <div class="section-icon intellectual">
                    <i class="bi bi-c-circle-fill"></i>
                  </div>
                  <h2>{{ t('terms.sections.intellectual.title') }}</h2>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.intellectual.content') }}</p>
                  <div class="ip-grid">
                    <div class="ip-item">
                      <i class="bi bi-c-circle"></i>
                      <h4>{{ t('terms.sections.intellectual.copyright') }}</h4>
                      <p>{{ t('terms.sections.intellectual.copyrightDesc') }}</p>
                    </div>
                    <div class="ip-item">
                      <i class="bi bi-trademark"></i>
                      <h4>{{ t('terms.sections.intellectual.trademarks') }}</h4>
                      <p>{{ t('terms.sections.intellectual.trademarksDesc') }}</p>
                    </div>
                    <div class="ip-item">
                      <i class="bi bi-lightbulb"></i>
                      <h4>{{ t('terms.sections.intellectual.patents') }}</h4>
                      <p>{{ t('terms.sections.intellectual.patentsDesc') }}</p>
                    </div>
                  </div>
                </div>
              </section>

              <!-- Limitation of Liability -->
              <section id="liability" class="terms-section">
                <div class="section-header">
                  <div class="section-icon liability">
                    <i class="bi bi-shield-fill"></i>
                  </div>
                  <h2>{{ t('terms.sections.liability.title') }}</h2>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.liability.content') }}</p>
                  <div class="disclaimer-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                      <strong>{{ t('terms.disclaimer') }}</strong>
                      {{ t('terms.sections.liability.disclaimer') }}
                    </div>
                  </div>
                </div>
              </section>

              <!-- Contact Information -->
              <section id="contact" class="terms-section contact">
                <div class="section-header">
                  <div class="section-icon contact">
                    <i class="bi bi-envelope-fill"></i>
                  </div>
                  <h2>{{ t('terms.sections.contact.title') }}</h2>
                </div>
                <div class="section-body">
                  <p>{{ t('terms.sections.contact.content') }}</p>
                  <div class="contact-grid">
                    <div class="contact-method">
                      <i class="bi bi-envelope"></i>
                      <div>
                        <strong>{{ t('terms.sections.contact.email') }}</strong>
                        <a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a>
                      </div>
                    </div>
                    <div class="contact-method">
                      <i class="bi bi-telephone"></i>
                      <div>
                        <strong>{{ t('terms.sections.contact.phone') }}</strong>
                        <a :href="`tel:${contactPhone}`">{{ contactPhone }}</a>
                      </div>
                    </div>
                    <div class="contact-method">
                      <i class="bi bi-clock"></i>
                      <div>
                        <strong>{{ t('terms.sections.contact.hours') }}</strong>
                        <span>{{ contactHours }}</span>
                      </div>
                    </div>
                    <div class="contact-method">
                      <i class="bi bi-geo-alt"></i>
                      <div>
                        <strong>{{ t('terms.sections.contact.address') }}</strong>
                        <span>{{ contactAddress }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

              <!-- Acceptance Footer -->
              <div class="acceptance-footer">
                <div class="acceptance-content">
                  <i class="bi bi-shield-check"></i>
                  <div>
                    <h3>{{ t('terms.acceptance.title') }}</h3>
                    <p>{{ t('terms.acceptance.content') }}</p>
                  </div>
                </div>
                <div class="acceptance-actions">
                  <button class="btn-success" @click="acceptTerms">
                    <i class="bi bi-check-lg"></i>
                    {{ t('terms.acceptance.accept') }}
                  </button>
                  <button class="btn-outline" @click="declineTerms">
                    {{ t('terms.acceptance.decline') }}
                  </button>
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
import { ref, computed, onMounted, onUnmounted, reactive } from 'vue'; // Add reactive here
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import Sidebar from './sidebar.vue';
import axios from "axios";

export default {
  name: 'TermsOfService',
  components: {
    Sidebar,
  },
  setup() {
    const { t } = useI18n();
    const router = useRouter();
    const termsContent = ref(null);
    const activeSection = ref('acceptance');
    const isExpanded = ref(false);
    const user = ref(null);

    const lastUpdated = new Date('2024-01-15');
    const version = '2.1.0';
    const readingTime = '10-15 minutes';

    const contactEmail = 'legal@fleetmanager.com';
    const contactPhone = '+1 (555) 123-4567';
    const contactHours = 'Monday - Friday, 9:00 AM - 6:00 PM EST';
    const contactAddress = '123 Business Ave, Suite 100, New York, NY 10001';

    const sections = [
      { id: 'acceptance', title: t('terms.sections.acceptance.title'), icon: 'bi-check-circle' },
      { id: 'description', title: t('terms.sections.description.title'), icon: 'bi-box-seam' },
      { id: 'accounts', title: t('terms.sections.accounts.title'), icon: 'bi-person' },
      { id: 'acceptable-use', title: t('terms.sections.acceptable.title'), icon: 'bi-exclamation-triangle' },
      { id: 'intellectual-property', title: t('terms.sections.intellectual.title'), icon: 'bi-c-circle' },
      { id: 'liability', title: t('terms.sections.liability.title'), icon: 'bi-shield' },
      { id: 'contact', title: t('terms.sections.contact.title'), icon: 'bi-envelope' },
    ];

    const serviceFeatures = computed(() => [
      t('terms.features.fleetManagement'),
      t('terms.features.maintenanceTracking'),
      t('terms.features.reporting'),
      t('terms.features.userManagement'),
      t('terms.features.dataAnalytics'),
      t('terms.features.mobileAccess'),
    ]);

    const accountRequirements = computed(() => [
      {
        title: t('terms.requirements.accuracy.title'),
        description: t('terms.requirements.accuracy.description')
      },
      {
        title: t('terms.requirements.security.title'),
        description: t('terms.requirements.security.description')
      },
      {
        title: t('terms.requirements.compliance.title'),
        description: t('terms.requirements.compliance.description')
      },
      {
        title: t('terms.requirements.notification.title'),
        description: t('terms.requirements.notification.description')
      }
    ]);

    const prohibitedActivities = computed(() => [
      {
        title: t('terms.prohibited.unauthorized.title'),
        description: t('terms.prohibited.unauthorized.description')
      },
      {
        title: t('terms.prohibited.misuse.title'),
        description: t('terms.prohibited.misuse.description')
      },
      {
        title: t('terms.prohibited.harmful.title'),
        description: t('terms.prohibited.harmful.description')
      },
      {
        title: t('terms.prohibited.commercial.title'),
        description: t('terms.prohibited.commercial.description')
      }
    ]);

    const scrollToSection = (sectionId) => {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
        activeSection.value = sectionId;
      }
    };

    const handleScroll = () => {
      if (!termsContent.value) return;

      const sections = termsContent.value.querySelectorAll('.terms-section');
      const scrollPosition = window.scrollY + 100;

      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
          activeSection.value = section.id;
        }
      });
    };

    const theme = reactive({
      isDark: false
    });
    
    const downloadTerms = () => {
      // Implementation for PDF download
      console.log('Downloading terms as PDF...');
      // You would typically generate or fetch a PDF here
    };

    const printTerms = () => {
      window.print();
    };

    const acceptTerms = () => {
      // Implementation for accepting terms
      console.log('Terms accepted');
      // Store acceptance in user profile/localStorage
      localStorage.setItem('termsAccepted', 'true');
      router.push('/dashboard');
    };

    const declineTerms = () => {
      // Implementation for declining terms
      console.log('Terms declined');
      router.push('/');
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const getUser = async () => {
      try {
        const token = localStorage.getItem("token");
        if (!token) {
          router.push("/login");
          return;
        }

        const res = await axios.get("http://127.0.0.1:8000/api/me", {
          headers: { Authorization: `Bearer ${token}` },
        });

        user.value = res.data;
      } catch (error) {
        console.error("Error fetching user:", error);
        router.push("/login");
      }
    };

    onMounted(() => {
      window.addEventListener('scroll', handleScroll);
      getUser();
    });

    const menuItems = computed(() => [
      { label: t('menu.dashboard'), to: '/dashboard' },
      { label: t('menu.vehicles'), to: '/vehicles' },
      { label: t('menu.interventions'), to: '/interventions' },
      { label: t('menu.alerts'), to: '/alerts' },
      { label: t('menu.reports'), to: '/reports' },
      { label: t('menu.users'), to: '/users' },
    ]);

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll);
    });

    return {
      t,
      termsContent,
      activeSection,
      isExpanded,
      user,
      theme,
      lastUpdated,
      version,
      readingTime,
      sections,
      serviceFeatures,
      accountRequirements,
      prohibitedActivities,
      contactEmail,
      contactPhone,
      contactHours,
      contactAddress,
      scrollToSection,
      downloadTerms,
      printTerms,
      acceptTerms,
      declineTerms,
      formatDate,
      menuItems,
    };
  },
};
</script>

