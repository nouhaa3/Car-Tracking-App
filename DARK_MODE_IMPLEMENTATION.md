# Dark Mode Implementation Guide

## Overview
This application now uses a **conditional CSS loading** approach for dark mode. The dark mode stylesheet (`dark-mode.css`) is only loaded when the user selects dark mode, optimizing performance for users who prefer light mode.

## Architecture

### Files Structure
```
resources/
├── css/
│   ├── app.css           # Light mode styles (default)
│   └── dark-mode.css     # Dark mode styles (loaded conditionally)
└── js/
    └── app.vue           # Theme system & dynamic CSS loader
```

### How It Works

1. **Default State**: When the app loads, only `app.css` is loaded
2. **Dark Mode Activation**: When user toggles dark mode:
   - `dark-mode.css` is dynamically loaded via JavaScript
   - `.dark` class is added to `<body>`
   - All dark mode styles become active
3. **Light Mode Switch**: When user switches back:
   - `dark-mode.css` is removed from DOM
   - `.dark` class is removed from `<body>`
   - App returns to light mode styles

## Implementation Details

### 1. Theme System (app.vue)

```javascript
const theme = reactive({
  isDark: localStorage.getItem('theme') === 'dark'
});

// Function to load/unload dark mode CSS
const updateDarkModeCSS = (isDark) => {
  const darkStylesheet = document.getElementById('dark-mode-stylesheet');
  
  if (isDark) {
    if (!darkStylesheet) {
      const link = document.createElement('link');
      link.id = 'dark-mode-stylesheet';
      link.rel = 'stylesheet';
      link.href = '/build/assets/dark-mode.css';
      document.head.appendChild(link);
    }
    document.body.classList.add('dark');
  } else {
    if (darkStylesheet) darkStylesheet.remove();
    document.body.classList.remove('dark');
  }
};

// Apply on mount
onMounted(() => updateDarkModeCSS(theme.isDark));

// Watch for changes
watch(() => theme.isDark, (newValue) => updateDarkModeCSS(newValue));
```

### 2. Toggle Function (sidebar.vue)

The existing `toggleTheme()` function works seamlessly:

```javascript
const toggleTheme = () => {
  theme.isDark = !theme.isDark;
  localStorage.setItem("theme", theme.isDark ? "dark" : "light");
  // CSS loading handled automatically by app.vue watcher
};
```

### 3. Color Palette

**Dark Mode Professional Colors:**
- Background Primary: `#0F172A` (Slate 900)
- Surface: `#1E293B` (Slate 800)
- Surface Elevated: `#334155` (Slate 700)
- Primary: `#60A5FA` (Blue 400) - lighter version of light mode `#344966`
- Primary Hover: `#3B82F6` (Blue 500)
- Text Primary: `#F1F5F9` (Slate 100)
- Text Secondary: `#CBD5E1` (Slate 300)
- Text Muted: `#94A3B8` (Slate 400)
- Success: `#34D399` (Emerald 400)
- Warning: `#FBBF24` (Amber 400)
- Error: `#F87171` (Red 400)

## Benefits

### Performance
- **Reduced Initial Load**: Users who never use dark mode don't download unnecessary CSS
- **Lazy Loading**: Dark mode CSS only loads when needed
- **No Bundle Bloat**: Keeps main bundle smaller

### Maintainability
- **Separation of Concerns**: Light and dark styles in separate files
- **Easy Updates**: Modify dark theme without touching light mode
- **Clear Organization**: Each file has single responsibility

### User Experience
- **Smooth Transitions**: 0.3s ease transitions on all theme changes
- **Persistent Preference**: Theme saved in localStorage
- **No Flash**: CSS loads before user sees content

## Build Process

### Vite Configuration
```javascript
laravel({
    input: [
        'resources/css/app.css',        // Light mode
        'resources/css/dark-mode.css',  // Dark mode
        'resources/js/app.js'
    ],
    refresh: true,
})
```

### Development
```bash
npm run dev
```
- Dark mode CSS available at: `/resources/css/dark-mode.css`

### Production
```bash
npm run build
```
- Dark mode CSS compiled to: `/public/build/assets/dark-mode-[hash].css`
- Automatically versioned and cached

## Testing

### Test Dark Mode Toggle
1. Open application in browser
2. Click theme toggle in sidebar
3. Verify:
   - Dark mode CSS loads (check Network tab)
   - Colors change smoothly
   - No layout shift
   - `.dark` class added to body

### Test Persistence
1. Enable dark mode
2. Refresh page
3. Verify dark mode is still active
4. Check localStorage for `theme: 'dark'`

### Test CSS Loading
1. Open DevTools → Network tab
2. Filter by CSS
3. On initial load: Only `app.css` loads
4. Toggle dark mode: `dark-mode.css` loads
5. Toggle back: `dark-mode.css` removed from DOM

## Modifying Styles

### Adding New Dark Mode Styles

**In dark-mode.css:**
```css
.dark .new-component {
  background: var(--bg-surface);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}
```

**Best Practices:**
1. Use CSS variables for consistency
2. Always prefix selectors with `.dark`
3. Match structure of light mode styles
4. Include smooth transitions
5. Test in both themes

### Updating Existing Styles

**Light mode (app.css):**
```css
.card {
  background: white;
  color: #333;
}
```

**Dark mode (dark-mode.css):**
```css
.dark .card {
  background: var(--bg-surface);
  color: var(--text-primary);
}
```

## Troubleshooting

### Dark mode CSS not loading
- Check Vite dev server is running
- Verify file exists: `resources/css/dark-mode.css`
- Check browser console for errors
- Ensure Vite config includes dark-mode.css

### Styles not applying
- Verify `.dark` class is on `<body>` element
- Check CSS selector specificity
- Ensure all selectors are prefixed with `.dark`
- Clear browser cache

### Flash of wrong theme
- Check `onMounted` runs before render
- Verify localStorage is read correctly
- Ensure CSS loads before content displays

## Future Enhancements

### Possible Improvements
1. **Preload dark CSS**: Add `<link rel="preload">` for users with dark mode preference
2. **System Preference**: Auto-detect OS dark mode with `prefers-color-scheme`
3. **Multiple Themes**: Extend system to support custom color schemes
4. **Theme Previewer**: Show live preview before switching

### System Preference Detection
```javascript
// Detect OS preference on first visit
if (!localStorage.getItem('theme')) {
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  theme.isDark = prefersDark;
  localStorage.setItem('theme', prefersDark ? 'dark' : 'light');
}
```

## Summary

✅ **Implemented:**
- Conditional CSS loading for dark mode
- Professional dark color palette
- Smooth theme transitions
- localStorage persistence
- Optimized performance

✅ **Files Modified:**
- Created: `resources/css/dark-mode.css`
- Modified: `resources/js/app.vue` (added dynamic CSS loader)
- Modified: `vite.config.js` (added dark-mode.css to build)

✅ **How to Use:**
1. User clicks theme toggle in sidebar
2. App automatically loads/unloads dark-mode.css
3. Theme preference persists across sessions
4. No manual intervention needed
