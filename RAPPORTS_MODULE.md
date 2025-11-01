# Module Rapports - Documentation

## Vue d'ensemble
Le module Rapports permet à l'administrateur de générer et télécharger différents types de rapports sur la flotte de véhicules, les interventions, les utilisateurs et les données financières.

## Composants créés

### 1. Frontend - `rapports.vue`
**Emplacement**: `resources/js/components/rapports.vue`

#### Fonctionnalités:
- ✅ Dashboard avec statistiques en temps réel
- ✅ 6 types de rapports disponibles:
  1. **Rapport Véhicules** - Liste complète des véhicules
  2. **Rapport Interventions** - Historique des interventions
  3. **Rapport Utilisateurs** - Liste des utilisateurs et rôles
  4. **Rapport Financier** - Analyse des coûts
  5. **Rapport Complet** - Tous les rapports combinés
  6. **Rapport Personnalisé** - Rapport configurable avec filtres

#### Caractéristiques:
- 📊 Cartes statistiques interactives
- 💾 Export en format CSV (simule PDF/Excel)
- 🎨 Interface moderne avec design cohérent
- ⏳ Modal de progression de téléchargement
- 🔍 Aperçu des rapports (placeholder)
- 📅 Filtres personnalisés par période

### 2. Backend - `RapportController.php`
**Emplacement**: `app/Http/Controllers/RapportController.php`

#### Méthodes:
```php
- rapportVoitures($format)      // Rapport des véhicules
- rapportInterventions($format) // Rapport des interventions
- rapportUsers($format)         // Rapport des utilisateurs
- rapportFinancier($format)     // Rapport financier avec stats
- rapportComplet($format)       // Rapport global multi-sections
- rapportCustom($format)        // Rapport personnalisé
```

#### Format de sortie:
- CSV avec encodage UTF-8 (BOM)
- Séparateur: point-virgule (;)
- Compatible Excel et LibreOffice

### 3. Styles CSS
**Emplacement**: `resources/css/app.css`

#### Sections ajoutées:
```css
/* ============================================
   RAPPORTS / REPORTS PAGE
   ============================================ */
```

#### Classes principales:
- `.reports-page-header` - En-tête de page
- `.stats-overview` - Cartes statistiques
- `.report-card` - Carte de rapport individuelle
- `.download-modal` - Modal de progression
- `.custom-filters` - Filtres personnalisés

### 4. Routes

#### Frontend - `router.js`
```javascript
{ path: '/rapports', name: 'Rapports', component: Rapports }
```

#### Backend - `api.php`
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/rapports/voitures/{format}', [RapportController::class, 'rapportVoitures']);
    Route::get('/rapports/interventions/{format}', [RapportController::class, 'rapportInterventions']);
    Route::get('/rapports/users/{format}', [RapportController::class, 'rapportUsers']);
    Route::get('/rapports/financier/{format}', [RapportController::class, 'rapportFinancier']);
    Route::get('/rapports/complet/{format}', [RapportController::class, 'rapportComplet']);
    Route::post('/rapports/custom/{format}', [RapportController::class, 'rapportCustom']);
});
```

## Utilisation

### Accès à la page
1. Se connecter en tant qu'administrateur
2. Cliquer sur "Rapports" dans la sidebar
3. URL: `http://localhost:8000/rapports`

### Télécharger un rapport simple
1. Choisir le type de rapport (Véhicules, Interventions, etc.)
2. Cliquer sur "PDF" ou "Excel" (génère un CSV)
3. Le fichier se télécharge automatiquement

### Créer un rapport personnalisé
1. Aller à la section "Rapport Personnalisé"
2. Sélectionner la période (7 jours, 30 jours, 3 mois, année, tout)
3. Cocher les types de données à inclure:
   - ☑️ Véhicules
   - ☑️ Interventions
   - ☑️ Utilisateurs
   - ☑️ Données financières
4. Cliquer sur "PDF" ou "Excel"

