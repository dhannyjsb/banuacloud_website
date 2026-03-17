<script setup lang="ts">
import { type Component } from 'vue';
import { useRouter } from 'vue-router';
import ACard from '../atoms/ACard.vue';

interface Props {
  icon: Component;
  title: string;
  description: string;
  gradient?: 'sky' | 'cyan' | 'violet';
  link?: string;
}

const props = withDefaults(defineProps<Props>(), {
  gradient: 'sky',
  link: '',
});

const router = useRouter();

const gradientBgClasses = {
  sky: 'bg-sky-500',
  cyan: 'bg-cyan-500',
  violet: 'bg-violet-500',
};

const gradientClasses = {
  sky: 'from-sky-500 to-cyan-500',
  cyan: 'from-cyan-500 to-sky-500',
  violet: 'from-violet-500 to-sky-500',
};

const handleClick = () => {
  if (props.link) {
    router.push(props.link);
  }
};
</script>

<template>
  <div @click="handleClick" :class="{ 'cursor-pointer': link }">
    <ACard hover padding="lg" class="group h-full">
      <!-- Icon container with gradient background -->
      <div class="relative mb-6">
        <div 
          class="w-14 h-14 rounded-xl bg-gradient-to-br flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
          :class="gradientClasses"
        >
          <component :is="icon" class="w-7 h-7 text-white" />
        </div>
        <!-- Glow effect -->
        <div 
          class="absolute inset-0 rounded-xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-300"
          :class="gradientBgClasses[gradient]"
        />
      </div>
      
      <!-- Title -->
      <h3 class="text-xl font-bold text-white mb-3 group-hover:text-sky-400 transition-colors duration-300">
        {{ title }}
      </h3>
      
      <!-- Description -->
      <p class="text-slate-400 leading-relaxed">
        {{ description }}
      </p>
      
      <!-- Learn more link -->
      <div class="mt-4 flex items-center text-sky-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <span class="text-sm font-medium">Learn more</span>
        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </div>
    </ACard>
  </div>
</template>
