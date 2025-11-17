<template>
  <div class="modern-home-page">
    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <!-- Navigation Header -->
    <header class="home-header">
      <div class="header-content">
        <div class="logo" @click="navigateToDashboard">
          <i class="bi bi-car-front-fill"></i>
          <span>{{ t('home.brandName') }}</span>
        </div>
        <nav class="navbar">
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
        <div class="header-actions">
          <!-- Language Selector -->
          <div class="language-selector" @click="toggleLanguageMenu" v-click-outside="closeLanguageMenu">
            <button class="language-btn">
              <span :class="['fi', `fi-${getCurrentCountryCode()}`]"></span>
              <i class="bi bi-chevron-down"></i>
            </button>
            <div class="language-dropdown" v-if="showLanguageMenu">
              <div 
                class="language-option" 
                :class="{ active: currentLocale === 'fr' }"
                @click="changeLanguage('fr')"
              >
                <span class="fi fi-fr"></span>
                <span>Français</span>
              </div>
              <div 
                class="language-option" 
                :class="{ active: currentLocale === 'en' }"
                @click="changeLanguage('en')"
              >
                <span class="fi fi-gb"></span>
                <span>English</span>
              </div>
              <div 
                class="language-option" 
                :class="{ active: currentLocale === 'ar' }"
                @click="changeLanguage('ar')"
              >
                <span class="fi fi-sa"></span>
                <span>العربية</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero About -->
    <section class="about-hero">
      <div class="about-hero-content">
        <div class="hero-badge">
          <i class="bi bi-star-fill"></i>
          <span>{{ t('about.hero.badge') }}</span>
        </div>
        <h1 class="hero-title">{{ t('about.hero.title') }}</h1>
        <p class="hero-description">{{ t('about.hero.description') }}</p>
      </div>
    </section>

    <!-- Mission Section -->
    <section id="mission" class="about-section">
      <div class="section-header">
        <span class="section-badge">
          <i class="bi bi-star-fill"></i>
          {{ t('about.mission.badge') }}
        </span>
        <h2 class="section-title-modern">{{ t('about.mission.title') }}</h2>
        <p class="section-description">{{ t('about.mission.description') }}</p>
      </div>
      
      <div class="mission-grid">
        <div class="mission-card">
          <div class="mission-icon" style="background: #E3F2FD;">
            <i class="bi bi-bullseye" style="color: #1976D2;"></i>
          </div>
          <h3>{{ t('about.mission.items.vision.title') }}</h3>
          <p>{{ t('about.mission.items.vision.text') }}</p>
        </div>
        <div class="mission-card">
          <div class="mission-icon" style="background: #E8F5E9;">
            <i class="bi bi-heart-fill" style="color: #388E3C;"></i>
          </div>
          <h3>{{ t('about.mission.items.values.title') }}</h3>
          <p>{{ t('about.mission.items.values.text') }}</p>
        </div>
        <div class="mission-card">
          <div class="mission-icon" style="background: #FFF3E0;">
            <i class="bi bi-rocket-takeoff" style="color: #F57C00;"></i>
          </div>
          <h3>{{ t('about.mission.items.innovation.title') }}</h3>
          <p>{{ t('about.mission.items.innovation.text') }}</p>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="stats-section">
      <div class="section-header">
        <span class="section-badge">
          <i class="bi bi-star-fill"></i>
          {{ t('about.stats.badge') }}
        </span>
        <h2 class="section-title-modern">{{ t('about.stats.title') }}</h2>
      </div>
      
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-number">500+</div>
          <div class="stat-label">{{ t('about.stats.vehicles') }}</div>
          <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
        </div>
        <div class="stat-card">
          <div class="stat-number">100+</div>
          <div class="stat-label">{{ t('about.stats.companies') }}</div>
          <div class="stat-icon"><i class="bi bi-building"></i></div>
        </div>
        <div class="stat-card">
          <div class="stat-number">99.8%</div>
          <div class="stat-label">{{ t('about.stats.availability') }}</div>
          <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
        </div>
        <div class="stat-card">
          <div class="stat-number">24/7</div>
          <div class="stat-label">{{ t('about.stats.support') }}</div>
          <div class="stat-icon"><i class="bi bi-headset"></i></div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="team-section">
      <div class="section-header">
        <span class="section-badge">
          <i class="bi bi-star-fill"></i>
          {{ t('about.team.badge') }}
        </span>
        <h2 class="section-title-modern">{{ t('about.team.title') }}</h2>
        <p class="section-description">{{ t('about.team.description') }}</p>
      </div>
      
      <div class="team-grid">
        <div class="team-member">
          <div class="member-avatar">
            <i class="bi bi-person-circle"></i>
          </div>
          <h3>{{ t('about.team.members.ceo.name') }}</h3>
          <p class="member-role">{{ t('about.team.members.ceo.role') }}</p>
          <p class="member-bio">{{ t('about.team.members.ceo.bio') }}</p>
          <div class="member-social">
            <a href="#" target="_blank"><i class="bi bi-linkedin"></i></a>
            <a href="#" target="_blank"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
        <div class="team-member">
          <div class="member-avatar">
            <i class="bi bi-person-circle"></i>
          </div>
          <h3>{{ t('about.team.members.cto.name') }}</h3>
          <p class="member-role">{{ t('about.team.members.cto.role') }}</p>
          <p class="member-bio">{{ t('about.team.members.cto.bio') }}</p>
          <div class="member-social">
            <a href="#" target="_blank"><i class="bi bi-linkedin"></i></a>
            <a href="#" target="_blank"><i class="bi bi-github"></i></a>
          </div>
        </div>
        <div class="team-member">
          <div class="member-avatar">
            <i class="bi bi-person-circle"></i>
          </div>
          <h3>{{ t('about.team.members.lead.name') }}</h3>
          <p class="member-role">{{ t('about.team.members.lead.role') }}</p>
          <p class="member-bio">{{ t('about.team.members.lead.bio') }}</p>
          <div class="member-social">
            <a href="#" target="_blank"><i class="bi bi-linkedin"></i></a>
            <a href="#" target="_blank"><i class="bi bi-twitter"></i></a>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-content">
        <h2>{{ t('about.cta.title') }}</h2>
        <p>{{ t('about.cta.description') }}</p>
        <div class="cta-buttons">
          <button class="btn-cta-primary" @click="$router.push('/register')">
            {{ t('home.hero.startFree') }}
            <i class="bi bi-arrow-right"></i>
          </button>
          <button class="btn-cta-secondary" @click="$router.push('/#contact')">
            {{ t('home.hero.requestDemo') }}
            <i class="bi bi-play-circle"></i>
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
      <div class="footer-content">
        <div class="footer-brand">
          <div class="footer-logo">
            <i class="bi bi-car-front-fill"></i>
            <span>{{ t('home.brandName') }}</span>
          </div>
          <p>{{ t('home.footer.tagline') }}</p>
          <div class="footer-social">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" title="Facebook">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" title="Twitter">
              <i class="bi bi-twitter"></i>
            </a>
            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" title="LinkedIn">
              <i class="bi bi-linkedin"></i>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" title="Instagram">
              <i class="bi bi-instagram"></i>
            </a>
          </div>
        </div>
        <div class="footer-links">
          <div class="footer-column">
            <h4>{{ t('home.footer.product') }}</h4>
            <a href="/" @click.prevent="$router.push('/')">{{ t('home.nav.home') }}</a>
            <a href="/about" @click.prevent="$router.push('/about')">{{ t('home.footer.about') }}</a>
            <a href="/faq" @click.prevent="$router.push('/faq')">{{ t('about.nav.faq') }}</a>
          </div>
          <div class="footer-column">
            <h4>{{ t('home.footer.company') }}</h4>
            <a href="/login" @click.prevent="$router.push('/login')">{{ t('auth.login') }}</a>
            <a href="/register" @click.prevent="$router.push('/register')">{{ t('auth.register') }}</a>
          </div>
          <div class="footer-column">
            <h4>{{ t('home.nav.contact') }}</h4>
            <a href="mailto:contact@cartrackingapp.com">
              <i class="bi bi-envelope"></i>
              contact@cartrackingapp.com
            </a>
            <a href="tel:+21611111111">
              <i class="bi bi-telephone"></i>
              +216 11 111 111
            </a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 {{ t('home.brandName') }}. {{ t('home.footer.rights') }}</p>
      </div>
    </footer>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';
