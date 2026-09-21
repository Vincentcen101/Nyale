<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { formatDate, formatTime } from '@/Utils/dates';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    submissions: { type: Array, default: () => [] },
});

const types = {
    partner: { label: 'Partner With Us', class: 'bg-nyale-blue/15 text-nyale-blue' },
    research_collaboration: { label: 'Research Collaboration', class: 'bg-purple-100 text-purple-700' },
    programme_collaboration: { label: 'Programme Collaboration', class: 'bg-indigo-100 text-indigo-700' },
    volunteer: { label: 'Volunteer / Internship', class: 'bg-orange-100 text-orange-700' },
    support: { label: 'Support Our Work', class: 'bg-nyale-green/20 text-nyale-green-dark' },
    general: { label: 'General Enquiry', class: 'bg-gray-100 text-gray-600' },
};

const statuses = {
    new: { label: 'New', dot: 'bg-nyale-blue', badge: 'bg-nyale-blue/15 text-nyale-blue' },
    in_progress: { label: 'In progress', dot: 'bg-yellow-500', badge: 'bg-yellow-100 text-yellow-700' },
    resolved: { label: 'Resolved', dot: 'bg-nyale-green', badge: 'bg-nyale-green/20 text-nyale-green-dark' },
};

const filters = ref({ search: '', status: 'all', type: '', from: '', to: '', sort: 'newest' });
const expanded = ref({});

const blank = () => ({ search: '', status: 'all', type: '', from: '', to: '', sort: 'newest' });
const hasFilters = computed(() => JSON.stringify(filters.value) !== JSON.stringify(blank()));
const clearFilters = () => { filters.value = blank(); };

const typeCount = (key) => props.submissions.filter((s) => s.type === key).length;

const rows = computed(() => {
    const f = filters.value;
    const term = f.search.trim().toLowerCase();

    return props.submissions
        .filter((s) => {
            if (f.status !== 'all' && s.status !== f.status) return false;
            if (f.type && s.type !== f.type) return false;
            if (term && ![s.name, s.email, s.organisation, s.phone, s.message].filter(Boolean).join(' ').toLowerCase().includes(term)) return false;
            const day = String(s.created_at).slice(0, 10);
            if (f.from && day < f.from) return false;
            if (f.to && day > f.to) return false;
            return true;
        })
        .sort((a, b) => (f.sort === 'oldest'
            ? String(a.created_at).localeCompare(String(b.created_at))
            : String(b.created_at).localeCompare(String(a.created_at))));
});

const ago = (value) => {
    const days = Math.floor((Date.now() - new Date(value).getTime()) / 86400000);
    if (days <= 0) return 'Today';
    if (days === 1) return 'Yesterday';
    if (days < 30) return `${days} days ago`;
    return `${Math.floor(days / 30)} mo ago`;
};

const initial = (name) => (name || '?').trim().charAt(0).toUpperCase();
const toggle = (id) => { expanded.value[id] = !expanded.value[id]; };

const updateStatus = (id, status) => router.patch(`/dashboard/get-involved/${id}/status`, { status }, { preserveScroll: true });
const destroy = async (id) => { if (await confirmDelete('Delete this enquiry?')) router.delete(`/dashboard/get-involved/${id}`, { preserveScroll: true }); };

const reply = (s) => `mailto:${s.email}?subject=${encodeURIComponent(`Re: your ${types[s.type]?.label || 'enquiry'} to Nyale Institute`)}`;

