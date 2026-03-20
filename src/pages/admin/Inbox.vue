<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import {
  Building2,
  CheckCheck,
  ChevronLeft,
  ChevronRight,
  Inbox,
  Mail,
  MessageSquareText,
  Phone,
  RefreshCw,
  Search,
  SlidersHorizontal,
} from 'lucide-vue-next';
import { fetchAdminInbox, markAdminInboxMessageRead, type InboxMessage } from '../../lib/siteApi';
import { useAuthStore } from '../../stores/auth';

type StatusFilter = 'all' | 'unread' | 'read';
type SortKey = 'submittedAt' | 'name' | 'company';
type SortDirection = 'asc' | 'desc';

const authStore = useAuthStore();
const messages = ref<InboxMessage[]>([]);
const isLoading = ref(false);
const errorMessage = ref('');
const selectedMessageId = ref<string | null>(null);
const isUpdatingMessageId = ref<string | null>(null);
const stats = ref({ total: 0, unread: 0 });
const searchQuery = ref('');
const statusFilter = ref<StatusFilter>('all');
const sortKey = ref<SortKey>('submittedAt');
const sortDirection = ref<SortDirection>('desc');
const rowsPerPage = ref(10);
const currentPage = ref(1);

const selectedMessage = computed(() => {
  return messages.value.find((message) => message.id === selectedMessageId.value) ?? null;
});

const readCount = computed(() => Math.max(0, stats.value.total - stats.value.unread));

const filteredMessages = computed(() => {
  const normalizedSearch = searchQuery.value.trim().toLowerCase();

  return messages.value.filter((message) => {
    if (statusFilter.value === 'unread' && message.isRead) {
      return false;
    }

    if (statusFilter.value === 'read' && !message.isRead) {
      return false;
    }

    if (!normalizedSearch) {
      return true;
    }

    return [
      message.name,
      message.email,
      message.whatsapp,
      message.company || '',
      message.message,
    ].some((value) => value.toLowerCase().includes(normalizedSearch));
  });
});

const sortedMessages = computed(() => {
  const items = [...filteredMessages.value];

  items.sort((left, right) => {
    const direction = sortDirection.value === 'asc' ? 1 : -1;

    if (sortKey.value === 'submittedAt') {
      const leftTime = left.submittedAt ? new Date(left.submittedAt).getTime() : 0;
      const rightTime = right.submittedAt ? new Date(right.submittedAt).getTime() : 0;

      return (leftTime - rightTime) * direction;
    }

    const leftValue = (left[sortKey.value] || '').toString().toLowerCase();
    const rightValue = (right[sortKey.value] || '').toString().toLowerCase();

    return leftValue.localeCompare(rightValue) * direction;
  });

  return items;
});

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(sortedMessages.value.length / rowsPerPage.value));
});

const paginatedMessages = computed(() => {
  const startIndex = (currentPage.value - 1) * rowsPerPage.value;

  return sortedMessages.value.slice(startIndex, startIndex + rowsPerPage.value);
});

const showingFrom = computed(() => {
  if (sortedMessages.value.length === 0) {
    return 0;
  }

  return (currentPage.value - 1) * rowsPerPage.value + 1;
});

const showingTo = computed(() => {
  return Math.min(currentPage.value * rowsPerPage.value, sortedMessages.value.length);
});

const hasActiveFilters = computed(() => {
  return searchQuery.value.trim() !== '' || statusFilter.value !== 'all';
});

const selectedWhatsappLink = computed(() => {
  const raw = selectedMessage.value?.whatsapp.replace(/\D/g, '') || '';

  return raw ? `https://wa.me/${raw}` : '';
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

const syncMessage = (updatedMessage: InboxMessage) => {
  messages.value = messages.value.map((message) => {
    return message.id === updatedMessage.id ? updatedMessage : message;
  });
};

const syncStats = () => {
  stats.value = {
    total: messages.value.length,
    unread: messages.value.filter((message) => !message.isRead).length,
  };
};

const loadInbox = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await fetchAdminInbox(authStore.token);
    messages.value = payload.messages;
    stats.value = payload.stats;
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to load inbox.';
  } finally {
    isLoading.value = false;
  }
};

