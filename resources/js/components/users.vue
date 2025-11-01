<template>
  <div :class="['home-page', { dark: theme.isDark }]">
    <div class="layout">
      <Sidebar :class="{ expanded: isExpanded }" />

      <div class="main-content">
        <router-link to="/profile" class="profile-float" v-if="currentUser">
          <img :src="currentUser.avatar || '/images/avatar.png'" alt="User" class="avatar" />
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
        <div class="users-page-header">
          <div class="header-left">
            <h1>{{ t('users.title') }}</h1>
            <p>{{ t('users.subtitle') }}</p>
          </div>
          <button class="add-user-btn" @click="openAddModal">
            {{ t('users.addUser') }}
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('users.loading') }}</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-container">
          <i class="bi bi-exclamation-triangle"></i>
          <p>{{ error }}</p>
          <button @click="fetchUsers" class="retry-btn">{{ t('common.retry') }}</button>
        </div>

        <!-- Users Table -->
        <div v-else-if="users.length > 0" class="users-table-container card">
          <div class="table-header">
            <div class="search-wrapper">
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="t('users.searchPlaceholder')"
                class="search-input"
              />
              <button 
                v-if="searchQuery" 
                @click="searchQuery = ''"
                class="clear-search-btn"
              >
                <i class="bi bi-x"></i>
              </button>
            </div>
            <div class="table-stats">
              <span class="stat-badge">
                {{ filteredUsers.length }} 
                {{ filteredUsers.length > 1 ? t('users.users') : t('users.user') }}
              </span>
            </div>
          </div>

          <div class="table-wrapper">
            <table class="users-table">
              <thead>
                <tr>
                  <th>{{ t('users.id') }}</th>
                  <th>{{ t('users.user') }}</th>
                  <th>{{ t('users.email') }}</th>
                  <th>{{ t('users.role') }}</th>
                  <th>{{ t('users.registrationDate') }}</th>
                  <th>{{ t('users.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                  <td>
                    <span class="user-id">#{{ user.id }}</span>
                  </td>
                  <td>
                    <div class="user-cell">
                      <div class="user-avatar-small">
                        <template v-if="user.avatar">
                          <img :src="user.avatar" :alt="t('users.avatar')" />
                        </template>
                        <template v-else>
                          <div class="initials-small">
                            {{ getInitials(user) }}
                          </div>
                        </template>
                      </div>
                      <div class="user-details">
                        <span class="user-name-cell">{{ user.nom }} {{ user.prenom }}</span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="user-email">{{ user.email }}</span>
                  </td>
                  <td>
                    <span class="role-badge" :class="getRoleClass(user.role)">
                      {{ user.role?.nomRole || t('common.na') }}
                    </span>
                  </td>
                  <td>
                    <span class="user-date">{{ formatDate(user.created_at) }}</span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button 
                        class="action-btn edit-btn" 
                        @click="openEditModal(user)"
                        :title="t('common.edit')"
                      >
                        {{ t('common.edit') }}
                      </button>
                      <button 
                        class="action-btn delete-btn" 
                        @click="confirmDelete(user)"
                        :title="t('common.delete')"
                      >
                        {{ t('common.delete') }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="empty-state card">
          <h3>{{ t('users.noUsers') }}</h3>
          <p>{{ t('users.addFirstUser') }}</p>
          <button @click="openAddModal" class="add-user-btn">
            {{ t('users.addUser') }}
          </button>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
          <div class="modal-container">
            <div class="modal-header">
              <h3>{{ isEditing ? t('users.editUser') : t('users.addUser') }}</h3>
              <button class="close-btn" @click="closeModal">
                <i class="bi bi-x"></i>
              </button>
            </div>

            <form @submit.prevent="saveUser" class="modal-form">
              <div class="form-grid">
                <div class="form-group">
                  <label>{{ t('users.firstName') }} *</label>
                  <input 
                    v-model="form.prenom" 
                    type="text"
                    class="form-input" 
                    :placeholder="t('users.firstNamePlaceholder')"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>{{ t('users.lastName') }} *</label>
                  <input 
                    v-model="form.nom" 
                    type="text"
                    class="form-input" 
                    :placeholder="t('users.lastNamePlaceholder')"
                    required
                  />
                </div>

                <div class="form-group full-width">
                  <label>{{ t('users.email') }} *</label>
                  <input 
                    v-model="form.email" 
                    type="email"
                    class="form-input" 
                    :placeholder="t('users.emailPlaceholder')"
                    required
                  />
                </div>

                <div class="form-group full-width">
                  <label>{{ t('users.role') }} *</label>
                  <select v-model="form.role_id" class="form-input" required>
                    <option value="">{{ t('users.selectRole') }}</option>
                    <option v-for="role in roles" :key="role.idRole" :value="role.idRole">
                      {{ role.nomRole }}
                    </option>
                  </select>
                </div>

                <div class="form-group full-width">
                  <label>{{ isEditing ? t('users.newPassword') : t('users.password') }} *</label>
                  <input 
                    v-model="form.password" 
                    type="password"
                    class="form-input" 
                    :placeholder="t('users.passwordPlaceholder')"
                    :required="!isEditing"
                  />
                  <small v-if="isEditing" class="form-help">{{ t('users.passwordHelp') }}</small>
                </div>

                <div class="form-group full-width">
                  <label>{{ isEditing ? t('users.confirmNewPassword') : t('users.confirmPassword') }} *</label>
                  <input 
                    v-model="form.password_confirmation" 
                    type="password"
                    class="form-input" 
                    :placeholder="t('users.passwordPlaceholder')"
                    :required="!isEditing && form.password"
                  />
                </div>
              </div>

              <div class="modal-actions">
                <button type="submit" class="btn-primary" :disabled="saving">
                  {{ saving ? t('common.saving') : (isEditing ? t('common.update') : t('common.create')) }}
                </button>
                <button type="button" class="btn-secondary" @click="closeModal" :disabled="saving">
                  {{ t('common.cancel') }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
          <div class="modal-container modal-small">
            <div class="modal-header">
              <h3>{{ t('users.confirmDelete') }}</h3>
              <button class="close-btn" @click="showDeleteModal = false">
                <i class="bi bi-x"></i>
              </button>
            </div>

            <div class="modal-body">
              <p>{{ t('users.deleteConfirmation', { name: userToDelete?.nom, firstName: userToDelete?.prenom }) }}</p>
              <p class="warning-text">{{ t('users.irreversibleAction') }}</p>
            </div>

            <div class="modal-actions">
              <button class="btn-danger" @click="deleteUser" :disabled="deleting">
                {{ deleting ? t('common.deleting') : t('common.delete') }}
              </button>
              <button class="btn-secondary" @click="showDeleteModal = false" :disabled="deleting">
                {{ t('common.cancel') }}
              </button>
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
  components: {
    Sidebar,
  },
  setup() {
    const { t } = useI18n();
    const theme = inject('theme');
    const router = useRouter();
    const isExpanded = ref(false);

    const currentUser = ref(null);
    const users = ref([]);
    const roles = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const searchQuery = ref('');
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const userToDelete = ref(null);

    const form = ref({
      id: null,
      prenom: '',
      nom: '',
      email: '',
      role_id: '',
      password: '',
      password_confirmation: ''
    });

    const menuItems = computed(() => [
      { label: t('nav.home'), to: "/" },
      { label: t('nav.dashboard'), to: "/admindashboard" },
      { label: t('nav.catalog'), to: "/voitures/cataloguevoitures" },
      { label: t('nav.interventions'), to: "/interventions/catalogue" },
      { label: t('nav.alerts'), to: "/alertes" },
    ]);

    const filteredUsers = computed(() => {
      if (!searchQuery.value) return users.value;
      
      const query = searchQuery.value.toLowerCase();
      return users.value.filter(user => {
        const fullName = `${user.nom} ${user.prenom}`.toLowerCase();
        const email = user.email?.toLowerCase() || '';
        return fullName.includes(query) || email.includes(query);
      });
    });

    const fetchUsers = async () => {
      loading.value = true;
      error.value = null;
      console.log('Users: Fetching users...');
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/users', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        users.value = response.data;
        console.log('Users: Fetched', users.value.length, 'users');
      } catch (err) {
        console.error('Erreur lors du chargement des utilisateurs:', err);
        error.value = t('users.loadError');
      } finally {
        loading.value = false;
      }
    };

    const fetchRoles = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/roles', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        roles.value = response.data;
      } catch (err) {
        console.error('Erreur lors du chargement des rôles:', err);
      }
    };

    const fetchCurrentUser = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        currentUser.value = response.data;
      } catch (err) {
        console.error('Erreur lors du chargement de l\'utilisateur:', err);
      }
    };

    const openAddModal = () => {
      isEditing.value = false;
      form.value = {
        id: null,
        prenom: '',
        nom: '',
        email: '',
        role_id: '',
        password: '',
        password_confirmation: ''
      };
      showModal.value = true;
    };

    const openEditModal = (user) => {
      isEditing.value = true;
      form.value = {
        id: user.id,
        prenom: user.prenom,
        nom: user.nom,
        email: user.email,
        role_id: user.role?.idRole || user.role_id,
        password: '',
        password_confirmation: ''
      };
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      form.value = {
        id: null,
        prenom: '',
        nom: '',
        email: '',
        role_id: '',
        password: '',
        password_confirmation: ''
      };
    };

    const saveUser = async () => {
      if (form.value.password && form.value.password !== form.value.password_confirmation) {
        await alerts.alertWarning(t('common.inputError'), t('users.passwordMismatch'));
        return;
      }

      saving.value = true;
      try {
        const token = localStorage.getItem('token');
        const payload = {
          prenom: form.value.prenom,
          nom: form.value.nom,
          email: form.value.email,
          role_id: form.value.role_id,
        };

        // Only include password if provided
        if (form.value.password) {
          payload.password = form.value.password;
          payload.password_confirmation = form.value.password_confirmation;
        }

        if (isEditing.value) {
          // Update user
          await axios.put(
            `http://127.0.0.1:8000/api/users/${form.value.id}`,
            payload,
            {
              headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json',
              },
            }
          );
          alerts.success(t('common.updated'), t('users.updateSuccess'));
        } else {
          // Create user
          await axios.post(
            'http://127.0.0.1:8000/api/users',
            payload,
            {
              headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json',
              },
            }
          );
          await alerts.alertSuccess(t('common.created'), t('users.createSuccess'));
        }

        closeModal();
        fetchUsers();
      } catch (err) {
        console.error('Erreur lors de la sauvegarde:', err);
        await alerts.alertError(t('common.error'), err.response?.data?.message || t('users.saveError'));
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (user) => {
      userToDelete.value = user;
      showDeleteModal.value = true;
    };

    const deleteUser = async () => {
      deleting.value = true;
      try {
        const token = localStorage.getItem('token');
        await axios.delete(
          `http://127.0.0.1:8000/api/users/${userToDelete.value.id}`,
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );
        await alerts.alertSuccess(t('common.deleted'), t('users.deleteSuccess'));
        showDeleteModal.value = false;
        userToDelete.value = null;
        fetchUsers();
      } catch (err) {
        console.error('Erreur lors de la suppression:', err);
        await alerts.alertError(t('common.error'), t('users.deleteError'));
      } finally {
        deleting.value = false;
      }
    };

    const getInitials = (user) => {
      const firstInitial = user.prenom?.charAt(0).toUpperCase() || '';
      const lastInitial = user.nom?.charAt(0).toUpperCase() || '';
      return firstInitial + lastInitial;
    };

    const getRoleClass = (role) => {
      if (!role) return 'role-default';
      const roleName = role.nomRole?.toLowerCase() || '';
      if (roleName === 'admin') return 'role-admin';
      if (roleName === 'agent') return 'role-agent';
      if (roleName === 'technicien') return 'role-technicien';
      return 'role-default';
    };

    const formatDate = (date) => {
      if (!date) return t('common.na');
      const d = new Date(date);
      return d.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    };

    const logout = () => {
      localStorage.removeItem('token');
      router.push('/login');
    };

    onMounted(() => {
      fetchUsers();
      fetchRoles();
      fetchCurrentUser();
    });

    return {
      t,
      theme,
      isExpanded,
      currentUser,
      users,
      roles,
      loading,
      error,
      searchQuery,
      showModal,
      showDeleteModal,
      isEditing,
      saving,
      deleting,
      userToDelete,
      form,
      menuItems,
      filteredUsers,
      fetchUsers,
      openAddModal,
      openEditModal,
      closeModal,
      saveUser,
      confirmDelete,
      deleteUser,
      getInitials,
      getRoleClass,
      formatDate,
      logout,
    };
  },
};
</script>