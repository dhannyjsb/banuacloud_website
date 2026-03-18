<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import {
  MessageSquare, CheckCircle, ArrowRight, Shield, TrendingUp, Users,
  HeadphonesIcon, Briefcase, BarChart3, GraduationCap
} from 'lucide-vue-next';
import ONavbar from '../../components/organisms/ONavbar.vue';
import OFooter from '../../components/organisms/OFooter.vue';
import AButton from '../../components/atoms/AButton.vue';
import AGradientText from '../../components/atoms/AGradientText.vue';
import AGlowOrb from '../../components/atoms/AGlowOrb.vue';
import { useScrollAnimation } from '../../composables/useIntersectionObserver';

const { initScrollAnimations } = useScrollAnimation();

const services = [
  {
    icon: Briefcase,
    title: 'Konsultasi Infrastruktur',
    description: 'Evaluasi dan optimalisasi infrastruktur IT Anda untuk performa maksimal.',
  },
  {
    icon: TrendingUp,
    title: 'Transformasi Digital',
    description: 'Panduan lengkap untuk transforms bisnis di era digital.',
  },
  {
    icon: Shield,
    title: 'Keamanan Siber',
    description: 'Evaluasi keamanan dan implementasi solusi perlindungan data.',
  },
  {
    icon: BarChart3,
    title: 'Optimasi Performa',
    description: 'Analisis dan peningkatan performa sistem Anda.',
  },
  {
    icon: GraduationCap,
    title: 'Pelatihan Tim IT',
    description: 'Program pelatihan untuk meningkatkan kapasitas tim teknologi Anda.',
  },
  {
    icon: Users,
    title: 'Rencana IT Strategis',
    description: 'Penyusunan roadmap IT yang aligned dengan tujuan bisnis.',
  },
];

const benefits = [
  'Tim berpengalaman dengan sertifikasi internasional',
  'Pendekatan praktis dan hasil terukur',
  'Konsultasi offline dan online',
  'Laporan detail dan rekomendasi actionable',
  'Follow-up support setelah konsultasi',
  'Harga transparan tanpa biaya tersembunyi',
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
      <AGlowOrb color="violet" :size="400" :position="{ top: '40%', left: '-10%' }" :delay="3" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Hero Section -->
    <section class="relative pt-28 pb-16">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <MessageSquare class="w-4 h-4 text-cyan-400" />
            <span class="text-sm text-slate-300">IT Consulting</span>
          </div>

          <!-- Title -->
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Strategi IT untuk
            <AGradientText>Bisnis Lebih Baik</AGradientText>
          </h1>

          <!-- Description -->
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            Dapatkan panduan ahli untuk transformasi digital bisnis Anda.
            Kami membantu Anda merencanakan, mengimplementasikan, dan mengoptimalkan infrastruktur teknologi.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-300">
            <AButton variant="primary" size="lg">
              Jadwalkan Konsultasi
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              Pelajari Lebih Lanjut
            </AButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section class="section-py relative bg-white/[0.02]">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Layanan <span class="gradient-text">Konsultasi</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Solusi komprehensif untuk semua kebutuhan IT bisnis Anda.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(service, index) in services"
            :key="service.title"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md p-6 h-full hover:border-cyan-500/30 transition-colors">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-teal-500/20 border border-cyan-500/20 flex items-center justify-center mb-4">
                <component :is="service.icon" class="w-6 h-6 text-cyan-400" />
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">{{ service.title }}</h3>
              <p class="text-slate-400 text-sm leading-relaxed">
                {{ service.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="max-w-4xl mx-auto">
          <div class="glass-md p-8 md:p-12">
            <div class="text-center mb-8">
              <h2 class="text-3xl font-bold text-white mb-4">
                Mengapa Memilih <span class="gradient-text">Banua Cloud</span>?
              </h2>
              <p class="text-slate-400">
                Keuntungan bekerja sama dengan tim konsultan kami.
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="(benefit, index) in benefits"
                :key="benefit"
                class="flex items-center gap-3"
              >
                <CheckCircle class="w-5 h-5 text-cyan-400 flex-shrink-0" />
                <span class="text-slate-300">{{ benefit }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="glass-md p-12 md:p-16 text-center max-w-4xl mx-auto relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 via-sky-500/10 to-violet-500/10" />

          <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Siap Meningkatkan Bisnis Anda?
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
              Jadwalkan konsultasi gratis untuk mendiskusikan kebutuhan IT bisnis Anda.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <AButton variant="primary" size="lg">
                Jadwalkan Konsultasi
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
