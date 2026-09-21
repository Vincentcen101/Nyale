<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    campaign: { type: Object, required: true },
});
</script>

<template>
    <PublicLayout :title="campaign.title">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <Link href="/campaigns" class="text-sm font-semibold text-white/70 hover:text-white">&larr; Campaigns &amp; Advocacy</Link>
                <h1 class="mt-4 text-4xl font-extrabold text-white">{{ campaign.title }}</h1>
                <p class="mt-4 text-white/85 leading-relaxed">{{ campaign.summary }}</p>
            </div>
        </section>

        <section v-if="campaign.banner" class="pt-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <img :src="`/storage/${campaign.banner}`" :alt="campaign.title"
                    class="w-full max-h-[28rem] object-cover rounded-3xl shadow-lg" />
            </div>
        </section>

        <section :class="campaign.banner ? 'pt-10 pb-16' : 'py-16'">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <p class="text-nyale-navy/80 leading-relaxed whitespace-pre-line">{{ campaign.body }}</p>
            </div>
        </section>
    </PublicLayout>
</template>
