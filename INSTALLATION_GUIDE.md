# 🚀 Guide d'installation rapide

## Étape 1: Exécuter les migrations

### Option A: Via Artisan (Recommandé)
```bash
cd C:\laragon\www\cartrackingapp
php artisan migrate
```

### Option B: Via Laragon Terminal
1. Ouvrir Laragon
2. Clic droit sur "Apache" → "Terminal"
3. Exécuter:
```bash
php artisan migrate
```

### Option C: SQL Manuel (si artisan ne fonctionne pas)
1. Ouvrir phpMyAdmin dans Laragon
2. Sélectionner votre base de données `cartrackingapp`
3. Onglet "SQL"
4. Copier/coller le contenu de `database/manual_migrations.sql`
5. Cliquer "Exécuter"

## Étape 2: Créer le lien symbolique pour storage

```bash
php artisan storage:link
```

Cela permet d'accéder aux fichiers uploadés via URL publique.

## Étape 3: Tester les nouvelles fonctionnalités

### Test 1: Documents Véhicule
1. Aller sur `/voitures/cataloguevoitures`
2. Cliquer sur un véhicule pour voir ses détails
3. Cliquer sur l'onglet "📄 Documents"
4. Cliquer "Ajouter un document"
5. Sélectionner type (Carte grise, Assurance, etc.)
6. Uploader un fichier PDF/JPG/PNG
7. Ajouter date d'expiration (optionnel)
8. Cliquer "Ajouter"
9. Vérifier que le document apparaît
10. Tester téléchargement et suppression

### Test 2: Historique Véhicule
1. Sur la même page détails véhicule
2. Cliquer sur l'onglet "📊 Historique"
3. Vérifier que les interventions et alertes apparaissent
4. Vérifier l'ordre chronologique (plus récent en haut)
5. Vérifier les icônes (🔧 intervention, ⚠️ alerte)

### Test 3: Corbeille
1. Supprimer un véhicule depuis le catalogue
2. Aller dans le menu Sidebar → "🗑️ Corbeille"
3. Vérifier que le véhicule apparaît dans l'onglet "Véhicules"
4. Cliquer "♻️ Restaurer"
5. Vérifier que le véhicule réapparaît dans le catalogue

### Test 4: Duplication Intervention
À implémenter dans l'interface:
1. Aller sur `/interventions/catalogue`
2. Cliquer sur une intervention
3. Cliquer bouton "Dupliquer" (à ajouter)
4. Vérifier qu'une copie est créée avec date actuelle

## Étape 4: Vérifier les erreurs

Ouvrir la console développeur (F12) et vérifier:
- Aucune erreur 404 sur les routes API
- Les uploads de fichiers fonctionnent
- Les composants Vue se chargent correctement

## Étape 5: Optimisation (Optionnel)

### Clear cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild frontend
```bash
npm run build
```

## 🔍 Troubleshooting

### Problème: Migrations échouent
**Solution**: Utiliser `database/manual_migrations.sql` via phpMyAdmin

### Problème: Documents ne s'uploadent pas
**Solution**: 
```bash
php artisan storage:link
chmod -R 775 storage
```

### Problème: Erreur 404 sur routes API
**Solution**:
```bash
php artisan route:clear
php artisan optimize
```

### Problème: Composants Vue ne se chargent pas
**Solution**:
```bash
npm install
npm run dev
```

### Problème: Images de documents ne s'affichent pas
**Vérifier**:
1. `storage/app/public/documents/` existe
2. `public/storage` est un lien symbolique vers `storage/app/public`
3. Permissions sur dossier storage (775)

## ✅ Checklist de validation

- [ ] Migrations exécutées avec succès
- [ ] storage:link créé
- [ ] Onglet Documents visible dans détails véhicule
- [ ] Upload de document fonctionne
- [ ] Téléchargement de document fonctionne
- [ ] Onglet Historique visible
- [ ] Timeline affiche interventions + alertes
- [ ] Lien Corbeille dans Sidebar
- [ ] Page Corbeille accessible
- [ ] Restauration de véhicule fonctionne
- [ ] Aucune erreur dans console navigateur
- [ ] Aucune erreur dans logs Laravel (`storage/logs/laravel.log`)

## 📊 Vérification base de données

Exécuter dans phpMyAdmin:
```sql
-- Vérifier nouvelle table
DESC documents_vehicule;

-- Vérifier soft deletes
DESC voitures;
DESC interventions;

-- Vérifier assigned_to
DESC interventions;

-- Compter documents uploadés
SELECT COUNT(*) FROM documents_vehicule;

-- Voir véhicules supprimés
SELECT * FROM voitures WHERE deleted_at IS NOT NULL;
```

## 🎯 Fonctionnalités à ajouter manuellement

### 1. Bouton Dupliquer dans DetailsIntervention.vue
Ajouter dans le template:
```vue
<button @click="duplicateIntervention" class="btn-duplicate">
  📋 Dupliquer
</button>
```

Ajouter dans methods:
```javascript
async duplicateIntervention() {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post(
      `http://localhost:8000/api/interventions/${this.interventionId}/duplicate`,
      {},
      { headers: { Authorization: `Bearer ${token}` } }
    );
    alertSuccess('Intervention dupliquée');
    this.$router.push(`/interventions/${response.data.intervention.idIntervention}`);
  } catch (error) {
    alertError('Erreur lors de la duplication');
  }
}
```

### 2. Dropdown Assigné à dans AjouterIntervention.vue
Ajouter dans le formulaire:
```vue
<div class="form-group">
  <label>Assigné à (optionnel)</label>
  <select v-model="form.assigned_to">
    <option value="">Non assigné</option>
    <option v-for="user in users" :key="user.id" :value="user.id">
      {{ user.nom }} {{ user.prenom }}
    </option>
  </select>
</div>
```

Charger les utilisateurs dans mounted():
```javascript
async mounted() {
  const token = localStorage.getItem('token');
  const response = await axios.get('http://localhost:8000/api/users', {
    headers: { Authorization: `Bearer ${token}` }
  });
  this.users = response.data;
}
```

## 🎉 Félicitations!

Toutes les fonctionnalités HIGH et MEDIUM priority sont maintenant implémentées!

Prochaine étape: Ajouter Import/Export Excel (LOW priority)
