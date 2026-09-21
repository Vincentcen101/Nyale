<script setup>
import { router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    resources: { type: Array, default: () => [] },
    filterType: { type: String, default: '' },
});

const types = [
    { key: '', label: 'All Resources' },
    { key: 'research_report', label: 'Research Reports' },
    { key: 'policy_brief', label: 'Policy Briefs' },
    { key: 'advocacy_manual', label: 'Advocacy Manuals' },
    { key: 'legal_resource', label: 'Legal Resources' },
    { key: 'issues_paper', label: 'Issues Papers' },
    { key: 'programme_report', label: 'Programme Reports' },
    { key: 'publication', label: 'Publications' },
];

const filter = (type) => {
    router.get('/knowledge-hub', type ? { type } : {}, { preserveScroll: true, preserveState: true });
};

const typeLabel = (key) => types.find((t) => t.key === key)?.label || key;
</script>

<template>
    <PublicLayout title="Knowledge Hub">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Evidence &amp; Knowledge</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Knowledge Hub</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/85 leading-relaxed">
                    Research reports, policy briefs, advocacy manuals and other knowledge products from Nyale
                    Institute.
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-2 mb-10 justify-center">
                    <button v-for="type in types" :key="type.key" @click="filter(type.key)"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="filterType === type.key ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70 hover:bg-nyale-blue/10'">
                        {{ type.label }}
                    </button>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="resource in resources" :key="resource.id"
                        class="rounded-3xl border border-nyale-blue/15 overflow-hidden flex flex-col">
                        <div v-if="resource.cover_image" class="h-40 overflow-hidden">
                            <img :src="`/storage/${resource.cover_image}`" :alt="resource.title" class="h-full w-full object-cover" />
                        </div>
                        <div class="p-7 flex flex-col flex-1">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-nyale-blue-light text-nyale-blue">
                                <Icon name="book" size="h-5 w-5" />
                            </div>
                            <p class="mt-4 text-xs font-bold uppercase tracking-wider text-nyale-green">{{ typeLabel(resource.type) }}</p>
                            <h3 class="mt-1 font-extrabold text-nyale-navy leading-snug">{{ resource.title }}</h3>
                            <p class="mt-2 text-sm text-nyale-navy/65 leading-relaxed flex-1">{{ resource.description }}</p>
                            <a v-if="resource.file_path" :href="`/knowledge-hub/${resource.id}/download`"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-nyale-blue hover:text-nyale-blue-dark">
                                Download PDF &darr;
                            </a>
                            <span v-else class="mt-5 text-sm font-semibold text-nyale-navy/40">Coming soon</span>
                        </div>
                    </div>
                </div>

                <p v-if="!resources.length" class="text-center text-nyale-navy/50 py-16">No resources found.</p>
            </div>
        </section>
    </PublicLayout>
</template>
