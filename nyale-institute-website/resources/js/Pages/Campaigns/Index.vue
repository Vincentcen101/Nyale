<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    campaigns: { type: Array, default: () => [] },
});
</script>

<template>
    <PublicLayout title="Campaigns & Advocacy">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Take Action</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Campaigns &amp; Advocacy</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    Public awareness initiatives, advocacy messages and opportunities to take action alongside Nyale
                    Institute.
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 gap-6">
                <Link v-for="campaign in campaigns" :key="campaign.id" :href="`/campaigns/${campaign.slug}`"
                    class="group rounded-3xl border border-nyale-blue/15 overflow-hidden transition hover:shadow-xl hover:-translate-y-1">
                    <div class="h-44 bg-gradient-to-br from-nyale-blue-light to-nyale-green/20 flex items-center justify-center overflow-hidden">
                        <img v-if="campaign.banner" :src="`/storage/${campaign.banner}`" :alt="campaign.title"
                            class="h-full w-full object-cover transition group-hover:scale-105" />
                        <Icon v-else name="megaphone" size="h-12 w-12 text-nyale-blue/40" />
                    </div>
                    <div class="p-7">
                        <span class="inline-block rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider"
                            :class="campaign.status === 'active' ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/60'">
                            {{ campaign.status === 'active' ? 'Active' : 'Past' }}
                        </span>
                        <h3 class="mt-3 text-xl font-extrabold text-nyale-navy group-hover:text-nyale-blue">{{ campaign.title }}</h3>
                        <p class="mt-2 text-sm text-nyale-navy/65 leading-relaxed">{{ campaign.summary }}</p>
                    </div>
                </Link>
            </div>
            <p v-if="!campaigns.length" class="text-center text-nyale-navy/50 py-16">No campaigns published yet.</p>
        </section>
    </PublicLayout>
</template>
