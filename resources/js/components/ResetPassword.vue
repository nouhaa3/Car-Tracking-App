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
            <i class="bi bi-shield-lock"></i>
          </div>
          <h2 class="auth-title">{{ t('resetPassword.title') }}</h2>
          <p class="auth-subtitle">
            {{ t('resetPassword.subtitle') }}
          </p>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="success-message-auth">
          <i class="bi bi-check-circle"></i>
          {{ successMessage }}
          <div style="margin-top: 1rem;">
            <a href="/login" class="auth-link">{{ t('resetPassword.loginNow') }}</a>
          </div>
        </div>

        <form v-if="!successMessage" @submit.prevent="resetPassword" class="auth-form">
          <!-- Email (read-only) -->
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
              readonly
              style="background-color: #f9fbfd;"
            />
          </div>

          <!-- New Password -->
          <div class="form-group-auth">
            <label for="password" class="form-label-auth">
              <i class="bi bi-lock"></i>
              {{ t('resetPassword.newPassword') }}
            </label>
            <div class="input-with-icon-auth">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                id="password"
                class="form-input-auth"
                :placeholder="t('auth.passwordPlaceholder')"
                required
                minlength="6"
              />
              <i
                class="toggle-password"
                :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"
                @click="togglePassword"
              ></i>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="form-group-auth">
            <label for="password_confirmation" class="form-label-auth">
              <i class="bi bi-lock-fill"></i>
              {{ t('resetPassword.confirmPassword') }}
            </label>
            <div class="input-with-icon-auth">
              <input
                v-model="password_confirmation"
                :type="showPasswordConfirm ? 'text' : 'password'"
                id="password_confirmation"
                class="form-input-auth"
                :placeholder="t('auth.passwordPlaceholder')"
                required
                minlength="6"
              />
              <i
                class="toggle-password"
                :class="showPasswordConfirm ? 'bi bi-eye-slash' : 'bi bi-eye'"
                @click="togglePasswordConfirm"
              ></i>
            </div>
          </div>

          <!-- Password Requirements -->
          <div class="password-requirements">
            <small>{{ t('resetPassword.passwordRequirements') }}</small>
          </div>

          <!-- Error Message -->
          <p v-if="errorMessage" class="error-message-auth">
            <i class="bi bi-exclamation-circle"></i>
            {{ errorMessage }}
          </p>

          <!-- Button -->
          <button type="submit" class="auth-btn" :disabled="loading">
            <span v-if="!loading">{{ t('resetPassword.resetButton') }}</span>
            <span v-else>{{ t('common.resetting') }}</span>
            <i v-if="!loading" class="bi bi-check-lg"></i>
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
  name: "ResetPassword",
  setup() {
    const { t } = useI18n();
    return { t };
  },
  data() {
    return {
      token: "",
      email: "",
      password: "",
      password_confirmation: "",
      errorMessage: "",
      successMessage: "",
      loading: false,
      showPassword: false,
      showPasswordConfirm: false
    };
  },
  mounted() {
    // Get token and email from URL query parameters
    const urlParams = new URLSearchParams(window.location.search);
    this.token = urlParams.get('token') || this.$route.query.token || "";
    this.email = urlParams.get('email') || this.$route.query.email || "";

    if (!this.token || !this.email) {
      this.errorMessage = this.t('resetPassword.errors.invalidLink');
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    togglePasswordConfirm() {
      this.showPasswordConfirm = !this.showPasswordConfirm;
    },
    async resetPassword() {
      this.loading = true;
      this.errorMessage = "";
      this.successMessage = "";

      // Validate passwords match
      if (this.password !== this.password_confirmation) {
        this.errorMessage = this.t('resetPassword.errors.passwordMismatch');
        this.loading = false;
        return;
      }

      // Validate password length
      if (this.password.length < 6) {
        this.errorMessage = this.t('resetPassword.errors.passwordTooShort');
        this.loading = false;
        return;
      }

      try {
        const response = await axios.post("http://127.0.0.1:8000/api/reset-password", {
          token: this.token,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        });

        this.successMessage = this.t('resetPassword.successMessage');
        this.password = "";
        this.password_confirmation = "";
      } catch (error) {
        if (error.response?.status === 400) {
          this.errorMessage = this.t('resetPassword.errors.invalidLink');
        } else if (error.response?.data?.message) {
          this.errorMessage = error.response.data.message;
        } else if (error.response?.data?.errors) {
          // Handle validation errors
          const errors = Object.values(error.response.data.errors).flat();
          this.errorMessage = errors.join(" ");
        } else {
          this.errorMessage = this.t('errors.generic');
        }
        console.error(this.t('errors.resetPasswordError'), error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>