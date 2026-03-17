<script setup lang="ts">
import { Shield, Zap, Globe, Database, Lock, Cpu } from 'lucide-vue-next';
import MFeatureItem from '../molecules/MFeatureItem.vue';

const features = [
  {
    icon: Zap,
    title: 'Super Cepat',
    description: 'Deploy aplikasi Anda dalam hitungan detik dengan infrastruktur berperforma tinggi dan jaringan CDN global kami.',
  },
  {
    icon: Shield,
    title: 'Keamanan Enterprise',
    description: 'Enkripsi tingkat bank dan sertifikasi kepatuhan untuk menjaga data Anda tetap aman setiap saat.',
  },
  {
    icon: Globe,
    title: 'Jaringan Global',
    description: 'Deploy di 15+ wilayah di seluruh dunia dengan koneksi latensi rendah untuk pengguna Anda.',
  },
  {
    icon: Database,
    title: 'Backup Otomatis',
    description: 'Backup otomatis harian dengan restore satu klik memastikan data Anda selalu aman.',
  },
  {
    icon: Lock,
    title: 'Perlindungan DDoS',
    description: 'Mitigasi DDoS tingkat enterprise untuk menjaga layanan Anda tetap berjalan saat serangan.',
  },
  {
    icon: Cpu,
    title: 'Auto Scaling',
    description: 'Skalakan sumber daya secara otomatis berdasarkan pola traffic untuk performa optimal.',
  },
];
</script>

<template>
  <section class="section-py relative">
    <div class="container-custom">
      <!-- Section Header -->
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
          <span class="text-sm text-sky-400 font-medium">Mengapa Memilih Kami</span>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
          Dibangun untuk <span class="gradient-text">Performa</span>
        </h2>
        <p class="text-slate-400 text-lg">
          Semuanya yang Anda butuhkan untuk membangun, mendeploy, dan menskalakan aplikasi dengan percaya diri.
        </p>
      </div>
      
      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(feature, index) in features"
          :key="feature.title"
          class="scroll-animate"
          :style="{ transitionDelay: `${index * 100}ms` }"
        >
          <MFeatureItem
            :icon="feature.icon"
            :title="feature.title"
            :description="feature.description"
          />
        </div>
      </div>
    </div>
  </section>
</template>
