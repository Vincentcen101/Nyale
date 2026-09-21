<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import EngagementBar from '@/Components/EngagementBar.vue';
import RichContent from '@/Components/RichContent.vue';
import { formatDate } from '@/Utils/dates';

defineProps({
    courtCase: { type: Object, required: true },
    related: { type: Array, default: () => [] },
    comments: { type: Array, default: () => [] },
    liked: { type: Boolean, default: false },
});

const statusLabels = { ongoing: 'Ongoing', on_appeal: 'On Appeal', concluded: 'Concluded' };
</script>

<template>
    <PublicLayout :title="courtCase.title">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <Link href="/case-tracker" class="text-sm font-semibold text-white/70 hover:text-white">&larr; Case Tracker</Link>
                <p class="mt-4 text-xs font-bold uppercase tracking-widest text-white/80">{{ statusLabels[courtCase.status] || courtCase.status }}</p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold leading-snug text-white">{{ courtCase.title }}</h1>
                <p v-if="courtCase.case_date" class="mt-3 text-sm text-white/60">{{ formatDate(courtCase.case_date) }}</p>
            </div>
        </section>

        <section class="pt-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-center rounded-3xl bg-gradient-to-br from-nyale-blue-light to-nyale-green/20 p-6">
                    <img src="/images/case-justice.webp" alt="Lady Justice holding the scales of justice"
                        class="h-56 sm:h-72 w-auto object-contain" />
                </div>
            </div>
        </section>

        <section class="pt-10 pb-16">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <dl v-if="courtCase.case_number || courtCase.court || courtCase.lawyer"
                    class="mb-8 grid sm:grid-cols-3 gap-4 rounded-2xl bg-nyale-blue-light p-5 text-sm">
                    <div v-if="courtCase.case_number">
                        <dt class="text-xs font-bold uppercase tracking-wider text-nyale-navy/50">Case number</dt>
                        <dd class="mt-1 font-semibold text-nyale-navy">{{ courtCase.case_number }}</dd>
                    </div>
                    <div v-if="courtCase.court">
                        <dt class="text-xs font-bold uppercase tracking-wider text-nyale-navy/50">Court</dt>
                        <dd class="mt-1 font-semibold text-nyale-navy">{{ courtCase.court }}</dd>
                    </div>
                    <div v-if="courtCase.lawyer">
                        <dt class="text-xs font-bold uppercase tracking-wider text-nyale-navy/50">Lead lawyer</dt>
                        <dd class="mt-1 font-semibold text-nyale-navy">{{ courtCase.lawyer }}</dd>
                    </div>
                </dl>

                <p v-if="courtCase.summary" class="text-lg font-semibold text-nyale-navy/80 leading-relaxed mb-6">{{ courtCase.summary }}</p>
                <RichContent :html="courtCase.body" class="text-nyale-navy/80 leading-relaxed" />

                <EngagementBar :title="courtCase.title" :like-url="`/case-tracker/${courtCase.id}/like`"
                    :comment-url="`/case-tracker/${courtCase.id}/comments`" :liked="liked"
                    :likes-count="courtCase.likes_count" :comments="comments" />
            </div>
        </section>

        <section v-if="related.length" class="py-16 bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">More Cases</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    <Link v-for="c in related" :key="c.id" :href="`/case-tracker/${c.slug}`"
                        class="rounded-3xl bg-white p-6 shadow-sm transition hover:shadow-lg hover:-translate-y-1">
                        <p class="text-xs font-bold uppercase tracking-wider text-nyale-green">{{ statusLabels[c.status] || c.status }}</p>
                        <h3 class="mt-2 font-bold text-nyale-navy leading-snug line-clamp-4">{{ c.title }}</h3>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
