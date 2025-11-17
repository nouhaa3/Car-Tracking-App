# Home Page Visual Testing Guide

## 🧪 Testing the Updates

### Prerequisites
- Dev server running on http://localhost:5173
- Clear browser cache before testing
- Test on Chrome, Firefox, and Safari

## 1. Language Selector Testing

### Visual Check
- [ ] Flag appears in top-right corner of header
- [ ] Flag is **circular** (not rectangular)
- [ ] Flag has white border (2px)
- [ ] Flag has subtle shadow

### Functionality Check
1. Click on the flag icon
2. Dropdown menu should appear with 3 options:
   - 🇫🇷 Français
   - 🇬🇧 English
   - 🇸🇦 العربية
3. Click each language
4. Verify page content changes immediately
5. Refresh page - language should persist
6. Click outside dropdown - menu should close

### Arabic RTL Check
1. Switch to Arabic (🇸🇦)
2. Verify:
   - [ ] Text aligns right
   - [ ] Layout mirrors (navbar items flip)
   - [ ] Reading direction is right-to-left
   - [ ] All content displays in Arabic

## 2. Hero Section Testing

### Content Check
- [ ] Badge: "Solution de gestion de flotte #1"
- [ ] Title: "La solution complète pour une gestion intelligente de votre flotte automobile"
- [ ] Description shows optimization and control messaging
- [ ] Two buttons:
  - Primary (blue): "Commencer gratuitement"
  - Secondary (white): "Demander une démo" (NOT "En savoir plus")

### Interaction
- [ ] Buttons have hover effects
- [ ] Clicking "Demander une démo" scrolls to contact form

## 3. Stats Section Testing

### Visual Check
- [ ] 3 stat cards displayed horizontally
- [ ] Each has a green checkmark icon (bi-check-circle-fill)
- [ ] Stats show:
  - "10,000+ véhicules gérés en Tunisie"
  - "500+ entreprises font confiance à FleetManager"
  - "99.9% de disponibilité garantie"

### Responsive
- [ ] Desktop: 3 columns
- [ ] Tablet: 3 columns (smaller)
- [ ] Mobile: Stack vertically

## 4. Features Section Testing

### Content Check - 6 Features
1. [ ] Suivi en temps réel (blue icon)
2. [ ] Maintenance préventive (green icon)
3. [ ] **Gestion administrative** (YELLOW icon - NEW)
4. [ ] Rapports et analytiques (purple icon)
5. [ ] Sécurité des données (red icon)
6. [ ] Application mobile (indigo icon)

### Visual Check
- [ ] 3x2 grid layout
- [ ] Each card has colored icon background
- [ ] Icon background colors are distinct
- [ ] Administrative management has **yellow** background

### Responsive
- [ ] Desktop: 3 columns
- [ ] Tablet: 2 columns
- [ ] Mobile: 1 column (stack)

## 5. Benefits Section Testing

### Order Check - CRITICAL
Benefits should appear in this order:
1. [ ] **Gain de temps** (Clock icon) - MUST BE FIRST
2. [ ] Réduction des coûts (Piggy bank icon)
3. [ ] Décisions éclairées (Chart icon)
4. [ ] Support 24/7 (Headset icon)

### Content Check
Each benefit has:
- [ ] Icon
- [ ] Title
- [ ] Description (percentage/stat)
- [ ] Details paragraph

### Responsive
- [ ] Desktop: 2x2 grid
- [ ] Mobile: Stack vertically

## 6. Contact Form Testing

### Form Fields Check - 2x2 Grid
**Row 1:**
- [ ] Nom complet (left)
- [ ] Email (right)

**Row 2:**
- [ ] Téléphone (left)
- [ ] Entreprise (right - NEW FIELD)

**Row 3:**
- [ ] Message (full width, textarea)

### Placeholders Check
- [ ] Full Name: "Mohamed Ahmed"
- [ ] Email: "mohamed@entreprise.com"
- [ ] Phone: "+216 20 123 456" (Tunisia format)
- [ ] Company: "Votre entreprise"
- [ ] Message: "Parlez-nous de vos besoins..."

### Contact Info Check - Tunisia
- [ ] Email: contact@fleetmanager.tn
- [ ] Phone: +216 71 123 456
- [ ] Mobile: +216 20 123 456
- [ ] Address: Avenue Habib Bourguiba, Tunis, Tunisie

### Functionality
- [ ] Form validation works
- [ ] Submit button shows "Envoi en cours..." when sending
- [ ] Success message appears on submit
- [ ] Form resets after success

## 7. Footer Testing

