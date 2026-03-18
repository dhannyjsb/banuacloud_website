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
