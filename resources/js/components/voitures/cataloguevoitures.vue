<template>
  <div :class="['home-page', { dark: isDark }]">
    <!-- Theme Toggle Button -->
    <button @click="toggleTheme" class="theme-btn">
      <i v-if="isDark" class="bi bi-sun"></i>
      <i v-else class="bi bi-moon-stars"></i>
    </button>

    <!-- Navbar -->
    <nav class="navbar mb-4">
      <router-link
        v-for="(item, index) in menuItems"
        :key="index"
        :to="item.to"
        class="nav-link"
        :class="{ active: $route.path === item.to }"
      >
        {{ item.label }}
      </router-link>
    </nav>

    <!-- === Catalogue en 2 colonnes === -->
    <div class="catalogue-container">
      
      <!-- 📌 Colonne gauche (Filtres) -->
      <div class="catalogue-left">
        <aside class="filter-sidebar">
            <div class="filter-header">
                <h3>Filtrer</h3>
                <!-- Bouton visible uniquement si un filtre est actif -->
                <button 
                    v-if="isFilterActive" 
                    class="clear-filters-btn" 
                    @click="resetFilters"
                    >
                    Supprimer
                </button>
            </div>
            <hr class="filter-divider" />

            <div class="filter-section">
            <h4>Marque</h4>
            <input type="text" v-model="filters.marque" placeholder="Ex: Toyota" />
            </div>

            <div class="filter-section">
            <h4>Modèle</h4>
            <input type="text" v-model="filters.modele" placeholder="Ex: Corolla" />
            </div>

            <div class="filter-section">
            <h4>Couleur</h4>
            <input type="text" v-model="filters.couleur" placeholder="Ex: Rouge" />
            </div>

            <div class="filter-section">
            <h4>Année</h4>
            <input type="number" v-model="filters.annee" placeholder="Ex: 2020" />
            </div>

          <div class="filter-section">
            <h4>Statut</h4>
            <label><input type="radio" value="" v-model="filters.status" /> Tous</label>
            <label><input type="radio" value="Disponible" v-model="filters.status" /> Disponible</label>
            <label><input type="radio" value="Indisponible" v-model="filters.status" /> Indisponible</label>
          </div>

          <div class="filter-section">
            <h4>État</h4>
            <label><input type="radio" value="" v-model="filters.etat" /> Tous</label>
            <label><input type="radio" value="Neuf" v-model="filters.etat" /> Neuf</label>
            <label><input type="radio" value="Occasion" v-model="filters.etat" /> Occasion</label>
          </div>
        </aside>
      </div>

      <!-- 📌 Colonne droite (Catalogue voitures) -->
      <div class="catalogue-right">
        <!-- 🔍 Barre de recherche alignée avec les voitures -->
        <div class="catalogue-header">
            <input
            v-model="searchQuery"
            type="text"
            placeholder="Rechercher par marque ou modèle..."
            class="search-input"
            />
        </div>
        <div class="catalogue-grid">
          <div
            v-for="(voiture, index) in filteredVoitures"
            :key="index"
            class="car-card"
          >
            <!-- Image -->
            <img :src="voiture.image || '/images/default.png'" alt="Image voiture" />

            <!-- Infos -->
            <div class="car-info">
              <div class="car-info-left">
                <p class="marque">{{ voiture.marque }}</p>
                <p class="modele">{{ voiture.modele }}</p>
              </div>
              <span class="car-info-right">{{ voiture.annee }}</span>
            </div>

            <!-- Bouton -->
            <div class="car-actions">
              <button class="car-btn" @click="voirPlus(voiture)">Voir plus</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Logout -->
    <button @click="logout" class="logout-btn">
      <i class="bi bi-box-arrow-right"></i>
    </button>

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
  </div>
</template>



<script>
import axios from "axios";

export default {
  name: "CatalogueVoitures",
  data() {
    return {
      isDark: false,
      searchQuery: "",
      showFilters: false,
      filters: {
        marque: "",
        modele: "",
        couleur: "",
        annee: "",
        status: "",
        etat: ""
      },
      menuItems: [
        { label: "Accueil", to: "/" },
        { label: "Tableau de bord", to: "/admindashboard" },
        { label: "Catalogue", to: "/voitures/cataloguevoitures" },
        { label: "Interventions", to: "/interventions" },
        { label: "Alertes", to: "/alertes" },
      ],
      voitures: [],
    };
  },
  computed: {
    filteredVoitures() {
      return this.voitures.filter((v) => {
        const matchSearch =
          v.marque.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          v.modele.toLowerCase().includes(this.searchQuery.toLowerCase());

        const matchMarque =
          !this.filters.marque ||
          v.marque.toLowerCase().includes(this.filters.marque.toLowerCase());

        const matchModele =
          !this.filters.modele ||
          v.modele.toLowerCase().includes(this.filters.modele.toLowerCase());

        const matchCouleur =
          !this.filters.couleur ||
          v.couleur.toLowerCase().includes(this.filters.couleur.toLowerCase());

        const matchAnnee =
          !this.filters.annee || v.annee == this.filters.annee;

        const matchStatus =
          this.filters.status === "" || v.status === this.filters.status;

        const matchEtat =
          this.filters.etat === "" || v.etat === this.filters.etat;

        return (
          matchSearch &&
          matchMarque &&
          matchModele &&
          matchCouleur &&
          matchAnnee &&
          matchStatus &&
          matchEtat
        );
      });
    },
    isFilterActive() {
    return (
      this.filters.marque ||
      this.filters.modele ||
      this.filters.couleur ||
      this.filters.annee ||
      this.filters.status ||
      this.filters.etat
    );
  }
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem("theme", this.isDark ? "dark" : "light");
    },
    toggleFilter() {
      this.showFilters = !this.showFilters;
    },
    async fetchVoitures() {
      try {
        const response = await axios.get("http://127.0.0.1:8000/api/voitures", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
        this.voitures = response.data;
      } catch (error) {
        console.error("Error fetching voitures:", error);
      }
    },
    voirPlus(voiture) {
      this.$router.push({ name: "VoitureDetail", params: { id: voiture.id } });
    },
    resetFilters() {
    this.filters = {
      marque: "",
      modele: "",
      couleur: "",
      annee: "",
      status: "",
      etat: ""
    };
  }
  },
  mounted() {
    const saved = localStorage.getItem("theme");
    if (saved) this.isDark = saved === "dark";
    this.fetchVoitures();
  },
};
</script>