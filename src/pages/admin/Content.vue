<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { Save, Image, Type, FileText, Eye, EyeOff, Plus, Trash2, GripVertical, Link } from 'lucide-vue-next';
import { emitSiteDataUpdated } from '../../composables/useSiteBootstrap';
import {
  type AdminMarketingCtaConfig,
  defaultFeatures,
  defaultHeroContent,
  defaultTestimonials,
  fetchAdminContent,
  updateAdminContent,
  type FeatureItem,
  type HeroContent,
  type TestimonialItem,
} from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const activeTab = ref('hero');
const isPreviewMode = ref(false);
const isSaving = ref(false);
const isLoading = ref(false);
const showSuccess = ref(false);
const errorMessage = ref('');
const authStore = useAuthStore();

const heroContent = reactive<HeroContent>({ ...defaultHeroContent });
const features = ref<FeatureItem[]>(defaultFeatures.map((feature) => ({ ...feature })));
const testimonials = ref<TestimonialItem[]>(defaultTestimonials.map((testimonial) => ({ ...testimonial })));
const marketingCtas = ref<AdminMarketingCtaConfig[]>([]);

const replaceFeatures = (items: FeatureItem[]) => {
  features.value = items.map((feature) => ({ ...feature }));
};

const replaceTestimonials = (items: TestimonialItem[]) => {
  testimonials.value = items.map((testimonial) => ({ ...testimonial }));
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
    replaceMarketingCtas(payload.marketingCtas || []);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load content.';
  } finally {
    isLoading.value = false;
  }
};

const addFeature = () => {
  const newId = Date.now().toString();
  features.value.push({
    id: newId,
    title: 'New Feature',
    description: 'Feature description here',
    icon: 'Star'
  });
};

const removeFeature = (id: string) => {
  const index = features.value.findIndex(f => f.id === id);
  if (index > -1) {
    features.value.splice(index, 1);
  }
};

const addTestimonial = () => {
  const newId = Date.now().toString();
  testimonials.value.push({
    id: newId,
    name: 'New Customer',
    role: 'Position',
    company: 'Company Name',
    content: 'Testimonial content here'
  });
};

const removeTestimonial = (id: string) => {
  const index = testimonials.value.findIndex(t => t.id === id);
  if (index > -1) {
    testimonials.value.splice(index, 1);
  }
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
      marketingCtas: marketingCtas.value.map((marketingCta) => ({ ...marketingCta })),
    });

    Object.assign(heroContent, payload.heroContent);
    replaceFeatures(payload.features);
    replaceTestimonials(payload.testimonials);
    replaceMarketingCtas(payload.marketingCtas || []);
    emitSiteDataUpdated();
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to save content.';
    isSaving.value = false;
    return;
  }

  isSaving.value = false;
  showSuccess.value = true;

  setTimeout(() => {
    showSuccess.value = false;
  }, 3000);
};

const tabs = [
  { id: 'hero', name: 'Hero Section', icon: Image },
  { id: 'features', name: 'Features', icon: Type },
  { id: 'testimonials', name: 'Testimonials', icon: FileText },
  { id: 'marketing', name: 'Marketing CTA', icon: Link },
];

