<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { Eye, EyeOff, FileText, GripVertical, Image, Link, Plus, Save, Trash2, Type } from 'lucide-vue-next';
import { emitSiteDataUpdated } from '../../composables/useSiteBootstrap';
import {
  contactCategoryOptions,
  defaultCaseStudies,
  defaultFaqs,
  defaultFeatures,
  defaultHeroContent,
  defaultTestimonials,
  fetchAdminContent,
  type AdminMarketingCtaConfig,
  type FeatureItem,
  type HeroContent,
  type MarketingCaseStudy,
  type MarketingFaq,
  type TestimonialItem,
  updateAdminContent,
} from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const activeTab = ref('hero');
const isPreviewMode = ref(false);
const isSaving = ref(false);
const isLoading = ref(false);
const showSuccess = ref(false);
const errorMessage = ref('');

const heroContent = reactive<HeroContent>({ ...defaultHeroContent });
const features = ref<FeatureItem[]>(defaultFeatures.map((feature) => ({ ...feature })));
const testimonials = ref<TestimonialItem[]>(defaultTestimonials.map((testimonial) => ({ ...testimonial })));
const faqs = ref<MarketingFaq[]>(defaultFaqs.map((faq) => ({ ...faq })));
const caseStudies = ref<MarketingCaseStudy[]>(defaultCaseStudies.map((caseStudy) => ({ ...caseStudy, tags: [...caseStudy.tags] })));
const caseStudiesEnabled = ref(true);
const marketingCtas = ref<AdminMarketingCtaConfig[]>([]);

const tabs = [
  { id: 'hero', name: 'Hero', icon: Image },
  { id: 'features', name: 'Features', icon: Type },
  { id: 'testimonials', name: 'Testimonials', icon: FileText },
  { id: 'caseStudies', name: 'Case Studies', icon: FileText },
  { id: 'faqs', name: 'FAQ', icon: FileText },
  { id: 'marketing', name: 'Marketing CTA', icon: Link },
];

const replaceFeatures = (items: FeatureItem[]) => {
  features.value = items.map((feature) => ({ ...feature }));
};

const replaceTestimonials = (items: TestimonialItem[]) => {
  testimonials.value = items.map((testimonial) => ({ ...testimonial }));
};

const replaceFaqs = (items: MarketingFaq[]) => {
  faqs.value = (items.length ? items : defaultFaqs).map((faq) => ({ ...faq }));
};

const replaceCaseStudies = (items: MarketingCaseStudy[]) => {
  caseStudies.value = (items.length ? items : defaultCaseStudies).map((caseStudy) => ({
    ...caseStudy,
    galleryImages: [...(caseStudy.galleryImages?.length ? caseStudy.galleryImages : ['/gallery/case-study-1.svg', '/gallery/case-study-2.svg', '/gallery/case-study-3.svg'])],
    tags: [...caseStudy.tags],
  }));
};

const replaceMarketingCtas = (items: AdminMarketingCtaConfig[]) => {
  marketingCtas.value = items.map((marketingCta) => ({ ...marketingCta }));
};

const loadContent = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await fetchAdminContent(authStore.token);
    Object.assign(heroContent, payload.heroContent);
    replaceFeatures(payload.features);
    replaceTestimonials(payload.testimonials);
    replaceFaqs(payload.faqs || []);
    replaceCaseStudies(payload.caseStudies || []);
    caseStudiesEnabled.value = payload.caseStudiesEnabled ?? true;
    replaceMarketingCtas(payload.marketingCtas || []);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load content.';
  } finally {
    isLoading.value = false;
  }
};

const addFeature = () => {
  features.value.push({
    id: Date.now().toString(),
    title: 'Fitur baru',
    description: 'Tulis deskripsi fitur di sini.',
    icon: 'Star',
  });
};

const removeFeature = (id: string) => {
  features.value = features.value.filter((feature) => feature.id !== id);
};

const addTestimonial = () => {
  testimonials.value.push({
    id: Date.now().toString(),
    name: 'Nama pelanggan',
    role: 'Jabatan',
    company: 'Perusahaan',
    content: 'Tulis testimonial di sini.',
  });
};

const removeTestimonial = (id: string) => {
  testimonials.value = testimonials.value.filter((testimonial) => testimonial.id !== id);
};

const addFaq = () => {
  faqs.value.push({
    question: 'Pertanyaan baru',
    answer: 'Jawaban baru',
  });
};

const removeFaq = (index: number) => {
  faqs.value.splice(index, 1);
};

