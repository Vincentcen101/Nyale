<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';
import { useSortableRows } from '@/Composables/useSortableRows';

const props = defineProps({
    members: { type: Array, default: () => [] },
});

const { rows, tbody } = useSortableRows(() => props.members, '/dashboard/team/reorder');

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({ name: '', role: '', bio: '', photo: null, type: 'staff', email: '', linkedin: '' });

const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (m) => {
    editingId.value = m.id;
    form.name = m.name; form.role = m.role; form.bio = m.bio; form.photo = null;
    form.type = m.type; form.email = m.email; form.linkedin = m.linkedin;
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/team/${editingId.value}`, {
            preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.transform((data) => data).post('/dashboard/team', { preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); } });
    }
};
const destroy = async (id) => { if (await confirmDelete('Remove this member?')) router.delete(`/dashboard/team/${id}`, { preserveScroll: true }); };
const toggleActive = (id) => router.patch(`/dashboard/team/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppSidebarLayout title="Team & Board Members">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? (showForm = false) : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add Member' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Name</label>
                    <input v-model="form.name" required class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Role</label>
                    <input v-model="form.role" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Bio</label>
                <textarea v-model="form.bio" rows="3" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Type</label>
                    <select v-model="form.type" class="w-full rounded-xl border-nyale-blue/20">
                        <option value="board">Board</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Photo</label>
                    <input @input="form.photo = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">LinkedIn</label>
                    <input v-model="form.linkedin" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">{{ editingId ? 'Update' : 'Add' }}</button>
        </form>

        <p v-if="rows.length > 1" class="mb-3 text-xs text-nyale-navy/50">
            Tip: drag a member by their <span class="font-bold">⠿</span> handle to change the order. Board and staff are listed in this order on the About page. Changes save automatically.
        </p>
        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr><th class="w-10 px-2 py-3"><span class="sr-only">Reorder</span></th><th class="text-left px-5 py-3 font-semibold">Name</th><th class="text-left px-5 py-3 font-semibold">Type</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-right px-5 py-3 font-semibold">Actions</th></tr>
                </thead>
                <tbody ref="tbody" class="divide-y divide-nyale-blue/10">
                    <tr v-for="m in rows" :key="m.id" :data-id="m.id" class="bg-white">
                        <td class="px-2 py-3 text-center">
                            <span class="drag-handle cursor-grab active:cursor-grabbing select-none touch-none px-2 text-lg leading-none text-nyale-navy/40 hover:text-nyale-blue"
                                title="Drag to reorder" aria-label="Drag to reorder">⠿</span>
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-semibold text-nyale-navy">{{ m.name }}</p>
                            <p class="text-xs text-nyale-navy/50">{{ m.role }}</p>
                        </td>
                        <td class="px-5 py-3 text-nyale-navy/60 capitalize">{{ m.type }}</td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(m.id)" class="rounded-full px-3 py-1 text-xs font-bold" :class="m.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ m.is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(m)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(m.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!rows.length"><td colspan="5" class="px-5 py-8 text-center text-nyale-navy/40">No team members yet — the board and staff sections on the About page stay hidden until you add one.</td></tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
