<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Eye, EyeOff, LogIn, AlertCircle, Loader2 } from 'lucide-vue-next';
import { useAuthStore } from '../../stores/auth';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { siteSettings } = useSiteBootstrap();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const rememberMe = ref(false);

const handleLogin = async () => {
  const success = await authStore.login({
    email: email.value,
    password: password.value,
    remember: rememberMe.value,
  });

  if (success) {
    const redirectPath = typeof route.query.redirect === 'string'
      ? route.query.redirect
      : '/admin';
    router.push(redirectPath);
  }
};
</script>

<template>
  <div class="min-h-screen bg-[#0a0f1a] flex items-center justify-center p-4">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/4 -left-32 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl" />
      <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-violet-500/20 rounded-full blur-3xl" />
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-md relative z-10">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div :class="['inline-flex items-center justify-center mb-4 overflow-hidden', siteSettings.logoUrl ? 'h-14 w-14 rounded-[1rem]' : 'w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-500']">
          <img v-if="siteSettings.logoUrl" :src="siteSettings.logoUrl" :alt="siteSettings.siteName" class="h-full w-full scale-[1.03] object-cover" />
          <span v-else class="text-white font-bold text-2xl">B</span>
        </div>
        <h1 class="text-2xl font-bold text-white">{{ siteSettings.siteName }}</h1>
        <p class="text-slate-400 mt-1">Admin Dashboard</p>
      </div>

      <!-- Login Form -->
      <div class="glass-md p-8 rounded-2xl border border-white/10">
        <h2 class="text-xl font-semibold text-white mb-6">Sign In</h2>

        <div
          v-if="authStore.loginHint"
          class="mb-6 p-4 rounded-lg bg-sky-500/10 border border-sky-500/20"
        >
          <p class="text-sky-300 text-sm">{{ authStore.loginHint }}</p>
        </div>

        <!-- Error Alert -->
        <div
          v-if="authStore.error"
          class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 flex items-start gap-3"
        >
          <AlertCircle class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" />
          <p class="text-red-400 text-sm">{{ authStore.error }}</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
            <input
              v-model="email"
              type="email"
              required
              class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-colors"
              placeholder="admin@banuacloud.com"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition-colors pr-12"
                placeholder="••••••••"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition-colors"
              >
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-4 h-4 rounded bg-white/5 border-white/20 text-sky-500 focus:ring-sky-500 focus:ring-offset-0"
              />
              <span class="text-sm text-slate-400">Remember me</span>
            </label>
            <a href="#" class="text-sm text-sky-400 hover:text-sky-300 transition-colors">
              Forgot password?
            </a>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="authStore.isLoading"
            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 text-white font-semibold hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 focus:ring-offset-[#0a0f1a] disabled:opacity-50 disabled:cursor-not-allowed transition-all"
          >
            <Loader2 v-if="authStore.isLoading" class="w-5 h-5 animate-spin" />
            <LogIn v-else class="w-5 h-5" />
            {{ authStore.isLoading ? 'Signing in...' : 'Sign In' }}
          </button>
        </form>
      </div>

      <!-- Back to Website -->
      <div class="text-center mt-6">
        <router-link
          to="/"
          class="text-sm text-slate-400 hover:text-white transition-colors"
        >
          ← Back to Website
        </router-link>
      </div>
    </div>
  </div>
</template>
