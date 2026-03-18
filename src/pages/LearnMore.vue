<script setup lang="ts">
import { nextTick, onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, CheckCircle, ChevronDown, ChevronUp } from 'lucide-vue-next';
import ONavbar from '../components/organisms/ONavbar.vue';
import OFooter from '../components/organisms/OFooter.vue';
import AButton from '../components/atoms/AButton.vue';
import AGradientText from '../components/atoms/AGradientText.vue';
import AGlowOrb from '../components/atoms/AGlowOrb.vue';
import { resolveMarketingIcon, resolveServiceIcon } from '../lib/iconMaps';
import { fetchLearnMorePageFromApi, type LearnMorePageData } from '../lib/siteApi';

const router = useRouter();
const page = ref<LearnMorePageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');
const openFaq = ref<number | null>(null);

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
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load marketing page.';
  } finally {
    isLoading.value = false;
  }
};

const goToService = (slug: string) => {
  router.push(`/services/${slug}`);
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
  <div class="min-h-screen bg-[#0a0f1a]">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <AGlowOrb color="sky" :size="400" :position="{ top: '10%', left: '-10%' }" :delay="0" intensity="low" />
      <AGlowOrb color="cyan" :size="350" :position="{ top: '30%', right: '-5%' }" :delay="2" intensity="low" />
      <AGlowOrb color="violet" :size="300" :position="{ bottom: '20%', left: '10%' }" :delay="4" intensity="low" />
    </div>

    <ONavbar />

    <section class="relative pt-32 pb-20">
      <div class="container-custom relative z-10">
        <div v-if="isLoading" class="max-w-4xl mx-auto text-center text-sky-300">
          Loading marketing content...
        </div>

        <div v-else-if="errorMessage" class="max-w-3xl mx-auto rounded-2xl border border-red-500/20 bg-red-500/10 p-8 text-center text-red-300">
          {{ errorMessage }}
        </div>

        <div v-else-if="page" class="max-w-4xl mx-auto text-center">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-fade-in-up">
            <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse" />
            <span class="text-sm text-slate-300">{{ page.heroBadge }}</span>
          </div>

          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up delay-100">
            {{ page.heroTitlePrefix }}
            <AGradientText>{{ page.heroTitleHighlight }}</AGradientText>
          </h1>

          <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed animate-fade-in-up delay-200">
            {{ page.heroDescription }}
          </p>

          <div class="grid grid-cols-3 gap-8 max-w-xl mx-auto animate-fade-in-up delay-300">
            <div v-for="stat in page.stats" :key="stat.label" class="text-center">
              <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-3">
                <component :is="resolveMarketingIcon(stat.icon)" class="w-6 h-6 text-sky-400" />
              </div>
              <div class="text-2xl md:text-3xl font-bold text-white">{{ stat.value }}</div>
              <div class="text-sm text-slate-400">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <template v-if="page && !isLoading && !errorMessage">
      <section class="section-py relative">
        <div class="container-custom">
          <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
              <span class="text-sm text-sky-400 font-medium">{{ page.serviceSectionBadge }}</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              {{ page.serviceSectionTitle }}
            </h2>
            <p class="text-slate-400 text-lg">
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
              <div class="glass-md rounded-2xl p-8 border border-white/5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                  <div>
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-6" :class="serviceBadgeClasses[service.gradient] || serviceBadgeClasses.sky">
                      <component :is="resolveServiceIcon(service.icon)" class="w-8 h-8 text-sky-400" />
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ service.title }}</h3>
                    <p class="text-sky-400 font-medium mb-4">{{ service.subtitle }}</p>
                    <p class="text-slate-400 leading-relaxed mb-6">
                      {{ service.description }}
                    </p>
                    <ul class="space-y-3">
                      <li v-for="feature in service.features" :key="feature" class="flex items-center gap-3 text-slate-300">
                        <CheckCircle class="w-5 h-5 text-sky-400 flex-shrink-0" />
                        <span>{{ feature }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="hidden lg:block">
                    <div class="aspect-square rounded-2xl p-8 relative overflow-hidden" :class="serviceGradientClasses[service.gradient] || serviceGradientClasses.sky">
                      <div class="absolute inset-0 flex items-center justify-center">
                        <component :is="resolveServiceIcon(service.icon)" class="w-32 h-32 text-white/10" />
                      </div>
                      <div class="relative z-10 mt-auto">
                        <AButton variant="primary" class="w-full justify-center" @click="goToService(service.slug)">
                          Lihat Detail
                          <ArrowRight class="ml-2 w-5 h-5" />
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
        <div class="container-custom">
          <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
              <span class="text-sm text-sky-400 font-medium">{{ page.reasonsBadge }}</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              {{ page.reasonsTitle }}
            </h2>
            <p class="text-slate-400 text-lg">
              {{ page.reasonsDescription }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
              v-for="(reason, index) in page.reasons"
              :key="reason.title"
              class="glass-md rounded-xl p-6 text-center scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-sky-500/20 to-cyan-500/20 border border-sky-500/20 mb-4">
                <component :is="resolveMarketingIcon(reason.icon)" class="w-7 h-7 text-sky-400" />
              </div>
              <h3 class="text-lg font-bold text-white mb-2">{{ reason.title }}</h3>
              <p class="text-slate-400 text-sm">
                {{ reason.description }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="section-py relative">
        <div class="container-custom">
          <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
              <span class="text-sm text-sky-400 font-medium">{{ page.faqBadge }}</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              {{ page.faqTitle }}
            </h2>
            <p class="text-slate-400 text-lg">
              {{ page.faqDescription }}
            </p>
          </div>

          <div class="max-w-3xl mx-auto space-y-4">
            <div
              v-for="(faq, index) in page.faqs"
              :key="faq.question"
              class="glass-md rounded-xl overflow-hidden scroll-animate"
              :style="{ transitionDelay: `${index * 100}ms` }"
            >
              <button class="w-full flex items-center justify-between p-6 text-left" @click="toggleFaq(index)">
                <span class="text-white font-medium pr-4">{{ faq.question }}</span>
                <ChevronDown v-if="openFaq !== index" class="w-5 h-5 text-sky-400 flex-shrink-0" />
                <ChevronUp v-else class="w-5 h-5 text-sky-400 flex-shrink-0" />
              </button>
              <div v-if="openFaq === index" class="px-6 pb-6">
                <p class="text-slate-400 leading-relaxed">
                  {{ faq.answer }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section-py relative">
        <div class="container-custom">
          <div class="glass-md rounded-2xl p-12 text-center border border-sky-500/20 bg-gradient-to-br from-sky-500/10 to-cyan-500/10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
              {{ page.ctaTitle }}
            </h2>
            <p class="text-slate-400 text-lg mb-8 max-w-2xl mx-auto">
              {{ page.ctaDescription }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
              <AButton variant="primary" size="lg">
                {{ page.ctaPrimary }}
                <ArrowRight class="ml-2 w-5 h-5" />
              </AButton>
              <AButton variant="secondary" size="lg">
                {{ page.ctaSecondary }}
              </AButton>
            </div>
          </div>
        </div>
      </section>
    </template>

    <OFooter />
  </div>
</template>
