import { computed, onMounted, onUnmounted, ref } from 'vue';
import {
  defaultSiteBootstrap,
  fetchSiteBootstrapFromApi,
  type SiteBootstrap,
} from '../lib/siteApi';

const SITE_DATA_UPDATED_EVENT = 'site-data-updated';

const siteBootstrap = ref<SiteBootstrap>(JSON.parse(JSON.stringify(defaultSiteBootstrap)) as SiteBootstrap);
const isSiteBootstrapLoading = ref(false);

let bootstrapPromise: Promise<void> | null = null;

export function emitSiteDataUpdated() {
  if (typeof window === 'undefined') {
    return;
  }

  window.dispatchEvent(new CustomEvent(SITE_DATA_UPDATED_EVENT));
}

async function refreshBootstrap(force = false) {
  if (!force && bootstrapPromise) {
    await bootstrapPromise;
    return;
  }

  bootstrapPromise = (async () => {
    isSiteBootstrapLoading.value = true;

    try {
      siteBootstrap.value = await fetchSiteBootstrapFromApi();
    } catch {
      siteBootstrap.value = JSON.parse(JSON.stringify(defaultSiteBootstrap)) as SiteBootstrap;
    } finally {
      isSiteBootstrapLoading.value = false;
      bootstrapPromise = null;
    }
  })();

  await bootstrapPromise;
}

export function useSiteBootstrap() {
  const handleSiteDataUpdated = () => {
    void refreshBootstrap(true);
  };

  onMounted(() => {
    void refreshBootstrap();
    window.addEventListener(SITE_DATA_UPDATED_EVENT, handleSiteDataUpdated);
  });

  onUnmounted(() => {
    if (typeof window === 'undefined') {
      return;
    }

    window.removeEventListener(SITE_DATA_UPDATED_EVENT, handleSiteDataUpdated);
  });

  return {
    siteBootstrap,
    siteSettings: computed(() => siteBootstrap.value.settings),
    heroContent: computed(() => siteBootstrap.value.heroContent),
    features: computed(() => siteBootstrap.value.features),
    testimonials: computed(() => siteBootstrap.value.testimonials),
    services: computed(() => siteBootstrap.value.services),
    isSiteBootstrapLoading,
    refreshSiteBootstrap: refreshBootstrap,
  };
}
