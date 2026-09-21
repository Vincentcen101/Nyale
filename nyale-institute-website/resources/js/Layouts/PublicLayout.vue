<script setup>
import { Head } from '@inertiajs/vue3';
import NavigationBar from '@/Partials/NavigationBar.vue';
import Footer from '@/Partials/Footer.vue';
import SocialRail from '@/Components/SocialRail.vue';
import FlashToasts from '@/Components/FlashToasts.vue';

defineProps({
    title: { type: String, default: 'Nyale Institute' },
});
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-white font-sans antialiased">
        <NavigationBar />
        <SocialRail />

        <main>
            <slot />
        </main>

        <Footer />
    </div>

    <FlashToasts />
</template>
