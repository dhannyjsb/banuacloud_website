import { computed, type ComputedRef, toValue, watchEffect, type MaybeRefOrGetter } from 'vue';

export type SeoSchema = Record<string, unknown>;

export type SeoMetaInput = {
  title: string;
  description: string;
  canonicalPath?: string;
  image?: string;
  robots?: string;
  type?: string;
  schema?: SeoSchema[];
};

type InitialSeoPayload = {
  siteName?: string;
  image?: string;
};

declare global {
  interface Window {
    __INITIAL_SEO__?: InitialSeoPayload;
  }
}

const DEFAULT_DESCRIPTION = 'Mitra infrastruktur IT, cloud, dan jaringan untuk bisnis di Indonesia';
const DEFAULT_ROBOTS = 'index, follow';
const DEFAULT_TYPE = 'website';

function getInitialSeoPayload(): InitialSeoPayload {
  if (typeof window === 'undefined') {
    return {};
  }

  return window.__INITIAL_SEO__ ?? {};
}

function getSiteName(): string {
  return getInitialSeoPayload().siteName || 'Banua Cloud Nusantara';
}

function getDefaultImage(): string {
  return getInitialSeoPayload().image || `${window.location.origin}/favicon.svg`;
}

function upsertMeta(selector: string, attributes: Record<string, string>): void {
  let element = document.head.querySelector<HTMLMetaElement>(selector);

  if (!element) {
    element = document.createElement('meta');
    document.head.appendChild(element);
  }

  Object.entries(attributes).forEach(([key, value]) => {
    element?.setAttribute(key, value);
  });
}

function upsertLink(selector: string, attributes: Record<string, string>): void {
  let element = document.head.querySelector<HTMLLinkElement>(selector);

  if (!element) {
    element = document.createElement('link');
    document.head.appendChild(element);
  }

  Object.entries(attributes).forEach(([key, value]) => {
    element?.setAttribute(key, value);
  });
}

function removeManagedSchemas(): void {
  document.head.querySelectorAll('[data-seo-schema="managed"]').forEach((element) => {
    element.remove();
  });
}

function appendSchemas(schema: SeoSchema[]): void {
  removeManagedSchemas();

  schema.forEach((item) => {
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.dataset.seoSchema = 'managed';
    script.text = JSON.stringify(item);
    document.head.appendChild(script);
  });
}

function toAbsoluteUrl(path?: string, fallback?: string): string {
  if (typeof window === 'undefined') {
    return fallback || '';
  }

  if (!path) {
    return fallback || window.location.href;
  }

  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }

  return new URL(path, window.location.origin).toString();
}

export function applySeoMeta(input: SeoMetaInput): void {
  if (typeof document === 'undefined' || typeof window === 'undefined') {
    return;
  }

  const siteName = getSiteName();
  const title = input.title.includes(siteName) ? input.title : `${input.title} | ${siteName}`;
  const description = input.description || DEFAULT_DESCRIPTION;
  const robots = input.robots || DEFAULT_ROBOTS;
  const type = input.type || DEFAULT_TYPE;
  const canonical = toAbsoluteUrl(input.canonicalPath, window.location.href);
  const image = toAbsoluteUrl(input.image, getDefaultImage());

  document.title = title;

  upsertMeta('meta[name="description"]', { name: 'description', content: description });
  upsertMeta('meta[name="robots"]', { name: 'robots', content: robots });
  upsertMeta('meta[property="og:type"]', { property: 'og:type', content: type });
  upsertMeta('meta[property="og:site_name"]', { property: 'og:site_name', content: siteName });
  upsertMeta('meta[property="og:title"]', { property: 'og:title', content: title });
  upsertMeta('meta[property="og:description"]', { property: 'og:description', content: description });
  upsertMeta('meta[property="og:url"]', { property: 'og:url', content: canonical });
  upsertMeta('meta[property="og:image"]', { property: 'og:image', content: image });
  upsertMeta('meta[name="twitter:card"]', { name: 'twitter:card', content: 'summary_large_image' });
  upsertMeta('meta[name="twitter:title"]', { name: 'twitter:title', content: title });
  upsertMeta('meta[name="twitter:description"]', { name: 'twitter:description', content: description });
  upsertMeta('meta[name="twitter:image"]', { name: 'twitter:image', content: image });
  upsertLink('link[rel="canonical"]', { rel: 'canonical', href: canonical });

  appendSchemas(input.schema ?? []);
}

export function useSeo(input: MaybeRefOrGetter<SeoMetaInput>): ComputedRef<SeoMetaInput> {
  const resolvedInput = computed(() => toValue(input));

  watchEffect(() => {
    applySeoMeta(resolvedInput.value);
  });

  return resolvedInput;
}
