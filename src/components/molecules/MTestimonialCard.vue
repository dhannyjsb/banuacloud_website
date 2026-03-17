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
  <div class="glass-md p-6 md:p-8">
    <!-- Quote icon -->
    <div class="mb-6">
      <svg class="w-10 h-10 text-sky-400/30" fill="currentColor" viewBox="0 0 24 24">
        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
      </svg>
    </div>
    
    <!-- Quote text -->
    <p class="text-slate-300 leading-relaxed mb-8 text-lg">
      {{ quote }}
    </p>
    
    <!-- Author -->
    <div class="flex items-center gap-4">
      <!-- Avatar -->
      <div class="w-12 h-12 rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center text-white font-bold text-lg">
        <img v-if="avatar" :src="avatar" :alt="name" class="w-full h-full rounded-full object-cover" />
        <span v-else>{{ name.charAt(0) }}</span>
      </div>
      
      <!-- Name and role -->
      <div>
        <h4 class="text-white font-semibold">{{ name }}</h4>
        <p class="text-slate-400 text-sm">{{ role }}</p>
      </div>
    </div>
  </div>
</template>