import { getDashboardRoute } from '../utils/navigation';

export default {
  name: "AboutPage",
  setup() {
    const { t, locale } = useI18n();
    const showLanguageMenu = ref(false);
    
    const currentLocale = computed(() => locale.value);
    
    const getCurrentCountryCode = () => {
      const codes = { fr: 'fr', en: 'gb', ar: 'sa' };
      return codes[locale.value] || 'fr';
    };
    
    const toggleLanguageMenu = () => {
      showLanguageMenu.value = !showLanguageMenu.value;
    };
    
    const closeLanguageMenu = () => {
      showLanguageMenu.value = false;
    };
    
    const changeLanguage = (lang) => {
      locale.value = lang;
      const savedSettings = localStorage.getItem('appSettings');
      const settings = savedSettings ? JSON.parse(savedSettings) : {};
      settings.language = lang;
      localStorage.setItem('appSettings', JSON.stringify(settings));
      
      if (lang === 'ar') {
        document.documentElement.setAttribute('dir', 'rtl');
      } else {
        document.documentElement.setAttribute('dir', 'ltr');
      }
      
      showLanguageMenu.value = false;
    };
    
    return { 
      t, 
      currentLocale,
      showLanguageMenu,
      getCurrentCountryCode, 
      toggleLanguageMenu,
      closeLanguageMenu,
      changeLanguage
    };
  },
  computed: {
    menuItems() {
      return [
        { label: this.t('nav.dashboard'), to: getDashboardRoute() },
        { label: this.t('nav.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('nav.interventions'), to: "/interventions/catalogue" },
        { label: this.t('nav.alerts'), to: "/alertes" },
      ];
    }
  },
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value(event);
          }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  },
  methods: {
    navigateToDashboard() {
      this.$router.push(getDashboardRoute());
    },
    scrollToSection(sectionId) {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }
  }
};
</script>

