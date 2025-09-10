import { defineStore } from "pinia";
import axios from "axios";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
    loading: false,
  }),
  actions: {
    async fetchUser() {
      this.loading = true;
      try {
        const response = await axios.get("/api/me"); //  use your existing endpoint
        this.user = response.data;
        console.log("Fetched user:", this.user);
      } catch (error) {
        console.error("Failed to fetch user:", error.response?.data || error.message);
        this.user = null;
      } finally {
        this.loading = false;
      }
    },
  },
});
