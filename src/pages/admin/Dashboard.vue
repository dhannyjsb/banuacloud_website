<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { FileText, History, MessageSquare, Package, RefreshCw } from 'lucide-vue-next';
import AdminTrafficOverview from '../../components/admin/AdminTrafficOverview.vue';
import { contactCategoryOptions, fetchAdminDashboard, inboxWorkflowOptions, type AdminDashboardPayload } from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const dashboard = ref<AdminDashboardPayload | null>(null);
const isLoading = ref(false);
const errorMessage = ref('');

const cards = computed(() => {
  const stats = dashboard.value?.stats;

  return [
    {
      title: 'Total Pesan Masuk',
      value: stats?.totalMessages ?? 0,
      helper: `${stats?.unreadMessages ?? 0} belum dibaca`,
      icon: MessageSquare,
      tone: 'sky',
    },
    {
      title: 'Perlu Follow Up',
      value: stats?.followUpMessages ?? 0,
      helper: 'Status baru atau sedang diproses',
      icon: History,
      tone: 'amber',
    },
    {
      title: 'Layanan Aktif',
      value: stats?.activeServices ?? 0,
      helper: `${stats?.testimonials ?? 0} testimonial aktif`,
      icon: Package,
      tone: 'emerald',
    },
    {
      title: 'Konten Profil',
      value: (stats?.faqs ?? 0) + (stats?.caseStudies ?? 0),
      helper: `${stats?.faqs ?? 0} FAQ, ${stats?.caseStudies ?? 0} case study`,
      icon: FileText,
      tone: 'violet',
    },
  ];
});

const toneClass = (tone: string) => {
  switch (tone) {
    case 'sky':
      return 'from-sky-500/20 to-sky-500/5 text-sky-300 border-sky-500/20';
    case 'amber':
      return 'from-amber-500/20 to-amber-500/5 text-amber-300 border-amber-500/20';
    case 'emerald':
      return 'from-emerald-500/20 to-emerald-500/5 text-emerald-300 border-emerald-500/20';
    default:
      return 'from-violet-500/20 to-violet-500/5 text-violet-300 border-violet-500/20';
  }
};

const workflowLabel = (key: string) => {
  return inboxWorkflowOptions.find((option) => option.value === key)?.label || key;
};

const categoryLabel = (key: string) => {
  return contactCategoryOptions.find((option) => option.value === key)?.label || key;
};

const workflowMax = computed(() => {
  const counts = dashboard.value?.workflow.map((item) => item.count) || [0];
  return Math.max(...counts, 1);
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

const loadDashboard = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    dashboard.value = await fetchAdminDashboard(authStore.token);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load dashboard.';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  void loadDashboard();
});
</script>