const addCaseStudy = () => {
  caseStudies.value.push({
    title: 'Judul proyek',
    clientName: 'Nama klien',
    category: 'konsultasi',
    summary: 'Ringkasan singkat proyek.',
    challenge: 'Ringkasan singkat proyek.',
    solution: 'Ringkasan singkat proyek.',
    outcome: 'Ringkasan singkat proyek.',
    tags: ['Tag'],
    galleryImages: ['/gallery/case-study-1.svg', '/gallery/case-study-2.svg', '/gallery/case-study-3.svg'],
    isFeatured: false,
  });
};

const removeCaseStudy = (index: number) => {
  caseStudies.value.splice(index, 1);
};

const updateCaseStudyTags = (index: number, event: Event) => {
  const target = event.target as HTMLInputElement | null;
  const value = target?.value || '';

  caseStudies.value[index].tags = value
    .split(',')
    .map((tag) => tag.trim())
    .filter(Boolean);
};

const handleSave = async () => {
  isSaving.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await updateAdminContent(authStore.token, {
      heroContent: { ...heroContent },
      features: features.value.map((feature) => ({ ...feature })),
      testimonials: testimonials.value.map((testimonial) => ({ ...testimonial })),
      faqs: faqs.value.map((faq) => ({ ...faq })),
      caseStudies: caseStudies.value.map((caseStudy) => ({
        ...caseStudy,
        challenge: caseStudy.summary,
        solution: caseStudy.summary,
        outcome: caseStudy.summary,
        galleryImages: [...caseStudy.galleryImages],
        tags: [...caseStudy.tags],
      })),
      caseStudiesEnabled: caseStudiesEnabled.value,
      marketingCtas: marketingCtas.value.map((marketingCta) => ({ ...marketingCta })),
    });

    Object.assign(heroContent, payload.heroContent);
    replaceFeatures(payload.features);
    replaceTestimonials(payload.testimonials);
    replaceFaqs(payload.faqs || []);
    replaceCaseStudies(payload.caseStudies || []);
    caseStudiesEnabled.value = payload.caseStudiesEnabled ?? true;
    replaceMarketingCtas(payload.marketingCtas || []);
    emitSiteDataUpdated();
    showSuccess.value = true;

    setTimeout(() => {
      showSuccess.value = false;
    }, 3000);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to save content.';
  } finally {
    isSaving.value = false;
  }
};

onMounted(() => {
  void loadContent();
});
</script>

