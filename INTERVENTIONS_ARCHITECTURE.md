# Architecture Complète - Module Interventions

## Vue d'ensemble

Le module **Interventions** doit gérer :
- 📝 Création d'interventions (maintenance, réparations)
- 📋 Liste/catalogue des interventions
- 👁️ Détails d'une intervention
- ✏️ Modification d'interventions
- 🗑️ Suppression d'interventions
- 📊 Filtrage et recherche
- 📎 Gestion des documents associés

## Structure recommandée

```
resources/js/components/interventions/
├── catalogue.vue           # Liste de toutes les interventions
├── ajouter.vue            # Formulaire création intervention
├── details.vue            # Détails d'une intervention
└── modifier.vue           # (Optionnel) Formulaire modification

app/Http/Controllers/
└── InterventionController.php  ✅ (déjà créé)

app/Models/
├── Intervention.php       ✅ (déjà créé)
├── Voiture.php           ✅ (déjà créé)
└── Document.php          ✅ (déjà créé)
```

## Base de données

### Table: interventions ✅
```sql
- idIntervention (PK)
- type (string) - Ex: "Vidange", "Révision", "Réparation"
- date (date)
- cout (double)
- garage (string)
- remarques (text, nullable)
- voiture_id (FK vers voitures)
- timestamps
```

### Relations:
- `Intervention` belongsTo `Voiture` ✅
- `Intervention` hasMany `Documents` ✅

## Fonctionnalités par composant

### 1. catalogue.vue (Liste)
**Fonctionnalités:**
- ✅ Affichage tableau/cards de toutes les interventions
- ✅ Filtres: par voiture, par type, par date, par garage
- ✅ Recherche
- ✅ Tri (date, coût, type)
- ✅ Statistiques: coût total, nombre d'interventions
- ✅ Actions: voir détails, modifier, supprimer
- ✅ Bouton "Nouvelle intervention"
- ✅ Export PDF/Excel (optionnel)

**Affichage suggéré:**
```
┌─────────────────────────────────────────────┐
│  🔧 Interventions                           │
│  [+ Nouvelle intervention]     [Filtres 🔽] │
├─────────────────────────────────────────────┤
│                                             │
│  📊 Statistiques:                          │
│  ├─ Total interventions: 45                │
│  ├─ Coût total: 15,240 €                  │
│  └─ Coût moyen: 338 €                     │
│                                             │
│  🚗 Renault Clio - #1234                  │
│  ├─ Type: Vidange                         │
│  ├─ Date: 15 Oct 2025                     │
│  ├─ Garage: Garage Central                │
│  ├─ Coût: 85 €                            │
│  └─ [Voir] [Modifier] [Supprimer]         │
│                                             │
│  🚗 Peugeot 308 - #5678                   │
│  ├─ Type: Révision complète                │
│  ├─ Date: 10 Oct 2025                     │
│  ├─ Garage: AutoService Plus              │
│  ├─ Coût: 450 €                           │
│  └─ [Voir] [Modifier] [Supprimer]         │
└─────────────────────────────────────────────┘
```

### 2. ajouter.vue (Création)
**Fonctionnalités:**
- ✅ Formulaire multi-étapes (comme ajouter voiture)
- ✅ Sélection de la voiture (dropdown avec recherche)
- ✅ Type d'intervention (dropdown ou radio)
- ✅ Date de l'intervention (date picker)
- ✅ Coût
- ✅ Garage
- ✅ Remarques (textarea)
- ✅ Upload de documents (factures, photos)
- ✅ Récapitulatif avant validation
- ✅ Validation des champs

**Étapes suggérées:**
```
Étape 1: Informations générales
├─ Sélectionner la voiture
├─ Type d'intervention
├─ Date
└─ Garage

Étape 2: Détails financiers
├─ Coût
├─ Mode de paiement (optionnel)
└─ Remarques

Étape 3: Documents
├─ Facture (PDF/Image)
├─ Photos avant/après
└─ Autres documents

Étape 4: Validation
└─ Récapitulatif
```

