<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import {
  HardDrive, CheckCircle, ArrowRight, Shield, Clock, Cloud,
  HeadphonesIcon, RefreshCw, Lock, Server
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
    icon: Clock,
    title: 'Backup Otomatis',
    description: 'Jadwalkan backup harian, mingguan, atau kustom sesuai kebutuhan.'
  },
  {
    icon: RefreshCw,
    title: 'Restore Cepat',
    description: 'Restore data dengan satu klik melalui panel kontrol.'
  },
  {
    icon: Cloud,
    title: 'Off-Site Storage',
    description: 'Data backup disimpan di datacenter terpisah untuk keamanan ekstra.'
  },
  {
    icon: Lock,
    title: 'Enkripsi End-to-End',
    description: 'Data dienkripsi sebelum dikirim dan disimpan dengan aman.'
  },
  {
    icon: Server,
    title: 'Multiple Destination',
    description: 'Backup ke berbagai lokasi: local, cloud, atau FTP/SFTP.'
  },
  {
    icon: Shield,
    title: 'Retensi Fleksibel',
    description: 'Pilih periode retensi 7, 14, 30, atau 90 hari.'
  },
];

const plans = [
  {
    name: 'Starter Backup',
    price: '75.000',
    period: '/bulan',
    specs: {
      storage: '10 GB',
      retention: '7 Hari',
      frequency: 'Harian',
      destinations: '2',
    },
    features: [
      'Backup Otomatis',
      'Restore 1 Klik',
      'Support 24/7',
      'Enkripsi AES-256',
    ],
    popular: false,
    color: 'sky',
  },
  {
    name: 'Professional Backup',
    price: '149.000',
    period: '/bulan',
    specs: {
      storage: '50 GB',
      retention: '30 Hari',
      frequency: 'Harian + Manual',
      destinations: '5',
    },
    features: [
      'Backup Otomatis',
      'Restore 1 Klik',
      'Priority Support',
      'Enkripsi AES-256',
      'Off-Site Storage',
    ],
    popular: true,
    color: 'cyan',
  },
  {
    name: 'Enterprise Backup',
    price: '299.000',
    period: '/bulan',
    specs: {
      storage: '200 GB',
      retention: '90 Hari',
      frequency: 'Custom',
      destinations: 'Unlimited',
    },
    features: [
      'Backup Otomatis',
      'Restore 1 Klik',
      'Dedicated Support',
      'Enkripsi AES-256',
      'Off-Site Storage',
      'API Access',
    ],
    popular: false,
    color: 'violet',
  },
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
      <AGlowOrb color="sky" :size="500" :position="{ top: '5%', right: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="400" :position="{ top: '40%', left: '-10%' }" :delay="3" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Hero Section -->
    <section class="relative pt-28 pb-16">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <HardDrive class="w-4 h-4 text-sky-400" />
            <span class="text-sm text-slate-300">Solusi Backup Data</span>
          </div>

          <!-- Title -->
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Lindungi Data Bisnis
            <AGradientText>Anda</AGradientText>
          </h1>

          <!-- Description -->
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            Backup otomatis harian dengan restore sekali klik.
            Jamin keamanan data bisnis Anda dengan solusi backup enterprise.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-300">
            <AButton variant="primary" size="lg">
              Pesan Sekarang
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              Pelajari Lebih Lanjut
            </AButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="section-py relative bg-white/[0.02]">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Fitur <span class="gradient-text">Lengkap</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Semua yang Anda butuhkan untuk menjaga keamanan data.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(feature, index) in features"
            :key="feature.title"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md p-6 h-full hover:border-sky-500/30 transition-colors">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 flex items-center justify-center mb-4">
                <component :is="feature.icon" class="w-6 h-6 text-sky-400" />
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

    <!-- Pricing Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Rencana <span class="gradient-text">Backup</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Pilih kapasitas yang sesuai dengan kebutuhan bisnis Anda.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div
            v-for="(plan, index) in plans"
            :key="plan.name"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div
              class="glass-md p-8 h-full relative"
              :class="{ 'border-sky-500/50': plan.popular }"
            >
              <div v-if="plan.popular" class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-sky-500 to-cyan-500 text-white">
                Paling Populer
              </div>

              <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-white mb-2">{{ plan.name }}</h3>
                <div class="flex items-baseline justify-center gap-1">
                  <span class="text-4xl font-bold text-white">Rp{{ plan.price }}</span>
                  <span class="text-slate-400">{{ plan.period }}</span>
                </div>
              </div>

              <div class="space-y-3 mb-6">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Storage</span>
                  <span class="text-white font-medium">{{ plan.specs.storage }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Retention</span>
                  <span class="text-white font-medium">{{ plan.specs.retention }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Frequency</span>
                  <span class="text-white font-medium">{{ plan.specs.frequency }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Destinations</span>
                  <span class="text-white font-medium">{{ plan.specs.destinations }}</span>
                </div>
              </div>

              <ul class="space-y-3 mb-8">
                <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-2 text-sm text-slate-300">
                  <CheckCircle class="w-4 h-4 text-sky-400 flex-shrink-0" />
                  {{ feature }}
                </li>
              </ul>

              <AButton :variant="plan.popular ? 'primary' : 'secondary'" class="w-full justify-center">
                Pilih Paket
              </AButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="glass-md p-12 md:p-16 text-center max-w-4xl mx-auto relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-sky-500/10 via-cyan-500/10 to-violet-500/10" />

          <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Amankan Data Sekarang
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
              Jangan tunggu sampai data hilang. Lindungi bisnis Anda hari ini.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <AButton variant="primary" size="lg">
                Pesan Sekarang
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