const markMessageAsRead = async (message: InboxMessage) => {
  if (message.isRead) {
    return;
  }

  isUpdatingMessageId.value = message.id;

  try {
    await authStore.ensureAuthState();

    if (!authStore.token) {
      throw new Error('Admin session not found. Please sign in again.');
    }

    const payload = await markAdminInboxMessageRead(authStore.token, message.id);
    syncMessage(payload.message);
    syncStats();
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'Failed to update message status.';
  } finally {
    isUpdatingMessageId.value = null;
  }
};

const openMessage = async (message: InboxMessage) => {
  selectedMessageId.value = message.id;
  await markMessageAsRead(message);
};

const resetFilters = () => {
  searchQuery.value = '';
  statusFilter.value = 'all';
  sortKey.value = 'submittedAt';
  sortDirection.value = 'desc';
  rowsPerPage.value = 10;
  currentPage.value = 1;
};

const goToPreviousPage = () => {
  currentPage.value = Math.max(1, currentPage.value - 1);
};

const goToNextPage = () => {
  currentPage.value = Math.min(totalPages.value, currentPage.value + 1);
};

watch([searchQuery, statusFilter, sortKey, sortDirection, rowsPerPage], () => {
  currentPage.value = 1;
});

watch(totalPages, (value) => {
  if (currentPage.value > value) {
    currentPage.value = value;
  }
});

watch(sortedMessages, (items) => {
  if (items.length === 0) {
    selectedMessageId.value = null;
    return;
  }

  if (!items.some((message) => message.id === selectedMessageId.value)) {
    selectedMessageId.value = items[0].id;
  }
}, { immediate: true });

onMounted(() => {
  void loadInbox();
});
</script>

