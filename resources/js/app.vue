
<template>
  <router-view></router-view>
</template>

<script>
  import { reactive, provide, watch, onMounted } from 'vue';
  // Import the CSS file so Vite can track it and provide the correct path
  import darkModeStyles from '../css/dark-mode.css?url';

  export default {
    setup() {
      const theme = reactive({
        isDark: localStorage.getItem('theme') === 'dark'
      });

      provide('theme', theme); // inject theme for children

      // Function to dynamically load/unload dark mode CSS
      const updateDarkModeCSS = (isDark) => {
        const darkStylesheet = document.getElementById('dark-mode-stylesheet');
        
        if (isDark) {
          // Load dark mode CSS if not already loaded
          if (!darkStylesheet) {
            const link = document.createElement('link');
            link.id = 'dark-mode-stylesheet';
            link.rel = 'stylesheet';
            link.href = darkModeStyles; // Vite will provide correct path with hash
            document.head.appendChild(link);
          }
          document.body.classList.add('dark');
        } else {
          // Remove dark mode CSS when switching to light mode
          if (darkStylesheet) {
            darkStylesheet.remove();
          }
          document.body.classList.remove('dark');
        }
      };

      // Apply theme on initial page load
      onMounted(() => {
        updateDarkModeCSS(theme.isDark);
      });

      // Watch for theme changes and update CSS accordingly
      watch(() => theme.isDark, (newValue) => {
        updateDarkModeCSS(newValue);
      });

      return { theme };
    }
  }
</script>
