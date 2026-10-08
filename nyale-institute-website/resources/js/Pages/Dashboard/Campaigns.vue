<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    campaigns: { type: Array, default: () => [] },
});

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({ title: '', summary: '', body: '', banner: null, status: 'active', published_at: '' });

const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (c) => {
    editingId.value = c.id;
    form.title = c.title; form.summary = c.summary; form.body = c.body; form.banner = null;
    form.status = c.status; form.published_at = c.published_at;
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/campaigns/${editingId.value}`, {
            preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.transform((data) => data).post('/dashboard/campaigns', { preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); } });
    }
};
const destroy = async (id) => { if (await confirmDelete('Delete this campaign?')) router.delete(`/dashboard/campaigns/${id}`, { preserveScroll: true }); };
const toggleActive = (id) => router.patch(`/dashboard/campaigns/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppSidebarLayout title="Campaigns & Advocacy">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? (showForm = false) : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add Campaign' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                    <input v-model="form.title" required class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-xl border-nyale-blue/20">
                        <option value="active">Active</option>
                        <option value="past">Past</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Summary</label>
                <input v-model="form.summary" class="w-full rounded-xl border-nyale-blue/20" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Full description</label>
                <textarea v-model="form.body" rows="4" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Banner</label>
                    <input @input="form.banner = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Published</label>
                    <input v-model="form.published_at" type="date" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">{{ editingId ? 'Update' : 'Create' }}</button>
        </form>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">Campaign</th>
                        <th class="text-left px-5 py-3 font-semibold">Stage</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="c in campaigns" :key="c.id">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img v-if="c.banner" :src="`/storage/${c.banner}`" :alt="c.title"
                                    class="h-12 w-20 rounded-lg object-cover flex-shrink-0" />
                                <div v-else class="h-12 w-20 rounded-lg bg-nyale-blue-light flex-shrink-0"></div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-nyale-navy">{{ c.title }}</p>
                                    <p class="text-xs text-nyale-navy/50 line-clamp-1">{{ c.summary }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-xs font-bold uppercase" :class="c.status === 'active' ? 'text-nyale-green' : 'text-nyale-navy/40'">{{ c.status }}</td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(c.id)" class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="c.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ c.is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(c)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(c.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!campaigns.length">
                        <td colspan="4" class="px-5 py-8 text-center text-nyale-navy/40">
                            No campaigns yet — the public Campaigns page stays empty until you add one.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
