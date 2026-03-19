<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Cloud, Menu, X } from 'lucide-vue-next';
import AButton from '../atoms/AButton.vue';

const router = useRouter();
const route = useRoute();
const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const navLinks = [
  { name: 'Beranda', href: '/' },
  { name: 'Layanan', href: '/learn-more' },
  { name: 'Cloud VPS', href: '/services/cloud-vps' },
  { name: 'Web Hosting', href: '/services/web-hosting' },
  { name: 'Domain', href: '/services/domain' },
];

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};

const navigateTo = (href: string) => {
  router.push(href);
  closeMobileMenu();
};

const goToLearnMore = () => {
  navigateTo('/learn-more');
};

const isActiveLink = (href: string) => {
  return route.path === href;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

watch(
  () => route.fullPath,
  () => {
    closeMobileMenu();
  },
);
</script>

<template>
  <nav
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      isScrolled
        ? 'py-3'
        : 'py-5'
    ]"
  >
    <div class="container-custom">
      <div
        :class="[
          'flex items-center justify-between rounded-[28px] border px-4 py-3 transition-all duration-300 md:px-5',
          isScrolled
            ? 'border-white/10 bg-[#09111f]/88 shadow-[0_18px_60px_rgba(2,12,27,0.35)] backdrop-blur-2xl'
            : 'border-white/8 bg-[#09111f]/62 backdrop-blur-xl'
        ]"
      >
        <!-- Logo -->
        <button type="button" class="flex items-center gap-3 group text-left" @click="navigateTo('/')">
          <div class="relative">
            <div class="relative z-10 w-11 h-11 rounded-2xl bg-[linear-gradient(135deg,#0ea5e9_0%,#22d3ee_100%)] flex items-center justify-center shadow-[0_14px_35px_rgba(14,165,233,0.35)]">
              <Cloud class="w-5 h-5 text-white" />
            </div>
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-sky-400 to-cyan-500 blur-xl opacity-45 group-hover:opacity-70 transition-opacity" />
          </div>
          <div>
            <span class="block text-lg font-bold text-white">Banua<span class="text-sky-400">Cloud</span></span>
            <span class="hidden text-xs uppercase tracking-[0.22em] text-slate-400 md:block">Infrastruktur Terkelola</span>
          </div>
        </button>

        <!-- Desktop Navigation - Right Side -->
        <div class="hidden lg:flex items-center gap-3">
          <div class="flex items-center gap-1 rounded-2xl border border-white/8 bg-white/[0.03] p-1.5">
            <button
            v-for="link in navLinks"
            :key="link.name"
              type="button"
              @click="navigateTo(link.href)"
              :class="[
                'rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200',
                isActiveLink(link.href)
                  ? 'bg-white/[0.08] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]'
                  : 'text-slate-300 hover:bg-white/[0.04] hover:text-white'
              ]"
          >
            {{ link.name }}
            </button>
          </div>
          <AButton variant="primary" size="sm" @click="goToLearnMore">
            Jelajahi Layanan
          </AButton>
        </div>

        <!-- Mobile Menu Button -->
        <button
          class="rounded-xl border border-white/10 bg-white/[0.03] p-2.5 text-white lg:hidden"
          @click="toggleMobileMenu"
          aria-label="Toggle menu"
        >
          <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
          <X v-else class="w-6 h-6" />
        </button>
      </div>

      <!-- Mobile Menu -->
      <div
        v-if="isMobileMenuOpen"
        class="mt-4 animate-fade-in-up lg:hidden"
      >
        <div class="glass-lg flex flex-col gap-2 p-4">
          <button
            v-for="link in navLinks"
            :key="link.name"
            type="button"
            @click="navigateTo(link.href)"
            :class="[
              'rounded-xl px-4 py-3 text-left text-sm font-medium transition-colors duration-200',
              isActiveLink(link.href)
                ? 'bg-white/[0.08] text-white'
                : 'text-slate-300 hover:bg-white/[0.04] hover:text-white'
            ]"
          >
            {{ link.name }}
          </button>
          <AButton variant="primary" class="w-full" @click="goToLearnMore">
            Jelajahi Layanan
          </AButton>
        </div>
      </div>
    </div>
  </nav>
</template>
