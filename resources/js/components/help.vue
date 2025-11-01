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

          <!-- Help Page Container -->
          <div class="help-container">
            <!-- Header -->
            <div class="help-header">
              <div class="header-background">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
              </div>
              <div class="header-content">
                <div class="header-icon-wrapper">
                  <i class="bi bi-question-circle-fill"></i>
                  <div class="icon-pulse"></div>
                </div>
                <div class="header-text">
                  <h1>Centre d'aide</h1>
                  <p>Solutions concrètes aux problèmes fréquents</p>
                  <div class="header-stats">
                    <div class="stat-item">
                      <span class="stat-number">39</span>
                      <span class="stat-label">Articles</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                      <span class="stat-number">6</span>
                      <span class="stat-label">Catégories</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                      <span class="stat-number">24/7</span>
                      <span class="stat-label">Disponible</span>
                    </div>
                  </div>
                </div>
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
              <p class="search-hint">
                <i class="bi bi-lightbulb"></i>
                Astuce : Parcourez les catégories ci-dessous pour trouver rapidement votre réponse
              </p>
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

            <!-- Support Card -->
            <div class="support-card">
              <div class="support-icon">
                <i class="bi bi-headset"></i>
              </div>
              <div class="support-content">
                <h3>Toujours besoin d'aide ?</h3>
                <p>Notre équipe de support est disponible pour répondre à vos questions et résoudre vos problèmes rapidement.</p>
                <router-link to="/chats" class="support-btn">
                  <i class="bi bi-chat-dots"></i>
                  Contacter le support
                </router-link>
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
        { label: 'Accueil', to: '/' },
        { label: 'Tableau de bord', to: '/admindashboard' },
        { label: 'Catalogue', to: '/voitures/cataloguevoitures' },
        { label: 'Interventions', to: '/interventions/catalogue' },
        { label: 'Alertes', to: '/alertes' },
      ],
      categories: [
        { id: 'vehicles', name: 'Véhicules', icon: 'bi bi-car-front-fill', color: 'linear-gradient(135deg, #5B9BD5 0%, #4A7FB8 100%)', count: 8 },
        { id: 'interventions', name: 'Interventions', icon: 'bi bi-tools', color: 'linear-gradient(135deg, #F4A460 0%, #D4A574 100%)', count: 7 },
        { id: 'alerts', name: 'Alertes', icon: 'bi bi-bell-fill', color: 'linear-gradient(135deg, #E74C3C 0%, #C85A54 100%)', count: 6 },
        { id: 'reports', name: 'Rapports', icon: 'bi bi-file-earmark-text-fill', color: 'linear-gradient(135deg, #344966 0%, #546A88 100%)', count: 6 },
        { id: 'troubleshooting', name: 'Dépannage', icon: 'bi bi-wrench-adjustable', color: 'linear-gradient(135deg, #C85A54 0%, #A84843 100%)', count: 7 },
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

<style scoped>
.help-container {
  max-width: 1200px;
  margin: 0 auto;
  padding-bottom: 2rem;
}

/* Header with Background Animation */
.help-header {
  position: relative;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  border-radius: 20px;
  padding: 3.5rem 2.5rem;
  margin-bottom: 2.5rem;
  box-shadow: 0 8px 32px rgba(52, 73, 102, 0.2);
  overflow: hidden;
}

.header-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  animation: float 20s infinite ease-in-out;
}

.circle-1 {
  width: 300px;
  height: 300px;
  top: -100px;
  right: -50px;
  animation-delay: 0s;
}

.circle-2 {
  width: 200px;
  height: 200px;
  bottom: -50px;
  left: 10%;
  animation-delay: 5s;
}

.circle-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  left: -30px;
  animation-delay: 10s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
  }
}

.header-content {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  gap: 2rem;
  color: white;
}

.header-icon-wrapper {
  position: relative;
  flex-shrink: 0;
}

.header-icon-wrapper > i {
  font-size: 5rem;
  opacity: 0.95;
  display: block;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.icon-pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  animation: pulsate 2s infinite;
}

@keyframes pulsate {
  0% {
    transform: translate(-50%, -50%) scale(0.8);
    opacity: 1;
  }
  100% {
    transform: translate(-50%, -50%) scale(1.5);
    opacity: 0;
  }
}

.header-text h1 {
  font-size: 2.75rem;
  font-weight: 800;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.5px;
}

.header-text p {
  font-size: 1.15rem;
  margin: 0 0 1.5rem 0;
  opacity: 0.95;
  font-weight: 400;
}

.header-stats {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-top: 1.5rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.stat-number {
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.85;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.3);
}

/* Search Section */
.search-section {
  margin-bottom: 2.5rem;
}

.search-wrapper {
  position: relative;
  max-width: 700px;
  margin: 0 auto 1rem;
}

.search-wrapper i.bi-search {
  position: absolute;
  left: 1.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #748BAA;
  font-size: 1.3rem;
  z-index: 1;
}