### 3. details.vue (Détails)
**Fonctionnalités:**
- ✅ Affichage complet de l'intervention
- ✅ Informations de la voiture associée
- ✅ Liste des documents avec téléchargement
- ✅ Historique des modifications
- ✅ Boutons: Modifier, Supprimer, Imprimer
- ✅ Retour au catalogue

**Affichage suggéré:**
```
┌─────────────────────────────────────────────┐
│  🔧 Détails de l'intervention #123          │
│  [← Retour] [✏️ Modifier] [🗑️ Supprimer]    │
├─────────────────────────────────────────────┤
│                                             │
│  🚗 Véhicule:                              │
│  ├─ Renault Clio 4                        │
│  ├─ Immatriculation: AB-123-CD            │
│  └─ [Voir détails voiture]                 │
│                                             │
│  📝 Intervention:                          │
│  ├─ Type: Révision complète                │
│  ├─ Date: 15 octobre 2025                 │
│  ├─ Garage: Garage Central                │
│  ├─ Coût: 450,00 €                        │
│  └─ Remarques:                             │
│      "Changement des plaquettes de frein   │
│       et révision complète des 30 000 km"  │
│                                             │
│  📎 Documents (3):                         │
│  ├─ facture_revision.pdf                   │
│  ├─ photo_freins.jpg                       │
│  └─ rapport_technique.pdf                  │
│                                             │
│  📊 Statistiques véhicule:                 │
│  ├─ Nombre d'interventions: 8             │
│  ├─ Coût total maintenance: 2,450 €       │
│  └─ Dernière intervention: il y a 5 jours │
└─────────────────────────────────────────────┘
```

## Routes nécessaires

### Frontend (router.js)
```javascript
// À ajouter dans router.js
{
  path: '/interventions/catalogue',
  name: 'CatalogueInterventions',
  component: () => import('./components/interventions/catalogue.vue')
},
{
  path: '/interventions/ajouter',
  name: 'AjouterIntervention',
  component: () => import('./components/interventions/ajouter.vue')
},
{
  path: '/interventions/:id',
  name: 'DetailsIntervention',
  component: () => import('./components/interventions/details.vue'),
  props: true
},
{
  path: '/interventions/:id/modifier',
  name: 'ModifierIntervention',
  component: () => import('./components/interventions/modifier.vue'),
  props: true
}
```

### Backend (api.php) - À ajouter
```php
// Interventions (déjà partiellement en place)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/interventions', [InterventionController::class, 'index']);
    Route::post('/interventions', [InterventionController::class, 'store']);
    Route::get('/interventions/{id}', [InterventionController::class, 'show']);
    Route::put('/interventions/{id}', [InterventionController::class, 'update']);
    Route::delete('/interventions/{id}', [InterventionController::class, 'destroy']);
    
    // Stats et filtres
    Route::get('/interventions/stats/summary', [InterventionController::class, 'getStatsSummary']);
    Route::get('/interventions/voiture/{voitureId}', [InterventionController::class, 'getByVoiture']);
});
```

## Améliorations du contrôleur

### Méthodes à ajouter dans InterventionController.php

```php
// 1. Statistiques globales
public function getStatsSummary()
{
    $totalInterventions = Intervention::count();
    $totalCost = Intervention::sum('cout');
    $avgCost = Intervention::avg('cout');
    $recentInterventions = Intervention::where('date', '>=', now()->subDays(30))->count();
    
    return response()->json([
        'total' => $totalInterventions,
        'total_cost' => $totalCost,
        'average_cost' => $avgCost,
        'recent' => $recentInterventions
    ]);
}

// 2. Interventions par voiture
public function getByVoiture($voitureId)
{
    $interventions = Intervention::where('voiture_id', $voitureId)
        ->with('documents')
        ->orderBy('date', 'desc')
        ->get();
    
    return response()->json($interventions);
}

// 3. Filtrage avancé
public function filter(Request $request)
{
    $query = Intervention::with('voiture');
    
    if ($request->type) {
        $query->where('type', $request->type);
    }
    
    if ($request->voiture_id) {
        $query->where('voiture_id', $request->voiture_id);
    }
    
    if ($request->date_debut && $request->date_fin) {
        $query->whereBetween('date', [$request->date_debut, $request->date_fin]);
    }
    
    if ($request->cout_min && $request->cout_max) {
        $query->whereBetween('cout', [$request->cout_min, $request->cout_max]);
    }
    
    return response()->json($query->get());
}
```

