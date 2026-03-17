# Banua Cloud Animation & Interaction Guide

> Comprehensive animation specifications for modern SaaS website experience

## Overview

Animation strategy yang mengutamakan:

- **Performance**: GPU-accelerated transforms, will-change optimization
- **Accessibility**: Reduced motion support, purposeful animations
- **Brand**: Tech-forward, smooth, professional feel
- **UX**: Guide attention, provide feedback, create delight

---

## 1. Animation Principles

### 1.1 Timing Functions

```css
/* Standard - Use for most transitions */
--ease-default: cubic-bezier(0.4, 0, 0.2, 1);

/* Entrance - Elements entering viewport */
--ease-in: cubic-bezier(0.4, 0, 1, 1);

/* Exit - Elements leaving viewport */
--ease-out: cubic-bezier(0, 0, 0.2, 1);

/* Bounce - Playful interactions */
--ease-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);

/* Smooth - Luxury feel */
--ease-smooth: cubic-bezier(0.25, 0.46, 0.45, 0.94);

/* Spring - Elastic feel */
--ease-spring: cubic-bezier(0.175, 0.885, 0.32, 1.275);
```

### 1.2 Duration Scale

| Type     | Duration    | Usage                           |
| -------- | ----------- | ------------------------------- |
| Micro    | 150ms       | Hover states, color changes     |
| Fast     | 200ms       | Button presses, small UI        |
| Normal   | 300ms       | Cards, modals, dropdowns        |
| Slow     | 500ms       | Page transitions, hero elements |
| Dramatic | 800ms       | Entrance animations             |
| Ambient  | 3000-6000ms | Background effects              |

### 1.3 Stagger Patterns

```typescript
// Standard stagger for lists
const staggerDelay = 100; // ms between items

// Slow stagger for dramatic effect
const dramaticStagger = 150;

// Fast stagger for quick reveals
const fastStagger = 50;
```

---

## 2. Scroll Animations

### 2.1 Intersection Observer Configuration

```typescript
interface ScrollAnimationConfig {
  threshold: 0.15; // Trigger at 15% visibility
  rootMargin: "0px 0px -50px 0px"; // Slightly early trigger
  triggerOnce: true; // Only animate first time
}
```

### 2.2 Scroll Animation Types

#### Fade In Up (Standard)

```css
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.6s var(--ease-out) forwards;
}
```

**Usage**: Section headers, text blocks, general content

#### Fade In Scale (Cards)

```css
@keyframes fade-in-scale {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-fade-in-scale {
  animation: fade-in-scale 0.5s var(--ease-out) forwards;
}
```

**Usage**: Feature cards, pricing cards, testimonials

#### Fade In Blur (Hero)

```css
@keyframes fade-in-blur {
  from {
    opacity: 0;
    filter: blur(10px);
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    filter: blur(0);
    transform: translateY(0);
  }
}

.animate-fade-in-blur {
  animation: fade-in-blur 0.8s var(--ease-smooth) forwards;
}
```

**Usage**: Hero headline, important CTAs

#### Slide In Left/Right

```css
@keyframes slide-in-left {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slide-in-right {
  from {
    opacity: 0;
    transform: translateX(50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
```

**Usage**: Split sections, alternating layouts

### 2.3 Section-Specific Scroll Triggers

| Section      | Animation       | Stagger | Delay             |
| ------------ | --------------- | ------- | ----------------- |
| Hero         | Fade in blur    | -       | 0ms, 200ms, 400ms |
| Stats        | Count up + fade | 100ms   | -                 |
| Features     | Fade in scale   | 100ms   | -                 |
| Services     | Fade in up      | 150ms   | -                 |
| Testimonials | Slide in        | -       | -                 |
| Pricing      | Fade in scale   | 100ms   | -                 |
| CTA          | Fade in blur    | -       | -                 |

---

## 3. Hover Interactions

### 3.1 Button Hover States

#### Primary Button

```css
.btn-primary {
  transition: all 0.2s var(--ease-default);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(14, 165, 233, 0.4);
  filter: brightness(1.1);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
}
```

#### Secondary/Ghost Button

```css
.btn-secondary {
  transition: all 0.2s var(--ease-default);
}

.btn-secondary:hover {
  background: rgba(14, 165, 233, 0.1);
  border-color: rgba(14, 165, 233, 0.5);
  color: white;
}
```

