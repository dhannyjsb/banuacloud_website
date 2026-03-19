<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
  LayoutDashboard,
  FileText,
  Package,
  Settings,
  Lock,
  LogOut,
  Menu,
  X,
  ChevronDown,
  ChevronRight,
  Bell,
  User
} from 'lucide-vue-next';
import { useAuthStore } from '../stores/auth';
import { useSiteBootstrap } from '../composables/useSiteBootstrap';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { siteSettings } = useSiteBootstrap();

const isSidebarOpen = ref(true);
const isMobileMenuOpen = ref(false);
const isUserMenuOpen = ref(false);
const userMenuRef = ref<HTMLElement | null>(null);
const currentYear = new Date().getFullYear();

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
  isUserMenuOpen.value = false;
};

const goToSettings = () => {
  closeUserMenu();
  router.push('/admin/settings');
};

const goToChangePassword = () => {
  closeUserMenu();
  router.push('/admin/settings?tab=security');
};

const handleLogout = async () => {
  closeUserMenu();
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
  if (path === '/admin') {
    return route.path === path;
  }

  return route.path === path || route.path.startsWith(path + '/');
};

const handleClickOutside = (event: MouseEvent) => {
  if (!userMenuRef.value) {
    return;
  }

  if (!userMenuRef.value.contains(event.target as Node)) {
    closeUserMenu();
  }
};

const handleEscape = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    closeUserMenu();
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleEscape);
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
          <div :class="['flex items-center justify-center overflow-hidden', siteSettings.logoUrl ? 'h-7 w-7 rounded-md' : 'w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-500']">
            <img v-if="siteSettings.logoUrl" :src="siteSettings.logoUrl" :alt="siteSettings.siteName" class="h-full w-full scale-[1.00] object-cover" />
            <span v-else class="text-white font-bold text-sm">B</span>
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

    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header -->
      <header class="relative z-30 h-16 bg-[#0f172a]/80 backdrop-blur-xl border-b border-white/10 flex items-center px-4 lg:px-8">
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
        </div>

        <div class="ml-auto flex items-center gap-4">
          <!-- Notifications -->
          <button class="relative p-2 rounded-lg text-slate-400 hover:bg-white/5 hover:text-white transition-colors">
            <Bell class="w-5 h-5" />
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
          </button>

          <!-- User Menu -->
          <div ref="userMenuRef" class="relative z-40 pl-4 border-l border-white/10">
            <button
              @click="toggleUserMenu"
              class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-left transition-colors hover:bg-white/5"
              :aria-expanded="isUserMenuOpen"
              aria-haspopup="menu"
            >
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-500 flex items-center justify-center">
                <User class="w-4 h-4 text-white" />
              </div>
              <div class="hidden md:block">
                <p class="text-sm font-medium text-white">{{ authStore.user?.name || 'Admin' }}</p>
                <p class="text-xs text-slate-400">Administrator</p>
              </div>
              <ChevronDown class="hidden md:block w-4 h-4 text-slate-400 transition-transform" :class="isUserMenuOpen ? 'rotate-180' : ''" />
            </button>

            <div
              v-if="isUserMenuOpen"
              class="absolute right-0 top-full z-50 mt-3 w-56 rounded-2xl border border-white/10 bg-[#0f172a]/95 p-2 shadow-2xl backdrop-blur-xl"
            >
              <div class="px-3 py-2 border-b border-white/10">
                <p class="text-sm font-medium text-white">{{ authStore.user?.name || 'Admin' }}</p>
                <p class="text-xs text-slate-400">{{ authStore.user?.email || 'admin@banuacloud.com' }}</p>
              </div>

              <button
                @click="goToChangePassword"
                class="mt-2 flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-white/5 hover:text-white"
              >
                <Lock class="w-4 h-4" />
                Change Password
              </button>

              <button
                @click="goToSettings"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-300 transition-colors hover:bg-white/5 hover:text-white"
              >
                <Settings class="w-4 h-4" />
                Settings
              </button>

              <button
                @click="handleLogout"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm text-red-300 transition-colors hover:bg-red-500/10 hover:text-red-200"
              >
                <LogOut class="w-4 h-4" />
                Logout
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="relative z-0 flex-1 overflow-auto p-4 lg:p-8">
        <div class="max-w-7xl mx-auto">
          <router-view />
        </div>
      </main>

      <footer class="border-t border-white/10 bg-[#0f172a]/70 px-4 py-4 backdrop-blur-xl lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
          <p>© {{ currentYear }} {{ siteSettings.siteName }} Admin Panel</p>
          <p>Infrastructure, Innovation, Impact</p>
        </div>
      </footer>
    </div>
  </div>
</template>
