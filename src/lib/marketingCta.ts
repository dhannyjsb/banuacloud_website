import type { Router } from 'vue-router';

const externalTargetPattern = /^(https?:\/\/|mailto:|tel:)/i;

function scrollToHashTarget(target: string): boolean {
  const elementId = target.slice(1);

  if (!elementId) {
    return false;
  }

  const element = document.getElementById(elementId);

  if (!element) {
    return false;
  }

  element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  return true;
}

export function executeMarketingCtaTarget(router: Router, target?: string, fallbackTarget?: string): void {
  const targets = [target?.trim(), fallbackTarget?.trim()].filter(Boolean) as string[];

  for (const candidate of targets) {
    if (candidate.startsWith('#')) {
      if (scrollToHashTarget(candidate)) {
        return;
      }

      continue;
    }

    if (externalTargetPattern.test(candidate)) {
      window.location.assign(candidate);
      return;
    }

    if (candidate.startsWith('/')) {
      void router.push(candidate);
      return;
    }
  }
}