### 3.2 Card Hover States

#### Glass Card Hover

```css
.card-glass {
  transition: all 0.3s var(--ease-default);
}

.card-glass:hover {
  transform: translateY(-8px);
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(14, 165, 233, 0.3);
  box-shadow:
    0 20px 40px rgba(0, 0, 0, 0.3),
    0 0 30px rgba(14, 165, 233, 0.1);
}
```

#### Service Card Hover (Icon Animation)

```css
.service-card:hover .service-icon {
  transform: scale(1.1) rotate(5deg);
  transition: transform 0.3s var(--ease-bounce);
}

.service-card:hover .service-icon-bg {
  box-shadow: 0 0 30px rgba(14, 165, 233, 0.5);
}
```

### 3.3 Link Hover States

#### Nav Link

```css
.nav-link {
  position: relative;
  transition: color 0.2s var(--ease-default);
}

.nav-link::after {
  content: "";
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #0ea5e9, #06b6d4);
  transition: width 0.3s var(--ease-out);
}

.nav-link:hover::after {
  width: 100%;
}
```

### 3.4 Image Hover (If applicable)

```css
.image-hover {
  transition: transform 0.5s var(--ease-default);
}

.image-hover:hover {
  transform: scale(1.05);
}
```

---

## 4. Ambient Background Animations

### 4.1 Floating Orbs (Hero Background)

```css
@keyframes float {
  0%,
  100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(-10px) translateX(-10px);
  }
  75% {
    transform: translateY(-30px) translateX(5px);
  }
}

.orb-1 {
  animation: float 8s ease-in-out infinite;
}

.orb-2 {
  animation: float 10s ease-in-out infinite;
  animation-delay: -3s;
}

.orb-3 {
  animation: float 12s ease-in-out infinite;
  animation-delay: -6s;
}
```

### 4.2 Pulse Glow Effect

```css
@keyframes pulse-glow {
  0%,
  100% {
    box-shadow: 0 0 20px rgba(14, 165, 233, 0.3);
  }
  50% {
    box-shadow: 0 0 40px rgba(14, 165, 233, 0.6);
  }
}

.pulse-glow {
  animation: pulse-glow 3s ease-in-out infinite;
}
```

### 4.3 Gradient Shift

```css
@keyframes gradient-shift {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient-shift 8s ease infinite;
}
```

### 4.4 Grid Background Animation

```css
.grid-bg {
  background-image:
    linear-gradient(rgba(14, 165, 233, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(14, 165, 233, 0.03) 1px, transparent 1px);
  background-size: 50px 50px;
  animation: grid-move 20s linear infinite;
}

@keyframes grid-move {
  0% {
    background-position: 0 0;
  }
  100% {
    background-position: 50px 50px;
  }
}
```

---

## 5. Micro-interactions

### 5.1 Scroll Indicator

```css
@keyframes scroll-bounce {
  0%,
  100% {
    transform: translateY(0);
    opacity: 1;
  }
  50% {
    transform: translateY(10px);
    opacity: 0.5;
  }
}

.scroll-indicator {
  animation: scroll-bounce 2s ease-in-out infinite;
}
```

### 5.2 Loading States

#### Button Loading

```css
@keyframes loading-pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.btn-loading {
  pointer-events: none;
  animation: loading-pulse 1.5s ease-in-out infinite;
}

/* Spinner */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.spinner {
  animation: spin 1s linear infinite;
}
```

### 5.3 Focus States

```css
/* Focus ring animation */
.focus-ring {
  transition: box-shadow 0.2s var(--ease-default);
}

.focus-ring:focus-visible {
  outline: none;
  box-shadow:
    0 0 0 2px #0a0f1a,
    0 0 0 4px rgba(14, 165, 233, 0.5);
}
```

### 5.4 Checkmark Animation

```css
@keyframes check-draw {
  0% {
    stroke-dashoffset: 24;
  }
  100% {
    stroke-dashoffset: 0;
  }
}

.check-icon path {
  stroke-dasharray: 24;
  stroke-dashoffset: 24;
  animation: check-draw 0.4s var(--ease-out) forwards;
}
```

---

## 6. Page Transitions

### 6.1 Vue Router Transitions

