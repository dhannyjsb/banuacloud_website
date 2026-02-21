<script setup lang="ts">
import { computed } from 'vue';
import Maintenance from './pages/Maintenance.vue';
import Home from './pages/Home.vue';

const isMaintenanceMode = computed(() => {
  const val = import.meta.env.VITE_MAINTENANCE_MODE || 'true';
  return val === 'true' || val === '1';
});
</script>

<template>
  <Maintenance v-if="isMaintenanceMode" />
  <Home v-else />
</template>
