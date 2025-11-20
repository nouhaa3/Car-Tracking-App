# Role-Based Access Control (RBAC) Configuration

**Date**: November 19, 2025  
**Status**: ✅ Complete

---

## 📋 Role Definitions

### Admin (Full Access)
- **Role Name in DB**: `Admin`
- **Access Level**: Complete system control

### Agent (Limited Management)
- **Role Name in DB**: `Agent`
- **Access Level**: Document management only

### Technicien (Maintenance Focus)
- **Role Name in DB**: `Technicien`
- **Access Level**: Alerts and reports

---

## 🔐 Access Matrix

| Feature | Admin | Agent | Technicien |
|---------|-------|-------|------------|
| **Users Management** |
| List users | ✅ | ❌ | ❌ |
| Add user | ✅ | ❌ | ❌ |
| Modify user | ✅ | ❌ | ❌ |
| Delete user | ✅ | ❌ | ❌ |
| Change user role | ✅ | ❌ | ❌ |
| **Roles Management** |
| Assign roles | ✅ | ❌ | ❌ |
| Manage roles | ✅ | ❌ | ❌ |
| **Vehicles** |
| List vehicles | ✅ | ✅ | ✅ |
| View vehicle details | ✅ | ✅ | ✅ |
| Add vehicle | ✅ | ❌ | ❌ |
| Modify vehicle | ✅ | ❌ | ❌ |
| Delete vehicle | ✅ | ❌ | ❌ |
| Upload vehicle image | ✅ | ❌ | ❌ |
| **Interventions** |
| List interventions | ✅ | ✅ | ✅ |
| View intervention details | ✅ | ✅ | ✅ |
| Add intervention | ✅ | ❌ | ❌ |
| Modify intervention | ✅ | ❌ | ❌ |
| Delete intervention | ✅ | ❌ | ❌ |
| **Documents** |
| View documents | ✅ | ✅ | ✅ |
| Upload documents | ✅ | ✅ | ❌ |
| Download documents | ✅ | ✅ | ✅ |
| Delete documents | ✅ | ✅ | ✅ |
| **Alerts** |
| View alerts | ✅ | ✅ | ✅ |
| Create alerts | ✅ | ❌ | ❌ |
| Mark as processed | ✅ | ❌ | ✅ |
| Delete alerts | ✅ | ❌ | ❌ |
| **Maintenance History** |
| View history | ✅ | ✅ | ✅ |
| **Reports** |
| Export vehicles report | ✅ | ❌ | ✅ |
| Export interventions report | ✅ | ❌ | ✅ |
| Export users report | ✅ | ❌ | ❌ |
| Export financial report | ✅ | ❌ | ❌ |
| Export complete report | ✅ | ❌ | ❌ |
| **Dashboard** |
| View dashboard | ✅ | ✅ | ✅ |
| **Authentication** |
| Login/Logout | ✅ | ✅ | ✅ |
| Password reset | ✅ | ✅ | ✅ |

---

## 🛠️ Technical Implementation

### Middleware Files

#### 1. `app/Http/Middleware/RoleMiddleware.php`
```php
public function handle(Request $request, Closure $next, $role): Response
{
    if (!$request->user() || $request->user()->role->nomRole !== $role) {
        return response()->json(['message' => 'Accès réfusé !'], 403);
    }
    return $next($request);
}
```

#### 2. `app/Http/Middleware/AdminOrAgentMiddleware.php`
```php
public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();
    if (!$user || !in_array($user->role->nomRole, ['Admin', 'Agent'])) {
        return response()->json(['message' => 'Accès réfusé.'], 403);
    }
    return $next($request);
}
```

#### 3. `app/Http/Middleware/AdminOrTechnicienMiddleware.php`
```php
public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();
    if (!$user || !in_array($user->role->nomRole, ['Admin', 'Technicien'])) {
        return response()->json(['message' => 'Accès réfusé.'], 403);
    }
    return $next($request);
}
```

### Middleware Registration

**File**: `app/Http/Kernel.php`

```php
protected $routeMiddleware = [
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'admin.or.agent' => \App\Http\Middleware\AdminOrAgentMiddleware::class,
    'admin.or.technicien' => \App\Http\Middleware\AdminOrTechnicienMiddleware::class,
];
```

---

## 📍 API Routes Structure

### Admin-Only Routes
```php
Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    // Users
    GET    /api/users
    POST   /api/users
    GET    /api/users/{id}
    PUT    /api/users/{id}
    DELETE /api/users/{id}
    
    // Roles
    GET    /api/roles
    POST   /api/roles
    PUT    /api/roles/{id}
    DELETE /api/roles/{id}
    
    // Vehicles (CRUD)
    POST   /api/voitures
    PUT    /api/voitures/{id}
    DELETE /api/voitures/{id}
    POST   /api/voitures/{id}/image
    DELETE /api/voitures/{id}/image
    
    // Interventions (CRUD)
    POST   /api/interventions
    PUT    /api/interventions/{id}
    DELETE /api/interventions/{id}
    
    // Alerts (Management)
    POST   /api/alertes
    PUT    /api/alertes/{id}
    DELETE /api/alertes/{id}
    POST   /api/alertes/generate
    
    // Reports (All types)
    GET    /api/rapports/users/{format}
    GET    /api/rapports/financier/{format}
    GET    /api/rapports/complet/{format}
    POST   /api/rapports/custom/{format}
    
    // Contact Messages
    GET    /api/contact-messages
    POST   /api/contact-messages/{id}/reply
    DELETE /api/contact-messages/{id}
    
    // Trash/Corbeille
    GET    /api/corbeille/voitures
    GET    /api/corbeille/interventions
    POST   /api/corbeille/voitures/{id}/restore
    DELETE /api/corbeille/voitures/{id}/force
});
```