## Types d'interventions suggérés

Créer une liste des types courants:
```javascript
const TYPES_INTERVENTION = [
  { value: 'vidange', label: 'Vidange', icon: 'bi-droplet' },
  { value: 'revision', label: 'Révision', icon: 'bi-tools' },
  { value: 'reparation', label: 'Réparation', icon: 'bi-wrench' },
  { value: 'pneus', label: 'Changement pneus', icon: 'bi-circle' },
  { value: 'freins', label: 'Freins', icon: 'bi-stop-circle' },
  { value: 'batterie', label: 'Batterie', icon: 'bi-lightning' },
  { value: 'climatisation', label: 'Climatisation', icon: 'bi-snow' },
  { value: 'controle', label: 'Contrôle technique', icon: 'bi-clipboard-check' },
  { value: 'autre', label: 'Autre', icon: 'bi-gear' }
];
```

## Intégration avec d'autres modules

### 1. Depuis Details Voiture
Afficher l'historique des interventions:
```vue
<!-- Dans detailsvoiture.vue -->
<section class="interventions-history">
  <h3>Historique des interventions</h3>
  <div v-for="intervention in voiture.interventions">
    <!-- Afficher résumé intervention -->
    <router-link :to="`/interventions/${intervention.id}`">
      Voir détails
    </router-link>
  </div>
  <button @click="$router.push(`/interventions/ajouter?voiture=${voiture.id}`)">
    + Nouvelle intervention
  </button>
</section>
```

### 2. Dashboard
Widgets interventions:
```
- Interventions à venir
- Coût maintenance ce mois
- Véhicules nécessitant maintenance
- Graphique coûts par mois
```

## Ordre de développement recommandé

### Phase 1: Base (Priorité haute)
1. ✅ **catalogue.vue** - Liste simple avec tableau
2. ✅ **ajouter.vue** - Formulaire de base (1 étape)
3. ✅ **details.vue** - Affichage complet
4. ✅ Routes frontend + backend
5. ✅ Test CRUD complet

### Phase 2: Amélioration (Priorité moyenne)
6. ✅ Filtres et recherche dans catalogue
7. ✅ Multi-étapes dans ajouter.vue
8. ✅ Upload documents
9. ✅ Statistiques et graphiques
10. ✅ Modification inline

### Phase 3: Avancé (Priorité basse)
11. ✅ Export PDF/Excel
12. ✅ Alertes maintenance préventive
13. ✅ Historique modifications
14. ✅ Planification interventions futures

## Checklist de développement

### Backend ✅
- [x] Model Intervention
- [x] Migration interventions
- [x] InterventionController CRUD
- [ ] Méthodes statistiques
- [ ] Méthodes filtrage
- [ ] Upload documents

### Frontend
- [ ] Créer dossier interventions/
- [ ] catalogue.vue
- [ ] ajouter.vue
- [ ] details.vue
- [ ] Routes dans router.js
- [ ] Liens dans sidebar/menu

### Intégration
- [ ] Lien depuis Details Voiture
- [ ] Widget dans Dashboard
- [ ] Navigation cohérente

## Recommandation finale

**Commencez par:**
1. Créer `catalogue.vue` (simple liste)
2. Créer `ajouter.vue` (formulaire basique)
3. Créer `details.vue` (affichage complet)

**Puis améliorez progressivement:**
- Ajoutez les filtres
- Améliorez le design
- Ajoutez les documents
- Intégrez avec le dashboard

Voulez-vous que je commence par créer ces composants Vue pour vous?
