import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

interface User {
  id: string;
  name: string;
  email: string;
  role: 'admin' | 'editor';
}

interface LoginCredentials {
  email: string;
  password: string;
  remember?: boolean;
}

interface AuthResponse {
  user: User;
  token: string;
}

interface AuthValidationResponse {
  user: User;
}

// API Configuration - In production, these should come from environment variables
const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL || '/api').replace(/\/+$/, '');
const DEMO_MODE = import.meta.env.VITE_ADMIN_DEMO_MODE === 'true';

// Environment-based credentials (for demo/development only)
// In production, these should NOT be used - use proper API authentication
// NOTE: Password is stored as SHA-256 hash for security in demo mode
const ADMIN_EMAIL = import.meta.env.VITE_ADMIN_EMAIL || '';
const ADMIN_PASSWORD_HASH = import.meta.env.VITE_ADMIN_PASSWORD_HASH || '';
const LOCAL_DEMO_EMAIL = 'admin@banuacloud.local';
const LOCAL_DEMO_PASSWORD = 'admin12345';
const HAS_CONFIGURED_DEMO_CREDENTIALS = Boolean(ADMIN_EMAIL && ADMIN_PASSWORD_HASH);
const USES_API_AUTH = Boolean(API_BASE_URL) && !DEMO_MODE;
const USES_LOCAL_FALLBACK_DEMO = import.meta.env.DEV && !USES_API_AUTH && !HAS_CONFIGURED_DEMO_CREDENTIALS;

function buildAuthHeaders(authToken?: string): HeadersInit {
  return {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}),
  };
}

