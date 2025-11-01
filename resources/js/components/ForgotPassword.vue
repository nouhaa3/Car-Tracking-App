<template>
  <div class="auth-page">
    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <div class="auth-container">
      <div class="auth-card">
        <!-- Logo & Title -->
        <div class="auth-header">
          <div class="auth-logo">
            <i class="bi bi-key"></i>
          </div>
          <h2 class="auth-title">{{ t('forgotPassword.title') }}</h2>
          <p class="auth-subtitle">
            {{ t('forgotPassword.subtitle') }}
          </p>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="success-message-auth">
          <i class="bi bi-check-circle"></i>
          {{ successMessage }}
          <div v-if="resetLink" class="reset-link-box">
            <p style="font-weight: 600; margin: 1rem 0 0.5rem 0; color: #0D1821;">
              {{ t('forgotPassword.resetLink') }}
            </p>
            
            <!-- Clickable Link Button -->
            <button 
              type="button"
              @click="goToResetPage" 
              class="reset-link-btn"
            >
              <i class="bi bi-link-45deg"></i>
              {{ t('forgotPassword.clickToReset') }}
            </button>
            
            <!-- Or Copy Link -->
            <p style="font-size: 0.85rem; color: #546A88; margin: 1rem 0 0.5rem 0; text-align: center;">
              {{ t('forgotPassword.orCopy') }}
            </p>
            <div class="link-display">
              <input 
                type="text" 
                :value="resetLink" 
                readonly 
                class="link-input"
                ref="linkInput"
              />
              <button 
                type="button" 
                @click="copyLink" 
                class="copy-btn"
                :class="{ copied: linkCopied }"
              >
                <i class="bi" :class="linkCopied ? 'bi-check-lg' : 'bi-clipboard'"></i>
                {{ linkCopied ? t('common.copied') : t('common.copy') }}
              </button>
            </div>
          </div>
        </div>

        <form v-if="!successMessage" @submit.prevent="sendResetLink" class="auth-form">
          <!-- Email -->
          <div class="form-group-auth">
            <label for="email" class="form-label-auth">
              <i class="bi bi-envelope"></i>
              {{ t('auth.email') }}
            </label>
            <input 
              v-model="email" 
              id="email" 
              type="email" 
              class="form-input-auth" 
              :placeholder="t('auth.emailPlaceholder')"
              required 
            />
          </div>

          <!-- Error Message -->
          <p v-if="errorMessage" class="error-message-auth">
            <i class="bi bi-exclamation-circle"></i>
            {{ errorMessage }}
          </p>

          <!-- Button -->
          <button type="submit" class="auth-btn" :disabled="loading">
            <span v-if="!loading">{{ t('forgotPassword.sendLink') }}</span>
            <span v-else>{{ t('common.sending') }}</span>
            <i v-if="!loading" class="bi bi-arrow-right"></i>
            <i v-else class="bi bi-hourglass-split"></i>
          </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
          <p>
            <a href="/login" class="auth-link">
              <i class="bi bi-arrow-left"></i>
              {{ t('auth.backToLogin') }}
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';
import axios from "axios";

export default {
  name: "ForgotPassword",
  setup() {
    const { t } = useI18n();
    return { t };
  },
  data() {
    return {
      email: "",
      errorMessage: "",
      successMessage: "",
      resetLink: "",
      linkCopied: false,
      loading: false
    };
  },
  methods: {
    async sendResetLink() {
      this.loading = true;
      this.errorMessage = "";
      this.successMessage = "";

      try {
        const response = await axios.post("http://127.0.0.1:8000/api/forgot-password", {
          email: this.email
        });

        this.successMessage = response.data.message;
        this.resetLink = response.data.reset_url || "";
        this.email = "";
      } catch (error) {
        if (error.response?.status === 404) {
          this.errorMessage = this.t('forgotPassword.errors.accountNotFound');
        } else if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else {
          this.errorMessage = this.t('errors.generic');
        }
        console.error(this.t('errors.forgotPasswordError'), error);
      } finally {
        this.loading = false;
      }
    },
    copyLink() {
      if (this.$refs.linkInput) {
        this.$refs.linkInput.select();
        document.execCommand('copy');
        this.linkCopied = true;
        setTimeout(() => {
          this.linkCopied = false;
        }, 2000);
      }
    },
    extractTokenAndEmail(url) {
      try {
        const urlObj = new URL(url);
        return {
          token: urlObj.searchParams.get('token'),
          email: urlObj.searchParams.get('email')
        };
      } catch (e) {
        return { token: '', email: '' };
      }
    },
    goToResetPage() {
      if (this.resetLink) {
        const { token, email } = this.extractTokenAndEmail(this.resetLink);
        if (token && email) {
          this.$router.push({
            path: '/reset-password',
            query: { token, email }
          });
        }
      }
    }
  }
};
</script>