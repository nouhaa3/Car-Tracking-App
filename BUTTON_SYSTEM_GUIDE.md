# 🎨 Button System Guide - Complete Reference

## 📦 Button Variants Overview

### 1. **Solid Buttons** (Primary Actions)
```html
<button class="btn-primary">Primary</button>
<button class="btn-secondary">Secondary</button>
<button class="btn-danger">Danger</button>
<button class="btn-success">Success</button>
```
**Use for:** Main actions, form submissions, critical operations

---

### 2. **Outline Buttons** (Secondary Actions)
```html
<button class="btn-outline-primary">Outline Primary</button>
<button class="btn-outline-secondary">Outline Secondary</button>
<button class="btn-outline-danger">Outline Danger</button>
<button class="btn-outline-success">Outline Success</button>
```
**Use for:** Secondary actions, alternative options, ghost-style buttons

**Features:**
- Transparent background
- Colored border (2px)
- Fills with color on hover
- No shadow by default

---

### 3. **Soft/Ghost Buttons** (Subtle Actions)
```html
<button class="btn-soft-primary">Soft Primary</button>
<button class="btn-soft-secondary">Soft Secondary</button>
<button class="btn-soft-danger">Soft Danger</button>
<button class="btn-soft-success">Soft Success</button>
```
**Use for:** Subtle actions, non-critical operations, modern UI

**Features:**
- Light transparent background (10-20% opacity)
- No border
- Darker background on hover
- Gentle elevation on hover

---

### 4. **Text/Link Buttons** (Minimal Actions)
```html
<button class="btn-text-primary">Text Primary</button>
<button class="btn-text-secondary">Text Secondary</button>
<button class="btn-text-danger">Text Danger</button>
<button class="btn-text-success">Text Success</button>
```
**Use for:** Links, cancel actions, minimal UI

**Features:**
- Completely transparent
- No border, no shadow
- Subtle background on hover
- Compact padding

---

### 5. **Specialized Buttons**

#### **Add Buttons**
```html
<button class="add-car-btn">
  <i class="bi bi-plus-circle"></i>
  Add Vehicle
</button>
<button class="add-user-btn">
  <i class="bi bi-person-plus"></i>
  Add User
</button>
```

#### **Action Buttons**
```html
<button class="action-btn-edit">
  <i class="bi bi-pencil"></i>
  Edit
</button>
<button class="action-btn-delete">
  <i class="bi bi-trash"></i>
  Delete
</button>
<button class="action-btn-success">
  <i class="bi bi-check-circle"></i>
  Resolve
</button>
```

#### **Filter/Toggle Buttons**
```html
<button class="filter-toggle-btn">
  <i class="bi bi-funnel"></i>
  Filters
</button>
<button class="filter-toggle-btn active">
  <i class="bi bi-funnel-fill"></i>
  Filters
</button>
```

#### **Social/Brand Buttons**
```html
<button class="btn-google">
  <i class="bi bi-google"></i>
  Continue with Google
</button>
<button class="btn-facebook">
  <i class="bi bi-facebook"></i>
  Continue with Facebook
</button>
```

---

## 🔄 Button States

### 1. **Loading State**
```html
<button class="btn-primary btn-loading">Processing...</button>
<button class="btn-secondary btn-loading">Loading...</button>
<button class="btn-danger btn-loading">Deleting...</button>
```

**Features:**
- Animated spinner automatically positioned
- Text becomes transparent
- Button disabled (pointer-events: none)
- Spinner size adapts to button size
- Color-aware (dark spinner on light buttons, light on dark)

**CSS Applied:**
```css
.btn-loading {
  position: relative;
  color: transparent !important;
  pointer-events: none;
  cursor: not-allowed;
}
```

---

### 2. **Disabled State**
```html
<button class="btn-primary" disabled>Disabled Primary</button>
<button class="btn-secondary" disabled>Disabled Secondary</button>
```

**Features:**
- 50% opacity
- Not-allowed cursor
- No hover effects
- No transform animations

---

### 3. **Active State**
```html
<button class="filter-toggle-btn active">Active Filter</button>
```

**Features:**
- Custom styling per button type
- Usually filled background for toggle buttons
- Rotated icons (if applicable)

---

## 📏 Button Sizes

