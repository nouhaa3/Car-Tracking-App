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


