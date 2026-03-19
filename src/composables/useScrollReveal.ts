import { nextTick, onMounted, onUnmounted, ref, type Ref } from 'vue';

export function useScrollReveal(rootRef: Ref<HTMLElement | null>) {
  const observer = ref<IntersectionObserver | null>(null);
  const mutationObserver = ref<MutationObserver | null>(null);

  const observeTargets = () => {
    const root = rootRef.value;
    if (!root || !observer.value) {
      return;
    }

    const targets = root.querySelectorAll<HTMLElement>('.reveal, .reveal-stagger');

    targets.forEach((element) => {
      if (element.classList.contains('is-visible')) {
        return;
      }

      observer.value?.observe(element);
    });
  };

  onMounted(() => {
    observer.value = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.value?.unobserve(entry.target);
          }
        }
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
    );

    const root = rootRef.value;
    if (!root) {
      return;
    }

    void nextTick(() => {
      observeTargets();
    });

    mutationObserver.value = new MutationObserver(() => {
      observeTargets();
    });

    mutationObserver.value.observe(root, {
      childList: true,
      subtree: true,
    });
  });

  onUnmounted(() => {
    mutationObserver.value?.disconnect();
    observer.value?.disconnect();
  });
}
