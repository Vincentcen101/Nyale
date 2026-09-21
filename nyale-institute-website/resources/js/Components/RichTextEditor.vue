<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Write here…' },
    minHeight: { type: String, default: '9rem' },
});
const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
const active = ref({ bold: false, italic: false });
let lastEmitted = null;

const escapeHtml = (text) =>
    text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

// Saved content is either HTML from this editor or older plain text (which keeps its line breaks).
const looksLikeHtml = (value) => /<[a-z][\s\S]*>|&(amp|lt|gt|quot|#39|nbsp);/i.test(value);
const toHtml = (value) => {
    if (!value) return '';
    return looksLikeHtml(value) ? value : escapeHtml(value).replace(/\r?\n/g, '<br>');
};

const emitValue = () => {
    const el = editor.value;
    const empty = !el.textContent.trim() && !el.querySelector('br, li');
    lastEmitted = empty ? '' : el.innerHTML;
    emit('update:modelValue', lastEmitted);
};

const refreshState = () => {
    const selection = window.getSelection();
    if (!editor.value || !selection?.anchorNode || !editor.value.contains(selection.anchorNode)) return;
    active.value = {
        bold: document.queryCommandState('bold'),
        italic: document.queryCommandState('italic'),
    };
};

const format = (command) => {
    editor.value.focus();
    document.execCommand(command, false, null);
    emitValue();
    refreshState();
};

const onPaste = (event) => {
    // Paste as plain text so formatting from Word/web pages never sneaks in.
    const text = event.clipboardData?.getData('text/plain') ?? '';
    document.execCommand('insertText', false, text);
};

onMounted(() => {
    editor.value.innerHTML = toHtml(props.modelValue);
    document.execCommand('defaultParagraphSeparator', false, 'p');
    document.addEventListener('selectionchange', refreshState);
});

onBeforeUnmount(() => document.removeEventListener('selectionchange', refreshState));

// Keep the editor in sync when the form is reset or filled for editing from outside.
watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && value !== lastEmitted) {
            editor.value.innerHTML = toHtml(value);
            lastEmitted = value;
        }
    },
);
</script>

<template>
    <div class="rounded-xl border border-nyale-blue/20 bg-white focus-within:border-nyale-blue focus-within:ring-1 focus-within:ring-nyale-blue">
        <div class="flex items-center gap-1 border-b border-nyale-blue/10 px-2 py-1.5">
            <button type="button" @mousedown.prevent="format('bold')" title="Bold (Ctrl+B)" aria-label="Bold"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-extrabold transition"
                :class="active.bold ? 'bg-nyale-blue text-white' : 'text-nyale-navy/70 hover:bg-nyale-blue-light'">
                B
            </button>
            <button type="button" @mousedown.prevent="format('italic')" title="Italic (Ctrl+I)" aria-label="Italic"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-sm italic font-semibold font-serif transition"
                :class="active.italic ? 'bg-nyale-blue text-white' : 'text-nyale-navy/70 hover:bg-nyale-blue-light'">
                I
            </button>
        </div>
        <div ref="editor" contenteditable="true" role="textbox" aria-multiline="true"
            :data-placeholder="placeholder" :style="{ minHeight }"
            class="rte-content rich-content px-3 py-2 text-sm text-nyale-navy focus:outline-none"
            @input="emitValue" @paste.prevent="onPaste" @keyup="refreshState" @mouseup="refreshState"></div>
    </div>
</template>

<style scoped>
.rte-content:empty::before {
    content: attr(data-placeholder);
    color: #9ca3af;
    pointer-events: none;
}
</style>
