<script setup lang="ts">
import { type Component } from 'vue';

interface Props {
  icon: Component;
  title: string;
  description: string;
}

defineProps<Props>();
</script>

<template>
  <div class="flex gap-4">
    <!-- Icon -->
    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 flex items-center justify-center">
      <component :is="icon" class="w-6 h-6 text-sky-400" />
    </div>
    
    <!-- Content -->
    <div>
      <h3 class="text-lg font-semibold text-white mb-2">
        {{ title }}
      </h3>
      <p class="text-slate-400 text-sm leading-relaxed">
        {{ description }}
      </p>
    </div>
  </div>
</template>
