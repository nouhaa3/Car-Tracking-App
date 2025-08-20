<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-gray-100 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">S'enregister</h2>

    <form @submit.prevent="addUser">
      <div class="mb-3">
        <label class="block text-sm">Nom</label>
        <input v-model="user.nom" type="text" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-3">
        <label class="block text-sm">Prénom</label>
        <input v-model="user.prenom" type="text" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-3">
        <label class="block text-sm">Email</label>
        <input v-model="user.email" type="email" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-3">
        <label class="block text-sm">Mot de passe</label>
        <input v-model="user.password" type="password" class="w-full border p-2 rounded" required />
      </div>

      <div class="mb-3">
        <label class="block text-sm">Rôle</label>
        <select v-model="user.role_id" class="w-full border p-2 rounded" required>
          <option value="" disabled>Sélectionnez votre rôle</option>
          <option value="1">Admin</option>
          <option value="2">Agent</option>
          <option value="3">Technicien</option>
        </select>
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
        Ajouter
      </button>
    </form>

    <p v-if="message" class="mt-3 text-green-600">{{ message }}</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      user: { nom: "", prenom: "", email: "", password: "", role_id: "" },
      message: ""
    };
  },
  methods: {
    async addUser() {
      try {
        await axios.post("http://127.0.0.1:8000/api/register", this.user);
        this.message = "Utilisateur ajouté avec succès";
        this.user = { nom: "", prenom: "", email: "", password: "", role_id: "" };
      } catch (error) {
        this.message = "Erreur lors de l'ajout";
        console.error(error.response?.data || error);
      }
    }
  }
};
</script>