### **Size Modifiers**
```html
<button class="btn-primary btn-xs">Extra Small</button>
<button class="btn-primary btn-sm">Small</button>
<button class="btn-primary btn-md">Medium (Default)</button>
<button class="btn-primary btn-lg">Large</button>
<button class="btn-primary btn-xl">Extra Large</button>
```

**Size Chart:**
| Class | Padding | Font Size | Use Case |
|-------|---------|-----------|----------|
| `.btn-xs` | 0.375rem 0.75rem | 0.75rem | Compact UI, tight spaces |
| `.btn-sm` | 0.5rem 1rem | 0.8125rem | Secondary actions |
| `.btn-md` | 0.75rem 1.5rem | 0.9375rem | Default size |
| `.btn-lg` | 0.875rem 1.75rem | 1rem | Primary actions |
| `.btn-xl` | 1rem 2rem | 1.125rem | Hero sections |

---

## 🎯 Icon Buttons

### **Icon Button Sizes**
```html
<button class="icon-btn-sm btn-primary"><i class="bi bi-plus"></i></button>
<button class="icon-btn btn-primary"><i class="bi bi-edit"></i></button>
<button class="icon-btn-lg btn-danger"><i class="bi bi-trash"></i></button>
<button class="icon-btn-xl btn-success"><i class="bi bi-check"></i></button>
```

**Size Chart:**
| Class | Dimensions | Font Size | Border Radius |
|-------|------------|-----------|---------------|
| `.icon-btn-sm` | 32x32px | 0.875rem | 8px |
| `.icon-btn` / `.icon-btn-md` | 40x40px | 1rem | 8px |
| `.icon-btn-lg` | 48x48px | 1.25rem | 10px |
| `.icon-btn-xl` | 56x56px | 1.5rem | 12px |

### **Circular Icon Buttons**
```html
<button class="icon-btn-sm btn-primary icon-btn-circle"><i class="bi bi-heart"></i></button>
<button class="icon-btn btn-danger icon-btn-circle"><i class="bi bi-x"></i></button>
<button class="icon-btn-lg btn-success circle"><i class="bi bi-check"></i></button>
```

**Features:**
- 50% border-radius (perfect circle)
- Works with any size modifier
- Great for floating action buttons

---

## 🔗 Button Groups

### **Horizontal Group**
```html
<div class="btn-group">
  <button class="btn-secondary">Left</button>
  <button class="btn-secondary">Center</button>
  <button class="btn-secondary">Right</button>
</div>
```

**Features:**
- Connected borders
- First button: rounded left corners
- Last button: rounded right corners
- Middle buttons: no rounded corners
- Shared shadow on group

### **Vertical Group**
```html
<div class="btn-group-vertical">
  <button class="btn-secondary">Top</button>
  <button class="btn-secondary">Middle</button>
  <button class="btn-secondary">Bottom</button>
</div>
```

**Features:**
- Stacked vertically
- First button: rounded top corners
- Last button: rounded bottom corners
- Border between buttons

---

## 🎨 Button Utilities

### **Full Width**
```html
<button class="btn-primary btn-block">Full Width Button</button>
```

### **Elevated Shadow**
```html
<button class="btn-primary btn-elevated">Elevated Button</button>
```

### **Rounded/Pill Shape**
```html
<button class="btn-primary btn-rounded">Pill Button</button>
```

### **Square Corners**
```html
<button class="btn-primary btn-square">Square Button</button>
```

### **No Shadow**
```html
<button class="btn-primary btn-flat">Flat Button</button>
```

### **With Badge**
```html
<button class="btn-primary btn-with-badge">
  Notifications
  <span class="badge">5</span>
</button>
```

### **Pulse Animation**
```html
<button class="btn-primary btn-pulse">Important Action</button>
```

### **Ripple Effect**
```html
<button class="btn-primary btn-ripple">Click Me</button>
```

---

## 🏗️ Combining Utilities

You can combine multiple utilities for custom button styles:

```html
<!-- Large, rounded, elevated, full-width button -->
<button class="btn-primary btn-lg btn-rounded btn-elevated btn-block">
  Get Started
</button>

<!-- Small, circular, soft icon button with pulse -->
<button class="icon-btn-sm btn-soft-primary icon-btn-circle btn-pulse">
  <i class="bi bi-bell"></i>
</button>

<!-- Medium outline button with ripple and badge -->
<button class="btn-outline-danger btn-md btn-ripple btn-with-badge">
  Alerts
  <span class="badge">3</span>
</button>
```

