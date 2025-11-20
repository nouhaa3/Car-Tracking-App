<template>
  <div :class="['sidebar', { expanded: isExpanded, dark: theme.isDark }]">
    <!-- Logo + App name -->
    <div class="sidebar-header" @click="toggleSidebar">
      <i class="bi bi-app-indicator logo"></i>
    </div>

    <!-- Main Menu -->
    <ul class="sidebar-menu">
      <li v-for="(item, index) in mainMenu" :key="index">
        <router-link :to="item.to" class="menu-link">
          <i :class="item.icon"></i>
          <span v-if="isExpanded">{{ item.label }}</span>
        </router-link>
      </li>
    </ul>

    <hr />

    <!-- System/Personal -->
    <ul class="sidebar-menu">
      <li v-for="(item, index) in personalMenu" :key="index">
        <router-link :to="item.to" class="menu-link">
          <i :class="item.icon"></i>
          <span v-if="isExpanded">{{ item.label }}</span>
        </router-link>
      </li>
    </ul>

    <!-- Footer with notifications + settings + dark mode + logout -->
    <div class="sidebar-footer">
      <router-link to="/settings" class="menu-link">
        <i class="bi bi-gear"></i>
        <span v-if="isExpanded">{{ t('nav.settings') }}</span>
      </router-link>

      <!-- Dark mode toggle -->
      <button @click="toggleTheme" class="menu-link theme-toggle">
        <i v-if="theme.isDark" class="bi bi-sun"></i>
        <i v-else class="bi bi-moon-stars"></i>
        <span v-if="isExpanded">{{ theme.isDark ? t('nav.lightMode') : t('nav.darkMode') }}</span>
      </button>

      <!-- Logout button -->
      <button @click="logout" class="menu-link logout-link">
        <i class="bi bi-box-arrow-right"></i>
        <span v-if="isExpanded">{{ t('nav.logout') }}</span>
      </button>

    </div>
  </div>
</template>

<script>
import { inject, onMounted, computed } from "vue";
import { useI18n } from 'vue-i18n';

export default {
  name: "Sidebar",
  setup() {
    const { t } = useI18n();
    const theme = inject("theme");

    const toggleTheme = () => {
      theme.isDark = !theme.isDark;
      localStorage.setItem("theme", theme.isDark ? "dark" : "light");

      if (theme.isDark) document.body.classList.add("dark");
      else document.body.classList.remove("dark");
    };

    onMounted(() => {
      if (theme.isDark) document.body.classList.add("dark");
      else document.body.classList.remove("dark");
    });

    const mainMenu = computed(() => [
      { label: t('nav.users'), to: "/users", icon: "bi bi-people" },
      { label: t('nav.reports'), to: "/rapports", icon: "bi bi-file-earmark-bar-graph" },
      { label: t('nav.messages'), to: "/chats", icon: "bi bi-chat-dots" },
    ]);

    const personalMenu = computed(() => [
      { label: t('nav.notifications'), to: "/notifications", icon: "bi bi-bell" },
      { label: t('nav.help'), to: "/help", icon: "bi bi-question-circle" },
      { label: t('nav.trash'), to: "/corbeille", icon: "bi bi-trash" },
    ]);

    return { 
      t, 
      theme, 
      toggleTheme, 
      mainMenu, 
      personalMenu,
    };
  },
  data() {
    return {
      isExpanded: false,
      user: null,
    };
  },
  methods: {
    toggleSidebar() {
      this.isExpanded = !this.isExpanded;
    },
    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      this.$router.push("/login");
    },
  },
  mounted() {
    // restore user (saved at login)
    const savedUser = localStorage.getItem("user");
    if (savedUser) this.user = JSON.parse(savedUser);
  },
};
</script>


