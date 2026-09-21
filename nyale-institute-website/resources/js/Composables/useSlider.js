import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

// Shared logic for the auto-advancing sliders on the home page.
export function useSlider(getItems, interval = 6000) {
    const current = ref(0);
    const items = computed(() => getItems() || []);
    const active = computed(() => items.value[current.value] || null);
    let timer = null;

    const next = () => {
        if (items.value.length) current.value = (current.value + 1) % items.value.length;
    };

    const prev = () => {
        if (items.value.length) current.value = (current.value - 1 + items.value.length) % items.value.length;
    };

    const go = (index) => {
        current.value = index;
    };

    const stop = () => {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    };

    const start = () => {
        stop();
        if (items.value.length > 1) timer = setInterval(next, interval);
    };

    onMounted(start);
    onBeforeUnmount(stop);

    return { current, active, next, prev, go, start, stop };
}
