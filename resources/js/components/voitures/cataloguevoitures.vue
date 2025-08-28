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

        <!-- Car Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div
                v-for="(voiture, index) in voitures"
                :key="index"
                class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex flex-col"
            >
                <!-- Image -->
                <img :src="voiture.image_url" alt="voiture image" class="w-full h-40 object-cover rounded mb-4" />

                <!-- Marque / Model & Year -->
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold">{{ voiture.marque }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ voiture.model }}</p>
                    </div>
                    <div class="text-gray-700 dark:text-gray-200 font-medium">
                        {{ voiture.annee }}
                    </div>
                </div>

                <!-- Status & Button -->
                <div class="flex justify-between items-center mt-auto">
                    <span
                        :class="[
                        'px-2 py-1 rounded text-sm font-semibold',
                        voiture.status === 'Disponible' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        ]"
                    >
                        {{ voiture.status }}
                    </span>
                    <button
                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm"
                        @click="voirPlus(voiture)"
                    >
                        Voir plus
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: "CatalogueVoitures",
        data() {
            return {
            isDark: false,
            menuItems: [
                { label: "Accueil", to: "/" },
                { label: "Tableau de bord", to: "/admindashboard" },
                { label: "Catalogue", to: "/voitures/cataloguevoitures" },
                { label: "Interventions", to: "/interventions" },
                { label: "Alertes", to: "/alertes" },
            ],
            voitures: [], // will be fetched from API
            };
        },
        methods: {
            toggleTheme() {
            this.isDark = !this.isDark;
            localStorage.setItem("theme", this.isDark ? "dark" : "light");
            },
            async fetchVoitures() {
                try {
                    const response = await axios.get("/api/voitures"); // make sure the URL is correct
                    console.log(response.data); // check the console if data comes
                    this.voitures = response.data;
                } catch (error) {
                    console.error("Error fetching voitures:", error);
                }
            },
            voirPlus(voiture) {
                // For example: navigate to detail page
                this.$router.push({ name: "VoitureDetail", params: { id: voiture.id } });
            },
        },
        mounted() {
                const saved = localStorage.getItem("theme");
                if (saved) this.isDark = saved === "dark";

                this.fetchVoitures(); // fetch cars from backend
        },
    };
</script>
