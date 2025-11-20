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

        <nav class="navbar mb-5">
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

        <div class="profile-wrapper">
          <!-- Page Header -->
          <div class="page-header">
            <div class="header-left">
              <h1>{{ t('notifications.title') }}</h1>
              <p>{{ unreadCount }} {{ t('notifications.unreadMessages') }}</p>
            </div>
            <div class="header-actions">
              <button 
                v-if="unreadCount > 0" 
                @click="markAllAsRead" 
                class="btn-action btn-mark-all"
              >
                <i class="bi bi-check2-all"></i>
                {{ t('notifications.markAllRead') }}
              </button>
              <div class="filter-tabs">
                <button 
                  :class="['filter-tab', { active: filter === 'all' }]"
                  @click="filter = 'all'"
                >
                  {{ t('notifications.all') }}
                </button>
                <button 
                  :class="['filter-tab', { active: filter === 'unread' }]"
                  @click="filter = 'unread'"
                >
                  {{ t('notifications.unread') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Notifications List -->
          <div v-if="loading" class="loading-state">
            <div class="spinner"></div>
            <p>{{ t('notifications.loading') }}</p>
          </div>

          <div v-else-if="filteredNotifications.length === 0" class="empty-state">
            <i class="bi bi-bell-slash"></i>
            <h3>{{ t('notifications.noNotifications') }}</h3>
            <p>{{ t('notifications.noNotificationsDesc') }}</p>
          </div>

          <div v-else class="notifications-list">
            <div 
              v-for="notification in filteredNotifications" 
              :key="notification.id"
              :class="['notification-card', { 'unread': !notification.read }]"
            >
              <div class="notification-content">
                <div class="notification-header">
                  <div :class="['notification-icon', notification.type]">
                    <i :class="getNotificationIcon(notification.type)"></i>
                  </div>
                  <div class="notification-info">
                    <h4 class="notification-title">{{ notification.title }}</h4>
                    <p class="notification-message">{{ notification.message }}</p>
                    <span class="notification-time">
                      <i class="bi bi-clock"></i>
                      {{ formatTime(notification.created_at) }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="notification-actions">
                <router-link 
                  v-if="notification.link" 
                  :to="notification.link" 
                  class="btn-view"
                  @click="markAsRead(notification.id)"
                >
                  <i class="bi bi-eye"></i>
                  {{ t('notifications.viewDetails') }}
                </router-link>
                <button 
                  v-if="!notification.read"
                  @click.stop="markAsRead(notification.id)"
                  class="btn-icon"
                  :title="t('notifications.markRead')"
                >
                  <i class="bi bi-check2"></i>
                </button>
                <button 
                  @click.stop="deleteNotification(notification.id)"
                  class="btn-icon btn-delete"
                  :title="t('notifications.delete')"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import Swal from 'sweetalert2';
import Sidebar from './Sidebar.vue';

export default {
  name: 'Notifications',
  components: {
    Sidebar,
  },
  setup() {
    const theme = inject("theme");
    const { t } = useI18n();
    return { theme, t };
  },
  data() {
    return {
      isExpanded: false,
      user: null,
      notifications: [],
      filter: 'all',
      loading: true,
      menuItems: [
        { label: this.t('menu.dashboard'), to: "/admindashboard" },
        { label: this.t('menu.catalog'), to: "/voitures/cataloguevoitures" },
        { label: this.t('menu.interventions'), to: "/interventions/catalogue" },
        { label: this.t('menu.alerts'), to: "/alertes" },
      ],
    };
  },
  computed: {
    filteredNotifications() {
      if (this.filter === 'unread') {
        return this.notifications.filter(n => !n.read_at && !n.is_read);
      }
      return this.notifications;
    },
    unreadCount() {
      return this.notifications.filter(n => !n.read_at && !n.is_read).length;
    },
  },
  mounted() {
    this.fetchUser();
    this.fetchNotifications();
  },
  methods: {
    async fetchUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push('/login');
          return;
        }
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.user = response.data;
        console.log('Notifications: User data loaded', this.user);
      } catch (error) {
        console.error('Error fetching user:', error);
        if (error.response?.status === 401) {
          localStorage.removeItem('token');
          this.$router.push('/login');
        }
      }
    },
    async fetchNotifications() {
      this.loading = true;
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push('/login');
          return;
        }
        const response = await axios.get('/api/notifications', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.notifications = Array.isArray(response.data) ? response.data : [];
      } catch (error) {
        console.error('Error fetching notifications:', error);
        this.notifications = []; // Ensure it's always an array
        if (error.response?.status === 401) {
          localStorage.removeItem('token');
          this.$router.push('/login');
        } else {
          Swal.fire({
            icon: 'error',
            title: this.t('notifications.error'),
            text: this.t('notifications.errorFetching'),
          });
        }
      } finally {
        this.loading = false;
      }
    },
    async markAsRead(id) {
      try {
        const token = localStorage.getItem('token');
        await axios.patch(`/api/notifications/${id}/read`, {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
          notification.read = true;
        }
      } catch (error) {
        console.error('Error marking as read:', error);
        Swal.fire({
          icon: 'error',
          title: this.t('notifications.error'),
          text: error.response?.data?.message || this.t('notifications.errorMarking'),
        });
      }
    },
    async markAllAsRead() {
      try {
        const token = localStorage.getItem('token');
        await axios.post('/api/notifications/mark-all-read', {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.notifications.forEach(n => n.read = true);
        
        Swal.fire({
          icon: 'success',
          title: this.t('notifications.allMarkedRead'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
        });
      } catch (error) {
        console.error('Error marking all as read:', error);
        Swal.fire({
          icon: 'error',
          title: this.t('notifications.error'),
          text: error.response?.data?.message || this.t('notifications.errorMarking'),
        });
      }
    },
    async deleteNotification(id) {
      const result = await Swal.fire({
        title: this.t('notifications.confirmDelete'),
        text: this.t('notifications.confirmDeleteText'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C85A54',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: this.t('notifications.delete'),
        cancelButtonText: this.t('notifications.cancel'),
      });

      if (result.isConfirmed) {
        try {
          const token = localStorage.getItem('token');
          await axios.delete(`/api/notifications/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.notifications = this.notifications.filter(n => n.id !== id);
          
          Swal.fire({
            icon: 'success',
            title: this.t('notifications.deleted'),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
          });
        } catch (error) {
          console.error('Error deleting notification:', error);
          Swal.fire({
            icon: 'error',
            title: this.t('notifications.error'),
            text: error.response?.data?.message || this.t('notifications.errorDeleting'),
          });
        }
      }
    },
    getNotificationIcon(type) {
      const icons = {
        alert: 'bi bi-exclamation-triangle-fill',
        message: 'bi bi-chat-dots-fill',
        system: 'bi bi-gear-fill',
        success: 'bi bi-check-circle-fill',
        warning: 'bi bi-exclamation-circle-fill',
        maintenance: 'bi bi-wrench-adjustable-circle-fill',
      };
      return icons[type] || 'bi bi-bell-fill';
    },
    formatTime(timestamp) {
      const now = new Date();
      const date = new Date(timestamp);
      const diff = Math.floor((now - date) / 1000);

      if (diff < 60) return this.t('notifications.justNow');
      if (diff < 3600) return this.t('notifications.minutesAgo', { count: Math.floor(diff / 60) });
      if (diff < 86400) return this.t('notifications.hoursAgo', { count: Math.floor(diff / 3600) });
      return this.t('notifications.daysAgo', { count: Math.floor(diff / 86400) });
    },
  },
};
</script>
