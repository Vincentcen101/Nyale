<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';

const isAdmin = computed(() => usePage().props.auth?.user?.role === 'admin');

const props = defineProps({
    counts: { type: Object, default: () => ({}) },
    recentSubmissions: { type: Array, default: () => [] },
    recentPosts: { type: Array, default: () => [] },
    recentActivity: { type: Array, default: () => [] },
});

const cards = [
    { key: 'workAreas', label: 'Programs', href: '/dashboard/work-areas' },
    { key: 'impactStories', label: 'Impact Stories', href: '/dashboard/impact' },
    { key: 'knowledgeResources', label: 'Knowledge Resources', href: '/dashboard/knowledge-hub' },
    { key: 'campaigns', label: 'Campaigns', href: '/dashboard/campaigns' },
    { key: 'posts', label: 'News & Updates', href: '/dashboard/news' },
    { key: 'cases', label: 'Cases', href: '/dashboard/cases' },
    { key: 'events', label: 'Events', href: '/dashboard/events' },
    { key: 'newSubmissions', label: 'New Enquiries', href: '/dashboard/get-involved' },
];
</script>

<template>
    <AppSidebarLayout title="Overview">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <Link v-for="card in cards" :key="card.key" :href="card.href"
                class="rounded-2xl border border-nyale-blue/15 p-6 transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-3xl font-extrabold text-nyale-blue">{{ counts[card.key] ?? 0 }}</p>
                <p class="mt-1 text-sm font-semibold text-nyale-navy/60">{{ card.label }}</p>
            </Link>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-nyale-blue/15 p-6">
                <h2 class="font-bold text-nyale-navy mb-4">Recent Get Involved Enquiries</h2>
                <div v-if="!recentSubmissions.length" class="text-sm text-nyale-navy/40">No enquiries yet.</div>
                <ul class="space-y-3">
                    <li v-for="s in recentSubmissions" :key="s.id" class="text-sm border-b border-nyale-blue/10 pb-3 last:border-0">
                        <p class="font-semibold text-nyale-navy">{{ s.name }} <span class="font-normal text-nyale-navy/40">— {{ s.type }}</span></p>
                        <p class="text-nyale-navy/60 line-clamp-1">{{ s.message }}</p>
                    </li>
                </ul>
            </div>

            <div v-if="isAdmin" class="rounded-2xl border border-nyale-blue/15 p-6">
                <h2 class="font-bold text-nyale-navy mb-4">Recent Activity</h2>
                <div v-if="!recentActivity.length" class="text-sm text-nyale-navy/40">No activity recorded yet.</div>
                <ul class="space-y-3">
                    <li v-for="log in recentActivity" :key="log.id" class="text-sm border-b border-nyale-blue/10 pb-3 last:border-0">
                        <p class="text-nyale-navy">{{ log.description }}</p>
                        <p class="text-xs text-nyale-navy/40">{{ log.user?.name }} &middot; {{ log.created_at }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </AppSidebarLayout>
</template>
