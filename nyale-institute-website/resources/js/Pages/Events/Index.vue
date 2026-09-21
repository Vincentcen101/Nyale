<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';
import { formatDay, formatMonth, formatTimeRange, formatDate } from '@/Utils/dates';

defineProps({
    upcoming: { type: Array, default: () => [] },
    past: { type: Array, default: () => [] },
});
</script>

<template>
    <PublicLayout title="Events">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Join Us</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Events</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    Workshops, dialogues, campaigns and community gatherings hosted or supported by Nyale Institute.
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">Upcoming Events</h2>

                <div v-if="upcoming.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="(e, index) in upcoming" :key="e.id" :href="`/events/${e.slug}`" v-reveal="(index % 3) * 100"
                        class="group rounded-3xl border border-nyale-blue/15 overflow-hidden bg-white transition hover:shadow-xl hover:-translate-y-1">
                        <div class="relative h-44 overflow-hidden bg-gradient-to-br from-nyale-blue-light to-nyale-green/20 flex items-center justify-center">
                            <img v-if="e.image" :src="`/storage/${e.image}`" :alt="e.title"
                                class="h-full w-full object-cover transition group-hover:scale-105" />
                            <Icon v-else name="calendar" size="h-14 w-14 text-nyale-blue/40" />
                            <div class="absolute left-4 top-4 rounded-2xl bg-white px-3 py-2 text-center shadow-md">
                                <p class="text-xl font-extrabold leading-none text-nyale-blue">{{ formatDay(e.starts_at) }}</p>
                                <p class="mt-1 text-[10px] font-bold tracking-wider text-nyale-navy/60">{{ formatMonth(e.starts_at) }}</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-extrabold text-nyale-navy group-hover:text-nyale-blue">{{ e.title }}</h3>
                            <p class="mt-2 text-xs font-semibold text-nyale-navy/50">
                                {{ formatTimeRange(e.starts_at, e.ends_at) }}<span v-if="e.location"> &middot; {{ e.location }}</span>
                            </p>
                            <p class="mt-3 text-sm text-nyale-navy/65 leading-relaxed line-clamp-3">{{ e.summary }}</p>
                            <span class="mt-4 inline-block text-sm font-bold text-nyale-blue">View details &rarr;</span>
                        </div>
                    </Link>
                </div>
                <p v-else class="rounded-2xl bg-nyale-blue-light p-8 text-center text-nyale-navy/60">
                    No upcoming events right now — please check back soon.
                </p>

                <div v-if="past.length" class="mt-16">
                    <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">Past Events</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <Link v-for="e in past" :key="e.id" :href="`/events/${e.slug}`" v-reveal
                            class="group flex items-center gap-4 rounded-2xl border border-nyale-blue/15 p-4 transition hover:shadow-md hover:border-nyale-blue/40">
                            <div class="flex h-16 w-16 flex-shrink-0 flex-col items-center justify-center rounded-2xl bg-nyale-blue-light text-center">
                                <span class="text-xl font-extrabold leading-none text-nyale-blue">{{ formatDay(e.starts_at) }}</span>
                                <span class="mt-1 text-[10px] font-bold tracking-wider text-nyale-navy/60">{{ formatMonth(e.starts_at) }}</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-nyale-navy group-hover:text-nyale-blue line-clamp-2">{{ e.title }}</h3>
                                <p class="mt-1 text-xs text-nyale-navy/50">{{ formatDate(e.starts_at) }} &middot; {{ formatTimeRange(e.starts_at, e.ends_at) }}<span v-if="e.location"> &middot; {{ e.location }}</span></p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
