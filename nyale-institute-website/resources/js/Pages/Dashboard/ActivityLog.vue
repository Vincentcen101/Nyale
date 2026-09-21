<script setup>
import { computed, reactive, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';

const props = defineProps({
    logs: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    users: { type: Array, default: () => [] },
    actions: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
});

const form = reactive({
    search: props.filters.search || '',
    user: props.filters.user || '',
    action: props.filters.action || '',
    module: props.filters.module || '',
    from: props.filters.from || '',
    to: props.filters.to || '',
});

const hasFilters = computed(() => Object.values(form).some(Boolean));
const moduleLabel = (m) => m.replace(/_/g, ' ');

const apply = () => {
    const params = Object.fromEntries(Object.entries(form).filter(([, v]) => v));
    router.get(route('dashboard.activity-logs'), params, { preserveState: true, preserveScroll: true, replace: true });
};

let timer;
watch(() => form.search, () => { clearTimeout(timer); timer = setTimeout(apply, 350); });
watch(() => [form.user, form.action, form.module, form.from, form.to], apply);

const clear = () => {
    clearTimeout(timer);
    Object.keys(form).forEach((k) => { form[k] = ''; });
};
</script>

<template>
    <AppSidebarLayout title="Activity Log">
        <div class="mb-4 rounded-2xl border border-nyale-blue/15 bg-nyale-blue-light/40 p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Search</label>
                    <input v-model="form.search" type="search" placeholder="Description or user name…"
                        class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">User</label>
                    <select v-model="form.user" class="w-full rounded-xl border-nyale-blue/20 text-sm">
                        <option value="">All users</option>
                        <option v-for="u in users" :key="u.id" :value="String(u.id)">{{ u.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Action</label>
                    <select v-model="form.action" class="w-full rounded-xl border-nyale-blue/20 text-sm capitalize">
                        <option value="">All actions</option>
                        <option v-for="a in actions" :key="a" :value="a">{{ a }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">Section</label>
                    <select v-model="form.module" class="w-full rounded-xl border-nyale-blue/20 text-sm capitalize">
                        <option value="">All sections</option>
                        <option v-for="m in modules" :key="m" :value="m">{{ moduleLabel(m) }}</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">From</label>
                        <input v-model="form.from" type="date" class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-nyale-navy/60 mb-1">To</label>
                        <input v-model="form.to" type="date" class="w-full rounded-xl border-nyale-blue/20 text-sm" />
                    </div>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <p class="text-xs text-nyale-navy/50">{{ logs.total }} {{ logs.total === 1 ? 'entry' : 'entries' }}</p>
                <button v-if="hasFilters" @click="clear" class="text-xs font-semibold text-nyale-blue hover:underline">Clear filters</button>
            </div>
        </div>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">User</th>
                        <th class="text-left px-5 py-3 font-semibold">Action</th>
                        <th class="text-left px-5 py-3 font-semibold">Section</th>
                        <th class="text-left px-5 py-3 font-semibold">Description</th>
                        <th class="text-left px-5 py-3 font-semibold">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="log in logs.data" :key="log.id">
                        <td class="px-5 py-3 font-semibold text-nyale-navy">{{ log.user?.name || '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-nyale-blue-light px-3 py-1 text-xs font-bold text-nyale-blue capitalize">{{ log.action }}</span>
                        </td>
                        <td class="px-5 py-3 text-nyale-navy/70 capitalize">{{ moduleLabel(log.module || '—') }}</td>
                        <td class="px-5 py-3 text-nyale-navy/70">{{ log.description }}</td>
                        <td class="px-5 py-3 text-nyale-navy/40 text-xs">{{ log.created_at }}</td>
                    </tr>
                    <tr v-if="!logs.data.length"><td colspan="5" class="px-5 py-8 text-center text-nyale-navy/40">{{ hasFilters ? 'No activity matches these filters.' : 'No activity recorded yet.' }}</td></tr>
                </tbody>
            </table>
        </div>

        <div v-if="logs.links?.length > 3" class="mt-6 flex flex-wrap justify-center gap-2">
            <Link v-for="(link, i) in logs.links" :key="i" :href="link.url || '#'" v-html="link.label"
                class="rounded-full px-4 py-2 text-sm font-semibold"
                :class="[link.active ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70', !link.url && 'opacity-40 pointer-events-none']" />
        </div>
    </AppSidebarLayout>
</template>