<template>
  <div class="space-y-6">
    <div class="rounded-[1.75rem] border border-white/10 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.16),transparent_32%),linear-gradient(145deg,rgba(15,23,42,0.96),rgba(2,6,23,0.92))] p-6 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.85)] lg:p-7">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
          <span class="inline-flex items-center gap-2 rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
            Content Admin
          </span>
          <div>
            <h1 class="text-2xl font-bold text-white">Content Management</h1>
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-slate-400">Kelola hero, feature, testimonial, FAQ, case study, dan CTA marketing dari satu panel agar profil perusahaan tetap konsisten di publik dan dashboard admin.</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10"
            @click="isPreviewMode = !isPreviewMode"
          >
            <Eye v-if="!isPreviewMode" class="h-4 w-4" />
            <EyeOff v-else class="h-4 w-4" />
            {{ isPreviewMode ? 'Mode Edit' : 'Preview Ringkas' }}
          </button>

          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="isSaving || isLoading"
            @click="handleSave"
          >
            <Save class="h-4 w-4" />
            {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="showSuccess" class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
      Perubahan konten berhasil disimpan.
    </div>

    <div v-if="errorMessage" class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
      {{ errorMessage }}
    </div>

    <div v-if="isLoading" class="rounded-xl border border-sky-500/20 bg-sky-500/10 px-4 py-3 text-sm text-sky-300">
      Memuat konten dari database...
    </div>

    <div class="flex flex-wrap gap-2 border-b border-white/10 pb-4">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        type="button"
        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium transition"
        :class="activeTab === tab.id ? 'bg-sky-500/20 text-sky-300' : 'bg-white/5 text-slate-300 hover:bg-white/10 hover:text-white'"
        @click="activeTab = tab.id"
      >
        <component :is="tab.icon" class="h-4 w-4" />
        {{ tab.name }}
      </button>
    </div>

    <div v-if="isPreviewMode" class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300">
      <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
          <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Features</p>
          <p class="mt-2 text-2xl font-semibold text-white">{{ features.length }}</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
          <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Testimonials</p>
          <p class="mt-2 text-2xl font-semibold text-white">{{ testimonials.length }}</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
          <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Case Studies</p>
          <p class="mt-2 text-2xl font-semibold text-white">{{ caseStudies.length }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ caseStudiesEnabled ? 'Section publik aktif' : 'Section publik nonaktif' }}</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
          <p class="text-xs uppercase tracking-[0.18em] text-slate-500">FAQ</p>
          <p class="mt-2 text-2xl font-semibold text-white">{{ faqs.length }}</p>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'hero'" class="glass-md space-y-6 rounded-2xl border border-white/10 p-6">
      <div>
        <h2 class="text-lg font-semibold text-white">Hero Section</h2>
        <p class="mt-1 text-sm text-slate-400">Atur pesan pembuka utama untuk halaman depan.</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <label class="space-y-2">
          <span class="text-sm font-medium text-slate-300">Title</span>
          <input v-model="heroContent.title" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
        </label>
        <label class="space-y-2">
          <span class="text-sm font-medium text-slate-300">Subtitle</span>
          <textarea v-model="heroContent.subtitle" rows="4" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
        </label>
        <label class="space-y-2">
          <span class="text-sm font-medium text-slate-300">Primary CTA</span>
          <input v-model="heroContent.ctaPrimary" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
        </label>
        <label class="space-y-2">
          <span class="text-sm font-medium text-slate-300">Secondary CTA</span>
          <input v-model="heroContent.ctaSecondary" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
        </label>
      </div>
    </div>

    <div v-if="activeTab === 'features'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-white">Features</h2>
          <p class="mt-1 text-sm text-slate-400">Sorot kapabilitas utama perusahaan.</p>
        </div>
        <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500/20 px-4 py-2.5 text-sm font-semibold text-sky-300 transition hover:bg-sky-500/30" @click="addFeature">
          <Plus class="h-4 w-4" />
          Tambah Feature
        </button>
      </div>

      <article v-for="feature in features" :key="feature.id" class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="flex items-start gap-4">
          <GripVertical class="mt-2 h-5 w-5 text-slate-500" />
          <div class="grid flex-1 gap-4 md:grid-cols-3">
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Title</span>
              <input v-model="feature.title" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Icon</span>
              <input v-model="feature.icon" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
            <label class="space-y-2 md:col-span-3">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Description</span>
              <textarea v-model="feature.description" rows="3" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
          </div>
          <button type="button" class="rounded-xl p-2 text-rose-300 transition hover:bg-rose-500/10" @click="removeFeature(feature.id)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </article>
    </div>

    <div v-if="activeTab === 'testimonials'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-white">Testimonials</h2>
          <p class="mt-1 text-sm text-slate-400">Tetap gunakan testimonial teks untuk social proof singkat di homepage.</p>
        </div>
        <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500/20 px-4 py-2.5 text-sm font-semibold text-sky-300 transition hover:bg-sky-500/30" @click="addTestimonial">
          <Plus class="h-4 w-4" />
          Tambah Testimonial
        </button>
      </div>

      <article v-for="testimonial in testimonials" :key="testimonial.id" class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="flex items-start gap-4">
          <div class="grid flex-1 gap-4 md:grid-cols-3">
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Name</span>
              <input v-model="testimonial.name" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Role</span>
              <input v-model="testimonial.role" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Company</span>
              <input v-model="testimonial.company" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
            <label class="space-y-2 md:col-span-3">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Content</span>
              <textarea v-model="testimonial.content" rows="4" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
          </div>
          <button type="button" class="rounded-xl p-2 text-rose-300 transition hover:bg-rose-500/10" @click="removeTestimonial(testimonial.id)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </article>
    </div>

    <div v-if="activeTab === 'caseStudies'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-white">Case Studies</h2>
          <p class="mt-1 text-sm text-slate-400">Gunakan blok ini untuk riwayat proyek, portofolio singkat, dan studi kasus.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <label class="inline-flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-slate-200">
            <input v-model="caseStudiesEnabled" type="checkbox" class="h-4 w-4 rounded border-white/10 bg-slate-950 text-sky-500" />
            Tampilkan di halaman publik
          </label>
          <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500/20 px-4 py-2.5 text-sm font-semibold text-sky-300 transition hover:bg-sky-500/30" @click="addCaseStudy">
            <Plus class="h-4 w-4" />
            Tambah Case Study
          </button>
        </div>
      </div>

      <div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
        Data riwayat proyek tetap tersimpan walau section ini dimatikan. Saat aktif, kartu publik memakai Ringkasan saja agar tampil lebih ringkas.
      </div>

      <article v-for="(caseStudy, index) in caseStudies" :key="`${caseStudy.title}-${index}`" class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="mb-5 flex items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-white">{{ caseStudy.title }}</h3>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-500">{{ caseStudy.clientName }}</p>
          </div>
          <button type="button" class="rounded-xl p-2 text-rose-300 transition hover:bg-rose-500/10" @click="removeCaseStudy(index)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <label class="space-y-2">
            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Judul</span>
            <input v-model="caseStudy.title" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
          <label class="space-y-2">
            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Klien</span>
            <input v-model="caseStudy.clientName" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
          <label class="space-y-2">
            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Kategori</span>
            <select v-model="caseStudy.category" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40">
              <option v-for="option in contactCategoryOptions" :key="option.value" :value="option.value" class="bg-slate-950 text-white">
                {{ option.label }}
              </option>
            </select>
          </label>
          <label class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-200">
            <input v-model="caseStudy.isFeatured" type="checkbox" class="h-4 w-4 rounded border-white/10 bg-slate-950 text-sky-500" />
            Tampilkan sebagai proyek unggulan
          </label>
          <label class="space-y-2 md:col-span-2">
            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Ringkasan</span>
            <textarea v-model="caseStudy.summary" rows="5" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
          <label class="space-y-2 md:col-span-2">
            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Tags</span>
            <input :value="caseStudy.tags.join(', ')" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" placeholder="Pisahkan dengan koma" @input="updateCaseStudyTags(index, $event)" />
          </label>
          <div class="space-y-3 md:col-span-2">
            <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Media / Galeri</span>
            <div class="grid gap-3 md:grid-cols-3">
              <label v-for="(_, imageIndex) in 3" :key="`${caseStudy.title}-${imageIndex}`" class="space-y-2">
                <span class="text-xs text-slate-500">Foto {{ imageIndex + 1 }}</span>
                <input v-model="caseStudy.galleryImages[imageIndex]" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" placeholder="/gallery/case-study-1.svg atau URL gambar" />
              </label>
            </div>
          </div>
        </div>
      </article>
    </div>

    <div v-if="activeTab === 'faqs'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold text-white">FAQ</h2>
          <p class="mt-1 text-sm text-slate-400">Kelola jawaban pertanyaan yang paling sering muncul di halaman profil perusahaan.</p>
        </div>
        <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500/20 px-4 py-2.5 text-sm font-semibold text-sky-300 transition hover:bg-sky-500/30" @click="addFaq">
          <Plus class="h-4 w-4" />
          Tambah FAQ
        </button>
      </div>

      <article v-for="(faq, index) in faqs" :key="`${faq.question}-${index}`" class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="flex items-start gap-4">
          <div class="grid flex-1 gap-4">
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Pertanyaan</span>
              <input v-model="faq.question" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
            <label class="space-y-2">
              <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Jawaban</span>
              <textarea v-model="faq.answer" rows="4" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
            </label>
          </div>
          <button type="button" class="rounded-xl p-2 text-rose-300 transition hover:bg-rose-500/10" @click="removeFaq(index)">
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </article>
    </div>

    <div v-if="activeTab === 'marketing'" class="space-y-4">
      <div class="glass-md rounded-2xl border border-white/10 p-6">
        <h2 class="text-lg font-semibold text-white">Marketing CTA Targets</h2>
        <p class="mt-1 text-sm leading-relaxed text-slate-400">Atur tujuan CTA untuk Learn More dan setiap detail layanan. Gunakan route internal seperti /services/cloud-vps, anchor seperti #contact, atau tautan penuh seperti mailto:.</p>
      </div>

      <article v-for="marketingCta in marketingCtas" :key="marketingCta.pageKey" class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="mb-5 flex items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-semibold text-white">{{ marketingCta.pageTitle }}</h3>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-500">{{ marketingCta.pageKey }}</p>
          </div>
          <span class="rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-300">
            {{ marketingCta.supportsHeroCtas ? 'Hero + Footer CTA' : 'Footer CTA' }}
          </span>
        </div>

        <div v-if="marketingCta.supportsHeroCtas" class="grid gap-4 md:grid-cols-2">
          <label class="space-y-2">
            <span class="text-sm font-medium text-slate-300">Hero Primary Target</span>
            <input v-model="marketingCta.heroPrimaryTarget" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
          <label class="space-y-2">
            <span class="text-sm font-medium text-slate-300">Hero Secondary Target</span>
            <input v-model="marketingCta.heroSecondaryTarget" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <label class="space-y-2">
            <span class="text-sm font-medium text-slate-300">Footer Primary Target</span>
            <input v-model="marketingCta.ctaPrimaryTarget" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
          <label class="space-y-2">
            <span class="text-sm font-medium text-slate-300">Footer Secondary Target</span>
            <input v-model="marketingCta.ctaSecondaryTarget" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40" />
          </label>
        </div>
      </article>
    </div>
  </div>
</template>
