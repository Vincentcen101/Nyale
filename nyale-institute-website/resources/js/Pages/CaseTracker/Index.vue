<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { formatDate } from '@/Utils/dates';

const props = defineProps({
    cases: { type: Array, default: () => [] },
});

// Every case shares the same illustration.
const caseImage = '/images/case-justice.webp';

const statuses = [
    { key: 'all', label: 'All Cases' },
    { key: 'ongoing', label: 'Ongoing' },
    { key: 'on_appeal', label: 'On Appeal' },
    { key: 'concluded', label: 'Concluded' },
];

const statusLabel = (key) => statuses.find((s) => s.key === key)?.label || key;
const statusClass = {
    ongoing: 'bg-yellow-100 text-yellow-700',
    on_appeal: 'bg-orange-100 text-orange-700',
    concluded: 'bg-nyale-green/15 text-nyale-green-dark',
};

const active = ref('all');
const filtered = computed(() => (active.value === 'all' ? props.cases : props.cases.filter((c) => c.status === active.value)));
</script>

<template>
    <PublicLayout title="Case Tracker">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Strategic Litigation</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Case Tracker</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    Follow the court cases Nyale Institute has brought or supported — from filing to outcome.
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-2 mb-10 justify-center">
                    <button v-for="s in statuses" :key="s.key" @click="active = s.key"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="active === s.key ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70 hover:bg-nyale-blue/10'">
                        {{ s.label }}
                    </button>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="(c, index) in filtered" :key="c.id" :href="`/case-tracker/${c.slug}`" v-reveal="(index % 3) * 100"
                        class="group flex flex-col rounded-3xl border border-nyale-blue/15 overflow-hidden bg-white transition hover:shadow-xl hover:-translate-y-1">
                        <div class="relative h-44 overflow-hidden bg-gradient-to-br from-nyale-blue-light to-nyale-green/20 flex items-center justify-center">
                            <img :src="caseImage" alt="Lady Justice holding the scales of justice" loading="lazy"
                                class="h-full w-full object-contain p-3 transition duration-300 group-hover:scale-105" />
                            <span v-if="c.lawyer" class="absolute bottom-0 left-0 bg-nyale-green px-3 py-1 text-xs font-semibold text-white">
                                {{ c.lawyer }}
                            </span>
                        </div>
                        <h3 class="bg-nyale-blue px-5 py-3 text-sm font-bold leading-snug text-white line-clamp-3 group-hover:bg-nyale-blue-dark transition">
                            {{ c.title }}
                        </h3>
                        <div class="flex flex-1 flex-col p-5">
                            <span class="self-start rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider" :class="statusClass[c.status]">
                                {{ statusLabel(c.status) }}
                            </span>
                            <p class="mt-3 text-sm text-nyale-navy/65 leading-relaxed line-clamp-4 flex-1">{{ c.summary }}</p>
                            <div class="mt-4 flex items-center justify-between text-xs text-nyale-navy/50">
                                <span>{{ formatDate(c.case_date) }}</span>
                                <span class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                        </svg>
                                        {{ c.likes_count }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                        </svg>
                                        {{ c.comments_count }}
                                    </span>
                                </span>
                            </div>
                            <span class="mt-4 text-sm font-bold text-nyale-blue">Read more &rarr;</span>
                        </div>
                    </Link>
                </div>

                <p v-if="!filtered.length" class="text-center text-nyale-navy/50 py-16">No cases in this category yet.</p>
            </div>
        </section>
    </PublicLayout>
</template>
