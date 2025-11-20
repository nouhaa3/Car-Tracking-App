<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="user">
          <template v-if="user.avatar">
            <img :src="user.avatar" :alt="t('common.userAvatar')" class="avatar" />
          </template>
          <template v-else>
            <div class="avatar-initials-circle">
              {{ getUserInitials() }}
            </div>
          </template>
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
            <h1>{{ t('messages.title') }}</h1>
            <p>{{ t('messages.subtitle') }}</p>
          </div>
          <div class="header-actions">
            <div class="filter-tabs">
              <button
                class="filter-tab"
                :class="{ active: activeFilter === 'all' }"
                @click="activeFilter = 'all'"
              >
                <span>{{ t('messages.filters.all') }}</span>
                <span class="count-badge">{{ messages.length }}</span>
              </button>
              <button
                class="filter-tab"
                :class="{ active: activeFilter === 'unread' }"
                @click="activeFilter = 'unread'"
              >
                <span>{{ t('messages.filters.unread') }}</span>
                <span class="count-badge unread">{{ unreadCount }}</span>
              </button>
              <button
                class="filter-tab"
                :class="{ active: activeFilter === 'read' }"
                @click="activeFilter = 'read'"
              >
                <span>{{ t('messages.filters.read') }}</span>
                <span class="count-badge">{{ readCount }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('common.loading') }}</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <p>{{ error }}</p>
          <button @click="fetchMessages" class="retry-btn">{{ t('common.retry') }}</button>
        </div>

        <!-- Messages List -->
        <div v-else-if="messages.length > 0" class="messages-container">
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
                <div class="message-footer-left">
                  <span v-if="msg.admin_reply" class="replied-text" @click.stop="viewReply(msg)" style="cursor: pointer;">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ t('messages.actions.replied') }}
                  </span>
                </div>
                <div class="message-actions">
                  <button
                    v-if="!msg.admin_reply"
                    class="btn-action btn-edit"
                    @click.stop="openReplyModal(msg)"
                    :title="t('messages.actions.reply')"
                  >
                    <i class="bi bi-reply-fill"></i>
                    <span>{{ t('messages.actions.reply') }}</span>
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

        <!-- Reply Modal -->
        <div v-if="showReplyModal" class="modal-overlay" @click="closeReplyModal">
          <div class="modal-content" @click.stop>
            <div class="modal-header">
              <h3>
                <i class="bi bi-reply-fill"></i>
                {{ t('messages.replyModal.title') }}
              </h3>
              <button class="close-btn" @click="closeReplyModal">
                <i class="bi bi-x"></i>
              </button>
            </div>

            <div class="modal-body">
              <!-- Original Message Info -->
              <div class="original-msg-info">
                <div class="msg-from">
                  <strong>{{ t('messages.replyModal.from') }}:</strong>
                  {{ selectedMessage?.nom }} {{ selectedMessage?.prenom }}
                </div>
                <div class="msg-email">
                  <strong>{{ t('messages.replyModal.email') }}:</strong>
                  {{ selectedMessage?.email }}
                </div>
                <div class="msg-original">
                  <strong>{{ t('messages.replyModal.originalMessage') }}:</strong>
                  <p class="original-text">{{ selectedMessage?.message }}</p>
                </div>
              </div>

              <!-- Reply Form -->
              <div class="reply-form">
                <label for="replyText">{{ t('messages.replyModal.yourReply') }}</label>
                <textarea
                  id="replyText"
                  v-model="replyText"
                  :placeholder="t('messages.replyModal.placeholder')"
                  rows="8"
                  class="form-control"
                ></textarea>
                <div class="char-count" :class="{ 'text-danger': replyText.length < 10 }">
                  {{ replyText.length }} {{ t('messages.replyModal.characters') }}
                  <span v-if="replyText.length < 10">({{ t('messages.replyModal.minChars') }})</span>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button class="btn-cancel" @click="closeReplyModal">
                {{ t('common.cancel') }}
              </button>
              <button
                class="btn-send"
                @click="sendReply"
                :disabled="replyText.length < 10 || sendingReply"
              >
                <i class="bi bi-send-fill"></i>
                {{ sendingReply ? t('messages.replyModal.sending') : t('messages.replyModal.send') }}
              </button>
            </div>
          </div>
        </div>

        <!-- View Reply Modal -->
        <div v-if="showViewReplyModal" class="modal-overlay" @click="closeViewReplyModal">
          <div class="modal-content" @click.stop>
            <div class="modal-header">
              <h3>
                <i class="bi bi-envelope-open-fill"></i>
                {{ t('messages.viewReplyModal.title') }}
              </h3>
              <button class="close-btn" @click="closeViewReplyModal">
                <i class="bi bi-x"></i>
              </button>
            </div>

            <div class="modal-body">
              <div class="reply-info">
                <div class="reply-meta">
                  <span class="replied-by">
                    <i class="bi bi-person-fill"></i>
                    {{ t('messages.viewReplyModal.repliedBy') }}: 
                    <strong>{{ selectedMessage?.admin_nom }} {{ selectedMessage?.admin_prenom }}</strong>
                  </span>
                  <span class="replied-at">
                    <i class="bi bi-clock-fill"></i>
                    {{ formatDate(selectedMessage?.replied_at) }}
                  </span>
                </div>
                
                <div class="original-msg-preview">
                  <strong>{{ t('messages.viewReplyModal.originalMessage') }}:</strong>
                  <p>{{ selectedMessage?.message }}</p>
                </div>

                <div class="admin-reply-content">
                  <strong>{{ t('messages.viewReplyModal.adminReply') }}:</strong>
                  <div class="reply-text">{{ selectedMessage?.admin_reply }}</div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button class="btn-primary" @click="closeViewReplyModal">
                {{ t('common.close') }}
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
    
    // Reply Modal
    const showReplyModal = ref(false);
    const showViewReplyModal = ref(false);
    const selectedMessage = ref(null);
    const replyText = ref('');
    const sendingReply = ref(false);

    const menuItems = [
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
        const token = localStorage.getItem('token');
        const res = await axios.get('http://127.0.0.1:8000/api/contact-messages', {
          headers: { Authorization: `Bearer ${token}` }
        });

        messages.value = Array.isArray(res.data) ? res.data : [];
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

    const getUserInitials = () => {
      if (!user.value) return '';
      const firstInitial = user.value.prenom?.charAt(0).toUpperCase() || user.value.name?.charAt(0).toUpperCase() || '';
      const lastInitial = user.value.nom?.charAt(0).toUpperCase() || '';
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
          const token = localStorage.getItem('token');
          await axios.patch(`http://127.0.0.1:8000/api/contact-messages/${msg.id}/read`, {}, {
            headers: { Authorization: `Bearer ${token}` }
          });
        } catch (err) {
          console.error(t('errors.updateMessageStatusError'), err);
          // Revert the change if request fails
          msg.is_read = 0;
        }
      }
    };

    const openReplyModal = (msg) => {
      selectedMessage.value = msg;
      replyText.value = '';
      showReplyModal.value = true;
    };

    const closeReplyModal = () => {
      showReplyModal.value = false;
      selectedMessage.value = null;
      replyText.value = '';
    };

    const sendReply = async () => {
      if (replyText.value.length < 10) {
        await alerts.alertWarning(t('common.warning'), t('messages.replyModal.minCharsError'));
        return;
      }

      sendingReply.value = true;

      try {
        const token = localStorage.getItem('token');
        const response = await axios.post(
          `http://127.0.0.1:8000/api/contact-messages/${selectedMessage.value.id}/reply`,
          { reply: replyText.value },
          { headers: { Authorization: `Bearer ${token}` } }
        );

        if (response.data.success) {
          await alerts.alertSuccess(
            t('messages.replyModal.successTitle'),
            t('messages.replyModal.successMessage')
          );

          // Update the message in the list
          const msgIndex = messages.value.findIndex(m => m.id === selectedMessage.value.id);
          if (msgIndex !== -1) {
            messages.value[msgIndex].admin_reply = replyText.value;
            messages.value[msgIndex].replied_at = new Date().toISOString();
            messages.value[msgIndex].replied_by = user.value.id;
            messages.value[msgIndex].admin_nom = user.value.nom;
            messages.value[msgIndex].admin_prenom = user.value.prenom;
          }

          closeReplyModal();
        }
      } catch (err) {
        console.error('Error sending reply:', err);
        await alerts.alertError(
          t('common.error'),
          err.response?.data?.message || t('messages.replyModal.errorMessage')
        );
      } finally {
        sendingReply.value = false;
      }
    };

    const viewReply = (msg) => {
      selectedMessage.value = msg;
      showViewReplyModal.value = true;
    };

    const closeViewReplyModal = () => {
      showViewReplyModal.value = false;
      selectedMessage.value = null;
    };

    const replyToMessage = (msg) => {
      openReplyModal(msg);
    };

    const deleteMessage = async (msg) => {
      const confirmed = await alerts.confirmDelete(t('messages.deleteConfirm', { name: `${msg.nom} ${msg.prenom}` }));
      if (!confirmed) return;

      try {
        const token = localStorage.getItem('token');
        await axios.delete(`http://127.0.0.1:8000/api/contact-messages/${msg.id}`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        // Remove from UI
        messages.value = messages.value.filter((m) => m.id !== msg.id);
        await alerts.alertSuccess(t('common.deleted'), t('messages.deleteSuccess'));
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
      getUserInitials,
      formatDate,
      timeAgo,
      openMessage,
      replyToMessage,
      deleteMessage,
      logout,
      // Reply Modal
      showReplyModal,
      showViewReplyModal,
      selectedMessage,
      replyText,
      sendingReply,
      openReplyModal,
      closeReplyModal,
      sendReply,
      viewReply,
      closeViewReplyModal,
    };
  },
};
</script>

