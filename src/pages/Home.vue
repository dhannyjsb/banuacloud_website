<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, Check, Quote } from 'lucide-vue-next';
import PublicFooter from '../components/public/PublicFooter.vue';
import PublicNavbar from '../components/public/PublicNavbar.vue';
import { useSiteBootstrap } from '../composables/useSiteBootstrap';
import { useSeo } from '../lib/seo';
import { useScrollReveal } from '../composables/useScrollReveal';
import { resolveFeatureIcon, resolveMarketingIcon, resolveServiceIcon } from '../lib/iconMaps';

const router = useRouter();
const pageRef = ref<HTMLElement | null>(null);
useScrollReveal(pageRef);
const { features, heroContent, services, siteSettings, testimonials } = useSiteBootstrap();

const enabledServices = computed(() => services.value.filter((service) => service.enabled));
const featuredTestimonials = computed(() => testimonials.value.slice(0, 3));
const planCount = computed(() => enabledServices.value.reduce((total, service) => total + service.plans.length, 0));

const heroTitleParts = computed(() => {
  const words = heroContent.value.title.trim().split(/\s+/).filter(Boolean);

  if (words.length < 3) {
    return {
      leading: 'Solusi Cloud',
      accent: heroContent.value.title,
    };
  }

  return {
    leading: words.slice(0, -2).join(' '),
    accent: words.slice(-2).join(' '),
  };
});

const serviceSnapshots = computed(() => {
  return enabledServices.value.map((service) => ({
    ...service,
    usesContactPricing: !getFirstPricedPlan(service),
    startingPrice: getServiceStartingPrice(service),
    highlights: getDisplayPlan(service)?.features?.slice(0, 3) ?? [
      'Implementasi tertata',
      'Monitoring yang mudah dibaca',
      'Pendampingan teknis responsif',
    ],
  }));
});

const heroStats = computed(() => [
  {
    label: 'Layanan aktif',
    value: `${enabledServices.value.length}+`,
  },
  {
    label: 'Pilihan paket',
    value: `${planCount.value}+`,
  },
  {
    label: 'Klien yang memberi testimoni',
    value: `${featuredTestimonials.value.length}+`,
  },
]);

const companyFocusAreas = [
  {
    icon: 'Server',
    title: 'Cloud & VPS',
    description: 'Server dan workload inti perusahaan.',
  },
  {
    icon: 'Globe2',
    title: 'Jaringan Gedung',
    description: 'Konstruksi dan revitalisasi jaringan.',
  },
  {
    icon: 'Code',
    title: 'Pengembangan Aplikasi',
    description: 'Web dan mobile untuk operasional.',
  },
  {
    icon: 'Shield',
    title: 'Managed IT & Backup',
    description: 'Support, keamanan, dan continuity.',
  },
];

const featuredCapabilities = computed(() => features.value.slice(0, 6));

useSeo(
  computed(() => ({
    title: siteSettings.value.siteName,
    description: heroContent.value.subtitle || siteSettings.value.siteDescription,
    canonicalPath: '/',
    type: 'website',
    schema: [
      {
        '@context': 'https://schema.org',
        '@type': 'Organization',
        name: siteSettings.value.companyName || siteSettings.value.siteName,
        url: typeof window === 'undefined' ? '' : window.location.origin,
        logo: siteSettings.value.logoUrl || `${typeof window === 'undefined' ? '' : window.location.origin}/favicon.svg`,
        description: siteSettings.value.siteDescription,
        contactPoint: siteSettings.value.companyEmail
          ? [
              {
                '@type': 'ContactPoint',
                contactType: 'customer support',
                email: siteSettings.value.companyEmail,
                telephone: siteSettings.value.companyPhone,
              },
            ]
          : [],
      },
      {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: siteSettings.value.siteName,
        url: typeof window === 'undefined' ? '' : window.location.origin,
        description: heroContent.value.subtitle || siteSettings.value.siteDescription,
      },
    ],
  })),
);

function formatPrice(price: number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(price);
}

function hasVisiblePrice(price: number) {
  return Number.isFinite(price) && price > 0;
}

function getFirstPricedPlan(service: (typeof enabledServices.value)[number]) {
  return service.plans.find((plan) => hasVisiblePrice(plan.price));
}

function getDisplayPlan(service: (typeof enabledServices.value)[number]) {
  return getFirstPricedPlan(service) ?? service.plans[0];
}

function formatStartingPrice(price: number, period: string) {
  const normalizedPeriod = period.trim().replace(/^\/+/, '');

  if (!normalizedPeriod) {
    return formatPrice(price);
  }

  return `${formatPrice(price)} / ${normalizedPeriod}`;
}

function getServiceStartingPrice(service: (typeof enabledServices.value)[number]) {
  const pricedPlan = getFirstPricedPlan(service);

  if (!pricedPlan) {
    return 'Hubungi kami';
  }

  return formatStartingPrice(pricedPlan.price, pricedPlan.period);
}

