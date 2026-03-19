<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, CheckCircle, ChevronDown, ChevronUp } from 'lucide-vue-next';
import ONavbar from '../components/organisms/ONavbar.vue';
import OFooter from '../components/organisms/OFooter.vue';
import AButton from '../components/atoms/AButton.vue';
import AGradientText from '../components/atoms/AGradientText.vue';
import AGlowOrb from '../components/atoms/AGlowOrb.vue';
import { resolveMarketingIcon, resolveServiceIcon } from '../lib/iconMaps';
import { executeMarketingCtaTarget } from '../lib/marketingCta';
import { fetchLearnMorePageFromApi, type LearnMorePageData } from '../lib/siteApi';

const router = useRouter();
const page = ref<LearnMorePageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');
const openFaq = ref<number | null>(null);

const highlightedServices = computed(() => page.value?.services.slice(0, 3).map((service) => service.title) ?? []);

const toggleFaq = (index: number) => {
  openFaq.value = openFaq.value === index ? null : index;
};

const serviceGradientClasses: Record<string, string> = {
  sky: 'bg-gradient-to-br from-sky-500/10 to-cyan-500/10',
  cyan: 'bg-gradient-to-br from-cyan-500/10 to-teal-500/10',
  violet: 'bg-gradient-to-br from-violet-500/10 to-purple-500/10',
};

const serviceBadgeClasses: Record<string, string> = {
  sky: 'bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20',
  cyan: 'bg-gradient-to-br from-cyan-500/20 to-teal-500/20 border border-cyan-500/20',
  violet: 'bg-gradient-to-br from-violet-500/20 to-purple-500/20 border border-violet-500/20',
};

let scrollObserver: IntersectionObserver | null = null;

const initScrollObserver = async () => {
  await nextTick();

  if (scrollObserver) {
    scrollObserver.disconnect();
  }

  scrollObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    },
    {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
    },
  );

  document.querySelectorAll('.scroll-animate').forEach((element) => {
    scrollObserver?.observe(element);
  });
};

const loadPage = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    page.value = await fetchLearnMorePageFromApi();
    await initScrollObserver();
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Konten halaman belum bisa dimuat saat ini.';
  } finally {
    isLoading.value = false;
  }
};

const goToService = (slug: string) => {
  router.push(`/services/${slug}`);
};

const goToPrimaryCta = () => {
  const firstServiceSlug = page.value?.services[0]?.slug;

  executeMarketingCtaTarget(
    router,
    page.value?.ctaPrimaryTarget,
    firstServiceSlug ? `/services/${firstServiceSlug}` : '#learn-more-services',
  );
};

const goToSecondaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.ctaSecondaryTarget, '#contact');
};

onMounted(() => {
  void loadPage();
});

onUnmounted(() => {
  if (scrollObserver) {
    scrollObserver.disconnect();
    scrollObserver = null;
  }
});
</script>

