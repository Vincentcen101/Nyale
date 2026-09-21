<script setup>
import { computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';
import RichContent from '@/Components/RichContent.vue';

const props = defineProps({
    post: { type: Object, required: true },
    related: { type: Array, default: () => [] },
    comments: { type: Array, default: () => [] },
    liked: { type: Boolean, default: false },
});

// Send readers back to the section they came from (News or Blog), not the mixed list.
const backLink = computed(() => {
    if (props.post.category === 'news') return { href: '/news?category=news', label: 'News' };
    if (props.post.category === 'blog') {
        const program = props.post.program?.slug;
        return { href: program ? `/news?category=blog&program=${program}` : '/news?category=blog', label: 'Blog' };
    }
    return { href: '/news', label: 'News & Updates' };
});

const relatedHeading = computed(() => {
    if (props.post.category === 'news') return 'More News';
    if (props.post.category === 'blog') return 'More Blog Posts';
    return 'More Stories';
});

const initials = (name) => (name || '?').trim().charAt(0).toUpperCase();

const toggleLike = () => {
    router.post(`/news/${props.post.id}/like`, {}, { preserveScroll: true, preserveState: true });
};

const commentForm = useForm({ name: '', email: '', body: '' });
const submitComment = () => {
    commentForm.post(`/news/${props.post.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
};
</script>

<template>
    <PublicLayout :title="post.title">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <Link :href="backLink.href" class="text-sm font-semibold text-white/70 hover:text-white">&larr; {{ backLink.label }}</Link>
                <p class="mt-4 text-xs font-bold uppercase tracking-widest text-white/80">{{ post.category?.replace('_', ' ') }}</p>
                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-white">{{ post.title }}</h1>
                <p v-if="post.published_at" class="mt-3 text-sm text-white/60">{{ post.published_at }}</p>
            </div>
        </section>

        <section v-if="post.cover_image" class="pt-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <img :src="`/storage/${post.cover_image}`" :alt="post.title"
                    class="w-full max-h-[28rem] object-cover rounded-3xl shadow-lg" />
            </div>
        </section>

        <section :class="post.cover_image ? 'pt-10 pb-16' : 'py-16'">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <RichContent :html="post.body" class="text-nyale-navy/80 leading-relaxed" />

                <!-- Like -->
                <div class="mt-10 flex flex-wrap items-center gap-4 border-y border-nyale-blue/10 py-5">
                    <button @click="toggleLike" type="button"
                        class="inline-flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-bold transition"
                        :class="liked ? 'bg-red-50 text-red-600' : 'bg-nyale-blue-light text-nyale-navy/70 hover:text-red-600'">
                        <svg class="h-5 w-5" :fill="liked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                        {{ liked ? 'Liked' : 'Like' }} &middot; {{ post.likes_count ?? 0 }}
                    </button>
                </div>

                <!-- Comments: Blog posts only -->
                <div v-if="post.category === 'blog'" class="mt-12">
                    <h2 class="text-xl font-extrabold text-nyale-navy mb-6">
                        Comments <span class="text-nyale-navy/40 font-semibold">({{ comments.length }})</span>
                    </h2>

                    <div class="space-y-5 mb-10">
                        <div v-for="comment in comments" :key="comment.id" class="flex gap-3">
                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-nyale-blue-light text-sm font-bold text-nyale-blue">
                                {{ initials(comment.name) }}
                            </div>
                            <div class="flex-1 rounded-2xl bg-nyale-blue-light/60 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-nyale-navy">{{ comment.name }}</span>
                                    <span class="text-xs text-nyale-navy/40">{{ comment.created_at }}</span>
                                </div>
                                <p class="mt-1 text-sm text-nyale-navy/70 leading-relaxed">{{ comment.body }}</p>
                            </div>
                        </div>
                        <p v-if="!comments.length" class="text-sm text-nyale-navy/40">Be the first to comment.</p>
                    </div>

                    <form @submit.prevent="submitComment" class="rounded-2xl bg-nyale-blue-light p-6 space-y-4">
                        <h3 class="font-bold text-nyale-navy">Leave a comment</h3>
                        <p class="-mt-2 text-xs text-nyale-navy/50">Comments are reviewed and appear once approved.</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <input v-model="commentForm.name" type="text" placeholder="Name" required
                                    class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue" />
                                <p v-if="commentForm.errors.name" class="mt-1 text-xs text-red-600">{{ commentForm.errors.name }}</p>
                            </div>
                            <div>
                                <input v-model="commentForm.email" type="email" placeholder="Email (optional)"
                                    class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue" />
                                <p v-if="commentForm.errors.email" class="mt-1 text-xs text-red-600">{{ commentForm.errors.email }}</p>
                            </div>
                        </div>
                        <div>
                            <textarea v-model="commentForm.body" rows="3" placeholder="Write your comment…" required
                                class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue"></textarea>
                            <p v-if="commentForm.errors.body" class="mt-1 text-xs text-red-600">{{ commentForm.errors.body }}</p>
                        </div>
                        <button type="submit" :disabled="commentForm.processing"
                            class="inline-flex items-center gap-2 rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 disabled:opacity-60">
                            {{ commentForm.processing ? 'Posting…' : 'Post Comment' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section v-if="related.length" class="py-16 bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">{{ relatedHeading }}</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    <Link v-for="p in related" :key="p.id" :href="`/news/${p.slug}`"
                        class="rounded-3xl bg-white p-6 shadow-sm transition hover:shadow-lg hover:-translate-y-1">
                        <Icon name="document-text" size="h-6 w-6 text-nyale-blue" />
                        <h3 class="mt-3 font-bold text-nyale-navy leading-snug">{{ p.title }}</h3>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
