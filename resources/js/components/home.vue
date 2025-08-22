<template>
    <div :class="['home-page', { dark: isDark }]">
        <!-- Theme Toggle Button -->
        <button @click="toggleTheme" class="theme-btn">
        <i v-if="isDark" class="bi bi-sun"></i>
        <i v-else class="bi bi-moon"></i>
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

        <!-- Welcome Section -->
        <section class="hero">
            <h2 class="text-4xl font-bold mb-4">
                Gérez vos véhicules en toute simplicité
            </h2>
            <p class="text-lg max-w-2xl mx-auto leading-relaxed">
                Notre plateforme centralise toutes vos opérations : suivi, maintenance et gestion de flotte automobile.
                Gagnez du temps, améliorez votre efficacité et assurez la disponibilité de vos véhicules.
            </p>
        </section>

        <!-- Properties Section -->
        <section class="features grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                <i class="bi bi-shield-lock text-4xl text-blue-600 mb-2"></i>
                <h3 class="font-bold">Sécurisé</h3>
                <p>Vos données sont protégées grâce à des normes de sécurité avancées.</p>
            </div>
            <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                <i class="bi bi-check-circle text-4xl text-green-600 mb-2"></i>
                <h3 class="font-bold">Fiable</h3>
                <p>Une plateforme stable et toujours disponible pour vos besoins.</p>
            </div>
            <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                <i class="bi bi-lightbulb text-4xl text-yellow-500 mb-2"></i>
                <h3 class="font-bold">Intuitif</h3>
                <p>Une interface claire et facile à utiliser, même pour les débutants.</p>
            </div>
            <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                <i class="bi bi-lightning text-4xl text-orange-500 mb-2"></i>
                <h3 class="font-bold">Rapide</h3>
                <p>Des performances optimisées pour un gain de temps maximal.</p>
            </div>
            <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                <i class="bi bi-clock-history text-4xl text-purple-600 mb-2"></i>
                <h3 class="font-bold">Accessibilité 24/7</h3>
                <p>Accédez à la plateforme à tout moment, où que vous soyez.</p>
            </div>
        </section>
    </div>
</template>

<script>
export default {
  name: "HomePage",
  data() {
    return {
      isDark: false, // default light mode
      menuItems: [
        { label: "Accueil", to: "/" },
        { label: "Tableau de bord", to: "/admindashboard" },
        { label: "Catalogue", to: "/cataloguevoitures" },
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


