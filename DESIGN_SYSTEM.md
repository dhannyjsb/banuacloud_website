# Banua Cloud Design System

> Modern Dark Tech SaaS Design System for Indonesian Cloud Services Market

## Overview

Design system untuk website Banua Cloud dengan fokus pada aesthetic dark tech professional, menggunakan glassmorphism effects, dan modern interactions. Design ini dioptimalkan untuk market Indonesia dengan pendekatan SaaS enterprise look.

---

## 1. Color Palette

### 1.1 Primary Colors

| Token                 | Value     | Usage                   |
| --------------------- | --------- | ----------------------- |
| `--color-primary-50`  | `#f0f9ff` | Lightest sky tint       |
| `--color-primary-100` | `#e0f2fe` | Light backgrounds       |
| `--color-primary-200` | `#bae6fd` | Hover states            |
| `--color-primary-300` | `#7dd3fc` | Secondary accents       |
| `--color-primary-400` | `#38bdf8` | Icons, highlights       |
| `--color-primary-500` | `#0ea5e9` | **Primary brand color** |
| `--color-primary-600` | `#0284c7` | Buttons, CTAs           |
| `--color-primary-700` | `#0369a1` | Active states           |
| `--color-primary-800` | `#075985` | Dark variants           |
| `--color-primary-900` | `#0c4a6e` | Deepest primary         |

### 1.2 Secondary Colors (Cyan)

| Token                   | Value     | Usage                |
| ----------------------- | --------- | -------------------- |
| `--color-secondary-400` | `#22d3ee` | Gradient endpoints   |
| `--color-secondary-500` | `#06b6d4` | **Secondary accent** |
| `--color-secondary-600` | `#0891b2` | Hover states         |

### 1.3 Tertiary Colors (Violet/Purple)

| Token                  | Value     | Usage               |
| ---------------------- | --------- | ------------------- |
| `--color-tertiary-400` | `#a78bfa` | Gradient accents    |
| `--color-tertiary-500` | `#8b5cf6` | **Tertiary accent** |
| `--color-tertiary-600` | `#7c3aed` | Deep accents        |

### 1.4 Background Colors

| Token                  | Value                    | Usage                    |
| ---------------------- | ------------------------ | ------------------------ |
| `--color-bg-primary`   | `#0a0f1a`                | **Main page background** |
| `--color-bg-secondary` | `#0d1321`                | Card backgrounds         |
| `--color-bg-tertiary`  | `#111827`                | Elevated surfaces        |
| `--color-bg-elevated`  | `#1a1f2e`                | Modals, popovers         |
| `--color-bg-glass`     | `rgba(255,255,255,0.03)` | Glassmorphism base       |

### 1.5 Surface Colors (Glassmorphism)

| Token               | Value                    | Usage             |
| ------------------- | ------------------------ | ----------------- |
| `--color-surface-1` | `rgba(255,255,255,0.05)` | Hover surfaces    |
| `--color-surface-2` | `rgba(255,255,255,0.08)` | Borders, dividers |
| `--color-surface-3` | `rgba(255,255,255,0.10)` | Active surfaces   |
| `--color-surface-4` | `rgba(255,255,255,0.15)` | Focus states      |

### 1.6 Text Colors

| Token                    | Value     | Usage                    |
| ------------------------ | --------- | ------------------------ |
| `--color-text-primary`   | `#ffffff` | Headings, primary text   |
| `--color-text-secondary` | `#94a3b8` | Body text, descriptions  |
| `--color-text-tertiary`  | `#64748b` | Muted text, placeholders |
| `--color-text-disabled`  | `#475569` | Disabled states          |

### 1.7 Semantic Colors

| Token                | Value                  | Usage                      |
| -------------------- | ---------------------- | -------------------------- |
| `--color-success`    | `#10b981`              | Success states, checkmarks |
| `--color-success-bg` | `rgba(16,185,129,0.1)` | Success backgrounds        |
| `--color-warning`    | `#f59e0b`              | Warnings, alerts           |
| `--color-warning-bg` | `rgba(245,158,11,0.1)` | Warning backgrounds        |
| `--color-error`      | `#ef4444`              | Errors, danger             |
| `--color-error-bg`   | `rgba(239,68,68,0.1)`  | Error backgrounds          |
| `--color-info`       | `#0ea5e9`              | Info messages              |