const goToLearnMore = () => {
  void router.push('/learn-more');
};

const goToService = (slug: string) => {
  void router.push(`/services/${slug}`);
};

const scrollToSection = (id: string) => {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};
</script>

<template>
  <div ref="pageRef" class="public-page">
    <PublicNavbar />

    <main class="public-main">
      <!-- Hero -->
      <section class="public-section pt-12 md:pt-20">
        <div class="public-container">
          <div class="grid items-start gap-10 lg:grid-cols-[1.25fr_0.75fr] lg:gap-14">
            <div class="space-y-8">
              <span class="public-kicker animate-fade-in">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                {{ siteSettings.siteName }}
              </span>

              <div class="space-y-4 animate-fade-in-up">
                <h1 class="public-title">
                  {{ heroTitleParts.leading }}
                  <span class="public-title-accent">{{ heroTitleParts.accent }}</span>
                </h1>
                <p class="max-w-xl text-[0.9375rem] leading-relaxed text-slate-500 md:text-base">
                  {{ heroContent.subtitle }}
                </p>
              </div>

              <div class="flex flex-col gap-3 sm:flex-row animate-fade-in-up delay-200">
                <button type="button" class="public-button public-button-primary" @click="goToLearnMore">
                  {{ heroContent.ctaPrimary }}
                  <ArrowRight class="h-4 w-4" />
                </button>
                <button type="button" class="public-button public-button-secondary" @click="scrollToSection('services')">
                  {{ heroContent.ctaSecondary }}
                </button>
              </div>

              <div class="flex flex-wrap gap-2 animate-fade-in delay-300">
                <span class="public-chip">Cloud, aplikasi, dan backup terkelola</span>
                <span class="public-chip">Konstruksi jaringan kantor dan gedung</span>
              </div>
            </div>

            <div class="public-card p-6 md:p-8 animate-fade-in-up delay-200">
              <div class="grid gap-3 grid-cols-3">
                <div v-for="stat in heroStats" :key="stat.label" class="public-stat text-center">
                  <p class="text-2xl font-bold text-slate-900">{{ stat.value }}</p>
                  <p class="mt-1 text-[0.6875rem] font-medium uppercase tracking-wider text-slate-400">{{ stat.label }}</p>
                </div>
              </div>

              <div class="public-divider mt-6 pt-6">
                <div class="flex items-center justify-between gap-4 mb-5">
                  <h2 class="text-lg font-semibold text-slate-900">Cakupan Layanan Utama</h2>
                  <button type="button" class="hidden text-sm font-medium text-teal-700 hover:text-teal-800 md:block" @click="goToLearnMore">
                    Lihat semua &rarr;
                  </button>
                </div>

                <div class="space-y-3">
                  <button
                    v-for="service in serviceSnapshots.slice(0, 3)"
                    :key="service.slug"
                    type="button"
                    class="public-card-interactive w-full p-4 text-left"
                    @click="goToService(service.slug)"
                  >
                    <div class="flex items-center gap-4">
                      <div class="public-icon-wrap public-icon-wrap-primary">
                        <component :is="resolveServiceIcon(service.icon)" class="h-5 w-5" />
                      </div>
                      <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-semibold text-slate-900">{{ service.name }}</h3>
                        <p class="mt-0.5 text-xs text-slate-500 truncate">{{ service.description }}</p>
                      </div>
                      <span :class="service.usesContactPricing ? 'shrink-0 text-xs font-semibold text-slate-500' : 'shrink-0 text-xs font-semibold text-teal-700'">{{ service.startingPrice }}</span>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="public-section pt-0">
        <div class="public-container">
          <div class="public-card-muted px-6 py-7 md:px-8 md:py-8">
            <div class="reveal flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
              <div>
                <span class="public-eyebrow mb-3">Bidang Utama</span>
                <h2 class="text-xl font-semibold tracking-tight text-slate-900 md:text-2xl">Kapabilitas perusahaan yang ditangani dalam satu alur implementasi.</h2>
              </div>
              <p class="max-w-2xl text-sm leading-relaxed text-slate-500">
                Dari cloud dan aplikasi sampai jaringan kantor atau gedung, setiap area ditata agar implementasi dan operasional berjalan lebih rapi.
              </p>
            </div>

            <div class="reveal-stagger mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
              <article
                v-for="area in companyFocusAreas"
                :key="area.title"
                class="reveal-item rounded-2xl border border-slate-200/80 bg-white/80 px-4 py-4 shadow-[0_18px_60px_-42px_rgba(15,23,42,0.3)]"
              >
                <div class="public-icon-wrap public-icon-wrap-secondary h-10 w-10">
                  <component :is="resolveMarketingIcon(area.icon)" class="h-4.5 w-4.5" />
                </div>
                <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ area.title }}</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ area.description }}</p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <!-- Services -->
      <section id="services" class="public-section public-anchor">
        <div class="public-container">
          <div class="reveal public-section-heading mb-12">
            <span class="public-eyebrow mb-3">Profil Layanan</span>
            <h2 class="public-section-title mb-3">Kapabilitas perusahaan untuk cloud, aplikasi, jaringan, dan infrastruktur operasional.</h2>
            <p class="text-[0.9375rem] leading-relaxed text-slate-500">
              Banua Cloud Nusantara mendampingi kebutuhan perusahaan dari perencanaan, implementasi, konstruksi jaringan, sampai pengelolaan operasional agar fondasi teknologi lebih siap tumbuh.
            </p>
          </div>

          <div class="reveal-stagger grid gap-5 lg:grid-cols-2">
            <article
              v-for="service in serviceSnapshots"
              :key="service.slug"
              class="reveal-item public-card p-6 md:p-7"
            >
              <div class="flex items-start gap-4">
                <div class="public-icon-wrap public-icon-wrap-primary">
                  <component :is="resolveServiceIcon(service.icon)" class="h-5 w-5" />
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <h3 class="text-base font-semibold text-slate-900">{{ service.name }}</h3>
                      <p class="mt-0.5 text-xs text-slate-400">{{ service.plans.length || 1 }} opsi tersedia</p>
                    </div>
                    <span :class="service.usesContactPricing ? 'shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600' : 'shrink-0 rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700'">{{ service.startingPrice }}</span>
                  </div>
                </div>
              </div>

              <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ service.description }}</p>

              <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                <li
                  v-for="highlight in service.highlights"
                  :key="highlight"
                  class="flex items-center gap-2.5 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700"
                >
                  <Check class="h-3.5 w-3.5 shrink-0 text-teal-600" />
                  <span>{{ highlight }}</span>
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

      <!-- Advantages -->
      <section id="advantages" class="public-section public-anchor">
        <div class="public-container">
          <div class="reveal public-section-heading mb-12">
            <span class="public-eyebrow mb-3">Keunggulan</span>
            <h2 class="public-section-title mb-3">Tim Anda tetap fokus ke delivery, sementara infrastruktur ditangani oleh kami.</h2>
            <p class="text-[0.9375rem] leading-relaxed text-slate-500">
              Kami merapikan area yang paling sering membuat tim teknis terhambat: provisioning, keamanan, backup, dan kesiapan saat trafik berubah.
            </p>
          </div>

          <div class="reveal-stagger grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <article
              v-for="feature in featuredCapabilities"
              :key="feature.id"
              class="reveal-item public-card p-6"
            >
              <div class="public-icon-wrap public-icon-wrap-secondary">
                <component :is="resolveFeatureIcon(feature.icon)" class="h-5 w-5" />
              </div>
              <h3 class="mt-4 text-base font-semibold text-slate-900">{{ feature.title }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ feature.description }}</p>
            </article>
          </div>
        </div>
      </section>

      <section id="testimonials" class="public-section public-anchor">
        <div class="public-container">
          <div class="reveal public-section-heading mb-12">
            <span class="public-eyebrow mb-3">Testimoni</span>
            <h2 class="public-section-title mb-3">Apa kata klien yang sudah bekerja sama dengan kami.</h2>
            <p class="text-[0.9375rem] leading-relaxed text-slate-500">
              Masukan pelanggan kami umumnya berputar di tiga area: respon support, stabilitas layanan, dan kejelasan dalam scaling.
            </p>
          </div>

          <div class="reveal-stagger grid gap-5 lg:grid-cols-3">
            <article
              v-for="testimonial in featuredTestimonials"
              :key="testimonial.id"
              class="reveal-item public-card p-6"
            >
              <Quote class="h-5 w-5 text-teal-600/40 mb-3" />
              <p class="public-quote text-sm leading-relaxed text-slate-600">{{ testimonial.content }}</p>
              <div class="public-divider mt-5 pt-4">
                <p class="text-sm font-semibold text-slate-900">{{ testimonial.name }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ testimonial.role }} &middot; {{ testimonial.company }}</p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- Bottom CTA -->
      <section class="public-section pt-0">
        <div class="public-container">
          <div class="reveal public-card-muted overflow-hidden px-6 py-8 md:px-10 md:py-10">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
              <div>
                <span class="public-kicker mb-3">
                  <span class="h-1.5 w-1.5 rounded-full bg-orange-500" />
                  Mulai dari fondasi yang rapi
                </span>
                <h2 class="public-section-title mb-3">Bangun ulang layer cloud Anda dengan pendekatan yang lebih masuk akal.</h2>
                <p class="text-sm leading-relaxed text-slate-500">
                  Pilih area yang paling relevan untuk workload, backup, atau implementasi aplikasi Anda.
                </p>
              </div>

              <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                <button type="button" class="public-button public-button-primary" @click="goToLearnMore">
                  Lihat gambaran lengkap
                  <ArrowRight class="h-4 w-4" />
                </button>
                <button type="button" class="public-button public-button-secondary" @click="scrollToSection('contact')">
                  Hubungi tim
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <PublicFooter />
  </div>
</template>
