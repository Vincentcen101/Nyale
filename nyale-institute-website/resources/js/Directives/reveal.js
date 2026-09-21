let observer = null;

const reducedMotion = () =>
    typeof window !== 'undefined' && window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

const cleanup = (el) => {
    el.classList.remove('reveal', 'reveal-visible', 'reveal-up', 'reveal-left', 'reveal-right', 'reveal-zoom');
    el.style.transitionDelay = '';
};

const getObserver = () => {
    if (observer) return observer;

    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                el.classList.add('reveal-visible');
                observer.unobserve(el);
                // Hand the element back to its own hover/transition styles once the reveal has played.
                const delay = parseFloat(el.style.transitionDelay) || 0;
                setTimeout(() => cleanup(el), delay + 900);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
    );

    return observer;
};

// Usage: v-reveal, v-reveal:left, v-reveal:zoom, v-reveal="150" (delay in ms)
export default {
    beforeMount(el, binding) {
        if (reducedMotion() || typeof IntersectionObserver === 'undefined') return;
        el.classList.add('reveal', `reveal-${binding.arg || 'up'}`);
        if (binding.value) el.style.transitionDelay = `${binding.value}ms`;
    },
    mounted(el) {
        if (!el.classList.contains('reveal')) return;
        getObserver().observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
