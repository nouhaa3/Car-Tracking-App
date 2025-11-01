<template>
    <div :class="['home-page', { dark: theme.isDark }]">
        <div class="layout">
            <Sidebar :class="{ expanded: isExpanded }" />
            
            <div class="main-content">
                <!-- Profile Float -->
                <router-link to="/profile" class="profile-float" v-if="user">
                    <img :src="user.avatar || '/images/avatar.png'" :alt="t('common.userAvatar')" class="avatar" />
                </router-link>

                <!-- Bootstrap Icons -->
                <link
                rel="stylesheet"
                href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
                />

                <!-- Navbar -->
                <nav class="navbar">
                    <router-link 
                        v-for="(item, index) in menuItems" 
                        :key="index"
                        :to="item.to" 
                        class="nav-link"
                        :class="{'active': $route.path === item.to}"
                    >
                        {{ item.label }}
                    </router-link>
                </nav>

                <div class="dashboard-container">
                    <header class="dashboard-header">
                        <h4>{{ t('agentDashboard.title') }}</h4>
                    </header>
                    
                    <div class="empty-state card">
                        <i class="bi bi-speedometer2"></i>
                        <h3>{{ t('agentDashboard.title') }}</h3>
                        <p>{{ t('agentDashboard.developmentMessage') }}</p>
                    </div>
                </div>

                <button @click="logout" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { inject, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import Sidebar from './sidebar.vue';
import axios from 'axios';

export default {
  name: "AgentDashboard",
  components: { Sidebar },
  setup() {
    const theme = inject('theme');
    const router = useRouter();
    const { t } = useI18n();
    const isExpanded = ref(false);
    const user = ref(null);

    const menuItems = [
      { label: t('nav.home'), to: "/" },
      { label: t('nav.dashboard'), to: "/agentdashboard" },
      { label: t('nav.catalog'), to: "/voitures/cataloguevoitures" },
      { label: t('nav.interventions'), to: "/interventions/catalogue" },
      { label: t('nav.alerts'), to: "/alertes" },
    ];

    const fetchUser = async () => {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://127.0.0.1:8000/api/me', {
          headers: { Authorization: `Bearer ${token}` }
        });
        user.value = response.data;
      } catch (error) {
        console.error(t('errors.fetchUserError'), error);
      }
    };

    const logout = () => {
      localStorage.removeItem('token');
      router.push('/login');
    };

    onMounted(() => {
      fetchUser();
    });

    return {
      theme,
      t,
      isExpanded,
      user,
      menuItems,
      logout,
    };
  },
};
</script>