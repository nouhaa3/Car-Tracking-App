
<template>
  <router-view></router-view>
</template>

<script>
  import { reactive, provide } from 'vue';

  export default {
    setup() {
      const theme = reactive({
        isDark: localStorage.getItem('theme') === 'dark'
      });

      provide('theme', theme); // inject theme for children
      return { theme };
    }
  }
</script>
