<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import {
  Database, CheckCircle, ArrowRight, Shield, Globe, Lock,
  HeadphonesIcon, Search, Star
} from 'lucide-vue-next';
import ONavbar from '../../components/organisms/ONavbar.vue';
import OFooter from '../../components/organisms/OFooter.vue';
import AButton from '../../components/atoms/AButton.vue';
import AGradientText from '../../components/atoms/AGradientText.vue';
import AGlowOrb from '../../components/atoms/AGlowOrb.vue';
import { useScrollAnimation } from '../../composables/useIntersectionObserver';

const { initScrollAnimations } = useScrollAnimation();

const features = [
  {
    icon: Search,
    title: 'Domain Checker',
    description: 'Cek ketersediaan domain favorite Anda secara real-time dengan mudah.'
  },
  {
    icon: Shield,
    title: 'Privacy Protection',
    description: 'Lindungi informasi pribadi Anda dengan WHOIS privacy gratis.'
  },
  {
    icon: Globe,
    title: 'DNS Management',
    description: 'Kelola DNS records dengan mudah melalui panel kontrol kami.'
  },
  {
    icon: Lock,
    title: 'SSL Gratis',
    description: 'Auto-install SSL untuk subdomain dengan Cloudflare integration.'
  },
  {
    icon: ArrowRight,
    title: 'Domain Transfer',
    description: 'Pindahkan domain Anda ke kami dengan proses mudah dan murah.'
  },
  {
    icon: Star,
    title: '500+ Ekstensi',
    description: 'Pilihan ekstensi domain lengkap dari .com hingga .id'
  },
];

const popularDomains = [
  { ext: '.com', price: '149.000', popular: true },
  { ext: '.id', price: '75.000', popular: true },
  { ext: '.net', price: '159.000', popular: false },
  { ext: '.org', price: '159.000', popular: false },
  { ext: '.co.id', price: '250.000', popular: true },
  { ext: '.web.id', price: '75.000', popular: false },
  { ext: '.biz', price: '99.000', popular: false },
  { ext: '.info', price: '79.000', popular: false },
];

onMounted(async () => {
  await nextTick();
  initScrollAnimations();
});
</script>

<template>
  <div class="min-h-screen bg-[#0a0f1a]">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb color="violet" :size="500" :position="{ top: '5%', left: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="sky" :size="400" :position="{ top: '40%', right: '-10%' }" :delay="3" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Hero Section -->
    <section class="relative pt-28 pb-16">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <Globe class="w-4 h-4 text-violet-400" />
            <span class="text-sm text-slate-300">Registrasi Domain</span>
          </div>

          <!-- Title -->
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Dapatkan Domain
            <AGradientText>Impian Anda</AGradientText>
          </h1>

          <!-- Description -->
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            Mulai perjalanan online Anda dengan domain yang tepat.
            Harga terjangkau dengan fitur lengkap.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-300">
            <AButton variant="primary" size="lg">
              Cek Ketersediaan
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              Transfer Domain
            </AButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Domains Section -->
    <section class="section-py relative bg-white/[0.02]">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Domain <span class="gradient-text">Populer</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Harga kompetitif untuk ekstensi domain paling dicari.
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
          <div
            v-for="(domain, index) in popularDomains"
            :key="domain.ext"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 50}ms` }"
          >
            <div
              class="glass-md p-6 text-center hover:border-violet-500/30 transition-colors cursor-pointer"
              :class="{ 'border-violet-500/30': domain.popular }"
            >
              <div class="flex items-center justify-center gap-2 mb-2">
                <span class="text-2xl font-bold text-white">{{ domain.ext }}</span>
                <Star v-if="domain.popular" class="w-4 h-4 text-yellow-400 fill-yellow-400" />
              </div>
              <span class="text-violet-400 font-semibold">Rp{{ domain.price }}/th</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Mengapa Beli Domain di <span class="gradient-text">Banua Cloud</span>?
          </h2>
          <p class="text-slate-400 text-lg">
            Fitur lengkap untuk mengelola domain Anda dengan mudah.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(feature, index) in features"
            :key="feature.title"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md p-6 h-full hover:border-violet-500/30 transition-colors">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/20 flex items-center justify-center mb-4">
                <component :is="feature.icon" class="w-6 h-6 text-violet-400" />
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">{{ feature.title }}</h3>
              <p class="text-slate-400 text-sm leading-relaxed">
                {{ feature.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="glass-md p-12 md:p-16 text-center max-w-4xl mx-auto relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-sky-500/10" />

          <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Domain Ready Untuk Online
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
              Dapatkan domain terbaik untuk bisnis atau proyek Anda hari ini.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <AButton variant="primary" size="lg">
                Cek Ketersediaan
                <ArrowRight class="ml-2 w-5 h-5" />
              </AButton>
              <AButton variant="secondary" size="lg">
                <HeadphonesIcon class="w-5 h-5 mr-2" />
                Chat dengan Kami
              </AButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <OFooter />
  </div>
</template>
