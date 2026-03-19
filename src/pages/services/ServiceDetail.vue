<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowRight, CheckCircle, HeadphonesIcon } from 'lucide-vue-next';
import ONavbar from '../../components/organisms/ONavbar.vue';
import OFooter from '../../components/organisms/OFooter.vue';
import AButton from '../../components/atoms/AButton.vue';
import AGradientText from '../../components/atoms/AGradientText.vue';
import AGlowOrb from '../../components/atoms/AGlowOrb.vue';
import { useScrollAnimation } from '../../composables/useIntersectionObserver';
import { resolveMarketingIcon } from '../../lib/iconMaps';
import { executeMarketingCtaTarget } from '../../lib/marketingCta';
import { fetchServiceDetailPageFromApi, type ServiceDetailPageData } from '../../lib/siteApi';

const route = useRoute();
const router = useRouter();
const { initScrollAnimations } = useScrollAnimation();

const page = ref<ServiceDetailPageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');

const accentOrb = computed(() => (page.value?.accent === 'violet' ? 'violet' : page.value?.accent === 'cyan' ? 'cyan' : 'sky'));
const pricingCards = computed(() => page.value?.pricingCards || []);
const heroHighlights = computed(() => page.value?.features.slice(0, 3).map((feature) => feature.title) ?? []);
const accentPanelClass = computed(() => {
  if (accentOrb.value === 'cyan') {
    return 'bg-[linear-gradient(180deg,rgba(34,211,238,0.14),rgba(255,255,255,0.03))]';
  }

  if (accentOrb.value === 'violet') {
    return 'bg-[linear-gradient(180deg,rgba(168,85,247,0.16),rgba(255,255,255,0.03))]';
  }

  return 'bg-[linear-gradient(180deg,rgba(14,165,233,0.14),rgba(255,255,255,0.03))]';
});
const accentLineClass = computed(() => {
  if (accentOrb.value === 'cyan') {
    return 'via-cyan-400/60';
  }

  if (accentOrb.value === 'violet') {
    return 'via-violet-400/60';
  }

  return 'via-sky-400/60';
});

const sectionBadgeClass = (accent: string) => {
  if (accent === 'cyan') {
    return 'bg-gradient-to-br from-cyan-500/20 to-teal-500/20 border border-cyan-500/20';
  }

  if (accent === 'violet') {
    return 'bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/20';
  }

  return 'bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20';
};

const sectionIconClass = (accent: string) => {
  if (accent === 'cyan') {
    return 'text-cyan-400';
  }

  if (accent === 'violet') {
    return 'text-violet-400';
  }

  return 'text-sky-400';
};

const defaultHeroPrimaryTarget = computed(() => (pricingCards.value.length ? '#service-pricing' : page.value?.extraSection ? '#service-extra' : '#service-contact'));
const defaultHeroSecondaryTarget = computed(() => (page.value?.extraSection ? '#service-extra' : '#contact'));
const defaultFooterPrimaryTarget = computed(() => '#service-contact');
const defaultFooterSecondaryTarget = computed(() => '#contact');

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

const goToRelatedAction = () => {
  if (page.value?.slug) {
    router.push('/learn-more');
  }
};

const loadPage = async (slug: string) => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    page.value = await fetchServiceDetailPageFromApi(slug);
    await nextTick();
    initScrollAnimations();
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

onMounted(() => {
  if (typeof route.params.slug === 'string') {
    void loadPage(route.params.slug);
  }
});
</script>

