<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage, Head } from '@inertiajs/vue3';
import NyaleLogo from '@/Components/NyaleLogo.vue';
import FlashToasts from '@/Components/FlashToasts.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { useTextSize, TEXT_STEPS } from '@/Composables/useTextSize';

defineProps({
    title: { type: String, default: 'Dashboard' },
});

const page = usePage();
const currentPath = computed(() => page.url);
const isCollapsed = ref(false);
const isMobileOpen = ref(false);

const { step: textStep, setStep: setTextStep, activate: activateTextSize, release: releaseTextSize } = useTextSize();
const letterSizes = ['text-xs', 'text-base', 'text-xl'];
onMounted(activateTextSize);
onBeforeUnmount(releaseTextSize);

const navigationItems = [
    { name: 'Overview', href: route('dashboard.index'), icon: 'overview' },
    { name: 'Sliders', href: route('dashboard.sliders'), icon: 'sliders' },
    { name: 'Programs', href: route('dashboard.work-areas'), icon: 'work' },
    { name: 'Our Impact', href: route('dashboard.impact'), icon: 'impact' },
    { name: 'Knowledge Hub', href: route('dashboard.knowledge-hub'), icon: 'knowledge' },
    { name: 'Campaigns', href: route('dashboard.campaigns'), icon: 'campaigns' },
    { name: 'Case Tracker', href: route('dashboard.cases'), icon: 'cases' },
    { name: 'Events', href: route('dashboard.events'), icon: 'events' },
    { name: 'News & Updates', href: route('dashboard.news'), icon: 'news' },
    { name: 'Comments', href: route('dashboard.comments'), icon: 'inbox' },
    { name: 'Team', href: route('dashboard.team'), icon: 'team', adminOnly: true },
    { name: 'Get Involved', href: route('dashboard.get-involved'), icon: 'inbox' },
    { name: 'Users', href: route('dashboard.users'), icon: 'users', adminOnly: true },
    { name: 'Activity Log', href: route('dashboard.activity-logs'), icon: 'activity', adminOnly: true },
    { name: 'Settings', href: route('dashboard.settings'), icon: 'settings' },
];

const toPathname = (url) => {
    try {
        return new URL(url, window.location.origin).pathname.replace(/\/+$/, '') || '/';
    } catch (e) {
        return url;
    }
};

const navItems = computed(() => {
    const current = toPathname(currentPath.value.split('?')[0].split('#')[0]);
    const dashboardPath = toPathname(route('dashboard.index'));

    const isAdmin = page.props.auth?.user?.role === 'admin';

    return navigationItems.filter((item) => isAdmin || !item.adminOnly).map((item) => {
        const itemPath = toPathname(item.href);
        const isActive = current === itemPath || (itemPath !== dashboardPath && current.startsWith(itemPath + '/'));
        return { ...item, current: isActive };
    });
});

const icons = {
    overview: 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
    sliders: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
    cases: 'M12 3v18m0-18L6 7.5M12 3l6 4.5M6 7.5L3 15a3 3 0 006 0L6 7.5zm12 0L15 15a3 3 0 006 0l-3-7.5z',
    events: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
    work: 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18a48.204 48.204 0 01-12.756 0c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.35 48.35 0 00-1.913-.247m-.5 8.116l-3.68 2.208a3 3 0 01-3.093 0L6.75 14.4m13.5 0l-3.68 2.208',
    impact: 'M2.25 18L9 11.25l4.306 4.306a11.95 11.95 0 015.814-5.518l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941',
    knowledge: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
    campaigns: 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535',
    news: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12h-9a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5h5.379a1.5 1.5 0 011.06.44l3.622 3.621a1.5 1.5 0 01.44 1.061V18a1.5 1.5 0 01-1.5 1.5z',
    team: 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z',
    inbox: 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
    users: 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-4.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106c0 1.113-.285 2.16-.786 3.07M6.625 12.5a4.125 4.125 0 117.25 0 4.125 4.125 0 01-7.25 0z',
    activity: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
    settings: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z',
};
</script>

