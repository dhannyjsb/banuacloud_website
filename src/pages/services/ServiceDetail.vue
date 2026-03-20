<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowRight, Check, Headphones } from 'lucide-vue-next';
import PublicFooter from '../../components/public/PublicFooter.vue';
import PublicNavbar from '../../components/public/PublicNavbar.vue';
import { resolveMarketingIcon } from '../../lib/iconMaps';
import { executeMarketingCtaTarget } from '../../lib/marketingCta';
import { useSeo } from '../../lib/seo';
import { fetchServiceDetailPageFromApi, type ServiceDetailPageData } from '../../lib/siteApi';
import { useScrollReveal } from '../../composables/useScrollReveal';

type AccentTheme = {
  badge: string;
  iconWrap: string;
  iconColor: string;
  tag: string;
  tint: string;
};

const accentThemes: Record<'sky' | 'cyan' | 'violet', AccentTheme> = {
  sky: {
    badge: 'bg-teal-50 text-teal-700',
    iconWrap: 'bg-teal-50',
    iconColor: 'text-teal-700',
    tag: 'bg-teal-50 text-teal-700',
    tint: 'bg-[linear-gradient(180deg,rgba(15,118,110,0.04),rgba(255,255,255,0))]',
  },
  cyan: {
    badge: 'bg-cyan-50 text-cyan-700',
    iconWrap: 'bg-cyan-50',
    iconColor: 'text-cyan-700',
    tag: 'bg-cyan-50 text-cyan-700',
    tint: 'bg-[linear-gradient(180deg,rgba(13,148,136,0.04),rgba(255,255,255,0))]',
  },
  violet: {
    badge: 'bg-violet-50 text-violet-700',
    iconWrap: 'bg-violet-50',
    iconColor: 'text-violet-700',
    tag: 'bg-violet-50 text-violet-700',
    tint: 'bg-[linear-gradient(180deg,rgba(124,61,143,0.04),rgba(255,255,255,0))]',
  },
};

const route = useRoute();
const router = useRouter();
const pageRef = ref<HTMLElement | null>(null);
const page = ref<ServiceDetailPageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');
useScrollReveal(pageRef);

const pricingCards = computed(() => page.value?.pricingCards ?? []);
const heroHighlights = computed(() => page.value?.features.slice(0, 3).map((feature) => feature.title) ?? []);
const extraItems = computed(() => page.value?.extraSection?.items ?? []);
const accentTheme = computed(() => {
  const key = page.value?.accent === 'cyan' ? 'cyan' : page.value?.accent === 'violet' ? 'violet' : 'sky';
  return accentThemes[key];
});

const summaryStats = computed(() => {
  if (!page.value) {
    return [];
  }

  return [
    {
      label: 'Area fokus',
      value: `${page.value.features.length} kapabilitas`,
    },
    {
      label: 'Pilihan paket',
      value: pricingCards.value.length ? `${pricingCards.value.length} opsi` : 'Konsultasi sesuai kebutuhan',
    },
    {
      label: 'Pendampingan',
      value: 'Support teknis responsif',
    },
  ];
});

const defaultHeroPrimaryTarget = computed(() => (pricingCards.value.length ? '#service-pricing' : page.value?.extraSection ? '#service-extra' : '#service-contact'));
const defaultHeroSecondaryTarget = computed(() => (page.value?.extraSection ? '#service-extra' : '#contact'));
const defaultFooterPrimaryTarget = computed(() => '#service-contact');
const defaultFooterSecondaryTarget = computed(() => '#contact');

useSeo(
  computed(() => ({
    title: page.value?.name ? `${page.value.name}` : 'Layanan',
    description: page.value?.heroDescription || 'Detail layanan Banua Cloud Nusantara.',
    canonicalPath: page.value?.slug ? `/services/${page.value.slug}` : route.path,
    type: 'article',
    schema: page.value
      ? [
          {
            '@context': 'https://schema.org',
            '@type': 'Service',
            name: page.value.name,
            description: page.value.heroDescription,
            url: typeof window === 'undefined' ? '' : `${window.location.origin}/services/${page.value.slug}`,
            provider: {
              '@type': 'Organization',
              name: 'Banua Cloud Nusantara',
            },
          },
        ]
      : [],
  })),
);

const goToHeroPrimaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.heroPrimaryTarget, defaultHeroPrimaryTarget.value);
};

const goToHeroSecondaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.heroSecondaryTarget, defaultHeroSecondaryTarget.value);
};

const goToFooterPrimaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.ctaPrimaryTarget, defaultFooterPrimaryTarget.value);
};

const goToFooterSecondaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.ctaSecondaryTarget, defaultFooterSecondaryTarget.value);
};

const hasVisiblePrice = (price: string): boolean => {
  const normalized = price.replace(/\./g, '').replace(/,/g, '').trim();

  return normalized !== '' && normalized !== '0';
};

const usesContactPricing = (price: string): boolean => {
  return !hasVisiblePrice(price);
};

const shouldShowExtraItemPrice = (): boolean => {
  return page.value?.slug !== 'domain';
};

const shouldUseCompactExtraGrid = (): boolean => {
  return page.value?.slug === 'domain';
};

const loadPage = async (slug: string) => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    page.value = await fetchServiceDetailPageFromApi(slug);
  } catch (error) {
    page.value = null;
    errorMessage.value = error instanceof Error ? error.message : 'Detail layanan belum bisa dimuat saat ini.';
  } finally {
    isLoading.value = false;
  }
};

watch(
  () => route.params.slug,
  (slug) => {
    if (typeof slug === 'string') {
      void loadPage(slug);
    }
  },
  { immediate: true },
);
</script>

