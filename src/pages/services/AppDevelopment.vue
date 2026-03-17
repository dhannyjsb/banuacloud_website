<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { 
  Code, CheckCircle, ArrowRight, Palette, Smartphone, Globe, 
  ArrowLeft, ChevronRight, HeadphonesIcon, Layout, Server, Lightbulb
} from 'lucide-vue-next';
import ONavbar from '../../components/organisms/ONavbar.vue';
import OFooter from '../../components/organisms/OFooter.vue';
import AButton from '../../components/atoms/AButton.vue';
import AGradientText from '../../components/atoms/AGradientText.vue';
import AGlowOrb from '../../components/atoms/AGlowOrb.vue';
import { useScrollAnimation } from '../../composables/useIntersectionObserver';

const router = useRouter();
const { initScrollAnimations } = useScrollAnimation();

const services = [
  {
    icon: Globe,
    title: 'Web Application',
    description: 'Aplikasi web responsif dan modern dengan teknologi terkini.',
    technologies: ['Vue.js', 'React', 'Next.js', 'Laravel', 'Node.js'],
  },
  {
    icon: Smartphone,
    title: 'Mobile App',
    description: 'Aplikasi mobile untuk iOS dan Android dengan performa optimal.',
    technologies: ['React Native', 'Flutter', 'Swift', 'Kotlin'],
  },
  {
    icon: Palette,
    title: 'UI/UX Design',
    description: 'Desain antarmuka yang menarik dan user-friendly.',
    technologies: ['Figma', 'Adobe XD', 'Sketch', 'Prototyping'],
  },
  {
    icon: Server,
    title: 'API Development',
    description: 'API yang robust dan secure untuk integrations.',
    technologies: ['REST API', 'GraphQL', 'Node.js', 'Python'],
  },
];

const process = [
  {
    step: '01',
    title: 'Konsultasi',
    description: 'Kami mendengarkan kebutuhan dan tujuan bisnis Anda.'
  },
  {
    step: '02',
    title: 'Perencanaan',
    description: 'Tim kami membuat roadmap dan timeline pengembangan.'
  },
  {
    step: '03',
    title: 'Development',
    description: 'Pengembangan aplikasi dengan metodologi agile.'
  },
  {
    step: '04',
    title: 'Testing',
    description: 'Quality assurance untuk memastikan kualitas produk.'
  },
  {
    step: '05',
    title: 'Launch',
    description: 'Deploy aplikasi dan pelatihan pengguna.'
  },
  {
    step: '06',
    title: 'Maintenance',
    description: 'Dukungan lanjutan dan pembaruan berkala.'
  },
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
      <AGlowOrb color="violet" :size="500" :position="{ top: '5%', left: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="sky" :size="400" :position="{ top: '40%', right: '-10%' }" :delay="3" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Breadcrumb -->
    <div class="container-custom pt-24 pb-4 relative z-10">
      <nav class="flex items-center gap-2 text-sm text-slate-400">
        <button @click="goBack" class="hover:text-violet-400 transition-colors flex items-center gap-1">
          <ArrowLeft class="w-4 h-4" />
          Semua Layanan
        </button>
        <ChevronRight class="w-4 h-4" />
        <span class="text-white">Pengembangan Aplikasi</span>
      </nav>
    </div>

    <!-- Hero Section -->
    <section class="relative pt-8 pb-16">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <Code class="w-4 h-4 text-violet-400" />
            <span class="text-sm text-slate-300">App Development</span>
          </div>
          
          <!-- Title -->
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Wujudkan Aplikasi
            <AGradientText>Impian Anda</AGradientText>
          </h1>
          
          <!-- Description -->
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            Tim pengembang profesional siap membangun aplikasi web dan mobile 
            yang responsif, modern, dan berkinerja tinggi.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-300">
            <AButton variant="primary" size="lg">
              Konsultasi Gratis
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              Lihat Portfolio
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
            Layanan <span class="gradient-text">Pengembangan</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Solusi lengkap untuk semua kebutuhan pengembangan aplikasi Anda.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="(service, index) in services" 
            :key="service.title"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md p-8 h-full hover:border-violet-500/30 transition-colors">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/20 flex items-center justify-center flex-shrink-0">
                  <component :is="service.icon" class="w-7 h-7 text-violet-400" />
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-white mb-2">{{ service.title }}</h3>
                  <p class="text-slate-400 text-sm mb-4">{{ service.description }}</p>
                  <div class="flex flex-wrap gap-2">
                    <span 
                      v-for="tech in service.technologies" 
                      :key="tech"
                      class="px-3 py-1 rounded-full text-xs bg-violet-500/10 text-violet-300 border border-violet-500/20"
                    >
                      {{ tech }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Process Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Proses <span class="gradient-text">Pengembangan</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Metodologi agile untuk hasil yang optimal dan tepat waktu.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="(step, index) in process" 
            :key="step.step"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md p-6 h-full">
              <div class="text-4xl font-bold text-violet-500/30 mb-4">{{ step.step }}</div>
              <h3 class="text-lg font-semibold text-white mb-2">{{ step.title }}</h3>
              <p class="text-slate-400 text-sm">
                {{ step.description }}
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
          <div class="absolute inset-0 bg-gradient-to-r from-violet-500/10 via-sky-500/10 to-cyan-500/10" />
          
          <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              Mulai Proyek Anda
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
              Konsultasi gratis untuk mendiskusikan kebutuhan aplikasi Anda.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <AButton variant="primary" size="lg">
                Konsultasi Gratis
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
