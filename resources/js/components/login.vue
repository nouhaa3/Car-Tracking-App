<template>
  <div class="home-page">
    <div class="register-card">
      <h2 class="register-title">Se connecter</h2>

      <form @submit.prevent="login" class="register-form">
        <!-- Email -->
        <div class="form-group">
          <label class="form-label">Email</label>
          <input v-model="credentials.email" type="email" class="form-input" required />
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label">Mot de passe</label>
          <input v-model="credentials.password" type="password" class="form-input" required />
        </div>

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
      errorMessage: ""
    };
  },
  methods: {
    async login() {
    try {
      const response = await axios.post("http://127.0.0.1:8000/api/login", this.credentials);

      // ✅ Save token
      localStorage.setItem("token", response.data.token);

      // ✅ Save user (optionnel, si tu veux afficher le nom)
      localStorage.setItem("user", JSON.stringify(response.data.user));

      // Redirect by role
      const role = response.data.user.role;
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
      this.errorMessage = "Email ou mot de passe incorrect";
      console.error(error.response?.data || error);
    }
  }
  }
};
</script>
