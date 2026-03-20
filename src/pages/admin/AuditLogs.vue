<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { History, RefreshCw, Search } from 'lucide-vue-next';
import { fetchAdminAuditLogs, type AuditLogEntry } from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const logs = ref<AuditLogEntry[]>([]);
const isLoading = ref(false);
const errorMessage = ref('');
const stats = ref({ total: 0, today: 0 });
const searchQuery = ref('');
const targetFilter = ref('all');

const availableTargets = computed(() => {
  return ['all', ...new Set(logs.value.map((log) => log.target))];
});

const filteredLogs = computed(() => {
  const search = searchQuery.value.trim().toLowerCase();

  return logs.value.filter((log) => {
    if (targetFilter.value !== 'all' && log.target !== targetFilter.value) {
      return false;
    }

    if (!search) {
      return true;
    }

    return [log.actorName, log.actorEmail, log.action, log.target, log.summary]
      .join(' ')
      .toLowerCase()
      .includes(search);
  });
});

const formatDate = (value?: string | null) => {
  if (!value) {
    return '-';
  }

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));
};

const loadLogs = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await fetchAdminAuditLogs(authStore.token);
    logs.value = payload.logs;
    stats.value = payload.stats;
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load audit logs.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  void loadLogs();
});
</script>

<template>
  <div class="space-y-6">
    <div class="rounded-[1.75rem] border border-white/10 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.16),transparent_32%),linear-gradient(145deg,rgba(15,23,42,0.96),rgba(2,6,23,0.92))] p-6 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.85)] lg:p-7">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
          <span class="inline-flex items-center gap-2 rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
            Audit Log
          </span>
          <div>
            <h1 class="text-2xl font-bold text-white">Riwayat Aktivitas Admin</h1>
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-slate-400">Pantau siapa yang mengubah content, settings, services, dan workflow inbox beserta waktunya.</p>
          </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[20rem]">
          <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total Log</p>
            <p class="mt-3 text-2xl font-semibold text-white">{{ stats.total }}</p>
          </div>
          <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-200/80">Hari Ini</p>
            <p class="mt-3 text-2xl font-semibold text-white">{{ stats.today }}</p>
          </div>
        </div>
      </div>
    </div>

    <div v-if="errorMessage" class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
      {{ errorMessage }}
    </div>

    <section class="glass-md rounded-2xl border border-white/10 overflow-hidden">
      <header class="flex flex-col gap-4 border-b border-white/10 px-5 py-5 lg:flex-row lg:items-center lg:justify-between lg:px-6">
        <div class="grid gap-3 lg:min-w-[38rem] lg:grid-cols-[1.3fr_0.6fr]">
          <label class="relative block">
            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" />
            <input v-model="searchQuery" type="text" class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-10 pr-4 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-sky-500/40" placeholder="Cari actor, target, action, atau ringkasan" />
          </label>

          <select v-model="targetFilter" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40">
            <option v-for="target in availableTargets" :key="target" :value="target" class="bg-slate-950 text-white">
              {{ target === 'all' ? 'Semua target' : target }}
            </option>
          </select>
        </div>

        <button type="button" class="inline-flex items-center gap-2 self-start rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10" :disabled="isLoading" @click="loadLogs">
          <RefreshCw class="h-4 w-4" :class="isLoading ? 'animate-spin' : ''" />
          Refresh
        </button>
      </header>

      <div v-if="isLoading" class="px-6 py-10 text-sm text-slate-400">Memuat audit log...</div>

      <div v-else-if="filteredLogs.length === 0" class="flex flex-col items-center justify-center gap-3 px-6 py-14 text-center">
        <History class="h-10 w-10 text-slate-500" />
        <div>
          <h2 class="text-base font-semibold text-white">Belum ada data audit</h2>
          <p class="mt-1 text-sm text-slate-400">Aktivitas admin akan muncul di sini setelah terjadi perubahan.</p>
        </div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/5 text-left">
          <thead class="bg-white/[0.03]">
            <tr>
              <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Waktu</th>
              <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Actor</th>
              <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Target</th>
              <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Action</th>
              <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ringkasan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-white/[0.04] transition">
              <td class="px-5 py-4 text-sm text-slate-300">{{ formatDate(log.createdAt) }}</td>
              <td class="px-5 py-4">
                <p class="text-sm font-medium text-white">{{ log.actorName }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ log.actorEmail }}</p>
              </td>
              <td class="px-5 py-4 text-sm text-slate-300">{{ log.target }}</td>
              <td class="px-5 py-4 text-sm text-slate-300">{{ log.action }}</td>
              <td class="px-5 py-4 text-sm leading-6 text-slate-300">{{ log.summary }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>
