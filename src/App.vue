<script setup lang="ts">
import { computed } from 'vue';
import Maintenance from './pages/Maintenance.vue';

const isMaintenanceMode = computed(() => {
  const val = import.meta.env.VITE_MAINTENANCE_MODE || 'true';
  return val === 'true' || val === '1';
});
</script>

<template>
  <Maintenance v-if="isMaintenanceMode" />
  <router-view v-else />
</template>
