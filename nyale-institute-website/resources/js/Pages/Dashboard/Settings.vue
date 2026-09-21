<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/Layouts/AppSidebarLayout.vue';
import { confirmDelete } from '@/Composables/useConfirm';

const props = defineProps({
    aboutSettings: { type: Array, default: () => [] },
    contactSettings: { type: Array, default: () => [] },
    socialSettings: { type: Array, default: () => [] },
    seoSettings: { type: Array, default: () => [] },
    partners: { type: Array, default: () => [] },
});

const tab = ref('about');

const aboutFields = [
    { key: 'who_we_are', label: 'Who We Are' },
    { key: 'our_story', label: 'Our Story' },
    { key: 'vision', label: 'Our Vision' },
    { key: 'mission', label: 'Our Mission' },
    { key: 'core_values', label: 'Core Values' },
    { key: 'leadership', label: 'Leadership' },
];
const findAbout = (section) => props.aboutSettings.find((a) => a.section === section) || {};

const aboutForm = useForm({ section: '', title: '', content: '', image: null });
const editingAbout = ref(null);
const openAbout = (field) => {
    editingAbout.value = field.key;
    const existing = findAbout(field.key);
    aboutForm.section = field.key;
    aboutForm.title = existing.title || field.label;
    aboutForm.content = existing.content || '';
    aboutForm.image = null;
};
const submitAbout = () => {
    aboutForm.post('/dashboard/settings/about', { preserveScroll: true, onSuccess: () => { editingAbout.value = null; } });
};

const contactTypes = [
    { key: 'address', label: 'Address' },
    { key: 'phone', label: 'Phone' },
    { key: 'email', label: 'Email' },
];
const findContact = (type) => props.contactSettings.find((c) => c.type === type) || {};
const contactForm = useForm({ type: '', label: '', value: '' });
const editingContact = ref(null);
const openContact = (field) => {
    editingContact.value = field.key;
    const existing = findContact(field.key);
    contactForm.type = field.key;
    contactForm.label = existing.label || field.label;
    contactForm.value = existing.value || '';
};
const submitContact = () => {
    contactForm.post('/dashboard/settings/contact', { preserveScroll: true, onSuccess: () => { editingContact.value = null; } });
};

const socialPlatforms = ['facebook', 'instagram', 'linkedin', 'twitter', 'youtube'];
const findSocial = (platform) => props.socialSettings.find((s) => s.platform === platform) || {};
const socialForms = computed(() =>
    Object.fromEntries(socialPlatforms.map((p) => [p, findSocial(p).url || '']))
);
const socialValues = ref({ ...socialForms.value });
const saveSocial = (platform) => {
    router.post('/dashboard/settings/social', { platform, url: socialValues.value[platform] }, { preserveScroll: true });
};

const globalSeo = computed(() => props.seoSettings.find((s) => s.page === 'global') || {});
const seoForm = useForm({
    page: 'global',
    meta_title: globalSeo.value.meta_title || '',
    meta_description: globalSeo.value.meta_description || '',
});
const submitSeo = () => seoForm.post('/dashboard/settings/seo', { preserveScroll: true });

const partnerForm = useForm({ name: '', logo: null, url: '', order: 0 });
const showPartnerForm = ref(false);
const submitPartner = () => {
    partnerForm.post('/dashboard/settings/partners', { preserveScroll: true, onSuccess: () => { showPartnerForm.value = false; partnerForm.reset(); } });
};
const destroyPartner = async (id) => { if (await confirmDelete('Remove this partner?')) router.delete(`/dashboard/settings/partners/${id}`, { preserveScroll: true }); };
</script>

