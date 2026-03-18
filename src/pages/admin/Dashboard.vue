<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { 
  TrendingUp, 
  TrendingDown, 
  Users, 
  Package, 
  DollarSign, 
  Eye,
  MessageSquare,
  ArrowUpRight,
  ArrowDownRight
} from 'lucide-vue-next';

interface StatCard {
  title: string;
  value: string;
  change: string;
  changeType: 'positive' | 'negative';
  icon: any;
  color: string;
}

interface RecentOrder {
  id: string;
  customer: string;
  service: string;
  amount: string;
  status: 'pending' | 'completed' | 'cancelled';
  date: string;
}

const stats = ref<StatCard[]>([
  {
    title: 'Total Visitors',
    value: '12,456',
    change: '+12.5%',
    changeType: 'positive',
    icon: Eye,
    color: 'sky'
  },
  {
    title: 'Active Services',
    value: '156',
    change: '+8.2%',
    changeType: 'positive',
    icon: Package,
    color: 'cyan'
  },
  {
    title: 'Total Revenue',
    value: 'Rp 45.2M',
    change: '+23.1%',
    changeType: 'positive',
    icon: DollarSign,
    color: 'green'
  },
  {
    title: 'New Messages',
    value: '28',
    change: '-2.4%',
    changeType: 'negative',
    icon: MessageSquare,
    color: 'violet'
  }
]);

const recentOrders = ref<RecentOrder[]>([
  { id: '1', customer: 'PT Maju Jaya', service: 'Cloud VPS 4GB', amount: 'Rp 299.000', status: 'completed', date: '2 min ago' },
  { id: '2', customer: 'CV Teknologi', service: 'Web Hosting Business', amount: 'Rp 59.000', status: 'pending', date: '15 min ago' },
  { id: '3', customer: 'Indonesia Digital', service: 'Domain .com', amount: 'Rp 149.000', status: 'completed', date: '1 hour ago' },
  { id: '4', customer: 'Startup Hub', service: 'Cloud VPS 8GB', amount: 'Rp 499.000', status: 'cancelled', date: '3 hours ago' },
  { id: '5', customer: 'Tech Corp', service: 'Backup Service', amount: 'Rp 199.000', status: 'completed', date: '5 hours ago' },
]);

const getStatusColor = (status: string) => {
  switch (status) {
    case 'completed': return 'text-green-400 bg-green-400/10';
    case 'pending': return 'text-yellow-400 bg-yellow-400/10';
    case 'cancelled': return 'text-red-400 bg-red-400/10';
    default: return 'text-slate-400 bg-slate-400/10';
  }
};

const getIconColor = (color: string) => {
  switch (color) {
    case 'sky': return 'from-sky-500/20 to-sky-500/5 text-sky-400';
    case 'cyan': return 'from-cyan-500/20 to-cyan-500/5 text-cyan-400';
    case 'green': return 'from-green-500/20 to-green-500/5 text-green-400';
    case 'violet': return 'from-violet-500/20 to-violet-500/5 text-violet-400';
    default: return 'from-slate-500/20 to-slate-500/5 text-slate-400';
  }
};

const popularServices = ref([
  { name: 'Cloud VPS', orders: 45, revenue: 'Rp 13.5M' },
  { name: 'Web Hosting', orders: 38, revenue: 'Rp 2.2M' },
  { name: 'Domain Registration', orders: 28, revenue: 'Rp 4.2M' },
  { name: 'Backup Service', orders: 15, revenue: 'Rp 3.0M' },
]);
</script>

