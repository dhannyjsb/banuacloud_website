<script setup lang="ts">
import { computed, onMounted, onUnmounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { Save, Shield, Globe, Mail, Phone, MapPin, Bell, Lock, Upload, Loader2 } from 'lucide-vue-next';
import { emitSiteSettingsUpdated } from '../../composables/useSiteSettings';
import {
  defaultSiteSettings,
  fetchAdminSettings,
  updateAdminPassword,
  type SiteSettings,
  updateAdminLogo,
  updateAdminSettings,
} from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const route = useRoute();
const isSaving = ref(false);
const isLoading = ref(false);
const showSuccess = ref(false);
const activeTab = ref('general');
const errorMessage = ref('');
const logoErrorMessage = ref('');
const logoSuccessMessage = ref('');
const passwordErrorMessage = ref('');
const passwordSuccessMessage = ref('');
const isUpdatingPassword = ref(false);
const isUploadingLogo = ref(false);
const authStore = useAuthStore();
const logoInputRef = ref<HTMLInputElement | null>(null);
const selectedLogoFile = ref<File | null>(null);
const selectedLogoPreview = ref('');

const settings = reactive<SiteSettings>({ ...defaultSiteSettings });
const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  newPasswordConfirmation: '',
});

const tabs = [
  { id: 'general', name: 'General', icon: Globe },
  { id: 'company', name: 'Company', icon: MapPin },
  { id: 'social', name: 'Social Media', icon: Globe },
  { id: 'notifications', name: 'Notifications', icon: Bell },
  { id: 'security', name: 'Security', icon: Lock },
];

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

watch(
  () => route.query.tab,
  (tab) => {
    if (typeof tab === 'string' && tabs.some((item) => item.id === tab)) {
      activeTab.value = tab;
    }
  },
  { immediate: true },
);

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

const resetPasswordForm = () => {
  passwordForm.currentPassword = '';
  passwordForm.newPassword = '';
  passwordForm.newPasswordConfirmation = '';
};

const currentLogoPreview = computed(() => selectedLogoPreview.value || settings.logoUrl);

const clearLogoSelection = () => {
  if (selectedLogoPreview.value.startsWith('blob:')) {
    URL.revokeObjectURL(selectedLogoPreview.value);
  }

  selectedLogoPreview.value = '';
  selectedLogoFile.value = null;

  if (logoInputRef.value) {
    logoInputRef.value.value = '';
  }
};

const openLogoPicker = () => {
  logoInputRef.value?.click();
};

const handleLogoSelected = (event: Event) => {
  logoErrorMessage.value = '';
  logoSuccessMessage.value = '';

  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];

  if (!file) {
    clearLogoSelection();
    return;
  }

  if (!file.type.startsWith('image/')) {
    logoErrorMessage.value = 'File logo harus berupa gambar yang valid.';
    clearLogoSelection();
    return;
  }

  clearLogoSelection();
  selectedLogoFile.value = file;
  selectedLogoPreview.value = URL.createObjectURL(file);
};

const handleLogoUpload = async () => {
  logoErrorMessage.value = '';
  logoSuccessMessage.value = '';

  if (!selectedLogoFile.value) {
    logoErrorMessage.value = 'Pilih file logo terlebih dahulu.';
    return;
  }

  isUploadingLogo.value = true;

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await updateAdminLogo(authStore.token, selectedLogoFile.value);
    Object.assign(settings, payload.settings);
    emitSiteSettingsUpdated();
    clearLogoSelection();
    logoSuccessMessage.value = 'Logo perusahaan berhasil diperbarui.';
  } catch (error) {
    logoErrorMessage.value = error instanceof Error ? error.message : 'Failed to upload company logo.';
  } finally {
    isUploadingLogo.value = false;
  }
};

const handleChangePassword = async () => {
  isUpdatingPassword.value = true;
  passwordErrorMessage.value = '';
  passwordSuccessMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    if (passwordForm.newPassword !== passwordForm.newPasswordConfirmation) {
      throw new Error('New password confirmation does not match.');
    }

    const payload = await updateAdminPassword(authStore.token, {
      currentPassword: passwordForm.currentPassword,
      newPassword: passwordForm.newPassword,
      newPasswordConfirmation: passwordForm.newPasswordConfirmation,
    });

    passwordSuccessMessage.value = payload.message;
    resetPasswordForm();
  } catch (error) {
    passwordErrorMessage.value = error instanceof Error ? error.message : 'Failed to update password.';
  } finally {
    isUpdatingPassword.value = false;
  }
};

