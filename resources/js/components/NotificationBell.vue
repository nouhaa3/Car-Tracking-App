<template>
  <div class="notifications-wrapper">
    <!-- Bell Icon with Badge -->
    <div class="notification-bell" @click="toggleDropdown" ref="bellIcon">
      <i class="bi bi-bell-fill"></i>
      <span v-if="unreadCount > 0" class="notification-badge">{{ displayCount }}</span>
    </div>

    <!-- Dropdown Notifications List -->
    <Transition name="dropdown">
      <div v-if="showDropdown" class="notifications-dropdown" ref="dropdown">
        <div class="dropdown-header">
          <h3>
            <i class="bi bi-bell"></i>
            {{ t('notifications.title') }}
          </h3>
          <div class="dropdown-actions">
            <button @click="markAllAsRead" class="btn-text" v-if="unreadCount > 0">
              <i class="bi bi-check-all"></i>
              {{ t('notifications.markAllRead') }}
            </button>
            <router-link to="/notifications" class="btn-text" @click="closeDropdown">
              <i class="bi bi-list-ul"></i>
              {{ t('notifications.viewAll') }}
            </router-link>
          </div>
        </div>

        <div class="notifications-list" v-if="recentNotifications.length > 0">
          <div
            v-for="notification in recentNotifications"
            :key="notification.idNotification"
            class="notification-item"
            :class="{ 'unread': !notification.is_read, [notification.priority]: true }"
            @click="handleNotificationClick(notification)"
          >
            <div class="notification-icon" :style="{ backgroundColor: notification.background_color }">
              <i :class="notification.icon" :style="{ color: notification.color }"></i>
            </div>
            <div class="notification-content">
              <h4>{{ notification.title }}</h4>
              <p>{{ notification.message }}</p>
              <span class="notification-time">{{ notification.time_ago }}</span>
            </div>
            <div class="notification-actions">
              <button @click.stop="markAsRead(notification.idNotification)" v-if="!notification.is_read" class="btn-mark-read">
                <i class="bi bi-check"></i>
              </button>
            </div>
          </div>
        </div>

        <div v-else class="empty-notifications">
          <i class="bi bi-bell-slash"></i>
          <p>{{ t('notifications.noNotifications') }}</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

export default {
  name: 'NotificationBell',
  
  setup() {
    const { t } = useI18n();
    const router = useRouter();
    
    const showDropdown = ref(false);
    const recentNotifications = ref([]);
    const unreadCount = ref(0);
    const bellIcon = ref(null);
    const dropdown = ref(null);
    const pollInterval = ref(null);

    const displayCount = computed(() => {
      return unreadCount.value > 99 ? '99+' : unreadCount.value;
    });

    const fetchUnreadCount = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/notifications/unread-count', {
          headers: { Authorization: `Bearer ${token}` }
        });
        unreadCount.value = response.data.count;
      } catch (error) {
        console.error('Error fetching unread count:', error);
      }
    };

    const fetchRecentNotifications = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/notifications/recent', {
          headers: { Authorization: `Bearer ${token}` }
        });
        recentNotifications.value = response.data;
      } catch (error) {
        console.error('Error fetching notifications:', error);
      }
    };

    const toggleDropdown = async () => {
      showDropdown.value = !showDropdown.value;
      if (showDropdown.value) {
        await fetchRecentNotifications();
      }
    };

    const closeDropdown = () => {
      showDropdown.value = false;
    };

    const markAsRead = async (notificationId) => {
      try {
        const token = localStorage.getItem('token');
        await axios.patch(`http://127.0.0.1:8000/api/notifications/${notificationId}/read`, {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        // Update local state
        const notification = recentNotifications.value.find(n => n.idNotification === notificationId);
        if (notification) {
          notification.is_read = true;
        }
        
        await fetchUnreadCount();
      } catch (error) {
        console.error('Error marking as read:', error);
      }
    };

    const markAllAsRead = async () => {
      try {
        const token = localStorage.getItem('token');
        await axios.patch('http://127.0.0.1:8000/api/notifications/mark-all-read', {}, {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        // Update local state
        recentNotifications.value.forEach(n => n.is_read = true);
        unreadCount.value = 0;
      } catch (error) {
        console.error('Error marking all as read:', error);
      }
    };

    const handleNotificationClick = async (notification) => {
      // Mark as read
      if (!notification.is_read) {
        await markAsRead(notification.idNotification);
      }
      
      // Navigate if link provided
      if (notification.metadata?.link) {
        closeDropdown();
        router.push(notification.metadata.link);
      }
    };

    const handleClickOutside = (event) => {
      if (
        showDropdown.value &&
        bellIcon.value &&
        dropdown.value &&
        !bellIcon.value.contains(event.target) &&
        !dropdown.value.contains(event.target)
      ) {
        closeDropdown();
      }
    };

    onMounted(() => {
      fetchUnreadCount();
      
      // Poll for new notifications every 30 seconds
      pollInterval.value = setInterval(fetchUnreadCount, 30000);
      
      // Click outside listener
      document.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
      if (pollInterval.value) {
        clearInterval(pollInterval.value);
      }
      document.removeEventListener('click', handleClickOutside);
    });

    return {
      t,
      showDropdown,
      recentNotifications,
      unreadCount,
      displayCount,
      bellIcon,
      dropdown,
      toggleDropdown,
      closeDropdown,
      markAsRead,
      markAllAsRead,
      handleNotificationClick
    };
  }
};
</script>

.notification-bell {
  position: relative;
  cursor: pointer;
  padding: 0.75rem;
  border-radius: 50%;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.1);
}

.notification-bell:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.1);
}