.search-input {
  width: 100%;
  padding: 1.25rem 4rem 1.25rem 4rem;
  border: 2px solid #E8F0F7;
  border-radius: 16px;
  font-size: 1.05rem;
  transition: all 0.3s ease;
  background: white;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

.search-input:focus {
  outline: none;
  border-color: #344966;
  box-shadow: 0 4px 20px rgba(52, 73, 102, 0.15);
  transform: translateY(-2px);
}

.search-input::placeholder {
  color: #B4CDED;
}

.clear-btn {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: #F9FBFD;
  border: none;
  color: #748BAA;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  border-radius: 8px;
  width: 36px;
  height: 36px;
}

.clear-btn:hover {
  color: #344966;
  background: #E8F0F7;
}

.search-hint {
  text-align: center;
  color: #748BAA;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin: 0;
}

.search-hint i {
  color: #D4A574;
  font-size: 1rem;
}

/* Category Tabs - Card Style */
.category-tabs {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1rem;
  margin-bottom: 3rem;
}

.category-tab {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: white;
  border: 2px solid transparent;
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  position: relative;
  overflow: hidden;
}

.category-tab::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(52, 73, 102, 0.05) 0%, rgba(116, 139, 170, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.category-tab:hover::before {
  opacity: 1;
}

.category-tab:hover {
  border-color: #E8F0F7;
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.category-tab.active {
  border-color: #344966;
  box-shadow: 0 8px 24px rgba(52, 73, 102, 0.2);
  transform: translateY(-2px);
}

.category-tab.active::before {
  opacity: 1;
}

.tab-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  color: white;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.tab-content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.25rem;
}

.tab-name {
  font-weight: 600;
  color: #344966;
  font-size: 1rem;
  line-height: 1.2;
}

.tab-count {
  font-size: 0.8rem;
  color: #748BAA;
  font-weight: 500;
}

.category-tab.active .tab-name {
  color: #344966;
  font-weight: 700;
}

/* Content Section */
.content-section {
  animation: fadeIn 0.4s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.section-title {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 2rem;
  color: #344966;
  margin-bottom: 2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
}

.section-title i {
  font-size: 2.5rem;
  background: linear-gradient(135deg, #546A88 0%, #344966 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* FAQ List */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  background: white;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
  cursor: pointer;
  border: 2px solid transparent;
}

.faq-item:hover {
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  border-color: #E8F0F7;
}

.faq-item.active {
  box-shadow: 0 8px 24px rgba(52, 73, 102, 0.15);
  border-color: #344966;
}

.faq-question {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.75rem 2rem;
  font-weight: 600;
  color: #344966;
  font-size: 1.1rem;
  gap: 1rem;
}

.faq-question span {
  flex: 1;
}

.faq-question i {
  color: #546A88;
  font-size: 1.3rem;
  transition: all 0.3s ease;
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F9FBFD;
  border-radius: 8px;
}

.faq-item.active .faq-question i {
  transform: rotate(180deg);
  background: #344966;
  color: white;
}

.faq-answer {
  padding: 0 2rem 2rem 2rem;
  color: #546A88;
  line-height: 1.8;
  animation: slideDown 0.3s ease;
  font-size: 1.05rem;
}

@keyframes slideDown {
  from {
    opacity: 0;
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
  }
  to {
    opacity: 1;
    max-height: 500px;
    padding-bottom: 2rem;
  }
}

.faq-answer p {
  margin: 0;
}

/* Support Card */
.support-card {
  display: flex;
  gap: 2.5rem;
  align-items: center;
  background: linear-gradient(135deg, #748BAA 0%, #546A88 100%);
  border-radius: 20px;
  padding: 3rem;
  margin-top: 4rem;
  color: white;
  box-shadow: 0 8px 32px rgba(116, 139, 170, 0.3);
  position: relative;
  overflow: hidden;
}

.support-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 300px;
  height: 300px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
}

.support-icon {
  flex-shrink: 0;
  position: relative;
  z-index: 1;
}

.support-icon i {
  font-size: 5rem;
  opacity: 0.95;
  animation: pulse 2s infinite;
}

.support-content {
  position: relative;
  z-index: 1;
}

.support-content h3 {
  font-size: 1.75rem;
  margin: 0 0 0.75rem 0;
  font-weight: 700;
}

.support-content p {
  margin: 0 0 2rem 0;
  opacity: 0.95;
  line-height: 1.7;
  font-size: 1.05rem;
}

.support-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: white;
  color: #344966;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  font-size: 1rem;
}

.support-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.support-btn i {
  font-size: 1.2rem;
}

/* Responsive */
@media (max-width: 992px) {
  .category-tabs {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

@media (max-width: 768px) {
  .help-header {
    padding: 2.5rem 1.5rem;
  }

  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .header-icon-wrapper > i {
    font-size: 4rem;
  }

  .header-text h1 {
    font-size: 2rem;
  }

  .header-stats {
    justify-content: center;
    gap: 1.5rem;
  }

  .stat-number {
    font-size: 1.5rem;
  }

  .category-tabs {
    grid-template-columns: 1fr;
  }

  .support-card {
    flex-direction: column;
    text-align: center;
    padding: 2.5rem 2rem;
  }

  .support-icon i {
    font-size: 4rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .section-title i {
    font-size: 2rem;
  }
}
</style>