// Simple hash function using Web Crypto API (SHA-256)
// Returns hex-encoded hash
async function hashPassword(password: string): Promise<string> {
  const encoder = new TextEncoder();
  const data = encoder.encode(password);
  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  const hasRestoredAuth = ref(false);

  const isAuthenticated = computed(() => !!token.value);
  const isAdmin = computed(() => user.value?.role === 'admin');
  const loginHint = computed(() => {
    if (USES_API_AUTH) {
      return null;
    }

    if (HAS_CONFIGURED_DEMO_CREDENTIALS) {
      return `Demo login configured for ${ADMIN_EMAIL}.`;
    }

    if (USES_LOCAL_FALLBACK_DEMO) {
      return `Local development login: ${LOCAL_DEMO_EMAIL} / ${LOCAL_DEMO_PASSWORD}`;
    }

    return 'Set VITE_API_BASE_URL or configure demo admin credentials to enable login.';
  });

  const clearPersistedAuth = () => {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
  };

  const clearAuthState = () => {
    user.value = null;
    token.value = null;
    clearPersistedAuth();
  };

  const persistAuthState = (authResponse: AuthResponse) => {
    user.value = authResponse.user;
    token.value = authResponse.token;
    localStorage.setItem('admin_token', authResponse.token);
    localStorage.setItem('admin_user', JSON.stringify(authResponse.user));
  };

  const updateAuthenticatedUser = (nextUser: User) => {
    user.value = nextUser;
    localStorage.setItem('admin_user', JSON.stringify(nextUser));
  };

  // Initialize from localStorage on store creation
  const initAuth = () => {
    try {
      const storedToken = localStorage.getItem('admin_token');
      const storedUser = localStorage.getItem('admin_user');

      if (storedToken && storedUser) {
        token.value = storedToken;
        user.value = JSON.parse(storedUser);
      }
    } catch {
      clearAuthState();
    }
  };

  // Validate token with backend (for production use)
  const validateToken = async (authToken: string): Promise<User | null> => {
    if (!USES_API_AUTH) {
      // In demo mode, just check if token exists
      return authToken ? user.value : null;
    }

    try {
      const response = await fetch(`${API_BASE_URL}/auth/validate`, {
        headers: buildAuthHeaders(authToken),
      });

      if (!response.ok) {
        return null;
      }

      const data: AuthValidationResponse = await response.json();
      return data.user;
    } catch {
      return null;
    }
  };

  const ensureAuthState = async (): Promise<boolean> => {
    if (hasRestoredAuth.value) {
      return isAuthenticated.value;
    }

    initAuth();
    hasRestoredAuth.value = true;

    if (!token.value || !USES_API_AUTH) {
      return isAuthenticated.value;
    }

    const validatedUser = await validateToken(token.value);

    if (!validatedUser || !token.value) {
      clearAuthState();
      return false;
    }

    user.value = validatedUser;
    localStorage.setItem('admin_user', JSON.stringify(validatedUser));
    return true;
  };

  // Login function - calls API in production, falls back to demo mode for development
  const login = async (credentials: LoginCredentials): Promise<boolean> => {
    isLoading.value = true;
    error.value = null;

    try {
      // Production: Call actual API
      if (USES_API_AUTH) {
        const response = await fetch(`${API_BASE_URL}/auth/login`, {
          method: 'POST',
          headers: buildAuthHeaders(),
          body: JSON.stringify(credentials),
        });

        if (!response.ok) {
          const data = await response.json();
          throw new Error(data.message || 'Login failed');
        }

        const data: AuthResponse = await response.json();
        persistAuthState(data);
        hasRestoredAuth.value = true;

        isLoading.value = false;
        return true;
      }

      // Demo/Development mode: Use hashed password comparison
      // WARNING: This is still NOT secure for production - use proper API authentication
      await new Promise(resolve => setTimeout(resolve, 800)); // Simulate API delay

      const demoEmail = USES_LOCAL_FALLBACK_DEMO ? LOCAL_DEMO_EMAIL : ADMIN_EMAIL;
      if (!demoEmail || (!USES_LOCAL_FALLBACK_DEMO && !ADMIN_PASSWORD_HASH)) {
        throw new Error('Admin login is not configured. Set VITE_API_BASE_URL, or provide VITE_ADMIN_EMAIL and VITE_ADMIN_PASSWORD_HASH for demo mode.');
      }

      const isPasswordValid = USES_LOCAL_FALLBACK_DEMO
        ? credentials.password === LOCAL_DEMO_PASSWORD
        : (await hashPassword(credentials.password)) === ADMIN_PASSWORD_HASH;

      if (credentials.email === demoEmail && isPasswordValid) {
        const mockUser: User = {
          id: '1',
          name: 'Admin Banua',
          email: credentials.email,
          role: 'admin'
        };

        // Generate a more secure token for demo (includes validation)
        const demoToken = `demo_${btoa(credentials.email)}_${Date.now()}_${Math.random().toString(36).substring(2)}`;
        persistAuthState({
          user: mockUser,
          token: demoToken,
        });
        hasRestoredAuth.value = true;

        isLoading.value = false;
        return true;
      } else {
        throw new Error('Invalid email or password');
      }
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Login failed';
      isLoading.value = false;
      return false;
    }
  };

  // Logout function
  const logout = async () => {
    const currentToken = token.value;

    clearAuthState();
    hasRestoredAuth.value = true;

    if (USES_API_AUTH && currentToken) {
      try {
        await fetch(`${API_BASE_URL}/auth/logout`, {
          method: 'POST',
          headers: buildAuthHeaders(currentToken),
        });
      } catch {
        // Ignore logout network errors and clear client state anyway.
      }
    }
  };

  // Check if user has permission
  const hasPermission = (permission: string): boolean => {
    if (!user.value) return false;

    const permissions: Record<string, string[]> = {
      admin: ['dashboard', 'content', 'services', 'settings', 'users', 'orders', 'analytics'],
      editor: ['dashboard', 'content'],
    };

    return permissions[user.value.role]?.includes(permission) ?? false;
  };

  // Initialize on store creation
  initAuth();

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    isAdmin,
    loginHint,
    login,
    logout,
    hasPermission,
    initAuth,
    ensureAuthState,
    validateToken,
    updateAuthenticatedUser,
  };
});
