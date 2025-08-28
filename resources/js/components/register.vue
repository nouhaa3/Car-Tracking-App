<template>
  <div class="home-page">
    <div class="register-card">
      <h2 class="register-title">S'enregistrer</h2>

      <form @submit.prevent="addUser" class="register-form">
        <!-- Nom -->
        <div class="form-group">
          <label class="form-label">Nom</label>
          <input v-model="user.nom" type="text" class="form-input" required />
        </div>

        <!-- Prénom -->
        <div class="form-group">
          <label class="form-label">Prénom</label>
          <input v-model="user.prenom" type="text" class="form-input" required />
        </div>

        <!-- Email -->
        <div class="form-group">
          <label class="form-label">Email</label>
          <input v-model="user.email" type="email" class="form-input" required />
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label">Mot de passe</label>
          <input v-model="user.password" type="password" class="form-input" required />
        </div>

        <!-- Rôle -->
        <div class="form-group">
          <label class="form-label">Rôle</label>
          <select v-model="user.role" class="form-input" required>
            <option value="" disabled>Sélectionnez votre rôle</option>
            <option value="1">Admin</option>
            <option value="2">Agent</option>
            <option value="3">Technicien</option>
          </select>
        </div>

        <!-- Button -->
        <button type="submit" class="register-btn">Ajouter</button>
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
      user: { nom: "", prenom: "", email: "", password: "", role: "" },
      errorMessage: ""
    };
  },
  methods: {
    async addUser() {
      try {
        const response = await axios.post("http://127.0.0.1:8000/api/register", this.user);

        // Save user in localStorage
        localStorage.setItem("user", JSON.stringify(response.data.user));

        // Redirect based on role
        const role = response.data.user.role.nomRole; 
        if (role === "admin") {
          this.$router.push("/admindashboard");
        } else if (role === "agent") {
          this.$router.push("/agentdashboard");
        } else if (role === "technicien") {
          this.$router.push("/techniciendashboard");
        } else {
          this.$router.push("/");
        }

      } catch (error) {
        this.errorMessage = error.response?.data?.message || "Erreur d'enregistrement";
        console.error(error.response?.data || error);
      }
    }
  }
};
</script>
