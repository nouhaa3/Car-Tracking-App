# CSS Organization Guide

## Overview
This document explains the CSS organization structure in the Car Tracking App workspace.

## Current Structure

### 1. Global Styles (`resources/css/app.css`)
The main CSS file containing all shared/common styles used across multiple components.

**What's included:**
- **Global Layout & Theme** (Lines 1-53)
  - View density settings
  - Layout structure
  - Main content styles

- **Button Styles** (Lines 99-547)
  - All button variations (primary, secondary, danger, success, etc.)
  - Button sizes (small, medium, large)
  - Icon buttons
  - Floating action buttons
  - Disabled states
  - Responsive adjustments

- **Navigation** (Lines 549-676)
  - Sidebar styles
  - Mobile navigation
  - Navigation items

- **Form Components** (Lines 677-1010)
  - Input fields
  - Select dropdowns
  - Form groups
  - Validation states

- **Dashboard Components** (Lines 1011+)
  - Cards
  - Tables
  - Stats displays
  - Charts

- **Landing Page Components** (Lines 5982+)
  - `.home-header` - Header navigation
  - `.footer-modern` - Footer layout
  - Hero sections
  - Feature grids

- **Common Component Styles** (Lines 10479+) - **NEWLY ADDED**
  - `.navbar` - Dashboard-style navigation
  - `.nav-link` - Navigation links
  - `.section-badge` - Section badges
  - `.section-title-modern` - Section titles
  - `.btn-cta-primary` / `.btn-cta-secondary` - CTA buttons
  - `.notfound-page` - 404 error page styles
  - Animation keyframes (`@keyframes drive`)
  - Responsive media queries

### 2. Component-Specific Styles (Vue `<style scoped>`)

Components with scoped styles keep their unique, component-specific CSS within their `.vue` files:

**Components with scoped styles:**
- `About.vue` - About page specific styles (hero, mission, stats, team sections)
- `FAQ.vue` - FAQ page specific styles (search, categories, accordion)
- `interventions/ajouter.vue` - Add intervention form styles
- `interventions/details.vue` - Intervention details page styles
- `interventions/catalogue.vue` - Interventions catalog styles
- `voitures/detailsvoiture.vue` - Vehicle details styles
- `voitures/DocumentsVehicule.vue` - Vehicle documents styles
- `alertes/details.vue` - Alert details styles
- `Settings.vue` - Settings page specific styles
- `Notifications.vue` - Notifications panel styles
- `help.vue` - Help page styles
- `TermsOfService.vue` - Terms page styles
- `PrivacyPolicy.vue` - Privacy page styles

**Components WITHOUT scoped styles (use global styles only):**
- `home.vue` - Uses global `.home-header`, `.footer-modern` from app.css
- `NotFound.vue` - Now uses global styles from app.css
- `login.vue`, `register.vue`, `ForgotPassword.vue`, `ResetPassword.vue` - Use global form styles

## Style Organization Principles

### When to use `app.css`:
✅ Styles shared across 3+ components
✅ Common UI patterns (buttons, forms, cards)
✅ Global layout and theme
✅ Utility classes
✅ Responsive breakpoints

### When to use `<style scoped>`:
✅ Component-unique styles
✅ Page-specific layouts
✅ Specialized animations
✅ Component state variations

## Common Patterns Now in app.css

### Navigation
```css
.navbar { /* Dashboard-style nav */ }
.nav-link { /* Navigation links */ }
.nav-link.active { /* Active state */ }
```

### Sections
```css
.section-badge { /* Blue gradient badges */ }
.section-title-modern { /* Large section titles */ }
.section-description { /* Section descriptions */ }
.section-header { /* Centered headers */ }
```

### Buttons (Landing Pages)
```css
.btn-cta-primary { /* White button with shadow */ }
.btn-cta-secondary { /* Glass-morphism button */ }
```

### 404 Page
```css
.notfound-page { /* Full-screen container */ }
.error-animation { /* Car animation */ }
.error-number { /* Large 404 text */ }
```

## Maintenance Guidelines

### Adding New Styles:

1. **Is it used in 3+ components?**
   - YES → Add to `app.css` in appropriate section
   - NO → Keep in component's `<style scoped>`

2. **Is it a variation of existing pattern?**
   - YES → Extend existing classes in `app.css`
   - NO → Create new scoped style

3. **Does it affect global layout?**
   - YES → Add to `app.css` global section
   - NO → Keep component-scoped

### Removing Duplicate Styles:

When you find duplicate styles:
1. Check if pattern exists in `app.css`
2. If not, add it to appropriate section
3. Remove from component files
4. Test components still work

### File Size Management:

- `app.css`: **~10,700+ lines** - This is acceptable for a global stylesheet
- Component styles: Keep under 500 lines per component
- If component styles exceed 500 lines, consider breaking into sub-components

## Recent Changes (November 2025)

### Added to app.css:
- Common navbar styles (`.navbar`, `.nav-link`)
- Common section components (`.section-badge`, `.section-title-modern`)
- CTA button styles (`.btn-cta-primary`, `.btn-cta-secondary`)
- NotFound page complete styles
- Drive animation keyframes
- Responsive utilities for common components

### Removed from Components:
- `NotFound.vue` - All styles moved to app.css

### To Do:
- Consider extracting repeated hero section styles from About.vue and FAQ.vue
- Consider creating separate CSS files for:
  - `components/interventions.css`
  - `components/vehicles.css`  
  - `components/alerts.css`
- Document color variables and create CSS custom properties

## CSS Architecture Recommendation

For better long-term maintenance, consider implementing:

1. **CSS Modules** (Optional)
   ```
   /resources/css/
     ├── app.css (base + utilities)
     ├── components/
     │   ├── buttons.css
     │   ├── forms.css
     │   ├── navigation.css
     │   └── cards.css
     └── pages/
         ├── landing.css
         ├── dashboard.css
         └── auth.css
   ```

2. **CSS Custom Properties** for theming
   ```css
   :root {
     --color-primary: #344966;
     --color-secondary: #546A88;
     --spacing-sm: 0.5rem;
     --spacing-md: 1rem;
   }
   ```

3. **BEM Naming Convention** for clarity
   ```css
   .card__header { }
   .card__header--highlighted { }
   ```

## Summary

The current approach balances:
- **Global styles** in `app.css` for consistency
- **Scoped styles** in components for encapsulation
- **Common patterns** extracted to reduce duplication

This provides a solid foundation while maintaining Vue's component-based architecture principles.
