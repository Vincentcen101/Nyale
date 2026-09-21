<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';
import RichContent from '@/Components/RichContent.vue';

defineProps({
    story: { type: Object, required: true },
    related: { type: Array, default: () => [] },
});

const categoryLabels = {
    litigation_outcome: 'Litigation Outcome',
    policy_influence: 'Policy Influence',
    community_impact: 'Community Impact',
    story_of_change: 'Story of Change',
};

const categoryIcons = {
    litigation_outcome: 'scale',
    policy_influence: 'document-text',
    community_impact: 'users',
    story_of_change: 'sparkles',
};
</script>

<template>
    <PublicLayout :title="story.title">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <Link href="/our-impact" class="text-sm font-semibold text-white/70 hover:text-white">&larr; Our Impact</Link>
                <p class="mt-4 text-xs font-bold uppercase tracking-widest text-white/80">
                    {{ categoryLabels[story.category] || 'Story of Change' }}
                </p>
                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-white">{{ story.title }}</h1>
                <p v-if="story.published_at" class="mt-3 text-sm text-white/60">{{ story.published_at }}</p>
            </div>
        </section>

        <section v-if="story.image" class="pt-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <img :src="`/storage/${story.image}`" :alt="story.title"
                    class="w-full max-h-[28rem] object-cover rounded-3xl shadow-lg" />
            </div>
        </section>

        <section :class="story.image ? 'pt-10 pb-16' : 'py-16'">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <p v-if="story.summary" class="text-lg font-semibold text-nyale-navy/80 leading-relaxed mb-6">{{ story.summary }}</p>
                <RichContent :html="story.body" class="text-nyale-navy/80 leading-relaxed" />
            </div>
        </section>

        <section v-if="related.length" class="py-16 bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">More Stories</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    <Link v-for="s in related" :key="s.id" :href="`/our-impact/${s.slug}`"
                        class="rounded-3xl bg-white p-6 shadow-sm transition hover:shadow-lg hover:-translate-y-1">
                        <Icon :name="categoryIcons[s.category] || 'sparkles'" size="h-6 w-6 text-nyale-blue" />
                        <p class="mt-3 text-xs font-bold uppercase tracking-wider text-nyale-green">
                            {{ categoryLabels[s.category] || 'Story of Change' }}
                        </p>
                        <h3 class="mt-1 font-bold text-nyale-navy leading-snug">{{ s.title }}</h3>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
