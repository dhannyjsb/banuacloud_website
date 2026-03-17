<script setup lang="ts">
import MTestimonialCard from '../molecules/MTestimonialCard.vue';

const testimonials = [
  {
    quote: "Banua Cloud telah mengubah cara kami mendeploy aplikasi. Kecepatan dan keandalan tidak tertandingi, dan tim support mereka selalu siap membantu kapan saja.",
    name: 'Ahmad Pratama',
    role: 'CTO, TechStart Indonesia',
  },
  {
    quote: "Kami telah mencoba banyak penyedia cloud, tapi Banua Cloud menonjol dengan uptime luar biasa dan harga kompetitif. Sangat direkomendasikan untuk bisnis di Indonesia.",
    name: 'Siti Nurhaliza',
    role: 'Founder, DigitalAgency',
  },
  {
    quote: "Migrasi ke Banua Cloud berjalan mulus. Tim mereka menangani semuanya dengan profesional, dan performa website kami meningkat signifikan.",
    name: 'Budi Santoso',
    role: 'IT Manager, EcomStore',
  },
];
</script>

<template>
  <section class="section-py relative">
    <div class="container-custom">
      <!-- Section Header -->
      <div class="text-center max-w-2xl mx-auto mb-16">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-6">
          <span class="text-sm text-sky-400 font-medium">Testimoni</span>
        </div>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
          Dipercaya oleh <span class="gradient-text">Ribuan</span>
        </h2>
        <p class="text-slate-400 text-lg">
          Lihat apa yang pelanggan kami katakan tentang pengalaman mereka dengan Banua Cloud.
        </p>
      </div>
      
      <!-- Testimonials Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="(testimonial, index) in testimonials"
          :key="testimonial.name"
          class="scroll-animate"
          :style="{ transitionDelay: `${index * 100}ms` }"
        >
          <MTestimonialCard
            :quote="testimonial.quote"
            :name="testimonial.name"
            :role="testimonial.role"
          />
        </div>
      </div>
    </div>
  </section>
</template>
