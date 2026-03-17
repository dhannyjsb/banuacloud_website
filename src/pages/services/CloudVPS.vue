<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { 
  Server, CheckCircle, ArrowRight, Shield, Zap, Clock, 
  HardDrive, Cpu, Globe, ArrowLeft, ChevronRight,
  Terminal, Database, Layers, Lock, RefreshCw, HeadphonesIcon
} from 'lucide-vue-next';
import ONavbar from '../../components/organisms/ONavbar.vue';
import OFooter from '../../components/organisms/OFooter.vue';
import AButton from '../../components/atoms/AButton.vue';
import AGradientText from '../../components/atoms/AGradientText.vue';
import AGlowOrb from '../../components/atoms/AGlowOrb.vue';
import { useScrollAnimation } from '../../composables/useIntersectionObserver';

const router = useRouter();
const { initScrollAnimations } = useScrollAnimation();

const features = [
  {
    icon: Cpu,
    title: 'Performa Tinggi',
    description: 'Prosesor terbaru dengan clock speed tinggi untuk处理数据 paling intensif.'
  },
  {
    icon: HardDrive,
    title: 'NVMe SSD Storage',
    description: 'Penyimpanan NVMe SSD ultra-cepat dengan kecepatan baca/tulis hingga 3500MB/s.'
  },
  {
    icon: Zap,
    title: 'Resource Terjamin',
    description: 'CPU, RAM, dan storage dedicated tidak berbagi dengan pengguna lain.'
  },
  {
    icon: Shield,
    title: 'Keamanan Terdepan',
    description: 'Firewall hardware, DDoS protection, dan backup otomatis harian.'
  },
  {
    icon: Globe,
    title: 'Global Network',
    description: 'Multiple datacenter lokasi dengan bandwidth unlimited dan low latency.'
  },
  {
    icon: Terminal,
    title: 'Akses Root Penuh',
    description: 'Kontrol penuh dengan akses root/kunci untuk konfigurasi custom.'
  },
];

const plans = [
  {
    name: 'Starter VPS',
    price: '149.000',
    period: '/bulan',
    specs: {
      cpu: '2 vCPU',
      ram: '2 GB',
      storage: '30 GB NVMe',
      bandwidth: 'Unlimited',
      ip: '1 IPv4',
    },
    features: [
      'Panel 管理 (optional)',
      'Backup Mingguan',
      'Support 24/7',
      '99.9% Uptime',
    ],
    popular: false,
    color: 'sky',
  },
  {
    name: 'Professional VPS',
    price: '299.000',
    period: '/bulan',
    specs: {
      cpu: '4 vCPU',
      ram: '4 GB',
      storage: '60 GB NVMe',
      bandwidth: 'Unlimited',
      ip: '1 IPv4',
    },
    features: [
      'Panel 管理 (optional)',
      'Backup Harian',
      'Priority Support',
      '99.9% Uptime',
      'DDoS Protection',
    ],
    popular: true,
    color: 'cyan',
  },
  {
    name: 'Enterprise VPS',
    price: '599.000',
    period: '/bulan',
    specs: {
      cpu: '8 vCPU',
      ram: '8 GB',
      storage: '120 GB NVMe',
      bandwidth: 'Unlimited',
      ip: '1 IPv4',
    },
    features: [
      'Panel 管理 (optional)',
      'Backup Harian',
      'Dedicated Support',
      '99.9% Uptime',
      'DDoS Protection',
      'Free SSL',
    ],
    popular: false,
    color: 'violet',
  },
];

const operatingSystems = [
  'Ubuntu 22.04 LTS',
  'Ubuntu 20.04 LTS',
  'CentOS 8',
  'Debian 11',
  'AlmaLinux 8',
  'Windows Server 2019',
  'Windows Server 2022',
];

const goBack = () => {
  router.push('/learn-more');
};

onMounted(async () => {
  await nextTick();
  initScrollAnimations();
});
</script>

<template>
  <div class="min-h-screen bg-[#0a0f1a]">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb color="sky" :size="500" :position="{ top: '5%', left: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="400" :position="{ top: '40%', right: '-10%' }" :delay="3" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Breadcrumb -->
    <div class="container-custom pt-24 pb-4 relative z-10">
      <nav class="flex items-center gap-2 text-sm text-slate-400">
        <button @click="goBack" class="hover:text-sky-400 transition-colors flex items-center gap-1">
          <ArrowLeft class="w-4 h-4" />
          Semua Layanan
        </button>
        <ChevronRight class="w-4 h-4" />
        <span class="text-white">Cloud VPS</span>
      </nav>
    </div>

    <!-- Hero Section -->
    <section class="relative pt-8 pb-16">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <Zap class="w-4 h-4 text-sky-400" />
            <span class="text-sm text-slate-300">Server Pribadi Virtual</span>
          </div>
          
          <!-- Title -->
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Cloud VPS dengan
            <AGradientText>Performa Tinggi</AGradientText>
          </h1>
          
          <!-- Description -->
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            Rasakan kontrol penuh atas server Anda dengan sumber daya dedicated, 
            performa tingkat enterprise, dan harga yang terjangkau.
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
            Mengapa Memilih <span class="gradient-text">Cloud VPS</span> Kami?
          </h2>
          <p class="text-slate-400 text-lg">
            Dilengkapi dengan infrastruktur enterprise untuk performa maksimal dan keandalan.
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
    <section class="section-py relative bg-white/[0.02]">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Rencana <span class="gradient-text">VPS</span> yang Fleksibel
          </h2>
          <p class="text-slate-400 text-lg">
            Pilih paket yang sesuai dengan kebutuhan Anda. Semua paket incluye support 24/7.
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
              <!-- Popular Badge -->
              <div 
                v-if="plan.popular"
                class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-sky-500 to-cyan-500 text-white"
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
                  <span class="text-slate-400">CPU</span>
                  <span class="text-white font-medium">{{ plan.specs.cpu }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">RAM</span>
                  <span class="text-white font-medium">{{ plan.specs.ram }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Storage</span>
                  <span class="text-white font-medium">{{ plan.specs.storage }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">Bandwidth</span>
                  <span class="text-white font-medium">{{ plan.specs.bandwidth }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-400">IP</span>
                  <span class="text-white font-medium">{{ plan.specs.ip }}</span>
                </div>
              </div>

              <!-- Features -->
              <ul class="space-y-3 mb-8">
                <li 
                  v-for="feature in plan.features" 
                  :key="feature"
                  class="flex items-center gap-2 text-sm text-slate-300"
                >
                  <CheckCircle class="w-4 h-4 text-sky-400 flex-shrink-0" />
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

    <!-- OS Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Pilihan <span class="gradient-text">Operating System</span>
            </h2>
            <p class="text-slate-400 text-lg">
              Install OS favorit Anda dengan satu klik atau gunakan custom ISO.
            </p>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div 
              v-for="os in operatingSystems" 
              :key="os"
              class="glass-md p-4 text-center hover:border-sky-500/30 transition-colors cursor-pointer"
            >
              <Terminal class="w-6 h-6 text-sky-400 mx-auto mb-2" />
              <span class="text-sm text-white">{{ os }}</span>
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
          <div class="absolute inset-0 bg-gradient-to-r from-sky-500/10 via-cyan-500/10 to-violet-500/10" />
          
          <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Siap Memulai?
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
              Tim kami siap membantu Anda memilih VPS yang tepat. Hubungi kami untuk konsultasi gratis.
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
