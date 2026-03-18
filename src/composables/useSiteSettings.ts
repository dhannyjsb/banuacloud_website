import { computed } from 'vue';
import { emitSiteDataUpdated, useSiteBootstrap } from './useSiteBootstrap';

export function emitSiteSettingsUpdated() {
  emitSiteDataUpdated();
}

export function useSiteSettings() {
  const { siteSettings, refreshSiteBootstrap } = useSiteBootstrap();

  return {
    settings: siteSettings,
    maintenanceMode: computed(() => siteSettings.value.maintenanceMode),
    syncSettings: () => refreshSiteBootstrap(true),
  };
}
