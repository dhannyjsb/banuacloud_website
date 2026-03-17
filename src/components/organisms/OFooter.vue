<script setup lang="ts">
import { ref } from 'vue';
import { Cloud, Mail, Phone, Instagram, Linkedin, Twitter, Send } from 'lucide-vue-next';
import AButton from '../atoms/AButton.vue';

const currentYear = new Date().getFullYear();

// Environment variables
const companyName = import.meta.env.VITE_COMPANY_NAME || 'Banua Cloud';
const companyTagline = import.meta.env.VITE_COMPANY_TAGLINE || 'Nusantara';
const companyFullName = import.meta.env.VITE_COMPANY_FULL_NAME || 'Banua Cloud Nusantara';
const supportEmail = import.meta.env.VITE_SUPPORT_EMAIL || 'support@banuacloud.id';
const companyDescriptor = import.meta.env.VITE_COMPANY_DESCRIPTOR || 'Mitra Solusi IT Terpercaya di Indonesia';
const supportPhone = import.meta.env.VITE_SUPPORT_PHONE || '+62 812-3456-7890';
const supportPhoneRaw = import.meta.env.VITE_SUPPORT_PHONE_RAW || '+6281234567890';

// Social links from environment variables
const socialLinks = [
  { 
    icon: Instagram, 
    href: import.meta.env.VITE_SOCIAL_INSTAGRAM || '#', 
    label: 'Instagram' 
  },
  { 
    icon: Linkedin, 
    href: import.meta.env.VITE_SOCIAL_LINKEDIN || '#', 
    label: 'LinkedIn' 
  },
  { 
    icon: Twitter, 
    href: import.meta.env.VITE_SOCIAL_TWITTER || '#', 
    label: 'Twitter' 
  },
].filter(link => link.href && link.href !== '#');

const footerLinks = {
  services: [
    { name: 'Cloud VPS', href: '#services' },
    { name: 'Web Hosting', href: '#services' },
    { name: 'Layanan Domain', href: '#services' },
    { name: 'Solusi Backup', href: '#services' },
    { name: 'Pengembangan Aplikasi', href: '#services' },
    { name: 'Konsultasi IT', href: '#services' },
  ],
  resources: [
    { name: 'Dokumentasi', href: '#' },
    { name: 'Referensi API', href: '#' },
    { name: 'Blog', href: '#' },
    { name: 'Halaman Status', href: '#' },
  ],
  company: [
    { name: 'Tentang Kami', href: '#' },
    { name: 'Karir', href: '#' },
    { name: 'Kontak', href: '#contact' },
    { name: 'Kebijakan Privasi', href: '#' },
  ],
};

const email = ref('');
const isSubscribed = ref(false);

const handleSubscribe = () => {
  if (email.value) {
    isSubscribed.value = true;
    setTimeout(() => {
      isSubscribed.value = false;
      email.value = '';
    }, 3000);
  }
};
</script>

<template>
  <footer id="contact" class="relative bg-white/[0.02] border-t border-white/5">
    <div class="container-custom section-py">
      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
        <!-- Brand Column -->
        <div class="lg:col-span-1">
          <a href="/" class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center">
              <Cloud class="w-5 h-5 text-white" />
            </div>
            <span class="text-xl font-bold text-white">{{ companyName }}</span>
          </a>
          <p class="text-slate-400 text-sm mb-6">
            {{ companyDescriptor }}
          </p>
           
          <!-- Newsletter -->
          <div>
            <h4 class="text-white font-semibold mb-3">Newsletter</h4>
            <div class="flex gap-2">
              <input
                v-model="email"
                type="email"
                placeholder="Masukkan email Anda"
                class="flex-1 px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 transition-colors"
              />
              <AButton variant="primary" size="sm" @click="handleSubscribe">
                <Send class="w-4 h-4" />
              </AButton>
            </div>
            <p v-if="isSubscribed" class="text-sky-400 text-xs mt-2">
              Terima kasih telah berlangganan!
            </p>
          </div>
        </div>
        
        <!-- Services Column -->
        <div>
          <h4 class="text-white font-semibold mb-4">Layanan</h4>
          <ul class="space-y-3">
            <li v-for="link in footerLinks.services" :key="link.name">
              <a :href="link.href" class="text-slate-400 hover:text-sky-400 transition-colors text-sm">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>
        
        <!-- Resources Column -->
        <div>
          <h4 class="text-white font-semibold mb-4">Resources</h4>
          <ul class="space-y-3">
            <li v-for="link in footerLinks.resources" :key="link.name">
              <a :href="link.href" class="text-slate-400 hover:text-sky-400 transition-colors text-sm">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>
        
        <!-- Contact Column -->
        <div>
          <h4 class="text-white font-semibold mb-4">Kontak</h4>
          <ul class="space-y-3">
            <li class="flex items-center gap-3 text-slate-400 text-sm">
              <Mail class="w-4 h-4 text-sky-400" />
              <a :href="`mailto:${supportEmail}`" class="hover:text-sky-400 transition-colors">
                {{ supportEmail }}
              </a>
            </li>
            <li class="flex items-center gap-3 text-slate-400 text-sm">
              <Phone class="w-4 h-4 text-sky-400" />
              <a :href="`tel:${supportPhoneRaw}`" class="hover:text-sky-400 transition-colors">
                {{ supportPhone }}
              </a>
            </li>
          </ul>
          
          <!-- Social Links -->
          <div class="flex items-center gap-3 mt-6">
            <a
              v-for="social in socialLinks"
              :key="social.label"
              :href="social.href"
              class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:text-sky-400 hover:border-sky-500/30 hover:bg-sky-500/10 transition-all duration-300"
              :aria-label="social.label"
            >
              <component :is="social.icon" class="w-4 h-4" />
            </a>
          </div>
        </div>
      </div>
      
      <!-- Bottom Bar -->
      <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-slate-500 text-sm">
          &copy; {{ currentYear }} {{ companyFullName }}. Semua hak dilindungi.
        </p>
        <p class="text-slate-600 text-sm">
          Dibuat dengan <span class="text-sky-400">❤</span> di {{ companyTagline }}
        </p>
      </div>
    </div>
  </footer>
</template>
