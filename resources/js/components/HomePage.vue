<template>
  <div class="home-page">
    <!-- Navbar -->
    <header class="navbar">
      <h1>CarTrackingApp</h1>
      <nav>
        <ul>
          <li><a href="#">Dashboard</a></li>
          <li><a href="#">Vehicles</a></li>
          <li><a href="#">Reports</a></li>
          <li><a href="#">Settings</a></li>
        </ul>
      </nav>
    </header>

    <!-- Hero / Welcome Section -->
    <section class="hero">
      <h2>Welcome to Car Tracking App 🚗</h2>
      <p>Monitor, manage and analyze your vehicles in real-time.</p>
      <button @click="goToDashboard">Get Started</button>
    </section>

    <!-- Dashboard Preview Section -->
    <section class="features">
      <div class="card">
        <h3>📍 Real-time Tracking</h3>
        <p>See where your vehicles are at any moment.</p>
      </div>
      <div class="card">
        <h3>📊 Reports</h3>
        <p>Generate performance and usage reports.</p>
      </div>
      <div class="card">
        <h3>⚙️ Maintenance</h3>
        <p>Track and schedule vehicle maintenance.</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

function goToDashboard() {
  router.push("/dashboard");
}
</script>

<style scoped>
.home-page {
  font-family: Arial, sans-serif;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #1a73e8;
  padding: 1rem 2rem;
  color: #fff;
}

.navbar ul {
  display: flex;
  list-style: none;
  gap: 1rem;
}

.navbar a {
  color: white;
  text-decoration: none;
}

.hero {
  text-align: center;
  padding: 4rem 2rem;
  background: #f4f7fc;
}

.hero h2 {
  font-size: 2rem;
}

.hero button {
  margin-top: 1rem;
  padding: 0.7rem 1.5rem;
  background: #1a73e8;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.features {
  display: flex;
  justify-content: center;
  gap: 2rem;
  padding: 3rem 2rem;
}

.card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  width: 250px;
  text-align: center;
}
</style>
