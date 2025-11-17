/**
 * Navigation utilities for role-based routing
 */

/**
 * Get the appropriate dashboard route based on user role
 * @returns {string} Dashboard route path
 */
export function getDashboardRoute() {
  const userStr = localStorage.getItem('user');
  
  if (!userStr) {
    return '/login'; // Not logged in
  }
  
  try {
    const user = JSON.parse(userStr);
    const role = user.role?.toLowerCase();
    
    switch (role) {
      case 'admin':
        return '/admindashboard';
      case 'agent':
        return '/agentdashboard';
      case 'technicien':
      case 'technician':
        return '/techniciendashboard';
      default:
        return '/admindashboard'; // Default fallback
    }
  } catch (error) {
    console.error('Error parsing user data:', error);
    return '/login';
  }
}

/**
 * Get user role from localStorage
 * @returns {string|null} User role or null if not found
 */
export function getUserRole() {
  const userStr = localStorage.getItem('user');
  
  if (!userStr) {
    return null;
  }
  
  try {
    const user = JSON.parse(userStr);
    return user.role?.toLowerCase();
  } catch (error) {
    console.error('Error parsing user data:', error);
    return null;
  }
}

/**
 * Check if user is logged in
 * @returns {boolean}
 */
export function isAuthenticated() {
  return !!localStorage.getItem('token');
}

/**
 * Navigate to the appropriate dashboard based on user role
 * @param {Object} router - Vue Router instance
 */
export function navigateToAccueil(router) {
  const dashboardRoute = getDashboardRoute();
  router.push(dashboardRoute);
}
