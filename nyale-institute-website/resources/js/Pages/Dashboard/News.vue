<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    posts: { type: Array, default: () => [] },
    programs: { type: Array, default: () => [] },
});

const categories = ['news', 'blog'];

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({ title: '', category: 'news', work_area_id: '', body: '', cover_image: null, published_at: '' });

const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (p) => {
    editingId.value = p.id;
    form.title = p.title; form.category = p.category; form.work_area_id = p.work_area_id ?? ''; form.body = p.body;
    form.cover_image = null; form.published_at = p.published_at;
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/news/${editingId.value}`, {
            preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.transform((data) => data).post('/dashboard/news', { preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); } });
    }
};
const destroy = async (id) => { if (await confirmDelete('Delete this post?')) router.delete(`/dashboard/news/${id}`, { preserveScroll: true }); };
const toggleActive = (id) => router.patch(`/dashboard/news/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppSidebarLayout title="News & Updates">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? (showForm = false) : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add Post' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                    <input v-model="form.title" required class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Category</label>
                    <select v-model="form.category" required class="w-full rounded-xl border-nyale-blue/20">
                        <option v-for="c in categories" :key="c" :value="c">{{ c.replace('_', ' ') }}</option>
                    </select>
                </div>
            </div>
            <div v-if="form.category === 'blog'">
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Program this blog belongs to</label>
                <select v-model="form.work_area_id" required class="w-full rounded-xl border-nyale-blue/20">
                    <option value="" disabled>Select a program…</option>
                    <option v-for="program in programs" :key="program.id" :value="program.id">{{ program.title }}</option>
                </select>
                <p v-if="form.errors.work_area_id" class="text-xs text-red-600 mt-1">{{ form.errors.work_area_id }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Full content</label>
                <RichTextEditor v-model="form.body" min-height="12rem" placeholder="Write the full post…" />
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Cover Image</label>
                    <input @input="form.cover_image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Published</label>
                    <input v-model="form.published_at" type="date" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">{{ editingId ? 'Update' : 'Publish' }}</button>
        </form>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr><th class="text-left px-5 py-3 font-semibold">Title</th><th class="text-left px-5 py-3 font-semibold">Category</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-right px-5 py-3 font-semibold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="p in posts" :key="p.id">
                        <td class="px-5 py-3 font-semibold text-nyale-navy">{{ p.title }}</td>
                        <td class="px-5 py-3 text-nyale-navy/60">{{ p.category.replace('_', ' ') }}<span v-if="p.program" class="block text-xs text-nyale-green">{{ p.program.title }}</span></td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(p.id)" class="rounded-full px-3 py-1 text-xs font-bold" :class="p.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ p.is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(p)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(p.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!posts.length"><td colspan="4" class="px-5 py-8 text-center text-nyale-navy/40">No posts yet.</td></tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
