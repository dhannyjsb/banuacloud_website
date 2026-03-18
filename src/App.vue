<script setup lang="ts">
import Maintenance from './pages/Maintenance.vue';
import { useSiteSettings } from './composables/useSiteSettings';

const { maintenanceMode } = useSiteSettings();
</script>

<template>
  <Maintenance v-if="maintenanceMode" />
  <router-view v-else />
</template>
