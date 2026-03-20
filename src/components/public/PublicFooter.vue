<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowUpRight, LoaderCircle, Mail, MapPin, Phone, SendHorizonal } from 'lucide-vue-next';
import { contactCategoryOptions, submitContactMessage } from '../../lib/siteApi';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const route = useRoute();
const router = useRouter();
const { services, siteSettings } = useSiteBootstrap();
const currentYear = new Date().getFullYear();

const visibleServices = computed(() => services.value.filter((service) => service.enabled).slice(0, 4));

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

const contactForm = reactive({
  name: '',
  email: '',
  whatsapp: '',
  company: '',
  category: 'konsultasi',
  message: '',
});

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const resetContactForm = () => {
  contactForm.name = '';
  contactForm.email = '';
  contactForm.whatsapp = '';
  contactForm.company = '';
  contactForm.category = 'konsultasi';
  contactForm.message = '';
};

const handleSubmit = async () => {
  isSubmitting.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const payload = await submitContactMessage({
      name: contactForm.name.trim(),
      email: contactForm.email.trim(),
      whatsapp: contactForm.whatsapp.trim(),
      company: contactForm.company.trim() || undefined,
      category: contactForm.category,
      message: contactForm.message.trim(),
    });

    successMessage.value = payload.message;
    resetContactForm();
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Pesan gagal dikirim.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <footer id="contact" class="public-footer public-anchor">
    <div class="public-container">
      <div class="public-card overflow-hidden px-6 py-8 md:px-8 lg:px-10">
        <div class="max-w-3xl">
          <span class="public-kicker mb-3">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
            Siap Dipakai Tim Anda
          </span>
          <h2 class="public-section-title mb-3">
            Infrastruktur yang rapi, support yang jelas, dan delivery yang bisa dipertanggungjawabkan.
          </h2>
          <p class="public-copy text-sm leading-relaxed">
            {{ siteSettings.companyName }} membantu tim Anda mengelola hosting, cloud, backup, dan implementasi teknis tanpa alur kerja yang berantakan.
          </p>
        </div>

        <div class="public-card-muted mt-8 p-5 md:p-6">
          <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <div class="space-y-3">
              <span class="public-eyebrow">Kotak Pesan</span>
              <h3 class="text-lg font-semibold tracking-tight text-slate-900 md:text-xl">Kirim kebutuhan Anda langsung ke inbox tim kami.</h3>
              <p class="public-copy text-sm leading-relaxed">
                Isi nama, email, nomor WhatsApp, perusahaan bila ada, lalu jelaskan kebutuhan Anda. Pesan yang masuk akan tampil di panel admin.
              </p>
            </div>

            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="handleSubmit">
              <label class="space-y-2">
                <span class="text-sm font-medium text-slate-700">Nama</span>
                <input
                  v-model="contactForm.name"
                  type="text"
                  required
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
                  placeholder="Nama lengkap"
                />
              </label>

              <label class="space-y-2">
                <span class="text-sm font-medium text-slate-700">Email</span>
                <input
                  v-model="contactForm.email"
                  type="email"
                  required
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
                  placeholder="nama@perusahaan.com"
                />
              </label>

              <label class="space-y-2">
                <span class="text-sm font-medium text-slate-700">No. WhatsApp</span>
                <input
                  v-model="contactForm.whatsapp"
                  type="text"
                  required
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
                  placeholder="08xxxxxxxxxx"
                />
              </label>

              <label class="space-y-2">
                <span class="text-sm font-medium text-slate-700">Perusahaan</span>
                <input
                  v-model="contactForm.company"
                  type="text"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
                  placeholder="Opsional"
                />
              </label>

              <label class="space-y-2 md:col-span-2">
                <span class="text-sm font-medium text-slate-700">Kategori kebutuhan</span>
                <select
                  v-model="contactForm.category"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
                >
                  <option
                    v-for="option in contactCategoryOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </label>

              <label class="space-y-2 md:col-span-2">
                <span class="text-sm font-medium text-slate-700">Pesan</span>
                <textarea
                  v-model="contactForm.message"
                  required
                  rows="5"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10"
                  placeholder="Jelaskan kebutuhan cloud, jaringan, aplikasi, atau konsultasi yang ingin Anda diskusikan."
                />
              </label>

              <div class="md:col-span-2 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="space-y-1 text-sm">
                  <p v-if="successMessage" class="font-medium text-emerald-600">{{ successMessage }}</p>
                  <p v-else-if="errorMessage" class="font-medium text-rose-600">{{ errorMessage }}</p>
                  <p v-else class="text-slate-500">Tim kami akan membalas melalui email atau WhatsApp yang Anda isi.</p>
                </div>

                <button type="submit" class="public-button public-button-primary min-w-44" :disabled="isSubmitting">
                  <LoaderCircle v-if="isSubmitting" class="h-4 w-4 animate-spin" />
                  <SendHorizonal v-else class="h-4 w-4" />
                  {{ isSubmitting ? 'Mengirim...' : 'Kirim Pesan' }}
                </button>
              </div>
            </form>
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
