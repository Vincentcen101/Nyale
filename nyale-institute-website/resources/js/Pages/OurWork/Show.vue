<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    workArea: { type: Object, required: true },
    related: { type: Array, default: () => [] },
});
</script>

<template>
    <PublicLayout :title="workArea.title">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <Link href="/programs" class="text-sm font-semibold text-white/70 hover:text-white">&larr; Programs</Link>
                <div class="mt-4 flex h-16 w-16 mx-auto items-center justify-center rounded-2xl bg-white/15 text-white">
                    <Icon :name="workArea.icon" size="h-8 w-8" />
                </div>
                <h1 class="mt-5 text-4xl font-extrabold text-white">{{ workArea.title }}</h1>
                <p class="mt-4 text-white/85 leading-relaxed max-w-2xl mx-auto">{{ workArea.summary }}</p>
            </div>
        </section>

        <section v-if="workArea.image" class="pt-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <img :src="`/storage/${workArea.image}`" :alt="workArea.title"
                    class="w-full max-h-[28rem] object-cover rounded-3xl shadow-lg" />
            </div>
        </section>

        <section :class="workArea.image ? 'pt-10 pb-16' : 'py-16'">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 prose prose-slate">
                <p class="text-nyale-navy/80 leading-relaxed whitespace-pre-line">{{ workArea.body }}</p>
            </div>
        </section>

        <section v-if="related.length" class="py-16 bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-extrabold text-nyale-navy mb-8">Other Programs</h2>
                <div class="grid sm:grid-cols-3 gap-6">
                    <Link v-for="area in related" :key="area.id" :href="`/programs/${area.slug}`"
                        class="rounded-3xl bg-white p-6 shadow-sm transition hover:shadow-lg hover:-translate-y-1">
                        <Icon :name="area.icon" size="h-6 w-6 text-nyale-blue" />
                        <h3 class="mt-3 font-bold text-nyale-navy">{{ area.title }}</h3>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
