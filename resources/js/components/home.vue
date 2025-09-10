<template>
    <div :class="['home-page', { dark: isDark }]">
        <!-- Theme Toggle Button -->
        <button @click="toggleTheme" class="theme-btn">
            <i v-if="isDark" class="bi bi-sun"></i>
            <i v-else class="bi bi-moon-stars"></i>
        </button>

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
                Notre plateforme centralise toutes vos opérations : suivi, maintenance et gestion de flotte automobile.<br/>
                Gagnez du temps, améliorez votre efficacité et assurez la disponibilité de vos véhicules.
            </p>
            <div class="space-x-4">
                <button class="btn btn-primary" @click="$router.push('/login')">Se connecter</button>
                <button class="btn btn-secondary" @click="$router.push('/register')">S'enregistrer</button>
            </div>
        </section>

        <!-- Intro Section -->
        <section class="intro text-center p-6">
            <h2 class="text-3xl font-bold mb-4">
                Pourquoi choisir notre plateforme de gestion de flotte automobile ?
            </h2>
            <p class="text-lg max-w-2xl mx-auto leading-relaxed">
                Que vous gériez une petite flotte ou une grande entreprise, notre solution est conçue pour répondre à vos besoins spécifiques.
                Découvrez les avantages qui font de nous le choix numéro un pour la gestion de flotte.
            </p>
        </section>

        <!-- Floating Scroll Button -->
        <button 
            v-if="showScrollBtn" 
            @click="scrollToBottom" 
            class="scroll-btn"
            >
            <i class="bi bi-arrow-down-circle"></i>
        </button>

        <!-- Properties Section -->
        <section class="features-section p-6">
            <h2 class="section-title">Nos avantages</h2>
            <div class="features grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mt-6">
                <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                    <i class="bi bi-shield-lock text-4xl text-blue-600 mb-2"></i>
                    <h3 class="fontbold">Sécurisé</h3>
                    <p>Vos données sont protégées grâce à des normes de sécurité avancées.</p>
                </div>
                <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                    <i class="bi bi-check-circle text-4xl text-green-600 mb-2"></i>
                    <h3 class="fontbold">Fiable</h3>
                    <p>Une plateforme stable et toujours disponible pour vos besoins.</p>
                </div>
                <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                    <i class="bi bi-lightbulb text-4xl text-yellow-500 mb-2"></i>
                    <h3 class="fontbold">Intuitif</h3>
                    <p>Une interface claire et facile à utiliser, même pour les débutants.</p>
                </div>
                <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                    <i class="bi bi-lightning text-4xl text-orange-500 mb-2"></i>
                    <h3 class="fontbold">Rapide</h3>
                    <p>Des performances optimisées pour un gain de temps maximal.</p>
                </div>
                <div class="card bg-white shadow-md rounded-xl p-4 text-center">
                    <i class="bi bi-clock-history text-4xl text-purple-600 mb-2"></i>
                    <h3 class="fontbold">Accessibilité 24/7</h3>
                    <p>Accédez à la plateforme à tout moment, où que vous soyez.</p>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="contact-section">
            <form class="contact-form">
                <h2 class="section-title">Contactez-nous</h2>

                <!-- Nom -->
                <div class="form-group">
                    <label for="name" class="form-label">Nom complet</label>
                    <input 
                        type="text" 
                        id="name" 
                        placeholder="Entrez votre nom" 
                        class="form-input"
                    />
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        placeholder="Entrez votre email" 
                        class="form-input"
                    />
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="message" class="form-label">Message</label>
                    <textarea 
                        id="message" 
                        rows="4" 
                        placeholder="Écrivez votre message..." 
                        class="form-input"
                    ></textarea>
                </div>

                <!-- Bouton -->
                <button type="submit" class="btn-submit">Envoyer</button>
            </form>
            </section>
    </div>
</template>

<script>
export default {
  name: "HomePage",
  data() {
    return {
      isDark: false, // default light mode
      showScrollBtn: true,
    };
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem("theme", this.isDark ? "dark" : "light"); // save preference
    },
    scrollToBottom() {
    this.showScrollBtn = false;
    window.scrollTo({
      top: document.body.scrollHeight,
      behavior: "smooth",
    });
    },
    handleScroll() {
        const scrollPos = window.scrollY + window.innerHeight;
        const pageHeight = document.body.scrollHeight;
        // si l’utilisateur est à moins de 100px du bas => cache le bouton
        this.showScrollBtn = scrollPos < pageHeight - 100;
    },
  },
  mounted() {
    // restore theme preference
    const saved = localStorage.getItem("theme");
    if (saved) this.isDark = saved === "dark";
    window.addEventListener("scroll", this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener("scroll", this.handleScroll);
  }
};
</script>