.filter-tab {
  padding: 8px 20px;
  border: none;
  background: transparent;
  color: #6c757d;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  border-radius: 4px;
  white-space: nowrap;
}

.filter-tab span {
  display: inline-block;
}

.filter-tab .count-badge {
  background: rgba(52, 73, 102, 0.1);
  color: #6c757d;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.filter-tab:hover {
  background: rgba(52, 73, 102, 0.05);
  color: #344966;
}

.filter-tab.active {
  background: white;
  color: #344966;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-tab.active .count-badge {
  background: rgba(52, 73, 102, 0.15);
  color: #344966;
}

/* Responsive Design for Filters */
@media (max-width: 768px) {
  .filter-tabs {
    flex-direction: column;
    gap: 8px;
    width: 100%;
  }
  
  .filter-tab {
    width: 100%;
    justify-content: space-between;
  }
}

/* Message Footer */
.message-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  margin-top: 12px;
  border-top: 1px solid #e9ecef;
}

.message-footer-left {
  flex: 1;
}

.replied-text {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #28a745;
  font-weight: 500;
}

.replied-text i {
  font-size: 14px;
}

.replied-text:hover {
  color: #218838;
  text-decoration: underline;
}

.message-actions {
  display: flex;
  gap: 8px;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 700px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 30px;
  border-bottom: 1px solid #e9ecef;
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  color: white;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
  color: white;
}