## Structure des rapports CSV

### Rapport Véhicules
```csv
ID;Marque;Modèle;Immatriculation;Année;Kilométrage;Statut;État
1;Toyota;Corolla;ABC-123;2020;45000;disponible;bon
```

### Rapport Interventions
```csv
ID;Véhicule;Type;Description;Date;Statut;Coût;Créé le
1;Toyota Corolla;Révision;Vidange;2025-01-15;terminee;500;2025-01-10
```

### Rapport Financier
```csv
ID;Véhicule;Type;Date;Coût (MAD);Statut
...
STATISTIQUES
Coût Total;;;;15000;MAD
Nombre d'interventions;;;;30;
Coût Moyen;;;;500;MAD
```

### Rapport Complet
```csv
=== RAPPORT VÉHICULES ===
...véhicules...

=== RAPPORT INTERVENTIONS ===
...interventions...

=== RAPPORT UTILISATEURS ===
...utilisateurs...

=== STATISTIQUES GLOBALES ===
Total Véhicules;25
Coût Total Interventions;15000 MAD
```

## Mise à jour de la Sidebar

### Fichiers à mettre à jour
Pour que le lien "Rapports" apparaisse dans la sidebar, ajoutez dans les `menuItems`:

```javascript
menuItems = [
  { to: "/admindashboard", icon: "bi-speedometer2", label: "Dashboard" },
  { to: "/cataloguevoitures", icon: "bi-car-front", label: "Véhicules" },
  { to: "/interventions/catalogue", icon: "bi-tools", label: "Interventions" },
  { to: "/users", icon: "bi-people", label: "Utilisateurs" },
  { to: "/rapports", icon: "bi-file-earmark-bar-graph", label: "Rapports" }, // ← NOUVEAU
  { to: "/chats", icon: "bi-chat-dots", label: "Messages" },
  { to: "/profile", icon: "bi-person-circle", label: "Profile" },
];
```

### Composants concernés:
- ✅ `rapports.vue` (déjà inclus)
- ⚠️ `admindashboard.vue` (à mettre à jour)
- ⚠️ `profile.vue` (à mettre à jour)
- ⚠️ `users.vue` (à mettre à jour)
- ⚠️ `chats.vue` (à mettre à jour)
- ⚠️ Autres pages admin...

## Améliorations futures

### Phase 2 - PDF/Excel natifs
Pour générer de vrais PDF et Excel, installer:

```bash
# PDF
composer require barryvdh/laravel-dompdf

# Excel
composer require maatwebsite/excel
```

Puis créer les classes Export et les vues Blade pour les templates PDF.

### Phase 3 - Graphiques
Ajouter des graphiques avec Chart.js:
- Évolution des coûts par mois
- Répartition des interventions par type
- Taux d'utilisation des véhicules

### Phase 4 - Rapports programmés
- Envoi automatique par email
- Génération quotidienne/hebdomadaire/mensuelle
- Notifications aux admins

## Tests

### Test manuel
1. ✅ Accès à la page `/rapports`
2. ✅ Affichage des statistiques
3. ✅ Téléchargement de chaque type de rapport
4. ✅ Filtres personnalisés
5. ✅ Modal de progression
6. ✅ Responsive design

### Commandes utiles
```bash
# Vérifier les routes
php artisan route:list --path=rapports

# Effacer le cache
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

## Support
Pour toute question ou problème:
1. Vérifier que l'utilisateur est authentifié en tant qu'admin
2. Vérifier les routes API dans `routes/api.php`
3. Consulter les logs Laravel: `storage/logs/laravel.log`
4. Tester les endpoints avec Postman

## Notes importantes
- ⚠️ Les rapports sont actuellement en CSV (pas de vraie génération PDF/Excel)
- ⚠️ Seuls les admins peuvent accéder à cette page
- ✅ Format CSV compatible Excel avec BOM UTF-8
- ✅ Séparateur point-virgule pour Excel français
- ✅ Design responsive et moderne
