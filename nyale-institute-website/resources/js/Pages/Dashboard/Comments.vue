<script setup>
import { computed, ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    comments: { type: Array, default: () => [] },
    caseComments: { type: Array, default: () => [] },
    programs: { type: Array, default: () => [] },
});

const tab = ref('blog');

const blankFilters = () => ({
    search: '',
    status: 'all',
    program: '',
    item: '',
    caseStatus: '',
    from: '',
    to: '',
    sort: 'newest',
});
const filters = ref(blankFilters());
const clearFilters = () => { filters.value = blankFilters(); };
watch(tab, clearFilters);

const pending = (list) => list.filter((c) => !c.is_approved).length;
const basePath = computed(() => (tab.value === 'blog' ? '/dashboard/comments' : '/dashboard/case-comments'));
const source = computed(() => (tab.value === 'blog' ? props.comments : props.caseComments));

const caseStatusLabels = { ongoing: 'Ongoing', on_appeal: 'On appeal', concluded: 'Concluded' };

// Distinct blog posts / cases that have comments, for the "Post" / "Case" filter.
const itemOptions = computed(() => {
    const seen = new Map();
    source.value.forEach((c) => {
        const item = tab.value === 'blog' ? c.post : c.court_case;
        if (item && !seen.has(item.id)) seen.set(item.id, item.title);
    });
    return [...seen.entries()].map(([id, title]) => ({ id, title })).sort((a, b) => a.title.localeCompare(b.title));
});

const itemOf = (c) => (tab.value === 'blog' ? c.post : c.court_case);

const filtered = computed(() => {
    const f = filters.value;
    const term = f.search.trim().toLowerCase();

    const rows = source.value.filter((c) => {
        if (f.status === 'pending' && c.is_approved) return false;
        if (f.status === 'approved' && !c.is_approved) return false;

        if (term) {
            const haystack = [c.name, c.email, c.body, itemOf(c)?.title].filter(Boolean).join(' ').toLowerCase();
            if (!haystack.includes(term)) return false;
        }

        if (f.item && itemOf(c)?.id !== Number(f.item)) return false;

        if (tab.value === 'blog' && f.program) {
            const programId = c.post?.work_area_id ?? null;
            if (f.program === 'none' ? programId !== null : programId !== Number(f.program)) return false;
        }
        if (tab.value === 'cases' && f.caseStatus && c.court_case?.status !== f.caseStatus) return false;

        const day = String(c.created_at).slice(0, 10);
        if (f.from && day < f.from) return false;
        if (f.to && day > f.to) return false;

        return true;
    });

    return rows.sort((a, b) => {
        // Pending comments always stay on top, then order by date.
        if (a.is_approved !== b.is_approved) return a.is_approved ? 1 : -1;
        return f.sort === 'oldest'
            ? String(a.created_at).localeCompare(String(b.created_at))
            : String(b.created_at).localeCompare(String(a.created_at));
    });
});

const hasFilters = computed(() => JSON.stringify(filters.value) !== JSON.stringify(blankFilters()));

const target = (comment) => {
    if (tab.value === 'blog') {
        return comment.post ? { href: `/news/${comment.post.slug}`, label: comment.post.title } : null;
    }
    return comment.court_case ? { href: `/case-tracker/${comment.court_case.slug}`, label: comment.court_case.title } : null;
};

const toggleApproval = (comment) => {
    router.patch(`${basePath.value}/${comment.id}/approval`, {}, { preserveScroll: true });
};