### 1.8 Gradient Patterns

```css
/* Primary Gradient - Hero, CTAs */
--gradient-primary: linear-gradient(
  135deg,
  #0ea5e9 0%,
  #06b6d4 50%,
  #8b5cf6 100%
);

/* Sky Gradient - Cards, highlights */
--gradient-sky: linear-gradient(135deg, #0ea5e9 0%, #22d3ee 100%);

/* Aurora Gradient - Background effects */
--gradient-aurora: linear-gradient(
  135deg,
  #0ea5e9 0%,
  #06b6d4 25%,
  #8b5cf6 50%,
  #a78bfa 100%
);

/* Glass Border Gradient */
--gradient-border: linear-gradient(
  135deg,
  rgba(14, 165, 233, 0.5) 0%,
  rgba(6, 182, 212, 0.3) 100%
);
```

---

## 2. Typography System

### 2.1 Font Family

```css
--font-sans:
  "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
  sans-serif;
--font-mono: "JetBrains Mono", "Fira Code", Consolas, monospace;
```

### 2.2 Type Scale

| Style       | Size            | Weight | Line Height | Letter Spacing | Usage             |
| ----------- | --------------- | ------ | ----------- | -------------- | ----------------- |
| `display-1` | 4.5rem (72px)   | 800    | 1.1         | -0.02em        | Hero headlines    |
| `display-2` | 3.75rem (60px)  | 700    | 1.15        | -0.02em        | Section titles    |
| `h1`        | 3rem (48px)     | 700    | 1.2         | -0.01em        | Page titles       |
| `h2`        | 2.25rem (36px)  | 600    | 1.25        | -0.01em        | Section headers   |
| `h3`        | 1.875rem (30px) | 600    | 1.3         | 0              | Subsection titles |
| `h4`        | 1.5rem (24px)   | 600    | 1.35        | 0              | Card titles       |
| `h5`        | 1.25rem (20px)  | 600    | 1.4         | 0              | Small headers     |
| `h6`        | 1rem (16px)     | 600    | 1.5         | 0              | Labels            |
| `body-lg`   | 1.125rem (18px) | 400    | 1.7         | 0              | Lead paragraphs   |
| `body`      | 1rem (16px)     | 400    | 1.6         | 0              | Standard text     |
| `body-sm`   | 0.875rem (14px) | 400    | 1.5         | 0              | Secondary text    |
| `caption`   | 0.75rem (12px)  | 500    | 1.4         | 0.01em         | Captions, badges  |
| `overline`  | 0.75rem (12px)  | 600    | 1.4         | 0.1em          | Labels, uppercase |

### 2.3 Font Weights

| Token              | Value | Usage              |
| ------------------ | ----- | ------------------ |
| `--font-light`     | 300   | Large display text |
| `--font-regular`   | 400   | Body text          |
| `--font-medium`    | 500   | Buttons, emphasis  |
| `--font-semibold`  | 600   | Headings, labels   |
| `--font-bold`      | 700   | Strong emphasis    |
| `--font-extrabold` | 800   | Display headlines  |

---

## 3. Spacing System

### 3.1 Base Unit: 4px

| Token        | Value   | Pixels |
| ------------ | ------- | ------ |
| `--space-0`  | 0       | 0px    |
| `--space-1`  | 0.25rem | 4px    |
| `--space-2`  | 0.5rem  | 8px    |
| `--space-3`  | 0.75rem | 12px   |
| `--space-4`  | 1rem    | 16px   |
| `--space-5`  | 1.25rem | 20px   |
| `--space-6`  | 1.5rem  | 24px   |
| `--space-8`  | 2rem    | 32px   |
| `--space-10` | 2.5rem  | 40px   |
| `--space-12` | 3rem    | 48px   |
| `--space-16` | 4rem    | 64px   |
| `--space-20` | 5rem    | 80px   |
| `--space-24` | 6rem    | 96px   |
| `--space-32` | 8rem    | 128px  |

