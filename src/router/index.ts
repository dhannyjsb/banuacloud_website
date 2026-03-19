import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Home from '../pages/Home.vue';
import LearnMore from '../pages/LearnMore.vue';
import ServiceDetail from '../pages/services/ServiceDetail.vue';

// Admin Pages
import AdminLogin from '../pages/admin/AdminLogin.vue';
import AdminDashboard from '../pages/admin/Dashboard.vue';
import AdminContent from '../pages/admin/Content.vue';
import AdminServices from '../pages/admin/Services.vue';
import AdminSettings from '../pages/admin/Settings.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

// Public routes
const publicRoutes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/learn-more',
    name: 'LearnMore',
    component: LearnMore,
  },
  // Service Routes
  {
    path: '/services/:slug',
    name: 'ServiceDetail',
    component: ServiceDetail,
  },
];

// Admin routes
const adminRoutes = [
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin,
    meta: { title: 'Admin Login', layout: 'none' }
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: { title: 'Dashboard' }
      },
      {
        path: 'content',
        name: 'AdminContent',
        component: AdminContent,
        meta: { title: 'Content Management' }
      },
      {
        path: 'services',
        name: 'AdminServices',
        component: AdminServices,
        meta: { title: 'Services Management' }
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: AdminSettings,
        meta: { title: 'Settings' }
      },
    ]
  },
  // Catch-all for admin routes
  {
    path: '/admin/:pathMatch(.*)*',
    redirect: '/admin'
  }
];

const routes = [...publicRoutes, ...adminRoutes];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }

    if (_to.hash) {
      return {
        el: _to.hash,
        top: 96,
        behavior: 'smooth',
      };
    }

    return { top: 0 };
  },
});

// Navigation guard for admin routes
router.beforeEach(async (to) => {
  const authStore = useAuthStore();
  await authStore.ensureAuthState();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return {
      path: '/admin/login',
      query: { redirect: to.fullPath }
    };
  }

  if (to.path === '/admin/login' && authStore.isAuthenticated) {
    return { path: '/admin' };
  }

  return true;
});

export default router;
