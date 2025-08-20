<template>
  <div>
    <h1>Login Page</h1>
    <form>
      <label>Email:</label>
      <input type="email" placeholder="Enter your email" />
      <br />
      <label>Password:</label>
      <input type="password" placeholder="Enter your password" />
      <br />
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script>
export default {
  name: "LoginPage",
};
</script>

<style scoped>
input {
  margin-bottom: 8px;
}
button {
  margin-top: 8px;
}
</style>
