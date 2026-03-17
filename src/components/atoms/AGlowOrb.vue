<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  color?: 'sky' | 'cyan' | 'violet';
  size?: number;
  position?: {
    top?: string;
    bottom?: string;
    left?: string;
    right?: string;
  };
  delay?: number;
  intensity?: 'low' | 'medium' | 'high';
}

const props = withDefaults(defineProps<Props>(), {
  color: 'sky',
  size: 300,
  position: () => ({
    top: 'auto',
    bottom: 'auto',
    left: 'auto',
    right: 'auto',
  }),
  delay: 0,
  intensity: 'medium',
});

const colorClasses = {
  sky: 'bg-sky-500',
  cyan: 'bg-cyan-500',
  violet: 'bg-violet-500',
};

const opacityClasses = {
  low: 'opacity-10',
  medium: 'opacity-20',
  high: 'opacity-30',
};

const blurClass = computed(() => {
  if (props.size >= 400) return 'blur-[150px]';
  if (props.size >= 300) return 'blur-[100px]';
  return 'blur-[80px]';
});

const positionStyles = computed(() => ({
  top: props.position.top,
  bottom: props.position.bottom,
  left: props.position.left,
  right: props.position.right,
  width: `${props.size}px`,
  height: `${props.size}px`,
  animationDelay: `${props.delay}s`,
}));
</script>

<template>
  <div
    class="absolute rounded-full pointer-events-none animate-orb-float"
    :class="[colorClasses[color], opacityClasses[intensity], blurClass]"
    :style="positionStyles"
  />
</template>