.notification-bell i {
  font-size: 1.5rem;
  color: #344966;
  transition: color 0.3s ease;
}

.notification-bell:hover i {
  color: #0EA5E9;
}

.notification-badge {
  position: absolute;
  top: 5px;
  right: 5px;
  background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
  color: white;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.15rem 0.35rem;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.15);
  }
}

.notifications-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 420px;
  max-height: 600px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  overflow: hidden;
  border: 1px solid rgba(52, 73, 102, 0.1);
}

.dropdown-header {
  padding: 1.25rem 1.5rem;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  color: white;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dropdown-actions {
  margin-top: 0.75rem;
  display: flex;
  gap: 1rem;
}

.btn-text {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.9);
  cursor: pointer;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.35rem;
  transition: color 0.2s ease;
  text-decoration: none;
  padding: 0;
}

.btn-text:hover {
  color: white;
}

.notifications-list {
  max-height: 500px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid rgba(52, 73, 102, 0.1);
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.notification-item:hover {
  background-color: rgba(52, 73, 102, 0.05);
}

.notification-item.unread {
  background-color: rgba(14, 165, 233, 0.05);
  border-left: 3px solid #0EA5E9;
}

.notification-item.critical {
  border-left-color: #EF4444;
}

.notification-item.high {
  border-left-color: #F59E0B;
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.notification-icon i {
  font-size: 1.25rem;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-content h4 {
  margin: 0 0 0.25rem 0;
  font-size: 0.95rem;
  font-weight: 600;
  color: #344966;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.notification-content p {
  margin: 0 0 0.5rem 0;
  font-size: 0.85rem;
  color: #546A88;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.notification-time {
  font-size: 0.75rem;
  color: #748BAA;
}

.notification-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.btn-mark-read {
  background: none;
  border: none;
  color: #10B981;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.btn-mark-read:hover {
  background-color: rgba(16, 185, 129, 0.1);
}

.empty-notifications {
  text-align: center;
  padding: 3rem 2rem;
  color: #748BAA;
}

.empty-notifications i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-notifications p {
  margin: 0;
  font-size: 0.95rem;
}

/* Dropdown transition */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.3s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Scrollbar */
.notifications-list::-webkit-scrollbar {
  width: 6px;
}

.notifications-list::-webkit-scrollbar-track {
  background: rgba(52, 73, 102, 0.05);
}

.notifications-list::-webkit-scrollbar-thumb {
  background: rgba(52, 73, 102, 0.2);
  border-radius: 3px;
}

