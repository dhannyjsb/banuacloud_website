<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Activity, Globe2, MapPinned, Users } from 'lucide-vue-next';
import type { RecentVisitEntry, TrafficBreakdownItem, TrafficDashboardData } from '../../lib/siteApi';

type TrendMetric = 'pageViews' | 'visitors';

const props = withDefaults(defineProps<{
  traffic?: TrafficDashboardData | null;
  isLoading?: boolean;
  detailRoute?: string | null;
}>(), {
  traffic: null,
  isLoading: false,
  detailRoute: null,
});

const selectedVisitId = ref<string | null>(null);

const geolocationMode = computed(() => props.traffic?.geolocationMode ?? 'disabled');

const geolocationStatusLabel = computed(() => {
  switch (geolocationMode.value) {
    case 'web_service':
      return 'MaxMind API Aktif';
    case 'database':
      return 'MaxMind Database Aktif';
    default:
      return 'GeoIP Belum Aktif';
  }
});

const geolocationNotice = computed(() => {
  if (geolocationMode.value === 'web_service') {
    return '';
  }

  if (geolocationMode.value === 'database') {
    return 'Resolver masih memakai file database lokal. Jika ingin pakai API, isi MAXMIND_ACCOUNT_ID dan MAXMIND_LICENSE_KEY atau MAXMIND_API_KEY di environment.';
  }

  return 'Isi MAXMIND_ACCOUNT_ID dan MAXMIND_LICENSE_KEY atau MAXMIND_API_KEY untuk MaxMind API. File .mmdb tetap bisa dipakai sebagai fallback opsional.';
});

const trafficCards = computed(() => {
  const traffic = props.traffic;

  return [
    {
      title: 'Pengunjung Hari Ini',
      value: traffic?.todayVisitors ?? 0,
      helper: 'Visitor unik dari token browser',
      icon: Users,
      tone: 'sky',
    },
    {
      title: 'Page Views Hari Ini',
      value: traffic?.todayPageViews ?? 0,
      helper: 'Semua page visit publik yang terekam',
      icon: Activity,
      tone: 'amber',
    },
    {
      title: 'Sumber Teratas',
      value: traffic?.topSources[0]?.label ?? '-',
      helper: `${traffic?.topSources[0]?.count ?? 0} kunjungan hari ini`,
      icon: Globe2,
      tone: 'emerald',
    },
    {
      title: 'Lokasi Teratas',
      value: traffic?.topCountries[0]?.label ?? '-',
      helper: traffic?.geolocationMode === 'web_service'
        ? 'Diambil dari MaxMind API'
        : traffic?.geolocationMode === 'database'
          ? 'Diambil dari database MaxMind lokal'
          : 'Menunggu kredensial MaxMind',
      icon: MapPinned,
      tone: 'violet',
    },
  ];
});

const trafficTrendMax = computed(() => {
  const values = (props.traffic?.dailyTrend || []).reduce<number[]>((items, point) => {
    items.push(point.pageViews, point.visitors);
    return items;
  }, []);

  return Math.max(...values, 1);
});

const trendSeries = computed(() => props.traffic?.dailyTrend || []);

const trendChartWidth = 640;
const trendChartHeight = 260;
const trendChartPaddingX = 24;
const trendChartPaddingTop = 20;
const trendChartPaddingBottom = 40;
const trendChartInnerWidth = trendChartWidth - trendChartPaddingX * 2;
const trendChartInnerHeight = trendChartHeight - trendChartPaddingTop - trendChartPaddingBottom;

const trendGridLines = computed(() => {
  const max = trafficTrendMax.value;

  return [1, 0.66, 0.33, 0].map((ratio) => ({
    y: trendChartPaddingTop + (1 - ratio) * trendChartInnerHeight,
    value: Math.round(max * ratio),
  }));
});

const trendPointX = (index: number, total: number) => {
  if (total <= 1) {
    return trendChartWidth / 2;
  }

  return trendChartPaddingX + (index * trendChartInnerWidth) / (total - 1);
};

