# Banua Cloud Component Architecture

> Vue 3.5 + TypeScript Component Structure for Modern SaaS Website

## Overview

Arsitektur komponen menggunakan Vue 3.5 Composition API dengan TypeScript, mengikuti pola atomic design untuk reusability dan maintainability.

---

## 1. Folder Structure

```
src/
├── components/
│   ├── atoms/           # Basic building blocks
│   ├── molecules/       # Simple component combinations
│   ├── organisms/       # Complex sections
│   ├── templates/       # Page layouts
│   └── composables/     # Shared logic
├── pages/
│   ├── Home.vue
│   ├── Maintenance.vue
│   └── ...
├── layouts/
│   └── MainLayout.vue
├── types/
│   └── components.ts
└── utils/
    └── animations.ts
```

---

## 2. Atomic Components

### 2.1 Atoms (`/components/atoms/`)

Basic UI elements yang tidak bergantung pada komponen lain.

#### AButton.vue

```typescript
interface ButtonProps {
  variant?: "primary" | "secondary" | "ghost" | "outline";
  size?: "sm" | "md" | "lg" | "xl";
  disabled?: boolean;
  loading?: boolean;
  type?: "button" | "submit" | "reset";
  to?: string; // For router-link
  external?: boolean;
}

interface ButtonSlots {
  default: () => unknown;
  icon?: () => unknown;
}

interface ButtonEmits {
  click: [event: MouseEvent];
}
```

**Usage:**

```vue
<AButton variant="primary" size="lg" @click="handleClick">
  Get Started
</AButton>
```

---

#### ACard.vue

```typescript
interface CardProps {
  variant?: "default" | "glass" | "gradient-border" | "elevated";
  padding?: "none" | "sm" | "md" | "lg" | "xl";
  hoverable?: boolean;
  clickable?: boolean;
}

interface CardSlots {
  default: () => unknown;
  header?: () => unknown;
  footer?: () => unknown;
}
```

**Usage:**

```vue
<ACard variant="glass" padding="lg" hoverable>
  <template #header>
    <h3>Feature Title</h3>
  </template>
  <p>Card content here</p>
</ACard>
```

---

#### ABadge.vue

```typescript
interface BadgeProps {
  variant?: "default" | "primary" | "success" | "warning" | "error";
  size?: "sm" | "md";
  pill?: boolean;
  dot?: boolean;
}
```

---

#### AIcon.vue (Wrapper for Lucide)

```typescript
interface IconProps {
  name: string; // Lucide icon name
  size?: "xs" | "sm" | "md" | "lg" | "xl" | "2xl";
  color?: string; // Tailwind color class
  strokeWidth?: number;
  class?: string;
}
```

---

#### AGradientText.vue

```typescript
interface GradientTextProps {
  variant?: "primary" | "sky" | "aurora";
  as?: "span" | "h1" | "h2" | "h3" | "p";
  animate?: boolean;
}
```

---

#### AGlowOrb.vue (Background effect)

```typescript
interface GlowOrbProps {
  color?: "sky" | "cyan" | "violet" | "mixed";
  size?: "sm" | "md" | "lg" | "xl";
  position: {
    top?: string;
    bottom?: string;
    left?: string;
    right?: string;
  };
  blur?: number;
  opacity?: number;
  animate?: boolean;
}
```

---

### 2.2 Molecules (`/components/molecules/`)

Kombinasi dari atoms untuk membuat komponen yang lebih kompleks.

#### MServiceCard.vue

```typescript
interface ServiceCardProps {
  icon: string; // Lucide icon name
  title: string;
  description: string;
  gradient?: string; // Tailwind gradient classes
  features?: string[];
  href?: string;
}
```

**Composition:**

- ACard (glass variant)
- AIcon
- ABadge (optional)

---

#### MFeatureItem.vue

```typescript
interface FeatureItemProps {
  icon: string;
  title: string;
  description: string;
  reversed?: boolean; // Layout direction
}
```

---

#### MTestimonialCard.vue

```typescript
interface TestimonialCardProps {
  quote: string;
  author: {
    name: string;
    role: string;
    company: string;
    avatar?: string;
  };
  rating?: number;
}
```

---

#### MNavLink.vue

