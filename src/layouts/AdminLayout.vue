<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  LayoutDashboard,
  FileText,
  Package,
  Settings,
  LogOut,
  Menu,
  X,
  ChevronRight,
  Bell,
  User
} from 'lucide-vue-next';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const isSidebarOpen = ref(true);
const isMobileMenuOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/admin/login');
};

const menuItems = [
  {
    name: 'Dashboard',
    path: '/admin',
    icon: LayoutDashboard
  },
  {
    name: 'Content',
    path: '/admin/content',
    icon: FileText
  },
  {
    name: 'Services',
    path: '/admin/services',
    icon: Package
  },
  {
    name: 'Settings',
    path: '/admin/settings',
    icon: Settings
  },
];

const isActiveRoute = (path: string) => {
  return route.path === path || route.path.startsWith(path + '/');
};

const breadcrumbs = computed(() => {
  const paths = route.path.split('/').filter(Boolean);
  return paths.map((path, index) => ({
    name: path.charAt(0).toUpperCase() + path.slice(1),
    path: '/' + paths.slice(0, index + 1).join('/'),
  }));
});
</script>

<template>
  <div class="min-h-screen bg-[#0a0f1a] flex">
    <!-- Mobile Overlay -->
    <div
      v-if="isMobileMenuOpen"
      class="fixed inset-0 bg-black/50 z-40 lg:hidden"
      @click="toggleMobileMenu"
    />

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed lg:static inset-y-0 left-0 z-50 w-64 bg-[#0f172a]/95 backdrop-blur-xl border-r border-white/10 transition-transform duration-300',
        isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isSidebarOpen ? 'lg:w-64' : 'lg:w-20'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center px-6 border-b border-white/10">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center">
            <span class="text-white font-bold text-sm">B</span>
          </div>
          <span
            :class="[
              'font-semibold text-white transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'lg:opacity-0 lg:hidden'
            ]"
          >
            Banua Admin
          </span>
        </div>
        <button
          @click="toggleMobileMenu"
          class="lg:hidden ml-auto text-slate-400 hover:text-white"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Navigation -->
      <nav class="p-4 space-y-1">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 group',
            isActiveRoute(item.path)
              ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30'
              : 'text-slate-400 hover:bg-white/5 hover:text-white'
          ]"
        >
          <component
            :is="item.icon"
            :class="[
              'w-5 h-5 flex-shrink-0',
              isActiveRoute(item.path) ? 'text-sky-400' : 'group-hover:text-white'
            ]"
          />
          <span
            :class="[
              'font-medium transition-all duration-300',
              isSidebarOpen ? 'opacity-100' : 'lg:opacity-0 lg:hidden lg:w-0'
            ]"
          >
            {{ item.name }}
          </span>
          <ChevronRight
            v-if="isActiveRoute(item.path) && isSidebarOpen"
            class="w-4 h-4 ml-auto"
          />
        </router-link>
      </nav>

      <!-- Logout -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
        <button
          @click="handleLogout"
          class="flex items-center gap-3 px-4 py-3 w-full rounded-lg text-slate-400 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200 group"
        >
          <LogOut class="w-5 h-5" />
          <span
            :class="[
              'font-medium transition-all duration-300',
              isSidebarOpen ? 'opacity-100' : 'lg:opacity-0 lg:hidden'
            ]"
          >
            Logout
          </span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header -->
      <header class="h-16 bg-[#0f172a]/80 backdrop-blur-xl border-b border-white/10 flex items-center px-4 lg:px-8">
        <div class="flex items-center gap-4">
          <button
            @click="toggleSidebar"
            class="hidden lg:flex p-2 rounded-lg text-slate-400 hover:bg-white/5 hover:text-white transition-colors"
          >
            <Menu class="w-5 h-5" />
          </button>
          <button
            @click="toggleMobileMenu"
            class="lg:hidden p-2 rounded-lg text-slate-400 hover:bg-white/5 hover:text-white transition-colors"
          >
            <Menu class="w-5 h-5" />
          </button>

          <!-- Breadcrumbs -->
          <nav class="hidden md:flex items-center gap-2 text-sm">
            <router-link to="/admin" class="text-slate-400 hover:text-white transition-colors">
              Admin
            </router-link>
            <ChevronRight class="w-4 h-4 text-slate-600" />
            <span class="text-white">{{ breadcrumbs[breadcrumbs.length - 1]?.name }}</span>
          </nav>
        </div>

        <div class="ml-auto flex items-center gap-4">
          <!-- Notifications -->
          <button class="relative p-2 rounded-lg text-slate-400 hover:bg-white/5 hover:text-white transition-colors">
            <Bell class="w-5 h-5" />
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
          </button>

          <!-- User Menu -->
          <div class="flex items-center gap-3 pl-4 border-l border-white/10">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center">
              <User class="w-4 h-4 text-white" />
            </div>
            <div class="hidden md:block">
              <p class="text-sm font-medium text-white">{{ authStore.user?.name || 'Admin' }}</p>
              <p class="text-xs text-slate-400">Administrator</p>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-auto p-4 lg:p-8">
        <div class="max-w-7xl mx-auto">
          <router-view />
        </div>
      </main>
    </div>
  </div>
</template>
