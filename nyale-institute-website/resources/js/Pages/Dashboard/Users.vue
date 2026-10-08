<script setup>
import { ref } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmAction } from '@/Composables/useConfirm';

defineProps({
    users: { type: Array, default: () => [] },
});

const page = usePage();
const currentUserId = page.props.auth.user?.id;

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({ name: '', email: '', password: '', role: 'editor' });

const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (u) => {
    editingId.value = u.id;
    form.name = u.name; form.email = u.email; form.role = u.role; form.password = '';
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.put(`/dashboard/users/${editingId.value}`, { preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); } });
    } else {
        form.post('/dashboard/users', { preserveScroll: true, onSuccess: () => { showForm.value = false; form.reset(); } });
    }
};
const suspend = async (id) => {
    const ok = await confirmAction({
        title: 'Deactivate this account?',
        message: 'They will no longer be able to sign in. All their data is kept and you can reactivate the account at any time.',
        confirmText: 'Deactivate',
        tone: 'warning',
    });
    if (ok) router.post(`/dashboard/users/${id}/suspend`, {}, { preserveScroll: true });
};
const activate = async (id) => {
    const ok = await confirmAction({
        title: 'Reactivate this account?',
        message: 'They will be able to sign in again.',
        confirmText: 'Reactivate',
        tone: 'info',
    });
    if (ok) router.post(`/dashboard/users/${id}/activate`, {}, { preserveScroll: true });
};
</script>

<template>
    <AppSidebarLayout title="Users">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? (showForm = false) : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add User' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 grid sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Name</label>
                <input v-model="form.name" required class="w-full rounded-xl border-nyale-blue/20" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Email</label>
                <input v-model="form.email" type="email" required class="w-full rounded-xl border-nyale-blue/20" />
            </div>
            <div v-if="!editingId">
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Password</label>
                <input v-model="form.password" type="password" class="w-full rounded-xl border-nyale-blue/20" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Role</label>
                <select v-model="form.role" class="w-full rounded-xl border-nyale-blue/20">
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">{{ editingId ? 'Update' : 'Create' }}</button>
        </form>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr><th class="text-left px-5 py-3 font-semibold">Name</th><th class="text-left px-5 py-3 font-semibold">Role</th><th class="text-left px-5 py-3 font-semibold">Status</th><th class="text-right px-5 py-3 font-semibold">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="u in users" :key="u.id">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-nyale-navy">{{ u.name }}</p>
                            <p class="text-xs text-nyale-navy/50">{{ u.email }}</p>
                        </td>
                        <td class="px-5 py-3 text-nyale-navy/60 capitalize">{{ u.role }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="u.status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-nyale-green/20 text-nyale-green-dark'">
                                {{ u.status === 'suspended' ? 'Deactivated' : 'Active' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(u)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <template v-if="u.id !== currentUserId">
                                <button v-if="u.status !== 'suspended'" @click="suspend(u.id)" class="font-semibold text-yellow-600 hover:underline">Deactivate</button>
                                <button v-else @click="activate(u.id)" class="font-semibold text-nyale-green-dark hover:underline">Reactivate</button>
                            </template>
                        </td>
                    </tr>
                    <tr v-if="!users.length"><td colspan="4" class="px-5 py-8 text-center text-nyale-navy/40">No users yet.</td></tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
