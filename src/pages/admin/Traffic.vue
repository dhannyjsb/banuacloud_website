<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Activity, RefreshCw } from 'lucide-vue-next';
import AdminTrafficOverview from '../../components/admin/AdminTrafficOverview.vue';
import { fetchAdminDashboard, type AdminDashboardPayload } from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const dashboard = ref<AdminDashboardPayload | null>(null);
const isLoading = ref(false);
const errorMessage = ref('');

const loadTraffic = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    dashboard.value = await fetchAdminDashboard(authStore.token);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load traffic analytics.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  void loadTraffic();
});
</script>

<template>
  <div class="space-y-6">
    <div class="rounded-[1.75rem] border border-white/10 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.18),transparent_30%),linear-gradient(145deg,rgba(15,23,42,0.96),rgba(2,6,23,0.92))] p-6 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.85)] lg:p-7">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
          <span class="inline-flex items-center gap-2 rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
            Traffic Monitoring
          </span>
          <div>
            <h1 class="text-2xl font-bold text-white">Analitik Pengunjung Website</h1>
            <p class="mt-1 max-w-3xl text-sm leading-relaxed text-slate-400">Halaman ini menampilkan traffic publik dari website Banua Cloud Nusantara, termasuk visitor unik, page views, sumber traffic, tren 7 hari terakhir, dan lokasi pengunjung.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Pengunjung Hari Ini</p>
            <p class="mt-3 text-2xl font-semibold text-white">{{ dashboard?.traffic.todayVisitors ?? 0 }}</p>
          </div>
          <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10" :disabled="isLoading" @click="loadTraffic">
            <RefreshCw class="h-4 w-4" :class="isLoading ? 'animate-spin' : ''" />
            Refresh
          </button>
        </div>
      </div>
    </div>

    <div v-if="errorMessage" class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
      {{ errorMessage }}
    </div>

    <div v-if="isLoading && !dashboard" class="rounded-xl border border-sky-500/20 bg-sky-500/10 px-4 py-3 text-sm text-sky-300">
      Memuat analitik traffic...
    </div>

    <AdminTrafficOverview :traffic="dashboard?.traffic" :is-loading="isLoading && !dashboard" />

    <section class="glass-md rounded-2xl border border-white/10 p-6">
      <div class="flex items-start gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-sky-500/20 bg-sky-500/10 text-sky-300">
          <Activity class="h-5 w-5" />
        </div>
        <div>
          <h2 class="text-base font-semibold text-white">Catatan Tracking</h2>
          <p class="mt-1 text-sm leading-6 text-slate-400">Traffic diambil dari route publik SPA, jadi perpindahan halaman publik tetap ikut tercatat sebagai kunjungan. Route admin tidak ikut dihitung ke analitik publik.</p>
        </div>
      </div>
    </section>
  </div>
</template>
