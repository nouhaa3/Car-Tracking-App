# 🗺️ FleetManager Navigation Map

## Public Site (Not Logged In)

```
┌─────────────────────────────────────────────────────────────┐
│                    HOME PAGE (/)                            │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Header: [ Features | Benefits | About | FAQ | Contact] │
│  │  Actions: [Login] [Register]                          │  │
│  └───────────────────────────────────────────────────────┘  │
│                                                             │
│  📍 Hero Section                                            │
│  📍 Features Section                                        │
│  📍 Benefits Section                                        │
│  📍 Contact Form                                            │
│  📍 Footer with Links                                       │
└─────────────────────────────────────────────────────────────┘
                    │
        ┌───────────┴──────────┬──────────┐
        │                      │          │
        ↓                      ↓          ↓
┌──────────────┐      ┌──────────┐   ┌─────────┐
│ ABOUT (/about│      │ FAQ      │   │ LOGIN   │
│              │      │ (/faq)   │   │ (/login)│
│ • Mission    │      │ • Search │   │         │
│ • Team       │      │ • Q&A    │   └────┬────┘
│ • Stats      │      │ • Filter │        │
└──────────────┘      └──────────┘        │
                                          ↓
                                    Authentication
```

## After Login (Role-Based Routing)

```
                    LOGIN SUCCESSFUL
                           │
                           ↓
              ┌────────────┴────────────┐
              │  Check User Role        │
              │  from localStorage      │
              └────────────┬────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ↓                  ↓                  ↓
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│ ADMIN DASH    │  │ AGENT DASH    │  │ TECHNICIEN    │
│ /admindashboard│  │ /agentdashboard│  │ /techniciendashboard│
└───────────────┘  └───────────────┘  └───────────────┘
        │
        ↓
┌────────────────────────────────────────────────┐
│  Admin Dashboard Navbar                        │
│  ┌──────────────────────────────────────────┐  │
│  │ [Dashboard] [Catalogue] [Interventions]  │  │
│  │ [Alertes] [Rapports] [Users] [Settings] │  │
│  └──────────────────────────────────────────┘  │
│                                                │
│  📊 Statistics Cards                           │
│  📈 Charts & Graphs                            │
│  🚗 Vehicle Management                         │
│  🔧 Interventions                              │
│  ⚠️  Alerts                                     │
└────────────────────────────────────────────────┘
```

## Navigation Button Behavior

```
┌──────────────────────────────────────────────────────────┐
│  "ACCUEIL" / "DASHBOARD" Button Logic                    │
└──────────────────────────────────────────────────────────┘
                           │
                           ↓
              ┌────────────────────────┐
              │ getDashboardRoute()    │
              │ Check user role        │
              └────────────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ↓                  ↓                  ↓
  role = "admin"     role = "agent"    role = "technicien"
        │                  │                  │
        ↓                  ↓                  ↓
 /admindashboard    /agentdashboard   /techniciendashboard
```

## Router Configuration

```javascript
routes = [
  // ===== PUBLIC ROUTES =====
  { path: '/', component: HomePage },           // Landing page
  { path: '/about', component: AboutPage },     // About us
  { path: '/faq', component: FAQPage },         // FAQ
  { path: '/login', component: LoginPage },     // Login
  { path: '/register', component: RegisterPage }, // Register
  
  // ===== PROTECTED ROUTES (Require Login) =====
  { path: '/admindashboard', component: DashboardAdmin },
  { path: '/agentdashboard', component: DashboardAgent },
  { path: '/techniciendashboard', component: DashboardTechnicien },
  { path: '/voitures/cataloguevoitures', component: CatalogueVoiture },
  { path: '/interventions/catalogue', component: CatalogueInterventions },
  { path: '/alertes', component: CatalogueAlertes },
  // ... more protected routes
  
  // ===== CATCH-ALL (404) =====
  { path: '/:pathMatch(.*)*', component: NotFoundPage }
];
```

## File Structure Visual

