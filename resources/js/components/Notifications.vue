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

        <div class="profile-wrapper">
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

          <!-- Page Header -->
          <div class="page-header">
            <div class="header-left">
              <h1>{{ t('notifications.title') }}</h1>
              <p>{{ unreadCount }} {{ t('notifications.unreadMessages') }}</p>
            </div>
            <div class="header-actions">
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
              :key="notification.idNotification"
              :class="['notification-card', { 'unread': !notification.is_read && !notification.read_at }]"
              @click="handleNotificationClick(notification)"
              :style="(!notification.is_read && !notification.read_at) ? 'cursor: pointer;' : ''"
            >
              <div class="notification-content">
                <div class="notification-header">
                  <div :class="['notification-icon', notification.type]">
                    <i :class="getNotificationIcon(notification.type)"></i>
                  </div>
                  <div class="notification-info">
                    <h4 class="notification-title">{{ notification.title }}</h4>
                    <p class="notification-message">{{ notification.message }}</p>
                    
                    <!-- Pending User Details -->
                    <div v-if="notification.type === 'pending_registration' && notification.metadata" class="pending-user-info">
                      <p><strong>Email:</strong> {{ notification.metadata.pending_user_email }}</p>
                    </div>

                    <!-- Urgent Alert Details -->
                    <div v-if="notification.type === 'alert_urgent' && notification.metadata" class="alert-info">
                      <p><strong>{{ t('notifications.vehicle') }}:</strong> {{ notification.metadata.vehicle_name }}</p>
                      <p><strong>{{ t('notifications.priority') }}:</strong> 
                        <span :class="['priority-badge-small', `priority-${notification.metadata.priority}`]">
                          {{ getPriorityLabel(notification.metadata.priority) }}
                        </span>
                      </p>
                    </div>
                    
                    <span class="notification-time">
                      <i class="bi bi-clock"></i>
                      {{ formatTime(notification.created_at) }}
                    </span>

                    <!-- Notification Actions Moved Below -->
                    <div class="notification-actions-bottom">
                      <!-- Approval Actions for Pending Registrations -->
                      <template v-if="notification.type === 'pending_registration'">
                        <!-- Show buttons if not processed -->
                        <template v-if="!notification.metadata?.processed && !notification.is_read && !notification.read_at">
                          <button 
                            @click.stop="approveUser(notification)"
                            class="btn-action btn-edit"
                          >
                            {{ t('notifications.approve') }}
                          </button>
                          <button 
                            @click.stop="rejectUser(notification)"
                            class="btn-action btn-delete"
                          >
                            {{ t('notifications.reject') }}
                          </button>
                        </template>
                        <!-- Show status text if processed -->
                        <template v-else>
                          <span :class="['status-text', notification.metadata?.action === 'rejected' ? 'rejected' : 'approved']">
                            {{ notification.metadata?.action === 'rejected' ? 'Refusé' : 'Approuvé' }}
                          </span>
                        </template>
                      </template>

                      <!-- Actions for Urgent Alerts -->
                      <template v-else-if="notification.type === 'alert_urgent' && notification.metadata">
                        <router-link 
                          :to="`/alertes/${notification.metadata.alert_id}`"
                          class="btn-action btn-edit"
                          @click.stop
                        >
                          {{ t('notifications.viewAlert') }}
                        </router-link>
                      </template>
                    </div>
                  </div>
                </div>
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
import alerts from '@/utils/alerts';
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
        return this.notifications.filter(n => !n.is_read && !n.read_at);
      }
      return this.notifications;
    },
    unreadCount() {
      return this.notifications.filter(n => !n.is_read && !n.read_at).length;
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
        // Use response.data.data for paginated notifications
        const notifications = Array.isArray(response.data.data) ? response.data.data : [];
        // Parse metadata if it's a JSON string and ensure ID is set
        this.notifications = notifications.map(notif => ({
          ...notif,
          idNotification: notif.idNotification || notif.id,
          metadata: typeof notif.metadata === 'string' ? JSON.parse(notif.metadata) : notif.metadata
        }));
      } catch (error) {
        console.error('Error fetching notifications:', error);
        this.notifications = []; // Ensure it's always an array
        if (error.response?.status === 401) {
          localStorage.removeItem('token');
          this.$router.push('/login');
        } else {
          await alerts.alertError(
            this.t('notifications.error'),
            this.t('notifications.errorFetching')
          );
        }
      } finally {
        this.loading = false;
      }
    },
    async markAsRead(id) {
      if (!id) {
        console.error('No notification ID provided');
        return;
      }
      try {
        const token = localStorage.getItem('token');
        await axios.patch(`/api/notifications/${id}/read`, {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        // Update the notification in the list with Vue reactivity
        const notificationIndex = this.notifications.findIndex(n => n.idNotification === id);
        if (notificationIndex !== -1) {
          this.notifications[notificationIndex] = {
            ...this.notifications[notificationIndex],
            is_read: true,
            read_at: new Date().toISOString()
          };
        }
      } catch (error) {
        console.error('Error marking as read:', error);
        await alerts.alertError(
          this.t('notifications.error'),
          error.response?.data?.message || this.t('notifications.errorMarking')
        );
      }
    },
    handleNotificationClick(notification) {
      // Only mark as read if it's unread and has an ID
      if (!notification.is_read && !notification.read_at && notification.idNotification) {
        this.markAsRead(notification.idNotification);
      }
    },
    async markAllAsRead() {
      try {
        const token = localStorage.getItem('token');
        await axios.patch('/api/notifications/mark-all-read', {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        // Update all notifications in the list
        this.notifications.forEach(n => {
          n.is_read = true;
          n.read_at = new Date().toISOString();
        });
        
        alerts.success(
          this.t('common.success'),
          this.t('notifications.allMarkedRead')
        );
      } catch (error) {
        console.error('Error marking all as read:', error);
        await alerts.alertError(
          this.t('notifications.error'),
          error.response?.data?.message || this.t('notifications.errorMarking')
        );
      }
    },
    async deleteNotification(id) {
      const confirmed = await alerts.confirm({
        title: this.t('notifications.confirmDelete'),
        message: this.t('notifications.confirmDeleteText'),
        confirmText: this.t('notifications.delete'),
        cancelText: this.t('notifications.cancel'),
        type: 'danger'
      });

      if (confirmed) {
        try {
          const token = localStorage.getItem('token');
          await axios.delete(`/api/notifications/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.notifications = this.notifications.filter(n => n.idNotification !== id);
          
          alerts.success(
            this.t('common.deleted'),
            this.t('notifications.deleted')
          );
        } catch (error) {
          console.error('Error deleting notification:', error);
          await alerts.alertError(
            this.t('notifications.error'),
            error.response?.data?.message || this.t('notifications.errorDeleting')
          );
        }
      }
    },
    getNotificationIcon(type) {
      const icons = {
        alert: 'bi bi-exclamation-triangle-fill',
        alert_urgent: 'bi bi-exclamation-triangle-fill',
        message: 'bi bi-chat-dots-fill',
        system: 'bi bi-gear-fill',
        success: 'bi bi-check-circle-fill',
        warning: 'bi bi-exclamation-circle-fill',
        maintenance: 'bi bi-wrench-adjustable-circle-fill',
        pending_registration: 'bi bi-person-plus-fill',
      };
      return icons[type] || 'bi bi-bell-fill';
    },
    getPriorityLabel(priority) {
      const labels = {
        'critique': this.t('alerts.critical'),
        'haute': this.t('alerts.high'),
        'moyenne': this.t('alerts.medium'),
        'faible': this.t('alerts.low')
      };
      return labels[priority] || priority;
    },
    async approveUser(notification) {
      const confirmed = await alerts.confirm({
        title: this.t('notifications.approveConfirm'),
        message: this.t('notifications.approveConfirmText', { name: notification.metadata?.pending_user_name }),
        confirmText: this.t('notifications.approve'),
        cancelText: this.t('notifications.cancel'),
        type: 'info'
      });

      if (confirmed) {
        try {
          const token = localStorage.getItem('token');
          const pendingUserId = notification.metadata.pending_user_id;
          console.log('Approving user ID:', pendingUserId);
          
          const response = await axios.post(
            `http://127.0.0.1:8000/api/users/${pendingUserId}/approve`,
            {},
            { headers: { Authorization: `Bearer ${token}` } }
          );
          
          console.log('Approval response:', response.data);
          
          // Mark notification as processed and read
          const notif = this.notifications.find(n => n.idNotification === notification.idNotification);
          if (notif) {
            notif.is_read = true;
            notif.read_at = new Date().toISOString();
            if (notif.metadata) {
              notif.metadata.processed = true;
              notif.metadata.action = 'approved';
            }
          }
          
          await alerts.alertSuccess(
            this.t('notifications.userApproved'),
            this.t('notifications.userApprovedText', { name: notification.metadata?.pending_user_name })
          );
        } catch (error) {
          console.error('Error approving user:', error);
          console.error('Error details:', error.response?.data);
          
          // If 404, the user was already processed - mark notification as processed
          if (error.response?.status === 404) {
            // Mark notification as processed to hide buttons
            const notif = this.notifications.find(n => n.idNotification === notification.idNotification);
            if (notif && notif.metadata) {
              notif.metadata.processed = true;
              notif.metadata.action = 'approved';
            }
            
            await alerts.alertError(
              this.t('notifications.error'),
              error.response?.data?.error || 'Cette demande a déjà été traitée.'
            );
            
            // Refresh notifications to get updated read status from database
            await this.fetchNotifications();
          } else {
            await alerts.alertError(
              this.t('notifications.error'),
              error.response?.data?.message || this.t('notifications.errorApproving')
            );
          }
        }
      }
    },
    async rejectUser(notification) {
      const confirmed = await alerts.confirm({
        title: this.t('notifications.rejectConfirm'),
        message: this.t('notifications.rejectConfirmText', { name: notification.metadata?.pending_user_name }),
        confirmText: this.t('notifications.reject'),
        cancelText: this.t('notifications.cancel'),
        type: 'danger'
      });

      if (confirmed) {
        try {
          const token = localStorage.getItem('token');
          const pendingUserId = notification.metadata.pending_user_id;
          console.log('Rejecting user ID:', pendingUserId);
          
          const response = await axios.post(
            `http://127.0.0.1:8000/api/users/${pendingUserId}/reject`,
            {},
            { headers: { Authorization: `Bearer ${token}` } }
          );
          
          console.log('Rejection response:', response.data);
          
          // Mark notification as processed and read
          const notif = this.notifications.find(n => n.idNotification === notification.idNotification);
          if (notif) {
            notif.is_read = true;
            notif.read_at = new Date().toISOString();
            if (notif.metadata) {
              notif.metadata.processed = true;
              notif.metadata.action = 'rejected';
            }
          }
          
          await alerts.alertSuccess(
            this.t('notifications.userRejected'),
            this.t('notifications.userRejectedText', { name: notification.metadata?.pending_user_name })
          );
        } catch (error) {
          console.error('Error rejecting user:', error);
          console.error('Error details:', error.response?.data);
          
          // If 404, the user was already processed - mark notification as processed
          if (error.response?.status === 404) {
            // Mark notification as processed to hide buttons
            const notif = this.notifications.find(n => n.idNotification === notification.idNotification);
            if (notif && notif.metadata) {
              notif.metadata.processed = true;
              notif.metadata.action = 'rejected';
            }
            
            await alerts.alertError(
              this.t('notifications.error'),
              error.response?.data?.error || 'Cette demande a déjà été traitée.'
            );
            
            // Refresh notifications to get updated read status from database
            await this.fetchNotifications();
          } else {
            await alerts.alertError(
              this.t('notifications.error'),
              error.response?.data?.message || this.t('notifications.errorRejecting')
            );
          }
        }
      }
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
.notification-actions {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  flex-wrap: wrap;
}

.notification-actions .btn-action {
  padding: 0.75rem 1.5rem;
  font-size: 0.9rem;
  min-width: 130px;
  width: 130px;
  flex: 0 0 auto;
  justify-content: center;
  text-align: center;
  white-space: nowrap;
}

.notification-actions-bottom {
  display: flex;
  gap: 0.75rem;
  align-items: center;
  justify-content: flex-end;
  margin-top: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px solid #E5E7EB;
}

.notification-actions-bottom .btn-action {
  padding: 0.65rem 1.25rem;
  font-size: 0.85rem;
  min-width: 110px;
}

.status-text {
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-text.approved {
  color: #155724;
}

.status-text.rejected {
  color: #721c24;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  margin-top: 0.5rem;
}

.status-badge.approved {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-badge.rejected {
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.status-badge i {
  font-size: 1rem;
}

.alert-info {
  margin-top: 0.5rem;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 6px;
  font-size: 0.875rem;
}

.alert-info p {
  margin: 0.25rem 0;
  color: #495057;
}

.priority-badge-small {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.priority-badge-small.priority-critique {
  background: #C85A54;
  color: white;
}

.priority-badge-small.priority-haute {
  background: #E89B5A;
  color: white;
}

.priority-badge-small.priority-moyenne {
  background: #F4C542;
  color: #1a1a1a;
}

.priority-badge-small.priority-faible {
  background: #8B9D83;
  color: white;
}
</style>