<template>
  <div class="space-y-8">
    <!-- Page Header -->
    <div>
      <h1 class="text-2xl font-bold text-white">Dashboard</h1>
      <p class="text-slate-400 mt-1">Welcome back! Here's what's happening with your business.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div 
        v-for="stat in stats" 
        :key="stat.title"
        class="glass-md p-6 rounded-xl border border-white/10"
      >
        <div class="flex items-start justify-between">
          <div 
            :class="[
              'w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br',
              getIconColor(stat.color)
            ]"
          >
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
          <div 
            :class="[
              'flex items-center gap-1 text-sm font-medium',
              stat.changeType === 'positive' ? 'text-green-400' : 'text-red-400'
            ]"
          >
            <ArrowUpRight v-if="stat.changeType === 'positive'" class="w-4 h-4" />
            <ArrowDownRight v-else class="w-4 h-4" />
            {{ stat.change }}
          </div>
        </div>
        <div class="mt-4">
          <h3 class="text-3xl font-bold text-white">{{ stat.value }}</h3>
          <p class="text-sm text-slate-400 mt-1">{{ stat.title }}</p>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Revenue Chart Placeholder -->
      <div class="lg:col-span-2 glass-md p-6 rounded-xl border border-white/10">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-white">Revenue Overview</h3>
          <select class="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-slate-300 focus:outline-none focus:border-sky-500">
            <option>Last 7 days</option>
            <option>Last 30 days</option>
            <option>Last 90 days</option>
          </select>
        </div>
        
        <!-- Chart Placeholder -->
        <div class="h-64 flex items-end justify-between gap-2">
          <div 
            v-for="i in 7" 
            :key="i"
            class="flex-1 bg-gradient-to-t from-sky-500/30 to-sky-500/10 rounded-t-lg relative group"
            :style="{ height: `${30 + Math.random() * 70}%` }"
          >
            <div class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-white/10 rounded text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity">
              Rp {{ Math.floor(Math.random() * 5000000 + 1000000).toLocaleString() }}
            </div>
          </div>
        </div>
        
        <!-- X-axis labels -->
        <div class="flex justify-between mt-2 text-xs text-slate-500">
          <span>Mon</span>
          <span>Tue</span>
          <span>Wed</span>
          <span>Thu</span>
          <span>Fri</span>
          <span>Sat</span>
          <span>Sun</span>
        </div>
      </div>

      <!-- Popular Services -->
      <div class="glass-md p-6 rounded-xl border border-white/10">
        <h3 class="text-lg font-semibold text-white mb-6">Popular Services</h3>
        <div class="space-y-4">
          <div 
            v-for="(service, index) in popularServices" 
            :key="service.name"
            class="flex items-center gap-4"
          >
            <span class="text-slate-500 text-sm w-6">{{ index + 1 }}</span>
            <div class="flex-1">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-white">{{ service.name }}</span>
                <span class="text-xs text-slate-400">{{ service.orders }} orders</span>
              </div>
              <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                <div 
                  class="h-full bg-gradient-to-r from-sky-500 to-cyan-500 rounded-full"
                  :style="{ width: `${(service.orders / 45) * 100}%` }"
                />
              </div>
            </div>
            <span class="text-sm font-medium text-slate-400">{{ service.revenue }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="glass-md p-6 rounded-xl border border-white/10">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-white">Recent Orders</h3>
        <router-link 
          to="/admin/services" 
          class="text-sm text-sky-400 hover:text-sky-300 transition-colors"
        >
          Manage services →
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-white/10">
              <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider pb-3">Customer</th>
              <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider pb-3">Service</th>
              <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider pb-3">Amount</th>
              <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider pb-3">Status</th>
              <th class="text-left text-xs font-medium text-slate-400 uppercase tracking-wider pb-3">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr 
              v-for="order in recentOrders" 
              :key="order.id"
              class="group hover:bg-white/5 transition-colors"
            >
              <td class="py-4">
                <span class="text-sm font-medium text-white">{{ order.customer }}</span>
              </td>
              <td class="py-4">
                <span class="text-sm text-slate-300">{{ order.service }}</span>
              </td>
              <td class="py-4">
                <span class="text-sm font-medium text-white">{{ order.amount }}</span>
              </td>
              <td class="py-4">
                <span 
                  :class="[
                    'inline-flex px-2.5 py-1 rounded-full text-xs font-medium capitalize',
                    getStatusColor(order.status)
                  ]"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="py-4">
                <span class="text-sm text-slate-400">{{ order.date }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>