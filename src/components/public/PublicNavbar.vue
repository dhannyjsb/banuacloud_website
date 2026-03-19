<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowRight, Menu, X } from 'lucide-vue-next';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const route = useRoute();
const router = useRouter();
const isMenuOpen = ref(false);
const { siteSettings } = useSiteBootstrap();

const navigateHome = () => {
  isMenuOpen.value = false;

  if (route.path === '/') {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return;
  }

  void router.push('/');
};

const goToHomeSection = (hash: string) => {
  isMenuOpen.value = false;

  if (route.path === '/') {
    document.querySelector(hash)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    return;
  }

  void router.push({ path: '/', hash });
};

const goToLearnMore = () => {
  isMenuOpen.value = false;
  void router.push('/learn-more');
};
</script>

<template>
  <header class="public-navbar">
    <div class="public-container">
      <div class="public-nav-shell">
        <button type="button" class="public-logo" @click="navigateHome">
          <span :class="['public-logo-mark overflow-hidden', siteSettings.logoUrl ? 'is-image h-8 w-8 rounded-lg' : '']">
            <img v-if="siteSettings.logoUrl" :src="siteSettings.logoUrl" :alt="siteSettings.siteName" class="h-full w-full scale-[1.00] object-cover" />
            <span v-else>B</span>
          </span>
          <span class="text-sm font-semibold tracking-tight text-slate-900">{{ siteSettings.siteName }}</span>
        </button>

        <nav class="hidden items-center gap-7 lg:flex">
          <button type="button" class="public-nav-link" @click="goToHomeSection('#services')">Layanan</button>
          <button type="button" class="public-nav-link" @click="goToHomeSection('#advantages')">Keunggulan</button>
          <button type="button" class="public-nav-link" @click="goToHomeSection('#testimonials')">Testimoni</button>
          <button type="button" class="public-nav-link" @click="goToHomeSection('#contact')">Kontak</button>
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
          <button type="button" class="public-button public-button-ghost" @click="goToHomeSection('#services')">
            Lihat Layanan
          </button>
          <button type="button" class="public-button public-button-primary" @click="goToLearnMore">
            Profil Perusahaan
            <ArrowRight class="h-3.5 w-3.5" />
          </button>
        </div>

        <button
          type="button"
          class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 lg:hidden"
          :aria-expanded="isMenuOpen"
          aria-label="Toggle menu"
          @click="isMenuOpen = !isMenuOpen"
        >
          <X v-if="isMenuOpen" class="h-5 w-5" />
          <Menu v-else class="h-5 w-5" />
        </button>
      </div>

      <div v-if="isMenuOpen" class="border-t border-slate-100 pb-4 lg:hidden">
        <div class="space-y-1 pt-3">
          <button type="button" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900" @click="goToHomeSection('#services')">Layanan</button>
          <button type="button" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900" @click="goToHomeSection('#advantages')">Keunggulan</button>
          <button type="button" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900" @click="goToHomeSection('#testimonials')">Testimoni</button>
          <button type="button" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900" @click="goToHomeSection('#contact')">Kontak</button>
          <div class="pt-2">
            <button type="button" class="public-button public-button-primary w-full" @click="goToLearnMore">
              Profil Perusahaan
              <ArrowRight class="h-3.5 w-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
