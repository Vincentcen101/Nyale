<script setup>
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { confirmState, settleConfirm } from '@/Composables/useConfirm';

const confirmButton = ref(null);

const onKey = (event) => {
    if (event.key === 'Escape') settleConfirm(false);
};

watch(() => confirmState.open, async (open) => {
    if (open) {
        window.addEventListener('keydown', onKey);
        await nextTick();
        confirmButton.value?.focus();
    } else {
        window.removeEventListener('keydown', onKey);
    }
});

onBeforeUnmount(() => window.removeEventListener('keydown', onKey));
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="confirmState.open" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                role="alertdialog" aria-modal="true" aria-labelledby="confirm-title">
                <div class="absolute inset-0 bg-nyale-navy/50 backdrop-blur-sm" @click="settleConfirm(false)"></div>

                <div class="relative w-full max-w-md rounded-3xl bg-white p-7 text-center shadow-2xl">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full"
                        :class="confirmState.tone === 'danger' ? 'bg-red-100 text-red-600' : confirmState.tone === 'warning' ? 'bg-yellow-100 text-yellow-600' : 'bg-nyale-blue-light text-nyale-blue'">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path v-if="confirmState.tone === 'info'" stroke-linecap="round" stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>

                    <h2 id="confirm-title" class="text-lg font-extrabold text-nyale-navy">{{ confirmState.title }}</h2>
                    <p v-if="confirmState.message" class="mt-2 text-sm leading-relaxed text-nyale-navy/60">{{ confirmState.message }}</p>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
                        <button type="button" @click="settleConfirm(false)"
                            class="rounded-full border border-nyale-navy/15 px-6 py-2.5 text-sm font-bold text-nyale-navy/70 transition hover:bg-nyale-blue-light">
                            {{ confirmState.cancelText }}
                        </button>
                        <button ref="confirmButton" type="button" @click="settleConfirm(true)"
                            class="rounded-full px-6 py-2.5 text-sm font-bold text-white shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="confirmState.tone === 'danger' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : confirmState.tone === 'warning' ? 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-500' : 'bg-nyale-blue hover:bg-nyale-blue-dark focus:ring-nyale-blue'">
                            {{ confirmState.confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
