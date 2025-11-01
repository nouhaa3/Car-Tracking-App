# Alertes Module - Complete Implementation
## Intelligent Alert System for Fleet Management

**Date**: October 20, 2025  
**Version**: 1.0  
**Status**: ✅ Fully Implemented

---

## 📋 TABLE OF CONTENTS

1. [Overview](#overview)
2. [Features Implemented](#features-implemented)
3. [Backend Architecture](#backend-architecture)
4. [Frontend Components](#frontend-components)
5. [API Endpoints](#api-endpoints)
6. [Alert Generation Logic](#alert-generation-logic)
7. [Command Usage](#command-usage)
8. [Testing Guide](#testing-guide)
9. [Future Enhancements](#future-enhancements)

---

## 🎯 OVERVIEW

The Alertes module provides an intelligent, automated alert system for vehicle fleet management. It automatically generates alerts based on:

- **Kilometrage thresholds** (oil change, tire rotation, major service)
- **Date-based deadlines** (technical inspection, annual service)
- **Vehicle condition** (poor state, prolonged maintenance)
- **Cost analysis** (high maintenance costs, recurring issues)

**Key Benefits**:
- ⏰ Proactive maintenance scheduling
- 💰 Cost optimization
- 🚫 Zero missed deadlines
- 📊 Priority-based alerting (Critique, Haute, Moyenne, Faible)
- 🔄 Automatic daily generation via Laravel scheduler

---

## ✅ FEATURES IMPLEMENTED

### Backend Services
- [x] **AlerteService** - Intelligent alert generation engine
- [x] **AlerteController** - Full REST API with CRUD operations
- [x] **GenerateAlertsCommand** - Artisan command for scheduled execution
- [x] **Priority calculation** - Dynamic priority assignment (critique/haute/moyenne/faible)
- [x] **Alert resolution tracking** - Mark alerts as treated

### Frontend Components
- [x] **Catalogue** - Alert list with filtering and statistics
- [x] **Details** - Comprehensive alert details page
- [x] **Priority indicators** - Color-coded badges and severity icons
- [x] **Status tracking** - En attente / Traité
- [x] **Vehicle integration** - Direct links to vehicle details
- [x] **Intervention creation** - Quick action to create maintenance

### Alert Types Supported
1. **Vidange** - Oil change (every 10,000 km)
2. **Révision** - Major service (every 30,000 km)
3. **Changement pneus** - Tire replacement (every 50,000 km)
4. **Contrôle freins** - Brake inspection (every 20,000 km)
5. **Remplacement batterie** - Battery replacement (every 60,000 km)
6. **Contrôle technique** - Technical inspection (annual)
7. **Révision annuelle** - Annual service
8. **État véhicule critique** - Vehicle in poor condition
9. **Maintenance prolongée** - Maintenance exceeding 14 days
10. **Coûts élevés** - High maintenance costs (>10,000 MAD / 6 months)
11. **Problème récurrent** - Same issue 3+ times in 3 months

---

## 🏗️ BACKEND ARCHITECTURE

### 1. AlerteService (`app/Services/AlerteService.php`)

**Purpose**: Core alert generation logic

**Methods**:
```php
// Main generation methods
generateAllAlerts()                    // Generate for all vehicles
generateAlertsForVehicle($voiture)    // Generate for specific vehicle

// Alert type checks
checkKilometrageAlerts($voiture)      // Mileage-based alerts
checkDateAlerts($voiture)             // Date-based alerts
checkConditionAlerts($voiture)        // Condition-based alerts
checkInterventionAlerts($voiture)     // Cost and pattern analysis

// Utility methods
calculatePriority($alerte)            // Determine alert priority
getPriorityColor($priority)           // Get color for priority
resolveAlert($alerte)                 // Mark as treated
cleanupOldAlerts($daysOld = 90)      // Remove old resolved alerts
```

**Kilometrage Thresholds**:
```php
const KILOMETRAGE_THRESHOLDS = [
    'Vidange' => 10000,
    'Révision' => 30000,
    'Changement pneus' => 50000,
    'Contrôle freins' => 20000,
    'Remplacement batterie' => 60000,
];
```

**Priority Logic**:
- **Critique**: Overdue, critical types (état critique, CT), or < 0 days
- **Haute**: 0-7 days until due
- **Moyenne**: 8-30 days until due
- **Faible**: 30+ days until due

---

### 2. AlerteController (`app/Http/Controllers/AlerteController.php`)

**Features**:
- Full CRUD operations
- Filtering by status, type, priority, vehicle
- Dynamic priority calculation on response
- Bulk alert generation
- Statistics endpoint

**Key Methods**:
```php
index()                           // List all alerts with filters
store($request)                   // Create new alert
show($id)                         // Get alert details
update($request, $id)             // Update alert
destroy($id)                      // Delete alert
resolve($id)                      // Mark as treated
generateAlerts()                  // Generate for all vehicles
generateForVehicle($voitureId)   // Generate for one vehicle
cleanup()                         // Remove old alerts
getStats()                        // Dashboard statistics
```

---

### 3. Alerte Model (`app/Models/Alerte.php`)

**Database Fields**:
```php
idAlerte          // Primary key
type              // Alert type (e.g., "Vidange", "Contrôle technique")
dateEchance       // Due date
kilometrageEchance // Kilometrage threshold
statut            // 'En attente' or 'Traité'
voiture_id        // Foreign key to voitures
created_at        // Creation timestamp
updated_at        // Last update timestamp
```

**Relationships**:
```php
voiture()  // BelongsTo Voiture
```

---

### 4. GenerateAlertsCommand (`app/Console/Commands/GenerateAlertsCommand.php`)

**Command Signature**:
```bash
php artisan alerts:generate [--vehicle=ID] [--cleanup]
```

**Options**:
- `--vehicle=ID` - Generate alerts for specific vehicle
- `--cleanup` - Remove old resolved alerts (90+ days)

**Features**:
- Progress bar for bulk generation
- Statistics display after completion
- Top 5 urgent alerts preview
- Comprehensive output formatting

**Scheduled Execution** (in `app/Console/Kernel.php`):
```php
// Daily at 6:00 AM
$schedule->command('alerts:generate')->dailyAt('06:00');

// Weekly cleanup on Sunday at 2:00 AM
$schedule->command('alerts:generate --cleanup')->weeklyOn(0, '02:00');
```

---

## 🎨 FRONTEND COMPONENTS

### 1. Catalogue (`resources/js/components/alertes/catalogue.vue`)

**Features**:
- Statistics cards (Total, Critique, Haute, Moyenne, Traité)
- Multi-filter system (status, priority, type, search)
- Priority-coded alert cards with color borders
- Quick actions (Resolve, Details, Delete)
- Generate alerts button
- Empty state messaging
- Loading and error states

**Filters Available**:
- Statut: Tous / En attente / Traité
- Priorité: Toutes / Critique / Haute / Moyenne / Faible
- Type: All alert types dropdown
- Recherche: Search by vehicle name

**Visual Indicators**:
- Priority badges with colors:
  - Critique: Red (#E74C3C)
  - Haute: Orange (#E67E22)
  - Moyenne: Yellow (#F39C12)
  - Faible: Blue (#3498DB)
- Status badges: Pending (yellow) / Treated (green)
- Days until due: Overdue (red) / Today (orange) / Upcoming (blue)

---

### 2. Details (`resources/js/components/alertes/details.vue`)

**Sections**:
1. **Header Card** - Alert type, ID, priority, status
2. **Alert Information** - Due date, kilometrage, type, priority, timestamps
3. **Vehicle Card** - Vehicle details, image, specs, link to vehicle page
4. **Recent Interventions** - Last 5 interventions for the vehicle
5. **Quick Actions**:
   - Mark as treated
   - Edit alert
   - Create intervention (pre-fills vehicle)
   - Delete alert

**Features**:
- Breadcrumb navigation
- Priority-colored header icon
- Vehicle image display
- Intervention history with status colors
- Action buttons with icons

---

## 🔌 API ENDPOINTS

### Alert Management

**List Alerts** (with filtering)
```http
GET /api/alertes?statut=En attente&priorite=critique&type=Vidange&voiture_id=1
Authorization: Bearer {token}

Response:
[
  {
    "idAlerte": 1,
    "type": "Vidange",
    "dateEchance": "2025-11-15",
    "kilometrageEchance": 120000,
    "statut": "En attente",
    "voiture_id": 1,
    "priorite": "haute",
    "prioriteColor": "#E67E22",
    "daysUntilDue": 26,
    "voiture": { ... },
    "created_at": "2025-10-20T10:00:00",
    "updated_at": "2025-10-20T10:00:00"
  }
]
```

**Get Alert Details**
```http
GET /api/alertes/{id}
Authorization: Bearer {token}

Response: Single alert with vehicle and interventions
```

**Create Alert**
```http
POST /api/alertes
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "Vidange",
  "dateEchance": "2025-11-15",
  "kilometrageEchance": 120000,
  "voiture_id": 1
}

Response: Created alert (201)
```

**Update Alert**
```http
PUT /api/alertes/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "dateEchance": "2025-11-20",
  "statut": "En attente"
}

Response: Updated alert
```

**Delete Alert**
```http
DELETE /api/alertes/{id}
Authorization: Bearer {token}

Response: 200 with success message
```

**Resolve Alert**
```http
PATCH /api/alertes/{id}/resolve
Authorization: Bearer {token}

Response: Alert with statut = "Traité"
```

**Generate Alerts (All Vehicles)**
```http
POST /api/alertes/generate
Authorization: Bearer {token}

Response:
{
  "message": "Génération des alertes terminée",
  "alertsGenerated": 14
}
```

**Generate Alerts (Specific Vehicle)**
```http
POST /api/alertes/generate/{voitureId}
Authorization: Bearer {token}

Response:
{
  "message": "Alertes générées pour le véhicule BMW X5",
  "alertsGenerated": 3
}
```

**Get Statistics**
```http
GET /api/alertes/stats
Authorization: Bearer {token}

Response:
{
  "total": 45,
  "treated": 12,
  "byPriority": {
    "critique": 8,
    "haute": 15,
    "moyenne": 18,
    "faible": 4
  },
  "urgentes": [
    {
      "idAlerte": 1,
      "type": "Contrôle technique",
      "date": "2025-10-25",
      "priorite": "critique",
      "prioriteColor": "#E74C3C",
      "vehicule": "BMW X5"
    }
  ]
}
```

**Cleanup Old Alerts**
```http
DELETE /api/alertes/cleanup
Authorization: Bearer {token}

Response:
{
  "message": "Nettoyage terminé",
  "alertsDeleted": 23
}
```

---

## 🧠 ALERT GENERATION LOGIC

### Kilometrage-Based Alerts

**Trigger Condition**: Vehicle is within 1,000 km of next service threshold

**Example**:
```php
// Vehicle: 119,500 km
// Last oil change: 110,000 km
// Next oil change: 120,000 km (110,000 + 10,000)
// Alert generated: YES (within 1,000 km buffer)
```

**Alert Creation**:
```php
Alerte::create([
    'type' => 'Vidange',
    'dateEchance' => Carbon::now()->addMonths(1), // Estimated
    'kilometrageEchance' => 120000,
    'statut' => 'En attente',
    'voiture_id' => $voiture->idVoiture,
]);
```

---

### Date-Based Alerts

**Technical Inspection (CT)**:
- Frequency: Annual
- Alert trigger: 60 days before expiration
- No previous CT: Alert created for 30 days from now

**Annual Service**:
- Frequency: Annual
- Alert trigger: 30 days before due

**Example**:
```php
// Last CT: 2024-12-20
// Next CT: 2025-12-20
// Alert generated: 2025-10-21 (60 days before)
```

---

### Condition-Based Alerts

**Poor Vehicle Condition**:
- Trigger: `voiture.etat === 'Mauvais'`
- Due date: 7 days from now
- Type: "État véhicule critique"

**Prolonged Maintenance**:
- Trigger: Vehicle in maintenance for 14+ days
- Due date: 3 days from now
- Type: "Maintenance prolongée"

---

### Cost and Pattern Alerts

**High Maintenance Costs**:
- Threshold: 10,000 MAD in last 6 months
- Type: "Coûts élevés"

**Recurring Issues**:
- Threshold: Same intervention type 3+ times in 3 months
- Type: "Problème récurrent: {type}"

---

## 🚀 COMMAND USAGE

### Generate Alerts for All Vehicles
```bash
php artisan alerts:generate
```

**Output**:
```
🚀 Starting alert generation...
🔄 Generating alerts for all vehicles...
 14/14 [============================] 100%
✅ Successfully generated 14 alert(s) for 14 vehicle(s)

📊 Alert Statistics:
+------------+-------+
| Status     | Count |
+------------+-------+
| En attente | 14    |
| Traité     | 0     |
| Total      | 14    |
+------------+-------+

⚠️  Top 5 Urgent Alerts:
+----+--------------------+----------------+------------+
| ID | Type               | Vehicle        | Due Date   |
+----+--------------------+----------------+------------+
| 1  | Contrôle technique | BMW X5         | 2025-11-19 |
| 2  | Contrôle technique | Mercedes C200  | 2025-11-19 |
| 3  | Contrôle technique | Peugeot 208    | 2025-11-19 |
| 4  | Contrôle technique | Audi A3        | 2025-11-19 |
| 5  | Contrôle technique | Toyota Corolla | 2025-11-19 |
+----+--------------------+----------------+------------+
```

### Generate for Specific Vehicle
```bash
php artisan alerts:generate --vehicle=5
```

**Output**:
```
🚀 Starting alert generation...
🔍 Generating alerts for vehicle ID: 5
✅ Generated 3 alert(s) for BMW X5
```

### Cleanup Old Alerts
```bash
php artisan alerts:generate --cleanup
```

**Output**:
```
🚀 Starting alert generation...
🧹 Cleaning up old resolved alerts...
✅ Cleaned up 23 old alerts.
```

### Combined Operations
```bash
php artisan alerts:generate --cleanup
# Will clean up first, then generate new alerts
```

---

## 🧪 TESTING GUIDE

### 1. Test Alert Generation

**Step 1**: Navigate to Alertes page
```
http://localhost:5175/alertes
```

**Step 2**: Click "Générer Alertes" button

**Step 3**: Verify alerts appear with:
- Correct type
- Priority badge (colored)
- Days until due
- Vehicle information

---

### 2. Test Filtering

**Filters to Test**:
- Status: Switch between "En attente" / "Traité"
- Priority: Filter by critique/haute/moyenne/faible
- Type: Select specific alert types
- Search: Enter vehicle name (e.g., "BMW")

**Expected**: Grid updates dynamically

---

### 3. Test Alert Actions

**Resolve Alert**:
1. Click "Résoudre" on any pending alert
2. Confirm dialog
3. Verify status changes to "Traité"
4. Verify statistics update

**View Details**:
1. Click "Détails" or click on alert card
2. Verify all information displayed
3. Check vehicle image loads
4. Check interventions list

**Delete Alert**:
1. Click delete button
2. Confirm dialog
3. Verify alert removed from list

---

### 4. Test Command Line

**Generate Alerts**:
```bash
php artisan alerts:generate
```

**Expected**:
- Progress bar displays
- Statistics table shows
- Urgent alerts listed

---

### 5. Test Priority Calculation

**Manual Test**:
1. Create intervention with date in past
2. Run alert generation
3. Verify overdue alert has "critique" priority

---

### 6. Test API Endpoints (Postman/Insomnia)

**Get Alerts**:
```http
GET http://127.0.0.1:8000/api/alertes
Authorization: Bearer {your_token}
```

**Get Stats**:
```http
GET http://127.0.0.1:8000/api/alertes/stats
Authorization: Bearer {your_token}
```

**Create Alert**:
```http
POST http://127.0.0.1:8000/api/alertes
Authorization: Bearer {your_token}
Content-Type: application/json

{
  "type": "Test Alert",
  "dateEchance": "2025-11-01",
  "kilometrageEchance": 125000,
  "voiture_id": 1
}
```

---

## 🔮 FUTURE ENHANCEMENTS

### Short-term (1-2 months)

**1. Email Notifications** 📧
- Send email when critical alert generated
- Daily digest of pending alerts
- Configurable notification preferences

**2. Alert Snooze** ⏰
- Postpone alert for X days
- Add snooze reason/note
- Track snooze history

**3. Alert Templates** 📋
- Pre-defined alert types
- Customizable thresholds per vehicle
- Import/export alert rules

---

### Medium-term (3-6 months)

**4. Mobile Push Notifications** 📱
- Real-time alerts on mobile app
- Priority-based notification sound
- In-app notification center

**5. Alert Dashboard Widget** 📊
- Embed alert summary on dashboards
- Click to view details
- Quick resolve from widget

**6. Alert History** 📜
- Track resolved alerts
- See resolution timestamps
- Who resolved each alert

---

### Long-term (6-12 months)

**7. AI-Powered Predictions** 🤖
- Machine learning for failure prediction
- Personalized alert thresholds per vehicle
- Cost optimization recommendations

**8. Integration with External Systems** 🔗
- Connect to garage APIs
- Auto-schedule maintenance appointments
- Import vehicle diagnostics data

**9. Advanced Analytics** 📈
- Alert trend analysis
- Cost vs. alert correlation
- Fleet health score

---

## 📊 STATISTICS & METRICS

### Current Implementation Stats

**Files Created**:
- 1 Service class
- 1 Controller (updated)
- 1 Artisan command
- 2 Vue components
- 1 Model (existing, enhanced)

**Lines of Code**:
- Backend: ~850 lines
- Frontend: ~1,400 lines
- Total: ~2,250 lines

**API Endpoints**: 10
- GET: 3 (index, show, stats)
- POST: 3 (store, generate, generateForVehicle)
- PUT: 1 (update)
- PATCH: 1 (resolve)
- DELETE: 2 (destroy, cleanup)

**Alert Types Supported**: 11

**Priority Levels**: 4 (Critique, Haute, Moyenne, Faible)

---

## 🎓 USER GUIDE

### For Administrators

**Daily Tasks**:
1. Check alert statistics on dashboard
2. Review critical alerts
3. Assign interventions for urgent alerts

**Weekly Tasks**:
1. Run manual alert generation if needed
2. Review resolved alerts
3. Adjust alert thresholds if necessary

---

### For Agents

**Daily Tasks**:
1. Check pending alerts for assigned vehicles
2. Mark alerts as treated after intervention
3. Create interventions from alerts

---

### For Technicians

**Daily Tasks**:
1. View alerts assigned to them
2. Mark alerts as treated after completing work
3. Update intervention status

---

## 🔐 SECURITY NOTES

**Authentication**: All alert endpoints require `auth:sanctum` middleware

**Authorization**:
- Admins: Full CRUD on all alerts
- Agents: Can create, view, and resolve alerts
- Techniciens: Can view and resolve alerts

**Data Validation**:
- Type: Required string
- Date: Required valid date format
- Kilometrage: Optional numeric
- Vehicle ID: Required, must exist in database

---

## 🐛 TROUBLESHOOTING

### Issue: Command fails with database error

**Solution**: Check database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cartrackingapp
DB_USERNAME=root
DB_PASSWORD=
```

### Issue: No alerts generated

**Possible causes**:
1. No vehicles in database
2. No interventions recorded
3. All vehicles already have alerts

**Debug**:
```bash
php artisan alerts:generate --vehicle=1
```

### Issue: Frontend shows "Erreur lors du chargement"

**Solution**:
1. Check Laravel is running: `php artisan serve`
2. Check Vite is running: `npm run dev`
3. Verify token in localStorage
4. Check browser console for errors

---

## 📝 CHANGELOG

**Version 1.0 - October 20, 2025**
- ✅ Initial release
- ✅ AlerteService with 4 alert generation methods
- ✅ AlerteController with full REST API
- ✅ GenerateAlertsCommand with scheduler integration
- ✅ Catalogue Vue component with filters
- ✅ Details Vue component with actions
- ✅ API routes added to api.php
- ✅ Frontend routes added to router.js
- ✅ Tested and working with 14 vehicles

---

## 📞 SUPPORT

For questions or issues with the Alertes module:
1. Check this documentation
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check browser console for frontend errors
4. Run command with verbose output: `php artisan alerts:generate -vvv`

---

**Document Version**: 1.0  
**Last Updated**: October 20, 2025  
**Module Status**: ✅ Production Ready  
**Test Status**: ✅ Passed  
**Documentation**: ✅ Complete

---

*© 2025 CARTRACKINGAPP - Alertes Module*
