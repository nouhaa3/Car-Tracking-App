<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" alt="User" class="avatar" />
        </router-link>

        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <nav class="navbar mb-5">
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

        <div class="profile-wrapper">
          <!-- Page Header -->
          <div class="page-header">
              <div class="header-left">
                <h1>Centre d'aide</h1>
                <p>Solutions concrètes aux problèmes fréquents</p>
              </div>
            </div>

            <!-- Search Bar -->
            <div class="search-section">
              <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Rechercher dans l'aide..."
                  class="search-input"
                />
                <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs">
              <button
                v-for="category in categories"
                :key="category.id"
                :class="['category-tab', { active: activeCategory === category.id }]"
                @click="activeCategory = category.id"
              >
                <div class="tab-icon" :style="{ background: category.color }">
                  <i :class="category.icon"></i>
                </div>
                <div class="tab-content">
                  <span class="tab-name">{{ category.name }}</span>
                  <span class="tab-count">{{ category.count }} articles</span>
                </div>
              </button>
            </div>

          <!-- Content Sections -->
          <div class="help-content">
            <!-- Vehicles Section -->
            <div v-show="activeCategory === 'vehicles'" class="content-section">
              <h2 class="section-title">
                <i class="bi bi-car-front-fill"></i>
                Problèmes liés aux véhicules
              </h2>

              <div class="faq-list">
                <div
                  v-for="item in vehiclesFAQ"
                  :key="item.id"
                  :class="['faq-item', { active: activeFAQ === item.id }]"
                  @click="toggleFAQ(item.id)"
                >
                  <div class="faq-question">
                    <span>{{ item.question }}</span>
                    <i :class="['bi', activeFAQ === item.id ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                  </div>
                  <div v-show="activeFAQ === item.id" class="faq-answer">
                    <p v-html="item.answer"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Interventions Section -->
            <div v-show="activeCategory === 'interventions'" class="content-section">
              <h2 class="section-title">
                <i class="bi bi-tools"></i>
                Problèmes d'interventions
              </h2>

              <div class="faq-list">
                <div
                  v-for="item in interventionsFAQ"
                  :key="item.id"
                  :class="['faq-item', { active: activeFAQ === item.id }]"
                  @click="toggleFAQ(item.id)"
                >
                  <div class="faq-question">
                    <span>{{ item.question }}</span>
                    <i :class="['bi', activeFAQ === item.id ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                  </div>
                  <div v-show="activeFAQ === item.id" class="faq-answer">
                    <p v-html="item.answer"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Alerts Section -->
            <div v-show="activeCategory === 'alerts'" class="content-section">
              <h2 class="section-title">
                <i class="bi bi-bell-fill"></i>
                Problèmes d'alertes
              </h2>

              <div class="faq-list">
                <div
                  v-for="item in alertsFAQ"
                  :key="item.id"
                  :class="['faq-item', { active: activeFAQ === item.id }]"
                  @click="toggleFAQ(item.id)"
                >
                  <div class="faq-question">
                    <span>{{ item.question }}</span>
                    <i :class="['bi', activeFAQ === item.id ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                  </div>
                  <div v-show="activeFAQ === item.id" class="faq-answer">
                    <p v-html="item.answer"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reports Section -->
            <div v-show="activeCategory === 'reports'" class="content-section">
              <h2 class="section-title">
                <i class="bi bi-file-earmark-text-fill"></i>
                Problèmes de rapports
              </h2>

              <div class="faq-list">
                <div
                  v-for="item in reportsFAQ"
                  :key="item.id"
                  :class="['faq-item', { active: activeFAQ === item.id }]"
                  @click="toggleFAQ(item.id)"
                >
                  <div class="faq-question">
                    <span>{{ item.question }}</span>
                    <i :class="['bi', activeFAQ === item.id ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                  </div>
                  <div v-show="activeFAQ === item.id" class="faq-answer">
                    <p v-html="item.answer"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Troubleshooting Section -->
            <div v-show="activeCategory === 'troubleshooting'" class="content-section">
              <h2 class="section-title">
                <i class="bi bi-wrench-adjustable"></i>
                Dépannage technique
              </h2>

              <div class="faq-list">
                <div
                  v-for="item in troubleshootingFAQ"
                  :key="item.id"
                  :class="['faq-item', { active: activeFAQ === item.id }]"
                  @click="toggleFAQ(item.id)"
                >
                  <div class="faq-question">
                    <span>{{ item.question }}</span>
                    <i :class="['bi', activeFAQ === item.id ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                  </div>
                  <div v-show="activeFAQ === item.id" class="faq-answer">
                    <p v-html="item.answer"></p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Account Section -->
            <div v-show="activeCategory === 'account'" class="content-section">
              <h2 class="section-title">
                <i class="bi bi-person-circle"></i>
                Problèmes de compte
              </h2>

              <div class="faq-list">
                <div
                  v-for="item in accountFAQ"
                  :key="item.id"
                  :class="['faq-item', { active: activeFAQ === item.id }]"
                  @click="toggleFAQ(item.id)"
                >
                  <div class="faq-question">
                    <span>{{ item.question }}</span>
                    <i :class="['bi', activeFAQ === item.id ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                  </div>
                  <div v-show="activeFAQ === item.id" class="faq-answer">
                    <p v-html="item.answer"></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from './sidebar.vue';
