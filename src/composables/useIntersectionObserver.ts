import { onUnmounted, ref, type Ref } from 'vue';

interface UseIntersectionObserverOptions {
  threshold?: number;
  rootMargin?: string;
  triggerOnce?: boolean;
}

export function useIntersectionObserver(
  elementRef: Ref<HTMLElement | null>,
  options: UseIntersectionObserverOptions = {}
) {
  const { threshold = 0.1, rootMargin = '0px', triggerOnce = true } = options;

  const isVisible = ref(false);
  let observer: IntersectionObserver | null = null;

  const initObserver = () => {
    if (!elementRef.value || observer) return;

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            isVisible.value = true;
            if (triggerOnce && observer) {
              observer.unobserve(entry.target);
            }
          } else if (!triggerOnce) {
            isVisible.value = false;
          }
        });
      },
      {
        threshold,
        rootMargin,
      }
    );

    observer.observe(elementRef.value);
  };

  onUnmounted(() => {
    if (observer) {
      observer.disconnect();
      observer = null;
    }
  });

  return {
    isVisible,
    initObserver,
  };
}

export function useScrollAnimation() {
  const isVisible = ref(false);
  let observer: IntersectionObserver | null = null;
  let mutationObserver: MutationObserver | null = null;
  const observedElements = new WeakSet<Element>();

  const observeAnimatedElement = (element: Element) => {
    if (!observer || observedElements.has(element)) {
      return;
    }

    observedElements.add(element);
    observer.observe(element);
  };

  const observeAnimatedTree = (root: ParentNode) => {
    root.querySelectorAll('.scroll-animate').forEach((element) => {
      observeAnimatedElement(element);
    });
  };

  const initScrollAnimations = () => {
    if (observer) {
      observer.disconnect();
      observer = null;
    }

    if (mutationObserver) {
      mutationObserver.disconnect();
      mutationObserver = null;
    }

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            // Optionally unobserve after animation triggers
            if (observer) {
              observer.unobserve(entry.target);
            }
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px',
      }
    );

    observeAnimatedTree(document);

    mutationObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (!(node instanceof Element)) {
            return;
          }

          if (node.matches('.scroll-animate')) {
            observeAnimatedElement(node);
          }

          observeAnimatedTree(node);
        });
      });
    });

    mutationObserver.observe(document.body, {
      childList: true,
      subtree: true,
    });
  };

  onUnmounted(() => {
    if (observer) {
      observer.disconnect();
      observer = null;
    }

    if (mutationObserver) {
      mutationObserver.disconnect();
      mutationObserver = null;
    }
  });

  return {
    isVisible,
    initScrollAnimations,
  };
}