### Admin + Agent Routes
```php
Route::middleware(['auth:sanctum', 'admin.or.agent'])->group(function () {
    // Documents Upload
    POST   /api/voitures/{voitureId}/documents
});
```

### Admin + Technicien Routes
```php
Route::middleware(['auth:sanctum', 'admin.or.technicien'])->group(function () {
    // Alerts
    PATCH  /api/alertes/{id}/resolve
    
    // Reports (Limited)
    GET    /api/rapports/voitures/{format}
    GET    /api/rapports/interventions/{format}
});
```

### All Authenticated Users
```php
Route::middleware('auth:sanctum')->group(function () {
    // Vehicles (Read-only)
    GET    /api/voitures
    GET    /api/voitures/{id}
    GET    /api/voitures/count-by-status
    GET    /api/voitures/top-expensive
    
    // Interventions (Read-only)
    GET    /api/interventions
    GET    /api/interventions/{id}
    GET    /api/interventions/recent-history
    
    // Documents (Read-only)
    GET    /api/voitures/{voitureId}/documents
    GET    /api/documents-vehicule/{id}/download
    
    // Alerts (Read-only)
    GET    /api/alertes
    GET    /api/alertes/{id}
    GET    /api/alertes/stats
    
    // Maintenance History
    GET    /api/voitures/{voitureId}/historique
    GET    /api/maintenance/total-cost
    
    // Notifications
    GET    /api/notifications
    PATCH  /api/notifications/{id}/read
    DELETE /api/notifications/{id}
    
    // Settings
    GET    /api/settings
    POST   /api/settings
    
    // Profile
    GET    /api/me
});
```

---

## 🧪 Testing Access Control

### Test Credentials (Default Seeder)

```bash
# Admin
Email: admin@cartrackingapp.com
Password: admin1234

# Agent
Email: agent@cartrackingapp.com
Password: agent1234

# Technicien
Email: technicien@cartrackingapp.com
Password: technicien1234
```

### Test Scenarios

#### 1. Test Admin Full Access
```bash
# Login as Admin
POST /api/login
{
  "email": "admin@cartrackingapp.com",
  "password": "admin1234"
}

# Try creating a user (should succeed)
POST /api/users
Authorization: Bearer {admin_token}
{
  "nom": "Test",
  "prenom": "User",
  "email": "test@example.com",
  "password": "password123",
  "role_id": 2
}
```

#### 2. Test Agent Limited Access
```bash
# Login as Agent
POST /api/login
{
  "email": "agent@cartrackingapp.com",
  "password": "agent1234"
}

# Try uploading document (should succeed)
POST /api/voitures/1/documents
Authorization: Bearer {agent_token}

# Try creating user (should fail - 403)
POST /api/users
Authorization: Bearer {agent_token}
```

#### 3. Test Technicien Access
```bash
# Login as Technicien
POST /api/login
{
  "email": "technicien@cartrackingapp.com",
  "password": "technicien1234"
}

# Try marking alert as resolved (should succeed)
PATCH /api/alertes/1/resolve
Authorization: Bearer {technicien_token}

# Try uploading document (should fail - 403)
POST /api/voitures/1/documents
Authorization: Bearer {technicien_token}
```

---

## 🔍 Frontend Integration

### Vue Router Guards

Update your Vue router to check user roles:

```javascript
// router/index.js
router.beforeEach((to, from, next) => {
  const user = store.state.user;
  
  if (to.meta.requiresAuth && !user) {
    return next('/login');
  }
  
  if (to.meta.roles && !to.meta.roles.includes(user.role.nomRole)) {
    return next('/dashboard'); // Redirect to dashboard if no permission
  }
  
  next();
});
```

### Route Meta Example

```javascript
{
  path: '/users',
  component: UsersManagement,
  meta: {
    requiresAuth: true,
    roles: ['Admin'] // Only Admin can access
  }
},
{
  path: '/documents',
  component: Documents,
  meta: {
    requiresAuth: true,
    roles: ['Admin', 'Agent'] // Admin and Agent can access
  }
}
```

---

## ✅ Implementation Checklist

- [x] Create RoleMiddleware
- [x] Create AdminOrAgentMiddleware
- [x] Create AdminOrTechnicienMiddleware
- [x] Register middleware in Kernel
- [x] Update API routes with role-based access
- [x] Add image upload/delete routes
- [x] Organize routes by role groups
- [x] Document access matrix
- [ ] Add frontend route guards (Vue)
- [ ] Test all role permissions
- [ ] Update API documentation

---

## 🚨 Security Notes

1. **Always validate on backend**: Never trust frontend role checks alone
2. **Token expiration**: Sanctum tokens should have reasonable expiration
3. **HTTPS required**: Always use HTTPS in production
4. **Rate limiting**: Consider adding rate limiting to prevent abuse
5. **Audit logging**: Consider logging all admin actions

---

## 📝 Next Steps

1. **Email Notifications**: Configure SMTP for alert emails
2. **Auto-Generate Alerts**: Create scheduled task for alert generation
3. **Activity Logging**: Implement audit trail for admin actions
4. **API Documentation**: Generate Swagger/OpenAPI documentation
5. **Testing**: Write integration tests for role-based access

---

## 📞 Support

For questions or issues with role-based access control:
- Check middleware logs in `storage/logs/laravel.log`
- Verify user has correct role assigned in database
- Ensure Sanctum tokens are valid and not expired
