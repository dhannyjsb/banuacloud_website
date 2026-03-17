<script setup lang="ts">
import { Server, Globe, Database, HardDrive, Code, MessageSquare } from 'lucide-vue-next';
import MServiceCard from '../molecules/MServiceCard.vue';

const services = [
  {
    icon: Server,
    title: 'Cloud VPS',
    description: 'Server virtual private dengan performa tinggi dengan sumber daya khusus dan akses root.',
    gradient: 'sky' as const,
    link: '/services/cloud-vps',
  },
  {
    icon: Globe,
    title: 'Web Hosting',
    description: 'Shared hosting cepat dan andal dengan cPanel dan penyimpanan SSD untuk website Anda.',
    gradient: 'cyan' as const,
    link: '/services/web-hosting',
  },
  {
    icon: Database,
    title: 'Layanan Domain',
    description: 'Daftar domain sempurna Anda dengan manajemen DNS gratis dan sertifikat SSL.',
    gradient: 'violet' as const,
    link: '/services/domain',
  },
  {
    icon: HardDrive,
    title: 'Solusi Backup',
    description: 'Backup otomatis harian dengan restore satu klik untuk menjaga data Anda tetap aman.',
    gradient: 'sky' as const,
    link: '/services/backup',
  },
  {
    icon: Code,
    title: 'Pengembangan Aplikasi',
    description: 'Pengembangan aplikasi web dan mobile dengan teknologi modern dan responsif.',
    gradient: 'violet' as const,
    link: '/services/app-development',
  },
  {
    icon: MessageSquare,
    title: 'Konsultasi IT',
    description: 'Konsultasi teknologi IT untuk transformasi digital dan optimalisasi bisnis Anda.',
    gradient: 'cyan' as const,
    link: '/services/it-consulting',
  },
];
</script>

<template>
  <section id="services" class="section-py relative bg-white/[0.02]">
    <div class="container-custom">
      <!-- Section Header -->
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
          <span class="text-sm text-sky-400 font-medium">Layanan Kami</span>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
          Solusi <span class="gradient-text">Handal</span>
        </h2>
        <p class="text-slate-400 text-lg">
          Layanan cloud komprehensif yang dirancang untuk memenuhi kebutuhan bisnis Anda di setiap tahap.
        </p>
      </div>
      
      <!-- Services Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
        <div
          v-for="(service, index) in services"
          :key="service.title"
          class="scroll-animate"
          :style="{ transitionDelay: `${index * 100}ms` }"
        >
          <MServiceCard
            :icon="service.icon"
            :title="service.title"
            :description="service.description"
            :gradient="service.gradient"
            :link="service.link"
          />
        </div>
      </div>
    </div>
  </section>
</template>
