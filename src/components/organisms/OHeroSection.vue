<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, Server, Clock, Users } from 'lucide-vue-next';
import AButton from '../atoms/AButton.vue';
import AGradientText from '../atoms/AGradientText.vue';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const router = useRouter();
const { heroContent } = useSiteBootstrap();

const stats = [
  { icon: Server, value: '500+', label: 'Server' },
  { icon: Clock, value: '99.9%', label: 'Uptime' },
  { icon: Users, value: '10RB+', label: 'Pelanggan' },
];

const goToLearnMore = () => {
  router.push('/learn-more');
};

const goToServices = () => {
  document.getElementById('services')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

const headlineParts = computed(() => {
  const words = heroContent.value.title.trim().split(/\s+/).filter(Boolean);

  if (words.length <= 2) {
    return {
      leading: '',
      highlighted: heroContent.value.title,
    };
  }

  return {
    leading: words.slice(0, -2).join(' '),
    highlighted: words.slice(-2).join(' '),
  };
});
</script>

<template>
  <section id="home" class="relative overflow-hidden pt-28 pb-12 md:pt-36 md:pb-16">
    <!-- Background gradient -->
    <div class="absolute inset-0 bg-gradient-to-b from-sky-500/5 via-transparent to-transparent" />
    <div class="absolute inset-x-0 top-0 h-[480px] bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.16),transparent_60%)]" />

    <div class="container-custom relative z-10">
      <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.06),rgba(255,255,255,0.02))] px-6 py-12 text-center shadow-[0_30px_110px_rgba(2,12,27,0.38)] md:px-12 md:py-14">
        <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/60 to-transparent" />
        <div class="absolute -right-12 top-10 h-40 w-40 rounded-full bg-sky-500/10 blur-3xl" />
        <div class="absolute -left-10 bottom-8 h-32 w-32 rounded-full bg-cyan-500/10 blur-3xl" />

        <div class="relative z-10 max-w-4xl mx-auto">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 rounded-full border border-sky-400/20 bg-sky-500/10 px-4 py-2 text-sm text-sky-100 shadow-[0_0_0_1px_rgba(125,211,252,0.08)] animate-fade-in-up">
          <span class="h-2 w-2 rounded-full bg-sky-300 animate-pulse" />
          <span>Dipercaya oleh 10.000+ pelanggan di Indonesia</span>
        </div>

        <!-- Headline -->
        <h1 class="font-display text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-[0.95] mb-6 animate-fade-in-up delay-100">
          <span v-if="headlineParts.leading" class="mr-3 inline-block">
            {{ headlineParts.leading }}
          </span>
          <AGradientText>{{ headlineParts.highlighted }}</AGradientText>
        </h1>

        <!-- Subheadline -->
        <p class="mx-auto mb-10 max-w-2xl text-base leading-8 text-slate-300 md:text-xl animate-fade-in-up delay-200">
          {{ heroContent.subtitle }}
        </p>

        <!-- CTA Buttons -->
        <div class="mb-12 flex flex-col items-center justify-center gap-4 sm:flex-row animate-fade-in-up delay-300">
          <AButton variant="primary" size="lg" @click="goToLearnMore">
            {{ heroContent.ctaPrimary }}
            <ArrowRight class="ml-2 w-5 h-5" />
          </AButton>
          <AButton variant="secondary" size="lg" @click="goToServices">
            {{ heroContent.ctaSecondary }}
          </AButton>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-5 max-w-4xl mx-auto animate-fade-in-up delay-400">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="rounded-[1.6rem] border border-white/10 bg-[#0b1628]/65 px-5 py-5 text-center shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]"
          >
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-sky-400/20 bg-gradient-to-br from-sky-500/20 to-cyan-500/20">
              <component :is="stat.icon" class="w-6 h-6 text-sky-400" />
            </div>
            <div class="text-3xl font-bold text-white">{{ stat.value }}</div>
            <div class="mt-1 text-sm text-slate-400">{{ stat.label }}</div>
          </div>
        </div>
        </div>
      </div>
    </div>

  </section>
</template>