import axios from 'axios';
import { inject } from 'vue';

export default {
  name: 'HelpPage',
  components: { Sidebar },
  setup() {
    const theme = inject('theme');
    return { theme };
  },
  data() {
    return {
      isExpanded: false,
      user: null,
      searchQuery: '',
      activeCategory: 'vehicles',
      activeFAQ: null,
      menuItems: [
        { label: 'Tableau de bord', to: '/admindashboard' },
        { label: 'Catalogue', to: '/voitures/cataloguevoitures' },
        { label: 'Interventions', to: '/interventions/catalogue' },
        { label: 'Alertes', to: '/alertes' },
      ],
      categories: [
        { id: 'vehicles', name: 'Véhicules', icon: 'bi bi-car-front-fill', color: 'linear-gradient(135deg, #748BAA 0%, #8A9BB5 100%)', count: 8 },
        { id: 'interventions', name: 'Interventions', icon: 'bi bi-tools', color: 'linear-gradient(135deg, #748BAA 0%, #8A9BB5 100%)', count: 7 },
        { id: 'alerts', name: 'Alertes', icon: 'bi bi-bell-fill', color: 'linear-gradient(135deg, #748BAA 0%, #8A9BB5 100%)', count: 6 },
        { id: 'reports', name: 'Rapports', icon: 'bi bi-file-earmark-text-fill', color: 'linear-gradient(135deg, #748BAA 0%, #8A9BB5 100%)', count: 6 },
        { id: 'troubleshooting', name: 'Dépannage', icon: 'bi bi-wrench-adjustable', color: 'linear-gradient(135deg, #748BAA 0%, #8A9BB5 100%)', count: 7 },
        { id: 'account', name: 'Compte', icon: 'bi bi-person-circle', color: 'linear-gradient(135deg, #748BAA 0%, #8A9BB5 100%)', count: 5 },
      ],
      vehiclesFAQ: [
        {
          id: 'v1',
          question: 'Pourquoi mon véhicule n\'apparaît pas dans la liste après l\'ajout ?',
          answer: 'Vérifiez que tous les champs obligatoires (immatriculation, marque, modèle) ont été correctement remplis. Si le problème persiste, actualisez la page (F5). Le véhicule doit aussi avoir un statut valide (Actif/Inactif).'
        },
        {
          id: 'v2',
          question: 'Comment suivre l\'historique complet d\'un véhicule ?',
          answer: 'Ouvrez la fiche détaillée du véhicule et consultez l\'onglet "Historique". Vous y trouverez toutes les interventions, alertes, et modifications effectuées. Utilisez les filtres par date pour cibler une période spécifique.'
        },
        {
          id: 'v3',
          question: 'Impossible de supprimer un véhicule, message d\'erreur affiché',
          answer: 'Un véhicule ne peut être supprimé s\'il a des interventions en cours ou des alertes actives. Résolvez d\'abord ces dépendances : clôturez les interventions et archivez les alertes, puis réessayez la suppression.'
        },
        {
          id: 'v4',
          question: 'Comment gérer plusieurs véhicules avec la même immatriculation ?',
          answer: 'Le système n\'autorise pas les doublons d\'immatriculation. Si vous avez un véhicule de remplacement avec la même plaque, archivez d\'abord l\'ancien véhicule (statut Inactif) avant d\'ajouter le nouveau.'
        },
        {
          id: 'v5',
          question: 'Les images des véhicules ne se chargent pas correctement',
          answer: 'Vérifiez que le format d\'image est supporté (JPG, PNG, max 5 MB). Si le problème persiste, videz le cache du navigateur (Ctrl+Maj+Suppr) ou essayez de télécharger l\'image à nouveau.'
        },
        {
          id: 'v6',
          question: 'Comment filtrer les véhicules nécessitant une maintenance urgente ?',
          answer: 'Utilisez les filtres avancés dans la liste des véhicules. Cochez "Alertes actives" et sélectionnez "Maintenance urgente". Vous pouvez aussi trier par date de dernière intervention pour identifier les véhicules négligés.'
        },
        {
          id: 'v7',
          question: 'Peut-on importer une liste de véhicules en masse ?',
          answer: 'Oui, utilisez le bouton "Importer" et téléchargez le modèle Excel fourni. Remplissez-le avec vos données (une ligne par véhicule), puis importez-le. Les erreurs seront affichées ligne par ligne pour correction.'
        },
        {
          id: 'v8',
          question: 'Comment associer des documents (carte grise, assurance) à un véhicule ?',
          answer: 'Dans la fiche véhicule, cliquez sur l\'onglet "Documents". Utilisez "Ajouter Document", sélectionnez le type (Carte grise, Assurance, etc.) et téléchargez le fichier PDF. Les dates d\'expiration peuvent déclencher des alertes automatiques.'
        },
      ],
      interventionsFAQ: [
        {
          id: 'i1',
          question: 'Une intervention n\'apparaît pas dans le calendrier malgré la date définie',
          answer: 'Vérifiez que le statut de l\'intervention est "Planifiée" ou "En cours". Les interventions "Terminées" ou "Annulées" ne s\'affichent pas dans le calendrier. Vérifiez aussi que la date est au format correct et dans le futur.'
        },
        {
          id: 'i2',
          question: 'Comment gérer une intervention urgente non planifiée ?',
          answer: 'Cliquez sur "Intervention Urgente" dans le menu rapide. Sélectionnez le véhicule concerné, cochez "Priorité Haute", et indiquez la nature du problème. Le système créera automatiquement une alerte pour le suivi.'
        },
        {
          id: 'i3',
          question: 'Impossible de clôturer une intervention, bouton "Terminer" grisé',
          answer: 'Pour clôturer une intervention, vous devez remplir : 1) Coût total, 2) Notes de travail, 3) Au moins un élément changé/réparé. Si un document est requis (facture), téléchargez-le avant de clôturer.'
        },
        {
          id: 'i4',
          question: 'Comment suivre les coûts d\'intervention par véhicule sur une période ?',
          answer: 'Accédez à "Rapports" > "Analyse par véhicule". Sélectionnez le véhicule et la période souhaitée. Le rapport affichera un graphique des coûts par type d\'intervention avec export Excel disponible.'
        },
        {
          id: 'i5',
          question: 'Les notifications de rappel d\'intervention ne fonctionnent pas',
          answer: 'Vérifiez vos paramètres de notification dans "Compte" > "Préférences". Assurez-vous que "Rappels d\'intervention" est activé et que votre email est vérifié. Les rappels sont envoyés 3 jours avant la date prévue.'
        },
        {
          id: 'i6',
          question: 'Comment dupliquer une intervention récurrente (vidange, révision) ?',
          answer: 'Ouvrez l\'intervention terminée, cliquez sur "Dupliquer". Modifiez uniquement la date et le kilométrage. Les autres champs (pièces, coûts estimés) seront pré-remplis pour gagner du temps.'
        },
        {
          id: 'i7',
          question: 'Peut-on assigner une intervention à un mécanicien spécifique ?',
          answer: 'Oui, dans le formulaire d\'intervention, utilisez le champ "Assigné à". Sélectionnez l\'utilisateur dans la liste (rôle Mécanicien requis). Il recevra une notification automatique et l\'intervention apparaîtra dans son tableau de bord.'
        },
      ],
      alertsFAQ: [
        {
          id: 'a1',
          question: 'Je reçois trop d\'alertes, comment réduire le spam ?',
          answer: 'Allez dans "Compte" > "Préférences Alertes". Désactivez les alertes non critiques (suggestions, rappels mineurs). Conservez activées : Maintenance urgente, Documents expirés, et Anomalies détectées. Vous pouvez aussi regrouper les alertes en digest quotidien.'
        },
        {
          id: 'a2',
          question: 'Une alerte de document expiré persiste après le renouvellement',
          answer: 'Après avoir téléchargé le nouveau document, mettez à jour la date d\'expiration dans la fiche véhicule > Documents. L\'alerte se fermera automatiquement sous 24h. Si elle persiste, cliquez sur "Résoudre" manuellement dans l\'alerte.'
        },
        {
          id: 'a3',
          question: 'Comment configurer des alertes de maintenance préventive personnalisées ?',
          answer: 'Dans "Alertes" > "Règles Personnalisées", créez une nouvelle règle. Définissez : 1) Critère (kilométrage, temps, usage), 2) Seuil d\'alerte, 3) Véhicules concernés, 4) Niveau de priorité. Exemple : alerte à 10,000 km pour vidange.'
        },
        {
          id: 'a4',
          question: 'Les alertes critiques n\'envoient pas de notification immédiate',
          answer: 'Vérifiez que le niveau "Critique" est coché dans vos préférences. Pour recevoir des SMS/notifications push, activez l\'option "Notifications urgentes" et autorisez les permissions dans votre navigateur (icône cadenas à gauche de l\'URL).'
        },
        {
          id: 'a5',
          question: 'Comment voir l\'historique des alertes résolues pour un véhicule ?',
          answer: 'Dans la fiche véhicule, onglet "Historique Alertes". Filtrez par statut "Résolues" ou "Archivées". Vous pouvez exporter cet historique en PDF pour documentation ou audit de maintenance.'
        },
        {
          id: 'a6',
          question: 'Peut-on déléguer la gestion des alertes à un collègue ?',
          answer: 'Oui, ouvrez l\'alerte et cliquez sur "Assigner". Sélectionnez l\'utilisateur qui recevra la responsabilité. Il sera notifié immédiatement et l\'alerte apparaîtra dans son tableau de bord avec priorité.'
        },
      ],
      reportsFAQ: [
        {
          id: 'r1',
          question: 'Le rapport PDF généré est vide ou incomplet',
          answer: 'Ce problème survient si aucune donnée ne correspond aux filtres. Vérifiez : 1) Période sélectionnée (pas trop restreinte), 2) Véhicules inclus dans le filtre, 3) Statuts d\'intervention (inclure "Terminées"). Si le problème persiste, essayez d\'exporter en Excel d\'abord pour vérifier les données.'
        },
        {
          id: 'r2',
          question: 'Comment créer un rapport comparatif entre plusieurs véhicules ?',
          answer: 'Allez dans "Rapports" > "Analyse Comparative". Sélectionnez jusqu\'à 5 véhicules, choisissez les métriques (coûts, interventions, alertes), et la période. Le rapport générera des graphiques côte à côte avec pourcentages d\'écart.'
        },
        {
          id: 'r3',
          question: 'Les graphiques du rapport ne correspondent pas aux données du tableau',
          answer: 'Cela peut être dû au cache. Cliquez sur "Actualiser les données" avant de générer le rapport. Si l\'écart persiste, vérifiez que les filtres appliqués au graphique et au tableau sont identiques (dates, catégories).'
        },
        {
          id: 'r4',
          question: 'Comment automatiser l\'envoi mensuel d\'un rapport au responsable ?',
          answer: 'Dans "Rapports" > "Programmation", créez une nouvelle tâche : 1) Choisissez le type de rapport, 2) Fréquence (mensuelle, 1er du mois à 8h), 3) Destinataires (emails séparés par virgule), 4) Format (PDF/Excel). Le système enverra automatiquement.'
        },
        {
          id: 'r5',
          question: 'Impossible de filtrer un rapport par centre de coût ou département',
          answer: 'Ces filtres nécessitent que les véhicules soient catégorisés. Allez dans chaque fiche véhicule et remplissez les champs "Département" et "Centre de coût". Une fois configurés, les filtres apparaîtront dans la génération de rapports.'
        },
        {
          id: 'r6',
          question: 'Comment exporter uniquement les coûts d\'intervention sans les détails ?',
          answer: 'Utilisez le rapport "Synthèse Financière" au lieu du rapport complet. Cochez uniquement "Coûts totaux" et "Catégories de dépenses". Décochez "Détails interventions" pour obtenir un résumé condensé exportable en Excel.'
        },
      ],
      troubleshootingFAQ: [
        {
          id: 't1',
          question: 'L\'application est lente ou les pages mettent du temps à charger',
          answer: 'Solutions : 1) Videz le cache navigateur (Ctrl+Maj+Suppr), 2) Vérifiez votre connexion Internet, 3) Fermez les onglets inutiles (max 2-3 onglets FleetManager), 4) Si vous avez >1000 véhicules, utilisez les filtres pour limiter l\'affichage. Contactez l\'admin si cela persiste.'
        },
        {
          id: 't2',
          question: 'Erreur "Session expirée" apparaît fréquemment',
          answer: 'Votre session expire après 2 heures d\'inactivité. Pour rester connecté : 1) Cochez "Rester connecté" à la connexion (session 30 jours), 2) Ne fermez pas le navigateur brutalement, déconnectez-vous proprement. Si le problème persiste, désactivez les extensions qui bloquent les cookies.'
        },
        {
          id: 't3',
          question: 'Impossible de télécharger un fichier (PDF, Excel, image)',
          answer: 'Vérifiez : 1) Taille du fichier < 10 MB, 2) Format autorisé (PDF, Excel, JPG, PNG), 3) Bloqueur de popups désactivé, 4) Espace disque suffisant sur votre appareil. Essayez dans un autre navigateur (Chrome recommandé) si le problème persiste.'
        },
        {
          id: 't4',
          question: 'Les modifications ne se sauvegardent pas ou disparaissent',
          answer: 'Causes fréquentes : 1) Clic trop rapide sur "Sauvegarder" (attendez le message de confirmation), 2) Connexion perdue pendant la sauvegarde (vérifiez WiFi), 3) Conflit de version (un collègue a modifié en même temps). Actualisez la page et réessayez.'
        },
        {
          id: 't5',
          question: 'Message d\'erreur "Accès refusé" sur certaines fonctionnalités',
          answer: 'Votre rôle ne dispose pas des permissions. Vérifiez avec votre administrateur : "Gestion Utilisateurs" > votre compte > Rôle et Permissions. Si vous devriez avoir accès, demandez une mise à niveau de votre rôle vers Manager ou Admin.'
        },
        {
          id: 't6',
          question: 'Les données ne se synchronisent pas entre mon PC et mon mobile',
          answer: 'FleetManager est une application web : les données sont synchronisées en temps réel. Si l\'écart persiste : 1) Actualisez les deux appareils (tirez vers le bas sur mobile), 2) Videz le cache, 3) Déconnectez-vous et reconnectez-vous. Vérifiez que vous utilisez le même compte.'
        },
        {
          id: 't7',
          question: 'Comment restaurer un véhicule ou une intervention supprimée par erreur ?',
          answer: 'Les suppressions sont définitives après confirmation. Contactez immédiatement votre administrateur (dans les 24h) : il peut restaurer depuis la sauvegarde quotidienne. Pour éviter cela à l\'avenir, utilisez le statut "Archivé" au lieu de supprimer définitivement.'
        },
      ],
      accountFAQ: [
        {
          id: 'ac1',
          question: 'Le lien de réinitialisation de mot de passe ne fonctionne pas ou a expiré',
          answer: 'Les liens expirent après 60 minutes. Demandez un nouveau lien via "Mot de passe oublié". Si le problème persiste, vérifiez vos spams ou contactez l\'administrateur pour réinitialiser manuellement votre compte.'
        },
        {
          id: 'ac2',
          question: 'Mon compte est bloqué après plusieurs tentatives de connexion',
          answer: 'Par sécurité, le compte se verrouille après 5 tentatives échouées. Attendez 15 minutes ou utilisez "Mot de passe oublié" pour le déverrouiller immédiatement. Les administrateurs peuvent aussi débloquer depuis "Gestion Utilisateurs".'
        },
        {
          id: 'ac3',
          question: 'Comment changer mon adresse email sans perdre l\'accès ?',
          answer: 'Allez dans "Compte" > "Modifier Profil". Saisissez le nouveau mail, un code de vérification sera envoyé aux DEUX adresses. Validez avec le code de l\'ancienne ET de la nouvelle pour confirmer le changement.'
        },
        {
          id: 'ac4',
          question: 'Je ne reçois plus les notifications par email',
          answer: 'Vérifiez : 1) Email confirmé (cliquez sur lien de vérification si nouveau compte), 2) Préférences notifications activées, 3) Notre adresse (noreply@fleetmanager.com) n\'est pas dans vos spams. Ajoutez-la à vos contacts pour garantir la réception.'
        },
        {
          id: 'ac5',
          question: 'Comment attribuer des permissions spécifiques à un utilisateur ?',
          answer: 'Dans "Gestion Utilisateurs", éditez l\'utilisateur et sélectionnez son rôle : 1) Admin (accès total), 2) Manager (lecture/écriture véhicules et interventions), 3) Mécanicien (interventions uniquement), 4) Consultant (lecture seule). Les permissions personnalisées sont disponibles en mode avancé.'
        },
      ],
    };
  },
  mounted() {
    this.getUser();
  },
  methods: {
    async getUser() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.user = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération de l\'utilisateur:', error);
      }
    },
    toggleFAQ(id) {
      this.activeFAQ = this.activeFAQ === id ? null : id;
    },
  },
};
</script>

