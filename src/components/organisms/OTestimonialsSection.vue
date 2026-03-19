<script setup lang="ts">
import { computed } from 'vue';
import MTestimonialCard from '../molecules/MTestimonialCard.vue';
import { useSiteBootstrap } from '../../composables/useSiteBootstrap';

const { testimonials } = useSiteBootstrap();

const testimonialCards = computed(() => {
  return testimonials.value.map((testimonial) => ({
    id: testimonial.id,
    quote: testimonial.content,
    name: testimonial.name,
    role: `${testimonial.role}, ${testimonial.company}`,
  }));
});
</script>

<template>
  <section class="section-py relative">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom,rgba(139,92,246,0.08),transparent_40%)]" />

    <div class="container-custom relative z-10">
      <!-- Section Header -->
      <div class="mx-auto mb-16 max-w-3xl text-center md:mb-20">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-violet-400/15 bg-violet-500/10 px-4 py-2">
          <span class="text-xs font-semibold uppercase tracking-[0.22em] text-violet-300">Testimoni</span>
        </div>
        <h2 class="mb-5 text-3xl md:text-5xl font-bold text-white">
          Dipercaya oleh <span class="gradient-text">tim yang butuh hasil</span>
        </h2>
        <p class="text-lg leading-8 text-slate-400">
          Lihat apa yang pelanggan kami katakan tentang pengalaman mereka dengan Banua Cloud.
        </p>
      </div>

      <!-- Testimonials Grid -->
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div
          v-for="(testimonial, index) in testimonialCards"
          :key="testimonial.id"
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
