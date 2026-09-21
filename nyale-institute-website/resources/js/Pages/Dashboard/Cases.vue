<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { confirmDelete } from '@/Composables/useConfirm';

defineProps({
    cases: { type: Array, default: () => [] },
});

const statusLabels = { ongoing: 'Ongoing', on_appeal: 'On appeal', concluded: 'Concluded' };

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({
    title: '', case_number: '', court: '', lawyer: '', status: 'ongoing',
    summary: '', body: '', image: null, case_date: '',
});

const close = () => { showForm.value = false; form.reset(); };
const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (c) => {
    editingId.value = c.id;
    form.title = c.title; form.case_number = c.case_number ?? ''; form.court = c.court ?? ''; form.lawyer = c.lawyer ?? '';
    form.status = c.status; form.summary = c.summary ?? ''; form.body = c.body ?? ''; form.image = null;
    form.case_date = c.case_date ?? '';
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/cases/${editingId.value}`, { preserveScroll: true, onSuccess: close });
    } else {
        form.transform((data) => data).post('/dashboard/cases', { preserveScroll: true, onSuccess: close });
    }
};
const destroy = async (id) => { if (await confirmDelete('Delete this case?', 'Its comments will be removed too.')) router.delete(`/dashboard/cases/${id}`, { preserveScroll: true }); };
const toggleActive = (id) => router.patch(`/dashboard/cases/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppSidebarLayout title="Case Tracker">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? close() : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add Case' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <h3 class="font-bold text-nyale-navy">{{ editingId ? 'Edit Case' : 'New Case' }}</h3>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Case title (parties)</label>
                <textarea v-model="form.title" rows="2" required class="w-full rounded-xl border-nyale-blue/20"></textarea>
                <p v-if="form.errors.title" class="text-xs text-red-600 mt-1">{{ form.errors.title }}</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Case number</label>
                    <input v-model="form.case_number" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Court</label>
                    <input v-model="form.court" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Lead lawyer</label>
                    <input v-model="form.lawyer" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-xl border-nyale-blue/20">
                        <option value="ongoing">Ongoing</option>
                        <option value="on_appeal">On appeal</option>
                        <option value="concluded">Concluded</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Date (filed / decided)</label>
                    <input v-model="form.case_date" type="date" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Summary</label>
                <textarea v-model="form.summary" rows="2" maxlength="1000" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Full case details</label>
                <RichTextEditor v-model="form.body" min-height="12rem" placeholder="Write the full case details…" />
            </div>
            <button type="submit" :disabled="form.processing" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white disabled:opacity-60">
                {{ editingId ? 'Update' : 'Create' }}
            </button>
        </form>

        <div class="space-y-3">
            <div v-for="c in cases" :key="c.id" class="rounded-2xl border border-nyale-blue/15 p-5 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase text-nyale-green">{{ statusLabels[c.status] }}<span v-if="c.case_date" class="text-nyale-navy/40"> · {{ c.case_date }}</span></p>
                    <p class="font-semibold text-nyale-navy line-clamp-2">{{ c.title }}</p>
                    <p class="text-xs text-nyale-navy/50 mt-1">{{ c.likes_count }} likes</p>
                </div>
                <div class="flex-shrink-0 space-x-3 text-xs">
                    <button @click="toggleActive(c.id)" class="font-semibold" :class="c.is_active ? 'text-nyale-green-dark' : 'text-nyale-navy/40'">
                        {{ c.is_active ? 'Live' : 'Hidden' }}
                    </button>
                    <button @click="openEdit(c)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                    <button @click="destroy(c.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                </div>
            </div>
            <p v-if="!cases.length" class="text-center text-nyale-navy/40 py-8">No cases yet.</p>
        </div>
    </AppSidebarLayout>
</template>