<style scoped>
/* Navbar Styles */
.navbar {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  margin: 0;
  padding: 0;
}

.nav-link {
  padding: 0.625rem 1.25rem;
  color: #344966;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.9375rem;
  border-radius: 8px;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.nav-link:hover {
  background-color: #E8F2F8;
  color: #0D1821;
}

.nav-link.active {
  background-color: #344966;
  color: white;
}

.nav-link.active::after {
  opacity: 1;
}

/* About Hero Section */
.about-hero {
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  padding: 8rem 2rem 6rem;
  text-align: center;
  color: white;
  position: relative;
  overflow: hidden;
}

.about-hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: 0.4;
}

.about-hero-content {
  max-width: 800px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  padding: 0.625rem 1.25rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 2rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-badge i {
  color: #BFCC94;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.hero-description {
  font-size: 1.25rem;
  opacity: 0.95;
  line-height: 1.8;
}

/* Sections */
.about-section {
  background: #FFFFFF;
  padding: 6rem 2rem;
}

.stats-section {
  background: #F8FAFB;
  padding: 6rem 2rem;
}

.team-section {
  background: #FFFFFF;
  padding: 6rem 2rem;
}

/* Section Headers */
.section-header {
  text-align: center;
  max-width: 800px;
  margin: 0 auto 4rem;
}

.section-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: #E8F2F8;
  color: #344966;
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.section-badge i {
  color: #BFCC94;
}

.section-title-modern {
  font-size: 2.5rem;
  font-weight: 800;
  color: #0D1821;
  margin-bottom: 1rem;
}

.section-description {
  font-size: 1.125rem;
  color: #546A88;
  line-height: 1.8;
}

/* Mission Grid */
.mission-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  max-width: 1280px;
  margin: 0 auto;
}