### Tagline Check
- [ ] "La solution de gestion de flotte la plus fiable en Tunisie"

### Links Structure - 3 Columns

**Product Column:**
- [ ] Fonctionnalités
- [ ] Demander une démo (NEW)
- [ ] API et Intégrations (NEW)

**Company Column:**
- [ ] À propos
- [ ] Carrières
- [ ] Presse (NEW)

**Legal Column:**
- [ ] Politique de confidentialité
- [ ] Conditions d'utilisation
- [ ] Mentions légales

### Visual Check
- [ ] Copyright text at bottom
- [ ] Links have hover effect
- [ ] Social media icons (if present)

## 8. Mobile Responsive Testing

### Breakpoints to Test
- [ ] Desktop: 1920px
- [ ] Laptop: 1366px
- [ ] Tablet: 768px
- [ ] Mobile: 375px

### Mobile-Specific Checks
- [ ] Language selector fits in header
- [ ] Hero text is readable
- [ ] Stats stack vertically
- [ ] Features stack in single column
- [ ] Benefits stack vertically
- [ ] Contact form adapts to mobile
- [ ] Footer columns stack

## 9. Cross-Language Testing

### Test Each Language
For each language (FR, EN, AR):
- [ ] Hero section displays correctly
- [ ] Stats are translated
- [ ] All 6 features are translated
- [ ] Benefits order is correct
- [ ] Contact form labels translated
- [ ] Contact info displays properly
- [ ] Footer links translated

### Arabic-Specific
- [ ] RTL layout works
- [ ] Arabic text displays properly (no broken characters)
- [ ] Numbers display correctly
- [ ] Icons remain in correct position

## 10. Performance Testing

### Load Time
- [ ] Page loads in < 3 seconds
- [ ] No console errors
- [ ] No 404 errors for images/assets
- [ ] Flags load correctly

### Interactions
- [ ] Language switching is instant
- [ ] Smooth scrolling works
- [ ] Dropdown animations are smooth
- [ ] No lag when typing in forms

## 🐛 Common Issues to Watch For

### Issue 1: Flags Not Circular
**Symptom**: Flags appear rectangular
**Fix**: Check that flag-icons CSS is loaded, verify border-radius: 50%

### Issue 2: Translation Keys Showing
**Symptom**: Text like "home.hero.title" appears
**Fix**: Clear browser cache, restart dev server

### Issue 3: Wrong Button in Hero
**Symptom**: Second button says "En savoir plus"
**Fix**: Should be "Demander une démo" - check home.vue template

### Issue 4: Benefits Wrong Order
**Symptom**: "Gain de temps" is not first
**Fix**: Check template order in home.vue - timeSavings should be first

### Issue 5: Missing Company Field
**Symptom**: Contact form has only 3 fields
**Fix**: Should have 4 fields + message - check form grid

### Issue 6: Old Footer Links
**Symptom**: Footer missing "Demo", "API", "Press"
**Fix**: Check footer template - should have new links

### Issue 7: Arabic Not RTL
**Symptom**: Arabic text aligns left
**Fix**: Check changeLanguage function sets dir="rtl"

## ✅ Sign-Off Checklist

Before marking as complete:
- [ ] All 3 languages tested
- [ ] Circular flags confirmed
- [ ] Benefits order verified (timeSavings first)
- [ ] Contact form has 4 fields
- [ ] Footer has new links
- [ ] Mobile responsive works
- [ ] No console errors
- [ ] Arabic RTL works properly
- [ ] Tunisia contact info correct
- [ ] All translation keys resolve

## 📸 Screenshots to Capture

1. Language selector dropdown open
2. Hero section in French
3. Features grid with 6 items
4. Benefits section (verify order)
5. Contact form 2x2 grid
6. Footer with new links
7. Arabic version (RTL layout)
8. Mobile view (all sections)

---

## Quick Test Commands

```bash
# Check if dev server is running
Get-Process -Name node

# Clear npm cache if issues
npm cache clean --force

# Restart dev server
npm run dev

# Check for build errors
npm run build
```

## Expected Results

✅ **Success Indicators:**
- Language selector with circular flags works perfectly
- All 3 languages display correctly
- Benefits section shows "Gain de temps" first
- Contact form has 4 input fields
- Footer has "Demo", "API & Intégrations", "Presse" links
- Tunisia phone numbers (+216) everywhere
- Arabic RTL layout works
- No console errors
- Mobile responsive design works

🎉 **When all checks pass, the home page is production-ready!**

---
*Testing Guide Version: 1.0*
*Last Updated: December 2024*
