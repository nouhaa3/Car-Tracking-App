<template>
  <div class="home-page">
    <div class="register-card">
      <!-- Bootstrap Icons -->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

      <h2 class="section-title">S'enregistrer</h2>

      <form @submit.prevent="addUser" class="register-form">
        <!-- Nom & prenom -->
        <div class="form-row">
          <div class="form-group">
            <label for="nom" class="form-label">Nom</label>
            <input v-model="user.nom" id="nom" type="text" class="form-input" required />
          </div>

          <div class="form-group">
            <label for="prenom" class="form-label">Prénom</label>
            <input v-model="user.prenom" id="prenom" type="text" class="form-input" required />
          </div>
        </div>
        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input v-model="user.email" id="email" type="email" class="form-input" required />
        </div>

        <!-- Password with eye toggle -->
        <div class="form-group password-wrapper">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="input-with-icon">
            <input
              v-model="user.password"
              :type="showPassword ? 'text' : 'password'"
              id="password"
              class="form-input"
              required
            />
            <i
              class="bi"
              :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"
              @click="togglePassword"
            ></i>
          </div>
        </div>

        <!-- Rôle -->
        <div class="form-group">
          <label for="role" class="form-label">Rôle</label>
          <select v-model="user.role_id" id="role" class="form-input" required>
            <option value="" disabled>Sélectionnez votre rôle</option>
            <option value="1">Admin</option>
            <option value="2">Agent</option>
            <option value="3">Technicien</option>
          </select>
        </div>

        <!-- Already have an account -->
        <p class="no-account">
          Vous avez déjà un compte ? <a href="/login">Se connecter.</a>
        </p>

        <!-- Button -->
        <button type="submit" class="register-btn">Créer</button>
      </form>

      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "RegisterPage",
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
          console.error("Réponse API inattendue:", response.data);
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
        console.error("Erreur register:", err);
      }
    }
  }
};
</script>
