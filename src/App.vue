<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Maintenance from './pages/Maintenance.vue';
import { useSiteSettings } from './composables/useSiteSettings';

const route = useRoute();
const { maintenanceMode } = useSiteSettings();

const canBypassMaintenance = computed(() => {
  return route.path.startsWith('/admin');
});

const shouldShowMaintenance = computed(() => {
  return maintenanceMode.value && !canBypassMaintenance.value;
});
</script>

<template>
  <Maintenance v-if="shouldShowMaintenance" />
  <router-view v-else />
</template>
