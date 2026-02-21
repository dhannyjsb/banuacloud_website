<script setup lang="ts">
const companyName = import.meta.env.VITE_COMPANY_NAME || 'Banua Cloud';
</script>

<template>
  <div class="h-screen bg-[#0a0f1a] flex items-center justify-center">
    <div class="text-center">
      <h1 class="text-3xl font-bold text-white mb-4">{{ companyName }}</h1>
      <p class="text-gray-400">Website sedang dalam pengembangan.</p>
    </div>
  </div>
</template>
