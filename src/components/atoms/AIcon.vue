<script setup lang="ts">
import { computed, type Component } from 'vue';

interface Props {
  icon: Component;
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
  color?: 'default' | 'sky' | 'cyan' | 'violet' | 'white';
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  color: 'default',
});

const sizeClasses = {
  xs: 'w-3 h-3',
  sm: 'w-4 h-4',
  md: 'w-5 h-5',
  lg: 'w-6 h-6',
  xl: 'w-8 h-8',
};

const colorClasses = {
  default: 'text-white',
  sky: 'text-sky-400',
  cyan: 'text-cyan-400',
  violet: 'text-violet-400',
  white: 'text-white',
};

const classes = computed(() => {
  return `${sizeClasses[props.size]} ${colorClasses[props.color]}`;
});
</script>

<template>
  <component :is="icon" :class="classes" />
</template>
