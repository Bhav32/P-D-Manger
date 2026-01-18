// API Configuration
export const API_CONFIG = {
  baseUrl: 'http://127.0.0.1:8003/api',
  endpoints: {
    auth: {
      login: '/auth/login',
      logout: '/auth/logout',
    },
    products: {
      list: '/products',
      view: '/products/:id',
      create: '/products',
      edit: '/products/:id',
      delete: '/products/:id'
    },
    discounts: {
      list: '/discounts',
      view: '/discounts/:id',
      create: '/discounts',
      edit: '/discounts/:id',
      delete: '/discounts/:id',
      active: '/discounts/active',
      forProduct: '/products/:productId/discounts'
    }
  }
};

// Helper function to build full URLs
export function getApiUrl(endpoint: string): string {
  return `${API_CONFIG.baseUrl}${endpoint}`;
}
