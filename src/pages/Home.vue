<script setup lang="ts">
import { onMounted, nextTick } from 'vue';
import AGlowOrb from '../components/atoms/AGlowOrb.vue';
import ONavbar from '../components/organisms/ONavbar.vue';
import OHeroSection from '../components/organisms/OHeroSection.vue';
import OFeaturesSection from '../components/organisms/OFeaturesSection.vue';
import OServicesSection from '../components/organisms/OServicesSection.vue';
import OTestimonialsSection from '../components/organisms/OTestimonialsSection.vue';
import OFooter from '../components/organisms/OFooter.vue';
import { useScrollAnimation } from '../composables/useIntersectionObserver';

// Background orbs configuration
const orbs = [
  {
    id: 1,
    color: 'sky' as const,
    size: 400,
    position: { top: '10%', left: '-10%' },
    delay: 0,
    intensity: 'low' as const,
  },
  {
    id: 2,
    color: 'cyan' as const,
    size: 350,
    position: { top: '30%', right: '-5%' },
    delay: 2,
    intensity: 'low' as const,
  },
  {
    id: 3,
    color: 'violet' as const,
    size: 300,
    position: { bottom: '20%', left: '10%' },
    delay: 4,
    intensity: 'low' as const,
  },
  {
    id: 4,
    color: 'sky' as const,
    size: 250,
    position: { bottom: '10%', right: '15%' },
    delay: 6,
    intensity: 'low' as const,
  },
];

// Initialize scroll animations using the composable
const { initScrollAnimations } = useScrollAnimation();

onMounted(async () => {
  // Wait for child components to render
  await nextTick();
  initScrollAnimations();
});
</script>

<template>
  <div class="min-h-screen text-white overflow-x-hidden" :style="{ backgroundColor: 'var(--color-background, #0a0f1a)' }">
    <!-- Background Orbs -->
    <AGlowOrb v-for="orb in orbs" :key="orb.id" v-bind="orb" />
    
    <!-- Grid Background -->
    <div class="absolute inset-0 grid-bg pointer-events-none opacity-50" />
    
    <!-- Navigation -->
    <ONavbar />
    
    <!-- Main Content -->
    <main>
      <OHeroSection />
      <OFeaturesSection />
      <OServicesSection />
      <OTestimonialsSection />
    </main>
    
    <!-- Footer -->
    <OFooter />
  </div>
</template>
