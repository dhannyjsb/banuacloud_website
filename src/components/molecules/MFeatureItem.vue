<script setup lang="ts">
import { type Component } from 'vue';
import ACard from '../atoms/ACard.vue';

interface Props {
  icon: Component;
  title: string;
  description: string;
}

defineProps<Props>();
</script>

<template>
  <ACard hover padding="lg" class="group relative h-full overflow-hidden">
    <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/60 to-transparent" />

    <div class="relative z-10 flex h-full flex-col">
      <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-sky-400/20 bg-gradient-to-br from-sky-500/20 to-cyan-500/20 shadow-[0_16px_36px_rgba(14,165,233,0.12)]">
        <component :is="icon" class="w-6 h-6 text-sky-300" />
      </div>

      <h3 class="mb-3 text-xl font-semibold text-white transition-colors duration-300 group-hover:text-sky-200">
        {{ title }}
      </h3>

      <p class="text-sm leading-7 text-slate-400">
        {{ description }}
      </p>

      <div class="mt-8 h-px w-full bg-gradient-to-r from-sky-500/30 via-cyan-400/10 to-transparent" />
    </div>
  </ACard>
</template>