.support-header {
  display: flex;
  align-items: flex-start;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.support-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 4rem;
  height: 4rem;
  background: linear-gradient(135deg, #748BAA 0%, #5d728f 100%);
  border-radius: 1rem;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(116, 139, 170, 0.3);
}

.support-icon i {
  font-size: 2rem;
  color: white;
}

.support-title h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
}

.support-title p {
  margin: 0;
  font-size: 0.9375rem;
  color: #6b7280;
  line-height: 1.5;
}

.support-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.support-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  border-radius: 0.75rem;
  font-size: 0.9375rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.support-btn i {
  font-size: 1.25rem;
}

.support-btn.primary {
  background: #748BAA;
  color: white;
  box-shadow: 0 2px 8px rgba(116, 139, 170, 0.3);
}

.support-btn.primary:hover {
  background: #5d728f;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(116, 139, 170, 0.4);
}

.support-btn.secondary {
  background: white;
  color: #748BAA;
  border: 2px solid #748BAA;
}

.support-btn.secondary:hover {
  background: #f8f9fa;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(116, 139, 170, 0.2);
}

.support-info {
  display: flex;
  gap: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #6b7280;
  font-size: 0.875rem;
}

.info-item i {
  font-size: 1.125rem;
  color: #748BAA;
}

/* Responsive */
@media (max-width: 768px) {
  .support-card {
    padding: 1.5rem;
  }

  .support-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .support-actions {
    flex-direction: column;
  }

  .support-info {
    flex-direction: column;
    gap: 1rem;
  }


