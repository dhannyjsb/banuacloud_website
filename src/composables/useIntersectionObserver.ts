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

  const initScrollAnimations = () => {
    // Clean up existing observer if any
    if (observer) {
      observer.disconnect();
      observer = null;
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

    // Observe all scroll-animate elements
    const animatedElements = document.querySelectorAll('.scroll-animate');
    animatedElements.forEach((el) => {
      observer?.observe(el);
    });
  };

  onUnmounted(() => {
    if (observer) {
      observer.disconnect();
      observer = null;
    }
  });

  return {
    isVisible,
    initScrollAnimations,
  };
}
