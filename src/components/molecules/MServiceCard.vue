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
    <ACard hover padding="lg" class="group relative flex h-full overflow-hidden">
      <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/55 to-transparent" />

      <div class="relative z-10 flex h-full w-full flex-col">
        <div class="mb-8 flex items-start justify-between gap-4">
          <div class="relative">
            <div
              class="relative z-10 flex h-16 w-16 items-center justify-center rounded-[1.4rem] bg-gradient-to-br shadow-[0_18px_36px_rgba(14,165,233,0.16)] transition-transform duration-300 group-hover:scale-105"
              :class="gradientClasses"
            >
              <component :is="icon" class="h-7 w-7 text-white" />
            </div>
            <div
              class="absolute inset-0 rounded-[1.4rem] blur-xl opacity-45 transition-opacity duration-300 group-hover:opacity-75"
              :class="gradientBgClasses[gradient]"
            />
          </div>

          <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">
            Terkelola
          </span>
        </div>

        <h3 class="mb-3 text-2xl font-bold text-white transition-colors duration-300 group-hover:text-sky-200">
          {{ title }}
        </h3>

        <p class="mb-8 leading-7 text-slate-400">
          {{ description }}
        </p>

        <div class="mt-auto flex items-center justify-between border-t border-white/8 pt-5 text-sky-300">
          <span class="text-sm font-semibold">Lihat Layanan</span>
          <svg class="h-4 w-4 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </div>
      </div>
    </ACard>
  </div>
</template>