const destroy = async (comment) => {
    if (await confirmDelete('Delete this comment?')) {
        router.delete(`${basePath.value}/${comment.id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <AppSidebarLayout title="Comments">
        <p class="mb-6 text-sm text-nyale-navy/60 max-w-2xl">
            Visitors' comments stay hidden until an editor or administrator approves them. Only Blog posts and Case Tracker cases accept comments.
        </p>

        <div class="flex gap-2 mb-6">
            <button @click="tab = 'blog'" class="rounded-full px-4 py-2 text-sm font-semibold"
                :class="tab === 'blog' ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70'">
                Blog
                <span v-if="pending(comments)" class="ml-1 rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-yellow-900">{{ pending(comments) }}</span>
            </button>
            <button @click="tab = 'cases'" class="rounded-full px-4 py-2 text-sm font-semibold"
                :class="tab === 'cases' ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70'">
                Case Tracker
                <span v-if="pending(caseComments)" class="ml-1 rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-yellow-900">{{ pending(caseComments) }}</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-6 rounded-2xl border border-nyale-blue/15 bg-nyale-blue-light/40 p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Search</label>
                    <input v-model="filters.search" type="search" placeholder="Name, email, comment text or title…"
                        class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Status</label>
                    <select v-model="filters.status" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="all">All</option>
                        <option value="pending">Pending approval</option>
                        <option value="approved">Approved</option>
                    </select>
                </div>
                <div v-if="tab === 'blog'">
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Program</label>
                    <select v-model="filters.program" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="">All programs</option>
                        <option v-for="program in programs" :key="program.id" :value="program.id">{{ program.title }}</option>
                        <option value="none">No program</option>
                    </select>
                </div>
                <div v-else>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Case status</label>
                    <select v-model="filters.caseStatus" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="">Any status</option>
                        <option v-for="(label, key) in caseStatusLabels" :key="key" :value="key">{{ label }}</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">{{ tab === 'blog' ? 'Blog post' : 'Case' }}</label>
                    <select v-model="filters.item" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="">{{ tab === 'blog' ? 'All blog posts' : 'All cases' }}</option>
                        <option v-for="item in itemOptions" :key="item.id" :value="item.id">{{ item.title }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">From</label>
                    <input v-model="filters.from" type="date" class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">To</label>
                    <input v-model="filters.to" type="date" class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                </div>
            </div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-nyale-navy/50">
                    Showing {{ filtered.length }} of {{ source.length }} comments
                </p>
                <div class="flex items-center gap-3">
                    <select v-model="filters.sort" class="rounded-xl border-nyale-blue/20 text-xs">
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                    <button v-if="hasFilters" @click="clearFilters" class="text-xs font-semibold text-nyale-blue hover:underline">
                        Clear filters
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div v-for="comment in filtered" :key="`${tab}-${comment.id}`"
                class="rounded-2xl border p-6"
                :class="comment.is_approved ? 'border-nyale-blue/15' : 'border-yellow-300 bg-yellow-50/50'">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-nyale-navy">
                            {{ comment.name }}
                            <span v-if="comment.email" class="font-normal text-nyale-navy/50">&lt;{{ comment.email }}&gt;</span>
                            <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-bold"
                                :class="comment.is_approved ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-yellow-200 text-yellow-800'">
                                {{ comment.is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </p>
                        <Link v-if="target(comment)" :href="target(comment).href" target="_blank"
                            class="block text-xs font-semibold text-nyale-blue hover:underline line-clamp-1">
                            on: {{ target(comment).label }}
                        </Link>
                        <p v-if="tab === 'blog' && comment.post?.program" class="text-xs font-semibold text-nyale-green">
                            Program: {{ comment.post.program.title }}
                        </p>
                        <p class="text-xs text-nyale-navy/40">
                            {{ comment.created_at }}
                            <span v-if="comment.is_approved && comment.approver"> · approved by {{ comment.approver.name }}</span>
                        </p>
                    </div>
                    <div class="flex-shrink-0 space-x-3 text-sm">
                        <button @click="toggleApproval(comment)" class="font-semibold hover:underline"
                            :class="comment.is_approved ? 'text-yellow-700' : 'text-nyale-green-dark'">
                            {{ comment.is_approved ? 'Hide' : 'Approve' }}
                        </button>
                        <button @click="destroy(comment)" class="font-semibold text-red-600 hover:underline">Delete</button>
                    </div>
                </div>
                <p class="mt-3 text-sm text-nyale-navy/70">{{ comment.body }}</p>
            </div>
            <p v-if="!filtered.length" class="text-center text-nyale-navy/40 py-12">
                {{ hasFilters ? 'No comments match these filters.' : 'No comments here yet.' }}
            </p>
        </div>
    </AppSidebarLayout>
</template>
