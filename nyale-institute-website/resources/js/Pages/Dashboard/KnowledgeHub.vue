<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    resources: { type: Array, default: () => [] },
});

const types = ['research_report', 'policy_brief', 'advocacy_manual', 'legal_resource', 'issues_paper', 'programme_report', 'publication'];

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({ title: '', type: 'research_report', description: '', file: null, cover_image: null, published_at: '' });

const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (r) => {
    editingId.value = r.id;
    form.title = r.title; form.type = r.type; form.description = r.description;
    form.file = null; form.cover_image = null; form.published_at = r.published_at;
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/knowledge-hub/${editingId.value}`, {
            preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.transform((data) => data).post('/dashboard/knowledge-hub', { preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); } });
    }
};
const destroy = async (id) => { if (await confirmDelete('Delete this resource?')) router.delete(`/dashboard/knowledge-hub/${id}`, { preserveScroll: true }); };
const toggleActive = (id) => router.patch(`/dashboard/knowledge-hub/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppSidebarLayout title="Knowledge Hub">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? (showForm = false) : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add Resource' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                    <input v-model="form.title" required class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Type</label>
                    <select v-model="form.type" class="w-full rounded-xl border-nyale-blue/20">
                        <option v-for="t in types" :key="t" :value="t">{{ t.replace('_', ' ') }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Description</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">PDF File</label>
                    <input @input="form.file = $event.target.files[0]" type="file" accept="application/pdf" class="w-full text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Cover Image</label>
                    <input @input="form.cover_image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Published</label>
                    <input v-model="form.published_at" type="date" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">{{ editingId ? 'Update' : 'Upload' }}</button>
        </form>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr><th class="text-left px-5 py-3 font-semibold">Title</th><th class="text-left px-5 py-3 font-semibold">Type</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-right px-5 py-3 font-semibold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="r in resources" :key="r.id">
                        <td class="px-5 py-3 font-semibold text-nyale-navy">{{ r.title }}</td>
                        <td class="px-5 py-3 text-nyale-navy/60">{{ r.type.replace('_', ' ') }}</td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(r.id)" class="rounded-full px-3 py-1 text-xs font-bold" :class="r.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ r.is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(r)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(r.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!resources.length"><td colspan="4" class="px-5 py-8 text-center text-nyale-navy/40">No resources yet.</td></tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