<template>
    <AppSidebarLayout title="Site Settings">
        <div class="flex flex-wrap gap-2 mb-6">
            <button v-for="t in ['about', 'contact', 'social', 'seo', 'partners']" :key="t" @click="tab = t"
                class="rounded-full px-4 py-2 text-sm font-semibold capitalize"
                :class="tab === t ? 'bg-nyale-blue text-white' : 'bg-nyale-blue-light text-nyale-navy/70'">
                {{ t }}
            </button>
        </div>

        <!-- About -->
        <div v-if="tab === 'about'" class="space-y-4">
            <div v-for="field in aboutFields" :key="field.key" class="rounded-2xl border border-nyale-blue/15 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-nyale-navy">{{ field.label }}</h3>
                    <button v-if="editingAbout !== field.key" @click="openAbout(field)" class="text-sm font-semibold text-nyale-blue hover:underline">Edit</button>
                </div>
                <p v-if="editingAbout !== field.key" class="mt-2 text-sm text-nyale-navy/60 whitespace-pre-line">{{ findAbout(field.key).content || 'Not set yet.' }}</p>
                <form v-else @submit.prevent="submitAbout" class="mt-4 space-y-3">
                    <input v-model="aboutForm.title" placeholder="Title" class="w-full rounded-xl border-nyale-blue/20" />
                    <textarea v-model="aboutForm.content" rows="5" class="w-full rounded-xl border-nyale-blue/20"></textarea>
                    <input @input="aboutForm.image = $event.target.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                    <div class="flex gap-3">
                        <button type="submit" class="rounded-full bg-nyale-blue px-5 py-2 text-sm font-bold text-white">Save</button>
                        <button type="button" @click="editingAbout = null" class="text-sm font-semibold text-nyale-navy/50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact -->
        <div v-else-if="tab === 'contact'" class="space-y-4">
            <div v-for="field in contactTypes" :key="field.key" class="rounded-2xl border border-nyale-blue/15 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-nyale-navy">{{ field.label }}</h3>
                    <button v-if="editingContact !== field.key" @click="openContact(field)" class="text-sm font-semibold text-nyale-blue hover:underline">Edit</button>
                </div>
                <p v-if="editingContact !== field.key" class="mt-2 text-sm text-nyale-navy/60 whitespace-pre-line">{{ findContact(field.key).value || 'Not set yet.' }}</p>
                <form v-else @submit.prevent="submitContact" class="mt-4 space-y-3">
                    <textarea v-model="contactForm.value" rows="2" class="w-full rounded-xl border-nyale-blue/20"></textarea>
                    <div class="flex gap-3">
                        <button type="submit" class="rounded-full bg-nyale-blue px-5 py-2 text-sm font-bold text-white">Save</button>
                        <button type="button" @click="editingContact = null" class="text-sm font-semibold text-nyale-navy/50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Social -->
        <div v-else-if="tab === 'social'" class="rounded-2xl border border-nyale-blue/15 p-6 space-y-4">
            <div v-for="platform in socialPlatforms" :key="platform" class="flex items-center gap-4">
                <label class="w-28 text-sm font-semibold capitalize text-nyale-navy/70">{{ platform }}</label>
                <input v-model="socialValues[platform]" type="text" class="flex-1 rounded-xl border-nyale-blue/20" />
                <button @click="saveSocial(platform)" class="rounded-full bg-nyale-blue px-4 py-2 text-xs font-bold text-white">Save</button>
            </div>
        </div>

        <!-- SEO -->
        <div v-else-if="tab === 'seo'" class="rounded-2xl border border-nyale-blue/15 p-6">
            <form @submit.prevent="submitSeo" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Meta Title</label>
                    <input v-model="seoForm.meta_title" class="w-full rounded-xl border-nyale-blue/20" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-nyale-navy/70 mb-1">Meta Description</label>
                    <textarea v-model="seoForm.meta_description" rows="3" class="w-full rounded-xl border-nyale-blue/20"></textarea>
                </div>
                <button type="submit" class="rounded-full bg-nyale-blue px-6 py-2.5 text-sm font-bold text-white">Save</button>
            </form>
        </div>

        <!-- Partners -->
        <div v-else-if="tab === 'partners'">
            <div class="flex justify-end mb-4">
                <button @click="showPartnerForm = !showPartnerForm" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">
                    {{ showPartnerForm ? 'Cancel' : '+ Add Partner' }}
                </button>
            </div>
            <form v-if="showPartnerForm" @submit.prevent="submitPartner" class="mb-6 rounded-2xl border border-nyale-blue/15 p-6 grid sm:grid-cols-4 gap-4 items-end">
                <input v-model="partnerForm.name" placeholder="Partner name" required class="rounded-xl border-nyale-blue/20 sm:col-span-2" />
                <input @input="partnerForm.logo = $event.target.files[0]" type="file" accept="image/*" class="text-sm" />
                <button type="submit" class="rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white">Add</button>
            </form>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="p in partners" :key="p.id" class="rounded-2xl border border-nyale-blue/15 p-5 flex items-center justify-between">
                    <span class="font-semibold text-nyale-navy text-sm">{{ p.name }}</span>
                    <button @click="destroyPartner(p.id)" class="text-xs font-semibold text-red-600 hover:underline">Remove</button>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