const trendPointY = (value: number, max: number) => {
  return trendChartPaddingTop + (1 - value / max) * trendChartInnerHeight;
};

const trendPath = (metric: TrendMetric) => {
  if (trendSeries.value.length === 0) {
    return '';
  }

  const max = trafficTrendMax.value;

  return trendSeries.value
    .map((point, index) => `${index === 0 ? 'M' : 'L'} ${trendPointX(index, trendSeries.value.length)} ${trendPointY(point[metric], max)}`)
    .join(' ');
};

const trendAreaPath = (metric: TrendMetric) => {
  if (trendSeries.value.length === 0) {
    return '';
  }

  const max = trafficTrendMax.value;
  const baselineY = trendChartPaddingTop + trendChartInnerHeight;
  const startX = trendPointX(0, trendSeries.value.length);
  const endX = trendPointX(trendSeries.value.length - 1, trendSeries.value.length);
  const linePath = trendPath(metric);

  return `${linePath} L ${endX} ${baselineY} L ${startX} ${baselineY} Z`;
};

const trendDots = (metric: TrendMetric) => {
  const max = trafficTrendMax.value;

  return trendSeries.value.map((point, index) => ({
    key: `${metric}-${point.date}`,
    x: trendPointX(index, trendSeries.value.length),
    y: trendPointY(point[metric], max),
    value: point[metric],
  }));
};

const selectedVisit = computed(() => {
  return props.traffic?.recentVisits.find((visit) => visit.id === selectedVisitId.value) ?? null;
});

watch(() => props.traffic?.recentVisits || [], (visits) => {
  if (visits.length === 0) {
    selectedVisitId.value = null;
    return;
  }

  if (!visits.some((visit) => visit.id === selectedVisitId.value)) {
    selectedVisitId.value = visits[0].id;
  }
}, { immediate: true });

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

const breakdownMax = (items?: TrafficBreakdownItem[]) => {
  return Math.max(...(items?.map((item) => item.count) || [0]), 1);
};

const scaleWidth = (value: number, max: number) => {
  return `${(value / max) * 100}%`;
};

const openVisitDetail = (visit: RecentVisitEntry) => {
  selectedVisitId.value = visit.id;
};

const displayValue = (value?: string | null) => {
  return value && value.trim() ? value : '-';
};

const formatDate = (value?: string | null) => {
  if (!value) {
    return '-';
  }

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(value));
};
</script>

