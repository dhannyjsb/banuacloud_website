<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import {
  Globe, CheckCircle, ArrowRight, Shield, Zap, Clock,
  HardDrive, Cpu, Mail, Database, FileCode,
  HeadphonesIcon, Star, Award
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
    icon: Zap,
    title: 'Speed Optimal',
    description: 'Server dengan LiteSpeed Web Server dan SSD NVMe untuk loading super cepat.'
  },
  {
    icon: Shield,
    title: 'SSL Gratis',
    description: 'Auto-install SSL Let\'s Encrypt tanpa batas dengan renew otomatis.'
  },
  {
    icon: Mail,
    title: 'Email Unlimited',
    description: 'Buat email profesional tanpa batas dengan webmail Roundcube.'
  },
  {
    icon: Database,
    title: 'Database MySQL',
    description: 'Database MySQL dan PostgreSQL dengan phpMyAdmin akses mudah.'
  },
  {
    icon: FileCode,
    title: 'PHP 8.x Support',
    description: 'Dukungan PHP 8.3, 8.2, 8.1, 8.0 dengan pilihan versi per domain.'
  },
  {
    icon: Cpu,
    title: 'cPanel Kontrol',
    description: 'Panel kontrol intuitif untuk manage website dengan mudah.'
  },
];

const plans = [
  {
    name: 'Starter Hosting',
    price: '29.000',
    period: '/bulan',
    specs: {
      space: '5 GB SSD',
      bandwidth: 'Unlimited',
      email: '5 Akun',
      database: '5 MySQL',
      subdomain: '5',
      addon_domain: '1',
    },
    features: [
      'cPanel Kontrol',
      'SSL Gratis',
      'Backup Mingguan',
      'Support 24/7',
    ],
    popular: false,
    color: 'sky',
  },
  {
    name: 'Business Hosting',
    price: '59.000',
    period: '/bulan',
    specs: {
      space: '15 GB SSD',
      bandwidth: 'Unlimited',
      email: 'Unlimited',
      database: 'Unlimited MySQL',
      subdomain: 'Unlimited',
      addon_domain: '3',
    },
    features: [
      'cPanel Kontrol',
      'SSL Gratis',
      'Backup Harian',
      'Priority Support',
      'LiteSpeed Cache',
    ],
    popular: true,
    color: 'cyan',
  },
  {
    name: 'Ultimate Hosting',
    price: '99.000',
    period: '/bulan',
    specs: {
      space: '30 GB SSD NVMe',
      bandwidth: 'Unlimited',
      email: 'Unlimited',
      database: 'Unlimited MySQL',
      subdomain: 'Unlimited',
      addon_domain: 'Unlimited',
    },
    features: [
      'cPanel Kontrol',
      'SSL Gratis',
      'Backup Harian',
      'Dedicated Support',
      'LiteSpeed Cache',
      'Priority Queue',
    ],
    popular: false,
    color: 'violet',
  },
];

const technologies = [
  'WordPress', 'Laravel', 'CodeIgniter', 'React', 'Vue.js',
  'Next.js', 'Node.js', 'Python', 'PHP', 'MySQL', 'PostgreSQL', 'MongoDB'
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
      <AGlowOrb color="cyan" :size="500" :position="{ top: '5%', right: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="sky" :size="400" :position="{ top: '40%', left: '-10%' }" :delay="3" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Hero Section -->
    <section class="relative pt-28 pb-16">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <Globe class="w-4 h-4 text-cyan-400" />
            <span class="text-sm text-slate-300">Web Hosting Indonesia</span>
          </div>

          <!-- Title -->
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Hosting Cepat &
            <AGradientText>Terpercaya</AGradientText>
          </h1>

          <!-- Description -->
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            Wujudkan website profesional dengan hosting berkecepatan tinggi,
            SSL gratis, dan support terbaik di Indonesia.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-300">
            <AButton variant="primary" size="lg">
              Pesan Sekarang
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              Lihat Pricing
            </AButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Keunggulan <span class="gradient-text">Web Hosting</span> Kami
          </h2>
          <p class="text-slate-400 text-lg">
            Didesain untuk performa maksimal dengan teknologi terkini.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(feature, index) in features"
            :key="feature.title"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md p-6 h-full hover:border-cyan-500/30 transition-colors">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-teal-500/20 border border-cyan-500/20 flex items-center justify-center mb-4">
                <component :is="feature.icon" class="w-6 h-6 text-cyan-400" />
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
    <section class="section-py relative bg-white/[0.02]">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Rencana <span class="gradient-text">Hosting</span> Terjangkau
          </h2>
          <p class="text-slate-400 text-lg">
            Mulai dari harga ramah kantong dengan fitur lengkap.
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
              :class="{ 'border-cyan-500/50': plan.popular }"
            >
              <!-- Popular Badge -->
              <div
                v-if="plan.popular"
                class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-cyan-500 to-teal-500 text-white"
              >
                Paling Populer
              </div>

              <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-white mb-2">{{ plan.name }}</h3>
                <div class="flex items-baseline justify-center gap-1">
                  <span class="text-4xl font-bold text-white">Rp{{ plan.price }}</span>
                  <span class="text-slate-400">{{ plan.period }}</span>
                </div>
              </div>

              <!-- Specs -->
              <div class="space-y-3 mb-6">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Disk Space</span>
                  <span class="text-white font-medium">{{ plan.specs.space }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Bandwidth</span>
                  <span class="text-white font-medium">{{ plan.specs.bandwidth }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Email</span>
                  <span class="text-white font-medium">{{ plan.specs.email }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Database</span>
                  <span class="text-white font-medium">{{ plan.specs.database }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Addon Domain</span>
                  <span class="text-white font-medium">{{ plan.specs.addon_domain }}</span>
                </div>
              </div>

              <!-- Features -->
              <ul class="space-y-3 mb-8">
                <li
                  v-for="feature in plan.features"
                  :key="feature"
                  class="flex items-center gap-2 text-sm text-slate-300"
                >
                  <CheckCircle class="w-4 h-4 text-cyan-400 flex-shrink-0" />
                  {{ feature }}
                </li>
              </ul>

              <AButton
                :variant="plan.popular ? 'primary' : 'secondary'"
                class="w-full justify-center"
              >
                Pilih Paket
              </AButton>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Technologies Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Kompatibel dengan <span class="gradient-text">Teknologi Populer</span>
            </h2>
            <p class="text-slate-400 text-lg">
              Semua framework dan CMS favorit Anda dapat dijalankan dengan mudah.
            </p>
          </div>

          <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
            <div
              v-for="tech in technologies"
              :key="tech"
              class="glass-md p-4 text-center hover:border-cyan-500/30 transition-colors cursor-pointer"
            >
              <span class="text-sm text-white">{{ tech }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="glass-md p-12 md:p-16 text-center max-w-4xl mx-auto relative overflow-hidden">
          <!-- Background gradient -->
          <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-sky-500/10 to-violet-500/10" />

          <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Mulai Online Hari Ini
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
              Punya pertanyaan? Tim sales kami siap membantu Anda memilih hosting yang tepat.
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
