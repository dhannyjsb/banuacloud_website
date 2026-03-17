<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { 
  Server, Globe, Database, HardDrive, Code, MessageSquare,
  CheckCircle, ArrowRight, Shield, Zap, Globe2, Clock, Users, Star,
  ChevronDown, ChevronUp, Mail, Phone
} from 'lucide-vue-next';
import ONavbar from '../components/organisms/ONavbar.vue';
import AButton from '../components/atoms/AButton.vue';
import AGradientText from '../components/atoms/AGradientText.vue';
import AGlowOrb from '../components/atoms/AGlowOrb.vue';

const services = [
  {
    id: 'cloud-vps',
    icon: Server,
    title: 'Cloud VPS',
    subtitle: 'Server Pribadi Virtual',
    description: 'Rasakan performa tingkat enterprise dengan server virtual kami yang dilengkapi dengan sumber daya dedicated dan akses root penuh. Cocok untuk aplikasi yang membutuhkan kontrol penuh atas lingkungan server.',
    features: [
      'Prosesor dedicated dengan performa tinggi',
      'Memori RAM yang terjamin',
      'Storage SSD NVMe ultra-cepat',
      'Akses root/kunci penuh',
      'Pilihan OS: Linux & Windows',
      'Backup otomatis harian',
    ],
    gradient: 'sky' as const,
  },
  {
    id: 'web-hosting',
    icon: Globe,
    title: 'Web Hosting',
    subtitle: 'Hosting Website',
    description: 'Hosting website cepat dan andal dengan panel kontrol cPanel dan penyimpanan SSD. Solusi sempurna untuk website personal, blog, hingga website bisnis skala kecil-menengah.',
    features: [
      'Panel kontrol cPanel mudah digunakan',
      'Penyimpanan SSD untuk kecepatan',
      'SSL gratis selamanya',
      'Email profesional unlimited',
      'Database MySQL/PostgreSQL',
      'Support PHP 8.x terbaru',
    ],
    gradient: 'cyan' as const,
  },
  {
    id: 'domain',
    icon: Database,
    title: 'Layanan Domain',
    subtitle: 'Registrasi Domain',
    description: 'Daftar domain sempurna untuk bisnis Anda dengan manajemen DNS gratis dan perlindungan privasi domain. Kami menyediakan berbagai ekstensi domain populer.',
    features: [
      '500+ ekstensi domain',
      'Manajemen DNS gratis',
      'SSL certificate gratis',
      'Privacy protection',
      'Auto-renewal',
      'DNS management panel',
    ],
    gradient: 'violet' as const,
  },
  {
    id: 'backup',
    icon: HardDrive,
    title: 'Solusi Backup',
    subtitle: 'Cadangan Data',
    description: 'Lindungi data bisnis Anda dengan backup otomatis harian dan restore dengan satu klik. Kami menggunakan penyimpanan redundan untuk keamanan ekstra.',
    features: [
      'Backup otomatis harian',
      'Penyimpanan off-site',
      'Restore dengan satu klik',
      'Retensi backup 30 hari',
      'Backup manual tambahan',
      'Enkripsi data end-to-end',
    ],
    gradient: 'sky' as const,
  },
  {
    id: 'app-development',
    icon: Code,
    title: 'Pengembangan Aplikasi',
    subtitle: 'App Development',
    description: 'Tim pengembang profesional kami siap membantu Anda membangun aplikasi web dan mobile yang responsif, modern, dan berkinerja tinggi menggunakan teknologi terkini.',
    features: [
      'Web application development',
      'Mobile app development (iOS & Android)',
      'UI/UX design modern',
      'API development & integration',
      'Maintenance & support',
      'Technologies: Vue, React, Node.js, Flutter',
    ],
    gradient: 'violet' as const,
  },
  {
    id: 'it-consulting',
    icon: MessageSquare,
    title: 'Konsultasi IT',
    subtitle: 'IT Consulting',
    description: 'Dapatkan panduan ahli untuk transformasi digital bisnis Anda. Kami membantu Anda merencanakan, mengimplementasikan, dan mengoptimalkan infrastruktur teknologi.',
    features: [
      'Konsultasi infrastruktur IT',
      'Transformasi digital',
      'Evaluasi keamanan sistem',
      'Optimasi performa',
      'Pelatihan tim IT',
      'Rencana IT strategis',
    ],
    gradient: 'cyan' as const,
  },
];

