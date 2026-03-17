<script setup lang="ts">
interface Props {
  gradient?: 'sky-cyan' | 'cyan-violet' | 'sky-violet';
}

withDefaults(defineProps<Props>(), {
  gradient: 'sky-cyan',
});

const gradientClasses = {
  'sky-cyan': 'from-sky-400 via-cyan-400 to-violet-400',
  'cyan-violet': 'from-cyan-400 via-sky-400 to-violet-400',
  'sky-violet': 'from-sky-400 via-cyan-400 to-violet-400',
};
</script>

<template>
  <span :class="['bg-clip-text text-transparent bg-gradient-to-r', gradientClasses[gradient]]">
    <slot />
  </span>
</template>