.modal-header h3 i {
  color: white;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 28px;
  cursor: pointer;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: background-color 0.2s;
}

.close-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.modal-body {
  padding: 30px;
  max-height: calc(90vh - 180px);
  overflow-y: auto;
}

.original-msg-info {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 24px;
  border-left: 4px solid #344966;
}

.msg-from,
.msg-email,
.msg-original {
  margin-bottom: 12px;
}

.msg-from strong,
.msg-email strong,
.msg-original strong {
  color: #344966;
  display: block;
  margin-bottom: 4px;
  font-size: 14px;
}

.original-text {
  background: white;
  padding: 12px;
  border-radius: 8px;
  margin-top: 8px;
  font-style: italic;
  color: #495057;
  line-height: 1.6;
}

.reply-form label {
  display: block;
  font-weight: 600;
  color: #344966;
  margin-bottom: 8px;
  font-size: 15px;
}

.reply-form textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  min-height: 150px;
  transition: border-color 0.2s;
}

.reply-form textarea:focus {
  outline: none;
  border-color: #344966;
}

.char-count {
  text-align: right;
  font-size: 13px;
  color: #6c757d;
  margin-top: 8px;
}

.char-count.text-danger {
  color: #dc3545;
  font-weight: 600;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 30px;
  border-top: 1px solid #e9ecef;
  background-color: #f8f9fa;
}

.btn-cancel,
.btn-send,
.btn-primary {
  padding: 10px 24px;
  border-radius: 8px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background-color: #5a6268;
}

.btn-send,
.btn-primary {
  background: linear-gradient(135deg, #344966 0%, #546A88 100%);
  color: white;
}

.btn-send:hover,
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 73, 102, 0.3);
}

.btn-send:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* View Reply Modal Styles */
.reply-info {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.reply-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background-color: #f8f9fa;
  border-radius: 8px;
}

.replied-by,
.replied-at {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #495057;
}

.replied-by i,
.replied-at i {
  color: #344966;
}

.original-msg-preview {
  background-color: #f8f9fa;
  padding: 16px;
  border-radius: 8px;
  border-left: 4px solid #6c757d;
}

.original-msg-preview strong {
  color: #6c757d;
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
}

.original-msg-preview p {
  margin: 0;
  font-style: italic;
  color: #495057;
  line-height: 1.6;
}

.admin-reply-content {
  background-color: #e7f3ff;
  padding: 20px;
  border-radius: 8px;
  border-left: 4px solid #344966;
}

.admin-reply-content strong {
  color: #344966;
  display: block;
  margin-bottom: 12px;
  font-size: 14px;
}

.reply-text {
  color: #212529;
  line-height: 1.8;
  white-space: pre-wrap;
  background: white;
  padding: 16px;
  border-radius: 8px;
}

/* Button Styles for Message Cards */
.view-reply-btn {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
  color: white;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.view-reply-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}
