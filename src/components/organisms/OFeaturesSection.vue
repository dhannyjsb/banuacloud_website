<script setup lang="ts">
import MFeatureItem from '../molecules/MFeatureItem.vue';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';
import { resolveFeatureIcon } from '../../lib/iconMaps';

const { features } = useSiteBootstrap();
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
          :key="feature.id"
          class="scroll-animate"
          :style="{ transitionDelay: `${index * 100}ms` }"
        >
          <MFeatureItem
            :icon="resolveFeatureIcon(feature.icon)"
            :title="feature.title"
            :description="feature.description"
          />
        </div>
      </div>
    </div>
  </section>
</template>
