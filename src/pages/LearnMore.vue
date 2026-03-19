<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, Check, ChevronDown, ChevronUp } from 'lucide-vue-next';
import PublicFooter from '../components/public/PublicFooter.vue';
import PublicNavbar from '../components/public/PublicNavbar.vue';
import { resolveMarketingIcon, resolveServiceIcon } from '../lib/iconMaps';
import { executeMarketingCtaTarget } from '../lib/marketingCta';
import { fetchLearnMorePageFromApi, type LearnMorePageData } from '../lib/siteApi';
import { useSiteBootstrap } from '../composables/useSiteBootstrap';
import { useScrollReveal } from '../composables/useScrollReveal';

const router = useRouter();
const pageRef = ref<HTMLElement | null>(null);
const page = ref<LearnMorePageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');
const openFaq = ref<number | null>(0);
const { siteSettings } = useSiteBootstrap();
useScrollReveal(pageRef);

const serviceHighlights = computed(() => page.value?.services.slice(0, 3).map((service) => service.title) ?? []);
const companyCapabilities = [
  {
    icon: 'Server',
    title: 'Cloud & Infrastruktur',
    description: 'Penyediaan server, hosting, backup, dan fondasi operasional inti.',
  },
  {
    icon: 'Globe2',
    title: 'Jaringan Kantor & Gedung',
    description: 'Konstruksi, revitalisasi, dan penataan jaringan untuk lingkungan kerja baru maupun existing.',
  },
  {
    icon: 'Code',
    title: 'Implementasi Aplikasi',
    description: 'Pengembangan sistem web dan mobile yang disiapkan untuk kebutuhan bisnis.',
  },
  {
    icon: 'Shield',
    title: 'Managed IT',
    description: 'Pendampingan teknis, keamanan, dan continuity plan yang lebih terukur.',
  },
];
const primaryFallbackTarget = computed(() => {
  const firstServiceSlug = page.value?.services[0]?.slug;
  return firstServiceSlug ? `/services/${firstServiceSlug}` : '#learn-more-services';
});

const toggleFaq = (index: number) => {
  openFaq.value = openFaq.value === index ? null : index;
};

const loadPage = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    page.value = await fetchLearnMorePageFromApi();
  } catch (error) {
    page.value = null;
    errorMessage.value = error instanceof Error ? error.message : 'Konten halaman belum bisa dimuat saat ini.';
  } finally {
    isLoading.value = false;
  }
};

const goToService = (slug: string) => {
  void router.push(`/services/${slug}`);
};

const goToPrimaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.ctaPrimaryTarget, primaryFallbackTarget.value);
};

const goToSecondaryCta = () => {
  executeMarketingCtaTarget(router, page.value?.ctaSecondaryTarget, '#contact');
};

onMounted(() => {
  void loadPage();
});
</script>