.mission-card {
  background: white;
  padding: 2.5rem;
  border-radius: 10px;
  text-align: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #E8F2F8;
  box-shadow: 0 2px 8px rgba(52, 73, 102, 0.08);
}

.mission-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(52, 73, 102, 0.15);
  border-color: #D2E1EE;
}

.mission-icon {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  font-size: 2rem;
  transition: all 0.3s ease;
}

.mission-card:hover .mission-icon {
  transform: scale(1.1);
}

.mission-card h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0D1821;
  margin-bottom: 1rem;
}

.mission-card p {
  color: #546A88;
  line-height: 1.7;
  font-size: 1rem;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  max-width: 1280px;
  margin: 0 auto;
}

.stat-card {
  background: white;
  padding: 3rem 2rem;
  border-radius: 10px;
  text-align: center;
  position: relative;
  box-shadow: 0 2px 8px rgba(52, 73, 102, 0.1);
  border: 1px solid #E8F2F8;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.stat-card:hover::before {
  transform: scaleX(1);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(52, 73, 102, 0.15);
}

.stat-number {
  font-size: 3rem;
  font-weight: 800;
  color: #344966;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1rem;
  color: #546A88;
  font-weight: 600;
}

.stat-icon {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  font-size: 2rem;
  color: #D2E1EE;
  opacity: 0.5;
}

/* Team Grid */
.team-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 3rem;
  max-width: 1280px;
  margin: 0 auto;
}

.team-member {
  text-align: center;
  padding: 2rem;
  border-radius: 10px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.team-member:hover {
  background: #F8FAFB;
  transform: translateY(-4px);
}

.member-avatar {
  width: 150px;
  height: 150px;
  margin: 0 auto 1.5rem;
  border-radius: 50%;
  background: linear-gradient(135deg, #E8F2F8 0%, #D2E1EE 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 5rem;
  color: #344966;
  transition: all 0.3s ease;
  border: 3px solid white;
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.15);
}

.team-member:hover .member-avatar {
  transform: scale(1.05);
  box-shadow: 0 8px 24px rgba(52, 73, 102, 0.2);
}

.team-member h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #0D1821;
  margin-bottom: 0.5rem;
}

.member-role {
  color: #344966;
  font-weight: 600;
  margin-bottom: 1rem;
  font-size: 1rem;
}

.member-bio {
  color: #546A88;
  line-height: 1.7;
  margin-bottom: 1.5rem;
  font-size: 0.9375rem;
}

.member-social {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
}

.member-social a {
  width: 40px;
  height: 40px;
  background: #E8F2F8;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #344966;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 1.125rem;
}

.member-social a:hover {
  background: #344966;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.25);
}

/* CTA Section */
.cta-section {
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  padding: 6rem 2rem;
  text-align: center;
  color: white;
  position: relative;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  opacity: 0.4;
}

.cta-content {
  max-width: 800px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

.cta-content h2 {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 1rem;
  line-height: 1.3;
}

.cta-content p {
  font-size: 1.25rem;
  margin-bottom: 2.5rem;
  opacity: 0.95;
  line-height: 1.7;
}

.cta-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-cta-primary,
.btn-cta-secondary {
  padding: 0.875rem 1.75rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
}

.btn-cta-primary {
  background: white;
  color: #344966;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.btn-cta-primary:hover {
  background: #f8f9fa;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.btn-cta-secondary {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-cta-secondary:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .mission-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .team-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .about-hero {
    padding: 6rem 1.5rem 4rem;
  }
  
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-description {
    font-size: 1.125rem;
  }
  
  .section-title-modern {
    font-size: 2rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .cta-content h2 {
    font-size: 2rem;
  }
  
  .cta-buttons {
    flex-direction: column;
    align-items: stretch;
  }
  
  .btn-cta-primary,
  .btn-cta-secondary {
    width: 100%;
    justify-content: center;
  }
}
</style>
