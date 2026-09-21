<script setup>
import { computed } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    posts: { type: Object, required: true },
    filterCategory: { type: String, default: '' },
    filterProgram: { type: String, default: '' },
});

const programs = computed(() => usePage().props.site?.programs || []);

// News and Blog are separate sections: when viewing one, only that content is shown.
const sections = {
    news: { title: 'News', eyebrow: 'Latest', blurb: 'The latest news from Nyale Institute.' },
    blog: { title: 'Blog', eyebrow: 'Stories & Insights', blurb: 'Blog posts from across our programs.' },
};
const section = computed(() => sections[props.filterCategory] || null);

const categories = [
    { key: '', label: 'All' },
    { key: 'news', label: 'News' },
    { key: 'programme_update', label: 'Programme Updates' },
    { key: 'field_story', label: 'Field Stories' },
    { key: 'blog', label: 'Blog' },
    { key: 'opinion', label: 'Opinion' },
    { key: 'media', label: 'Media' },
];

const filter = (category) => {
    router.get('/news', category ? { category } : {}, { preserveScroll: true, preserveState: true });
};

const filterByProgram = (slug) => {
    router.get('/news', slug ? { category: 'blog', program: slug } : { category: 'blog' }, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <PublicLayout :title="section ? section.title : 'News & Updates'">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">{{ section ? section.eyebrow : 'Stay Informed' }}</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">{{ section ? section.title : 'News & Updates' }}</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    {{ section ? section.blurb : 'News, programme updates, field stories, blogs and opinion pieces from Nyale Institute.' }}
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="!section" class="flex flex-wrap gap-2 mb-10 justify-center">
                    <button v-for="cat in categories" :key="cat.key" @click="filter(cat.key)"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="filterCategory === cat.key ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70 hover:bg-nyale-blue/10'">
                        {{ cat.label }}
                    </button>
                </div>

                <div v-if="filterCategory === 'blog'" class="flex flex-wrap items-center gap-2 mb-10 -mt-4 justify-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-nyale-navy/40 mr-1">Program</span>
                    <button @click="filterByProgram('')"
                        class="rounded-full px-3 py-1.5 text-xs font-semibold transition"
                        :class="!filterProgram ? 'bg-nyale-green text-white' : 'bg-nyale-green/10 text-nyale-green-dark hover:bg-nyale-green/20'">
                        All programs
                    </button>
                    <button v-for="program in programs" :key="program.id" @click="filterByProgram(program.slug)"
                        class="rounded-full px-3 py-1.5 text-xs font-semibold transition"
                        :class="filterProgram === program.slug ? 'bg-nyale-green text-white' : 'bg-nyale-green/10 text-nyale-green-dark hover:bg-nyale-green/20'">
                        {{ program.title }}
                    </button>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="post in posts.data" :key="post.id" :href="`/news/${post.slug}`"
                        class="group rounded-3xl border border-nyale-blue/10 overflow-hidden transition hover:shadow-lg hover:-translate-y-1">
                        <div class="h-40 bg-gradient-to-br from-nyale-blue-light to-nyale-blue/20 flex items-center justify-center overflow-hidden">
                            <img v-if="post.cover_image" :src="`/storage/${post.cover_image}`" :alt="post.title"
                                class="h-full w-full object-cover transition group-hover:scale-105" />
                            <Icon v-else name="document-text" size="h-10 w-10 text-nyale-blue/40" />
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-nyale-green">{{ post.category?.replace('_', ' ') }}<span v-if="post.program" class="text-nyale-blue normal-case tracking-normal"> · {{ post.program.title }}</span></p>
                            <h3 class="mt-2 font-bold text-nyale-navy group-hover:text-nyale-blue">{{ post.title }}</h3>
                            <p class="mt-2 text-sm text-nyale-navy/60 line-clamp-2">{{ post.excerpt }}</p>
                        </div>
                    </Link>
                </div>

                <p v-if="!posts.data.length" class="text-center text-nyale-navy/50 py-16">
                    {{ section ? `No ${section.title.toLowerCase()} posts yet.` : 'No posts found.' }}
                </p>

                <div v-if="posts.links.length > 3" class="mt-12 flex flex-wrap justify-center gap-2">
                    <Link v-for="(link, i) in posts.links" :key="i" :href="link.url || '#'"
                        v-html="link.label"
                        class="rounded-full px-4 py-2 text-sm font-semibold"
                        :class="[link.active ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70', !link.url && 'opacity-40 pointer-events-none']" />
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