<template>
  <div class="space-y-6">
    <div class="rounded-[1.75rem] border border-white/10 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.16),transparent_32%),linear-gradient(145deg,rgba(15,23,42,0.96),rgba(2,6,23,0.92))] p-6 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.85)] lg:p-7">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
          <span class="inline-flex items-center gap-2 rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
            Dashboard
          </span>
          <div>
            <h1 class="text-2xl font-bold text-white">Ringkasan Operasional Admin</h1>
            <p class="mt-1 max-w-3xl text-sm leading-relaxed text-slate-400">Dashboard ini sekarang menggabungkan data inbox, konten, audit log, dan traffic publik yang masuk dari halaman website secara real time.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]" :class="dashboard?.stats.maintenanceMode ? 'border-amber-500/20 bg-amber-500/10 text-amber-200' : 'border-emerald-500/20 bg-emerald-500/10 text-emerald-200'">
            {{ dashboard?.stats.maintenanceMode ? 'Maintenance Mode Aktif' : 'Situs Publik Aktif' }}
          </span>
          <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10" :disabled="isLoading" @click="loadDashboard">
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
      Memuat dashboard dari database...
    </div>

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
      <article v-for="card in cards" :key="card.title" class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="flex items-start justify-between gap-4">
          <div :class="['flex h-12 w-12 items-center justify-center rounded-2xl border bg-gradient-to-br', toneClass(card.tone)]">
            <component :is="card.icon" class="h-5 w-5" />
          </div>
          <span class="text-xs uppercase tracking-[0.18em] text-slate-500">Real data</span>
        </div>
        <p class="mt-5 text-3xl font-semibold text-white">{{ card.value }}</p>
        <h2 class="mt-2 text-sm font-semibold text-slate-200">{{ card.title }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ card.helper }}</p>
      </article>
    </div>

    <AdminTrafficOverview :traffic="dashboard?.traffic" :is-loading="isLoading && !dashboard" detail-route="/admin/traffic" />

    <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
      <section class="glass-md rounded-2xl border border-white/10 p-6">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-white">Distribusi Workflow Inbox</h2>
            <p class="mt-1 text-sm text-slate-400">Pantau antrean follow up berdasarkan status yang aktif saat ini.</p>
          </div>
          <router-link to="/admin/inbox" class="text-sm font-medium text-sky-300 transition hover:text-sky-200">
            Buka inbox →
          </router-link>
        </div>

        <div class="mt-6 space-y-4">
          <div v-for="item in dashboard?.workflow || []" :key="item.key" class="space-y-2">
            <div class="flex items-center justify-between gap-4 text-sm">
              <span class="font-medium text-white">{{ workflowLabel(item.key) }}</span>
              <span class="text-slate-400">{{ item.count }}</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-white/5">
              <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400" :style="{ width: `${(item.count / workflowMax) * 100}%` }" />
            </div>
          </div>
        </div>

        <div class="public-divider mt-6 pt-6">
          <h3 class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Kategori Kebutuhan</h3>
          <div class="mt-4 flex flex-wrap gap-3">
            <div v-for="item in dashboard?.categories || []" :key="item.key" class="rounded-xl border border-white/10 bg-white/5 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ categoryLabel(item.key) }}</p>
              <p class="mt-2 text-lg font-semibold text-white">{{ item.count }}</p>
            </div>
          </div>
        </div>
      </section>

      <section class="glass-md rounded-2xl border border-white/10 overflow-hidden">
        <div class="border-b border-white/10 px-6 py-5">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h2 class="text-lg font-semibold text-white">Inbox Terbaru</h2>
              <p class="mt-1 text-sm text-slate-400">Pesan terbaru yang masuk dari form publik.</p>
            </div>
            <router-link to="/admin/inbox" class="text-sm font-medium text-sky-300 transition hover:text-sky-200">
              Lihat semua →
            </router-link>
          </div>
        </div>

        <div v-if="(dashboard?.recentMessages.length || 0) === 0" class="px-6 py-10 text-sm text-slate-400">
          Belum ada pesan yang masuk.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-white/5 text-left">
            <thead class="bg-white/[0.03]">
              <tr>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pengirim</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kategori</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Status</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Masuk</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr v-for="message in dashboard?.recentMessages || []" :key="message.id" class="hover:bg-white/[0.04] transition">
                <td class="px-5 py-4">
                  <p class="text-sm font-semibold text-white">{{ message.name }}</p>
                  <p class="mt-1 text-xs text-slate-500">{{ message.company || 'Tanpa perusahaan' }}</p>
                </td>
                <td class="px-5 py-4 text-sm text-slate-300">{{ categoryLabel(message.category) }}</td>
                <td class="px-5 py-4 text-sm text-slate-300">{{ workflowLabel(message.status) }}</td>
                <td class="px-5 py-4 text-sm text-slate-400">{{ formatDate(message.submittedAt) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <section class="glass-md rounded-2xl border border-white/10 overflow-hidden">
      <div class="border-b border-white/10 px-6 py-5">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-white">Aktivitas Admin Terbaru</h2>
            <p class="mt-1 text-sm text-slate-400">Ringkasan perubahan terakhir yang tercatat di audit log.</p>
          </div>
          <router-link to="/admin/audit-logs" class="text-sm font-medium text-sky-300 transition hover:text-sky-200">
            Audit log lengkap →
          </router-link>
        </div>
      </div>

      <div v-if="(dashboard?.recentAuditLogs.length || 0) === 0" class="px-6 py-10 text-sm text-slate-400">
        Belum ada aktivitas admin yang tercatat.
      </div>

      <div v-else class="divide-y divide-white/5">
        <article v-for="log in dashboard?.recentAuditLogs || []" :key="log.id" class="flex flex-col gap-2 px-6 py-4 md:flex-row md:items-start md:justify-between">
          <div>
            <p class="text-sm font-semibold text-white">{{ log.summary }}</p>
            <p class="mt-1 text-sm text-slate-400">{{ log.actorName }} · {{ log.target }} · {{ log.action }}</p>
          </div>
          <p class="text-sm text-slate-500">{{ formatDate(log.createdAt) }}</p>
        </article>
      </div>
    </section>
  </div>
</template>
