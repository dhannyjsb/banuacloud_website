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
    ? 'hover:-translate-y-2 hover:shadow-xl hover:shadow-sky-500/10 transition-all duration-300 cursor-pointer' 
    : '';
  
  // Glow classes
  const glowClasses = props.glow
    ? 'hover:shadow-lg hover:shadow-sky-500/20'
    : '';
  
  return `${base} ${paddingClasses[props.padding]} ${hoverClasses} ${glowClasses}`;
});
</script>

<template>
  <div :class="classes">
    <slot />
  </div>
</template>
