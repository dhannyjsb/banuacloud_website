<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowUpRight, Mail, MapPin, MessageCircleMore, Phone } from 'lucide-vue-next';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const route = useRoute();
const router = useRouter();
const { services, siteSettings } = useSiteBootstrap();
const currentYear = new Date().getFullYear();

const visibleServices = computed(() => services.value.filter((service) => service.enabled).slice(0, 4));
const whatsappLink = computed(() => {
  const raw = siteSettings.value.companyWhatsapp?.replace(/\D/g, '') || '';
  return raw ? `https://wa.me/${raw}` : '';
});

const navigateHomeSection = (hash: string) => {
  if (route.path === '/') {
    document.querySelector(hash)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    return;
  }

  void router.push({ path: '/', hash });
};

const goToService = (slug: string) => {
  void router.push(`/services/${slug}`);
};
</script>

<template>
  <footer id="contact" class="public-footer public-anchor">
    <div class="public-container">
      <div class="public-card overflow-hidden px-6 py-8 md:px-8 lg:px-10">
        <div class="grid gap-8 lg:grid-cols-[1.25fr_0.75fr] lg:items-end">
          <div>
            <span class="public-kicker mb-3">
              <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
              Siap Dipakai Tim Anda
            </span>
            <h2 class="public-section-title mb-3">
              Infrastruktur yang rapi, support yang jelas, dan delivery yang bisa dipertanggungjawabkan.
            </h2>
            <p class="public-copy max-w-2xl text-sm leading-relaxed">
              {{ siteSettings.companyName }} membantu tim Anda mengelola hosting, cloud, backup, dan implementasi teknis tanpa alur kerja yang berantakan.
            </p>
          </div>

          <div class="flex flex-wrap gap-3 lg:justify-end">
            <a :href="`mailto:${siteSettings.companyEmail}`" class="public-button public-button-secondary text-sm">
              <Mail class="h-3.5 w-3.5" />
              Email Tim
            </a>
            <a v-if="whatsappLink" :href="whatsappLink" class="public-button public-button-primary text-sm">
              <MessageCircleMore class="h-3.5 w-3.5" />
              WhatsApp
            </a>
          </div>
        </div>

        <div class="public-divider mt-8 pt-8">
          <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="space-y-4">
              <div class="public-logo">
                <span :class="['public-logo-mark overflow-hidden', siteSettings.logoUrl ? 'is-image h-6 w-6 rounded-md' : '']">
                  <img v-if="siteSettings.logoUrl" :src="siteSettings.logoUrl" :alt="siteSettings.siteName" class="h-full w-full object-cover" />
                  <span v-else>B</span>
                </span>
                <span>
                  <span class="block text-sm font-semibold text-slate-900">{{ siteSettings.siteName }}</span>
                  <span class="block text-xs text-slate-500">Cloud &amp; managed IT</span>
                </span>
              </div>
              <p class="public-copy text-xs leading-relaxed">
                {{ siteSettings.siteDescription }}
              </p>
            </div>

            <div>
              <p class="mb-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Navigasi</p>
              <div class="space-y-2.5 text-sm text-slate-600">
                <button type="button" class="block transition hover:text-slate-900" @click="navigateHomeSection('#services')">Layanan</button>
                <button type="button" class="block transition hover:text-slate-900" @click="navigateHomeSection('#advantages')">Keunggulan</button>
                <button type="button" class="block transition hover:text-slate-900" @click="navigateHomeSection('#testimonials')">Testimoni</button>
                <button type="button" class="block transition hover:text-slate-900" @click="navigateHomeSection('#contact')">Kontak</button>
              </div>
            </div>

            <div>
              <p class="mb-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Layanan Populer</p>
              <div class="space-y-2.5 text-sm text-slate-600">
                <button
                  v-for="service in visibleServices"
                  :key="service.slug"
                  type="button"
                  class="flex items-center gap-2 transition hover:text-slate-900"
                  @click="goToService(service.slug)"
                >
                  <ArrowUpRight class="h-3.5 w-3.5 text-teal-600" />
                  {{ service.name }}
                </button>
              </div>
            </div>

            <div>
              <p class="mb-4 text-xs font-semibold uppercase tracking-wider text-slate-400">Kontak</p>
              <div class="space-y-2.5 text-sm text-slate-600">
                <a :href="`mailto:${siteSettings.companyEmail}`" class="flex items-start gap-2.5 transition hover:text-slate-900">
                  <Mail class="mt-0.5 h-3.5 w-3.5 text-teal-600" />
                  <span>{{ siteSettings.companyEmail }}</span>
                </a>
                <a :href="`tel:${siteSettings.companyPhone}`" class="flex items-start gap-2.5 transition hover:text-slate-900">
                  <Phone class="mt-0.5 h-3.5 w-3.5 text-teal-600" />
                  <span>{{ siteSettings.companyPhone }}</span>
                </a>
                <div class="flex items-start gap-2.5">
                  <MapPin class="mt-0.5 h-3.5 w-3.5 text-teal-600" />
                  <span>{{ siteSettings.companyAddress }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-2 px-1 pt-5 text-xs text-slate-400 md:flex-row md:items-center md:justify-between">
        <p>&copy; {{ currentYear }} {{ siteSettings.companyName }}. Seluruh hak cipta dilindungi.</p>
        <p>Cloud, hosting &amp; implementasi yang lebih tertata.</p>
      </div>
    </div>
  </footer>
</template>