onMounted(() => {
  void loadContent();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Content Management</h1>
        <p class="text-slate-400 mt-1">Manage your website content</p>
      </div>
      <div class="flex items-center gap-3">
        <button
          @click="isPreviewMode = !isPreviewMode"
          class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-colors"
        >
          <Eye v-if="!isPreviewMode" class="w-4 h-4" />
          <EyeOff v-else class="w-4 h-4" />
          {{ isPreviewMode ? 'Edit Mode' : 'Preview' }}
        </button>
        <button
          @click="handleSave"
          :disabled="isSaving || isLoading"
          class="flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500 text-white font-medium hover:bg-sky-600 disabled:opacity-50 transition-colors"
        >
          <Save class="w-4 h-4" />
          {{ isLoading ? 'Loading...' : isSaving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>

    <!-- Success Message -->
    <div
      v-if="showSuccess"
      class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400"
    >
      Changes saved successfully!
    </div>

    <div
      v-if="errorMessage"
      class="p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400"
    >
      {{ errorMessage }}
    </div>

    <div
      v-if="isLoading"
      class="p-4 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-300"
    >
      Loading content from the database...
    </div>

    <!-- Tabs -->
    <div class="flex gap-2 border-b border-white/10 pb-4">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors',
          activeTab === tab.id
            ? 'bg-sky-500/20 text-sky-400'
            : 'text-slate-400 hover:text-white hover:bg-white/5'
        ]"
      >
        <component :is="tab.icon" class="w-4 h-4" />
        {{ tab.name }}
      </button>
    </div>

    <!-- Hero Section -->
    <div v-if="activeTab === 'hero'" class="glass-md p-6 rounded-xl border border-white/10 space-y-6">
      <h3 class="text-lg font-semibold text-white">Hero Section</h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label class="text-sm font-medium text-slate-300">Title</label>
          <input
            v-model="heroContent.title"
            type="text"
            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
          />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium text-slate-300">Subtitle</label>
          <input
            v-model="heroContent.subtitle"
            type="text"
            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
          />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium text-slate-300">Primary CTA</label>
          <input
            v-model="heroContent.ctaPrimary"
            type="text"
            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
          />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium text-slate-300">Secondary CTA</label>
          <input
            v-model="heroContent.ctaSecondary"
            type="text"
            class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
          />
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div v-if="activeTab === 'features'" class="space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-white">Features</h3>
        <button
          @click="addFeature"
          class="flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500/20 text-sky-400 hover:bg-sky-500/30 transition-colors"
        >
          <Plus class="w-4 h-4" />
          Add Feature
        </button>
      </div>

      <div class="space-y-4">
        <div
          v-for="(feature, index) in features"
          :key="feature.id"
          class="glass-md p-6 rounded-xl border border-white/10"
        >
          <div class="flex items-start gap-4">
            <div class="cursor-move text-slate-500 hover:text-white mt-2">
              <GripVertical class="w-5 h-5" />
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="space-y-2">
                <label class="text-xs text-slate-500 uppercase">Title</label>
                <input
                  v-model="feature.title"
                  type="text"
                  class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500 text-sm"
                />
              </div>
              <div class="space-y-2 md:col-span-2">
                <label class="text-xs text-slate-500 uppercase">Description</label>
                <input
                  v-model="feature.description"
                  type="text"
                  class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500 text-sm"
                />
              </div>
            </div>
            <button
              @click="removeFeature(feature.id)"
              class="p-2 rounded-lg text-red-400 hover:bg-red-500/20 transition-colors"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Testimonials Section -->
    <div v-if="activeTab === 'testimonials'" class="space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-white">Testimonials</h3>
        <button
          @click="addTestimonial"
          class="flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500/20 text-sky-400 hover:bg-sky-500/30 transition-colors"
        >
          <Plus class="w-4 h-4" />
          Add Testimonial
        </button>
      </div>

      <div class="space-y-4">
        <div
          v-for="testimonial in testimonials"
          :key="testimonial.id"
          class="glass-md p-6 rounded-xl border border-white/10"
        >
          <div class="flex items-start gap-4">
            <div class="flex-1 space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                  <label class="text-xs text-slate-500 uppercase">Name</label>
                  <input
                    v-model="testimonial.name"
                    type="text"
                    class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500 text-sm"
                  />
                </div>
                <div class="space-y-2">
                  <label class="text-xs text-slate-500 uppercase">Role</label>
                  <input
                    v-model="testimonial.role"
                    type="text"
                    class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500 text-sm"
                  />
                </div>
                <div class="space-y-2">
                  <label class="text-xs text-slate-500 uppercase">Company</label>
                  <input
                    v-model="testimonial.company"
                    type="text"
                    class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500 text-sm"
                  />
                </div>
              </div>
              <div class="space-y-2">
                <label class="text-xs text-slate-500 uppercase">Content</label>
                <textarea
                  v-model="testimonial.content"
                  rows="3"
                  class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500 text-sm resize-none"
                />
              </div>
            </div>
            <button
              @click="removeTestimonial(testimonial.id)"
              class="p-2 rounded-lg text-red-400 hover:bg-red-500/20 transition-colors"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'marketing'" class="space-y-6">
      <div class="glass-md p-6 rounded-xl border border-white/10 space-y-2">
        <h3 class="text-lg font-semibold text-white">Marketing CTA Targets</h3>
        <p class="text-sm text-slate-400">
          Set CTA destinations for Learn More and every service detail page. Use internal routes like `/services/cloud-vps`, on-page anchors like `#service-pricing`, or full links such as `mailto:` and `tel:`.
        </p>
      </div>

      <div class="space-y-4">
        <div
          v-for="marketingCta in marketingCtas"
          :key="marketingCta.pageKey"
          class="glass-md p-6 rounded-xl border border-white/10 space-y-5"
        >
          <div class="flex items-center justify-between gap-4">
            <div>
              <h4 class="text-base font-semibold text-white">{{ marketingCta.pageTitle }}</h4>
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ marketingCta.pageKey }}</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-sky-500/10 border border-sky-500/20 text-xs font-medium text-sky-300">
              {{ marketingCta.supportsHeroCtas ? 'Hero + Footer CTA' : 'Footer CTA' }}
            </span>
          </div>

          <div v-if="marketingCta.supportsHeroCtas" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Hero Primary Target</label>
              <input
                v-model="marketingCta.heroPrimaryTarget"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Hero Secondary Target</label>
              <input
                v-model="marketingCta.heroSecondaryTarget"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Footer Primary Target</label>
              <input
                v-model="marketingCta.ctaPrimaryTarget"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Footer Secondary Target</label>
              <input
                v-model="marketingCta.ctaSecondaryTarget"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
