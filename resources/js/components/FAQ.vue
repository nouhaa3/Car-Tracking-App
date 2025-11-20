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

    <!-- Hero FAQ -->
    <section class="faq-hero">
      <div class="faq-hero-content">
        <div class="hero-badge">
          <i class="bi bi-star-fill"></i>
          <span>{{ t('faq.hero.badge') }}</span>
        </div>
        <h1 class="hero-title">{{ t('faq.title') }}</h1>
        <p class="hero-description">{{ t('faq.subtitle') }}</p>
        
        <!-- Search Box -->
        <div class="faq-search">
          <i class="bi bi-search"></i>
          <input 
            type="text" 
            v-model="searchQuery"
            :placeholder="t('faq.searchPlaceholder')" 
          />
        </div>
      </div>
    </section>

    <!-- FAQ Categories -->
    <section class="faq-categories">
      <div class="categories-container">
        <button 
          v-for="(category, index) in categories" 
          :key="index"
          @click="activeCategory = category.id"
          :class="['category-btn', { active: activeCategory === category.id }]"
        >
          <i :class="category.icon"></i>
          {{ t(category.label) }}
        </button>
      </div>
    </section>

    <!-- FAQ Content -->
    <section class="faq-section">
      <div class="faq-container">
        <div 
          v-for="(item, index) in filteredFaqItems" 
          :key="index"
          class="faq-item"
        >
          <button 
            @click="toggleFaq(index)"
            class="faq-question"
          >
            <div class="question-content">
              <i :class="['bi', item.icon]"></i>
              <span>{{ t(item.question) }}</span>
            </div>
            <i :class="['bi', 'chevron-icon', expanded[index] ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
          </button>
          <transition name="slide">
            <div v-if="expanded[index]" class="faq-answer">
              <p>{{ t(item.answer) }}</p>
            </div>
          </transition>
        </div>
      </div>
    </section>

    <!-- Contact CTA -->
    <section class="faq-cta">
      <div class="cta-content">
        <i class="bi bi-question-circle"></i>
        <h2>{{ t('faq.cta.title') }}</h2>
        <p>{{ t('faq.cta.description') }}</p>
        <button class="btn-cta-primary" @click="$router.push('/#contact')">
          {{ t('faq.cta.button') }}
          <i class="bi bi-arrow-right"></i>
        </button>
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
            <a href="/faq" @click.prevent="$router.push('/faq')">{{ t('faq.title') }}</a>
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
  name: "FAQPage",
  setup() {
    const { t, locale } = useI18n();
    const showLanguageMenu = ref(false);
    const searchQuery = ref('');
    const activeCategory = ref('all');
    const expanded = ref({});
    
    const currentLocale = computed(() => locale.value);
    
    const categories = [
      { id: 'all', label: 'faq.categories.all', icon: 'bi bi-grid' },
      { id: 'general', label: 'faq.categories.general', icon: 'bi bi-info-circle' },
      { id: 'vehicles', label: 'faq.categories.vehicles', icon: 'bi bi-car-front' },
      { id: 'interventions', label: 'faq.categories.interventions', icon: 'bi bi-tools' },
      { id: 'reports', label: 'faq.categories.reports', icon: 'bi bi-file-earmark-text' },
      { id: 'account', label: 'faq.categories.account', icon: 'bi bi-person' },
    ];
    
    const faqItems = [
      // General
      { category: 'general', icon: 'bi bi-info-circle', question: 'faq.items.q1', answer: 'faq.items.a1' },
      { category: 'general', icon: 'bi bi-gear', question: 'faq.items.q2', answer: 'faq.items.a2' },
      { category: 'general', icon: 'bi bi-shield-check', question: 'faq.items.q3', answer: 'faq.items.a3' },
      
      // Vehicles
      { category: 'vehicles', icon: 'bi bi-plus-circle', question: 'faq.items.q4', answer: 'faq.items.a4' },
      { category: 'vehicles', icon: 'bi bi-pencil', question: 'faq.items.q5', answer: 'faq.items.a5' },
      { category: 'vehicles', icon: 'bi bi-trash', question: 'faq.items.q6', answer: 'faq.items.a6' },
      
      // Interventions
      { category: 'interventions', icon: 'bi bi-clipboard-plus', question: 'faq.items.q7', answer: 'faq.items.a7' },
      { category: 'interventions', icon: 'bi bi-calendar-check', question: 'faq.items.q8', answer: 'faq.items.a8' },
      { category: 'interventions', icon: 'bi bi-currency-euro', question: 'faq.items.q9', answer: 'faq.items.a9' },
      
      // Reports
      { category: 'reports', icon: 'bi bi-file-pdf', question: 'faq.items.q10', answer: 'faq.items.a10' },
      { category: 'reports', icon: 'bi bi-download', question: 'faq.items.q11', answer: 'faq.items.a11' },
      
      // Account
      { category: 'account', icon: 'bi bi-key', question: 'faq.items.q12', answer: 'faq.items.a12' },
      { category: 'account', icon: 'bi bi-people', question: 'faq.items.q13', answer: 'faq.items.a13' },
    ];
    
    const filteredFaqItems = computed(() => {
      let items = faqItems;
      
      // Filter by category
      if (activeCategory.value !== 'all') {
        items = items.filter(item => item.category === activeCategory.value);
      }
      
      // Filter by search
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        items = items.filter(item => 
          t(item.question).toLowerCase().includes(query) ||
          t(item.answer).toLowerCase().includes(query)
        );
      }
      
      return items;
    });
    
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
    
    const toggleFaq = (index) => {
      expanded.value[index] = !expanded.value[index];
    };
    
    return { 
      t, 
      currentLocale,
      showLanguageMenu,
      searchQuery,
      activeCategory,
      expanded,
      categories,
      filteredFaqItems,
      getCurrentCountryCode, 
      toggleLanguageMenu,
      closeLanguageMenu,
      changeLanguage,
      toggleFaq
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
  methods: {
    navigateToDashboard() {
      this.$router.push(getDashboardRoute());
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
  }
};
</script>


