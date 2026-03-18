<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { ArrowRight, CheckCircle, HeadphonesIcon } from 'lucide-vue-next';
import ONavbar from '../../components/organisms/ONavbar.vue';
import OFooter from '../../components/organisms/OFooter.vue';
import AButton from '../../components/atoms/AButton.vue';
import AGradientText from '../../components/atoms/AGradientText.vue';
import AGlowOrb from '../../components/atoms/AGlowOrb.vue';
import { useScrollAnimation } from '../../composables/useIntersectionObserver';
import { resolveMarketingIcon } from '../../lib/iconMaps';
import { fetchServiceDetailPageFromApi, type ServiceDetailPageData } from '../../lib/siteApi';

const route = useRoute();
const { initScrollAnimations } = useScrollAnimation();

const page = ref<ServiceDetailPageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');

const accentOrb = computed(() => (page.value?.accent === 'violet' ? 'violet' : page.value?.accent === 'cyan' ? 'cyan' : 'sky'));
const pricingCards = computed(() => page.value?.pricingCards || []);

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

const loadPage = async (slug: string) => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    page.value = await fetchServiceDetailPageFromApi(slug);
    await nextTick();
    initScrollAnimations();
  } catch (error) {
    page.value = null;
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load service detail.';
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
  <div class="min-h-screen bg-[#0a0f1a]">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb :color="accentOrb" :size="500" :position="{ top: '5%', left: '-15%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="400" :position="{ top: '40%', right: '-10%' }" :delay="3" intensity="low" />
    </div>

    <ONavbar />

    <section class="relative pt-28 pb-16">
      <div class="container-custom relative z-10">
        <div v-if="isLoading" class="max-w-4xl mx-auto text-center text-sky-300">
          Loading service detail...
        </div>

        <div v-else-if="errorMessage" class="max-w-3xl mx-auto rounded-2xl border border-red-500/20 bg-red-500/10 p-8 text-center text-red-300">
          {{ errorMessage }}
        </div>

        <div v-else-if="page" class="max-w-4xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <component :is="resolveMarketingIcon(page.icon)" :class="['w-4 h-4', sectionIconClass(page.accent)]" />
            <span class="text-sm text-slate-300">{{ page.heroBadge }}</span>
          </div>

          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            {{ page.heroTitlePrefix }}
            <AGradientText>{{ page.heroTitleHighlight }}</AGradientText>
          </h1>

          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-8 leading-relaxed animate-fade-in-up delay-200">
            {{ page.heroDescription }}
          </p>

          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up delay-300">
            <AButton variant="primary" size="lg">
              {{ page.heroPrimaryCta }}
              <ArrowRight class="ml-2 w-5 h-5" />
            </AButton>
            <AButton variant="secondary" size="lg">
              {{ page.heroSecondaryCta }}
            </AButton>
          </div>
        </div>
      </div>
    </section>

    <template v-if="page && !isLoading && !errorMessage">
      <section class="section-py relative">
        <div class="container-custom">
          <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              {{ page.featureSectionTitle }}
            </h2>
            <p class="text-slate-400 text-lg">
              {{ page.featureSectionDescription }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="(feature, index) in page.features"
              :key="feature.title"
              class="scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="glass-md p-6 h-full transition-colors" :class="page.accent === 'violet' ? 'hover:border-violet-500/30' : page.accent === 'cyan' ? 'hover:border-cyan-500/30' : 'hover:border-sky-500/30'">
                <div :class="['w-12 h-12 rounded-xl flex items-center justify-center mb-4', sectionBadgeClass(page.accent)]">
                  <component :is="resolveMarketingIcon(feature.icon)" :class="['w-6 h-6', sectionIconClass(page.accent)]" />
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">{{ feature.title }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed mb-4">
                  {{ feature.description }}
                </p>
                <div v-if="feature.tags?.length" class="flex flex-wrap gap-2">
                  <span
                    v-for="tag in feature.tags"
                    :key="tag"
                    class="px-3 py-1 rounded-full text-xs border"
                    :class="page.accent === 'violet' ? 'bg-violet-500/10 text-violet-300 border-violet-500/20' : page.accent === 'cyan' ? 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20' : 'bg-sky-500/10 text-sky-300 border-sky-500/20'"
                  >
                    {{ tag }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="pricingCards.length" class="section-py relative bg-white/[0.02]">
        <div class="container-custom">
          <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              {{ page.pricingTitle }}
            </h2>
            <p class="text-slate-400 text-lg">
              {{ page.pricingDescription }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div
              v-for="(plan, index) in pricingCards"
              :key="plan.name"
              class="scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="glass-md p-8 h-full relative" :class="{ 'border-sky-500/50': plan.popular }">
                <div v-if="plan.popular" class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-sky-500 to-cyan-500 text-white">
                  Paling Populer
                </div>

                <div class="text-center mb-6">
                  <h3 class="text-xl font-bold text-white mb-2">{{ plan.name }}</h3>
                  <div class="flex items-baseline justify-center gap-1">
                    <span class="text-4xl font-bold text-white">Rp{{ plan.price }}</span>
                    <span class="text-slate-400">{{ plan.period }}</span>
                  </div>
                </div>

                <div class="space-y-3 mb-6">
                  <div v-for="(value, key) in plan.specs" :key="key" class="flex items-center justify-between text-sm gap-4">
                    <span class="text-slate-400">{{ key }}</span>
                    <span class="text-white font-medium text-right">{{ value }}</span>
                  </div>
                </div>

                <ul class="space-y-3 mb-8">
                  <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-2 text-sm text-slate-300">
                    <CheckCircle class="w-4 h-4 text-sky-400 flex-shrink-0" />
                    {{ feature }}
                  </li>
                </ul>

                <AButton :variant="plan.popular ? 'primary' : 'secondary'" class="w-full justify-center">
                  Pilih Paket
                </AButton>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="page.extraSection" class="section-py relative">
        <div class="container-custom">
          <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
              <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                {{ page.extraSection.title }}
              </h2>
              <p class="text-slate-400 text-lg">
                {{ page.extraSection.description }}
              </p>
            </div>

            <div v-if="page.extraSection.type === 'badge-grid'" class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div
                v-for="item in page.extraSection.items"
                :key="item.label"
                class="glass-md p-4 text-center transition-colors cursor-pointer"
                :class="page.accent === 'violet' ? 'hover:border-violet-500/30' : page.accent === 'cyan' ? 'hover:border-cyan-500/30' : 'hover:border-sky-500/30'"
              >
                <span class="text-sm text-white">{{ item.label }}</span>
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'price-grid'" class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
              <div
                v-for="item in page.extraSection.items"
                :key="item.title"
                class="glass-md p-6 text-center transition-colors cursor-pointer"
                :class="item.popular ? 'border-violet-500/30' : 'hover:border-violet-500/30'"
              >
                <div class="flex items-center justify-center gap-2 mb-2">
                  <span class="text-2xl font-bold text-white">{{ item.title }}</span>
                </div>
                <span class="text-violet-400 font-semibold">Rp{{ item.price }}{{ item.suffix }}</span>
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'timeline-grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="item in page.extraSection.items" :key="item.step" class="glass-md p-6 h-full">
                <div class="text-4xl font-bold text-violet-500/30 mb-4">{{ item.step }}</div>
                <h3 class="text-lg font-semibold text-white mb-2">{{ item.title }}</h3>
                <p class="text-slate-400 text-sm">{{ item.description }}</p>
              </div>
            </div>

            <div v-else-if="page.extraSection.type === 'checklist'" class="glass-md p-8 md:p-12">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="item in page.extraSection.items" :key="item.text" class="flex items-center gap-3">
                  <CheckCircle class="w-5 h-5 text-cyan-400 flex-shrink-0" />
                  <span class="text-slate-300">{{ item.text }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-py relative">
        <div class="container-custom">
          <div class="glass-md p-12 md:p-16 text-center max-w-4xl mx-auto relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-sky-500/10 via-cyan-500/10 to-violet-500/10" />
            <div class="relative z-10">
              <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                {{ page.ctaTitle }}
              </h2>
              <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">
                {{ page.ctaDescription }}
              </p>
              <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <AButton variant="primary" size="lg">
                  {{ page.ctaPrimary }}
                  <ArrowRight class="ml-2 w-5 h-5" />
                </AButton>
                <AButton variant="secondary" size="lg">
                  <HeadphonesIcon class="w-5 h-5 mr-2" />
                  {{ page.ctaSecondary }}
                </AButton>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>

    <OFooter />
  </div>
</template>