<template>
  <div ref="pageRef" class="public-page">
    <PublicNavbar />

    <main class="public-main">
      <section class="public-section pt-12 md:pt-20">
        <div class="public-container">
          <div v-if="isLoading" class="public-card px-6 py-16 text-center text-base text-slate-600 md:px-8">
            Memuat informasi layanan {{ siteSettings.siteName }}...
          </div>

          <div v-else-if="errorMessage" class="public-card border-red-200 bg-red-50/80 px-6 py-10 text-center text-red-700 md:px-8">
            {{ errorMessage }}
          </div>

          <div v-else-if="page" class="space-y-6">
            <div class="public-card overflow-hidden px-6 py-8 md:px-8 md:py-10 lg:px-10 lg:py-12">
              <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                  <span class="public-kicker mb-3 animate-fade-in">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                    {{ page.heroBadge }}
                  </span>
                  <h1 class="public-title animate-fade-in-up">
                    {{ page.heroTitlePrefix }}
                    <span class="public-title-accent">{{ page.heroTitleHighlight }}</span>
                  </h1>
                  <p class="public-copy mt-5 max-w-3xl text-[0.9375rem] leading-relaxed animate-fade-in-up delay-100">
                    {{ page.heroDescription }}
                  </p>
                  <div v-if="serviceHighlights.length" class="mt-6 flex flex-wrap gap-2 animate-fade-in delay-200">
                    <span v-for="serviceTitle in serviceHighlights" :key="serviceTitle" class="public-chip">{{ serviceTitle }}</span>
                  </div>
                </div>

                <div class="grid gap-4 grid-cols-3 animate-fade-in-up delay-200">
                  <article v-for="stat in page.stats" :key="stat.label" class="public-stat text-center">
                    <div class="public-icon-wrap public-icon-wrap-primary mx-auto">
                      <component :is="resolveMarketingIcon(stat.icon)" class="h-4 w-4" />
                    </div>
                    <p class="mt-2 text-lg font-bold text-slate-900">{{ stat.value }}</p>
                    <p class="mt-1 text-[0.6875rem] font-medium uppercase tracking-wider text-slate-400">{{ stat.label }}</p>
                  </article>
                </div>
              </div>

              <div class="public-divider mt-8 pt-8">
                <div class="flex items-center justify-between gap-4 mb-5">
                  <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Bidang Utama</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">Kapabilitas perusahaan yang ditangani dalam satu alur implementasi</h2>
                  </div>
                </div>

                <div class="reveal-stagger grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                  <article
                    v-for="capability in companyCapabilities"
                    :key="capability.title"
                    class="reveal-item rounded-2xl border border-slate-200/80 bg-white/75 px-4 py-4 shadow-[0_18px_60px_-42px_rgba(15,23,42,0.3)]"
                  >
                    <div class="public-icon-wrap public-icon-wrap-secondary h-10 w-10">
                      <component :is="resolveMarketingIcon(capability.icon)" class="h-4.5 w-4.5" />
                    </div>
                    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ capability.title }}</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ capability.description }}</p>
                  </article>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <template v-if="page && !isLoading && !errorMessage">
        <section id="learn-more-services" class="public-section public-anchor">
          <div class="public-container">
            <div class="reveal public-section-heading mb-12">
              <span class="public-eyebrow mb-3">{{ page.serviceSectionBadge }}</span>
              <h2 class="public-section-title mb-3">{{ page.serviceSectionTitle }}</h2>
              <p class="text-[0.9375rem] leading-relaxed text-slate-500">{{ page.serviceSectionDescription }}</p>
            </div>

            <div class="reveal-stagger grid gap-5 xl:grid-cols-2">
              <article v-for="service in page.services" :key="service.slug" class="reveal-item public-card p-6 md:p-7">
                <div class="flex items-start gap-4">
                  <div class="public-icon-wrap public-icon-wrap-primary">
                    <component :is="resolveServiceIcon(service.icon)" class="h-5 w-5" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                      <div>
                        <h3 class="text-base font-semibold text-slate-900">{{ service.title }}</h3>
                        <p class="mt-0.5 text-xs font-medium text-slate-500">{{ service.subtitle }}</p>
                      </div>
                      <span class="shrink-0 rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">Terkelola</span>
                    </div>
                  </div>
                </div>

                <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ service.description }}</p>

                <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                  <li v-for="feature in service.features" :key="feature" class="flex items-center gap-2.5 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700">
                    <Check class="h-3.5 w-3.5 shrink-0 text-teal-600" />
                    <span>{{ feature }}</span>
                  </li>
                </ul>

                <div class="mt-5">
                  <button type="button" class="public-button public-button-secondary text-sm" @click="goToService(service.slug)">
                    Lihat detail
                    <ArrowRight class="h-3.5 w-3.5" />
                  </button>
                </div>
              </article>
            </div>
          </div>
        </section>

        <section class="public-section">
          <div class="public-container">
            <div class="grid gap-6 lg:grid-cols-[0.72fr_1.28fr] lg:items-start">
              <div class="reveal public-card-muted p-6 md:p-7 lg:sticky lg:top-28">
                <span class="public-eyebrow mb-3">{{ page.reasonsBadge }}</span>
                <h2 class="public-section-title mb-3">{{ page.reasonsTitle }}</h2>
                <p class="text-sm leading-relaxed text-slate-500">{{ page.reasonsDescription }}</p>
              </div>

              <div class="reveal-stagger grid gap-5 md:grid-cols-2">
                <article v-for="reason in page.reasons" :key="reason.title" class="reveal-item public-card p-6">
                  <div class="public-icon-wrap public-icon-wrap-secondary">
                    <component :is="resolveMarketingIcon(reason.icon)" class="h-5 w-5" />
                  </div>
                  <h3 class="mt-4 text-base font-semibold text-slate-900">{{ reason.title }}</h3>
                  <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ reason.description }}</p>
                </article>
              </div>
            </div>
          </div>
        </section>

        <section id="learn-more-faq" class="public-section public-anchor pt-0">
          <div class="public-container">
            <div class="reveal public-section-heading mb-12">
              <span class="public-eyebrow mb-3">{{ page.faqBadge }}</span>
              <h2 class="public-section-title mb-3">{{ page.faqTitle }}</h2>
              <p class="text-[0.9375rem] leading-relaxed text-slate-500">{{ page.faqDescription }}</p>
            </div>

            <div class="reveal-stagger space-y-3">
              <article v-for="(faq, index) in page.faqs" :key="faq.question" class="reveal-item public-card overflow-hidden">
                <button
                  type="button"
                  class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                  @click="toggleFaq(index)"
                >
                  <span class="text-base font-semibold text-slate-900">{{ faq.question }}</span>
                  <ChevronUp v-if="openFaq === index" class="h-4 w-4 shrink-0 text-slate-400" />
                  <ChevronDown v-else class="h-4 w-4 shrink-0 text-slate-400" />
                </button>
                <div v-if="openFaq === index" class="public-divider px-6 py-5">
                  <p class="text-sm leading-relaxed text-slate-500">{{ faq.answer }}</p>
                </div>
              </article>
            </div>
          </div>
        </section>

        <section class="public-section pt-0">
          <div class="public-container">
            <div class="reveal public-card-muted px-6 py-8 md:px-10 md:py-10">
              <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div>
                  <span class="public-kicker mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500" />
                    Langkah berikutnya
                  </span>
                  <h2 class="public-section-title mb-3">{{ page.ctaTitle }}</h2>
                  <p class="text-sm leading-relaxed text-slate-500">{{ page.ctaDescription }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                  <button type="button" class="public-button public-button-primary" @click="goToPrimaryCta">
                    {{ page.ctaPrimary }}
                    <ArrowRight class="h-4 w-4" />
                  </button>
                  <button type="button" class="public-button public-button-secondary" @click="goToSecondaryCta">
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
