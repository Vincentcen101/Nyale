<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Sortable from 'sortablejs';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    sliders: { type: Array, default: () => [] },
    buttonOptions: { type: Array, default: () => [] },
});

const showForm = ref(false);
const editingId = ref(null);
const currentImage = ref(null);

const form = useForm({
    title: '',
    eyebrow: '',
    description: '',
    image: null,
    button_url: '',
});

// Local copy of the list so drag-and-drop can reorder it instantly.
const rows = ref([...props.sliders]);
watch(() => props.sliders, (value) => { rows.value = [...value]; });

const tbody = ref(null);
let sortable = null;

onMounted(() => {
    sortable = Sortable.create(tbody.value, {
        handle: '.drag-handle',
        draggable: 'tr[data-id]',
        animation: 150,
        ghostClass: 'opacity-40',
        onEnd: ({ item, from, oldIndex, newIndex, oldDraggableIndex, newDraggableIndex }) => {
            if (oldIndex === newIndex) return;
            // Put the row back where it was and let Vue re-render from the reordered array.
            from.removeChild(item);
            from.insertBefore(item, from.children[oldIndex] ?? null);

            const [moved] = rows.value.splice(oldDraggableIndex, 1);
            rows.value.splice(newDraggableIndex, 0, moved);

            router.patch('/dashboard/sliders/reorder', { ids: rows.value.map((s) => s.id) }, {
                preserveScroll: true,
                preserveState: true,
            });
        },
    });
});

onBeforeUnmount(() => sortable?.destroy());

const openCreate = () => {
    editingId.value = null;
    currentImage.value = null;
    form.reset();
    showForm.value = true;
};

const openEdit = (slide) => {
    editingId.value = slide.id;
    currentImage.value = slide.image;
    form.title = slide.title;
    form.eyebrow = slide.eyebrow ?? '';
    form.description = slide.description ?? '';
    form.image = null;
    // Older slides may hold a custom link that is no longer offered; they fall back to the default.
    form.button_url = props.buttonOptions.some((o) => o.url === slide.button_url) ? slide.button_url : '';
    showForm.value = true;
};

const close = () => {
    showForm.value = false;
    form.reset();
};

const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/sliders/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: close,
        });
    } else {
        form.transform((data) => data).post('/dashboard/sliders', { preserveScroll: true, onSuccess: close });
    }
};

const destroy = async (id) => {
    if (await confirmDelete('Delete this slide?')) {
        router.delete(`/dashboard/sliders/${id}`, { preserveScroll: true });
    }
};

const toggleActive = (id) => {
    router.patch(`/dashboard/sliders/${id}/toggle-active`, {}, { preserveScroll: true });
};
</script>

<template>
    <AppSidebarLayout title="Home Page Sliders">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <p class="text-sm text-nyale-navy/60 max-w-xl">
                Only slides created here appear in the home page slider. News, blog and impact stories are never added automatically. Every slide has two buttons: the first goes to About Us ("Learn More") unless you pick another page, and the second is always "Get Involved".
            </p>
            <button @click="showForm ? close() : openCreate()"
                class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white hover:bg-nyale-blue-dark">
                {{ showForm ? 'Cancel' : '+ Add Slide' }}
            </button>
        </div>

        <form v-if="showForm" @submit.prevent="submit" class="mb-8 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <h3 class="font-bold text-nyale-navy">{{ editingId ? 'Edit Slide' : 'New Slide' }}</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                    <input v-model="form.title" type="text" required class="w-full rounded-xl border-nyale-blue/20" />
                    <p v-if="form.errors.title" class="text-xs text-red-600 mt-1">{{ form.errors.title }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Small label above title (optional)</label>
                    <input v-model="form.eyebrow" type="text" maxlength="100" placeholder="e.g. Litigation Outcome" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Description</label>
                <RichTextEditor v-model="form.description" min-height="6rem" placeholder="Short text shown on the slide…" />
                <p class="text-xs text-nyale-navy/40 mt-1">Use B and I to make text bold or italic. Keep it under 600 characters.</p>
                <p v-if="form.errors.description" class="text-xs text-red-600 mt-1">{{ form.errors.description }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Button takes visitors to</label>
                <select v-model="form.button_url" class="w-full sm:w-1/2 rounded-xl border-nyale-blue/20">
                    <option value="">About Us — "Learn More" (default)</option>
                    <option v-for="opt in buttonOptions" :key="opt.url" :value="opt.url">
                        {{ opt.label }}
                    </option>
                </select>
                <p class="text-xs text-nyale-navy/40 mt-1">The button text is set automatically to match the page you pick.</p>
                <p v-if="form.errors.button_url" class="text-xs text-red-600 mt-1">{{ form.errors.button_url }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Image</label>
                <input @input="form.image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                <p v-if="editingId && currentImage" class="text-xs text-nyale-navy/50 mt-1">Leave empty to keep the current image.</p>
                <p v-if="form.errors.image" class="text-xs text-red-600 mt-1">{{ form.errors.image }}</p>
            </div>
            <button type="submit" :disabled="form.processing" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white disabled:opacity-60">
                {{ editingId ? 'Update' : 'Create' }}
            </button>
        </form>

        <p v-if="rows.length > 1" class="mb-3 text-xs text-nyale-navy/50">
            Tip: drag a slide by its <span class="font-bold">⠿</span> handle to change the order. The slide at the top shows first on the home page. Changes save automatically.
        </p>
        <div class="rounded-2xl border border-nyale-blue/15 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-nyale-blue-light text-nyale-navy/70">
                    <tr>
                        <th class="w-10 px-2 py-3"><span class="sr-only">Reorder</span></th>
                        <th class="text-left px-5 py-3 font-semibold">Slide</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="text-right px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody ref="tbody" class="divide-y divide-nyale-blue/10">
                    <tr v-for="slide in rows" :key="slide.id" :data-id="slide.id" class="bg-white">
                        <td class="px-2 py-3 text-center">
                            <span class="drag-handle cursor-grab active:cursor-grabbing select-none touch-none px-2 text-lg leading-none text-nyale-navy/40 hover:text-nyale-blue"
                                title="Drag to reorder" aria-label="Drag to reorder">⠿</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <img v-if="slide.image" :src="`/storage/${slide.image}`" :alt="slide.title"
                                    class="h-12 w-20 rounded-lg object-cover flex-shrink-0" />
                                <div v-else class="h-12 w-20 rounded-lg bg-nyale-blue-light flex-shrink-0"></div>
                                <div>
                                    <p class="font-semibold text-nyale-navy">{{ slide.title }}</p>
                                    <p v-if="slide.eyebrow" class="text-xs text-nyale-green font-semibold">{{ slide.eyebrow }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <button @click="toggleActive(slide.id)" class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="slide.is_active ? 'bg-nyale-green/15 text-nyale-green-dark' : 'bg-nyale-navy/10 text-nyale-navy/50'">
                                {{ slide.is_active ? 'Live' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <button @click="openEdit(slide)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                            <button @click="destroy(slide.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!rows.length">
                        <td colspan="4" class="px-5 py-8 text-center text-nyale-navy/40">
                            No slides yet — the home page slider stays hidden until you add one.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppSidebarLayout>
</template>
