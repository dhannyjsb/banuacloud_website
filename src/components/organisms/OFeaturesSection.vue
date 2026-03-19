<script setup lang="ts">
import MFeatureItem from '../molecules/MFeatureItem.vue';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';
import { resolveFeatureIcon } from '../../lib/iconMaps';

const { features } = useSiteBootstrap();
</script>

<template>
  <section id="advantages" class="section-py relative">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(56,189,248,0.08),transparent_40%)]" />

    <div class="container-custom relative z-10">
      <!-- Section Header -->
      <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-sky-400/15 bg-sky-500/10 px-4 py-2">
          <span class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-300">Mengapa Memilih Kami</span>
        </div>
        <h2 class="mb-5 text-3xl md:text-5xl font-bold text-white">
          Dibangun untuk <span class="gradient-text">performa nyata</span>
        </h2>
        <p class="text-lg leading-8 text-slate-400">
          Semuanya yang Anda butuhkan untuk membangun, mendeploy, dan menskalakan aplikasi dengan percaya diri.
        </p>
      </div>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
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
