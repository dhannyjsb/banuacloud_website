<script setup lang="ts">
interface Props {
  quote: string;
  name: string;
  role: string;
  avatar?: string;
}

defineProps<Props>();
</script>

<template>
  <div class="glass-md group relative flex h-full flex-col overflow-hidden p-6 md:p-8">
    <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/55 to-transparent" />

    <div class="mb-6 inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-sky-400/20 bg-sky-500/10 text-sky-300">
      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
      </svg>
    </div>

    <p class="mb-8 flex-1 text-base leading-8 text-slate-300 md:text-lg">
      {{ quote }}
    </p>

    <div class="flex items-center gap-4 border-t border-white/8 pt-5">
      <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-lg font-bold text-white shadow-[0_14px_30px_rgba(14,165,233,0.28)]">
        <img v-if="avatar" :src="avatar" :alt="name" class="w-full h-full rounded-full object-cover" />
        <span v-else>{{ name.charAt(0) }}</span>
      </div>

      <div>
        <h4 class="text-white font-semibold">{{ name }}</h4>
        <p class="text-sm text-slate-400">{{ role }}</p>
      </div>
    </div>
  </div>
</template>
