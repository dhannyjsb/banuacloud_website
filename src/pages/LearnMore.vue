<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, Check, ChevronDown, ChevronLeft, ChevronRight, ChevronUp, X } from 'lucide-vue-next';
import PublicFooter from '../components/public/PublicFooter.vue';
import PublicNavbar from '../components/public/PublicNavbar.vue';
import { resolveMarketingIcon, resolveServiceIcon } from '../lib/iconMaps';
import { executeMarketingCtaTarget } from '../lib/marketingCta';
import { useSeo } from '../lib/seo';
import { contactCategoryOptions, fetchLearnMorePageFromApi, type LearnMorePageData } from '../lib/siteApi';
import { useSiteBootstrap } from '../composables/useSiteBootstrap';
import { useScrollReveal } from '../composables/useScrollReveal';

const router = useRouter();
const pageRef = ref<HTMLElement | null>(null);
const page = ref<LearnMorePageData | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');
const openFaq = ref<number | null>(0);
const activeGalleryImages = ref<string[]>([]);
const activeGalleryTitle = ref('');
const activeGalleryIndex = ref(0);
const { siteSettings } = useSiteBootstrap();
useScrollReveal(pageRef);

const serviceHighlights = computed(() => page.value?.services.slice(0, 3).map((service) => service.title) ?? []);
const shouldShowCaseStudies = computed(() => {
  return Boolean(page.value?.caseStudiesEnabled) && (page.value?.caseStudies.length ?? 0) > 0;
});
const currentGalleryImage = computed(() => {
  return activeGalleryImages.value[activeGalleryIndex.value] ?? '';
});
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

useSeo(
  computed(() => ({
    title: `Profil Layanan ${siteSettings.value.siteName}`,
    description: page.value?.heroDescription || siteSettings.value.siteDescription,
    canonicalPath: '/learn-more',
    type: 'article',
    image: siteSettings.value.logoUrl || undefined,
    schema: page.value
      ? [
          {
            '@context': 'https://schema.org',
            '@type': 'AboutPage',
            name: `Profil Layanan ${siteSettings.value.siteName}`,
            url: typeof window === 'undefined' ? '' : `${window.location.origin}/learn-more`,
            description: page.value.heroDescription,
          },
        ]
      : [],
  })),
);

const toggleFaq = (index: number) => {
  openFaq.value = openFaq.value === index ? null : index;
};

const categoryLabel = (value: string) => {
  return contactCategoryOptions.find((option) => option.value === value)?.label || value;
};

const openGallery = (images: string[], index: number, title: string) => {
  activeGalleryImages.value = images;
  activeGalleryIndex.value = index;
  activeGalleryTitle.value = title;
  document.body.style.overflow = 'hidden';
};

const closeGallery = () => {
  activeGalleryImages.value = [];
  activeGalleryIndex.value = 0;
  activeGalleryTitle.value = '';
  document.body.style.overflow = '';
};

const showPreviousGalleryImage = () => {
  if (!activeGalleryImages.value.length) {
    return;
  }

  activeGalleryIndex.value = (activeGalleryIndex.value - 1 + activeGalleryImages.value.length) % activeGalleryImages.value.length;
};

const showNextGalleryImage = () => {
  if (!activeGalleryImages.value.length) {
    return;
  }

  activeGalleryIndex.value = (activeGalleryIndex.value + 1) % activeGalleryImages.value.length;
};

