<script setup lang="ts">
import { useRouter } from 'vue-router';
import { ArrowRight, Server, Clock, Users } from 'lucide-vue-next';
import AButton from '../atoms/AButton.vue';
import AGradientText from '../atoms/AGradientText.vue';

const router = useRouter();

const stats = [
  { icon: Server, value: '500+', label: 'Server' },
  { icon: Clock, value: '99.9%', label: 'Uptime' },
  { icon: Users, value: '10RB+', label: 'Pelanggan' },
];

const goToLearnMore = () => {
  router.push('/learn-more');
};
</script>

<template>
  <section id="home" class="relative min-h-screen flex items-center pt-20 overflow-hidden">
    <!-- Background gradient -->
    <div class="absolute inset-0 bg-gradient-to-b from-sky-500/5 via-transparent to-transparent" />
    
    <div class="container-custom relative z-10">
      <div class="max-w-4xl mx-auto text-center">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
          <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse" />
          <span class="text-sm text-slate-300">Dipercaya oleh 10.000+ pelanggan</span>
        </div>
        
        <!-- Headline -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
          Solusi Cloud untuk
          <AGradientText>Bisnis Modern</AGradientText>
        </h1>
        
        <!-- Subheadline -->
        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed animate-fade-in-up delay-200">
          Rasakan performa super cepat dengan infrastruktur cloud tingkat enterprise kami. 
          Solusi yang skalabel, aman, dan terpercaya disesuaikan dengan kebutuhan bisnis Anda.
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16 animate-fade-in-up delay-300">
          <AButton variant="primary" size="lg" @click="goToLearnMore">
            Mulai Sekarang
            <ArrowRight class="ml-2 w-5 h-5" />
          </AButton>
          <AButton variant="secondary" size="lg">
            Lihat Harga
          </AButton>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-3 gap-8 max-w-xl mx-auto animate-fade-in-up delay-400">
          <div 
            v-for="(stat, index) in stats" 
            :key="stat.label"
            class="text-center"
          >
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-3">
              <component :is="stat.icon" class="w-6 h-6 text-sky-400" />
            </div>
            <div class="text-2xl md:text-3xl font-bold text-white">{{ stat.value }}</div>
            <div class="text-sm text-slate-400">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
      <div class="w-6 h-10 rounded-full border-2 border-white/20 flex items-start justify-center p-2">
        <div class="w-1.5 h-3 bg-white/50 rounded-full animate-particle" />
      </div>
    </div>
  </section>
</template>
