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

        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ t('profile.loading') }}</p>
        </div>

        <!-- Profile Content -->
        <div v-else-if="user" class="profile-wrapper">
          <!-- Page Header -->
          <div class="profile-page-header">
            <h1>{{ t('profile.title') }}</h1>
            <p>{{ t('profile.subtitle') }}</p>
          </div>

          <!-- Profile Container -->
          <div class="profile-container">
            <!-- Left: Avatar Card -->
            <div class="profile-left">
              <div class="avatar-card card">
                <div class="avatar-wrapper">
                  <img 
                    :src="user.avatar || '/images/avatar.png'" 
                    :alt="t('common.userAvatar')" 
                    class="profile-avatar" 
                  />
                  <input 
                    type="file" 
                    ref="avatarInput" 
                    @change="handleAvatarChange" 
                    accept="image/*" 
                    style="display: none"
                  />
                  <button v-if="!editMode" class="change-avatar-btn" @click="triggerFileInput" :disabled="uploadingAvatar">
                    {{ uploadingAvatar ? t('profile.uploading') : t('profile.changePhoto') }}
                  </button>
                </div>
                <div class="user-basic-info">
                  <h2>{{ user.nom }} {{ user.prenom }}</h2>
                  <span class="user-role-badge" :class="getRoleClass(user.role?.nomRole)">
                    {{ user.role?.nomRole || t('profile.roles.user') }}
                  </span>
                </div>
                <div class="profile-stats">
                  <div class="stat-item">
                    <span class="stat-label">{{ t('profile.memberSince') }}</span>
                    <span class="stat-value">{{ formatDate(user.created_at) }}</span>
                  </div>
                  <div class="stat-item" v-if="userVehiclesCount !== null">
                    <span class="stat-label">{{ t('profile.vehicles') }}</span>
                    <span class="stat-value">{{ userVehiclesCount }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right: Details Card -->
            <div class="profile-right">
              <!-- View Mode -->
              <div v-if="!editMode" class="details-card card">
                <div class="card-header-section">
                  <h3>{{ t('profile.personalInfo') }}</h3>
                  <button class="icon-btn edit-btn" @click="enableEditMode">
                    {{ t('common.edit') }}
                  </button>
                </div>

                <div class="info-grid">
                  <div class="info-row">
                    <span class="info-label">{{ t('auth.firstName') }}</span>
                    <span class="info-value">{{ user.prenom }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('auth.lastName') }}</span>
                    <span class="info-value">{{ user.nom }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('auth.email') }}</span>
                    <span class="info-value">{{ user.email }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('profile.role') }}</span>
                    <span class="info-value">
                      <span class="badge-role" :class="getRoleClass(user.role?.nomRole)">
                        {{ user.role?.nomRole || t('profile.roles.user') }}
                      </span>
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">{{ t('profile.userId') }}</span>
                    <span class="info-value">{{ user.id }}</span>
                  </div>
                </div>
              </div>

              <!-- Edit Mode -->
              <div v-else class="edit-card card">
                <div class="card-header-section">
                  <h3>{{ t('profile.editProfile') }}</h3>
                </div>

                <form @submit.prevent="updateProfile" class="edit-form">
                  <div class="form-grid">
                    <div class="form-group">
                      <label>{{ t('auth.firstName') }} *</label>
                      <input 
                        v-model="form.prenom" 
                        class="form-input" 
                        :placeholder="t('auth.firstNamePlaceholder')"
                        required
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('auth.lastName') }} *</label>
                      <input 
                        v-model="form.nom" 
                        class="form-input" 
                        :placeholder="t('auth.lastNamePlaceholder')"
                        required
                      />
                    </div>

                    <div class="form-group full-width">
                      <label>{{ t('auth.email') }} *</label>
                      <input 
                        type="email"
                        v-model="form.email" 
                        class="form-input" 
                        :placeholder="t('auth.emailPlaceholder')"
                        required
                      />
                    </div>
                  </div>

                  <div class="form-divider"></div>

                  <h4 class="form-section-title">{{ t('profile.changePassword') }}</h4>
                  <div class="form-grid">
                    <div class="form-group full-width">
                      <label>{{ t('profile.currentPassword') }}</label>
                      <input 
                        type="password"
                        v-model="form.current_password" 
                        class="form-input" 
                        :placeholder="t('auth.passwordPlaceholder')"
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('profile.newPassword') }}</label>
                      <input 
                        type="password"
                        v-model="form.new_password" 
                        class="form-input" 
                        :placeholder="t('auth.passwordPlaceholder')"
                      />
                    </div>

                    <div class="form-group">
                      <label>{{ t('profile.confirmPassword') }}</label>
                      <input 
                        type="password"
                        v-model="form.password_confirmation" 
                        class="form-input" 
                        :placeholder="t('auth.passwordPlaceholder')"
                      />
                    </div>
                  </div>

                  <div class="form-actions">
                    <button type="submit" class="btn-primary" :disabled="saving">
                      {{ saving ? t('common.saving') : t('profile.saveChanges') }}
                    </button>
                    <button type="button" class="btn-secondary" @click="cancelEdit" :disabled="saving">
                      {{ t('common.cancel') }}
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else class="error-state card">
          <h3>{{ t('errors.loadError') }}</h3>
          <p>{{ t('profile.loadError') }}</p>
          <button @click="fetchUserData" class="btn-primary">{{ t('common.retry') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { inject, ref, onMounted, reactive } from 'vue';
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
    const theme = inject('theme');
    const router = useRouter();
    const { t } = useI18n();
    const isExpanded = ref(false);
    
    const user = ref(null);
    const loading = ref(true);
    const editMode = ref(false);
    const saving = ref(false);
    const userVehiclesCount = ref(null);
    const uploadingAvatar = ref(false);
    const avatarInput = ref(null);

    const form = reactive({
      prenom: '',
      nom: '',
      email: '',
      current_password: '',
      new_password: '',
      password_confirmation: ''
    });

    const menuItems = [
      { label: t('nav.home'), to: "/" },
      { label: t('nav.dashboard'), to: "/admindashboard" },
      { label: t('nav.catalog'), to: "/voitures/cataloguevoitures" },
      { label: t('nav.interventions'), to: "/interventions/catalogue" },
      { label: t('nav.alerts'), to: "/alertes" },
    ];

    const fetchUserData = async () => {
      loading.value = true;
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        user.value = response.data;
        
        // Initialize form with user data
        form.prenom = user.value.prenom;
        form.nom = user.value.nom;
        form.email = user.value.email;

        // Fetch user's vehicles count if available
        fetchUserVehicles();
      } catch (error) {
        console.error(t('errors.fetchUserError'), error);
      } finally {
        loading.value = false;
      }
    };

    const fetchUserVehicles = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/voitures', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        // Count vehicles belonging to this user
        const vehicles = response.data.filter(v => v.user_id === user.value.id);
        userVehiclesCount.value = vehicles.length;
      } catch (error) {
        console.error(t('errors.loadVehiclesError'), error);
      }
    };

    const enableEditMode = () => {
      editMode.value = true;
      // Reset form with current user data
      form.prenom = user.value.prenom;
      form.nom = user.value.nom;
      form.email = user.value.email;
      form.current_password = '';
      form.new_password = '';
      form.password_confirmation = '';
    };

    const cancelEdit = () => {
      editMode.value = false;
      form.current_password = '';
      form.new_password = '';
      form.password_confirmation = '';
    };

    const updateProfile = async () => {
      saving.value = true;
      try {
        const token = localStorage.getItem('token');
        const payload = {
          prenom: form.prenom,
          nom: form.nom,
          email: form.email,
        };

        // Only include password fields if changing password
        if (form.current_password && form.new_password) {
          if (form.new_password !== form.password_confirmation) {
            await alerts.alertWarning(t('common.warning'), t('profile.errors.passwordMismatch'));
            saving.value = false;
            return;
          }
          payload.current_password = form.current_password;
          payload.password = form.new_password;
          payload.password_confirmation = form.password_confirmation;
        }

        const response = await axios.put(
          `http://127.0.0.1:8000/api/users/${user.value.id}`,
          payload,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'application/json',
            },
          }
        );

        user.value = response.data;
        editMode.value = false;
        await alerts.alertSuccess(t('common.success'), t('profile.updateSuccess'));
        
        // Reset password fields
        form.current_password = '';
        form.new_password = '';
        form.password_confirmation = '';
      } catch (error) {
        console.error(t('errors.updateProfileError'), error);
        await alerts.alertError(t('common.error'), error.response?.data?.message || t('errors.updateProfileError'));
      } finally {
        saving.value = false;
      }
    };

    const triggerFileInput = () => {
      avatarInput.value.click();
    };

    const handleAvatarChange = async (event) => {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file type
      if (!file.type.startsWith('image/')) {
        await alerts.alertWarning(t('common.warning'), t('profile.errors.invalidFormat'));
        return;
      }

      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        await alerts.alertWarning(t('common.warning'), t('profile.errors.fileTooLarge'));
        return;
      }

      uploadingAvatar.value = true;
      try {
        const token = localStorage.getItem('token');
        const formData = new FormData();
        formData.append('avatar', file);

        const response = await axios.post(
          `http://127.0.0.1:8000/api/users/${user.value.id}/avatar`,
          formData,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              'Content-Type': 'multipart/form-data',
            },
          }
        );

        // Update user avatar
        user.value.avatar = response.data.avatar;
        await alerts.alertSuccess(t('common.success'), t('profile.avatarUpdateSuccess'));
      } catch (error) {
        console.error(t('errors.uploadAvatarError'), error);
        await alerts.alertError(t('common.error'), error.response?.data?.message || t('errors.uploadAvatarError'));
      } finally {
        uploadingAvatar.value = false;
        // Reset input
        if (avatarInput.value) {
          avatarInput.value.value = '';
        }
      }
    };

    const getRoleClass = (role) => {
      if (!role) return 'role-default';
      const roleLower = role.toLowerCase();
      if (roleLower === 'admin') return 'role-admin';
      if (roleLower === 'agent') return 'role-agent';
      if (roleLower === 'technicien') return 'role-technicien';
      return 'role-default';
    };

    const formatDate = (date) => {
      if (!date) return t('common.notAvailable');
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
      fetchUserData();
    });

    return {
      theme,
      t,
      isExpanded,
      user,
      loading,
      editMode,
      saving,
      form,
      menuItems,
      userVehiclesCount,
      uploadingAvatar,
      avatarInput,
      fetchUserData,
      enableEditMode,
      cancelEdit,
      updateProfile,
      triggerFileInput,
      handleAvatarChange,
      getRoleClass,
      formatDate,
      logout,
    };
  },
};
</script>