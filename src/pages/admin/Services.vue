<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Save, Plus, Trash2, GripVertical } from 'lucide-vue-next';
import { emitSiteDataUpdated } from '../../composables/useSiteBootstrap';
import { resolveServiceIcon } from '../../lib/iconMaps';
import {
  defaultServices,
  fetchAdminServices,
  type SiteService,
  updateAdminServices,
} from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const activeService = ref('cloud-vps');
const isSaving = ref(false);
const isLoading = ref(false);
const showSuccess = ref(false);
const errorMessage = ref('');
const authStore = useAuthStore();

const services = ref<SiteService[]>(defaultServices.map((service) => ({ ...service, plans: service.plans.map((plan) => ({ ...plan, specs: { ...plan.specs }, features: [...plan.features] })) })));

const currentService = computed(() => services.value.find((service) => service.slug === activeService.value));

const replaceServices = (items: SiteService[]) => {
  services.value = items.map((service) => ({
    ...service,
    plans: service.plans.map((plan) => ({
      ...plan,
      specs: { ...plan.specs },
      features: [...plan.features],
    })),
  }));

  if (!services.value.find((service) => service.slug === activeService.value)) {
    activeService.value = services.value[0]?.slug || 'cloud-vps';
  }
};

const loadServices = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await fetchAdminServices(authStore.token);
    replaceServices(payload.services);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load services.';
  } finally {
    isLoading.value = false;
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

    const payload = await updateAdminServices(authStore.token, {
      services: services.value.map((service) => ({
        ...service,
        plans: service.plans.map((plan) => ({
          ...plan,
          specs: { ...plan.specs },
          features: [...plan.features],
        })),
      })),
    });

    replaceServices(payload.services);
    emitSiteDataUpdated();
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to save services.';
    isSaving.value = false;
    return;
  }

  isSaving.value = false;
  showSuccess.value = true;
  setTimeout(() => showSuccess.value = false, 3000);
};

const toggleServiceEnabled = (service: SiteService) => {
  service.enabled = !service.enabled;
};

const deletePlan = (service: SiteService, planId: string) => {
  const index = service.plans.findIndex(p => p.id === planId);
  if (index > -1) {
    service.plans.splice(index, 1);
  }
};

const addPlan = (service: SiteService) => {
  service.plans.push({
    id: `plan-${Date.now()}`,
    name: 'New Plan',
    price: 0,
    period: '/bulan',
    specs: { cpu: '1 Core', ram: '1GB', storage: '10GB', bandwidth: '100GB' },
    features: ['Feature 1'],
    popular: false,
    color: 'sky'
  });
};

onMounted(() => {
  void loadServices();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Services Management</h1>
        <p class="text-slate-400 mt-1">Manage your service offerings and pricing</p>
      </div>
      <button
        @click="handleSave"
        :disabled="isSaving || isLoading"
        class="flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-500 text-white font-medium hover:bg-sky-600 disabled:opacity-50 transition-colors"
      >
        <Save class="w-4 h-4" />
        {{ isLoading ? 'Loading...' : isSaving ? 'Saving...' : 'Save Changes' }}
      </button>
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
      Loading services from the database...
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Service List -->
      <div class="glass-md p-4 rounded-xl border border-white/10">
        <h3 class="text-sm font-medium text-slate-400 uppercase mb-4">Services</h3>
        <div class="space-y-2">
          <button
            v-for="service in services"
            :key="service.id"
            @click="activeService = service.slug"
            :class="[
              'w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors',
              activeService === service.slug
                ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30'
                : 'text-slate-300 hover:bg-white/5'
            ]"
          >
            <div class="flex items-center gap-3">
              <component :is="resolveServiceIcon(service.icon)" class="w-5 h-5" />
              <span class="font-medium">{{ service.name }}</span>
            </div>
            <div
              @click.stop="toggleServiceEnabled(service)"
              :class="[
                'w-8 h-5 rounded-full p-1 transition-colors cursor-pointer',
                service.enabled ? 'bg-sky-500' : 'bg-slate-600'
              ]"
            >
              <div
                :class="[
                  'w-3 h-3 rounded-full bg-white transition-transform',
                  service.enabled ? 'translate-x-3' : 'translate-x-0'
                ]"
              />
            </div>
          </button>
        </div>
      </div>

      <!-- Service Details -->
      <div class="lg:col-span-3 space-y-6">
        <div v-if="currentService" class="glass-md p-6 rounded-xl border border-white/10">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <component :is="resolveServiceIcon(currentService.icon)" class="w-8 h-8 text-sky-400" />
              <div>
                <h3 class="text-lg font-semibold text-white">{{ currentService.name }}</h3>
                <p class="text-sm text-slate-400">{{ currentService.description }}</p>
              </div>
            </div>
            <div
              :class="[
                'px-3 py-1 rounded-full text-xs font-medium',
                currentService.enabled ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'
              ]"
            >
              {{ currentService.enabled ? 'Active' : 'Disabled' }}
            </div>
          </div>

          <!-- Description -->
          <div class="space-y-2 mb-6">
            <label class="text-sm font-medium text-slate-300">Description</label>
            <input
              v-model="currentService.description"
              type="text"
              class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
            />
          </div>

          <!-- Plans (if applicable) -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-medium text-slate-300 uppercase">Pricing Plans</h4>
              <button
                @click="addPlan(currentService)"
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-sky-500/20 text-sky-400 text-sm hover:bg-sky-500/30 transition-colors"
              >
                <Plus class="w-4 h-4" />
                Add Plan
              </button>
            </div>

            <div class="space-y-4">
              <div
                v-for="plan in currentService.plans"
                :key="plan.id"
                class="p-4 rounded-lg bg-white/5 border border-white/10"
              >
                <div class="flex items-start gap-4">
                  <div class="cursor-move text-slate-500 hover:text-white mt-2">
                    <GripVertical class="w-5 h-5" />
                  </div>
                  <div class="flex-1 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">Plan Name</label>
                        <input
                          v-model="plan.name"
                          type="text"
                          class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500"
                        />
                      </div>
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">Price (Rp)</label>
                        <input
                          v-model.number="plan.price"
                          type="number"
                          class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500"
                        />
                      </div>
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">Popular</label>
                        <div class="flex items-center gap-2 h-[42px]">
                          <input
                            v-model="plan.popular"
                            type="checkbox"
                            class="w-4 h-4 rounded bg-white/5 border-white/20 text-sky-500"
                          />
                          <span class="text-sm text-slate-400">Mark as popular</span>
                        </div>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">CPU</label>
                        <input
                          v-model="plan.specs.cpu"
                          type="text"
                          class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500"
                        />
                      </div>
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">RAM</label>
                        <input
                          v-model="plan.specs.ram"
                          type="text"
                          class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500"
                        />
                      </div>
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">Storage</label>
                        <input
                          v-model="plan.specs.storage"
                          type="text"
                          class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500"
                        />
                      </div>
                      <div class="space-y-2">
                        <label class="text-xs text-slate-500 uppercase">Bandwidth</label>
                        <input
                          v-model="plan.specs.bandwidth"
                          type="text"
                          class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-white text-sm focus:outline-none focus:border-sky-500"
                        />
                      </div>
                    </div>
                  </div>
                  <button
                    @click="deletePlan(currentService, plan.id)"
                    class="p-2 rounded-lg text-red-400 hover:bg-red-500/20 transition-colors"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-if="!currentService.plans.length" class="text-center py-8 text-slate-500">
            <p>This service doesn't have pricing plans</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
