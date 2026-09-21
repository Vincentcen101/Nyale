<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    value: { type: [String, Number], default: '' },
    duration: { type: Number, default: 2000 },
});

// Splits "10,000+" into prefix "", number 10000, suffix "+" so the digits can be animated.
const match = String(props.value).match(/^(\D*?)(\d[\d,]*(?:\.\d+)?)(.*)$/);
const prefix = match ? match[1] : '';
const suffix = match ? match[3] : '';
const target = match ? parseFloat(match[2].replace(/,/g, '')) : null;
const decimals = match && match[2].includes('.') ? match[2].split('.')[1].length : 0;
const useCommas = match ? match[2].includes(',') : false;

const format = (n) =>
    prefix +
    (useCommas
        ? n.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })
        : n.toFixed(decimals)) +
    suffix;

const root = ref(null);
const display = ref(target === null ? String(props.value) : format(0));

let observer = null;
let frame = null;

const animate = () => {
    const start = performance.now();
    const step = (now) => {
        const progress = Math.min((now - start) / props.duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        display.value = format(target * eased);
        if (progress < 1) frame = requestAnimationFrame(step);
    };
    frame = requestAnimationFrame(step);
};

onMounted(() => {
    if (target === null) return;

    const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
    if (reduced || typeof IntersectionObserver === 'undefined') {
        display.value = format(target);
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                animate();
                observer.disconnect();
            }
        },
        { threshold: 0.4 },
    );
    observer.observe(root.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
    if (frame) cancelAnimationFrame(frame);
});
</script>

<template>
    <span ref="root">{{ display }}</span>
</template>
