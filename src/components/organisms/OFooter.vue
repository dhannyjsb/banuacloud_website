<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowRight, Cloud, Mail, Phone, Instagram, Linkedin, Twitter } from 'lucide-vue-next';
import AButton from '../atoms/AButton.vue';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const currentYear = new Date().getFullYear();
const router = useRouter();
const { siteSettings, services } = useSiteBootstrap();

const companyName = computed(() => siteSettings.value.siteName);
const companyFullName = computed(() => siteSettings.value.companyName);
const supportEmail = computed(() => siteSettings.value.companyEmail);
const companyDescriptor = computed(() => {
  const description = siteSettings.value.siteDescription?.trim();

  if (description === 'Trusted IT Solutions Partner in Indonesia') {
    return 'Mitra solusi IT tepercaya di Indonesia';
  }

  return description;
});
const supportPhone = computed(() => siteSettings.value.companyPhone);
const supportPhoneRaw = computed(() => siteSettings.value.companyPhone.replace(/[^\d+]/g, ''));
const companyAddress = computed(() => siteSettings.value.companyAddress);

const socialLinks = computed(() => {
  return [
    { icon: Instagram, href: siteSettings.value.socialInstagram, label: 'Instagram' },
    { icon: Linkedin, href: siteSettings.value.socialLinkedin, label: 'LinkedIn' },
    { icon: Twitter, href: siteSettings.value.socialTwitter, label: 'Twitter' },
  ].filter((link) => link.href);
});

const quickLinks = [
  { name: 'Beranda', href: '/' },
  { name: 'Jelajahi Layanan', href: '/learn-more' },
  { name: 'Admin Login', href: '/admin/login' },
];

const serviceLinks = computed(() => {
  return services.value.filter((service) => service.enabled).map((service) => ({
    name: service.name,
    href: `/services/${service.slug}`,
  }));
});

const goToLearnMore = () => {
  router.push('/learn-more');
};
</script>

<template>
  <footer id="contact" class="relative overflow-hidden border-t border-white/5 bg-[#08111f]/90">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.08),transparent_38%)]" />

    <div class="container-custom relative z-10 section-py">
      <div class="mb-12 rounded-[2rem] border border-white/10 bg-[linear-gradient(135deg,rgba(14,165,233,0.12),rgba(255,255,255,0.03))] p-6 shadow-[0_24px_90px_rgba(2,12,27,0.38)] md:p-8 lg:flex lg:items-center lg:justify-between">
        <div class="max-w-2xl">
          <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-sky-300">Siap Melangkah</p>
          <h3 class="mb-3 text-3xl font-bold text-white md:text-4xl">
            Infrastruktur yang siap dipakai hari ini, bukan nanti.
          </h3>
          <p class="text-base leading-8 text-slate-300">
            Diskusikan kebutuhan cloud, hosting, domain, atau modernisasi aplikasi Anda dengan tim Banua Cloud.
          </p>
        </div>

        <div class="mt-6 flex flex-col gap-3 lg:mt-0 lg:items-end">
          <a :href="`mailto:${supportEmail}`" class="text-sm text-sky-300 hover:text-sky-200 transition-colors">
            {{ supportEmail }}
          </a>
          <AButton variant="primary" size="md" @click="goToLearnMore">
            Jelajahi Layanan
            <ArrowRight class="w-4 h-4" />
          </AButton>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1.4fr_0.8fr_1fr_1fr]">
        <div>
          <a href="/" class="mb-5 flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center shadow-[0_14px_34px_rgba(14,165,233,0.28)]">
              <Cloud class="w-5 h-5 text-white" />
            </div>
            <div>
              <span class="block text-xl font-bold text-white">{{ companyName }}</span>
              <span class="text-xs uppercase tracking-[0.22em] text-slate-500">Mitra Infrastruktur Cloud</span>
            </div>
          </a>
          <p class="mb-6 max-w-md text-sm leading-7 text-slate-400">
            {{ companyDescriptor }}
          </p>

          <div class="rounded-[1.5rem] border border-white/8 bg-white/[0.03] p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi Operasional</p>
            <p class="mt-3 text-sm leading-7 text-slate-300">{{ companyAddress }}</p>
          </div>
        </div>

        <div>
          <h4 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Navigasi Cepat</h4>
          <ul class="space-y-3">
            <li v-for="link in quickLinks" :key="link.name">
              <a :href="link.href" class="text-sm text-slate-300 transition-colors hover:text-sky-300">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Layanan</h4>
          <ul class="space-y-3">
            <li v-for="link in serviceLinks" :key="link.name">
              <a :href="link.href" class="text-sm text-slate-300 transition-colors hover:text-sky-300">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Kontak</h4>
          <ul class="space-y-3">
            <li class="flex items-center gap-3 text-sm text-slate-300">
              <Mail class="w-4 h-4 text-sky-400" />
                <a :href="`mailto:${supportEmail}`" class="transition-colors hover:text-sky-300">
                {{ supportEmail }}
              </a>
            </li>
            <li class="flex items-center gap-3 text-sm text-slate-300">
              <Phone class="w-4 h-4 text-sky-400" />
                <a :href="`tel:${supportPhoneRaw}`" class="transition-colors hover:text-sky-300">
                {{ supportPhone }}
              </a>
            </li>
          </ul>

          <div class="flex items-center gap-3 mt-6">
            <a
              v-for="social in socialLinks"
              :key="social.label"
              :href="social.href"
              class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.03] text-slate-400 transition-all duration-300 hover:border-sky-400/30 hover:bg-sky-500/10 hover:text-sky-300"
              :aria-label="social.label"
            >
              <component :is="social.icon" class="w-4 h-4" />
            </a>
          </div>
        </div>
      </div>

      <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/5 pt-8 md:flex-row">
        <p class="text-sm text-slate-500">
          &copy; {{ currentYear }} {{ companyFullName }}. Semua hak dilindungi.
        </p>
        <p class="text-sm text-slate-600">
          Infrastruktur andal, inovasi terarah, dampak nyata.
        </p>
      </div>
    </div>
  </footer>
</template>