const handleKeydown = (event: KeyboardEvent) => {
  if (!currentGalleryImage.value) {
    return;
  }

  if (event.key === 'Escape') {
    closeGallery();
  }

  if (event.key === 'ArrowLeft') {
    showPreviousGalleryImage();
  }

  if (event.key === 'ArrowRight') {
    showNextGalleryImage();
  }
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
  document.addEventListener('keydown', handleKeydown);
  void loadPage();
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
  document.body.style.overflow = '';
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

        <section v-if="shouldShowCaseStudies" id="learn-more-case-studies" class="public-section public-anchor pt-0">
          <div class="public-container pb-12">
            <div class="reveal public-section-heading mb-12">
              <span class="public-eyebrow mb-3">{{ page.caseStudyBadge }}</span>
              <h2 class="public-section-title mb-3">{{ page.caseStudyTitle }}</h2>
              <p class="text-[0.9375rem] leading-relaxed text-slate-500">{{ page.caseStudyDescription }}</p>
            </div>

            <div class="reveal-stagger grid gap-5 xl:grid-cols-3">
              <article v-for="caseStudy in page.caseStudies" :key="`${caseStudy.clientName}-${caseStudy.title}`" class="reveal-item public-card p-6 md:p-7">
                <div class="flex items-center justify-between gap-3">
                  <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">{{ categoryLabel(caseStudy.category) }}</span>
                  <span v-if="caseStudy.isFeatured" class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">Unggulan</span>
                </div>

                <div class="mt-4">
                  <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ caseStudy.clientName }}</p>
                  <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ caseStudy.title }}</h3>
                  <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ caseStudy.summary }}</p>
                </div>

                <div v-if="caseStudy.tags.length" class="mt-5 flex flex-wrap gap-2">
                  <span v-for="tag in caseStudy.tags" :key="tag" class="public-chip">{{ tag }}</span>
                </div>

                <div v-if="caseStudy.galleryImages.length" class="mt-5 grid grid-cols-3 gap-3">
                  <button
                    v-for="(image, imageIndex) in caseStudy.galleryImages"
                    :key="`${caseStudy.title}-${imageIndex}`"
                    type="button"
                    class="group overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-100 shadow-[0_18px_40px_-30px_rgba(15,23,42,0.28)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-30px_rgba(15,23,42,0.35)]"
                    @click="openGallery(caseStudy.galleryImages, imageIndex, caseStudy.title)"
                  >
                    <img :src="image" :alt="`${caseStudy.title} media ${imageIndex + 1}`" class="h-24 w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" />
                  </button>
                </div>
              </article>
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

    <div v-if="currentGalleryImage" class="fixed inset-0 z-[120] flex items-center justify-center bg-slate-950/82 p-4 backdrop-blur-sm" @click="closeGallery">
      <div class="relative w-full max-w-5xl" @click.stop>
        <button type="button" class="absolute right-3 top-3 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-slate-950/70 text-white transition hover:bg-slate-900" @click="closeGallery">
          <X class="h-5 w-5" />
        </button>

        <button
          v-if="activeGalleryImages.length > 1"
          type="button"
          class="absolute left-3 top-1/2 z-10 inline-flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/15 bg-slate-950/70 text-white transition hover:bg-slate-900"
          @click="showPreviousGalleryImage"
        >
          <ChevronLeft class="h-5 w-5" />
        </button>

        <button
          v-if="activeGalleryImages.length > 1"
          type="button"
          class="absolute right-3 top-1/2 z-10 inline-flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/15 bg-slate-950/70 text-white transition hover:bg-slate-900"
          @click="showNextGalleryImage"
        >
          <ChevronRight class="h-5 w-5" />
        </button>

        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white p-3 shadow-[0_40px_120px_-45px_rgba(15,23,42,0.95)] sm:p-4">
          <img :src="currentGalleryImage" :alt="`${activeGalleryTitle} preview`" class="max-h-[80vh] w-full rounded-[1.5rem] object-contain bg-slate-100" />
        </div>

        <div class="mt-4 flex items-center justify-between gap-4 px-2 text-sm text-white/90">
          <p class="font-medium">{{ activeGalleryTitle }}</p>
          <p>{{ activeGalleryIndex + 1 }} / {{ activeGalleryImages.length }}</p>
        </div>
      </div>
    </div>

    <PublicFooter />
  </div>
</template>
