<template>
  <div :class="['sidebar', { expanded: isExpanded, dark: isDark }]">
    <!-- Logo + App name -->
    <div class="sidebar-header" @click="toggleSidebar">
      <i class="bi bi-app-indicator logo"></i>
    </div>

    <!-- Search (optional, shown when expanded) -->
    <div v-if="isExpanded" class="sidebar-search">
      <i class="bi bi-search"></i>
      <input type="text" placeholder="Rechercher ..." />
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

    <!-- Footer with settings + dark mode + profile -->
    <div class="sidebar-footer">
      <router-link to="/settings" class="menu-link">
        <i class="bi bi-gear"></i>
        <span v-if="isExpanded">Paramètres</span>
      </router-link>

      <!-- Dark mode toggle -->
      <button @click="toggleTheme" class="menu-link theme-toggle">
        <i v-if="isDark" class="bi bi-sun"></i>
        <i v-else class="bi bi-moon-stars"></i>
        <span v-if="isExpanded">{{ isDark ? "Light Mode" : "Dark Mode" }}</span>
      </button>

    </div>
  </div>
</template>

<script>
export default {
  name: "Sidebar",
  data() {
    return {
      isExpanded: false,
      isDark: false,
      user: null,
      mainMenu: [
        { label: "Utilisateurs", to: "/users", icon: "bi bi-people" },
        { label: "Messages", to: "/chats", icon: "bi bi-chat-dots" },
        { label: "Rapports", to: "/rapports", icon: "bi bi-bar-chart" },
      ],
      personalMenu: [
        { label: "Notifications", to: "/notifications", icon: "bi bi-bell" },
        { label: "Historique", to: "/historique", icon: "bi bi-clock-history" },
        { label: "Aide", to: "/help", icon: "bi bi-question-circle" },
      ],
    };
  },
  methods: {
    toggleSidebar() {
      this.isExpanded = !this.isExpanded;
    },
    toggleTheme() {
      this.isDark = !this.isDark;
      localStorage.setItem("theme", this.isDark ? "dark" : "light");
    },
  },
  mounted() {
    // restore theme
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) this.isDark = savedTheme === "dark";

    // restore user (saved at login)
    const savedUser = localStorage.getItem("user");
    if (savedUser) {
      this.user = JSON.parse(savedUser);
    }
  },
};
</script>