onUnmounted(() => {
  clearLogoSelection();
});

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

          <div class="space-y-4 rounded-xl border border-white/10 bg-white/5 p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
              <div class="flex items-center gap-4">
                <div class="flex h-[4.25rem] w-[4.25rem] items-center justify-center overflow-hidden rounded-[1.1rem] border border-white/10 bg-transparent">
                  <img v-if="currentLogoPreview" :src="currentLogoPreview" :alt="settings.siteName" class="h-full w-full scale-[1.00] object-cover" />
                  <span v-else class="text-3xl font-bold text-slate-500">B</span>
                </div>
                <div>
                  <p class="text-sm font-medium text-white">Logo Perusahaan</p>
                  <p class="mt-1 text-sm leading-relaxed text-slate-400">Upload logo utama untuk navbar, footer, maintenance page, dan halaman admin.</p>
                </div>
              </div>

              <div class="flex flex-wrap gap-3">
                <input
                  ref="logoInputRef"
                  type="file"
                  accept="image/png,image/jpeg,image/jpg,image/webp"
                  class="hidden"
                  @change="handleLogoSelected"
                />
                <button
                  type="button"
                  class="flex items-center gap-2 rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition-colors hover:bg-white/10"
                  @click="openLogoPicker"
                >
                  <Upload class="h-4 w-4" />
                  Pilih Logo
                </button>
                <button
                  type="button"
                  class="flex items-center gap-2 rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="isUploadingLogo || !selectedLogoFile"
                  @click="handleLogoUpload"
                >
                  <Loader2 v-if="isUploadingLogo" class="h-4 w-4 animate-spin" />
                  <Upload v-else class="h-4 w-4" />
                  {{ isUploadingLogo ? 'Uploading...' : 'Upload Logo' }}
                </button>
              </div>
            </div>

            <p v-if="selectedLogoFile" class="text-xs text-slate-400">File dipilih: {{ selectedLogoFile.name }}</p>
            <p v-if="logoSuccessMessage" class="text-sm text-green-400">{{ logoSuccessMessage }}</p>
            <p v-if="logoErrorMessage" class="text-sm text-red-400">{{ logoErrorMessage }}</p>
          </div>

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

          <div class="rounded-xl border border-white/10 bg-white/5 p-5 space-y-4">
            <div>
              <h4 class="text-base font-semibold text-white">Change Password</h4>
              <p class="text-sm text-slate-400 mt-1">Update your admin password securely.</p>
            </div>

            <div
              v-if="passwordSuccessMessage"
              class="rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400"
            >
              {{ passwordSuccessMessage }}
            </div>

            <div
              v-if="passwordErrorMessage"
              class="rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400"
            >
              {{ passwordErrorMessage }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2 md:col-span-2">
                <label class="text-sm font-medium text-slate-300">Current Password</label>
                <input
                  v-model="passwordForm.currentPassword"
                  type="password"
                  autocomplete="current-password"
                  class="w-full px-4 py-3 rounded-lg bg-[#0b1120] border border-white/10 text-white focus:outline-none focus:border-sky-500"
                />
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium text-slate-300">New Password</label>
                <input
                  v-model="passwordForm.newPassword"
                  type="password"
                  autocomplete="new-password"
                  class="w-full px-4 py-3 rounded-lg bg-[#0b1120] border border-white/10 text-white focus:outline-none focus:border-sky-500"
                />
                <p class="text-xs text-slate-500">Use at least 8 characters.</p>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium text-slate-300">Confirm New Password</label>
                <input
                  v-model="passwordForm.newPasswordConfirmation"
                  type="password"
                  autocomplete="new-password"
                  class="w-full px-4 py-3 rounded-lg bg-[#0b1120] border border-white/10 text-white focus:outline-none focus:border-sky-500"
                />
              </div>
            </div>

            <div class="flex justify-end">
              <button
                @click="handleChangePassword"
                :disabled="isUpdatingPassword"
                class="inline-flex items-center gap-2 rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-sky-600 disabled:opacity-50"
              >
                <Lock class="w-4 h-4" />
                {{ isUpdatingPassword ? 'Updating...' : 'Change Password' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
