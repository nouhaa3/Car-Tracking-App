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
        <div class="logo">
          <i class="bi bi-car-front-fill"></i>
          <span>{{ t('home.brandName') }}</span>
        </div>
        <div class="header-actions">
          <button class="btn-header-secondary" @click="$router.push('/login')">
            {{ t('auth.login') }}
          </button>
          <button class="btn-header-primary" @click="$router.push('/register')">
            {{ t('home.getStarted') }}
          </button>
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

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-left">
        <div class="hero-badge">
          <i class="bi bi-star-fill"></i>
          <span>{{ t('home.hero.badge') }}</span>
        </div>
        <h1 class="hero-title">
          {{ t('home.hero.title') }}
        </h1>
        <p class="hero-description">
          {{ t('home.hero.description') }}
        </p>
      </div>
      <div class="hero-right">
        <div class="hero-cta">
          <button class="btn-cta-primary" @click="$router.push('/register')">
            {{ t('home.hero.startFree') }}
            <i class="bi bi-arrow-right"></i>
          </button>
          <button class="btn-cta-secondary" @click="scrollToSection('contact')">
            {{ t('home.hero.requestDemo') }}
            <i class="bi bi-play-circle"></i>
          </button>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
            <div class="stat-label">{{ t('home.stats.vehiclesManaged') }}</div>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div class="stat-label">{{ t('home.stats.companies') }}</div>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">{{ t('home.stats.availability') }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section-modern">
      <div class="section-header">
        <span class="section-badge">
          <i class="bi bi-star-fill"></i>
          {{ t('home.features.title') }}
        </span>
        <h2 class="section-title-modern">{{ t('home.features.subtitle') }}</h2>
        <p class="section-description">
          {{ t('home.features.description') }}
        </p>
      </div>
      <div class="features-grid-modern">
        <div class="feature-card-modern">
          <div class="feature-icon" style="background: #E3F2FD;">
            <i class="bi bi-clipboard-data" style="color: #1976D2;"></i>
          </div>
          <h3>{{ t('home.features.items.realTimeTracking.title') }}</h3>
          <p>{{ t('home.features.items.realTimeTracking.description') }}</p>
        </div>
        <div class="feature-card-modern">
          <div class="feature-icon" style="background: #F3E5F5;">
            <i class="bi bi-tools" style="color: #7B1FA2;"></i>
          </div>
          <h3>{{ t('home.features.items.preventiveMaintenance.title') }}</h3>
          <p>{{ t('home.features.items.preventiveMaintenance.description') }}</p>
        </div>
        <div class="feature-card-modern">
          <div class="feature-icon" style="background: #FFF9C4;">
            <i class="bi bi-file-earmark-text" style="color: #F57F17;"></i>
          </div>
          <h3>{{ t('home.features.items.administrativeManagement.title') }}</h3>
          <p>{{ t('home.features.items.administrativeManagement.description') }}</p>
        </div>
        <div class="feature-card-modern">
          <div class="feature-icon" style="background: #FFF3E0;">
            <i class="bi bi-graph-up" style="color: #F57C00;"></i>
          </div>
          <h3>{{ t('home.features.items.detailedReports.title') }}</h3>
          <p>{{ t('home.features.items.detailedReports.description') }}</p>
        </div>
        <div class="feature-card-modern">
          <div class="feature-icon" style="background: #E8F5E9;">
            <i class="bi bi-shield-check" style="color: #388E3C;"></i>
          </div>
          <h3>{{ t('home.features.items.enhancedSecurity.title') }}</h3>
          <p>{{ t('home.features.items.enhancedSecurity.description') }}</p>
        </div>
        <div class="feature-card-modern">
          <div class="feature-icon" style="background: #E0F2F1;">
            <i class="bi bi-phone" style="color: #00796B;"></i>
          </div>
          <h3>{{ t('home.features.items.mobileApp.title') }}</h3>
          <p>{{ t('home.features.items.mobileApp.description') }}</p>
        </div>
      </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="benefits-section">
      <div class="section-header">
        <span class="section-badge">
          <i class="bi bi-star-fill"></i>
          {{ t('home.benefits.title') }}
        </span>
        <h2 class="section-title-modern">{{ t('home.benefits.subtitle') }}</h2>
        <p class="section-description">
          {{ t('home.benefits.description') }}
        </p>
      </div>
      <div class="benefits-grid">
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <div class="benefit-icon" style="background: #E3F2FD;">
              <i class="bi bi-clock-history" style="color: #1976D2;"></i>
            </div>
          </div>
          <h3>{{ t('home.benefits.items.timeSavings.title') }}</h3>
          <p>{{ t('home.benefits.items.timeSavings.description') }}</p>
        </div>
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <div class="benefit-icon" style="background: #E8F5E9;">
              <i class="bi bi-piggy-bank" style="color: #388E3C;"></i>
            </div>
          </div>
          <h3>{{ t('home.benefits.items.costReduction.title') }}</h3>
          <p>{{ t('home.benefits.items.costReduction.description') }}</p>
        </div>
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <div class="benefit-icon" style="background: #FFF3E0;">
              <i class="bi bi-lightbulb" style="color: #F57C00;"></i>
            </div>
          </div>
          <h3>{{ t('home.benefits.items.informedDecisions.title') }}</h3>
          <p>{{ t('home.benefits.items.informedDecisions.description') }}</p>
        </div>
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <div class="benefit-icon" style="background: #F3E5F5;">
              <i class="bi bi-headset" style="color: #7B1FA2;"></i>
            </div>
          </div>
          <h3>{{ t('home.benefits.items.support247.title') }}</h3>
          <p>{{ t('home.benefits.items.support247.description') }}</p>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section-modern">
      <div class="contact-container-modern">
        <div class="contact-header">
          <span class="section-badge">
            <i class="bi bi-star-fill"></i>
            {{ t('home.contact.title') }}
          </span>
          <h2 class="section-title-modern">{{ t('home.contact.subtitle') }}</h2>
          <p class="section-description">
            {{ t('home.contact.description') }}
          </p>
        </div>
        <div class="contact-content-modern">
          <form class="contact-form-modern">
            <div class="form-row-modern">
              <div class="form-group-modern">
                <label>{{ t('home.contact.form.fullName') }}</label>
                <input type="text" name="nom" :placeholder="t('home.contact.form.fullNamePlaceholder')" class="input-modern" required />
              </div>
              <div class="form-group-modern">
                <label>{{ t('home.contact.form.email') }}</label>
                <input type="email" name="email" :placeholder="t('home.contact.form.emailPlaceholder')" class="input-modern" required />
              </div>
            </div>
            <div class="form-row-modern">
              <div class="form-group-modern">
                <label>{{ t('home.contact.form.phone') }}</label>
                <input type="text" name="phone" :placeholder="t('home.contact.form.phonePlaceholder')" class="input-modern" />
              </div>
              <div class="form-group-modern">
                <label>{{ t('home.contact.form.company') }}</label>
                <input type="text" name="company" :placeholder="t('home.contact.form.companyPlaceholder')" class="input-modern" />
              </div>
            </div>
            <div class="form-group-modern full-width">
              <label>{{ t('home.contact.form.message') }}</label>
              <textarea name="message" :placeholder="t('home.contact.form.messagePlaceholder')" class="textarea-modern" rows="5" required></textarea>
            </div>
            <div class="captcha-modern">
              <div class="g-recaptcha" data-sitekey="6LcmDesrAAAAABYomrU6uWTydYDNuYXHAOHWL9Mf"></div>
            </div>
            <button type="submit" class="btn-submit-modern">
              {{ t('home.contact.sendMessage') }}
              <i class="bi bi-send"></i>
            </button>
          </form>
          <div class="contact-info-modern">
            <div class="info-card-modern">
              <div class="info-icon-modern">
                <i class="bi bi-envelope"></i>
              </div>
              <h4>{{ t('home.contact.info.email') }}</h4>
              <p>{{ t('home.contact.info.emailValue') }}</p>
            </div>
            <div class="info-card-modern">
              <div class="info-icon-modern">
                <i class="bi bi-telephone"></i>
              </div>
              <h4>{{ t('home.contact.info.phone') }}</h4>
              <p>{{ t('home.contact.info.phoneValue') }}</p>
            </div>
            <div class="info-card-modern">
              <div class="info-icon-modern">
                <i class="bi bi-geo-alt"></i>
              </div>
              <h4>{{ t('home.contact.info.address') }}</h4>
              <p>{{ t('home.contact.info.addressValue') }}</p>
            </div>
          </div>
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
            <a href="#features" @click.prevent="scrollToSection('features')">
              {{ t('home.footer.features') }}
            </a>
            <a href="#benefits" @click.prevent="scrollToSection('benefits')">
              {{ t('home.nav.benefits') }}
            </a>
            <a href="#contact" @click.prevent="scrollToSection('contact')">
              {{ t('home.footer.demo') }}
            </a>
            <a href="/register" @click.prevent="$router.push('/register')">
              {{ t('home.getStarted') }}
            </a>
          </div>
          <div class="footer-column">
            <h4>{{ t('home.footer.company') }}</h4>
            <a href="/about" @click.prevent="$router.push('/about')">
              {{ t('home.footer.about') }}
            </a>
            <a href="/faq" @click.prevent="$router.push('/faq')">
              {{ t('faq.title') }}
            </a>
            <a href="/#contact" @click.prevent="scrollToSection('contact')">
              {{ t('home.nav.contact') }}
            </a>
            <a href="/login" @click.prevent="$router.push('/login')">
              {{ t('auth.login') }}
            </a>
            <a href="/register" @click.prevent="$router.push('/register')">
              {{ t('auth.register') }}
            </a>
          </div>
          <div class="footer-column">
            <h4>{{ t('home.nav.contact') }}</h4>
            <a href="mailto:contact@cartrackingapp.com">
              <i class="bi bi-envelope"></i>
              {{ t('home.contact.info.emailValue') }}
            </a>
            <a href="tel:+21611111111">
              <i class="bi bi-telephone"></i>
              {{ t('home.contact.info.phoneValue') }}
            </a>
            <a href="#contact" @click.prevent="scrollToSection('contact')">
              <i class="bi bi-geo-alt"></i>
              {{ t('home.contact.info.addressValue') }}
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
import alerts from '@/utils/alerts';

