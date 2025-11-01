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
          <h2 class="auth-title">{{ t('auth.createAccount') }}</h2>
          <p class="auth-subtitle">{{ t('auth.registerSubtitle') }}</p>
        </div>

        <form @submit.prevent="addUser" class="auth-form">
          <!-- Nom & prenom -->
          <div class="form-row-auth">
            <div class="form-group-auth">
              <label for="nom" class="form-label-auth">
                <i class="bi bi-person"></i>
                {{ t('auth.lastName') }}
              </label>
              <input 
                v-model="user.nom" 
                id="nom" 
                type="text" 
                class="form-input-auth" 
                :placeholder="t('auth.lastNamePlaceholder')"
                required 
              />
            </div>

            <div class="form-group-auth">
              <label for="prenom" class="form-label-auth">
                <i class="bi bi-person"></i>
                {{ t('auth.firstName') }}
              </label>
              <input 
                v-model="user.prenom" 
                id="prenom" 
                type="text" 
                class="form-input-auth" 
                :placeholder="t('auth.firstNamePlaceholder')"
                required 
              />
            </div>
          </div>

          <!-- Email -->
          <div class="form-group-auth">
            <label for="email" class="form-label-auth">
              <i class="bi bi-envelope"></i>
              {{ t('auth.email') }}
            </label>
            <input 
              v-model="user.email" 
              id="email" 
              type="email" 
              class="form-input-auth" 
              :placeholder="t('auth.emailPlaceholder')"
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
                v-model="user.password"
                :type="showPassword ? 'text' : 'password'"
                id="password"
                class="form-input-auth"
                :placeholder="t('auth.passwordPlaceholder')"
                required
              />
              <i
                class="toggle-password"
                :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"
                @click="togglePassword"
              ></i>
            </div>
          </div>

          <!-- Rôle -->
          <div class="form-group-auth">
            <label for="role" class="form-label-auth">
              <i class="bi bi-briefcase"></i>
              {{ t('auth.role') }}
            </label>
            <select v-model="user.role_id" id="role" class="form-input-auth" required>
              <option value="" disabled>{{ t('auth.selectRole') }}</option>
              <option value="1">{{ t('auth.roles.admin') }}</option>
              <option value="2">{{ t('auth.roles.agent') }}</option>
              <option value="3">{{ t('auth.roles.technician') }}</option>
            </select>
          </div>

          <!-- Error Message -->
          <p v-if="errorMessage" class="error-message-auth">
            <i class="bi bi-exclamation-circle"></i>
            {{ errorMessage }}
          </p>

          <!-- Button -->
          <button type="submit" class="auth-btn">
            <span>{{ t('auth.createAccount') }}</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
          <p>{{ t('auth.haveAccount') }} <a href="/login" class="auth-link">{{ t('auth.login') }}</a></p>
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
  name: "RegisterPage",
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  data() {
    return {
      user: { nom: "", prenom: "", email: "", password: "", role_id: "" },
      errorMessage: "",
      showPassword: false
    };
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async addUser() {
      try {
        const response = await axios.post("http://127.0.0.1:8000/api/register", {
          nom: this.user.nom,
          prenom: this.user.prenom,
          email: this.user.email,
          password: this.user.password,
          role_id: this.user.role_id,
        });

        // sécuriser la récupération
        const apiUser = response.data.user ?? response.data;
        const token = response.data.token;

        if (!apiUser) {
          console.error(this.t('errors.unexpectedResponse'), response.data);
          return;
        }

        // stocker et configurer axios
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        localStorage.setItem("user", JSON.stringify(apiUser));
        if (token) localStorage.setItem("token", token);

        // redirection selon role_id
        switch (parseInt(apiUser.role_id)) {
          case 1:
            this.$router.push("/admindashboard");
            break;
          case 2:
            this.$router.push("/agentdashboard");
            break;
          case 3:
            this.$router.push("/techniciendashboard");
            break;
          default:
            this.$router.push("/");
        }
      } catch (err) {
        console.error(this.t('errors.registerError'), err);
        this.errorMessage = this.t('auth.errors.registrationFailed');
      }
    }
  }
};
</script>