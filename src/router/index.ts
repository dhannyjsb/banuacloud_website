import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import LearnMore from '../pages/LearnMore.vue';

// Service Pages
import CloudVPS from '../pages/services/CloudVPS.vue';
import WebHosting from '../pages/services/WebHosting.vue';
import Domain from '../pages/services/Domain.vue';
import Backup from '../pages/services/Backup.vue';
import AppDevelopment from '../pages/services/AppDevelopment.vue';
import ITConsulting from '../pages/services/ITConsulting.vue';

const routes = [
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

export default router;