export default {
  name: "HomePage",
  setup() {
    const { t, locale } = useI18n();
    const showLanguageMenu = ref(false);
    
    const currentLocale = computed(() => locale.value);
    
    const getCurrentCountryCode = () => {
      const codes = {
        fr: 'fr',
        en: 'gb',
        ar: 'sa'
      };
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
      
      // Save to localStorage for persistence
      const savedSettings = localStorage.getItem('appSettings');
      const settings = savedSettings ? JSON.parse(savedSettings) : {};
      settings.language = lang;
      localStorage.setItem('appSettings', JSON.stringify(settings));
      
      // Update document direction for RTL languages
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
    scrollToSection(sectionId) {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    },
    initContactForm() {
      const form = document.querySelector('.contact-form-modern');
      if (!form) return;
      
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        try {
          const res = await fetch('http://127.0.0.1:8000/api/messages', {
            method: 'POST',
            body: formData,
          });

          const data = await res.json();
          if (data.status === 'success') {
            await alerts.alertSuccess(this.t('common.success'), this.t('home.contact.successMessage'));
            form.reset();
            if(window.grecaptcha) grecaptcha.reset();
          } else {
            await alerts.alertError(this.t('common.error'), data.message || this.t('errors.generic'));
          }
        } catch (err) {
          console.error(err);
          await alerts.alertError(this.t('common.error'), this.t('errors.serverError'));
        }
      });
    }
  },
  mounted() {
    // Load reCAPTCHA script
    const existing = document.querySelector('script[src="https://www.google.com/recaptcha/api.js"]');
    if (!existing) {
      const recaptchaScript = document.createElement('script');
      recaptchaScript.src = "https://www.google.com/recaptcha/api.js";
      recaptchaScript.async = true;
      recaptchaScript.defer = true;
      recaptchaScript.onload = () => {
        this.initContactForm();
      };
      document.body.appendChild(recaptchaScript);
    } else {
      this.initContactForm();
    }

    // Smooth scroll for navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  }
};
</script>