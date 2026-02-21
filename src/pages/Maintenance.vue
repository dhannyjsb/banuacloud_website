<script setup lang="ts">
import {
  Server,
  Globe,
  Code2,
  Cloud,
  Rocket,
  Mail,
  Phone,
  Instagram,
  Linkedin,
  Twitter,
} from 'lucide-vue-next';

const services = [
  { icon: Cloud, label: 'Cloud Hosting', color: 'from-sky-400 to-cyan-400' },
  { icon: Globe, label: 'Web Development', color: 'from-cyan-400 to-teal-400' },
  { icon: Code2, label: 'Software Dev', color: 'from-teal-400 to-emerald-400' },
  { icon: Server, label: 'Server Mgmt', color: 'from-emerald-400 to-green-400' },
];

const currentYear = new Date().getFullYear();
const companyName = import.meta.env.VITE_COMPANY_NAME || 'Banua Cloud';
const companyTagline = import.meta.env.VITE_COMPANY_TAGLINE || 'Nusantara';
const companyFullName = import.meta.env.VITE_COMPANY_FULL_NAME || 'Banua Cloud Nusantara';
const supportEmail = import.meta.env.VITE_SUPPORT_EMAIL || 'support@banuacloud.id';
const supportPhone = import.meta.env.VITE_SUPPORT_PHONE || '+62 812-3456-7890';
const supportPhoneRaw = import.meta.env.VITE_SUPPORT_PHONE_RAW || '+6281234567890';
const whatsappRaw = import.meta.env.VITE_SUPPORT_WHATSAPP_RAW || '';
const companyDescriptor = import.meta.env.VITE_COMPANY_DESCRIPTOR || 'Trusted IT Solutions Partner in Indonesia';
const logoUrl = import.meta.env.VITE_LOGO_URL || '';

const allSocialLinks = [
  { icon: Instagram, href: import.meta.env.VITE_SOCIAL_INSTAGRAM, label: 'Instagram' },
  { icon: Linkedin, href: import.meta.env.VITE_SOCIAL_LINKEDIN, label: 'LinkedIn' },
  { icon: Twitter, href: import.meta.env.VITE_SOCIAL_TWITTER, label: 'Twitter' },
];
const socialLinks = allSocialLinks.filter(s => s.href);
const showWhatsapp = !!whatsappRaw;

const progressSteps = [
  { label: 'Planning', done: true },
  { label: 'Design', done: true },
  { label: 'Development', done: false },
  { label: 'Launch', done: false },
];
</script>