<template>
  <div class="min-h-screen">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb color="sky" :size="400" :position="{ top: '10%', left: '-10%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="350" :position="{ top: '30%', right: '-5%' }" :delay="2" intensity="low" />
      <AGlowOrb color="violet" :size="300" :position="{ bottom: '20%', left: '10%' }" :delay="4" intensity="low" />
    </div>

    <ONavbar />

    <section class="relative overflow-hidden pt-28 pb-14 md:pt-36 md:pb-18">
      <div class="absolute inset-0 bg-gradient-to-b from-sky-500/5 via-transparent to-transparent" />
      <div class="absolute inset-x-0 top-0 h-[520px] bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.16),transparent_58%)]" />

      <div class="container-custom relative z-10">
        <div v-if="isLoading" class="mx-auto max-w-4xl rounded-[2rem] border border-sky-400/15 bg-[#0b1628]/70 px-8 py-14 text-center text-sky-300 shadow-[0_24px_90px_rgba(2,12,27,0.36)]">
          Memuat informasi layanan Banua Cloud...
        </div>

        <div v-else-if="errorMessage" class="mx-auto max-w-3xl rounded-[2rem] border border-red-500/20 bg-red-500/10 p-8 text-center text-red-300 shadow-[0_24px_90px_rgba(2,12,27,0.36)]">
          {{ errorMessage }}
        </div>

        <div v-else-if="page" class="mx-auto max-w-6xl">
          <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.06),rgba(255,255,255,0.02))] px-6 py-12 shadow-[0_30px_110px_rgba(2,12,27,0.38)] md:px-12 md:py-14">
            <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/60 to-transparent" />
            <div class="absolute -right-12 top-10 h-40 w-40 rounded-full bg-sky-500/10 blur-3xl" />
            <div class="absolute -left-10 bottom-8 h-32 w-32 rounded-full bg-cyan-500/10 blur-3xl" />

            <div class="relative z-10 text-center">
              <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-sky-400/20 bg-sky-500/10 px-4 py-2 text-sm text-sky-100 shadow-[0_0_0_1px_rgba(125,211,252,0.08)] animate-fade-in-up">
                <span class="h-2 w-2 rounded-full bg-sky-300 animate-pulse" />
                <span>{{ page.heroBadge }}</span>
              </div>

              <h1 class="mb-6 font-display text-4xl font-bold leading-[0.96] text-white animate-fade-in-up delay-100 md:text-6xl lg:text-7xl">
                <span class="mr-3 inline-block">{{ page.heroTitlePrefix }}</span>
                <AGradientText>{{ page.heroTitleHighlight }}</AGradientText>
              </h1>

              <p class="mx-auto mb-8 max-w-3xl text-base leading-8 text-slate-300 animate-fade-in-up delay-200 md:text-xl">
                {{ page.heroDescription }}
              </p>

              <div v-if="highlightedServices.length" class="mb-10 flex flex-wrap items-center justify-center gap-3 animate-fade-in-up delay-300">
                <span
                  v-for="serviceTitle in highlightedServices"
                  :key="serviceTitle"
                  class="rounded-full border border-white/10 bg-white/[0.04] px-4 py-2 text-sm font-medium text-slate-200"
                >
                  {{ serviceTitle }}
                </span>
              </div>

              <div class="mx-auto grid max-w-4xl grid-cols-1 gap-4 animate-fade-in-up delay-400 sm:grid-cols-3 sm:gap-5">
                <div
                  v-for="stat in page.stats"
                  :key="stat.label"
                  class="rounded-[1.6rem] border border-white/10 bg-[#0b1628]/65 px-5 py-5 text-center shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]"
                >
                  <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-sky-400/20 bg-gradient-to-br from-sky-500/20 to-cyan-500/20">
                    <component :is="resolveMarketingIcon(stat.icon)" class="h-6 w-6 text-sky-400" />
                  </div>
                  <div class="text-3xl font-bold text-white">{{ stat.value }}</div>
                  <div class="mt-1 text-sm text-slate-400">{{ stat.label }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <template v-if="page && !isLoading && !errorMessage">
      <section id="learn-more-services" class="section-py relative">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(6,182,212,0.08),transparent_45%)]" />

        <div class="container-custom relative z-10">
          <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-400/15 bg-cyan-500/10 px-4 py-2">
              <span class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-300">{{ page.serviceSectionBadge }}</span>
            </div>
            <h2 class="mb-5 text-3xl font-bold text-white md:text-5xl">
              {{ page.serviceSectionTitle }}
            </h2>
            <p class="text-lg leading-8 text-slate-400">
              {{ page.serviceSectionDescription }}
            </p>
          </div>

          <div class="space-y-8">
            <div
              v-for="(service, index) in page.services"
              :key="service.slug"
              class="scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="group relative overflow-hidden rounded-[1.9rem] border border-white/10 bg-[#0b1628]/72 p-6 shadow-[0_24px_90px_rgba(2,12,27,0.3)] md:p-8">
                <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/55 to-transparent" />

                <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-[1.25fr_0.75fr]">
                  <div>
                    <div class="mb-6 inline-flex items-center justify-center rounded-[1.35rem] p-4" :class="serviceBadgeClasses[service.gradient] || serviceBadgeClasses.sky">
                      <component :is="resolveServiceIcon(service.icon)" class="h-8 w-8 text-sky-300" />
                    </div>

                    <div class="mb-6">
                      <p class="mb-2 text-xs font-semibold uppercase tracking-[0.22em] text-sky-300">{{ service.subtitle }}</p>
                      <h3 class="mb-3 text-2xl font-bold text-white md:text-3xl">{{ service.title }}</h3>
                      <p class="max-w-2xl leading-8 text-slate-400">
                        {{ service.description }}
                      </p>
                    </div>

                    <ul class="grid gap-3 sm:grid-cols-2">
                      <li
                        v-for="feature in service.features"
                        :key="feature"
                        class="flex items-start gap-3 rounded-2xl border border-white/8 bg-white/[0.03] px-4 py-3 text-slate-300"
                      >
                        <CheckCircle class="mt-0.5 h-5 w-5 flex-shrink-0 text-sky-400" />
                        <span>{{ feature }}</span>
                      </li>
                    </ul>
                  </div>

                  <div>
                    <div class="relative min-h-[280px] overflow-hidden rounded-[1.8rem] border border-white/10 p-8 shadow-[inset_0_1px_0_rgba(255,255,255,0.05)]" :class="serviceGradientClasses[service.gradient] || serviceGradientClasses.sky">
                      <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-white/10 blur-3xl" />
                      <div class="absolute inset-0 flex items-center justify-center">
                        <component :is="resolveServiceIcon(service.icon)" class="h-32 w-32 text-white/10" />
                      </div>

                      <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="rounded-full border border-white/10 bg-white/[0.08] px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white/85">
                          Solusi Terkelola
                        </div>

                        <AButton variant="primary" class="w-full justify-center" @click="goToService(service.slug)">
                          Lihat Detail Layanan
                          <ArrowRight class="ml-2 h-5 w-5" />
                        </AButton>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-py relative bg-white/[0.02]">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(56,189,248,0.08),transparent_40%)]" />

        <div class="container-custom relative z-10">
          <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-sky-400/15 bg-sky-500/10 px-4 py-2">
              <span class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-300">{{ page.reasonsBadge }}</span>
            </div>
            <h2 class="mb-5 text-3xl font-bold text-white md:text-5xl">
              {{ page.reasonsTitle }}
            </h2>
            <p class="text-lg leading-8 text-slate-400">
              {{ page.reasonsDescription }}
            </p>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div
              v-for="(reason, index) in page.reasons"
              :key="reason.title"
              class="scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="group relative h-full overflow-hidden rounded-[1.6rem] border border-white/10 bg-[#0b1628]/72 p-6 shadow-[0_24px_90px_rgba(2,12,27,0.28)] transition-transform duration-300 hover:-translate-y-1">
                <div class="mb-6 flex items-center justify-between">
                  <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-sky-400/20 bg-gradient-to-br from-sky-500/20 to-cyan-500/20">
                    <component :is="resolveMarketingIcon(reason.icon)" class="h-7 w-7 text-sky-400" />
                  </div>
                  <span class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">0{{ index + 1 }}</span>
                </div>

                <h3 class="mb-3 text-xl font-bold text-white">{{ reason.title }}</h3>
                <p class="text-sm leading-7 text-slate-400">
                  {{ reason.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="learn-more-faq" class="section-py relative">
        <div class="container-custom relative z-10">
          <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-sky-400/15 bg-sky-500/10 px-4 py-2">
              <span class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-300">{{ page.faqBadge }}</span>
            </div>
            <h2 class="mb-5 text-3xl font-bold text-white md:text-5xl">
              {{ page.faqTitle }}
            </h2>
            <p class="text-lg leading-8 text-slate-400">
              {{ page.faqDescription }}
            </p>
          </div>

          <div class="mx-auto max-w-4xl space-y-4">
            <div
              v-for="(faq, index) in page.faqs"
              :key="faq.question"
              class="scroll-animate overflow-hidden rounded-[1.5rem] border border-white/10 bg-[#0b1628]/72 shadow-[0_20px_70px_rgba(2,12,27,0.24)]"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <button class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left transition-colors hover:bg-white/[0.03] md:px-7" @click="toggleFaq(index)">
                <span class="pr-4 text-base font-semibold text-white md:text-lg">{{ faq.question }}</span>
                <ChevronDown v-if="openFaq !== index" class="h-5 w-5 flex-shrink-0 text-sky-400" />
                <ChevronUp v-else class="h-5 w-5 flex-shrink-0 text-sky-400" />
              </button>
              <div v-if="openFaq === index" class="border-t border-white/8 px-6 pb-6 pt-4 md:px-7">
                <p class="leading-8 text-slate-400">
                  {{ faq.answer }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-py relative">
        <div class="container-custom">
          <div class="relative overflow-hidden rounded-[2rem] border border-sky-400/15 bg-[linear-gradient(135deg,rgba(14,165,233,0.12),rgba(255,255,255,0.03))] px-6 py-12 text-center shadow-[0_24px_90px_rgba(2,12,27,0.38)] md:px-10 md:py-14">
            <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-sky-400/60 to-transparent" />
            <div class="absolute -right-12 top-8 h-36 w-36 rounded-full bg-sky-500/10 blur-3xl" />

            <div class="relative z-10">
              <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-sky-300">Konsultasi Banua Cloud</p>
              <h2 class="mb-4 text-3xl font-bold text-white md:text-4xl">
                {{ page.ctaTitle }}
              </h2>
              <p class="mx-auto mb-8 max-w-2xl text-lg leading-8 text-slate-300">
                {{ page.ctaDescription }}
              </p>
              <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <AButton variant="primary" size="lg" @click="goToPrimaryCta">
                  {{ page.ctaPrimary }}
                  <ArrowRight class="ml-2 h-5 w-5" />
                </AButton>
                <AButton variant="secondary" size="lg" @click="goToSecondaryCta">
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
