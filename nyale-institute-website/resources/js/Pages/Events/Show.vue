<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';
import { formatDate, formatTimeRange, formatDay, formatMonth } from '@/Utils/dates';

const props = defineProps({
    event: { type: Object, required: true },
    related: { type: Array, default: () => [] },
});

const isPast = computed(() => new Date(props.event.starts_at) < new Date());
</script>

<template>
    <PublicLayout :title="event.title">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <Link href="/events" class="text-sm font-semibold text-white/70 hover:text-white">&larr; Events</Link>
                <p class="mt-4 text-xs font-bold uppercase tracking-widest text-white/80">{{ isPast ? 'Past event' : 'Upcoming event' }}</p>
                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-white">{{ event.title }}</h1>
            </div>
        </section>

        <section v-if="event.image" class="pt-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <img :src="`/storage/${event.image}`" :alt="event.title"
                    class="w-full max-h-[28rem] object-cover rounded-3xl shadow-lg" />
            </div>
        </section>

        <section :class="event.image ? 'pt-10 pb-16' : 'py-16'">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-wrap items-center gap-6 rounded-2xl bg-nyale-blue-light p-5">
                    <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-white text-center shadow-sm">
                        <span class="text-2xl font-extrabold leading-none text-nyale-blue">{{ formatDay(event.starts_at) }}</span>
                        <span class="mt-1 text-[10px] font-bold tracking-wider text-nyale-navy/60">{{ formatMonth(event.starts_at) }}</span>
                    </div>
                    <div class="text-sm text-nyale-navy/80">
                        <p class="font-bold text-nyale-navy">{{ formatDate(event.starts_at) }}</p>
                        <p>{{ formatTimeRange(event.starts_at, event.ends_at) }}</p>
                        <p v-if="event.location" class="mt-1">{{ event.location }}</p>
                    </div>
                    <a v-if="event.registration_url && !isPast" :href="event.registration_url" target="_blank" rel="noopener"
                        class="ml-auto inline-flex items-center gap-2 rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-nyale-blue-dark hover:-translate-y-0.5">
                        Register
                    </a>
                </div>

                <p v-if="event.summary" class="text-lg font-semibold text-nyale-navy/80 leading-relaxed mb-6">{{ event.summary }}</p>
                <p class="text-nyale-navy/80 leading-relaxed whitespace-pre-line">{{ event.body }}</p>
            </div>
        </section>

        <section v-if="related.length" class="py-16 bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">More Upcoming Events</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    <Link v-for="e in related" :key="e.id" :href="`/events/${e.slug}`"
                        class="rounded-3xl bg-white p-6 shadow-sm transition hover:shadow-lg hover:-translate-y-1">
                        <Icon name="calendar" size="h-6 w-6 text-nyale-blue" />
                        <p class="mt-3 text-xs font-bold uppercase tracking-wider text-nyale-green">{{ formatDate(e.starts_at) }}</p>
                        <h3 class="mt-1 font-bold text-nyale-navy leading-snug">{{ e.title }}</h3>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