<template>
  <div class="space-y-8">
    <div class="rounded-[1.75rem] border border-white/10 bg-[radial-gradient(circle_at_top_left,rgba(14,165,233,0.16),transparent_32%),linear-gradient(145deg,rgba(15,23,42,0.96),rgba(2,6,23,0.92))] p-6 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.85)] lg:p-7">
      <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
          <span class="inline-flex items-center gap-2 rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-200">
            Inbox Admin
          </span>
          <div>
            <h1 class="text-2xl font-bold text-white">Kotak Masuk</h1>
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-slate-400">Pesan dari form kontak publik masuk ke halaman ini. Gunakan pencarian, filter, dan pagination untuk menelusuri pesan dengan pola datatable.</p>
          </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[28rem]">
          <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total</p>
            <p class="mt-3 text-2xl font-semibold text-white">{{ stats.total }}</p>
          </div>
          <div class="rounded-2xl border border-sky-500/20 bg-sky-500/10 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-200/80">Belum Dibaca</p>
            <p class="mt-3 text-2xl font-semibold text-white">{{ stats.unread }}</p>
          </div>
          <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-200/80">Sudah Dibaca</p>
            <p class="mt-3 text-2xl font-semibold text-white">{{ readCount }}</p>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="errorMessage"
      class="rounded-xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-300"
    >
      {{ errorMessage }}
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.28fr_0.72fr]">
      <section class="glass-md flex h-full min-h-[42rem] flex-col rounded-2xl border border-white/10 overflow-hidden">
        <header class="flex flex-col gap-5 border-b border-white/10 px-5 py-5 lg:px-6">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h2 class="text-base font-semibold text-white">Datatable Inbox</h2>
              <p class="mt-1 text-xs text-slate-400">Klik baris untuk membuka detail dan menandainya sebagai sudah dibaca.</p>
            </div>

            <button
              type="button"
              class="inline-flex items-center gap-2 self-start rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10"
              :disabled="isLoading"
              @click="loadInbox"
            >
              <RefreshCw class="h-4 w-4" :class="isLoading ? 'animate-spin' : ''" />
              Refresh
            </button>
          </div>

          <div class="grid gap-3 lg:grid-cols-[1.2fr_0.7fr_0.7fr_0.55fr_auto]">
            <label class="relative block">
              <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" />
              <input
                v-model="searchQuery"
                type="text"
                class="w-full rounded-xl border border-white/10 bg-white/5 py-3 pl-10 pr-4 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-sky-500/40 focus:bg-white/10"
                placeholder="Cari nama, email, WhatsApp, perusahaan, atau isi pesan"
              />
            </label>

            <label class="relative block">
              <SlidersHorizontal class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" />
              <select
                v-model="statusFilter"
                class="w-full appearance-none rounded-xl border border-white/10 bg-white/5 py-3 pl-10 pr-4 text-sm text-white outline-none transition focus:border-sky-500/40 focus:bg-white/10"
              >
                <option value="all" class="bg-slate-950 text-white">Semua status</option>
                <option value="unread" class="bg-slate-950 text-white">Belum dibaca</option>
                <option value="read" class="bg-slate-950 text-white">Sudah dibaca</option>
              </select>
            </label>

            <select
              v-model="sortKey"
              class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40 focus:bg-white/10"
            >
              <option value="submittedAt" class="bg-slate-950 text-white">Urutkan: Tanggal</option>
              <option value="name" class="bg-slate-950 text-white">Urutkan: Nama</option>
              <option value="company" class="bg-slate-950 text-white">Urutkan: Perusahaan</option>
            </select>

            <select
              v-model="sortDirection"
              class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white outline-none transition focus:border-sky-500/40 focus:bg-white/10"
            >
              <option value="desc" class="bg-slate-950 text-white">Terbaru dulu</option>
              <option value="asc" class="bg-slate-950 text-white">Terlama dulu</option>
            </select>

            <button
              v-if="hasActiveFilters"
              type="button"
              class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/10"
              @click="resetFilters"
            >
              Reset
            </button>
          </div>
        </header>

        <div v-if="isLoading" class="flex flex-1 items-center px-6 py-10 text-sm text-slate-400">Memuat datatable inbox...</div>

        <div v-else-if="sortedMessages.length === 0" class="flex flex-1 flex-col items-center justify-center gap-3 px-6 py-12 text-center">
          <Inbox class="h-10 w-10 text-slate-500" />
          <div>
            <h3 class="text-base font-semibold text-white">Tidak ada data yang cocok</h3>
            <p class="mt-1 text-sm text-slate-400">{{ hasActiveFilters ? 'Ubah pencarian atau filter untuk melihat pesan lain.' : 'Form kontak publik belum menerima pesan baru.' }}</p>
          </div>
        </div>

        <div v-else class="flex flex-1 flex-col">
          <div class="flex-1 overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5 text-left">
              <thead class="bg-white/[0.03]">
                <tr>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pengirim</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kontak</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Perusahaan</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ringkasan</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Status</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Masuk</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aksi</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-white/5">
                <tr
                  v-for="message in paginatedMessages"
                  :key="message.id"
                  :class="[
                    'cursor-pointer transition hover:bg-white/[0.04]',
                    selectedMessageId === message.id ? 'bg-sky-500/[0.08]' : ''
                  ]"
                  @click="openMessage(message)"
                >
                  <td class="px-5 py-4 align-top">
                    <div class="flex items-start gap-3">
                      <span :class="['mt-1 h-2.5 w-2.5 rounded-full', message.isRead ? 'bg-slate-600' : 'bg-emerald-400']" />
                      <div>
                        <p class="text-sm font-semibold text-white">{{ message.name }}</p>
                        <p class="mt-1 text-xs text-slate-500">ID #{{ message.id }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-4 align-top">
                    <p class="text-sm text-slate-200">{{ message.email }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ message.whatsapp }}</p>
                  </td>
                  <td class="px-5 py-4 align-top text-sm text-slate-300">
                    {{ message.company || '-' }}
                  </td>
                  <td class="px-5 py-4 align-top">
                    <p class="max-w-sm line-clamp-2 text-sm leading-6 text-slate-300">{{ message.message }}</p>
                  </td>
                  <td class="px-5 py-4 align-top">
                    <span :class="message.isRead ? 'inline-flex rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300' : 'inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200'">
                      {{ message.isRead ? 'Sudah dibaca' : 'Belum dibaca' }}
                    </span>
                  </td>
                  <td class="px-5 py-4 align-top text-sm text-slate-400">
                    {{ formatDate(message.submittedAt) }}
                  </td>
                  <td class="px-5 py-4 align-top">
                    <button
                      v-if="!message.isRead"
                      type="button"
                      class="inline-flex items-center gap-2 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-200 transition hover:bg-emerald-500/15"
                      :disabled="isUpdatingMessageId === message.id"
                      @click.stop="markMessageAsRead(message)"
                    >
                      <CheckCheck class="h-3.5 w-3.5" />
                      {{ isUpdatingMessageId === message.id ? 'Memproses...' : 'Tandai dibaca' }}
                    </button>
                    <span v-else class="text-xs font-medium text-slate-500">Terbaca</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <footer class="mt-auto flex flex-col gap-4 border-t border-white/10 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-3 text-sm text-slate-400 md:flex-row md:items-center">
              <span>Menampilkan {{ showingFrom }}-{{ showingTo }} dari {{ sortedMessages.length }} pesan</span>
              <label class="flex items-center gap-3">
                <span>Baris</span>
                <select
                  v-model="rowsPerPage"
                  class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white outline-none transition focus:border-sky-500/40"
                >
                  <option :value="5" class="bg-slate-950 text-white">5</option>
                  <option :value="10" class="bg-slate-950 text-white">10</option>
                  <option :value="20" class="bg-slate-950 text-white">20</option>
                </select>
              </label>
            </div>

            <div class="flex items-center gap-2 self-end">
              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="currentPage === 1"
                @click="goToPreviousPage"
              >
                <ChevronLeft class="h-4 w-4" />
                Sebelumnya
              </button>
              <span class="px-3 text-sm text-slate-400">Halaman {{ currentPage }} / {{ totalPages }}</span>
              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="currentPage === totalPages"
                @click="goToNextPage"
              >
                Berikutnya
                <ChevronRight class="h-4 w-4" />
              </button>
            </div>
          </footer>
        </div>
      </section>

      <section class="glass-md rounded-2xl border border-white/10 p-6 xl:sticky xl:top-6 self-start">
        <div v-if="selectedMessage" class="space-y-6">
          <div class="flex flex-wrap items-start justify-between gap-4 border-b border-white/10 pb-5">
            <div>
              <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-white">{{ selectedMessage.name }}</h2>
                <span :class="selectedMessage.isRead ? 'rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300' : 'rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-300'">
                  {{ selectedMessage.isRead ? 'Sudah dibaca' : 'Baru' }}
                </span>
              </div>
              <p class="mt-2 text-sm text-slate-400">Masuk pada {{ formatDate(selectedMessage.submittedAt) }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
              <a
                :href="`mailto:${selectedMessage.email}`"
                class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10"
              >
                <Mail class="h-4 w-4" />
                Email
              </a>
              <a
                v-if="selectedWhatsappLink"
                :href="selectedWhatsappLink"
                target="_blank"
                rel="noreferrer"
                class="inline-flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-2.5 text-sm font-medium text-emerald-200 transition hover:bg-emerald-500/15"
              >
                <Phone class="h-4 w-4" />
                WhatsApp
              </a>
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
              <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                <Mail class="h-3.5 w-3.5" />
                Email
              </div>
              <p class="mt-3 text-sm text-white">{{ selectedMessage.email }}</p>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
              <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                <Phone class="h-3.5 w-3.5" />
                WhatsApp
              </div>
              <p class="mt-3 text-sm text-white">{{ selectedMessage.whatsapp }}</p>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4 md:col-span-2">
              <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                <Building2 class="h-3.5 w-3.5" />
                Perusahaan
              </div>
              <p class="mt-3 text-sm text-white">{{ selectedMessage.company || '-' }}</p>
            </div>
          </div>

          <div class="rounded-2xl border border-white/10 bg-[#020617]/40 p-5">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
              <MessageSquareText class="h-3.5 w-3.5" />
              Pesan
            </div>
            <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-200">{{ selectedMessage.message }}</p>
          </div>

          <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-xs text-slate-500">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <span>Masuk: {{ formatDate(selectedMessage.submittedAt) }}</span>
              <span>Dibaca: {{ formatDate(selectedMessage.readAt) }}</span>
            </div>
          </div>
        </div>

        <div v-else class="flex min-h-72 flex-col items-center justify-center gap-3 text-center">
          <Inbox class="h-10 w-10 text-slate-500" />
          <div>
            <h3 class="text-base font-semibold text-white">Pilih pesan</h3>
            <p class="mt-1 text-sm text-slate-400">Detail pesan akan tampil di panel ini.</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
