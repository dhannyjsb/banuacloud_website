<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  hover?: boolean;
  glow?: boolean;
  padding?: 'none' | 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  hover: false,
  glow: false,
  padding: 'md',
});

const classes = computed(() => {
  // Base glass effect
  const base = 'glass-md';

  // Padding classes
  const paddingClasses = {
    none: '',
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8',
  };

  // Hover classes
  const hoverClasses = props.hover
    ? 'hover:-translate-y-1.5 hover:border-sky-400/20 hover:bg-white/[0.07] hover:shadow-[0_28px_70px_rgba(14,165,233,0.12)] transition-all duration-300 cursor-pointer'
    : '';

  // Glow classes
  const glowClasses = props.glow
    ? 'hover:shadow-[0_24px_70px_rgba(34,211,238,0.16)]'
    : '';

  return `${base} ${paddingClasses[props.padding]} ${hoverClasses} ${glowClasses}`;
});
</script>

<template>
  <div :class="classes">
    <slot />
  </div>
</template>
