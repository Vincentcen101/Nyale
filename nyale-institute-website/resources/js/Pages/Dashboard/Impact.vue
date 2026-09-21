<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    stats: { type: Array, default: () => [] },
    stories: { type: Array, default: () => [] },
});

const tab = ref('stats');

// --- Stats ---
const statForm = useForm({ label: '', value: '', icon: '', order: 0 });
const editingStatId = ref(null);
const showStatForm = ref(false);

const openStatCreate = () => { editingStatId.value = null; statForm.reset(); showStatForm.value = true; };
const openStatEdit = (stat) => {
    editingStatId.value = stat.id;
    statForm.label = stat.label; statForm.value = stat.value; statForm.icon = stat.icon; statForm.order = stat.order;
    showStatForm.value = true;
};
const submitStat = () => {
    if (editingStatId.value) {
        statForm.put(`/dashboard/impact/stats/${editingStatId.value}`, { preserveScroll: true, onSuccess: () => { showStatForm.value = false; statForm.reset(); } });
    } else {
        statForm.post('/dashboard/impact/stats', { preserveScroll: true, onSuccess: () => { showStatForm.value = false; statForm.reset(); } });
    }
};
const destroyStat = async (id) => { if (await confirmDelete('Remove this stat?')) router.delete(`/dashboard/impact/stats/${id}`, { preserveScroll: true }); };

// --- Stories ---
const storyForm = useForm({ title: '', category: 'story_of_change', summary: '', body: '', image: null, published_at: '' });
const editingStoryId = ref(null);
const showStoryForm = ref(false);
const categories = ['litigation_outcome', 'policy_influence', 'community_impact', 'story_of_change'];

const openStoryCreate = () => { editingStoryId.value = null; storyForm.reset(); showStoryForm.value = true; };
const openStoryEdit = (story) => {
    editingStoryId.value = story.id;
    storyForm.title = story.title; storyForm.category = story.category; storyForm.summary = story.summary;
    storyForm.body = story.body; storyForm.image = null;
    storyForm.published_at = story.published_at;
    showStoryForm.value = true;
};
const submitStory = () => {
    if (editingStoryId.value) {
        storyForm.transform((data) => ({ ...data, _method: 'put' })).post(`/dashboard/impact/stories/${editingStoryId.value}`, {
            preserveScroll: true, onSuccess: () => { showStoryForm.value = false; storyForm.reset(); },
        });
    } else {
        storyForm.transform((data) => data).post('/dashboard/impact/stories', { preserveScroll: true, onSuccess: () => { showStoryForm.value = false; storyForm.reset(); } });
    }
};
const destroyStory = async (id) => { if (await confirmDelete('Remove this story?')) router.delete(`/dashboard/impact/stories/${id}`, { preserveScroll: true }); };
const toggleStoryActive = (id) => router.patch(`/dashboard/impact/stories/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppSidebarLayout title="Our Impact">
        <div class="flex gap-2 mb-6">
            <button @click="tab = 'stats'" class="rounded-full px-4 py-2 text-sm font-semibold" :class="tab === 'stats' ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70'">Impact Stats</button>
            <button @click="tab = 'stories'" class="rounded-full px-4 py-2 text-sm font-semibold" :class="tab === 'stories' ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70'">Impact Stories</button>
        </div>

        <div v-if="tab === 'stats'">
            <div class="flex justify-end mb-4">
                <button @click="showStatForm ? (showStatForm = false) : openStatCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                    {{ showStatForm ? 'Cancel' : '+ Add Stat' }}
                </button>
            </div>
            <form v-if="showStatForm" @submit.prevent="submitStat" class="mb-6 rounded-2xl border border-nyale-blue/15 p-6 grid sm:grid-cols-4 gap-4 items-end">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Label</label>
                    <input v-model="statForm.label" required class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Value</label>
                    <input v-model="statForm.value" required class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <button type="submit" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">{{ editingStatId ? 'Update' : 'Add' }}</button>
            </form>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="stat in stats" :key="stat.id" class="rounded-2xl border border-nyale-blue/15 p-5">
                    <p class="text-2xl font-extrabold text-nyale-blue">{{ stat.value }}</p>
                    <p class="text-sm text-nyale-navy/60">{{ stat.label }}</p>
                    <div class="mt-3 space-x-3 text-xs">
                        <button @click="openStatEdit(stat)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                        <button @click="destroyStat(stat.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="flex justify-end mb-4">
                <button @click="showStoryForm ? (showStoryForm = false) : openStoryCreate()" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                    {{ showStoryForm ? 'Cancel' : '+ Add Story' }}
                </button>
            </div>
            <form v-if="showStoryForm" @submit.prevent="submitStory" class="mb-6 rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Title</label>
                        <input v-model="storyForm.title" required class="w-full rounded-xl border-nyale-blue/20" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Category</label>
                        <select v-model="storyForm.category" class="w-full rounded-xl border-nyale-blue/20">
                            <option v-for="c in categories" :key="c" :value="c">{{ c.replace('_', ' ') }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Summary</label>
                    <input v-model="storyForm.summary" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Full story</label>
                    <textarea v-model="storyForm.body" rows="4" class="w-full rounded-xl border-nyale-blue/20"></textarea>
                </div>
                <div class="grid sm:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Image</label>
                        <input @input="storyForm.image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Published</label>
                        <input v-model="storyForm.published_at" type="date" class="w-full rounded-xl border-nyale-blue/20" />
                    </div>
                </div>
                <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">{{ editingStoryId ? 'Update' : 'Create' }}</button>
            </form>
            <div class="space-y-3">
                <div v-for="story in stories" :key="story.id" class="rounded-2xl border border-nyale-blue/15 p-5 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-nyale-green">{{ story.category.replace('_', ' ') }}</p>
                        <p class="font-semibold text-nyale-navy">{{ story.title }}</p>
                    </div>
                    <div class="flex-shrink-0 space-x-3 text-xs">
                        <button @click="toggleStoryActive(story.id)" class="font-semibold" :class="story.is_active ? 'text-nyale-green-dark' : 'text-nyale-navy/40'">
                            {{ story.is_active ? 'Active' : 'Hidden' }}
                        </button>
                        <button @click="openStoryEdit(story)" class="font-semibold text-nyale-blue hover:underline">Edit</button>
                        <button @click="destroyStory(story.id)" class="font-semibold text-red-600 hover:underline">Delete</button>
                    </div>
                </div>
                <p v-if="!stories.length" class="text-center text-nyale-navy/40 py-8">No stories yet.</p>
            </div>
        </div>
    </AppSidebarLayout>
</template>
