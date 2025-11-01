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
        <nav class="header-nav">
          <a href="#features" class="nav-item">{{ t('home.nav.features') }}</a>
          <a href="#benefits" class="nav-item">{{ t('home.nav.benefits') }}</a>
          <a href="#contact" class="nav-item">{{ t('home.nav.contact') }}</a>
        </nav>
        <div class="header-actions">
          <button class="btn-header-secondary" @click="$router.push('/login')">
            {{ t('auth.login') }}
          </button>
          <button class="btn-header-primary" @click="$router.push('/register')">
            {{ t('home.getStarted') }}
          </button>
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
          <button class="btn-cta-secondary" @click="scrollToSection('features')">
            {{ t('home.hero.learnMore') }}
            <i class="bi bi-play-circle"></i>
          </button>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-number">500+</div>
            <div class="stat-label">{{ t('home.stats.companies') }}</div>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <div class="stat-number">10K+</div>
            <div class="stat-label">{{ t('home.stats.vehiclesManaged') }}</div>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <div class="stat-number">99.9%</div>
            <div class="stat-label">{{ t('home.stats.availability') }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section-modern">
      <div class="section-header">
        <span class="section-badge">{{ t('home.features.title') }}</span>
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
          <div class="feature-icon" style="background: #FCE4EC;">
            <i class="bi bi-people" style="color: #C2185B;"></i>
          </div>
          <h3>{{ t('home.features.items.teamManagement.title') }}</h3>
          <p>{{ t('home.features.items.teamManagement.description') }}</p>
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
        <span class="section-badge">{{ t('home.benefits.title') }}</span>
        <h2 class="section-title-modern">{{ t('home.benefits.subtitle') }}</h2>
        <p class="section-description">
          {{ t('home.benefits.description') }}
        </p>
      </div>
      <div class="benefits-grid">
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
            <div class="benefit-icon" style="background: #E3F2FD;">
              <i class="bi bi-clock-history" style="color: #1976D2;"></i>
            </div>
          </div>
          <h3>{{ t('home.benefits.items.timeSaving.title') }}</h3>
          <p>{{ t('home.benefits.items.timeSaving.description') }}</p>
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
          <span class="section-badge">{{ t('home.contact.title') }}</span>
          <h2 class="section-title-modern">{{ t('home.contact.subtitle') }}</h2>
          <p class="section-description">
            {{ t('home.contact.description') }}
          </p>
        </div>
        <div class="contact-content-modern">
          <form class="contact-form-modern">
            <div class="form-row-modern">
              <div class="form-group-modern">
                <label>{{ t('auth.lastName') }}</label>
                <input type="text" name="nom" :placeholder="t('auth.lastNamePlaceholder')" class="input-modern" required />
              </div>
              <div class="form-group-modern">
                <label>{{ t('auth.firstName') }}</label>
                <input type="text" name="prenom" :placeholder="t('auth.firstNamePlaceholder')" class="input-modern" required />
              </div>
            </div>
            <div class="form-row-modern">
              <div class="form-group-modern">
                <label>{{ t('auth.email') }}</label>
                <input type="email" name="email" :placeholder="t('auth.emailPlaceholder')" class="input-modern" required />
              </div>
              <div class="form-group-modern">
                <label>{{ t('home.contact.phone') }}</label>
                <input type="text" name="phone" :placeholder="t('home.contact.phonePlaceholder')" class="input-modern" />
              </div>
            </div>
            <div class="form-group-modern full-width">
              <label>{{ t('home.contact.message') }}</label>
              <textarea name="message" :placeholder="t('home.contact.messagePlaceholder')" class="textarea-modern" rows="5" required></textarea>
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
              <h4>{{ t('home.contact.email') }}</h4>
              <p>contact@fleetmanager.com</p>
            </div>
            <div class="info-card-modern">
              <div class="info-icon-modern">
                <i class="bi bi-telephone"></i>
              </div>
              <h4>{{ t('home.contact.phone') }}</h4>
              <p>+33 1 23 45 67 89</p>
            </div>
            <div class="info-card-modern">
              <div class="info-icon-modern">
                <i class="bi bi-geo-alt"></i>
              </div>
              <h4>{{ t('home.contact.address') }}</h4>
              <p>Paris, France</p>
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
        </div>
        <div class="footer-links">
          <div class="footer-column">
            <h4>{{ t('home.footer.product') }}</h4>
            <a href="#features">{{ t('home.nav.features') }}</a>
            <a href="#benefits">{{ t('home.nav.benefits') }}</a>
            <a href="#">{{ t('home.footer.pricing') }}</a>
          </div>
          <div class="footer-column">
            <h4>{{ t('home.footer.company') }}</h4>
            <a href="#">{{ t('home.footer.about') }}</a>
            <a href="#contact">{{ t('home.nav.contact') }}</a>
            <a href="#">{{ t('home.footer.careers') }}</a>
          </div>
          <div class="footer-column">
            <h4>{{ t('home.footer.legal') }}</h4>
            <a href="#">{{ t('home.footer.privacy') }}</a>
            <a href="#">{{ t('home.footer.terms') }}</a>
            <a href="#">{{ t('home.footer.legalNotice') }}</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 {{ t('home.brandName') }}. {{ t('home.footer.rights') }}</p>
        <div class="footer-social">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </footer>

  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';
import alerts from '@/utils/alerts';

export default {
  name: "HomePage",
  setup() {
    const { t } = useI18n();
    return { t };
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