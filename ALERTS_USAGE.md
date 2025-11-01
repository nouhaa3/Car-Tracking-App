# Modern Alert System - Usage Guide

## 🎨 Features
- ✅ Professional toast notifications (Success, Error, Warning, Info)
- ✅ Beautiful confirmation dialogs
- ✅ Matches your app's color theme
- ✅ Smooth animations
- ✅ Auto-dismiss with progress bar
- ✅ Fully responsive

## 📦 How to Use

### 1. Import in Your Component

```javascript
import alerts from '@/utils/alerts';
```

### 2. Toast Notifications

Replace all `alert()` calls with these beautiful toasts:

```javascript
// Success
alerts.success('Succès!', 'Véhicule ajouté avec succès');

// Error
alerts.error('Erreur', 'Impossible de sauvegarder');

// Warning
alerts.warning('Attention', 'Cette action est irréversible');

// Info
alerts.info('Info', 'Données synchronisées');

// Custom duration (in milliseconds)
alerts.success('Sauvegardé', 'Données mises à jour', 6000);
```

### 3. Confirmation Dialogs

Replace all `confirm()` calls:

**OLD WAY:**
```javascript
if (!confirm("Voulez-vous vraiment supprimer ce véhicule ?")) return;
```

**NEW WAY:**
```javascript
const confirmed = await alerts.confirmDelete('ce véhicule');
if (!confirmed) return;
```

**More examples:**
```javascript
// Delete confirmation
const confirmed = await alerts.confirmDelete('cette intervention');
if (confirmed) {
  // Do delete
}

// General confirmation
const confirmed = await alerts.confirmAction(
  'Générer les alertes ?',
  'Cette action va créer des alertes pour tous les véhicules'
);

// Custom confirmation
const confirmed = await alerts.confirm({
  title: 'Marquer comme traitée ?',
  message: 'L\'alerte sera archivée',
  confirmText: 'Confirmer',
  cancelText: 'Annuler',
  type: 'info' // danger, warning, or info
});
```

## 🔄 Migration Examples

### Example 1: Delete Vehicle

**Before:**
```javascript
async deleteVoiture() {
  if (!confirm("Voulez-vous vraiment supprimer ce véhicule ?")) return;
  
  try {
    await axios.delete(`/api/voitures/${this.id}`);
    alert("Véhicule supprimé !");
    this.$router.push("/voitures");
  } catch (error) {
    alert("Erreur : " + error.message);
  }
}
```

**After:**
```javascript
import alerts from '@/utils/alerts';

async deleteVoiture() {
  const confirmed = await alerts.confirmDelete('ce véhicule');
  if (!confirmed) return;
  
  try {
    await axios.delete(`/api/voitures/${this.id}`);
    alerts.success('Supprimé!', 'Le véhicule a été supprimé avec succès');
    this.$router.push("/voitures");
  } catch (error) {
    alerts.error('Erreur', 'Impossible de supprimer: ' + error.message);
  }
}
```

### Example 2: Add Car

**Before:**
```javascript
async addCar() {
  try {
    await axios.post('/api/voitures', this.form);
    alert('Voiture ajoutée avec succès');
    this.$router.push('/voitures');
  } catch (error) {
    alert('Erreur');
  }
}
```

**After:**
```javascript
import alerts from '@/utils/alerts';

async addCar() {
  try {
    await axios.post('/api/voitures', this.form);
    alerts.success(
      'Véhicule ajouté!', 
      `${this.form.marque} ${this.form.modele} a été ajouté au catalogue`
    );
    this.$router.push('/voitures');
  } catch (error) {
    alerts.error('Erreur', error.response?.data?.message || 'Impossible d\'ajouter le véhicule');
  }
}
```

### Example 3: Update User

**Before:**
```javascript
async updateUser() {
  try {
    await axios.put(`/api/users/${this.userId}`, this.userData);
    alert('Utilisateur mis à jour avec succès');
  } catch (error) {
    alert('Erreur lors de la mise à jour');
  }
}
```

**After:**
```javascript
import alerts from '@/utils/alerts';

async updateUser() {
  try {
    await axios.put(`/api/users/${this.userId}`, this.userData);
    alerts.success('Mis à jour!', 'Les informations ont été mises à jour');
  } catch (error) {
    alerts.error('Erreur', 'Impossible de mettre à jour l\'utilisateur');
  }
}
```

### Example 4: Generate Alerts

**Before:**
```javascript
async generateAlerts() {
  if (!confirm("Voulez-vous générer de nouvelles alertes pour tous les véhicules ?")) {
    return;
  }
  
  try {
    await axios.post('/api/alertes/generate');
    alert('Alertes générées avec succès');
  } catch (error) {
    alert('Erreur');
  }
}
```

**After:**
```javascript
import alerts from '@/utils/alerts';

async generateAlerts() {
  const confirmed = await alerts.confirmAction(
    'Générer les alertes ?',
    'De nouvelles alertes seront créées pour tous les véhicules'
  );
  
  if (!confirmed) return;
  
  try {
    await axios.post('/api/alertes/generate');
    alerts.success('Alertes générées!', 'Les alertes ont été créées avec succès');
  } catch (error) {
    alerts.error('Erreur', 'Impossible de générer les alertes');
  }
}
```

## 📍 Files to Update

Here are the files that currently use `alert()` or `confirm()`:

1. **voitures/detailsvoiture.vue** ✅ (Already updated!)
2. **interventions/catalogue.vue** - Delete confirmation
3. **interventions/details.vue** - Delete confirmation
4. **alertes/catalogue.vue** - Generate alerts, mark as processed, delete
5. **alertes/details.vue** - Mark as processed, delete
6. **users.vue** - Password validation, CRUD operations
7. **profile.vue** - Password validation, profile update
8. **chats.vue** - Delete message confirmation
9. **home.vue** - Message sent notifications
10. **rapports.vue** - Error notifications

## 🎨 Styling

The alerts automatically match your app's color scheme:
- **Success**: Green (#BFCC94)
- **Error**: Red (#C85A54)
- **Warning**: Orange (#D4A574)
- **Info**: Blue (#748BAA)

## 💡 Tips

1. **Keep it concise**: Short titles (2-4 words), clear messages
2. **Be specific**: Include relevant details (vehicle name, user name, etc.)
3. **Use appropriate types**: Match the alert type to the action
4. **Await confirmations**: Always use `await` with confirm methods
5. **Handle errors**: Always show error alerts in catch blocks

## ✨ Next Steps

Replace all `alert()` and `confirm()` calls in your codebase with the new system for a professional, consistent user experience!
