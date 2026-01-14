// API Configuration
export const API_CONFIG = {
  baseUrl: 'http://127.0.0.1:8000/api',
  endpoints: {
    auth: {
      login: '/auth/login',
      logout: '/auth/logout',
    },
    products: '/products',
    discounts: '/discounts'
  }
};

// Helper function to build full URLs
export function getApiUrl(endpoint: string): string {
  return `${API_CONFIG.baseUrl}${endpoint}`;
}
