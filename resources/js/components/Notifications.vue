<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="user">
          <img :src="user.avatar || '/images/avatar.png'" :alt="t('user.avatarAlt')" class="avatar" />
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

        <!-- Notifications Content -->
        <div class="notifications-container">
          <!-- Header -->
          <div class="page-header">
            <div class="header-left">
              <i class="bi bi-bell-fill"></i>
              <div>
                <h1>{{ t('notifications.title') }}</h1>
                <p class="subtitle">{{ unreadCount }} {{ t('notifications.unreadMessages') }}</p>
              </div>
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
        return this.notifications.filter(n => !n.read);
      }
      return this.notifications;
    },
    unreadCount() {
      return this.notifications.filter(n => !n.read).length;
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
        const response = await axios.get('/api/user', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user:', error);
      }
    },
    async fetchNotifications() {
      this.loading = true;
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('/api/notifications', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.notifications = response.data;
      } catch (error) {
        console.error('Error fetching notifications:', error);
        Swal.fire({
          icon: 'error',
          title: this.t('notifications.error'),
          text: this.t('notifications.errorFetching'),
        });
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

<style scoped>
/* Layout Structure - matches workspace global styles */
.home-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.layout {
  display: flex;
}

.main-content {
  position: relative;
  flex: 1;
  margin-left: 70px;
  padding: 1.5rem;
  transition: margin-left 0.3s ease;
}

/* Adjust margin when sidebar is expanded */
:deep(.sidebar.expanded ~ .main-content) {
  margin-left: 240px;
}

/* Profile Float */
.profile-float {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.profile-float:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.profile-float .avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Navbar - transparent background like other pages */
.navbar {
  display: flex;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  background: transparent;
  border-radius: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.nav-link {
  padding: 10px 20px;
  border-radius: 10px;
  text-decoration: none;
  color: #546A88;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s;
  white-space: nowrap;
}

.nav-link:hover {
  background: rgba(84, 106, 136, 0.1);
  color: #344966;
  transform: translateY(-2px);
}

.nav-link.active {
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.3);
}

/* Notifications Container */
.notifications-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Page Header */
.page-header {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.header-left > i {
  font-size: 2.5rem;
  color: #344966;
  background: linear-gradient(135deg, rgba(52, 73, 102, 0.1), rgba(84, 106, 136, 0.1));
  width: 70px;
  height: 70px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-left h1 {
  margin: 0 0 0.25rem 0;
  font-size: 1.75rem;
  font-weight: 600;
  color: #344966;
}

.subtitle {
  margin: 0;
  font-size: 0.95rem;
  color: #748BAA;
  font-weight: 400;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn-action {
  background: white;
  border: 2px solid #E5E7EB;
  color: #748BAA;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s;
}

.btn-action:hover {
  border-color: #344966;
  color: #344966;
  background: rgba(52, 73, 102, 0.05);
}

.btn-mark-all {
  border-color: #344966;
  color: #344966;
}

.btn-mark-all:hover {
  background: #344966;
  color: white;
}

.filter-tabs {
  display: flex;
  gap: 0.5rem;
  background: #F3F4F6;
  padding: 0.35rem;
  border-radius: 10px;
}

.filter-tab {
  background: transparent;
  border: none;
  color: #748BAA;
  padding: 0.6rem 1.25rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
  white-space: nowrap;
}

.filter-tab:hover {
  color: #344966;
  background: rgba(52, 73, 102, 0.08);
}

.filter-tab.active {
  background: white;
  color: #344966;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #748BAA;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #E5E7EB;
  border-top-color: #344966;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  margin: 0;
  font-size: 1rem;
  font-weight: 500;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.empty-state i {
  font-size: 4rem;
  color: #CBD5E1;
  margin-bottom: 1.5rem;
  display: block;
}

.empty-state h3 {
  color: #64748b;
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.empty-state p {
  margin: 0;
  font-size: 1rem;
  color: #94A3B8;
}

/* Notifications List */
.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Notification Card */
.notification-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transition: all 0.2s;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
  border-left: 4px solid transparent;
}

.notification-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.notification-card.unread {
  border-left-color: #344966;
  background: linear-gradient(to right, rgba(52, 73, 102, 0.03) 0%, white 10%);
}

.notification-content {
  flex: 1;
}

.notification-header {
  display: flex;
  gap: 1.25rem;
  align-items: flex-start;
}

.notification-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.notification-icon.alert {
  background: linear-gradient(135deg, #FEE2E2, #FECACA);
  color: #DC2626;
}

.notification-icon.message {
  background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
  color: #2563EB;
}

.notification-icon.system {
  background: linear-gradient(135deg, #E0E7FF, #C7D2FE);
  color: #4F46E5;
}

.notification-icon.success {
  background: linear-gradient(135deg, #D1FAE5, #A7F3D0);
  color: #059669;
}

.notification-icon.warning {
  background: linear-gradient(135deg, #FEF3C7, #FDE68A);
  color: #D97706;
}

.notification-icon.maintenance {
  background: linear-gradient(135deg, #F3E8FF, #E9D5FF);
  color: #9333EA;
}

.notification-info {
  flex: 1;
}

.notification-title {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #1e293b;
}

.notification-message {
  margin: 0 0 0.75rem 0;
  font-size: 0.9rem;
  color: #64748b;
  line-height: 1.5;
}

.notification-time {
  font-size: 0.8rem;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.notification-time i {
  font-size: 0.75rem;
}

/* Notification Actions */
.notification-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  flex-shrink: 0;
}

.btn-view {
  background: linear-gradient(135deg, #748BAA, #546A88);
  color: white;
  text-decoration: none;
  padding: 0.65rem 1.25rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.2s;
  border: none;
  cursor: pointer;
  white-space: nowrap;
}

.btn-view:hover {
  background: linear-gradient(135deg, #546A88, #344966);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.25);
}

.btn-icon {
  background: #F3F4F6;
  border: none;
  color: #748BAA;
  cursor: pointer;
  padding: 0.65rem;
  border-radius: 8px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
}

.btn-icon:hover {
  background: #E5E7EB;
  color: #344966;
}

.btn-icon.btn-delete:hover {
  background: #FEE2E2;
  color: #DC2626;
}

/* Responsive */
@media (max-width: 1024px) {
  .main-content {
    margin-left: 0;
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }

  .page-header {
    padding: 1.5rem;
    flex-direction: column;
    align-items: flex-start;
  }

  .header-left {
    flex-direction: column;
    align-items: flex-start;
  }

  .header-left > i {
    width: 60px;
    height: 60px;
    font-size: 2rem;
  }

  .header-left h1 {
    font-size: 1.5rem;
  }

  .header-actions {
    width: 100%;
    flex-direction: column;
    align-items: stretch;
  }

  .filter-tabs {
    justify-content: center;
  }

  .notification-card {
    flex-direction: column;
    align-items: flex-start;
  }

  .notification-actions {
    width: 100%;
    justify-content: space-between;
  }

  .btn-view {
    flex: 1;
    justify-content: center;
  }
}
</style>
