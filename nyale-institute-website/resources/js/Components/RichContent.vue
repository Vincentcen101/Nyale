<script setup>
import { computed } from 'vue';

const props = defineProps({
    html: { type: String, default: '' },
});

// Newer content is sanitised HTML from the editor; older content is plain text with line breaks.
const isHtml = computed(() => /<[a-z][\s\S]*>|&(amp|lt|gt|quot|#39|nbsp);/i.test(props.html || ''));
</script>

<template>
    <!-- Content is cleaned server-side (App\Support\RichText) before it is stored. -->
    <div v-if="isHtml" class="rich-content" v-html="html"></div>
    <div v-else class="whitespace-pre-line">{{ html }}</div>
</template>
