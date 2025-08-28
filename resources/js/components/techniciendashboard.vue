<template>
    <div :class="['home-page', { dark: isDark }]">
        <!-- Theme Toggle Button -->
        <button @click="toggleTheme" class="theme-btn">
        <i v-if="isDark" class="bi bi-sun"></i>
        <i v-else class="bi bi-moon-stars"></i>
        </button>

        <!-- Navbar -->
        <nav class="navbar">
            <router-link 
                v-for="(item, index) in menuItems" 
                :key="index"
                :to="item.to" 
                class="nav-link"
                :class="{'active': $route.path === item.to}"
            >
                {{ item.label }}
            </router-link>
        </nav>

        <!-- Bootstrap Icons -->
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        zzz

    </div>
</template>

<script>
export default {
  name: "TechnicienDashboard",
  data() {
    return {
      isDark: false, // default light mode
      menuItems: [
        { label: "Accueil", to: "/" },
        { label: "Tableau de bord", to: "/techniciendashboard" },
        { label: "Catalogue", to: "/voitures/cataloguevoitures" },
        { label: "Interventions", to: "/interventions" },
        { label: "Alertes", to: "/alertes" },
      ],
    };
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem("theme", this.isDark ? "dark" : "light"); // save preference
    },
  },
  mounted() {
    // restore theme preference
    const saved = localStorage.getItem("theme");
    if (saved) this.isDark = saved === "dark";
  },
};
</script>


