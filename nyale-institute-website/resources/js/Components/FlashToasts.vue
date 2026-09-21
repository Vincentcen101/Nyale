<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const toasts = ref([]);
const timers = new Map();
let counter = 0;

const dismiss = (id) => {
    clearTimeout(timers.get(id));
    timers.delete(id);
    toasts.value = toasts.value.filter((t) => t.id !== id);
};

const push = (type, message) => {
    const id = ++counter;
    toasts.value.push({ id, type, message });
    timers.set(id, setTimeout(() => dismiss(id), type === 'error' ? 7000 : 4500));
};

// Every Inertia response carries a fresh `flash` object, so this fires once per new message
// (even when the same text repeats, e.g. deleting two records in a row).
watch(() => page.props.flash, (flash) => {
    if (flash?.success) push('success', flash.success);
    if (flash?.error) push('error', flash.error);
}, { immediate: true });

onBeforeUnmount(() => timers.forEach((t) => clearTimeout(t)));
</script>

<template>
    <Teleport to="body">
        <div class="pointer-events-none fixed right-4 top-4 z-[110] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3" aria-live="polite">
            <TransitionGroup enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 translate-x-6"
                enter-to-class="opacity-100 translate-x-0" leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100" leave-to-class="opacity-0 translate-x-6">
                <div v-for="toast in toasts" :key="toast.id" role="alert"
                    class="pointer-events-auto flex items-start gap-3 rounded-2xl border bg-white p-4 shadow-xl"
                    :class="toast.type === 'success' ? 'border-nyale-green/40' : 'border-red-200'">
                    <span class="mt-0.5 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full text-white"
                        :class="toast.type === 'success' ? 'bg-nyale-green' : 'bg-red-500'">
                        <svg v-if="toast.type === 'success'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v5m0 3.5h.01" />
                        </svg>
                    </span>
                    <p class="flex-1 text-sm font-semibold leading-snug text-nyale-navy">{{ toast.message }}</p>
                    <button type="button" @click="dismiss(toast.id)" class="text-nyale-navy/30 transition hover:text-nyale-navy" aria-label="Dismiss">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
