# 🎨 Button Inventory - Complete Color Palette & Usage Guide

## 📋 Table of Contents
1. [Primary Action Buttons](#primary-action-buttons)
2. [Secondary Action Buttons](#secondary-action-buttons)
3. [Danger/Delete Buttons](#dangerdelete-buttons)
4. [Success Buttons](#success-buttons)
5. [Edit Buttons](#edit-buttons)
6. [View/Info Buttons](#viewinfo-buttons)
7. [Icon Buttons](#icon-buttons)
8. [Special Purpose Buttons](#special-purpose-buttons)
9. [Outline Variants](#outline-variants)
10. [Home Page Buttons](#home-page-buttons)
11. [Complete Color Reference](#complete-color-reference)

---

## 1. Primary Action Buttons

### `.btn-primary` & `.auth-btn`
**Purpose:** Save, Submit, Confirm, Login actions  
**Background:** `linear-gradient(135deg, #344966 0%, #546A88 100%)`  
**Hover:** `linear-gradient(135deg, #213144 0%, #344966 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(52, 73, 102, 0.2)`  
**Hover Shadow:** `0 4px 12px rgba(52, 73, 102, 0.3)`  
**Size:** Padding `14px 28px`, Font `16px`, Height `52px`

**Used in:**
- Login form submit button
- Register form submit button
- Profile save button
- Settings save button
- Form submit actions
- Vehicle details return to catalog
- Intervention details return to catalog

---

### `.add-car-btn` & `.add-user-btn`
**Purpose:** Add new items (Vehicle, Intervention, Alert, User)  
**Background:** `linear-gradient(135deg, #344966 0%, #546A88 100%) !important`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(52, 73, 102, 0.2)`  
**Hover Effect:** Lift `-2px`, enhanced shadow  
**Size:** Padding `14px 28px`, Font `16px`, Height `52px`

**Used in:**
- Interventions catalog - Add new intervention
- Vehicles catalog - Add new vehicle
- Users page - Add new user

---

## 2. Secondary Action Buttons

### `.btn-secondary`
**Purpose:** Cancel, Back, Alternative actions  
**Background:** White  
**Text Color:** `#546A88`  
**Border:** `#D2E1EE`  
**Hover Background:** `#D2E1EE`  
**Hover Text:** `#344966`  
**Shadow:** `0 2px 6px rgba(84, 106, 136, 0.1)`  
**Size:** Padding `12px 24px`, Font `15px`, Height `46px`

**Used in:**
- Form cancel buttons
- Modal close buttons
- Step navigation back buttons
- Alternative action choices
- Settings action buttons

---

## 3. Danger/Delete Buttons

### `.btn-danger` & `.icon-btn.delete-btn` & `.action-btn-delete`
**Purpose:** Delete, Remove, Destructive actions  
**Background:** `linear-gradient(135deg, #C85A54 0%, #A84842 100%)`  
**Hover:** `linear-gradient(135deg, #B04944 0%, #8B3734 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(200, 90, 84, 0.2)`  
**Hover Shadow:** `0 4px 12px rgba(200, 90, 84, 0.3)`  
**Hover Effect:** Lift `-2px`

**Used in:**
- Vehicle delete button
- Intervention delete button
- User delete confirmation modal
- Delete action buttons in lists

---

### `.btn-delete` (New variant in detailsvoiture.vue)
**Purpose:** Delete action in vehicle details  
**Background:** `linear-gradient(135deg, #EF4444 0%, #DC2626 100%)`  
**Hover:** `linear-gradient(135deg, #DC2626 0%, #B91C1C 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(239, 68, 68, 0.3)`  
**Hover Shadow:** `0 4px 12px rgba(239, 68, 68, 0.4)`  
**Size:** Padding `10px 20px`, Min-width `120px`, Font `15px`  
**Hover Effect:** Lift `-2px`, icon scale `1.1`

---

## 4. Success Buttons

### `.btn-success` & `.action-btn-success`
**Purpose:** Resolve, Complete, Success actions  
**Background:** `linear-gradient(135deg, #BFCC94 0%, #A8B880 100%)`  
**Hover:** `linear-gradient(135deg, #A8B880 0%, #93A46E 100%)`  
**Text Color:** `#0D1821` (Dark text)  
**Shadow:** `0 2px 8px rgba(191, 204, 148, 0.2)`  
**Hover Shadow:** `0 4px 12px rgba(191, 204, 148, 0.3)`

**Used in:**
- Alert "Traiter" (Resolve) button
- Step navigation "Terminer" (Finish) button
- Terms acceptance button

---

## 5. Edit Buttons

### `.icon-btn.edit-btn` & `.action-btn-edit`
**Purpose:** Edit, Modify, Update actions  
**Background:** `linear-gradient(135deg, #748BAA 0%, #B4CDED 100%)`  
**Hover:** `linear-gradient(135deg, #546A88 0%, #748BAA 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(116, 139, 170, 0.2)`  
**Hover Shadow:** `0 4px 12px rgba(116, 139, 170, 0.3)`  
**Hover Effect:** Lift `-2px`

**Used in:**
- Vehicle edit button
- Intervention edit button
- Profile edit button
- User edit button in lists

---

### `.btn-edit` (New variant in detailsvoiture.vue)
**Purpose:** Edit action in vehicle details  
**Background:** `linear-gradient(135deg, #4A90E2 0%, #357ABD 100%)`  
**Hover:** `linear-gradient(135deg, #357ABD 0%, #2868A8 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(74, 144, 226, 0.3)`  
**Hover Shadow:** `0 4px 12px rgba(74, 144, 226, 0.4)`  
**Size:** Padding `10px 20px`, Min-width `120px`, Font `15px`  
**Hover Effect:** Lift `-2px`, icon scale `1.1`

---

## 6. View/Info Buttons

### `.car-btn-voir-plus` & `.action-btn-primary`
**Purpose:** View details, Info, Preview  
**Background:** `linear-gradient(135deg, #546A88 0%, #748BAA 100%)`  
**Hover:** `linear-gradient(135deg, #344966 0%, #546A88 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(84, 106, 136, 0.2)`  
**Hover Shadow:** `0 4px 12px rgba(84, 106, 136, 0.3)`  
**Hover Effect:** Lift `-2px`

**Used in:**
- Vehicle catalog "Voir détails" button
- Intervention catalog "Voir détails" button
- View details actions

---

### `.view-vehicle-btn`
**Purpose:** View associated vehicle from intervention details  
**Custom styling in component scope**

---

## 7. Icon Buttons

### `.icon-btn` (Base class)
**Size:** `40px × 40px`  
**Padding:** `0`  
**Border Radius:** `8px`  
**Font Size:** `1rem` (16px)  
**Display:** `inline-flex`, centered

### `.icon-btn.icon-only`
**Purpose:** Icon-only circular buttons  
**Size:** `40px × 40px`  
**Border Radius:** `8px` (square with rounded corners)  
**Font Size:** `1.1rem` (18px)

**Variants:**
- `.icon-btn-sm` - `32px × 32px`, Font `0.875rem` (14px)
- `.icon-btn-md` - `40px × 40px`, Font `1rem` (16px) - Default
- `.icon-btn-lg` - `48px × 48px`, Font `1.125rem` (18px)

---

## 8. Special Purpose Buttons

### `.filter-toggle-btn`
**Purpose:** Toggle filters visibility  
**Background:** White  
**Active Background:** `linear-gradient(135deg, #344966 0%, #546A88 100%)`  
**Text Color:** `#546A88`  
**Active Text:** White  
**Border:** `#D2E1EE`  
**Height:** `48px`

**Used in:**
- Vehicle catalog filter toggle
- Intervention catalog filter toggle

---

### `.clear-filters-btn` & `.clear-search-btn`
**Purpose:** Clear filters, Clear search, Reset  
**Background:** White  
**Text Color:** `#C85A54`  
**Border:** `#C85A54`  
**Hover Background:** `#C85A54`  
**Hover Text:** White  
**Shadow:** `0 2px 6px rgba(200, 90, 84, 0.1)`

**Used in:**
- Filter panel clear button
- Search input clear button (circular, 36px)

---

### `.retry-btn`
**Purpose:** Retry, Reload, Refresh  
**Background:** `linear-gradient(135deg, #748BAA 0%, #B4CDED 100%)`  
**Hover:** `linear-gradient(135deg, #546A88 0%, #748BAA 100%)`  
**Text Color:** White  
**Shadow:** `0 2px 8px rgba(116, 139, 170, 0.2)`

**Used in:**
- Error state retry buttons
- Failed data fetch retry

---

### `.quick-action-btn`
**Purpose:** Floating action button for quick add  
**Position:** Fixed `bottom: 2rem`, `right: 2rem`  
**Background:** `linear-gradient(135deg, #344966 0%, #546A88 100%) !important`  
**Border Radius:** `50px` (pill shape)  
**Shadow:** `0 4px 16px rgba(52, 73, 102, 0.3)`  
**Hover Shadow:** `0 6px 20px rgba(52, 73, 102, 0.4)`  
**z-index:** `100`

**Used in:**
- Vehicle catalog floating add button
- Intervention catalog floating add button

---

### `.btn-back`
**Purpose:** Back navigation  
**Custom styled in component scope**

**Used in:**
- Add vehicle form back button
- Add intervention form back button

---

### `.btn-step` (with variants)
**Purpose:** Wizard/stepper navigation  
**Variants:** Combined with `.btn-primary`, `.btn-secondary`, `.btn-success`

**Used in:**
- Multi-step forms (Add vehicle, Add intervention)
- Step 1: "Précédent" (Previous) - `.btn-secondary`
- Step 2: "Suivant" (Next) - `.btn-primary`
- Step 3: "Terminer" (Finish) - `.btn-success`

---

### `.tab-btn`
**Purpose:** Tab navigation in detail views  
**Background:** None (transparent)  
**Active Background:** White  
**Active Border Bottom:** `3px solid #344966`  
**Hover Background:** `#e8edf4`  
**Text Color:** `#666`  
**Active Text:** `#344966`

**Used in:**
- Vehicle details tabs (Informations, Documents, Historique)

---

## 9. Outline Variants

### `.btn-outline-primary`
**Background:** Transparent  
**Text Color:** `#344966`  
**Border:** `2px solid #344966`  
**Hover Background:** `#344966`  
**Hover Text:** White

---

### `.btn-outline-secondary`
**Background:** Transparent  
**Text Color:** `#546A88`  
**Border:** `2px solid #D2E1EE`  
**Hover Background:** `#D2E1EE`  
**Hover Text:** `#344966`

---

### `.btn-outline-danger`
**Background:** Transparent  
**Text Color:** `#C85A54`  
**Border:** `2px solid #C85A54`  
**Hover Background:** `#C85A54`  
**Hover Text:** White

---

### `.btn-outline-success`
**Background:** Transparent  
**Text Color:** `#BFCC94`  
**Border:** `2px solid #BFCC94`  
**Hover Background:** `#BFCC94`  
**Hover Text:** `#0D1821`

---

### `.btn-outline`
**Purpose:** Generic outline button (Terms decline)  
**Custom styling in component scope**

---

## 10. Home Page Buttons

### `.btn-header-primary`
**Purpose:** Header primary CTA (Register)  
**Custom gradient styling for home page**

### `.btn-header-secondary`
**Purpose:** Header secondary action (Login)  
**Custom outline styling for home page**

### `.btn-cta-primary`
**Purpose:** Hero section primary CTA  
**Large, prominent button with gradient**

### `.btn-cta-secondary`
**Purpose:** Hero section secondary CTA  
**Outline style matching brand**

### `.btn-submit-modern`
**Purpose:** Contact form submit  
**Modern gradient design**

### `.language-btn`
**Purpose:** Language selector  
**Transparent with border**

---

## 11. Reports Module Buttons

### `.download-btn` (Base class with variants)
**Purpose:** Download report in different formats

#### `.pdf-btn`
**Background:** Red gradient for PDF downloads  
**Icon:** PDF file icon

#### `.excel-btn`
**Background:** Green gradient for Excel downloads  
**Icon:** Excel file icon

#### `.preview-btn`
**Background:** Blue gradient for preview  
**Icon:** Eye/preview icon

#### `.featured-btn`
**Background:** Special gradient for featured reports  
**Icon:** Custom report icon

---

## 12. Other Specialized Buttons

### `.change-avatar-btn`
**Purpose:** Change profile avatar  
**Custom styling in profile component**

### `.image-action-btn` (with variants)
**Purpose:** Image management in add forms  
**Variants:** `.change-btn`, `.cancel-btn`

### `.close-btn`
**Purpose:** Modal close buttons  
**Position:** Usually absolute top-right  
**Style:** Minimal, icon-only

### `.menu-link` (Sidebar)
**Purpose:** Sidebar navigation links  
**Special variants:** `.theme-toggle`, `.logout-link`

### `.logout-btn`
**Purpose:** Logout action  
**Custom styled in technician dashboard**

### `.category-btn` (FAQ)
**Purpose:** FAQ category filters  
**Toggle active state for selected category**

### `.support-btn`
**Purpose:** Link to support chat  
**Styled as prominent call-to-action**

### `.reset-link-btn` & `.copy-btn`
**Purpose:** Password reset link actions  
**Custom styled for forgot password flow**

### `.action-btn`, `.action-btn-small`
**Purpose:** Notification actions  
**Sizes:** Regular and small variants  
**Variants:** `.delete-btn` for delete actions

---

## 📊 Complete Color Reference

### Primary Color Palette
| Color Name | Hex Code | RGB | Usage |
|------------|----------|-----|-------|
| **Dark Blue (Primary)** | `#344966` | rgb(52, 73, 102) | Primary buttons base |
| **Medium Blue** | `#546A88` | rgb(84, 106, 136) | Primary buttons gradient end |
| **Darker Blue** | `#213144` | rgb(33, 49, 68) | Primary buttons hover start |
| **Light Blue** | `#748BAA` | rgb(116, 139, 170) | Edit buttons, view buttons |
| **Very Light Blue** | `#B4CDED` | rgb(180, 205, 237) | Edit buttons gradient, accents |
| **Pale Blue** | `#D2E1EE` | rgb(210, 225, 238) | Secondary backgrounds, borders |
| **Bright Blue (Edit)** | `#4A90E2` | rgb(74, 144, 226) | New edit button variant |
| `#357ABD` | rgb(53, 122, 189) | New edit button gradient/hover |
| `#2868A8` | rgb(40, 104, 168) | New edit button hover end |

### Danger Color Palette
| Color Name | Hex Code | RGB | Usage |
|------------|----------|-----|-------|
| **Primary Red** | `#C85A54` | rgb(200, 90, 84) | Danger buttons base |
| **Dark Red** | `#A84842` | rgb(168, 72, 66) | Danger buttons gradient end |
| **Darker Red** | `#B04944` | rgb(176, 73, 68) | Danger buttons hover start |
| **Darkest Red** | `#8B3734` | rgb(139, 55, 52) | Danger buttons hover end |
| **Bright Red (Delete)** | `#EF4444` | rgb(239, 68, 68) | New delete button variant |
| **Crimson** | `#DC2626` | rgb(220, 38, 38) | New delete button gradient |
| **Dark Crimson** | `#B91C1C` | rgb(185, 28, 28) | New delete button hover end |
| **Accent Red** | `#F44336` | rgb(244, 67, 54) | Special delete actions |
| **Orange Red** | `#e64a19` | rgb(230, 74, 25) | Alternative danger |

### Success Color Palette
| Color Name | Hex Code | RGB | Usage |
|------------|----------|-----|-------|
| **Light Green** | `#BFCC94` | rgb(191, 204, 148) | Success buttons base |
| **Medium Green** | `#A8B880` | rgb(168, 184, 128) | Success buttons gradient |
| **Dark Green** | `#93A46E` | rgb(147, 164, 110) | Success buttons hover |
| **Bright Green** | `#10B981` | rgb(16, 185, 129) | Available status badge |
| **Emerald** | `#059669` | rgb(5, 150, 105) | Available status gradient |

### Status Badge Colors
| Status | Start Color | End Color | Usage |
|--------|-------------|-----------|-------|
| **Available** | `#10B981` (Green) | `#059669` (Emerald) | Vehicle available |
| **Rented** | `#F59E0B` (Amber) | `#D97706` (Orange) | Vehicle rented |
| **Maintenance** | `#6366F1` (Indigo) | `#4F46E5` (Purple) | Vehicle in maintenance |

### Neutral Colors
| Color Name | Hex Code | RGB | Usage |
|------------|----------|-----|-------|
| **White** | `#FFFFFF` | rgb(255, 255, 255) | Backgrounds, text on dark |
| **Off White** | `#F9FBFD` | rgb(249, 251, 253) | Subtle backgrounds |
| **Light Gray** | `#F8FAFB` | rgb(248, 250, 251) | Card backgrounds |
| **Medium Gray** | `#F0F4F8` | rgb(240, 244, 248) | Disabled backgrounds |
| **Border Gray** | `#E8F0F7` | rgb(232, 240, 247) | Borders, dividers |
| **Text Gray** | `#6B7280` | rgb(107, 114, 128) | Secondary text |
| **Dark Gray** | `#666` | rgb(102, 102, 102) | Inactive tab text |
| **Very Dark** | `#0D1821` | rgb(13, 24, 33) | Dark text on light backgrounds |

### Social Media Colors
| Platform | Hex Code | RGB | Usage |
|----------|----------|-----|-------|
| **Google Blue** | `#4285F4` | rgb(66, 133, 244) | Google login button |
| **Facebook Blue** | `#1877F2` | rgb(24, 119, 242) | Facebook login button |

---

## 🎯 Button Size System

| Size Category | Classes | Padding | Font Size | Height | Use Case |
|---------------|---------|---------|-----------|--------|----------|
| **Large** | `.btn-primary`, `.auth-btn`, `.add-car-btn` | `14px 28px` | `16px` | `52px` | Primary CTAs, Submit, Login |
| **Medium** | `.btn-secondary`, `.btn-danger`, `.btn-success` | `12px 24px` | `15px` | `46px` | Secondary actions, Cancel |
| **Compact** | `.btn-action` (new) | `10px 20px` | `15px` | `~45px` | Detail page actions |
| **Small** | `.action-btn`, `.clear-filters-btn` | `10px 20px` | `14px` | `44px` | Compact actions |
| **Icon Small** | `.icon-btn-sm` | `0` | `0.875rem` | `32px` | Small icon buttons |
| **Icon Medium** | `.icon-btn`, `.icon-btn-md` | `0` | `1rem` | `40px` | Default icon buttons |
| **Icon Large** | `.icon-btn-lg` | `0` | `1.125rem` | `48px` | Large icon buttons |
| **Icon Only** | `.icon-btn.icon-only` | `0` | `1.1rem` | `40px` | Square icon buttons |

---

## 🎨 Shadow System

### Subtle Shadows (Rest State)
- **Primary/Success/Danger:** `0 2px 8px rgba([color], 0.2)`
- **Secondary/Outline:** `0 2px 6px rgba([color], 0.1)`
- **Floating Action:** `0 4px 16px rgba(52, 73, 102, 0.3)`

### Enhanced Shadows (Hover State)
- **Primary/Success/Danger:** `0 4px 12px rgba([color], 0.3)`
- **Secondary/Outline:** `0 3px 10px rgba([color], 0.15)`
- **Floating Action:** `0 6px 20px rgba(52, 73, 102, 0.4)`

### Active State
- **All buttons:** `0 2px 6px rgba([color], 0.25)`

---

## ✨ Interaction States

### Hover Effects
- **Transform:** `translateY(-2px)` for most buttons
- **Icon Scale:** `scale(1.1)` for icons inside buttons
- **Shadow:** Enhanced shadow depth
- **Background:** Darker gradient or solid color change

### Active/Click Effects
- **Transform:** `translateY(0)` or `scale(0.98)`
- **Shadow:** Reduced to pressed state

### Disabled State
- **Opacity:** `0.5`
- **Cursor:** `not-allowed`
- **Transform:** `none !important`
- **Shadow:** `none !important`

### Loading State
- **Class:** `.btn-loading`
- **Text Color:** `transparent !important`
- **After Element:** Spinning border animation
- **Spinner Size:** 14px-18px depending on button size

---

## 📱 Responsive Behavior

### Mobile Breakpoint (@media max-width: 768px)

#### `.btn-action` (Vehicle Details)
- Font size reduced to `14px`
- Padding reduced to `9px 16px`
- Min-width reduced to `110px`
- Text (`<span>`) hidden - show icon only
- Icon margin removed (centered)

#### `.status-badge-large`
- Width `100%`
- Text align `center`

#### General Button Adjustments
- Stacked layouts preferred
- Full-width buttons on small screens
- Reduced padding and font sizes
- Icon-only variants for space efficiency

---

## 🔧 Usage Guidelines

### ✅ Do:
- Use `.btn-primary` for main call-to-action
- Use `.btn-secondary` for cancel/alternative actions
- Use `.btn-danger` for destructive actions with confirmation
- Maintain consistent sizing within the same context
- Use icon buttons for repetitive actions in lists
- Provide loading states for async operations
- Disable buttons during processing

### ❌ Don't:
- Mix multiple primary buttons in same view
- Use danger buttons without confirmation dialogs
- Stack too many buttons vertically
- Use tiny buttons for critical actions
- Forget disabled states
- Use colors inconsistently across similar actions

---

## 🎯 Common Button Combinations

### Form Actions
```vue
<button type="submit" class="btn-primary">Save</button>
<button type="button" class="btn-secondary">Cancel</button>
```

### Detail View Actions
```vue
<button class="btn-action btn-edit">
  <i class="fas fa-edit"></i>
  <span>Edit</span>
</button>
<button class="btn-action btn-delete">
  <i class="fas fa-trash"></i>
  <span>Delete</span>
</button>
```

### Catalog Quick Actions
```vue
<button class="car-btn-voir-plus">View Details</button>
<router-link to="/add" class="add-car-btn">Add New</router-link>
```

### Modal Actions
```vue
<button class="btn-danger">Confirm Delete</button>
<button class="btn-secondary">Cancel</button>
```

### Wizard Steps
```vue
<button class="btn-step btn-secondary">Previous</button>
<button class="btn-step btn-primary">Next</button>
<button class="btn-step btn-success">Finish</button>
```

---

## 📝 Component-Specific Buttons

### Home Page
- `.btn-header-primary`, `.btn-header-secondary`
- `.btn-cta-primary`, `.btn-cta-secondary`
- `.btn-submit-modern`
- `.language-btn`

### Reports
- `.download-btn.pdf-btn`, `.download-btn.excel-btn`
- `.download-btn.preview-btn`, `.download-btn.featured-btn`

### Notifications
- `.action-btn`, `.action-btn-small`
- `.menu-btn`, `.delete-option`

### Profile
- `.change-avatar-btn`
- `.icon-btn.edit-btn`

### Settings
- `.btn-link` (for non-prominent actions)

### FAQ
- `.category-btn` (with `.active` state)

### Terms of Service
- `.btn-outline` (decline action)

---

## 🌟 Recently Added (Latest Update)

### `.btn-action` System
**New button style for detail views** (detailsvoiture.vue)

#### Base Class: `.btn-action`
- Padding: `10px 20px`
- Min-width: `120px`
- Font: `15px`, weight `600`
- Display: `inline-flex`, gap `8px`
- Border-radius: `8px`
- Responsive: Icon-only on mobile

#### `.btn-edit` (New Blue Variant)
- Background: `linear-gradient(135deg, #4A90E2 0%, #357ABD 100%)`
- Hover: `linear-gradient(135deg, #357ABD 0%, #2868A8 100%)`
- Shadow: `0 2px 8px rgba(74, 144, 226, 0.3)`

#### `.btn-delete` (New Red Variant)
- Background: `linear-gradient(135deg, #EF4444 0%, #DC2626 100%)`
- Hover: `linear-gradient(135deg, #DC2626 0%, #B91C1C 100%)`
- Shadow: `0 2px 8px rgba(239, 68, 68, 0.3)`

**Features:**
- Hover lift effect (`translateY(-2px)`)
- Icon scale animation on hover (`scale(1.1)`)
- Enhanced shadows on hover
- Mobile responsive (text hidden, icon only)

---

## 📚 Total Button Count

**Total Unique Button Classes:** 60+

### By Category:
- **Primary Actions:** 5 classes
- **Secondary Actions:** 3 classes
- **Danger/Delete:** 4 classes
- **Success:** 2 classes
- **Edit:** 3 classes
- **View/Info:** 3 classes
- **Icon Buttons:** 4 size variants
- **Special Purpose:** 15+ classes
- **Outline Variants:** 4 classes
- **Home Page:** 6 classes
- **Reports:** 5 variants
- **Component-Specific:** 15+ classes

---

## 🎨 Design System Summary

**Color Families:**
- **Blue Family:** 9 shades (Primary, Secondary, Edit, Info)
- **Red Family:** 9 shades (Danger, Delete, Error)
- **Green Family:** 5 shades (Success, Available)
- **Neutral Family:** 9 shades (Backgrounds, Text, Borders)
- **Status Colors:** 6 unique gradients

**Interaction Effects:**
- Hover: Gradient shift + Shadow enhancement + Lift
- Active: Scale or translateY(0)
- Disabled: Opacity 0.5 + No interaction
- Loading: Spinner overlay + Transparent text

**Responsive Strategy:**
- Mobile: Icon-only for compact buttons
- Tablet: Maintain full buttons with adjusted padding
- Desktop: Full buttons with all features

---

*Last Updated: Based on latest detailsvoiture.vue enhancements with improved button sizing and gradient styling*