<template>
  <div class="min-h-screen">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb :color="accentOrb" :size="500" :position="{ top: '5%', left: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="400" :position="{ top: '40%', right: '-10%' }" :delay="3" intensity="low" />
    </div>

    <ONavbar />

    <section class="relative overflow-hidden pt-28 pb-14 md:pt-36 md:pb-18">
      <div class="absolute inset-0 bg-gradient-to-b from-sky-500/5 via-transparent to-transparent" />

      <div class="container-custom relative z-10">
        <div v-if="isLoading" class="mx-auto max-w-4xl rounded-[2rem] border border-sky-400/15 bg-[#0b1628]/70 px-8 py-14 text-center text-sky-300 shadow-[0_24px_90px_rgba(2,12,27,0.36)]">
          Memuat detail layanan...
        </div>

        <div v-else-if="errorMessage" class="mx-auto max-w-3xl rounded-[2rem] border border-red-500/20 bg-red-500/10 p-8 text-center text-red-300 shadow-[0_24px_90px_rgba(2,12,27,0.36)]">
          {{ errorMessage }}
        </div>

        <div v-else-if="page" class="mx-auto max-w-6xl">
          <div :class="['relative overflow-hidden rounded-[2rem] border border-white/10 px-6 py-12 shadow-[0_30px_110px_rgba(2,12,27,0.38)] md:px-12 md:py-14', accentPanelClass]">
            <div :class="['absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent to-transparent', accentLineClass]" />
            <div class="absolute -right-12 top-10 h-40 w-40 rounded-full bg-white/10 blur-3xl" />
            <div class="absolute -left-10 bottom-8 h-32 w-32 rounded-full bg-white/8 blur-3xl" />

            <div class="relative z-10 text-center">
              <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/[0.06] px-4 py-2 text-sm text-slate-200 shadow-[0_0_0_1px_rgba(255,255,255,0.04)] animate-fade-in-up">
                <component :is="resolveMarketingIcon(page.icon)" :class="['h-4 w-4', sectionIconClass(page.accent)]" />
                <span>{{ page.heroBadge }}</span>
              </div>

              <h1 class="mb-6 font-display text-4xl font-bold leading-[0.96] text-white animate-fade-in-up delay-100 md:text-6xl lg:text-7xl">
                <span class="mr-3 inline-block">{{ page.heroTitlePrefix }}</span>
                <AGradientText>{{ page.heroTitleHighlight }}</AGradientText>
              </h1>

              <p class="mx-auto mb-8 max-w-3xl text-base leading-8 text-slate-300 animate-fade-in-up delay-200 md:text-xl">
                {{ page.heroDescription }}
              </p>

              <div v-if="heroHighlights.length" class="mb-10 flex flex-wrap items-center justify-center gap-3 animate-fade-in-up delay-300">
                <span
                  v-for="highlight in heroHighlights"
                  :key="highlight"
                  class="rounded-full border border-white/10 bg-white/[0.04] px-4 py-2 text-sm font-medium text-slate-200"
                >
                  {{ highlight }}
                </span>
              </div>

              <div class="flex flex-col items-center justify-center gap-4 animate-fade-in-up delay-400 sm:flex-row">
                <AButton variant="primary" size="lg" @click="goToHeroPrimaryCta">
                  {{ page.heroPrimaryCta }}
                  <ArrowRight class="ml-2 h-5 w-5" />
                </AButton>
                <AButton variant="secondary" size="lg" @click="goToHeroSecondaryCta">
                  {{ page.heroSecondaryCta }}
                </AButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <template v-if="page && !isLoading && !errorMessage">
      <section class="section-py relative">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(56,189,248,0.08),transparent_40%)]" />

        <div class="container-custom relative z-10">
          <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-sky-400/15 bg-sky-500/10 px-4 py-2">
              <span class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-300">Keunggulan Layanan</span>
            </div>
            <h2 class="mb-5 text-3xl font-bold text-white md:text-5xl">
              {{ page.featureSectionTitle }}
            </h2>
            <p class="text-lg leading-8 text-slate-400">
              {{ page.featureSectionDescription }}
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div
              v-for="(feature, index) in page.features"
              :key="feature.title"
              class="scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="group relative h-full overflow-hidden rounded-[1.7rem] border border-white/10 bg-[#0b1628]/72 p-6 shadow-[0_24px_90px_rgba(2,12,27,0.28)] transition-transform duration-300 hover:-translate-y-1" :class="page.accent === 'violet' ? 'hover:border-violet-500/30' : page.accent === 'cyan' ? 'hover:border-cyan-500/30' : 'hover:border-sky-500/30'">
                <div :class="['mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl', sectionBadgeClass(page.accent)]">
                  <component :is="resolveMarketingIcon(feature.icon)" :class="['h-6 w-6', sectionIconClass(page.accent)]" />
                </div>
                <h3 class="mb-3 text-xl font-semibold text-white">{{ feature.title }}</h3>
                <p class="mb-5 text-sm leading-7 text-slate-400">
                  {{ feature.description }}
                </p>
                <div v-if="feature.tags?.length" class="flex flex-wrap gap-2">
                  <span
                    v-for="tag in feature.tags"
                    :key="tag"
                    class="rounded-full border px-3 py-1 text-xs"
                    :class="page.accent === 'violet' ? 'border-violet-500/20 bg-violet-500/10 text-violet-300' : page.accent === 'cyan' ? 'border-cyan-500/20 bg-cyan-500/10 text-cyan-300' : 'border-sky-500/20 bg-sky-500/10 text-sky-300'"
                  >
                    {{ tag }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="pricingCards.length" id="service-pricing" class="section-py relative bg-white/[0.02]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(6,182,212,0.08),transparent_45%)]" />

        <div class="container-custom relative z-10">
          <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-400/15 bg-cyan-500/10 px-4 py-2">
              <span class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">Pilihan Paket</span>
            </div>
            <h2 class="mb-5 text-3xl font-bold text-white md:text-5xl">
              {{ page.pricingTitle }}
            </h2>
            <p class="text-lg leading-8 text-slate-400">
              {{ page.pricingDescription }}
            </p>
          </div>

          <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 md:grid-cols-3">
            <div
              v-for="(plan, index) in pricingCards"
              :key="plan.name"
              class="scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="relative h-full overflow-hidden rounded-[1.8rem] border border-white/10 bg-[#0b1628]/75 p-8 shadow-[0_24px_90px_rgba(2,12,27,0.28)]" :class="{ 'border-sky-400/40 shadow-[0_28px_100px_rgba(14,165,233,0.16)]': plan.popular }">
                <div v-if="plan.popular" class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-4 py-1 text-xs font-semibold text-white">
                  Paling Populer
                </div>

                <div class="mb-6 text-center">
                  <h3 class="mb-2 text-2xl font-bold text-white">{{ plan.name }}</h3>
                  <div class="flex items-baseline justify-center gap-1">
                    <span class="text-4xl font-bold text-white">Rp{{ plan.price }}</span>
                    <span class="text-slate-400">{{ plan.period }}</span>
                  </div>
                </div>

                <div class="mb-6 space-y-3 rounded-[1.35rem] border border-white/8 bg-white/[0.03] p-4">
                  <div v-for="(value, key) in plan.specs" :key="key" class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-slate-400">{{ key }}</span>
                    <span class="text-right font-medium text-white">{{ value }}</span>
                  </div>
                </div>

                <ul class="mb-8 space-y-3">
                  <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-3 text-sm text-slate-300">
                    <CheckCircle class="h-4 w-4 flex-shrink-0 text-sky-400" />
                    {{ feature }}
                  </li>
                </ul>

                <AButton :variant="plan.popular ? 'primary' : 'secondary'" class="w-full justify-center" @click="goToFooterSecondaryCta">
                  Pilih Paket
                </AButton>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="page.extraSection" id="service-extra" class="section-py relative">
        <div class="container-custom relative z-10">
          <div class="mx-auto max-w-5xl">
            <div class="mx-auto mb-12 max-w-3xl text-center md:mb-16">
              <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-4 py-2">
                <span class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-300">Kapabilitas Tambahan</span>
              </div>
              <h2 class="mb-5 text-3xl font-bold text-white md:text-5xl">
                {{ page.extraSection.title }}
              </h2>
              <p class="text-lg leading-8 text-slate-400">
                {{ page.extraSection.description }}
              </p>
            </div>

            <div v-if="page.extraSection.type === 'badge-grid'" class="grid grid-cols-2 gap-4 md:grid-cols-4">
              <div
                v-for="item in page.extraSection.items"
                :key="item.label"
                class="rounded-[1.4rem] border border-white/10 bg-[#0b1628]/72 px-4 py-5 text-center text-sm text-white shadow-[0_20px_70px_rgba(2,12,27,0.22)] transition-colors"
                :class="page.accent === 'violet' ? 'hover:border-violet-500/30' : page.accent === 'cyan' ? 'hover:border-cyan-500/30' : 'hover:border-sky-500/30'"
              >
                {{ item.label }}
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'price-grid'" class="mx-auto grid max-w-4xl grid-cols-2 gap-4 md:grid-cols-4">
              <div
                v-for="item in page.extraSection.items"
                :key="item.title"
                class="rounded-[1.4rem] border border-white/10 bg-[#0b1628]/72 p-6 text-center shadow-[0_20px_70px_rgba(2,12,27,0.22)] transition-colors"
                :class="item.popular ? 'border-violet-500/30' : 'hover:border-violet-500/30'"
              >
                <div class="mb-2 text-2xl font-bold text-white">{{ item.title }}</div>
                <span class="font-semibold text-violet-400">Rp{{ item.price }}{{ item.suffix }}</span>
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'timeline-grid'" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
              <div v-for="item in page.extraSection.items" :key="item.step" class="rounded-[1.6rem] border border-white/10 bg-[#0b1628]/72 p-6 shadow-[0_20px_70px_rgba(2,12,27,0.22)]">
                <div class="mb-4 text-4xl font-bold text-violet-500/30">{{ item.step }}</div>
                <h3 class="mb-2 text-lg font-semibold text-white">{{ item.title }}</h3>
                <p class="text-sm leading-7 text-slate-400">{{ item.description }}</p>
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'checklist'" class="rounded-[1.8rem] border border-white/10 bg-[#0b1628]/72 p-8 shadow-[0_24px_90px_rgba(2,12,27,0.24)] md:p-12">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div v-for="item in page.extraSection.items" :key="item.text" class="flex items-center gap-3 rounded-2xl border border-white/8 bg-white/[0.03] px-4 py-3">
                  <CheckCircle class="h-5 w-5 flex-shrink-0 text-cyan-400" />
                  <span class="text-slate-300">{{ item.text }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="service-contact" class="section-py relative">
        <div class="container-custom">
          <div class="relative mx-auto max-w-4xl overflow-hidden rounded-[2rem] border border-white/10 px-6 py-12 text-center shadow-[0_24px_90px_rgba(2,12,27,0.38)] md:px-10 md:py-14" :class="accentPanelClass">
            <div :class="['absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent to-transparent', accentLineClass]" />
            <div class="absolute inset-0 bg-gradient-to-r from-sky-500/8 via-cyan-500/8 to-violet-500/8" />

            <div class="relative z-10">
              <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-sky-300">Diskusikan Kebutuhan Anda</p>
              <h2 class="mb-4 text-3xl font-bold text-white md:text-4xl">
                {{ page.ctaTitle }}
              </h2>
              <p class="mx-auto mb-8 max-w-2xl text-lg leading-8 text-slate-300">
                {{ page.ctaDescription }}
              </p>
              <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <AButton variant="primary" size="lg" @click="goToFooterPrimaryCta">
                  {{ page.ctaPrimary }}
                  <ArrowRight class="ml-2 h-5 w-5" />
                </AButton>
                <AButton variant="secondary" size="lg" @click="goToFooterSecondaryCta">
                  <HeadphonesIcon class="mr-2 h-5 w-5" />
                  {{ page.ctaSecondary }}
                </AButton>
              </div>
              <button type="button" class="mt-5 text-sm font-medium text-slate-400 transition-colors hover:text-slate-200" @click="goToRelatedAction">
                Lihat ringkasan semua layanan
              </button>
            </div>
          </div>
        </div>
      </section>
    </template>

    <OFooter />
  </div>
</template>
