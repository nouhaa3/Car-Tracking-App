<template>
  <div class="profile-page" v-if="!loading && user">
    <h2 class="profile-title">Profil</h2>
    <p class="profile-subtitle">Voici vos informations personnelles</p>

    <div class="profile-container">
      <div class="profile-card left">
        <img :src="user.avatar || '/images/avatar.png'" alt="Avatar" class="profile-avatar" />
        <h3>{{ user.nom }} {{ user.prenom }}</h3>
        <p class="role">{{ user.role?.nomRole || "Utilisateur" }}</p>
      </div>

      <div class="profile-card right">
        <div class="profile-info">
          <div class="info-item"><span class="label">Email</span><span class="value">{{ user.email }}</span></div>
          <div class="info-item"><span class="label">Rôle</span><span class="value">{{ user.role?.nomRole }}</span></div>
        </div>
      </div>
    </div>
  </div>

  <div v-else-if="loading" class="loading">Chargement du profil...</div>
  <div v-else class="error">Impossible de charger le profil</div>
</template>

<script setup>
import { onMounted } from "vue";
import { useUserStore } from "@/stores/user";

const userStore = useUserStore();
const user = userStore.user;
const loading = userStore.loading;

onMounted(() => {
  if (!userStore.user) {
    userStore.fetchUser();
  }
});
</script>