const faqs = [
  {
    question: 'Bagaimana cara memilih layanan yang tepat untuk bisnis saya?',
    answer: 'Tim sales kami siap membantu Anda memilih layanan yang sesuai dengan kebutuhan. Anda dapat menghubungi kami melalui live chat, email, atau telepon untuk berkonsultasi secara gratis.',
  },
  {
    question: 'Apakah saya bisa upgrade layanan di kemudian hari?',
    answer: 'Tentu! Semua layanan kami dapat di-upgrade kapan saja. Anda cukup mengajukan upgrade melalui panel klien kami, dan tim kami akan memprosesnya dalam waktu singkat.',
  },
  {
    question: 'Berapa lama waktu setup layanan?',
    answer: 'Cloud VPS dan dedicated server biasanya ready dalam 1-24 jam. Web hosting dan domain dapat langsung aktif dalam hitungan menit setelah pembayaran terkonfirmasi.',
  },
  {
    question: 'Apakah ada garansi uptime?',
    answer: 'Ya, kami memberikan garansi uptime 99.9% untuk semua layanan cloud VPS dan dedicated server. Jika uptime tidak terpenuhi, Anda berhak mendapatkan kompensasi.',
  },
  {
    question: 'Bagaimana sistem support Banua Cloud?',
    answer: 'Kami menyediakan support 24/7 melalui ticket system, live chat, dan telepon. Tim support kami terdiri dari teknisi berpengalaman yang siap membantu Anda kapan saja.',
  },
];

const openFaq = ref<number | null>(null);

const toggleFaq = (index: number) => {
  openFaq.value = openFaq.value === index ? null : index;
};

let scrollObserver: IntersectionObserver | null = null;

onMounted(() => {
  scrollObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    },
    {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
    }
  );

  const animatedElements = document.querySelectorAll('.scroll-animate');
  animatedElements.forEach((el) => {
    scrollObserver?.observe(el);
  });
});

onUnmounted(() => {
  if (scrollObserver) {
    scrollObserver.disconnect();
    scrollObserver = null;
  }
});
</script>