```vue
<!-- App.vue -->
<template>
  <RouterView v-slot="{ Component }">
    <Transition name="page" mode="out-in">
      <component :is="Component" />
    </Transition>
  </RouterView>
</template>

<style>
.page-enter-active,
.page-leave-active {
  transition: all 0.3s var(--ease-default);
}

.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
```

### 6.2 Section Transitions

```css
.section-enter-active {
  transition: all 0.6s var(--ease-out);
}

.section-enter-from {
  opacity: 0;
  transform: translateY(40px);
}
```

---

## 7. Carousel/Slider Animations

### 7.1 Testimonial Carousel

```css
/* Slide transition */
.carousel-slide-enter-active,
.carousel-slide-leave-active {
  transition: all 0.5s var(--ease-default);
}

.carousel-slide-enter-from {
  opacity: 0;
  transform: translateX(50px);
}

.carousel-slide-leave-to {
  opacity: 0;
  transform: translateX(-50px);
}

/* Dot indicators */
.carousel-dot {
  transition: all 0.3s var(--ease-default);
}

.carousel-dot.active {
  width: 24px;
  background: linear-gradient(90deg, #0ea5e9, #06b6d4);
}
```

---

## 8. Counter Animations

### 8.1 Number Count Up

```typescript
// useCountUp composable
function useCountUp(end: number, duration: number = 2000): Ref<number> {
  const current = ref(0);
  const startTime = performance.now();

  const animate = (now: number) => {
    const elapsed = now - startTime;
    const progress = Math.min(elapsed / duration, 1);

    // Ease out quart
    const easeProgress = 1 - Math.pow(1 - progress, 4);
    current.value = Math.floor(easeProgress * end);

    if (progress < 1) {
      requestAnimationFrame(animate);
    }
  };

  requestAnimationFrame(animate);
  return current;
}
```

---

## 9. Performance Optimizations

### 9.1 GPU Acceleration

```css
/* Promote to GPU layer */
.gpu-accelerated {
  will-change: transform, opacity;
  transform: translateZ(0);
  backface-visibility: hidden;
}

/* Remove will-change after animation */
.animation-complete {
  will-change: auto;
}
```

### 9.2 Reduced Motion Support

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }

  .animate-float,
  .animate-pulse-glow,
  .animate-gradient {
    animation: none !important;
  }
}
```

### 9.3 Intersection Observer Lazy Animation

```typescript
// Only animate when visible
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("animate-in");
        observer.unobserve(entry.target); // Trigger once
      }
    });
  },
  { threshold: 0.15, rootMargin: "0px 0px -50px 0px" },
);
```

---

## 10. Animation Implementation Checklist

### Per Component

- [ ] Define entry animation type
- [ ] Add hover interactions
- [ ] Include focus states (accessibility)
- [ ] Support reduced-motion
- [ ] Optimize with will-change
- [ ] Test on mobile devices

### Global

- [ ] Page transition setup
- [ ] Scroll animation observer
- [ ] Ambient background effects
- [ ] Loading states
- [ ] Error state animations

---

## 11. CSS Classes Reference

| Class                     | Animation         | Duration  |
| ------------------------- | ----------------- | --------- |
| `.animate-fade-in-up`     | Fade + translateY | 600ms     |
| `.animate-fade-in-scale`  | Fade + scale      | 500ms     |
| `.animate-fade-in-blur`   | Fade + blur       | 800ms     |
| `.animate-slide-in-left`  | Slide from left   | 600ms     |
| `.animate-slide-in-right` | Slide from right  | 600ms     |
| `.animate-float`          | Float ambient     | 6-12s     |
| `.animate-pulse-glow`     | Pulsing glow      | 3s        |
| `.animate-gradient`       | Gradient shift    | 8s        |
| `.animate-bounce`         | Bounce            | 2s        |
| `.animate-spin`           | Rotation          | 1s linear |

---

## 12. Vue Transition Components

```vue
<!-- Fade transition wrapper -->
<template>
  <Transition name="fade">
    <slot />
  </Transition>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
```

```vue
<!-- Slide up transition wrapper -->
<template>
  <Transition name="slide-up">
    <slot />
  </Transition>
</template>

<style>
.slide-up-enter-active {
  transition: all 0.5s cubic-bezier(0, 0, 0.2, 1);
}

.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 1, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
```