```typescript
interface NavLinkProps {
  to: string;
  label: string;
  icon?: string;
  external?: boolean;
  active?: boolean;
}
```

---

#### MSocialButton.vue

```typescript
interface SocialButtonProps {
  platform: "instagram" | "linkedin" | "twitter" | "whatsapp" | "github";
  href: string;
  variant?: "default" | "glass";
}
```

---

#### MStatCounter.vue

```typescript
interface StatCounterProps {
  value: number;
  suffix?: string;
  prefix?: string;
  label: string;
  duration?: number; // Animation duration in ms
}
```

---

### 2.3 Organisms (`/components/organisms/`)

Komponen kompleks yang membentuk section utama dari website.

#### ONavbar.vue

```typescript
interface NavbarProps {
  transparent?: boolean;
  sticky?: boolean;
}

interface NavItem {
  label: string;
  href: string;
  external?: boolean;
  children?: NavItem[];
}
```

**Features:**

- Glassmorphism background on scroll
- Mobile hamburger menu
- Active state indicator
- Logo slot

---

#### OHeroSection.vue

```typescript
interface HeroSectionProps {
  title: string;
  subtitle: string;
  description?: string;
  ctaPrimary?: {
    label: string;
    href: string;
  };
  ctaSecondary?: {
    label: string;
    href: string;
  };
  showStats?: boolean;
}
```

**Composition:**

- AGradientText (for title highlight)
- AGlowOrb (background effects)
- AButton (CTAs)
- MStatCounter (optional stats)

---

#### OFeaturesSection.vue

```typescript
interface FeaturesSectionProps {
  title: string;
  subtitle?: string;
  features: FeatureItemProps[];
  layout?: "grid" | "alternating" | "cards";
}
```

---

#### OServicesSection.vue

```typescript
interface ServicesSectionProps {
  title: string;
  subtitle?: string;
  services: ServiceCardProps[];
  columns?: 2 | 3 | 4;
}
```

---

#### OTestimonialsSection.vue

```typescript
interface TestimonialsSectionProps {
  title: string;
  subtitle?: string;
  testimonials: TestimonialCardProps[];
  autoplay?: boolean;
  interval?: number;
}
```

---

#### OCTASection.vue

```typescript
interface CTASectionProps {
  title: string;
  description?: string;
  primaryAction: {
    label: string;
    href: string;
  };
  secondaryAction?: {
    label: string;
    href: string;
  };
  variant?: "default" | "gradient-bg";
}
```

---

#### OFooter.vue

```typescript
interface FooterProps {
  companyName: string;
  tagline?: string;
  columns: {
    title: string;
    links: { label: string; href: string; external?: boolean }[];
  }[];
  socials: SocialButtonProps[];
  showNewsletter?: boolean;
}
```

---

#### OPricingCard.vue

```typescript
interface PricingCardProps {
  name: string;
  description?: string;
  price: {
    monthly: number;
    yearly?: number;
    currency?: string;
  };
  features: {
    text: string;
    included: boolean;
  }[];
  popular?: boolean;
  cta: {
    label: string;
    href: string;
  };
}
```

---

### 2.4 Templates (`/components/templates/`)

Layout wrappers untuk page structure.

#### TPageLayout.vue

```typescript
interface PageLayoutProps {
  fullWidth?: boolean;
  noPadding?: boolean;
}

interface PageLayoutSlots {
  navbar: () => unknown;
  default: () => unknown;
  footer: () => unknown;
}
```

---

#### TSectionLayout.vue

```typescript
interface SectionLayoutProps {
  id?: string;
  class?: string;
  container?: boolean;
  size?: "sm" | "md" | "lg" | "xl" | "full";
  background?: "default" | "muted" | "gradient";
}
```

---

## 3. Composables

### useScrollAnimation.ts

```typescript
interface ScrollAnimationOptions {
  threshold?: number;
  rootMargin?: string;
  triggerOnce?: boolean;
}

export function useScrollAnimation(
  elementRef: Ref<HTMLElement>,
  options?: ScrollAnimationOptions,
): {
  isVisible: Ref<boolean>;
  hasAnimated: Ref<boolean>;
};
```

### useCountUp.ts