<template>
  <div ref="pageRef" class="public-page">
    <PublicNavbar />

    <main class="public-main">
      <section class="public-section pt-12 md:pt-20">
        <div class="public-container">
          <div v-if="isLoading" class="public-card px-6 py-16 text-center text-base text-slate-600 md:px-8">
            Memuat detail layanan...
          </div>

          <div v-else-if="errorMessage" class="public-card border-red-200 bg-red-50/80 px-6 py-10 text-center text-red-700 md:px-8">
            {{ errorMessage }}
          </div>

          <div v-else-if="page" :class="['public-card overflow-hidden px-6 py-8 md:px-8 md:py-10 lg:px-10 lg:py-12', accentTheme.tint]">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.12fr)_minmax(20rem,0.88fr)] lg:items-start lg:gap-8">
              <div>
                <span :class="['mb-3 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold animate-fade-in', accentTheme.badge]">
                  <component :is="resolveMarketingIcon(page.icon)" class="h-3.5 w-3.5" />
                  {{ page.heroBadge }}
                </span>

                <h1 class="public-title animate-fade-in-up">
                  {{ page.heroTitlePrefix }}
                  <span :class="accentTheme.iconColor">{{ page.heroTitleHighlight }}</span>
                </h1>

                <p class="mt-5 max-w-3xl text-[0.9375rem] leading-relaxed text-slate-500 animate-fade-in-up delay-100">{{ page.heroDescription }}</p>

                <div v-if="heroHighlights.length" class="mt-6 flex flex-wrap gap-2 animate-fade-in delay-200">
                  <span v-for="highlight in heroHighlights" :key="highlight" class="public-chip">{{ highlight }}</span>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row animate-fade-in-up delay-200">
                  <button type="button" class="public-button public-button-primary" @click="goToHeroPrimaryCta">
                    {{ page.heroPrimaryCta }}
                    <ArrowRight class="h-4 w-4" />
                  </button>
                  <button type="button" class="public-button public-button-secondary" @click="goToHeroSecondaryCta">
                    {{ page.heroSecondaryCta }}
                  </button>
                </div>
              </div>

              <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-2 animate-fade-in-up delay-200">
                <article
                  v-for="(stat, index) in summaryStats"
                  :key="stat.label"
                  :class="[
                    'public-stat h-full text-left',
                    index === summaryStats.length - 1 ? 'sm:col-span-3 lg:col-span-2' : '',
                  ]"
                >
                  <p class="text-xl font-bold text-slate-900">{{ stat.value }}</p>
                  <p class="mt-1 text-[0.6875rem] font-medium uppercase tracking-wider text-slate-400">{{ stat.label }}</p>
                </article>
              </div>
            </div>
          </div>
        </div>
      </section>

      <template v-if="page && !isLoading && !errorMessage">
        <section class="public-section">
          <div class="public-container">
            <div class="reveal public-section-heading mb-12">
              <span class="public-eyebrow mb-3">Keunggulan Layanan</span>
              <h2 class="public-section-title mb-3">{{ page.featureSectionTitle }}</h2>
              <p class="text-[0.9375rem] leading-relaxed text-slate-500">{{ page.featureSectionDescription }}</p>
            </div>

            <div class="reveal-stagger grid gap-5 md:grid-cols-2 xl:grid-cols-3">
              <article v-for="feature in page.features" :key="feature.title" class="reveal-item public-card p-6">
                <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', accentTheme.iconWrap]">
                  <component :is="resolveMarketingIcon(feature.icon)" :class="['h-5 w-5', accentTheme.iconColor]" />
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-900">{{ feature.title }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ feature.description }}</p>
                <div v-if="feature.tags?.length" class="mt-4 flex flex-wrap gap-1.5">
                  <span v-for="tag in feature.tags" :key="tag" :class="['rounded-full px-2.5 py-0.5 text-xs font-semibold', accentTheme.tag]">{{ tag }}</span>
                </div>
              </article>
            </div>
          </div>
        </section>

        <section v-if="pricingCards.length" id="service-pricing" class="public-section public-anchor">
          <div class="public-container">
            <div class="reveal public-section-heading mb-12">
              <span class="public-eyebrow mb-3">Pilihan Paket</span>
              <h2 class="public-section-title mb-3">{{ page.pricingTitle }}</h2>
              <p class="text-[0.9375rem] leading-relaxed text-slate-500">{{ page.pricingDescription }}</p>
            </div>

            <div class="reveal-stagger grid gap-5 xl:grid-cols-3">
              <article v-for="plan in pricingCards" :key="plan.name" class="reveal-item public-card flex h-full flex-col p-6">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <p v-if="hasVisiblePrice(plan.price)" class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ plan.period }}</p>
                    <p v-else class="text-xs font-medium uppercase tracking-wider text-slate-400">Harga fleksibel</p>
                    <h3 class="mt-1.5 text-base font-semibold text-slate-900">{{ plan.name }}</h3>
                  </div>
                  <span v-if="plan.popular" :class="['rounded-full px-2.5 py-0.5 text-xs font-semibold', accentTheme.tag]">Paling dipilih</span>
                </div>

                <div class="mt-5 min-h-14">
                  <p v-if="hasVisiblePrice(plan.price)" class="text-2xl font-bold text-slate-900">{{ plan.price }}</p>
                  <div v-else class="inline-flex max-w-[17rem] items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div :class="['mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full', accentTheme.iconWrap]">
                      <Headphones :class="['h-4 w-4', accentTheme.iconColor]" />
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-slate-900">Hubungi kami untuk harganya</p>
                      <p class="mt-1 text-xs leading-relaxed text-slate-500">Paket ini disesuaikan dengan kebutuhan implementasi Anda.</p>
                    </div>
                  </div>
                </div>

                <div :class="['mt-5 grid gap-2 rounded-xl p-4', usesContactPricing(plan.price) ? 'bg-slate-100/80' : 'bg-slate-50']">
                  <div v-for="(specValue, specLabel) in plan.specs" :key="specLabel" class="flex items-center justify-between gap-4 text-sm text-slate-600">
                    <span class="capitalize">{{ specLabel }}</span>
                    <span class="font-semibold text-slate-900">{{ specValue }}</span>
                  </div>
                </div>

                <ul class="mt-5 space-y-2.5 text-sm text-slate-700">
                  <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-2.5">
                    <Check :class="['h-3.5 w-3.5 shrink-0', accentTheme.iconColor]" />
                    <span>{{ feature }}</span>
                  </li>
                </ul>

                <div class="mt-auto pt-5">
                  <button type="button" class="public-button public-button-secondary w-full text-sm" @click="goToFooterSecondaryCta">
                    {{ page.ctaSecondary }}
                  </button>
                </div>
              </article>
            </div>
          </div>
        </section>

        <section v-if="page.extraSection" id="service-extra" class="public-section public-anchor">
          <div class="public-container">
            <div class="reveal public-section-heading mb-12">
              <span class="public-eyebrow mb-3">Ruang Lingkup Tambahan</span>
              <h2 class="public-section-title mb-3">{{ page.extraSection.title }}</h2>
              <p class="text-[0.9375rem] leading-relaxed text-slate-500">{{ page.extraSection.description }}</p>
            </div>

            <div v-if="page.extraSection.type === 'badge-grid'" class="reveal-stagger grid gap-5 md:grid-cols-2 xl:grid-cols-3">
              <article v-for="(item, index) in extraItems" :key="`${item.label}-${index}`" class="reveal-item public-card p-6">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ item.label || `Item ${index + 1}` }}</p>
                <p class="mt-2 text-base font-semibold text-slate-900">{{ item.text || item.title }}</p>
                <p v-if="item.description" class="mt-2 text-sm leading-relaxed text-slate-500">{{ item.description }}</p>
              </article>
            </div>

            <div v-else-if="page.extraSection.type === 'timeline-grid'" class="reveal-stagger grid gap-5 lg:grid-cols-2">
              <article v-for="(item, index) in extraItems" :key="`${item.step}-${index}`" class="reveal-item public-card p-6">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">{{ item.step || `Tahap ${index + 1}` }}</p>
                <h3 class="mt-2 text-base font-semibold text-slate-900">{{ item.title || item.label }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ item.description || item.text }}</p>
              </article>
            </div>

            <div v-else-if="page.extraSection.type === 'checklist'" class="reveal public-card p-6">
              <div class="grid gap-3 md:grid-cols-2">
                <div v-for="(item, index) in extraItems" :key="`${item.text}-${index}`" class="flex items-center gap-2.5 rounded-lg bg-slate-50 px-3 py-3 text-sm text-slate-700">
                  <Check :class="['h-3.5 w-3.5 shrink-0', accentTheme.iconColor]" />
                  <span>{{ item.text || item.title || item.description }}</span>
                </div>
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'price-grid'">
              <div v-if="shouldUseCompactExtraGrid()" class="reveal-stagger grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <article v-for="(item, index) in extraItems" :key="`${item.title}-${index}`" class="reveal-item public-card p-4">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <h3 class="text-base font-semibold text-slate-900">{{ item.title || item.label }}</h3>
                      <p class="mt-1 text-xs uppercase tracking-wider text-slate-400">Ekstensi populer</p>
                    </div>
                    <div :class="['flex h-9 w-9 items-center justify-center rounded-full', accentTheme.iconWrap]">
                      <Check :class="['h-4 w-4', accentTheme.iconColor]" />
                    </div>
                  </div>
                  <span v-if="item.popular" :class="['mt-4 inline-flex rounded-full px-2.5 py-1 text-[0.6875rem] font-semibold', accentTheme.tag]">Rekomendasi</span>
                </article>
              </div>

              <div v-else class="reveal-stagger grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <article v-for="(item, index) in extraItems" :key="`${item.title}-${index}`" class="reveal-item public-card p-6">
                  <div class="flex items-start justify-between gap-4">
                    <h3 class="text-lg font-semibold text-slate-900">{{ item.title || item.label }}</h3>
                    <span v-if="item.popular" :class="['rounded-full px-2.5 py-0.5 text-xs font-semibold', accentTheme.tag]">Rekomendasi</span>
                  </div>
                  <p v-if="shouldShowExtraItemPrice() && item.price" class="mt-3 text-2xl font-bold text-slate-900">{{ item.price }}<span v-if="item.suffix" class="text-sm font-medium text-slate-500"> {{ item.suffix }}</span></p>
                  <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ item.description || item.text }}</p>
                </article>
              </div>
            </div>
          </div>
        </section>

        <section id="service-contact" class="public-section public-anchor pt-0">
          <div class="public-container">
            <div class="reveal public-card-muted px-6 py-8 md:px-10 md:py-10">
              <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div>
                  <span class="public-kicker mb-3">
                    <Headphones class="h-3.5 w-3.5 text-orange-500" />
                    Koordinasi berikutnya
                  </span>
                  <h2 class="public-section-title mb-3">{{ page.ctaTitle }}</h2>
                  <p class="text-sm leading-relaxed text-slate-500">{{ page.ctaDescription }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                  <button type="button" class="public-button public-button-primary" @click="goToFooterPrimaryCta">
                    {{ page.ctaPrimary }}
                    <ArrowRight class="h-4 w-4" />
                  </button>
                  <button type="button" class="public-button public-button-secondary" @click="goToFooterSecondaryCta">
                    {{ page.ctaSecondary }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </template>
    </main>

    <PublicFooter />
  </div>
</template>
