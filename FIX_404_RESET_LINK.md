# Fix: Error 404 Not Found - Reset Password Link

## Le problème

Quand vous copiez le lien de réinitialisation, vous obtenez une erreur 404. Cela arrive parce que l'URL générée ne correspond pas à votre configuration frontend.

## Solutions

### Solution 1: Utiliser le bouton vert "Cliquer ici"

J'ai ajouté un gros bouton vert avec le texte "Cliquer ici pour réinitialiser votre mot de passe". 

**Utilisez ce bouton au lieu de copier le lien !**

Ce bouton utilise un lien relatif qui fonctionne automatiquement avec votre configuration.

### Solution 2: Vérifier votre port frontend

#### Trouver sur quel port votre application tourne:

1. **Vite (port par défaut: 5173)**
   ```bash
   npm run dev
   ```
   Regardez la sortie console, elle affiche:
   ```
   VITE v4.x.x  ready in xxx ms
   ➜  Local:   http://localhost:5173/
   ```

2. **Laravel built-in server (port 8000)**
   ```bash
   php artisan serve
   ```

3. **Laragon (port 80)**
   Si vous utilisez Laragon directement:
   ```
   http://cartrackingapp.test
   ```

### Solution 3: Configurer l'URL correcte

Une fois que vous connaissez votre URL frontend, mettez-la à jour:

#### Option A: Via variable d'environnement

Créez ou modifiez le fichier `.env` à la racine du projet:

```env
FRONTEND_URL=http://localhost:5173
```

Puis modifiez `PasswordResetController.php`:
```php
$frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
```

#### Option B: Directement dans le code

Éditez `app/Http/Controllers/PasswordResetController.php`:

Ligne 47-48, remplacez:
```php
$frontendUrl = $request->header('Origin', 'http://localhost:5173');
```

Par votre URL réelle:
```php
$frontendUrl = 'http://localhost:5173';  // Vite
// OU
$frontendUrl = 'http://localhost:8000';  // Laravel serve
// OU
$frontendUrl = 'http://cartrackingapp.test';  // Laragon
```

## Test complet

### Étape 1: Déterminer votre URL

Ouvrez votre navigateur et regardez l'URL quand vous êtes sur la page de login:

- Si c'est `http://localhost:5173/login` → utilisez `http://localhost:5173`
- Si c'est `http://localhost:8000/login` → utilisez `http://localhost:8000`
- Si c'est `http://cartrackingapp.test/login` → utilisez `http://cartrackingapp.test`

### Étape 2: Tester avec le bouton vert

1. Allez sur `/login`
2. Cliquez "Mot de passe oublié ?"
3. Entrez un email valide
4. **Cliquez sur le gros bouton VERT** "Cliquer ici pour réinitialiser votre mot de passe"
5. Vous devriez être redirigé vers la page de réinitialisation

### Étape 3: Si le bouton vert ne fonctionne pas

Cela signifie que votre router Vue n'est pas configuré correctement. Vérifiez:

1. **Vérifiez que le serveur dev tourne:**
   ```bash
   npm run dev
   ```

2. **Vérifiez les routes dans `router.js`:**
   ```javascript
   { path: '/reset-password', name: 'ResetPassword', component: ResetPassword }
   ```

3. **Testez manuellement:**
   Tapez dans votre navigateur:
   ```
   http://localhost:5173/reset-password?token=test&email=test@test.com
   ```
   
   Vous devriez voir la page de réinitialisation (même si le token est invalide).

## Configuration recommandée pour Laragon + Vite

Si vous utilisez Laragon avec Vite, voici la configuration recommandée:

### 1. Démarrez Vite en mode développement:
```bash
npm run dev
```

### 2. Accédez à l'application via Vite:
```
http://localhost:5173
```

### 3. Le backend Laravel tourne sur:
```
http://127.0.0.1:8000
```

### 4. Dans `PasswordResetController.php`, utilisez:
```php
$frontendUrl = 'http://localhost:5173';
```

## Vérification rapide

Pour tester si tout fonctionne, ouvrez votre console navigateur (F12) sur la page "Mot de passe oublié" et exécutez:

```javascript
console.log(window.location.origin);
```

Le résultat vous dira quelle URL utiliser !

## URL du lien généré vs URL frontend

Le problème vient d'une différence entre:
- **Backend Laravel**: `http://127.0.0.1:8000`
- **Frontend Vite**: `http://localhost:5173`

Le lien est généré avec l'URL du backend mais doit pointer vers le frontend.

## En résumé

**Solution la plus simple**: 
1. Utilisez le **bouton vert** au lieu de copier le lien
2. Si ça ne marche pas, configurez la bonne URL dans le contrôleur

**Solution permanente**:
1. Ajoutez `FRONTEND_URL=http://localhost:5173` dans `.env`
2. Modifiez le contrôleur pour utiliser `env('FRONTEND_URL')`

---

Si vous avez toujours des problèmes, partagez:
1. L'URL que vous voyez dans votre navigateur quand vous êtes sur la page login
2. Le lien généré (visible dans le message de succès)
3. Comment vous lancez votre application (npm run dev, php artisan serve, etc.)
