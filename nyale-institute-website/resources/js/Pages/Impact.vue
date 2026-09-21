<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    stats: { type: Array, default: () => [] },
    stories: { type: Array, default: () => [] },
});

const categories = [
    { key: 'all', label: 'All Stories' },
    { key: 'litigation_outcome', label: 'Litigation Outcomes' },
    { key: 'policy_influence', label: 'Policy Influence' },
    { key: 'community_impact', label: 'Community Impact' },
    { key: 'story_of_change', label: 'Stories of Change' },
];

const activeCategory = ref('all');
const filteredStories = computed(() => {
    if (activeCategory.value === 'all') return props.stories;
    return props.stories.filter((s) => s.category === activeCategory.value);
});

const categoryLabel = (key) => categories.find((c) => c.key === key)?.label || key;
</script>

<template>
    <PublicLayout title="Our Impact">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Results &amp; Change</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Our Impact</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    The difference Nyale Institute's work is making — in courtrooms, in policy, and in communities
                    across Malawi.
                </p>
            </div>
        </section>

        <section v-if="stats.length" class="py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div v-for="stat in stats" :key="stat.id" class="rounded-3xl bg-nyale-blue-light p-6 text-center">
                        <p class="text-3xl sm:text-4xl font-extrabold text-nyale-blue">{{ stat.value }}</p>
                        <p class="mt-2 text-sm font-semibold text-nyale-navy/70">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="pb-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-2 mb-10 justify-center">
                    <button v-for="cat in categories" :key="cat.key" @click="activeCategory = cat.key"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="activeCategory === cat.key ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70 hover:bg-nyale-blue/10'">
                        {{ cat.label }}
                    </button>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <Link v-for="story in filteredStories" :key="story.id" :href="`/our-impact/${story.slug}`"
                        class="group rounded-3xl border border-nyale-blue/15 p-8 transition hover:shadow-lg hover:-translate-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-nyale-green">{{ categoryLabel(story.category) }}</p>
                        <h3 class="mt-3 text-lg font-extrabold text-nyale-navy leading-snug group-hover:text-nyale-blue">{{ story.title }}</h3>
                        <p class="mt-3 text-sm text-nyale-navy/70 leading-relaxed">{{ story.summary }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <p v-if="story.published_at" class="text-xs text-nyale-navy/40">{{ story.published_at }}</p>
                            <span class="text-sm font-bold text-nyale-blue">Read full story &rarr;</span>
                        </div>
                    </Link>
                </div>

                <p v-if="!filteredStories.length" class="text-center text-nyale-navy/50 py-16">
                    No stories in this category yet.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
