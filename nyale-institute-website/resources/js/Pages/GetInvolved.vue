<script setup>
import { useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

const options = [
    { key: 'partner', label: 'Partner With Us', icon: 'handshake', desc: 'Explore institutional partnerships and collaboration.' },
    { key: 'research_collaboration', label: 'Research Collaboration', icon: 'chart-bar', desc: 'Collaborate with us on SRHR research and evidence generation.' },
    { key: 'programme_collaboration', label: 'Programme Collaboration', icon: 'users', desc: 'Work with us on joint programmes and community initiatives.' },
    { key: 'volunteer', label: 'Volunteer / Internship', icon: 'academic-cap', desc: 'Join our team as a volunteer or intern.' },
    { key: 'support', label: 'Support Our Work', icon: 'heart', desc: 'Support Nyale Institute as a donor or funding partner.' },
    { key: 'general', label: 'General Enquiry', icon: 'document-text', desc: 'Any other question or request for information.' },
];

const form = useForm({
    name: '',
    email: '',
    organisation: '',
    phone: '',
    type: 'general',
    message: '',
});

const submit = () => {
    form.post('/get-involved', { preserveScroll: true, onSuccess: () => form.reset() });
};
</script>

<template>
    <PublicLayout title="Get Involved">
        <section class="bg-gradient-to-br from-nyale-green to-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Work With Us</p>
                <h1 class="mt-3 text-4xl font-extrabold text-white">Get Involved</h1>
                <p class="mt-5 max-w-2xl mx-auto text-white/90 leading-relaxed">
                    Government institutions, CSOs, donors, researchers, media, young people and other stakeholders —
                    there are many ways to engage with Nyale Institute.
                </p>
            </div>
        </section>

        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-6 mb-6">
                <div v-for="opt in options" :key="opt.key" class="rounded-3xl border border-nyale-blue/15 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-nyale-blue-light text-nyale-blue">
                        <Icon :name="opt.icon" size="h-5 w-5" />
                    </div>
                    <h3 class="mt-4 font-bold text-nyale-navy">{{ opt.label }}</h3>
                    <p class="mt-1 text-sm text-nyale-navy/60">{{ opt.desc }}</p>
                </div>
            </div>
        </section>

        <section class="pb-20">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-4xl bg-nyale-blue-light p-8 sm:p-12">
                    <h2 class="text-2xl font-extrabold text-nyale-navy">Send us a message</h2>
                    <form @submit.prevent="submit" class="mt-8 space-y-5">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-nyale-navy/80 mb-1">Name</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-nyale-navy/80 mb-1">Email</label>
                                <input v-model="form.email" type="email" required
                                    class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-nyale-navy/80 mb-1">Organisation</label>
                                <input v-model="form.organisation" type="text"
                                    class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-nyale-navy/80 mb-1">Phone</label>
                                <input v-model="form.phone" type="text"
                                    class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-nyale-navy/80 mb-1">I am interested in</label>
                            <select v-model="form.type" class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue">
                                <option v-for="opt in options" :key="opt.key" :value="opt.key">{{ opt.label }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-nyale-navy/80 mb-1">Message</label>
                            <textarea v-model="form.message" rows="5" required
                                class="w-full rounded-xl border-nyale-blue/20 focus:border-nyale-blue focus:ring-nyale-blue"></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-nyale-blue to-nyale-green px-8 py-3.5 text-sm font-bold text-white shadow-md transition hover:-translate-y-0.5 disabled:opacity-60">
                            {{ form.processing ? 'Sending…' : 'Send Message' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
