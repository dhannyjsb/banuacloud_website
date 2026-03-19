<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'primary' | 'secondary' | 'ghost';
  size?: 'sm' | 'md' | 'lg';
  loading?: boolean;
  disabled?: boolean;
  type?: 'button' | 'submit' | 'reset';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  loading: false,
  disabled: false,
  type: 'button',
});

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();

const classes = computed(() => {
  const base = 'group relative inline-flex items-center justify-center gap-2 overflow-hidden font-semibold tracking-[-0.015em] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0a0f1a] disabled:opacity-50 disabled:cursor-not-allowed';

  // Size classes
  const sizeClasses = {
    sm: 'px-4 py-2.5 text-sm rounded-xl',
    md: 'px-6 py-3.5 text-sm md:text-base rounded-2xl',
    lg: 'px-7 py-4 text-base md:text-lg rounded-2xl',
  };

  // Variant classes
  const variantClasses = {
    primary: 'bg-[linear-gradient(135deg,#38bdf8_0%,#22d3ee_55%,#7dd3fc_100%)] text-slate-950 hover:-translate-y-0.5 hover:shadow-[0_18px_45px_rgba(34,211,238,0.25)] focus:ring-sky-400/60',
    secondary: 'border border-white/12 bg-white/[0.04] text-white hover:-translate-y-0.5 hover:border-sky-400/30 hover:bg-white/[0.08] focus:ring-white/30',
    ghost: 'text-white hover:bg-white/[0.06] focus:ring-white/30',
  };

  return `${base} ${sizeClasses[props.size]} ${variantClasses[props.variant]}`;
});

const handleClick = (event: MouseEvent) => {
  if (!props.loading && !props.disabled) {
    emit('click', event);
  }
};
</script>

<template>
  <button
    :type="type"
    :class="classes"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <!-- Loading spinner -->
    <svg
      v-if="loading"
      class="animate-spin -ml-1 mr-2 h-4 w-4"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      />
    </svg>

    <!-- Slot for button content -->
    <slot />
  </button>
</template>