<template>
  <div class="min-h-screen bg-[#0a0f1a]">
    <!-- Background Orbs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb color="sky" :size="400" :position="{ top: '10%', left: '-10%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="350" :position="{ top: '30%', right: '-5%' }" :delay="2" intensity="low" />
      <AGlowOrb color="violet" :size="300" :position="{ bottom: '20%', left: '10%' }" :delay="4" intensity="low" />
    </div>

    <!-- Navbar -->
    <ONavbar />

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20">
      <div class="container-custom relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse" />
            <span class="text-sm text-slate-300">Pelajari Lebih Lanjut</span>
          </div>
          
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            Semua yang Anda butuhkan untuk
            <AGradientText>Bisnis Digital</AGradientText>
          </h1>
          
          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed animate-fade-in-up delay-200">
            Temukan layanan cloud dan IT terbaik yang dirancang untuk memenuhi kebutuhan bisnis Anda di era digital.
          </p>

          <!-- Stats -->
          <div class="grid grid-cols-3 gap-8 max-w-xl mx-auto animate-fade-in-up delay-300">
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-3">
                <Server class="w-6 h-6 text-sky-400" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-white">500+</div>
              <div class="text-sm text-slate-400">Server</div>
            </div>
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-3">
                <Clock class="w-6 h-6 text-sky-400" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-white">99.9%</div>
              <div class="text-sm text-slate-400">Uptime</div>
            </div>
            <div class="text-center">
              <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-3">
                <Users class="w-6 h-6 text-sky-400" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-white">10RB+</div>
              <div class="text-sm text-slate-400">Pelanggan</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Detail Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
            <span class="text-sm text-sky-400 font-medium">Layanan Kami</span>
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Solusi <span class="gradient-text">Komprehensif</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Dari hosting sederhana hingga pengembangan aplikasi enterprise, kami memiliki semuanya untuk Anda.
          </p>
        </div>

        <!-- Services Cards -->
        <div class="space-y-8">
          <div
            v-for="(service, index) in services"
            :key="service.id"
            class="scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <div class="glass-md rounded-2xl p-8 border border-white/5">
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Icon & Title -->
                <div>
                  <div 
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-6"
                    :class="{
                      'bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20': service.gradient === 'sky',
                      'bg-gradient-to-br from-cyan-500/20 to-teal-500/20 border border-cyan-500/20': service.gradient === 'cyan',
                      'bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/20': service.gradient === 'violet',
                    }"
                  >
                    <component :is="service.icon" class="w-8 h-8 text-sky-400" />
                  </div>
                  <h3 class="text-2xl font-bold text-white mb-2">{{ service.title }}</h3>
                  <p class="text-sky-400 font-medium mb-4">{{ service.subtitle }}</p>
                  <p class="text-slate-400 leading-relaxed mb-6">
                    {{ service.description }}
                  </p>
                  <ul class="space-y-3">
                    <li 
                      v-for="feature in service.features" 
                      :key="feature"
                      class="flex items-center gap-3 text-slate-300"
                    >
                      <CheckCircle class="w-5 h-5 text-sky-400 flex-shrink-0" />
                      <span>{{ feature }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Visual -->
                <div class="hidden lg:block">
                  <div 
                    class="aspect-square rounded-2xl p-8 relative overflow-hidden"
                    :class="{
                      'bg-gradient-to-br from-sky-500/10 to-cyan-500/10': service.gradient === 'sky',
                      'bg-gradient-to-br from-cyan-500/10 to-teal-500/10': service.gradient === 'cyan',
                      'bg-gradient-to-br from-violet-500/10 to-purple-500/10': service.gradient === 'violet',
                    }"
                  >
                    <div class="absolute inset-0 flex items-center justify-center">
                      <component :is="service.icon" class="w-32 h-32 text-white/10" />
                    </div>
                    <div class="relative z-10 mt-auto">
                      <AButton variant="primary" class="w-full justify-center">
                        Pesan Layanan
                        <ArrowRight class="ml-2 w-5 h-5" />
                      </AButton>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section-py relative bg-white/[0.02]">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
            <span class="text-sm text-sky-400 font-medium">Keunggulan</span>
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Mengapa <span class="gradient-text">Banua Cloud</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Kami berkomitmen memberikan layanan terbaik untuk kesuksesan bisnis Anda.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="glass-md rounded-xl p-6 text-center scroll-animate">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-4">
              <Zap class="w-7 h-7 text-sky-400" />
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Super Cepat</h3>
            <p class="text-slate-400 text-sm">
              Infrastruktur performa tinggi dengan CDN global untuk kecepatan optimal.
            </p>
          </div>
          <div class="glass-md rounded-xl p-6 text-center scroll-animate" style="transition-delay: 100ms">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-4">
              <Shield class="w-7 h-7 text-sky-400" />
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Aman Terjamin</h3>
            <p class="text-slate-400 text-sm">
              Enkripsi tingkat bank dan proteksi DDoS enterprise untuk keamanan data Anda.
            </p>
          </div>
          <div class="glass-md rounded-xl p-6 text-center scroll-animate" style="transition-delay: 200ms">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-4">
              <Globe2 class="w-7 h-7 text-sky-400" />
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Global Network</h3>
            <p class="text-slate-400 text-sm">
              15+ data center di seluruh dunia dengan latensi rendah untuk pengguna global.
            </p>
          </div>
          <div class="glass-md rounded-xl p-6 text-center scroll-animate" style="transition-delay: 300ms">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-4">
              <Star class="w-7 h-7 text-sky-400" />
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Support 24/7</h3>
            <p class="text-slate-400 text-sm">
              Tim support profesional siap membantu Anda kapan saja, tanpa pandang waktu.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
            <span class="text-sm text-sky-400 font-medium">FAQ</span>
          </div>
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Pertanyaan <span class="gradient-text">Umum</span>
          </h2>
          <p class="text-slate-400 text-lg">
            Temukan jawaban untuk pertanyaan yang sering diajukan tentang layanan kami.
          </p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4">
          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="glass-md rounded-xl overflow-hidden scroll-animate"
            :style="{ transitionDelay: `${index * 100}ms` }"
          >
            <button
              class="w-full flex items-center justify-between p-6 text-left"
              @click="toggleFaq(index)"
            >
              <span class="text-white font-medium pr-4">{{ faq.question }}</span>
              <ChevronDown 
                v-if="openFaq !== index" 
                class="w-5 h-5 text-sky-400 flex-shrink-0" 
              />
              <ChevronUp 
                v-else 
                class="w-5 h-5 text-sky-400 flex-shrink-0" 
              />
            </button>
            <div 
              v-if="openFaq === index"
              class="px-6 pb-6"
            >
              <p class="text-slate-400 leading-relaxed">
                {{ faq.answer }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py relative">
      <div class="container-custom">
        <div class="glass-md rounded-2xl p-12 text-center border border-sky-500/20 bg-gradient-to-br from-sky-500/10 to-cyan-500/10">
          <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Siap Memulai?
          </h2>
          <p class="text-slate-400 text-lg mb-8 max-w-2xl mx-auto">
            Hubungi tim sales kami sekarang untuk konsultasi gratis dan temukan solusi terbaik untuk bisnis Anda.
          </p>
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <AButton variant="primary" size="lg">
              Hubungi Sales
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              Lihat Harga
            </AButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="relative bg-white/[0.02] border-t border-white/5">
      <div class="container-custom section-py">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center">
              <Server class="w-4 h-4 text-white" />
            </div>
            <span class="text-white font-bold">Banua<span class="text-sky-400">Cloud</span></span>
          </div>
          <div class="flex items-center gap-6 text-slate-400 text-sm">
            <a href="mailto:support@banuacloud.id" class="flex items-center gap-2 hover:text-sky-400 transition-colors">
              <Mail class="w-4 h-4" />
              support@banuacloud.id
            </a>
            <a href="tel:+6281234567890" class="flex items-center gap-2 hover:text-sky-400 transition-colors">
              <Phone class="w-4 h-4" />
              +62 812-3456-7890
            </a>
          </div>
        </div>
        <div class="border-t border-white/5 mt-8 pt-8 text-center text-slate-500 text-sm">
          &copy; 2025 Banua Cloud Nusantara. Semua hak dilindungi.
        </div>
      </div>
    </footer>
  </div>
</template>
