<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { Save, Shield, Globe, Mail, Phone, MapPin, Bell, Lock } from 'lucide-vue-next';
import { emitSiteSettingsUpdated } from '../../composables/useSiteSettings';
import {
  defaultSiteSettings,
  fetchAdminSettings,
  type SiteSettings,
  updateAdminSettings,
} from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const isSaving = ref(false);
const isLoading = ref(false);
const showSuccess = ref(false);
const activeTab = ref('general');
const errorMessage = ref('');
const authStore = useAuthStore();

const settings = reactive<SiteSettings>({ ...defaultSiteSettings });

const loadSettings = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await fetchAdminSettings(authStore.token);
    Object.assign(settings, payload.settings);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load settings.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  void loadSettings();
});

const handleSave = async () => {
  isSaving.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await updateAdminSettings(authStore.token, {
      settings: { ...settings },
    });

    Object.assign(settings, payload.settings);
    emitSiteSettingsUpdated();
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to save settings.';
    isSaving.value = false;
    return;
  }

  isSaving.value = false;
  showSuccess.value = true;

  setTimeout(() => {
    showSuccess.value = false;
  }, 3000);
};

const resetSettings = () => {
  if (confirm('Reload the latest saved settings from the server?')) {
    void loadSettings();
  }
};

const tabs = [
  { id: 'general', name: 'General', icon: Globe },
  { id: 'company', name: 'Company', icon: MapPin },
  { id: 'social', name: 'Social Media', icon: Globe },
  { id: 'notifications', name: 'Notifications', icon: Bell },
  { id: 'security', name: 'Security', icon: Lock },
];
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white">Settings</h1>
        <p class="text-slate-400 mt-1">Manage your website settings</p>
      </div>
      <div class="flex items-center gap-3">
        <button
          @click="resetSettings"
          class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-colors"
        >
          Reload Saved
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
      Settings saved successfully!
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
      Loading settings from the database...
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Tabs -->
      <div class="glass-md p-4 rounded-xl border border-white/10 h-fit">
        <nav class="space-y-1">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors',
              activeTab === tab.id
                ? 'bg-sky-500/20 text-sky-400'
                : 'text-slate-400 hover:text-white hover:bg-white/5'
            ]"
          >
            <component :is="tab.icon" class="w-5 h-5" />
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <div class="lg:col-span-3 space-y-6">
        <!-- General Settings -->
        <div v-if="activeTab === 'general'" class="glass-md p-6 rounded-xl border border-white/10 space-y-6">
          <h3 class="text-lg font-semibold text-white">General Settings</h3>

          <!-- Maintenance Mode -->
          <div class="flex items-center justify-between p-4 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
            <div class="flex items-center gap-3">
              <Shield class="w-5 h-5 text-yellow-400" />
              <div>
                <p class="font-medium text-white">Maintenance Mode</p>
                <p class="text-sm text-slate-400">Show maintenance page to visitors</p>
              </div>
            </div>
            <button
              @click="settings.maintenanceMode = !settings.maintenanceMode"
              :class="[
                'w-12 h-6 rounded-full p-1 transition-colors',
                settings.maintenanceMode ? 'bg-yellow-500' : 'bg-slate-600'
              ]"
            >
              <div
                :class="[
                  'w-4 h-4 rounded-full bg-white transition-transform',
                  settings.maintenanceMode ? 'translate-x-6' : 'translate-x-0'
                ]"
              />
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Site Name</label>
              <input
                v-model="settings.siteName"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Site Description</label>
              <input
                v-model="settings.siteDescription"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
          </div>
        </div>

        <!-- Company Settings -->
        <div v-if="activeTab === 'company'" class="glass-md p-6 rounded-xl border border-white/10 space-y-6">
          <h3 class="text-lg font-semibold text-white">Company Information</h3>

          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-300">Company Name</label>
            <input
              v-model="settings.companyName"
              type="text"
              class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
            />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Email</label>
              <div class="relative">
                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" />
                <input
                  v-model="settings.companyEmail"
                  type="email"
                  class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
                />
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Phone</label>
              <div class="relative">
                <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" />
                <input
                  v-model="settings.companyPhone"
                  type="text"
                  class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
                />
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">WhatsApp</label>
              <input
                v-model="settings.companyWhatsapp"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Address</label>
              <input
                v-model="settings.companyAddress"
                type="text"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
          </div>
        </div>

        <!-- Social Media -->
        <div v-if="activeTab === 'social'" class="glass-md p-6 rounded-xl border border-white/10 space-y-6">
          <h3 class="text-lg font-semibold text-white">Social Media Links</h3>

          <div class="space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Instagram</label>
              <input
                v-model="settings.socialInstagram"
                type="url"
                placeholder="https://instagram.com/yourcompany"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">LinkedIn</label>
              <input
                v-model="settings.socialLinkedin"
                type="url"
                placeholder="https://linkedin.com/company/yourcompany"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Twitter / X</label>
              <input
                v-model="settings.socialTwitter"
                type="url"
                placeholder="https://x.com/yourcompany"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-300">Facebook</label>
              <input
                v-model="settings.socialFacebook"
                type="url"
                placeholder="https://facebook.com/yourcompany"
                class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
              />
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div v-if="activeTab === 'notifications'" class="glass-md p-6 rounded-xl border border-white/10 space-y-6">
          <h3 class="text-lg font-semibold text-white">Email Notifications</h3>

          <div class="space-y-4">
            <div class="flex items-center justify-between p-4 rounded-lg bg-white/5">
              <div>
                <p class="font-medium text-white">Email Notifications</p>
                <p class="text-sm text-slate-400">Receive email notifications</p>
              </div>
              <button
                @click="settings.emailNotifications = !settings.emailNotifications"
                :class="[
                  'w-12 h-6 rounded-full p-1 transition-colors',
                  settings.emailNotifications ? 'bg-sky-500' : 'bg-slate-600'
                ]"
              >
                <div
                  :class="[
                    'w-4 h-4 rounded-full bg-white transition-transform',
                    settings.emailNotifications ? 'translate-x-6' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>

            <div class="flex items-center justify-between p-4 rounded-lg bg-white/5">
              <div>
                <p class="font-medium text-white">Order Alerts</p>
                <p class="text-sm text-slate-400">Get notified for new orders</p>
              </div>
              <button
                @click="settings.orderAlerts = !settings.orderAlerts"
                :class="[
                  'w-12 h-6 rounded-full p-1 transition-colors',
                  settings.orderAlerts ? 'bg-sky-500' : 'bg-slate-600'
                ]"
              >
                <div
                  :class="[
                    'w-4 h-4 rounded-full bg-white transition-transform',
                    settings.orderAlerts ? 'translate-x-6' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>

            <div class="flex items-center justify-between p-4 rounded-lg bg-white/5">
              <div>
                <p class="font-medium text-white">Support Alerts</p>
                <p class="text-sm text-slate-400">Get notified for new support tickets</p>
              </div>
              <button
                @click="settings.supportAlerts = !settings.supportAlerts"
                :class="[
                  'w-12 h-6 rounded-full p-1 transition-colors',
                  settings.supportAlerts ? 'bg-sky-500' : 'bg-slate-600'
                ]"
              >
                <div
                  :class="[
                    'w-4 h-4 rounded-full bg-white transition-transform',
                    settings.supportAlerts ? 'translate-x-6' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>
          </div>
        </div>

        <!-- Security -->
        <div v-if="activeTab === 'security'" class="glass-md p-6 rounded-xl border border-white/10 space-y-6">
          <h3 class="text-lg font-semibold text-white">Security Settings</h3>

          <div class="flex items-center justify-between p-4 rounded-lg bg-white/5">
            <div>
              <p class="font-medium text-white">Two-Factor Authentication</p>
              <p class="text-sm text-slate-400">Add an extra layer of security</p>
            </div>
            <button
              @click="settings.twoFactorEnabled = !settings.twoFactorEnabled"
              :class="[
                'w-12 h-6 rounded-full p-1 transition-colors',
                settings.twoFactorEnabled ? 'bg-sky-500' : 'bg-slate-600'
              ]"
            >
              <div
                :class="[
                  'w-4 h-4 rounded-full bg-white transition-transform',
                  settings.twoFactorEnabled ? 'translate-x-6' : 'translate-x-0'
                ]"
              />
            </button>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-slate-300">Session Timeout (minutes)</label>
            <input
              v-model.number="settings.sessionTimeout"
              type="number"
              min="5"
              max="120"
              class="w-full px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-500"
            />
            <p class="text-xs text-slate-500">User will be logged out after this period of inactivity</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
