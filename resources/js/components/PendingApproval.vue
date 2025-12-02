<template>
  <div class="auth-page">
    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <div class="auth-container">
      <div class="auth-card">
        <!-- Logo & Icon -->
        <div class="auth-header">
          <div class="auth-logo pending">
            <i class="bi bi-clock-history"></i>
          </div>
          <h2 class="auth-title">{{ t('auth.pendingApproval.title') }}</h2>
          <p class="auth-subtitle">{{ t('auth.pendingApproval.message') }}</p>
        </div>

        <!-- Info Box -->
        <div class="info-box-auth">
          <i class="bi bi-info-circle"></i>
          <p>{{ t('auth.pendingApproval.info') }}</p>
        </div>

        <!-- Button -->
        <button @click="$router.push('/login')" class="auth-btn">
          <i class="bi bi-arrow-left"></i>
          <span>{{ t('auth.backToLogin') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();
</script>

<style scoped>
.auth-logo.pending {
  background: #344966;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.9;
    transform: scale(1.05);
  }
}

.info-box-auth {
  background: #d4e7ff;
  border: 1px solid #344966;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
  color: #344966;
}

.info-box-auth i {
  font-size: 20px;
  flex-shrink: 0;
}

.info-box-auth p {
  margin: 0;
  font-size: 14px;
  line-height: 1.5;
}
</style>