### 3.2 Section Spacing

| Token          | Value | Usage                  |
| -------------- | ----- | ---------------------- |
| `--section-sm` | 3rem  | Compact sections       |
| `--section-md` | 5rem  | Standard sections      |
| `--section-lg` | 8rem  | Hero, feature sections |
| `--section-xl` | 10rem | Major page sections    |

---

## 4. Border & Radius System

### 4.1 Border Radius

| Token           | Value          | Usage                |
| --------------- | -------------- | -------------------- |
| `--radius-none` | 0              | Sharp corners        |
| `--radius-sm`   | 0.375rem (6px) | Tags, small elements |
| `--radius-md`   | 0.5rem (8px)   | Inputs, buttons      |
| `--radius-lg`   | 0.75rem (12px) | Cards, containers    |
| `--radius-xl`   | 1rem (16px)    | Large cards          |
| `--radius-2xl`  | 1.5rem (24px)  | Feature cards        |
| `--radius-3xl`  | 2rem (32px)    | Hero containers      |
| `--radius-full` | 9999px         | Pills, avatars       |

### 4.2 Border Widths

| Token             | Value | Usage             |
| ----------------- | ----- | ----------------- |
| `--border-thin`   | 1px   | Default borders   |
| `--border-medium` | 2px   | Focus states      |
| `--border-thick`  | 3px   | Active indicators |

---

## 5. Shadow System

### 5.1 Drop Shadows

```css
/* Subtle elevation */
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);

/* Standard cards */
--shadow-md:
  0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -2px rgba(0, 0, 0, 0.3);

/* Elevated cards */
--shadow-lg:
  0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.4);

/* Modals, popovers */
--shadow-xl:
  0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.4);

/* Glow effects */
--shadow-glow-sm: 0 0 20px rgba(14, 165, 233, 0.3);
--shadow-glow-md: 0 0 40px rgba(14, 165, 233, 0.4);
--shadow-glow-lg: 0 0 60px rgba(14, 165, 233, 0.5);

/* Colored glows */
--shadow-glow-sky: 0 0 30px rgba(14, 165, 233, 0.4);
--shadow-glow-cyan: 0 0 30px rgba(6, 182, 212, 0.4);
--shadow-glow-violet: 0 0 30px rgba(139, 92, 246, 0.4);
```

---

## 6. Glassmorphism Specifications

### 6.1 Standard Glass Card

```css
.glass {
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
```

### 6.2 Glass Hover State

```css
.glass-hover:hover {
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(14, 165, 233, 0.3);
  box-shadow: 0 0 30px rgba(14, 165, 233, 0.1);
}
```

### 6.3 Glass Active State

```css
.glass-active {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(14, 165, 233, 0.5);
}
```

### 6.4 Glass Gradient Border

```css
.glass-gradient-border {
  position: relative;
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(10px);
}

.glass-gradient-border::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  padding: 1px;
  background: linear-gradient(
    135deg,
    rgba(14, 165, 233, 0.5),
    rgba(6, 182, 212, 0.3)
  );
  -webkit-mask:
    linear-gradient(#fff 0 0) content-box,
    linear-gradient(#fff 0 0);
  mask:
    linear-gradient(#fff 0 0) content-box,
    linear-gradient(#fff 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
}
```

---

## 7. Z-Index Scale

| Token                | Value | Usage               |
| -------------------- | ----- | ------------------- |
| `--z-base`           | 0     | Default layer       |
| `--z-dropdown`       | 100   | Dropdowns           |
| `--z-sticky`         | 200   | Sticky headers      |
| `--z-fixed`          | 300   | Fixed navigation    |
| `--z-modal-backdrop` | 400   | Modal overlays      |
| `--z-modal`          | 500   | Modal content       |
| `--z-popover`        | 600   | Popovers, tooltips  |
| `--z-toast`          | 700   | Toast notifications |
| `--z-tooltip`        | 800   | Tooltips            |

---