<template>
  <section class="glass-md rounded-2xl border border-white/10 p-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
      <div>
        <h2 class="text-lg font-semibold text-white">Traffic Monitoring Publik</h2>
        <p class="mt-1 max-w-3xl text-sm text-slate-400">Pantau visitor hari ini, sumber traffic, dan asal pengunjung dari route publik SPA.</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]" :class="traffic?.geolocationEnabled ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-200' : 'border-amber-500/20 bg-amber-500/10 text-amber-200'">
          {{ geolocationStatusLabel }}
        </span>
        <router-link v-if="detailRoute" :to="detailRoute" class="inline-flex items-center rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10">
          Buka halaman →
        </router-link>
      </div>
    </div>

    <div v-if="geolocationNotice" class="mt-5 rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">
      {{ geolocationNotice }}
    </div>

    <div v-if="isLoading && !traffic" class="mt-6 rounded-xl border border-sky-500/20 bg-sky-500/10 px-4 py-3 text-sm text-sky-300">
      Memuat data traffic dari database...
    </div>

    <template v-else>
      <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        <article v-for="card in trafficCards" :key="card.title" class="rounded-2xl border border-white/10 bg-white/[0.03] p-5">
          <div class="flex items-start justify-between gap-4">
            <div :class="['flex h-11 w-11 items-center justify-center rounded-2xl border bg-gradient-to-br', toneClass(card.tone)]">
              <component :is="card.icon" class="h-5 w-5" />
            </div>
            <span class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Hari ini</span>
          </div>
          <p class="mt-4 text-2xl font-semibold text-white">{{ card.value }}</p>
          <h3 class="mt-2 text-sm font-semibold text-slate-200">{{ card.title }}</h3>
          <p class="mt-1 text-sm text-slate-500">{{ card.helper }}</p>
        </article>
      </div>

      <div class="mt-6 grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
        <section class="rounded-2xl border border-white/10 bg-white/[0.02] p-6">
          <div>
            <h3 class="text-base font-semibold text-white">Trend 7 Hari Terakhir</h3>
            <p class="mt-1 text-sm text-slate-400">Bandingkan page views dan visitor unik untuk melihat pergerakan traffic publik.</p>
          </div>

          <div v-if="(traffic?.dailyTrend.length || 0) === 0" class="mt-6 text-sm text-slate-400">
            Belum ada data traffic yang terekam.
          </div>

          <div v-else class="mt-6">
            <div class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-4">
              <svg class="h-72 w-full" :viewBox="`0 0 ${trendChartWidth} ${trendChartHeight}`" fill="none" role="img" aria-label="Grafik tren traffic 7 hari terakhir">
                <g>
                  <line v-for="grid in trendGridLines" :key="grid.value + '-' + grid.y" :x1="trendChartPaddingX" :x2="trendChartWidth - trendChartPaddingX" :y1="grid.y" :y2="grid.y" stroke="rgba(148, 163, 184, 0.14)" stroke-dasharray="4 6" />
                  <text v-for="grid in trendGridLines" :key="'label-' + grid.value + '-' + grid.y" x="8" :y="grid.y + 4" fill="rgba(148, 163, 184, 0.72)" font-size="11">
                    {{ grid.value }}
                  </text>
                </g>

                <path :d="trendAreaPath('pageViews')" fill="rgba(56, 189, 248, 0.16)" />
                <path :d="trendAreaPath('visitors')" fill="rgba(16, 185, 129, 0.12)" />
                <path :d="trendPath('pageViews')" stroke="rgb(56, 189, 248)" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
                <path :d="trendPath('visitors')" stroke="rgb(16, 185, 129)" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />

                <g v-for="point in trendDots('pageViews')" :key="point.key">
                  <circle :cx="point.x" :cy="point.y" r="4.5" fill="rgb(56, 189, 248)" />
                  <circle :cx="point.x" :cy="point.y" r="8" fill="rgba(56, 189, 248, 0.18)" />
                </g>

                <g v-for="point in trendDots('visitors')" :key="point.key">
                  <circle :cx="point.x" :cy="point.y" r="4.5" fill="rgb(16, 185, 129)" />
                  <circle :cx="point.x" :cy="point.y" r="8" fill="rgba(16, 185, 129, 0.16)" />
                </g>

                <text v-for="(point, index) in trendSeries" :key="point.date" :x="trendPointX(index, trendSeries.length)" :y="trendChartHeight - 12" fill="rgba(226, 232, 240, 0.86)" font-size="11" text-anchor="middle">
                  {{ point.label }}
                </text>
              </svg>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
              <span class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-sky-400" />
                Page Views
              </span>
              <span class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-400" />
                Visitor Unik
              </span>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
              <div v-for="point in trendSeries" :key="'summary-' + point.date" class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ point.label }}</p>
                <p class="mt-2 text-sm text-white">{{ point.pageViews }} page views</p>
                <p class="mt-1 text-sm text-slate-400">{{ point.visitors }} visitor unik</p>
              </div>
            </div>
          </div>
        </section>

        <div class="grid gap-6">
          <section class="rounded-2xl border border-white/10 bg-white/[0.02] p-6">
            <h3 class="text-base font-semibold text-white">Sumber Traffic</h3>
            <p class="mt-1 text-sm text-slate-400">Referrer dan UTM source yang paling banyak mengirim visitor hari ini.</p>

            <div v-if="(traffic?.topSources.length || 0) === 0" class="mt-6 text-sm text-slate-400">
              Belum ada sumber traffic yang tercatat.
            </div>

            <div v-else class="mt-6 space-y-4">
              <div v-for="item in traffic?.topSources || []" :key="item.label" class="space-y-2">
                <div class="flex items-center justify-between gap-4 text-sm">
                  <span class="font-medium text-white">{{ item.label }}</span>
                  <span class="text-slate-400">{{ item.count }}</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/5">
                  <div class="h-full rounded-full bg-gradient-to-r from-fuchsia-500 to-sky-400" :style="{ width: scaleWidth(item.count, breakdownMax(traffic?.topSources)) }" />
                </div>
              </div>
            </div>
          </section>

          <section class="rounded-2xl border border-white/10 bg-white/[0.02] p-6">
            <h3 class="text-base font-semibold text-white">Asal Pengunjung</h3>
            <p class="mt-1 text-sm text-slate-400">Negara teratas dan kota yang berhasil dikenali dari IP publik.</p>

            <div v-if="(traffic?.topCountries.length || 0) === 0" class="mt-6 text-sm text-slate-400">
              Belum ada data lokasi yang bisa ditampilkan.
            </div>

            <div v-else class="mt-6 space-y-4">
              <div v-for="item in traffic?.topCountries || []" :key="item.label" class="space-y-2">
                <div class="flex items-center justify-between gap-4 text-sm">
                  <span class="font-medium text-white">{{ item.label }}</span>
                  <span class="text-slate-400">{{ item.count }}</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/5">
                  <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-lime-400" :style="{ width: scaleWidth(item.count, breakdownMax(traffic?.topCountries)) }" />
                </div>
              </div>
            </div>

            <div v-if="(traffic?.topCities.length || 0) > 0" class="public-divider mt-6 pt-6">
              <h4 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kota Teratas</h4>
              <div class="mt-3 flex flex-wrap gap-2">
                <span v-for="item in traffic?.topCities || []" :key="item.label" class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-slate-300">
                  {{ item.label }} · {{ item.count }}
                </span>
              </div>
            </div>
          </section>
        </div>
      </div>

      <section class="mt-6 rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
        <div class="border-b border-white/10 px-6 py-5">
          <h3 class="text-base font-semibold text-white">Most Visit IP</h3>
          <p class="mt-1 text-sm text-slate-400">IP yang paling sering muncul dari traffic yang sudah terekam, lengkap dengan browser terakhir dan lokasi terakhir yang dikenali.</p>
        </div>

        <div v-if="(traffic?.mostVisitedIps.length || 0) === 0" class="px-6 py-10 text-sm text-slate-400">
          Belum ada data IP yang cukup untuk dirangkum.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-white/5 text-left">
            <thead class="bg-white/[0.03]">
              <tr>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">IP</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total Visit</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Visitor Unik</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Browser Terakhir</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Negara</th>
                <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Terakhir Terlihat</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <tr v-for="entry in traffic?.mostVisitedIps || []" :key="entry.ipAddress" class="transition hover:bg-white/[0.04]">
                <td class="px-5 py-4 align-top text-sm font-medium text-white">{{ entry.ipAddress }}</td>
                <td class="px-5 py-4 align-top text-sm text-slate-300">{{ entry.totalVisits }}</td>
                <td class="px-5 py-4 align-top text-sm text-slate-300">{{ entry.uniqueVisitors }}</td>
                <td class="px-5 py-4 align-top text-sm text-slate-300">{{ displayValue(entry.browser) }}</td>
                <td class="px-5 py-4 align-top">
                  <p class="text-sm text-slate-300">{{ displayValue(entry.countryName) }}</p>
                  <p class="mt-1 text-xs text-slate-500">{{ displayValue(entry.cityName) }}</p>
                </td>
                <td class="px-5 py-4 align-top text-sm text-slate-400">{{ formatDate(entry.lastVisitedAt) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border border-white/10 bg-white/[0.02] overflow-hidden">
          <div class="border-b border-white/10 px-6 py-5">
            <h3 class="text-base font-semibold text-white">Aktivitas Traffic Terbaru</h3>
            <p class="mt-1 text-sm text-slate-400">Baris traffic terbaru menampilkan IP, browser, asal negara, dan bisa dibuka untuk melihat seluruh detail visit.</p>
          </div>

          <div v-if="(traffic?.recentVisits.length || 0) === 0" class="px-6 py-10 text-sm text-slate-400">
            Belum ada kunjungan publik yang terekam.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5 text-left">
              <thead class="bg-white/[0.03]">
                <tr>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">IP</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Browser</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Negara</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Sumber</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Waktu</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                <tr
                  v-for="visit in traffic?.recentVisits || []"
                  :key="visit.id"
                  :class="['cursor-pointer transition hover:bg-white/[0.04]', selectedVisitId === visit.id ? 'bg-sky-500/[0.08]' : '']"
                  @click="openVisitDetail(visit)"
                >
                  <td class="px-5 py-4 align-top text-sm text-slate-200">
                    {{ displayValue(visit.ipAddress) }}
                  </td>
                  <td class="px-5 py-4 align-top">
                    <p class="text-sm font-medium text-white">{{ displayValue(visit.browser) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ displayValue(visit.medium || visit.source) }}</p>
                  </td>
                  <td class="px-5 py-4 align-top">
                    <p class="text-sm text-slate-200">{{ displayValue(visit.countryName) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ displayValue(visit.cityName) }}</p>
                  </td>
                  <td class="px-5 py-4 align-top">
                    <span class="inline-flex rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-sky-200">
                      {{ visit.source }}
                    </span>
                  </td>
                  <td class="px-5 py-4 align-top text-sm text-slate-400">
                    {{ formatDate(visit.visitedAt) }}
                  </td>
                  <td class="px-5 py-4 align-top">
                    <button
                      type="button"
                      class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10"
                      @click.stop="openVisitDetail(visit)"
                    >
                      Detail
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="glass-md self-start rounded-2xl border border-white/10 p-6 xl:sticky xl:top-6">
          <div v-if="selectedVisit" class="space-y-6">
            <div class="border-b border-white/10 pb-5">
              <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-semibold text-white">Detail Visit</h3>
                <span class="rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-sky-200">
                  {{ selectedVisit.source }}
                </span>
              </div>
              <p class="mt-2 text-sm text-slate-400">Data lengkap dari kunjungan yang dipilih, berdasarkan semua field yang saat ini disimpan oleh sistem traffic.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">IP Address</p>
                <p class="mt-3 break-all text-sm text-white">{{ displayValue(selectedVisit.ipAddress) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Browser</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.browser) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Negara</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.countryName) }}</p>
                <p class="mt-1 text-xs text-slate-500">Code: {{ displayValue(selectedVisit.countryCode) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kota / Lokasi</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.cityName) }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ displayValue(selectedVisit.location) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Waktu Visit</p>
                <p class="mt-3 text-sm text-white">{{ formatDate(selectedVisit.visitedAt) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Visitor Token</p>
                <p class="mt-3 break-all text-sm text-white">{{ displayValue(selectedVisit.visitorToken) }}</p>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Path</p>
                <p class="mt-3 break-all text-sm text-white">{{ displayValue(selectedVisit.path) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Route Name</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.routeName) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4 md:col-span-2">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Page Title</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.pageTitle) }}</p>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Source</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.source) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Medium</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.medium) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">UTM Campaign</p>
                <p class="mt-3 text-sm text-white">{{ displayValue(selectedVisit.utmCampaign) }}</p>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Referrer Host</p>
                <p class="mt-3 break-all text-sm text-white">{{ displayValue(selectedVisit.referrerHost) }}</p>
              </div>
              <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Referrer URL</p>
                <p class="mt-3 break-all text-sm text-white">{{ displayValue(selectedVisit.referrerUrl) }}</p>
              </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">User Agent</p>
              <p class="mt-3 break-all text-sm leading-6 text-slate-200">{{ displayValue(selectedVisit.userAgent) }}</p>
            </div>
          </div>

          <div v-else class="flex min-h-[24rem] flex-col items-center justify-center gap-3 text-center">
            <div>
              <h3 class="text-base font-semibold text-white">Pilih visit</h3>
              <p class="mt-1 text-sm text-slate-400">Klik salah satu baris pada datatable untuk membuka detail traffic lengkap.</p>
            </div>
          </div>
        </section>
      </div>
    </template>
  </section>
</template>
