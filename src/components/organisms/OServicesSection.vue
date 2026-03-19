<script setup lang="ts">
import { computed } from 'vue';
import MServiceCard from '../molecules/MServiceCard.vue';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';
import { resolveServiceIcon } from '../../lib/iconMaps';

const { services } = useSiteBootstrap();

const serviceCards = computed(() => {
  return services.value
    .filter((service) => service.enabled)
    .map((service) => ({
      ...service,
      iconComponent: resolveServiceIcon(service.icon),
      gradient: (service.plans[0]?.color || 'sky') as 'sky' | 'cyan' | 'violet',
      link: `/services/${service.slug}`,
    }));
});
</script>

<template>
  <section id="services" class="section-py relative bg-white/[0.02]">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(6,182,212,0.08),transparent_45%)]" />

    <div class="container-custom relative z-10">
      <!-- Section Header -->
      <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-400/15 bg-cyan-500/10 px-4 py-2">
          <span class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">Layanan Kami</span>
        </div>
        <h2 class="mb-5 text-3xl md:text-5xl font-bold text-white">
          Solusi <span class="gradient-text">handal</span> untuk tumbuh lebih cepat
        </h2>
        <p class="text-lg leading-8 text-slate-400">
          Layanan cloud komprehensif yang dirancang untuk memenuhi kebutuhan bisnis Anda di setiap tahap.
        </p>
      </div>

      <!-- Services Grid -->
      <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        <div
          v-for="(service, index) in serviceCards"
          :key="service.id"
          class="scroll-animate"
          :style="{ transitionDelay: `${index * 100}ms` }"
        >
          <MServiceCard
            :icon="service.iconComponent"
            :title="service.name"
            :description="service.description"
            :gradient="service.gradient"
            :link="service.link"
          />
        </div>
      </div>
    </div>
  </section>
</template>
