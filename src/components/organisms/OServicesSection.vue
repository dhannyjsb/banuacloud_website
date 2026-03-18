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