<template>
  <div class="h-screen bg-[#0a0f1a] relative overflow-hidden grid-bg">
    <!-- Background orbs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-20 left-10 w-72 h-72 bg-sky-500/10 rounded-full blur-[100px] animate-float" />
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-500/10 rounded-full blur-[120px] animate-float" :style="{ animationDelay: '-3s' }" />
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-500/5 rounded-full blur-[150px]" />
    </div>

    <div class="relative z-10 h-full flex flex-col items-center justify-center px-4 pt-10 sm:pt-14 lg:pt-16 pb-3 sm:pb-6 lg:pb-8">

      <!-- Logo -->
      <div class="mb-5 sm:mb-7 text-center">
        <div class="inline-flex items-center gap-3 sm:gap-4">
          <div class="relative">
            <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center animate-pulse-glow overflow-hidden">
              <img v-if="logoUrl" :src="logoUrl" :alt="companyName" class="w-full h-full object-cover" />
              <Cloud v-else class="w-6 h-6 sm:w-8 sm:h-8 lg:w-9 lg:h-9 text-white" />
            </div>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-500 blur-xl opacity-50" />
          </div>
          <div class="text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white tracking-tight">{{ companyName }}</h1>
            <p class="text-xs sm:text-sm text-sky-400 font-medium tracking-wider uppercase">{{ companyTagline }}</p>
          </div>
        </div>
      </div>

      <!-- Main Card -->
      <div class="w-full max-w-3xl">
        <div class="glass rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12 text-center relative overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-sky-500 via-cyan-500 to-violet-500" />

          <!-- Rocket Icon -->
          <div class="flex justify-center mb-5 sm:mb-6">
            <div class="relative">
              <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-sky-500/20 to-violet-500/20 flex items-center justify-center border border-sky-500/30 animate-rocket-hover">
                <Rocket class="w-10 h-10 sm:w-12 sm:h-12 text-sky-400 -rotate-45" />
              </div>
              <!-- Particle trails -->
              <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 flex gap-1">
                <span class="w-1 h-1 rounded-full bg-sky-400/60 animate-particle" :style="{ animationDelay: '0s' }" />
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400/40 animate-particle" :style="{ animationDelay: '0.3s' }" />
                <span class="w-1 h-1 rounded-full bg-violet-400/50 animate-particle" :style="{ animationDelay: '0.6s' }" />
              </div>
            </div>
          </div>

          <!-- Heading -->
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 sm:mb-3">
            Segera <span class="gradient-text">Hadir</span>
          </h2>

          <p class="text-gray-400 text-base sm:text-lg mb-5 sm:mb-7 max-w-lg mx-auto leading-relaxed">
            Kami sedang membangun platform terbaik untuk Anda. Nantikan peluncuran kami!
          </p>

          <!-- Progress Steps -->
          <div class="flex items-center justify-center gap-0 mb-6 sm:mb-8 max-w-md mx-auto">
            <template v-for="(step, i) in progressSteps" :key="step.label">
              <div class="flex flex-col items-center gap-2">
                <div
                  :class="[
                    'w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all',
                    step.done
                      ? 'bg-gradient-to-br from-sky-500 to-cyan-500 text-white shadow-lg shadow-sky-500/30'
                      : i === progressSteps.findIndex(s => !s.done)
                        ? 'border-2 border-sky-400 text-sky-400 animate-pulse'
                        : 'border border-white/20 text-gray-500'
                  ]"
                >
                  <span v-if="step.done">&#10003;</span>
                  <span v-else>{{ i + 1 }}</span>
                </div>
                <span :class="['text-[10px] sm:text-xs font-medium', step.done ? 'text-sky-400' : 'text-gray-500']">
                  {{ step.label }}
                </span>
              </div>
              <div
                v-if="i < progressSteps.length - 1"
                :class="['flex-1 h-0.5 mb-5 sm:mb-6 mx-1.5 sm:mx-3 rounded-full transition-all', step.done && progressSteps[i+1]?.done ? 'bg-gradient-to-r from-sky-500 to-cyan-500' : step.done ? 'progress-bar-animated' : 'bg-white/10']"
              />
            </template>
          </div>

          <!-- Services -->
          <div class="grid grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-7">
            <div
              v-for="service in services"
              :key="service.label"
              class="group p-3 sm:p-4 rounded-xl bg-white/5 border border-white/10 hover:border-sky-500/30 hover:bg-white/10 transition-all duration-300"
            >
              <div :class="['w-9 h-9 sm:w-11 sm:h-11 rounded-lg bg-gradient-to-br flex items-center justify-center mx-auto mb-1.5 sm:mb-2.5 group-hover:scale-110 transition-transform duration-300', service.color]">
                <component :is="service.icon" class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] sm:text-xs lg:text-sm text-gray-400 font-medium leading-tight">{{ service.label }}</span>
            </div>
          </div>

          <!-- Contact -->
          <div class="border-t border-white/10 pt-5 sm:pt-6">
            <p class="text-gray-500 text-xs sm:text-sm mb-3 sm:mb-4">Hubungi kami</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 sm:gap-4 mb-4">
              <a :href="`mailto:${supportEmail}`" class="flex items-center gap-2 px-4 py-2 sm:py-2.5 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 hover:bg-sky-500/20 transition-colors">
                <Mail class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                <span class="text-xs sm:text-sm font-medium">{{ supportEmail }}</span>
              </a>
              <a :href="`tel:${supportPhoneRaw}`" class="flex items-center gap-2 px-4 py-2 sm:py-2.5 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 hover:bg-cyan-500/20 transition-colors">
                <Phone class="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                <span class="text-xs sm:text-sm font-medium">{{ supportPhone }}</span>
              </a>
            </div>

            <div v-if="socialLinks.length || showWhatsapp" class="flex items-center justify-center gap-3">
              <a
                v-for="social in socialLinks"
                :key="social.label"
                :href="social.href"
                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-sky-400 hover:border-sky-500/30 hover:bg-sky-500/10 transition-all duration-300"
                :aria-label="social.label"
                target="_blank" rel="noopener noreferrer"
              >
                <component :is="social.icon" class="w-4 h-4 sm:w-5 sm:h-5" />
              </a>
              <a
                v-if="showWhatsapp"
                :href="`https://wa.me/${whatsappRaw}`"
                class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-green-400 hover:border-green-500/30 hover:bg-green-500/10 transition-all duration-300"
                aria-label="WhatsApp" target="_blank" rel="noopener noreferrer"
              >
                <svg viewBox="0 0 24 24" class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" aria-hidden="true">
                  <path d="M19.05 4.91A9.82 9.82 0 0 0 12.07 2h-.03A9.87 9.87 0 0 0 3.5 16.8L2 22l5.34-1.4a9.87 9.87 0 0 0 4.7 1.2h.04A9.92 9.92 0 0 0 22 11.9a9.84 9.84 0 0 0-2.95-6.99Zm-6.98 15.2h-.03a8.18 8.18 0 0 1-4.17-1.14l-.3-.18-3.17.83.85-3.09-.2-.32a8.1 8.1 0 0 1 6.98-12.5h.03a8.12 8.12 0 0 1 8.08 8.15 8.24 8.24 0 0 1-8.07 8.25Zm4.45-6.14c-.24-.13-1.43-.71-1.65-.8-.22-.08-.38-.13-.54.13-.16.24-.62.8-.76.96-.14.16-.28.18-.52.06-.24-.13-1.03-.38-1.96-1.23-.73-.64-1.22-1.43-1.36-1.67-.14-.24-.02-.37.1-.5.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.13-.54-1.3-.74-1.78-.2-.47-.4-.41-.54-.42h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2 0 1.18.86 2.32.98 2.48.12.16 1.69 2.58 4.1 3.62.57.24 1.01.38 1.36.48.57.18 1.08.16 1.49.1.45-.07 1.43-.58 1.63-1.15.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z" />
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-4 sm:mt-6 lg:mt-8 text-center">
        <p class="text-gray-600 text-xs sm:text-sm">&copy; {{ currentYear }} {{ companyFullName }}. All rights reserved.</p>
        <p class="text-gray-700 text-xs sm:text-sm mt-0.5">{{ companyDescriptor }}</p>
      </div>
    </div>
  </div>
</template>