```typescript
interface CountUpOptions {
  start?: number;
  end: number;
  duration?: number;
  delay?: number;
  decimals?: number;
}

export function useCountUp(options: CountUpOptions): Ref<number>;
```

### useGlassmorphism.ts

```typescript
interface GlassmorphismOptions {
  blur?: number;
  opacity?: number;
  borderOpacity?: number;
}

export function useGlassmorphism(options?: GlassmorphismOptions): {
  className: ComputedRef<string>;
  style: ComputedRef<CSSProperties>;
};
```

### useParallax.ts

```typescript
interface ParallaxOptions {
  speed?: number;
  direction?: "vertical" | "horizontal";
}

export function useParallax(options?: ParallaxOptions): {
  style: ComputedRef<CSSProperties>;
  onScroll: (e: Event) => void;
};
```

---

## 4. Type Definitions

### `/types/components.ts`

```typescript
// Common types used across components

export type ButtonVariant = "primary" | "secondary" | "ghost" | "outline";
export type ButtonSize = "sm" | "md" | "lg" | "xl";

export type CardVariant = "default" | "glass" | "gradient-border" | "elevated";

export type ColorScheme =
  | "sky"
  | "cyan"
  | "violet"
  | "emerald"
  | "amber"
  | "rose";

export interface IconConfig {
  name: string;
  size?: "xs" | "sm" | "md" | "lg" | "xl" | "2xl";
  color?: string;
}

export interface AnimationConfig {
  type: "fade" | "slide" | "scale" | "blur";
  duration?: number;
  delay?: number;
  easing?: string;
}
```

---

## 5. Component Usage Patterns

### 5.1 Composition Pattern Example

```vue
<!-- organisms/OServicesSection.vue -->
<template>
  <TSectionLayout id="services">
    <div class="text-center mb-16">
      <ABadge variant="primary">Our Services</ABadge>
      <h2 class="text-3xl font-bold text-white mt-4">
        Solutions for Your <AGradientText>Business</AGradientText>
      </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <MServiceCard
        v-for="service in services"
        :key="service.title"
        v-bind="service"
      />
    </div>
  </TSectionLayout>
</template>
```

### 5.2 Slot Pattern Example

```vue
<!-- molecules/MServiceCard.vue -->
<template>
  <ACard variant="glass" hoverable class="h-full">
    <template #header>
      <div
        :class="[
          'w-14 h-14 rounded-xl bg-gradient-to-br flex items-center justify-center',
          gradient,
        ]"
      >
        <AIcon :name="icon" size="lg" color="white" />
      </div>
    </template>

    <h3 class="text-xl font-semibold text-white mb-2">{{ title }}</h3>
    <p class="text-gray-400 text-sm leading-relaxed">{{ description }}</p>

    <template v-if="features?.length" #footer>
      <ul class="space-y-2">
        <li
          v-for="feature in features"
          :key="feature"
          class="flex items-center gap-2 text-sm text-gray-300"
        >
          <AIcon name="Check" size="sm" color="text-emerald-400" />
          {{ feature }}
        </li>
      </ul>
    </template>
  </ACard>
</template>
```

---

## 6. Props Naming Conventions

| Pattern       | Example                         | Usage                |
| ------------- | ------------------------------- | -------------------- |
| Boolean props | `isLoading`, `isDisabled`       | State indicators     |
| Action props  | `onClick`, `onSubmit`           | Event handlers       |
| Style props   | `variant`, `size`, `color`      | Visual customization |
| Content props | `title`, `description`, `label` | Text content         |
| Complex props | `items`, `options`, `config`    | Object arrays        |

---

## 7. Event Naming

| Pattern       | Example                       | Usage            |
| ------------- | ----------------------------- | ---------------- |
| Native events | `click`, `submit`, `change`   | DOM events       |
| Custom events | `update:modelValue`, `select` | Component events |
| Async events  | `load`, `error`, `success`    | State changes    |

---

## 8. Component Checklist

Before creating a new component:

- [ ] Define Props interface with JSDoc comments
- [ ] Define Emits for all events
- [ ] Define Slots if needed
- [ ] Use TypeScript generics where applicable
- [ ] Add default values for optional props
- [ ] Document with usage examples
- [ ] Ensure accessibility attributes
- [ ] Add responsive behavior
- [ ] Test with different content lengths