---

## 📱 Responsive Behavior

Buttons automatically resize on mobile devices:

**Desktop (>1024px):**
- Full size as defined

**Tablet (768px - 1024px):**
- Slightly reduced padding
- Smaller font sizes

**Mobile (<768px):**
- Further reduced padding
- Compact font sizes
- Icon buttons remain accessible (minimum 32px touch target)

---

## ♿ Accessibility

All buttons include:
- ✅ Proper `:focus` states (outline visible)
- ✅ `:disabled` state handling (cursor, opacity, no interactions)
- ✅ Keyboard navigation support
- ✅ Screen reader friendly (use proper text or `aria-label`)
- ✅ Minimum touch target size (44x44px on mobile)

**Best Practices:**
```html
<!-- Icon-only button - Add aria-label -->
<button class="icon-btn btn-primary" aria-label="Edit user">
  <i class="bi bi-pencil"></i>
</button>

<!-- Loading state - Add aria-busy -->
<button class="btn-primary btn-loading" aria-busy="true">
  Processing...
</button>

<!-- Disabled - Use disabled attribute -->
<button class="btn-primary" disabled>
  Submit
</button>
```

---

## 🎯 Use Case Examples

### **Form Actions**
```html
<div class="form-actions">
  <button class="btn-secondary" type="button">Cancel</button>
  <button class="btn-primary" type="submit">Save Changes</button>
</div>
```

### **Card Actions**
```html
<div class="card-actions">
  <button class="icon-btn-sm btn-soft-primary">
    <i class="bi bi-eye"></i>
  </button>
  <button class="icon-btn-sm btn-soft-secondary">
    <i class="bi bi-pencil"></i>
  </button>
  <button class="icon-btn-sm btn-soft-danger">
    <i class="bi bi-trash"></i>
  </button>
</div>
```

### **Toolbar**
```html
<div class="toolbar btn-group">
  <button class="btn-secondary"><i class="bi bi-text-left"></i></button>
  <button class="btn-secondary"><i class="bi bi-text-center"></i></button>
  <button class="btn-secondary"><i class="bi bi-text-right"></i></button>
</div>
```

### **Floating Action Button**
```html
<button class="quick-action-btn">
  <i class="bi bi-plus"></i>
  Add New
</button>
```

### **Alert/Modal Actions**
```html
<div class="modal-actions">
  <button class="btn-text-secondary">Cancel</button>
  <button class="btn-danger">Delete</button>
</div>
```

---

## 🎨 Color Reference

### **Primary Colors**
- Primary: `#344966` → `#546A88` (gradient)
- Secondary: `#546A88` → `#748BAA`
- Danger: `#C85A54` → `#A84842`
- Success: `#BFCC94` → `#A8B880`

### **Border Colors**
- Light: `#D2E1EE` (Columbia blue)
- Medium: `#B4CDED`
- Dark: `#546A88`

---

## 🚀 Performance Notes

- ✅ All animations use GPU-accelerated properties (`transform`, `opacity`)
- ✅ No layout-triggering properties in animations
- ✅ Cubic-bezier easing for natural motion
- ✅ Optimal animation duration (0.3s - 0.6s)
- ✅ Will-change hints for frequently animated properties
- ✅ Smooth 60fps performance

---

## 📋 Quick Reference Table

| Button Type | Class | Use Case |
|-------------|-------|----------|
| Primary Action | `.btn-primary` | Submit, Save, Confirm |
| Secondary Action | `.btn-secondary` | Cancel, Back |
| Danger Action | `.btn-danger` | Delete, Remove |
| Success Action | `.btn-success` | Resolve, Complete |
| Outline | `.btn-outline-*` | Alternative options |
| Soft/Ghost | `.btn-soft-*` | Subtle actions |
| Text/Link | `.btn-text-*` | Links, minimal UI |
| Add Button | `.add-car-btn`, `.add-user-btn` | Create new items |
| Icon Button | `.icon-btn-*` | Icon-only actions |
| Filter Toggle | `.filter-toggle-btn` | Show/hide filters |
| Quick Action | `.quick-action-btn` | Floating FAB |

---

**Implementation Date:** November 16, 2025
**Status:** ✅ Complete
**File:** `resources/css/app.css`
**Lines Added:** ~350 lines of enhanced button styles
