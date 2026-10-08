<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';

defineProps({
    events: { type: Array, default: () => [] },
});

const showForm = ref(false);
const editingId = ref(null);
const form = useForm({
    title: '', summary: '', body: '', image: null, location: '',
    starts_at: '', ends_at: '', registration_url: '',
});

const close = () => { showForm.value = false; form.reset(); };
const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (e) => {
    editingId.value = e.id;
    form.title = e.title; form.summary = e.summary ?? ''; form.body = e.body ?? ''; form.image = null;
    form.location = e.location ?? ''; form.starts_at = e.starts_at ?? ''; form.ends_at = e.ends_at ?? '';
    form.registration_url = e.registration_url ?? '';
    showForm.value = true;
};
const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/events/${editingId.value}`, { preserveScroll: true, onSuccess: close });
    } else {
        form.transform((data) => data).post('/dashboard/events', { preserveScroll: true, onSuccess: close });
    }
};
const destroy = async (id) => { if (await confirmDelete('Delete this event?')) router.delete(`/dashboard/events/${id}`, { preserveScroll: true }); };
const toggleActive = (id) => router.patch(`/dashboard/events/${id}/toggle-active`, {}, { preserveScroll: true });
const isPast = (e) => new Date(e.starts_at) < new Date();
</script>

<template>
    <AppSidebarLayout title="Events">
        <div class="flex justify-end mb-6">
            <button @click="showForm ? close() : openCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                {{ showForm ? 'Cancel' : '+ Add Event' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <h3 class="font-bold text-nyale-navy">{{ editingId ? 'Edit Event' : 'New Event' }}</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                    <input v-model="form.title" required class="w-full rounded-xl border-nyale-blue/20" />
                    <p v-if="form.errors.title" class="text-xs text-red-600 mt-1">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Location</label>
                    <input v-model="form.location" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Starts</label>
                    <input v-model="form.starts_at" type="datetime-local" required class="w-full rounded-xl border-nyale-blue/20" />
                    <p v-if="form.errors.starts_at" class="text-xs text-red-600 mt-1">{{ form.errors.starts_at }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Ends (optional)</label>
                    <input v-model="form.ends_at" type="datetime-local" class="w-full rounded-xl border-nyale-blue/20" />
                    <p v-if="form.errors.ends_at" class="text-xs text-red-600 mt-1">{{ form.errors.ends_at }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Registration link (optional)</label>
                    <input v-model="form.registration_url" placeholder="https://…" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Summary</label>
                <textarea v-model="form.summary" rows="2" maxlength="1000" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Full details</label>
                <textarea v-model="form.body" rows="5" class="w-full rounded-xl border-nyale-blue/20"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Image</label>
                <input @input="form.image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                <p v-if="form.errors.image" class="text-xs text-red-600 mt-1">{{ form.errors.image }}</p>
            </div>
            <button type="submit" :disabled="form.processing" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white disabled:opacity-60">
                {{ editingId ? 'Update' : 'Create' }}
            </button>
        </form>

        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">Event</th>
                        <th class="text-left px-5 py-3 font-semibold">Date</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-nyale-blue/10">
                    <tr v-for="e in events" :key="e.id">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img v-if="e.image" :src="`/storage/${e.image}`" :alt="e.title"
                                    class="h-12 w-20 rounded-lg object-cover flex-shrink-0" />
                                <div v-else class="h-12 w-20 rounded-lg bg-nyale-blue-light flex-shrink-0"></div>
                                <div>
                                    <p class="font-semibold text-nyale-navy">{{ e.title }}</p>
                                    <p v-if="e.location" class="text-xs text-nyale-navy/50">{{ e.location }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-xs font-bold uppercase" :class="isPast(e) ? 'text-nyale-navy/40' : 'text-nyale-green'">
                                {{ isPast(e) ? 'Past' : 'Upcoming' }}
                            </p>
                            <p class="text-nyale-navy/60">{{ e.starts_at?.replace('T', ' ') }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(e.id)" class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="e.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ e.is_active ? 'Live' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(e)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(e.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!events.length">
                        <td colspan="4" class="px-5 py-8 text-center text-nyale-navy/40">
                            No events yet — the public Events page stays empty until you add one.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
