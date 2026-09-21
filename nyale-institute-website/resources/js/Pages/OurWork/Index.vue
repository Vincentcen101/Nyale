<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    workAreas: { type: Array, default: () => [] },
});
</script>

<template>
    <PublicLayout title="Programs">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">What We Do</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Programs</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    Nyale Institute advances sexual and reproductive health governance in Malawi through its
                    interconnected programs.
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="area in workAreas" :key="area.id" :href="`/programs/${area.slug}`"
                        class="group rounded-3xl border border-nyale-blue/15 overflow-hidden transition hover:shadow-xl hover:-translate-y-1">
                        <div v-if="area.image" class="h-40 overflow-hidden">
                            <img :src="`/storage/${area.image}`" :alt="area.title"
                                class="h-full w-full object-cover transition group-hover:scale-105" />
                        </div>
                        <div class="p-8">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-nyale-blue-light text-nyale-blue">
                                <Icon :name="area.icon" size="h-7 w-7" />
                            </div>
                            <h3 class="mt-5 text-xl font-extrabold text-nyale-navy group-hover:text-nyale-blue">{{ area.title }}</h3>
                            <p class="mt-2 text-sm text-nyale-navy/65 leading-relaxed">{{ area.summary }}</p>
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-nyale-blue">
                                Read more &rarr;
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
