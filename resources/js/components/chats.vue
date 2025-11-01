<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.userAvatar')" class="avatar" />
        </router-link>

        <link
          rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <nav class="navbar mb-4">
          <router-link
            v-for="(item, index) in menuItems"
            :key="index"
            :to="item.to"
            class="nav-link"
            :class="{ active: $route.path === item.to }"
          >
            {{ item.label }}
          </router-link>
        </nav>

        <!-- Page Header -->
        <div class="messages-page-header">
          <div class="header-left">
            <h1>{{ t('messages.title') }}</h1>
            <p>{{ t('messages.subtitle') }}</p>
          </div>
          <div class="header-stats">
            <span class="stat-badge">{{ totalMessages }} {{ t('messages.message', totalMessages) }}</span>
            <span class="stat-badge unread-badge" v-if="unreadCount > 0">
              {{ unreadCount }} {{ t('messages.unread', unreadCount) }}
            </span>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('common.loading') }}</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <i class="bi bi-exclamation-triangle"></i>
          <p>{{ error }}</p>
          <button @click="fetchMessages" class="retry-btn">{{ t('common.retry') }}</button>
        </div>

        <!-- Messages List -->
        <div v-else-if="messages.length > 0" class="messages-container">
          <!-- Filter Tabs -->
          <div class="message-filters">
            <button
              class="filter-tab"
              :class="{ active: activeFilter === 'all' }"
              @click="activeFilter = 'all'"
            >
              {{ t('messages.filters.all') }} ({{ messages.length }})
            </button>
            <button
              class="filter-tab"
              :class="{ active: activeFilter === 'unread' }"
              @click="activeFilter = 'unread'"
            >
              {{ t('messages.filters.unread') }} ({{ unreadCount }})
            </button>
            <button
              class="filter-tab"
              :class="{ active: activeFilter === 'read' }"
              @click="activeFilter = 'read'"
            >
              {{ t('messages.filters.read') }} ({{ readCount }})
            </button>
          </div>

          <!-- Messages Cards -->
          <div class="messages-grid">
            <div
              v-for="msg in filteredMessages"
              :key="msg.id"
              class="message-card card"
              :class="{ 'message-unread': !msg.is_read || msg.is_read == 0 }"
              @click="openMessage(msg)"
            >
              <div class="message-header">
                <div class="message-user">
                  <div class="message-avatar">
                    <template v-if="msg.avatar">
                      <img :src="msg.avatar" :alt="t('common.userAvatar')" />
                    </template>
                    <template v-else>
                      <div class="avatar-initials">
                        {{ getInitials(msg) }}
                      </div>
                    </template>
                  </div>
                  <div class="message-user-info">
                    <h4>{{ msg.nom }} {{ msg.prenom }}</h4>
                    <span class="message-email" v-if="msg.email">{{ msg.email }}</span>
                  </div>
                </div>
                <div class="message-meta">
                  <span class="message-time">{{ timeAgo(msg.created_at) }}</span>
                  <span class="status-badge" :class="msg.is_read == 1 ? 'status-read' : 'status-unread'">
                    {{ msg.is_read == 1 ? t('messages.status.read') : t('messages.status.unread') }}
                  </span>
                </div>
              </div>

              <div class="message-content">
                <p>{{ msg.message }}</p>
              </div>

              <div class="message-footer">
                <div class="message-actions">
                  <button
                    class="action-btn-small reply-btn"
                    @click.stop="replyToMessage(msg)"
                    :title="t('messages.actions.reply')"
                  >
                    {{ t('messages.actions.reply') }}
                  </button>
                  <button
                    class="action-btn-small delete-btn"
                    @click.stop="deleteMessage(msg)"
                    :title="t('messages.actions.delete')"
                  >
                    {{ t('messages.actions.delete') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state card">
          <i class="bi bi-inbox"></i>
          <h3>{{ t('messages.empty.title') }}</h3>
          <p>{{ t('messages.empty.description') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { inject, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Sidebar from './sidebar.vue';
import alerts from '@/utils/alerts';

export default {
  name: 'Chats',
  components: { Sidebar },
  setup() {
    const theme = inject('theme');
    const router = useRouter();
    const { t } = useI18n();
    const isExpanded = ref(false);

    const user = ref(null);
    const messages = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const activeFilter = ref('all');

    const menuItems = [
      { label: t('nav.home'), to: '/' },
      { label: t('nav.dashboard'), to: '/admindashboard' },
      { label: t('nav.catalog'), to: '/voitures/cataloguevoitures' },
      { label: t('nav.interventions'), to: '/interventions/catalogue' },
      { label: t('nav.alerts'), to: '/alertes' },
    ];

    const totalMessages = computed(() => messages.value.length);

    const unreadCount = computed(() => {
      return messages.value.filter((msg) => !msg.is_read || msg.is_read == 0).length;
    });

    const readCount = computed(() => {
      return messages.value.filter((msg) => msg.is_read == 1).length;
    });

    const filteredMessages = computed(() => {
      if (activeFilter.value === 'unread') {
        return messages.value.filter((msg) => !msg.is_read || msg.is_read == 0);
      } else if (activeFilter.value === 'read') {
        return messages.value.filter((msg) => msg.is_read == 1);
      }
      return messages.value;
    });

    const fetchUser = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: { Authorization: `Bearer ${token}` }
        });
        user.value = response.data;
      } catch (err) {
        console.error(t('errors.fetchUserError'), err);
      }
    };

    const fetchMessages = async () => {
      loading.value = true;
      error.value = null;
      try {
        const res = await fetch('http://127.0.0.1:8000/get_messages.php');
        if (!res.ok) throw new Error(t('errors.httpError', { status: res.status }));

        const data = await res.json();
        messages.value = Array.isArray(data) ? data : [];
      } catch (err) {
        console.error(t('errors.generic'), err);
        error.value = t('errors.loadMessagesError');
      } finally {
        loading.value = false;
      }
    };

    const getInitials = (msg) => {
      const firstInitial = msg.prenom?.charAt(0).toUpperCase() || '';
      const lastInitial = msg.nom?.charAt(0).toUpperCase() || '';
      return firstInitial + lastInitial;
    };

    const formatDate = (date) => {
      if (!date) return t('common.notAvailable');
      const d = new Date(date);
      return d.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    };

    const timeAgo = (date) => {
      if (!date) return t('common.notAvailable');
      const now = new Date();
      const past = new Date(date);
      const diffMs = now - past;
      const diffSec = Math.floor(diffMs / 1000);
      
      if (diffSec < 60) return t('timeAgo.seconds', { count: diffSec });
      
      const diffMin = Math.floor(diffSec / 60);
      if (diffMin < 60) return t('timeAgo.minutes', { count: diffMin });
      
      const diffHr = Math.floor(diffMin / 60);
      if (diffHr < 24) return t('timeAgo.hours', { count: diffHr });
      
      const diffDay = Math.floor(diffHr / 24);
      if (diffDay < 7) return t('timeAgo.days', { count: diffDay });
      
      const diffWeek = Math.floor(diffDay / 7);
      if (diffWeek < 4) return t('timeAgo.weeks', { count: diffWeek });
      
      return formatDate(date);
    };

    const openMessage = async (msg) => {
      if (!msg.is_read || msg.is_read == 0) {
        // Update UI immediately
        msg.is_read = 1;
        
        // Update in backend
        try {
          const response = await fetch('http://127.0.0.1:8000/update_message_status.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              id: msg.id,
              is_read: 1,
            }),
          });

          if (!response.ok) {
            console.error(t('errors.updateMessageStatusError'));
            // Revert the change if backend fails
            msg.is_read = 0;
          }
        } catch (err) {
          console.error(t('errors.updateMessageStatusError'), err);
          // Revert the change if request fails
          msg.is_read = 0;
        }
      }
    };

    const replyToMessage = (msg) => {
      // TODO: Implement reply functionality
      alerts.alertInfo(t('common.comingSoon'), t('messages.replyTo', { name: `${msg.nom} ${msg.prenom}` }));
    };

    const deleteMessage = async (msg) => {
      const confirmed = await alerts.confirmDelete(t('messages.deleteConfirm', { name: `${msg.nom} ${msg.prenom}` }));
      if (!confirmed) return;

      try {
        const response = await fetch('http://127.0.0.1:8000/delete_message.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            id: msg.id,
          }),
        });

        if (response.ok) {
          // Remove from UI only if backend deletion succeeds
          messages.value = messages.value.filter((m) => m.id !== msg.id);
          await alerts.alertSuccess(t('common.deleted'), t('messages.deleteSuccess'));
        } else {
          await alerts.alertError(t('common.error'), t('messages.deleteError'));
        }
      } catch (err) {
        console.error(t('errors.deleteMessageError'), err);
        await alerts.alertError(t('common.error'), t('messages.deleteError'));
      }
    };

    const logout = () => {
      localStorage.removeItem('token');
      router.push('/login');
    };

    onMounted(() => {
      fetchUser();
      fetchMessages();
    });

    return {
      theme,
      t,
      isExpanded,
      user,
      messages,
      loading,
      error,
      activeFilter,
      menuItems,
      totalMessages,
      unreadCount,
      readCount,
      filteredMessages,
      fetchMessages,
      getInitials,
      formatDate,
      timeAgo,
      openMessage,
      replyToMessage,
      deleteMessage,
      logout,
    };
  },
};
</script>