const exportCsv = () => {
    const cell = (v) => `"${String(v ?? '').replace(/"/g, '""')}"`;
    const header = ['Received', 'Name', 'Organisation', 'Email', 'Phone', 'Interested in', 'Status', 'Message'];
    const lines = rows.value.map((s) => [
        `${formatDate(s.created_at)} ${formatTime(s.created_at)}`, s.name, s.organisation, s.email, s.phone,
        types[s.type]?.label, statuses[s.status]?.label, s.message,
    ].map(cell).join(','));
    const blob = new Blob(['﻿' + [header.map(cell).join(','), ...lines].join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `get-involved-enquiries-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
};
</script>

<template>
    <AppSidebarLayout title="Get Involved — Enquiries">
        <!-- Interest filter (click to filter) -->
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-nyale-navy/40 mr-1">Interested in</span>
            <button v-for="(t, key) in types" :key="key" @click="filters.type = filters.type === key ? '' : key"
                class="rounded-full px-3 py-1.5 text-xs font-semibold transition"
                :class="[t.class, filters.type === key ? 'ring-2 ring-offset-1 ring-nyale-navy/40' : 'opacity-80 hover:opacity-100']">
                {{ t.label }} · {{ typeCount(key) }}
            </button>
        </div>

        <!-- Filters -->
        <div class="mb-4 rounded-2xl border border-nyale-blue/15 bg-nyale-blue-light/40 p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Search</label>
                    <input v-model="filters.search" type="search" placeholder="Name, organisation, email, phone or message…"
                        class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Status</label>
                    <select v-model="filters.status" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="all">All</option>
                        <option v-for="(st, key) in statuses" :key="key" :value="key">{{ st.label }}</option>
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
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Sort</label>
                    <select v-model="filters.sort" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="newest">Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-nyale-navy/50">Showing {{ rows.length }} of {{ submissions.length }} enquiries</p>
                <div class="flex items-center gap-4">
                    <button v-if="hasFilters" @click="clearFilters" class="text-xs font-semibold text-nyale-blue hover:underline">Clear filters</button>
                    <button @click="exportCsv" :disabled="!rows.length"
                        class="text-xs font-semibold text-nyale-navy/70 hover:text-nyale-blue disabled:opacity-40">
                        Download as CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Enquiries -->
        <div class="rounded-2xl border border-nyale-blue/15 overflow-x-auto">
            <table class="w-full min-w-[56rem] text-sm">
                <thead class="bg-nyale-blue-light text-left text-xs uppercase tracking-wider text-nyale-navy/60">
                    <tr>
                        <th class="px-5 py-3 font-bold">From</th>
                        <th class="px-5 py-3 font-bold">Contact</th>
                        <th class="px-5 py-3 font-bold">Interested in</th>
                        <th class="px-5 py-3 font-bold">Received</th>
                        <th class="px-5 py-3 font-bold">Status</th>
                        <th class="px-5 py-3 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="s in rows" :key="s.id">
                        <tr class="border-t border-nyale-blue/10 align-top transition hover:bg-nyale-blue-light/30"
                            :class="s.status === 'new' ? 'bg-nyale-blue/5' : ''">
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-nyale-blue-light font-bold text-nyale-blue">
                                        {{ initial(s.name) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-nyale-navy">{{ s.name }}</p>
                                        <p v-if="s.organisation" class="text-xs text-nyale-navy/60">{{ s.organisation }}</p>
                                        <p v-else class="text-xs text-nyale-navy/30">Individual</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <a :href="`mailto:${s.email}`" class="block font-medium text-nyale-blue hover:underline break-all">{{ s.email }}</a>
                                <a v-if="s.phone" :href="`tel:${s.phone}`" class="block text-xs text-nyale-navy/60 hover:underline">{{ s.phone }}</a>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-block rounded-full px-3 py-1 text-xs font-bold" :class="types[s.type]?.class">
                                    {{ types[s.type]?.label || s.type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-semibold text-nyale-navy">{{ formatDate(s.created_at) }}</p>
                                <p class="text-xs text-nyale-navy/50">{{ formatTime(s.created_at) }} · {{ ago(s.created_at) }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <select :value="s.status" @change="updateStatus(s.id, $event.target.value)"
                                    class="rounded-full border-0 py-1.5 pl-3 pr-8 text-xs font-bold focus:ring-2 focus:ring-nyale-blue"
                                    :class="statuses[s.status]?.badge">
                                    <option v-for="(st, key) in statuses" :key="key" :value="key">{{ st.label }}</option>
                                </select>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap space-x-3">
                                <button @click="toggle(s.id)" class="font-semibold text-nyale-blue hover:underline">
                                    {{ expanded[s.id] ? 'Hide message' : 'Read message' }}
                                </button>
                                <a :href="reply(s)" class="font-semibold text-nyale-green-dark hover:underline">Reply</a>
                                <button @click="destroy(s.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="expanded[s.id]" class="bg-nyale-blue-light/40">
                            <td colspan="6" class="px-5 pb-5 pt-2">
                                <p class="text-xs font-bold uppercase tracking-wider text-nyale-navy/40 mb-1">Message</p>
                                <p class="max-w-3xl whitespace-pre-line leading-relaxed text-nyale-navy/80">{{ s.message }}</p>
                            </td>
                        </tr>
                    </template>
                    <tr v-if="!rows.length">
                        <td colspan="6" class="px-5 py-12 text-center text-nyale-navy/40">
                            {{ hasFilters ? 'No enquiries match these filters.' : 'No enquiries received yet.' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