<template>
    <Head :title="title" />

    <div v-if="isMobileOpen" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden" @click="isMobileOpen = false"></div>

    <aside :class="[
        'fixed left-0 top-0 z-50 flex h-full flex-col bg-nyale-blue-dark transition-all duration-300',
        isCollapsed ? 'w-20' : 'w-64',
        isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
    ]">
        <div class="relative z-10 flex h-16 items-center justify-between bg-white px-4 shadow-md">
            <Link v-if="!isCollapsed" :href="route('dashboard.index')" class="flex items-center gap-2 min-w-0">
                <NyaleLogo />
            </Link>
            <Link v-else :href="route('dashboard.index')" class="mx-auto">
                <NyaleLogo icon-only />
            </Link>
            <button @click="isCollapsed = !isCollapsed"
                class="rounded-lg p-1.5"
                :class="isCollapsed ? 'absolute -right-3 top-5 bg-nyale-blue-dark text-white/80 hover:text-white border border-white/10 shadow' : 'text-nyale-navy/50 hover:bg-nyale-blue-light hover:text-nyale-blue'">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path v-if="!isCollapsed" stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" d="M5.25 4.5l7.5 7.5-7.5 7.5m6-15l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <Link v-for="item in navItems" :key="item.name" :href="item.href" @click="isMobileOpen = false"
                class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all"
                :class="item.current ? 'bg-white/15 text-nyale-green' : 'text-white/70 hover:bg-white/10 hover:text-nyale-green'"
                :title="isCollapsed ? item.name : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="icons[item.icon]" />
                </svg>
                <span v-if="!isCollapsed" class="truncate">{{ item.name }}</span>
                <span v-if="item.name === 'Comments' && $page.props.pendingComments > 0"
                    class="rounded-full bg-yellow-400 px-2 py-0.5 text-xs font-bold text-yellow-900"
                    :class="isCollapsed ? 'absolute right-1 top-1 px-1.5 text-[10px]' : 'ml-auto'">
                    {{ $page.props.pendingComments }}
                </span>
                <span v-if="item.current" class="absolute left-0 top-1/2 h-8 w-1 -translate-y-1/2 rounded-r-full bg-nyale-green"></span>
            </Link>
        </nav>

        <div class="border-t border-white/10 p-4">
            <div v-if="!isCollapsed" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-nyale-green font-bold">
                    {{ ($page.props.auth.user?.name || 'A')[0] }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $page.props.auth.user?.name }}</p>
                    <p class="text-xs text-white/50 truncate">{{ $page.props.auth.user?.email }}</p>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="rounded-lg p-1.5 text-white/60 hover:bg-white/10 hover:text-nyale-green">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </Link>
            </div>
        </div>
    </aside>

    <button @click="isMobileOpen = !isMobileOpen"
        class="fixed bottom-4 right-4 z-50 rounded-full bg-nyale-blue-dark p-3 text-white shadow-lg lg:hidden">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div :class="['transition-all duration-300', isCollapsed ? 'lg:pl-20' : 'lg:pl-64']">
        <header class="sticky top-0 z-30 flex items-center justify-between gap-4 bg-white px-6 py-4 shadow-md">
            <h1 class="text-xl font-extrabold text-nyale-navy">{{ title }}</h1>
            <div role="group" aria-label="Text size" class="inline-flex flex-shrink-0 items-center rounded-full border border-nyale-blue/30 p-0.5">
                <button v-for="option in TEXT_STEPS" :key="option.step" type="button" @click="setTextStep(option.step)"
                    :aria-pressed="textStep === option.step" :title="option.label" :aria-label="option.label"
                    class="flex items-center gap-1 rounded-full px-3 py-1 font-bold leading-none transition"
                    :class="textStep === option.step ? 'bg-nyale-blue text-white' : 'text-nyale-blue hover:bg-nyale-blue-light'">
                    <span aria-hidden="true" :class="letterSizes[option.step]">A</span>
                    <span v-if="option.step" class="hidden text-[11px] sm:inline">{{ option.short }}</span>
                </button>
            </div>
        </header>

        <main class="p-6">
            <slot />
        </main>
    </div>

    <FlashToasts />
    <ConfirmDialog />
</template>

<style scoped>
.sidebar-nav {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
}

.sidebar-nav::-webkit-scrollbar {
    width: 6px;
}

.sidebar-nav::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.25);
    border-radius: 9999px;
}

.sidebar-nav:hover::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.4);
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
    background-color: #6dbe45;
}
</style>