## 8. Breakpoints

| Token      | Value  | Usage            |
| ---------- | ------ | ---------------- |
| `--bp-sm`  | 640px  | Mobile landscape |
| `--bp-md`  | 768px  | Tablets          |
| `--bp-lg`  | 1024px | Small laptops    |
| `--bp-xl`  | 1280px | Desktops         |
| `--bp-2xl` | 1536px | Large screens    |

---

## 9. Transition Defaults

| Token               | Value                                  | Usage                |
| ------------------- | -------------------------------------- | -------------------- |
| `--duration-fast`   | 150ms                                  | Micro-interactions   |
| `--duration-normal` | 300ms                                  | Standard transitions |
| `--duration-slow`   | 500ms                                  | Complex animations   |
| `--ease-default`    | cubic-bezier(0.4, 0, 0.2, 1)           | Standard easing      |
| `--ease-in`         | cubic-bezier(0.4, 0, 1, 1)             | Enter animations     |
| `--ease-out`        | cubic-bezier(0, 0, 0.2, 1)             | Exit animations      |
| `--ease-bounce`     | cubic-bezier(0.68, -0.55, 0.265, 1.55) | Playful effects      |

---

## 10. Icon System

- **Library**: Lucide Vue Next
- **Default Size**: 24px (w-6 h-6)
- **Small Size**: 16px (w-4 h-4)
- **Large Size**: 32px (w-8 h-8)
- **Stroke Width**: 2px default

### Icon Sizes Usage

| Size | Class              | Usage           |
| ---- | ------------------ | --------------- |
| XS   | `w-3 h-3` (12px)   | Inline icons    |
| SM   | `w-4 h-4` (16px)   | Buttons, inputs |
| MD   | `w-5 h-5` (20px)   | Navigation      |
| LG   | `w-6 h-6` (24px)   | Feature icons   |
| XL   | `w-8 h-8` (32px)   | Large features  |
| 2XL  | `w-10 h-10` (40px) | Hero sections   |

---

## 11. Button Specifications

### 11.1 Button Variants

| Variant   | Background                                  | Text            | Border              | Hover                       |
| --------- | ------------------------------------------- | --------------- | ------------------- | --------------------------- |
| Primary   | `bg-gradient-to-r from-sky-500 to-cyan-500` | white           | none                | brightness-110, shadow-glow |
| Secondary | `bg-white/5`                                | `text-sky-400`  | `border-sky-500/30` | `bg-sky-500/10`             |
| Ghost     | transparent                                 | `text-gray-400` | none                | `text-white`, `bg-white/5`  |
| Outline   | transparent                                 | `text-white`    | `border-white/20`   | `border-sky-500/50`         |

### 11.2 Button Sizes

| Size | Height | Padding | Font Size |
| ---- | ------ | ------- | --------- |
| SM   | 32px   | 0 12px  | 14px      |
| MD   | 40px   | 0 20px  | 14px      |
| LG   | 48px   | 0 28px  | 16px      |
| XL   | 56px   | 0 36px  | 18px      |

---

## 12. Implementation Notes

### Tailwind CSS v4 Configuration

```javascript
// tailwind.config.js - Theme extension
theme: {
  extend: {
    colors: {
      // Primary (Sky)
      primary: {
        50: '#f0f9ff',
        500: '#0ea5e9',
        600: '#0284c7',
      },
      // Background
      bg: {
        primary: '#0a0f1a',
        secondary: '#0d1321',
        tertiary: '#111827',
      },
      // Surface
      surface: {
        1: 'rgba(255,255,255,0.05)',
        2: 'rgba(255,255,255,0.08)',
        3: 'rgba(255,255,255,0.10)',
      }
    },
    fontFamily: {
      sans: ['Inter', 'system-ui', 'sans-serif'],
    },
    animation: {
      'float': 'float 6s ease-in-out infinite',
      'pulse-glow': 'pulse-glow 3s ease-in-out infinite',
    }
  }
}
```

### CSS Custom Properties

All design tokens should be defined as CSS custom properties in `index.css` for runtime theming flexibility.
