<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NyaleLogo from '@/Components/NyaleLogo.vue';

const page = usePage();
const currentUrl = computed(() => page.url || '/');

const navItems = computed(() => [
    { name: 'Home', href: '/' },
    { name: 'About Us', href: '/about' },
    { name: 'Programs', href: '/programs' },
    { name: 'Our Impact', href: '/our-impact' },
    { name: 'Knowledge Hub', href: '/knowledge-hub' },
    { name: 'Campaigns & Advocacy', href: '/campaigns' },
    { name: 'Case Tracker', href: '/case-tracker' },
    {
        name: 'News & Updates',
        href: '/news',
        children: [
            { name: 'News', href: '/news?category=news' },
            { name: 'Blog', href: '/news?category=blog' },
            { name: 'Events', href: '/events' },
        ],
    },
]);

const isActive = (href) => {
    if (href === '/') return currentUrl.value === '/';
    return currentUrl.value.startsWith(href);
};

// Query-string links (News, Blog) match exactly; plain links (Events) also match their sub-pages.
const isChildActive = (href) => (href.includes('?') ? currentUrl.value === href : currentUrl.value.startsWith(href));

const isItemActive = (item) =>
    isActive(item.href) || (item.children || []).some((child) => currentUrl.value.startsWith(child.href.split('?')[0]));

const mobileOpen = ref(false);
const mobileSubmenu = ref(false);
</script>

<template>
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-nyale-blue/15 shadow-sm">
        <div class="h-1 w-full bg-gradient-to-r from-nyale-blue via-nyale-green to-nyale-blue-dark"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between gap-4">
                <Link href="/" class="flex-shrink-0 transition-transform hover:scale-[1.02]">
                    <NyaleLogo size="nav" />
                </Link>

                <div class="hidden xl:flex items-center gap-0.5">
                    <template v-for="item in navItems" :key="item.name">
                        <div v-if="item.children" class="group relative">
                            <button type="button" aria-haspopup="true"
                                class="relative inline-flex items-center gap-1 px-2.5 py-2 text-[13px] font-semibold rounded-full transition-colors whitespace-nowrap"
                                :class="isItemActive(item) ? 'text-nyale-blue bg-nyale-blue-light' : 'text-nyale-navy/70 group-hover:text-nyale-blue group-hover:bg-nyale-blue-light/60'">
                                {{ item.name }}
                                <svg class="h-3.5 w-3.5 transition-transform group-hover:rotate-180 group-focus-within:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div class="invisible absolute left-0 top-full z-50 pt-2 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                                <div class="w-44 rounded-2xl border border-nyale-blue/10 bg-white p-2 shadow-xl">
                                    <Link v-for="child in item.children" :key="child.name" :href="child.href"
                                        class="block rounded-xl px-3 py-2 text-sm font-semibold transition-colors"
                                        :class="isChildActive(child.href) ? 'bg-nyale-blue-light text-nyale-blue' : 'text-nyale-navy/70 hover:bg-nyale-blue-light/60 hover:text-nyale-blue'">
                                        {{ child.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <Link v-else :href="item.href"
                            class="relative px-2.5 py-2 text-[13px] font-semibold rounded-full transition-colors whitespace-nowrap"
                            :class="isActive(item.href) ? 'text-nyale-blue bg-nyale-blue-light' : 'text-nyale-navy/70 hover:text-nyale-blue hover:bg-nyale-blue-light/60'">
                            {{ item.name }}
                        </Link>
                    </template>
                </div>

                <div class="hidden xl:flex items-center gap-3">
                    <Link href="/get-involved"
                        class="inline-flex items-center gap-2 rounded-full bg-nyale-blue px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-nyale-blue/25 transition-all hover:bg-nyale-blue-dark hover:shadow-lg hover:-translate-y-0.5">
                        Get Involved
                    </Link>
                </div>

                <button @click="mobileOpen = !mobileOpen"
                    class="xl:hidden inline-flex items-center justify-center rounded-xl p-2.5 text-nyale-navy/70 hover:bg-nyale-blue-light"
                    aria-label="Toggle navigation">
                    <svg v-if="!mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
            <div v-show="mobileOpen" class="xl:hidden border-t border-nyale-blue/10 bg-white shadow-xl px-4 py-4 space-y-1 max-h-[80vh] overflow-y-auto">
                <template v-for="item in navItems" :key="item.name">
                    <template v-if="item.children">
                        <button type="button" @click="mobileSubmenu = !mobileSubmenu" :aria-expanded="mobileSubmenu"
                            class="flex w-full items-center justify-between px-4 py-3 text-base font-semibold rounded-xl transition-colors"
                            :class="isItemActive(item) ? 'bg-nyale-blue-light text-nyale-blue' : 'text-nyale-navy/70 hover:bg-nyale-blue-light/60'">
                            {{ item.name }}
                            <svg class="h-4 w-4 transition-transform" :class="mobileSubmenu ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <template v-if="mobileSubmenu">
                            <Link v-for="child in item.children" :key="child.name" :href="child.href" @click="mobileOpen = false"
                                class="block ml-6 border-l-2 border-nyale-blue/20 px-4 py-2 text-sm font-semibold rounded-r-xl transition-colors"
                                :class="isChildActive(child.href) ? 'bg-nyale-blue-light text-nyale-blue' : 'text-nyale-navy/60 hover:bg-nyale-blue-light/60'">
                                {{ child.name }}
                            </Link>
                        </template>
                    </template>
                    <Link v-else :href="item.href" @click="mobileOpen = false"
                        class="block px-4 py-3 text-base font-semibold rounded-xl transition-colors"
                        :class="isActive(item.href) ? 'bg-nyale-blue-light text-nyale-blue' : 'text-nyale-navy/70 hover:bg-nyale-blue-light/60'">
                        {{ item.name }}
                    </Link>
                </template>
                <Link href="/get-involved" @click="mobileOpen = false"
                    class="mt-2 flex w-full items-center justify-center rounded-full bg-nyale-blue px-4 py-3 text-base font-bold text-white shadow-md">
                    Get Involved
                </Link>
            </div>
        </Transition>
    </nav>
</template>
