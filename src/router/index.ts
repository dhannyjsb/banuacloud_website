import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Home from '../pages/Home.vue';
import LearnMore from '../pages/LearnMore.vue';

// Service Pages
import CloudVPS from '../pages/services/CloudVPS.vue';
import WebHosting from '../pages/services/WebHosting.vue';
import Domain from '../pages/services/Domain.vue';
import Backup from '../pages/services/Backup.vue';
import AppDevelopment from '../pages/services/AppDevelopment.vue';
import ITConsulting from '../pages/services/ITConsulting.vue';

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
    path: '/services/cloud-vps',
    name: 'CloudVPS',
    component: CloudVPS,
  },
  {
    path: '/services/web-hosting',
    name: 'WebHosting',
    component: WebHosting,
  },
  {
    path: '/services/domain',
    name: 'Domain',
    component: Domain,
  },
  {
    path: '/services/backup',
    name: 'Backup',
    component: Backup,
  },
  {
    path: '/services/app-development',
    name: 'AppDevelopment',
    component: AppDevelopment,
  },
  {
    path: '/services/it-consulting',
    name: 'ITConsulting',
    component: ITConsulting,
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
    } else {
      return { top: 0 };
    }
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
