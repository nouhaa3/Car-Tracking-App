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
            <i class="bi bi-car-front-fill"></i>
          </div>
          <h2 class="auth-title">{{ t('auth.welcome') }}</h2>
          <p class="auth-subtitle">{{ t('auth.loginSubtitle') }}</p>
        </div>

        <form @submit.prevent="login" class="auth-form" method="post" action="/api/login">
          <!-- Email -->
          <div class="form-group-auth">
            <label for="email" class="form-label-auth">
              <i class="bi bi-envelope"></i>
              {{ t('auth.email') }}
            </label>
            <input 
              v-model="credentials.email" 
              id="email" 
              name="email"
              type="email" 
              class="form-input-auth" 
              :placeholder="t('auth.emailPlaceholder')"
              autocomplete="username"
              required 
            />
          </div>

          <!-- Password with eye toggle -->
          <div class="form-group-auth">
            <label for="password" class="form-label-auth">
              <i class="bi bi-lock"></i>
              {{ t('auth.password') }}
            </label>
            <div class="input-with-icon-auth">
              <input
                v-model="credentials.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-input-auth"
                id="password"
                name="password"
                :placeholder="t('auth.passwordPlaceholder')"
                autocomplete="current-password"
                required
              />
              <i
                class="toggle-password"
                :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"
                @click="togglePassword"
              ></i>
            </div>
            <a href="/forgot-password" class="forgot-link">{{ t('auth.forgotPassword') }}</a>
          </div>

          <!-- Error Message -->
          <p v-if="errorMessage" class="error-message-auth">
            <i class="bi bi-exclamation-circle"></i>
            {{ errorMessage }}
          </p>

          <!-- Button -->
          <button type="submit" class="auth-btn">
            <span>{{ t('auth.login') }}</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
          <p>{{ t('auth.noAccount') }} <a href="/register" class="auth-link">{{ t('auth.createAccount') }}</a></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';
import axios from "axios";
import { inject } from 'vue';

export default {
  name: "LoginPage",
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  data() {
    return {
      credentials: { email: "", password: "" },
      errorMessage: "",
      showPassword: false
    };
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async login() {
      try {
        const response = await axios.post("http://127.0.0.1:8000/api/login", this.credentials);

        // Récupération des données API
        const token = response.data.token;
        const user = response.data.user ?? response.data;

        if (!token || !user) {
          console.error(this.t('errors.unexpectedResponse'), response.data);
          this.errorMessage = this.t('errors.serverError');
          return;
        }

        // Sauvegarde dans localStorage
        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(user));

        // Configurer axios pour les futures requêtes
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        // Vérification rôle (selon ce que ton API renvoie)
        let role = null;

        if (user.role && user.role.nomRole) {
          role = user.role.nomRole.toLowerCase(); // ex: "admin"
        } else if (user.role_id) {
          // fallback si API ne renvoie que role_id
          switch (parseInt(user.role_id)) {
            case 1: role = "admin"; break;
            case 2: role = "agent"; break;
            case 3: role = "technicien"; break;
            default: role = "user";
          }
        }

        // Redirection selon rôle
        if (role === "admin") this.$router.push("/admindashboard");
        else if (role === "agent") this.$router.push("/agentdashboard");
        else if (role === "technicien") this.$router.push("/techniciendashboard");
        else this.$router.push("/");

      } catch (error) {
        this.errorMessage = this.t('auth.errors.invalidCredentials');
        console.error(error.response?.data || error);
      }
    }
  }
};
</script>