```
📁 resources/js/
│
├── 📁 components/
│   ├── 🌐 home.vue ..................... Landing page (public)
│   ├── ℹ️  About.vue ................... About page (public) ✨ NEW
│   ├── ❓ FAQ.vue ...................... FAQ page (public) ✨ NEW
│   ├── 🚫 NotFound.vue ................. 404 page ✨ NEW
│   │
│   ├── 🔐 login.vue .................... Login
│   ├── 📝 register.vue ................. Register
│   │
│   ├── 👨‍💼 admindashboard.vue ........... Admin workspace (Updated)
│   ├── 👤 agentdashboard.vue ........... Agent workspace
│   ├── 🔧 techniciendashboard.vue ...... Technician workspace
│   │
│   └── 📁 voitures/, interventions/, alertes/ ...
│
├── 📁 utils/
│   ├── 🧭 navigation.js ................ Role-based routing ✨ NEW
│   └── ⚠️  alerts.js
│
├── 📁 locales/
│   ├── 🇫🇷 fr.js ....................... French (Updated)
│   ├── 🇬🇧 en.js ....................... English (Updated)
│   └── 🇸🇦 ar.js ....................... Arabic (Updated)
│
└── 🗺️  router.js ....................... Router config (Updated)
```

## User Flow Examples

### Example 1: Visitor Explores Site
```
1. Visit https://fleetmanager.com/
   → Shows home.vue (hero, features, benefits)

2. Click "About" in navbar
   → Routes to /about
   → Shows About.vue (mission, team, stats)

3. Click "FAQ" in navbar
   → Routes to /faq
   → Shows FAQ.vue (searchable Q&A)

4. Click "Login" button
   → Routes to /login
   → User enters credentials

5. Login successful (user role: admin)
   → Auto-redirect to /admindashboard
   → Shows full admin workspace
```

### Example 2: Admin User Workflow
```
1. Login as admin@example.com
   → Redirected to /admindashboard

2. Click "Catalogue" in sidebar
   → Routes to /voitures/cataloguevoitures
   → Manages vehicles

3. Click "Dashboard" (Accueil) button
   → getDashboardRoute() returns /admindashboard
   → Returns to admin dashboard

4. Logout
   → Clears localStorage
   → Redirected to / (home.vue)
```

### Example 3: Agent User Workflow
```
1. Login as agent@example.com
   → Redirected to /agentdashboard

2. Click "Dashboard" (Accueil) button
   → getDashboardRoute() returns /agentdashboard
   → Shows agent-specific dashboard

3. Access limited features
   → No access to admin-only routes
   → Can manage assigned vehicles only
```

## Translation Keys Overview

```javascript
// For About Page
t('about.hero.badge')          → "About Us"
t('about.mission.title')       → "Our Mission"
t('about.team.ceo.name')       → "John Smith"

// For FAQ Page
t('faq.title')                 → "Frequently Asked Questions"
t('faq.items.q1')              → "What is FleetManager?"
t('faq.categories.vehicles')   → "Vehicles"

// For 404 Page
t('notfound.title')            → "Page Not Found"
t('notfound.backHome')         → "Back to Home"
```

## Quick Access URLs (Development)

```
Landing Page:      http://localhost:5173/
About:             http://localhost:5173/about
FAQ:               http://localhost:5173/faq
Login:             http://localhost:5173/login
Register:          http://localhost:5173/register

Admin Dashboard:   http://localhost:5173/admindashboard
Agent Dashboard:   http://localhost:5173/agentdashboard
Tech Dashboard:    http://localhost:5173/techniciendashboard

404 Test:          http://localhost:5173/random-page
```

---

## 🎯 Key Takeaways

1. **home.vue** = Public landing page (marketing site)
2. **Dashboard pages** = Private workspaces (per role)
3. **"Accueil"** button = Dynamic redirect based on user role
4. **About/FAQ** = Accessible from landing page navigation
5. **Role-based routing** = Automatic via `getDashboardRoute()`

✅ All pages created, routes configured, translations added!
