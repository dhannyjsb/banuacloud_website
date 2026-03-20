import type { RouteLocationNormalizedLoaded, Router } from 'vue-router';
import { trackVisitorVisit } from './siteApi';

const VISITOR_TOKEN_KEY = 'visitor_token';

let lastTrackedPath = '';

export function registerTrafficTracking(router: Router): void {
  if (typeof window === 'undefined') {
    return;
  }

  router.isReady().then(() => {
    void trackRoute(router.currentRoute.value);
  });

  router.afterEach((to, from) => {
    if (buildTrackablePath(to) === buildTrackablePath(from)) {
      return;
    }

    void trackRoute(to);
  });
}

async function trackRoute(route: RouteLocationNormalizedLoaded): Promise<void> {
  if (!shouldTrackRoute(route)) {
    return;
  }

  const path = buildTrackablePath(route);

  if (path === lastTrackedPath) {
    return;
  }

  lastTrackedPath = path;

  try {
    await trackVisitorVisit({
      visitorToken: getVisitorToken(),
      path,
      routeName: typeof route.name === 'string' ? route.name : null,
      pageTitle: typeof document !== 'undefined' ? document.title : null,
      referrerUrl: typeof document !== 'undefined' && document.referrer ? document.referrer : null,
      utmSource: currentSearchParams().get('utm_source'),
      utmMedium: currentSearchParams().get('utm_medium'),
      utmCampaign: currentSearchParams().get('utm_campaign'),
    });
  } catch {
    // Ignore analytics failures so public navigation is never interrupted.
  }
}

function shouldTrackRoute(route: RouteLocationNormalizedLoaded): boolean {
  return !route.path.startsWith('/admin') && ['Home', 'LearnMore', 'ServiceDetail'].includes(String(route.name ?? ''));
}

function getVisitorToken(): string {
  const existingToken = localStorage.getItem(VISITOR_TOKEN_KEY);

  if (existingToken) {
    return existingToken;
  }

  const generatedToken = typeof crypto !== 'undefined' && 'randomUUID' in crypto
    ? crypto.randomUUID()
    : `visitor-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;

  localStorage.setItem(VISITOR_TOKEN_KEY, generatedToken);

  return generatedToken;
}

function buildTrackablePath(route: RouteLocationNormalizedLoaded): string {
  const search = queryString(route);

  return search ? `${route.path}?${search}` : route.path;
}

function queryString(route: RouteLocationNormalizedLoaded): string {
  const params = new URLSearchParams();

  Object.entries(route.query).forEach(([key, value]) => {
    if (Array.isArray(value)) {
      value.forEach((item) => {
        if (typeof item === 'string') {
          params.append(key, item);
        }
      });

      return;
    }

    if (typeof value === 'string') {
      params.set(key, value);
    }
  });

  return params.toString();
}

function currentSearchParams(): URLSearchParams {
  return new URLSearchParams(typeof window !== 'undefined' ? window.location.search : '');
}
