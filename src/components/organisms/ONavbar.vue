<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Cloud, Menu, X } from 'lucide-vue-next';
import AButton from '../atoms/AButton.vue';

const router = useRouter();
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

const goToLearnMore = () => {
  router.push('/learn-more');
  closeMobileMenu();
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
  <nav
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      isScrolled 
        ? 'bg-[#0a0f1a]/80 backdrop-blur-lg border-b border-white/5 shadow-lg shadow-sky-500/5 py-3' 
        : 'bg-transparent py-5'
    ]"
  >
    <div class="container-custom">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-3 group" @click="closeMobileMenu">
          <div class="relative">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center">
              <Cloud class="w-5 h-5 text-white" />
            </div>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500 blur-lg opacity-50 group-hover:opacity-75 transition-opacity" />
          </div>
          <span class="text-xl font-bold text-white">Banua<span class="text-sky-400">Cloud</span></span>
        </a>

        <!-- Desktop Navigation - Right Side -->
        <div class="hidden md:flex items-center gap-6">
          <a
            v-for="link in navLinks"
            :key="link.name"
            :href="link.href"
            class="text-slate-300 hover:text-white transition-colors duration-200 font-medium"
          >
            {{ link.name }}
          </a>
          <!-- CTA Button -->
          <AButton variant="primary" size="sm" @click="goToLearnMore">
            Mulai Sekarang
          </AButton>
        </div>

        <!-- Mobile Menu Button -->
        <button
          class="md:hidden text-white p-2"
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
        class="md:hidden mt-4 pb-4 animate-fade-in-up"
      >
        <div class="glass-md rounded-xl p-4 flex flex-col gap-4">
          <a
            v-for="link in navLinks"
            :key="link.name"
            :href="link.href"
            class="text-slate-300 hover:text-white transition-colors duration-200 font-medium py-2"
            @click="closeMobileMenu"
          >
            {{ link.name }}
          </a>
          <AButton variant="primary" class="w-full" @click="goToLearnMore">
            Mulai Sekarang
          </AButton>
        </div>
      </div>
    </div>
  </nav>
</template>
