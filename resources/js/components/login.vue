<template>
  <div class="home-page">
    <div class="register-card">
      <!-- Bootstrap Icons -->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />
        
      <!-- Title -->
      <h2 class="section-title">Se connecter</h2>

      <form @submit.prevent="login" class="register-form">
        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input v-model="credentials.email" id="email" type="email" class="form-input" required />
        </div>

        <!-- Password with eye toggle -->
        <div class="form-group password-wrapper">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="input-with-icon">
            <input
              v-model="credentials.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-input"
              id="password"
              required
            />
            <i
              class="bi"
              :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"
              @click="togglePassword"
            ></i>
          </div>
          <small class="forgot-password">Mot de passe oublié ?</small>
        </div>

        <!-- No account yet -->
        <p class="no-account">
          Vous n'avez de compte ? <a href="/register">Créer un nouveau.</a>
        </p>

        <!-- Button -->
        <button type="submit" class="register-btn">Connexion</button>
      </form>

      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "LoginPage",
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
          console.error("Réponse API inattendue:", response.data);
          this.errorMessage = "Erreur serveur, réessayez.";
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
        this.errorMessage = "Email ou mot de passe incorrect";
        console.error(error.response?.data || error);
      }
    }
  }
};
</script>
