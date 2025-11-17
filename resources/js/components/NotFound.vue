<template>
  <div class="notfound-page">
    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <div class="notfound-container">
      <!-- Animated 404 -->
      <div class="error-animation">
        <div class="car-animation">
          <i class="bi bi-car-front-fill"></i>
        </div>
        <div class="road"></div>
      </div>

      <h1 class="error-number">404</h1>
      <h2 class="error-message">{{ t('notfound.title') }}</h2>
      <p class="error-subtitle">{{ t('notfound.description') }}</p>

      <!-- Return to Home Button -->
      <button class="home-button" @click="$router.push('/')">
        <i class="bi bi-house-door"></i>
        {{ t('notfound.backHome') }}
      </button>
    </div>
  </div>
</template>

<script>
import { useI18n } from 'vue-i18n';

export default {
  name: "NotFound",
  setup() {
    const { t } = useI18n();
    return { t };
  }
};
</script>
