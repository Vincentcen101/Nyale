<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import Icon from '@/Components/Icon.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    workAreas: { type: Array, default: () => [] },
});

const iconOptions = ['scale', 'document-text', 'chart-bar', 'users', 'academic-cap', 'megaphone', 'calendar', 'heart', 'globe', 'book', 'handshake', 'briefcase'];

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    title: '',
    icon: 'scale',
    summary: '',
    body: '',
    image: null,
    order: 0,
});

const openCreate = () => {
    editingId.value = null;
    form.reset();
    showForm.value = true;
};

const openEdit = (area) => {
    editingId.value = area.id;
    form.title = area.title;
    form.icon = area.icon;
    form.summary = area.summary;
    form.body = area.body;
    form.image = null;
    form.order = area.order;
    showForm.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/work-areas/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => { showForm.value = false; form.reset(); },
        });
    } else {
        form.transform((data) => data).post('/dashboard/work-areas', {
            preserveScroll: true,
            onSuccess: () => { showForm.value = false; form.reset(); },
        });
    }
};

const destroy = async (id) => {
    if (await confirmDelete('Delete this program?')) {
        router.delete(`/dashboard/work-areas/${id}`, { preserveScroll: true });
    }
};

const toggleActive = (id) => {
    router.patch(`/dashboard/work-areas/${id}/toggle-active`, {}, { preserveScroll: true });
};
</script>

<template>
    <AppSidebarLayout title="Programs">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? (showForm = false) : openCreate()"
                class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white hover:bg-nyale-blue-dark">
                {{ showForm ? 'Cancel' : '+ Add Program' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <h3 class="font-bold text-nyale-navy">{{ editingId ? 'Edit Program' : 'New Program' }}</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                    <input v-model="form.title" type="text" required class="w-full rounded-xl border-nyale-blue/20" />
                    <p v-if="form.errors.title" class="text-xs text-red-600 mt-1">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Icon</label>
                    <select v-model="form.icon" class="w-full rounded-xl border-nyale-blue/20">
                        <option v-for="icon in iconOptions" :key="icon" :value="icon">{{ icon }}</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Summary</label>
                <input v-model="form.summary" type="text" maxlength="500" class="w-full rounded-xl border-nyale-blue/20" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Full description</label>
                <textarea v-model="form.body" rows="4" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Image</label>
                    <input @input="form.image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Order</label>
                    <input v-model.number="form.order" type="number" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <button type="submit" :disabled="form.processing" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white disabled:opacity-60">
                {{ editingId ? 'Update' : 'Create' }}
            </button>
        </form>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">Program</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="area in workAreas" :key="area.id">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <Icon :name="area.icon" size="h-5 w-5 text-nyale-blue" />
                                <div>
                                    <p class="font-semibold text-nyale-navy">{{ area.title }}</p>
                                    <p class="text-xs text-nyale-navy/50 line-clamp-1">{{ area.summary }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(area.id)"
                                class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="area.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ area.is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(area)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(area.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!workAreas.length">
                        <td colspan="3" class="px-5 py-8 text-center text-nyale-navy/40">No programs yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
