<script setup>
defineProps({
    // Places the logo on a white chip so it stays readable on dark or saturated backgrounds.
    dark: { type: Boolean, default: false },
    iconOnly: { type: Boolean, default: false },
    size: { type: String, default: 'md' },
});

const heights = { sm: 'h-8', md: 'h-11', nav: 'h-14', lg: 'h-16' };
const iconHeights = { sm: 'h-7', md: 'h-9', nav: 'h-12', lg: 'h-14' };
</script>

<template>
    <span class="inline-flex items-center select-none" :class="dark ? 'rounded-xl bg-white px-2.5 py-1.5 shadow-sm' : ''">
        <img v-if="iconOnly" src="/images/logo-icon.png" alt="Nyale Institute" :class="iconHeights[size] || iconHeights.md" class="w-auto" />
        <img v-else src="/images/logo.png" alt="Nyale Institute" :class="heights[size] || heights.md" class="w-auto" />
    </span>
</template>
