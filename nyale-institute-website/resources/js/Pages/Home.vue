<script setup>
import { Link } from '@inertiajs/vue3';
import { useSlider } from '@/Composables/useSlider';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';
import CountUp from '@/Components/CountUp.vue';
import RichContent from '@/Components/RichContent.vue';

const props = defineProps({
    stats: { type: Array, default: () => [] },
    workAreas: { type: Array, default: () => [] },
    campaigns: { type: Array, default: () => [] },
    latestPosts: { type: Array, default: () => [] },
    featuredStory: { type: Object, default: null },
    sliders: { type: Array, default: () => [] },
});

const categoryLabels = {
    litigation_outcome: 'Litigation Outcome',
    policy_influence: 'Policy Influence',
    community_impact: 'Community Impact',
    story_of_change: 'Story of Change',
};

// Hero slider — shows only the slides created under Sliders in the admin dashboard
const {
    current: currentSlide, active: activeSlide, next: nextSlide, prev: prevSlide,
    go: goToSlide, start: startAutoplay, stop: stopAutoplay,
} = useSlider(() => props.sliders, 6000);

// Campaigns slider — the active campaigns, shown one at a time
const {
    current: currentCampaign, active: activeCampaign, next: nextCampaign, prev: prevCampaign,
    go: goToCampaign, start: startCampaigns, stop: stopCampaigns,
} = useSlider(() => props.campaigns, 7000);
</script>

<template>
    <PublicLayout title="Home">
        <!-- Hero slider: only slides published from the admin Sliders tab, text on solid panel per design recommendations -->
        <section v-if="sliders.length" class="bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
                <div v-reveal:zoom class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-nyale-blue to-nyale-blue-dark shadow-xl"
                    @mouseenter="stopAutoplay" @mouseleave="startAutoplay">
                    <Transition name="fade" mode="out-in">
                        <div v-if="activeSlide" :key="activeSlide.id" class="relative">
                            <img v-if="activeSlide.image" :src="`/storage/${activeSlide.image}`" :alt="activeSlide.title"
                                class="absolute inset-0 h-full w-full object-cover" />
                            <div class="slide-overlay absolute inset-0"></div>
                            <div class="relative grid lg:grid-cols-2 items-center">
                                <div class="px-8 py-14 sm:px-12 lg:py-20">
                                    <p v-if="activeSlide.eyebrow" class="text-sm font-bold uppercase tracking-[0.2em] text-nyale-green mb-3">
                                        {{ activeSlide.eyebrow }}
                                    </p>
                                    <h2 class="text-3xl sm:text-4xl font-extrabold leading-tight text-white">
                                        {{ activeSlide.title }}
                                    </h2>
                                    <RichContent v-if="activeSlide.description" :html="activeSlide.description"
                                        class="mt-6 text-base leading-relaxed text-white/85 max-w-xl line-clamp-4" />
                                    <div class="mt-8 flex flex-wrap gap-3">
                                        <Link :href="activeSlide.button_url || '/about'"
                                            class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-nyale-blue shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">
                                            {{ activeSlide.button_label || 'Learn More' }}
                                        </Link>
                                        <Link href="/get-involved"
                                            class="inline-flex items-center gap-2 rounded-full border-2 border-white/40 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                                            Get Involved
                                        </Link>
                                    </div>
                                </div>
                                <div class="hidden lg:flex h-full min-h-[22rem] items-center justify-center">
                                    <Icon v-if="!activeSlide.image" name="sparkles" size="h-40 w-40 text-white/25" />
                                </div>
                            </div>
                        </div>
                    </Transition>

                    <!-- Slider controls -->
                    <template v-if="sliders.length > 1">
                        <button @click="prevSlide" aria-label="Previous story"
                            class="absolute left-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/30">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <button @click="nextSlide" aria-label="Next story"
                            class="absolute right-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/30">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2">
                            <button v-for="(slide, index) in sliders" :key="slide.id" @click="goToSlide(index)"
                                :aria-label="`Go to slide ${index + 1}`"
                                class="h-2 rounded-full transition-all"
                                :class="index === currentSlide ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/60'">
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Programs teaser (What We Do) -->
        <section class="py-14 bg-nyale-green">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-reveal class="flex items-end justify-between mb-10">
                    <div>
                        <h2 class="text-4xl sm:text-5xl font-extrabold uppercase tracking-tight text-white">What We Do</h2>
                        <div class="mt-2 h-px w-40 bg-white"></div>
                    </div>
                    <Link href="/programs" class="hidden sm:inline text-sm font-bold text-white hover:underline">
                        View all programs &rarr;
                    </Link>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <Link v-for="(area, index) in workAreas" :key="area.id" :href="`/programs/${area.slug}`" v-reveal="index * 120"
                        class="group rounded-3xl border border-nyale-blue/15 bg-white overflow-hidden shadow-sm transition hover:shadow-xl hover:-translate-y-1">
                        <div v-if="area.image" class="h-40 overflow-hidden">
                            <img :src="`/storage/${area.image}`" :alt="area.title"
                                class="h-full w-full object-cover transition group-hover:scale-105" />
                        </div>
                        <h3 class="bg-nyale-blue px-6 py-3.5 text-lg font-extrabold leading-tight text-white transition group-hover:bg-nyale-blue-dark">
                            {{ area.title }}
                        </h3>
                        <div class="p-6">
                            <p class="text-sm text-nyale-navy/65 leading-relaxed line-clamp-4">{{ area.summary }}</p>
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-nyale-blue">
                                Read more &rarr;
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Campaigns slider -->
        <section v-if="campaigns.length" class="py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-reveal:zoom class="relative overflow-hidden rounded-4xl bg-gradient-to-r from-nyale-green to-nyale-blue text-white shadow-xl"
                    @mouseenter="stopCampaigns" @mouseleave="startCampaigns">
                    <Transition name="fade" mode="out-in">
                        <div v-if="activeCampaign" :key="activeCampaign.id" class="relative">
                            <img v-if="activeCampaign.banner" :src="`/storage/${activeCampaign.banner}`" :alt="activeCampaign.title"
                                class="absolute inset-0 h-full w-full object-cover" />
                            <div v-if="activeCampaign.banner" class="absolute inset-0 bg-gradient-to-r from-nyale-navy/85 via-nyale-navy/60 to-nyale-blue/60"></div>
                            <div class="relative flex min-h-[19rem] flex-col justify-center px-8 py-14 sm:px-16 sm:py-16">
                                <p class="text-sm font-bold uppercase tracking-widest text-white/80">Campaigns &amp; Advocacy</p>
                                <h2 class="mt-3 max-w-3xl text-3xl font-extrabold">{{ activeCampaign.title }}</h2>
                                <p class="mt-4 max-w-2xl text-white/90 leading-relaxed line-clamp-3">{{ activeCampaign.summary }}</p>
                                <div>
                                    <Link :href="`/campaigns/${activeCampaign.slug}`"
                                        class="mt-6 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-nyale-blue shadow-md hover:-translate-y-0.5 transition">
                                        Learn More
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </Transition>

                    <template v-if="campaigns.length > 1">
                        <button @click="prevCampaign" aria-label="Previous campaign"
                            class="absolute left-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/30">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <button @click="nextCampaign" aria-label="Next campaign"
                            class="absolute right-3 top-1/2 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/30">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2">
                            <button v-for="(campaign, index) in campaigns" :key="campaign.id" @click="goToCampaign(index)"
                                :aria-label="`Go to campaign ${index + 1}`"
                                class="h-2 rounded-full transition-all"
                                :class="index === currentCampaign ? 'w-6 bg-white' : 'w-2 bg-white/40 hover:bg-white/60'">
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Featured story of change -->
        <section v-if="featuredStory" class="py-14 bg-nyale-blue-light">
            <div v-reveal class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-nyale-blue">
                    {{ categoryLabels[featuredStory.category] || 'Story of Change' }}
                </p>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-nyale-navy">{{ featuredStory.title }}</h2>
                <p class="mt-4 text-nyale-navy/70 leading-relaxed">{{ featuredStory.summary }}</p>
                <Link :href="`/our-impact/${featuredStory.slug}`" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-nyale-blue hover:text-nyale-blue-dark">
                    Read full story &rarr;
                </Link>
            </div>
        </section>

        <!-- Latest news -->
        <section v-if="latestPosts.length" class="py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-reveal class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-nyale-blue">Stay Informed</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-nyale-navy">News &amp; Updates</h2>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="(post, index) in latestPosts" :key="post.id" :href="`/news/${post.slug}`" v-reveal="index * 120"
                        class="group rounded-3xl border border-nyale-blue/10 overflow-hidden transition hover:shadow-lg hover:-translate-y-1">
                        <div class="h-40 bg-gradient-to-br from-nyale-blue-light to-nyale-blue/20 flex items-center justify-center overflow-hidden">
                            <img v-if="post.cover_image" :src="`/storage/${post.cover_image}`" :alt="post.title"
                                class="h-full w-full object-cover transition group-hover:scale-105" />
                            <Icon v-else name="document-text" size="h-10 w-10 text-nyale-blue/40" />
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-nyale-green">{{ post.category?.replace('_', ' ') }}</p>
                            <h3 class="mt-2 font-bold text-nyale-navy group-hover:text-nyale-blue">{{ post.title }}</h3>
                            <p class="mt-2 text-sm text-nyale-navy/60 line-clamp-2">{{ post.excerpt }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-nyale-navy">
            <div v-reveal class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-extrabold text-white">Partner with Nyale Institute</h2>
                <p class="mt-4 text-white/70">
                    Whether you are a donor, researcher, policymaker or community organisation — there are many ways
                    to work with us to advance sexual and reproductive justice in Malawi.
                </p>
                <Link href="/get-involved"
                    class="mt-8 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-nyale-blue to-nyale-green px-8 py-3.5 text-sm font-bold text-white shadow-lg hover:-translate-y-0.5 transition">
                    Get Involved
                </Link>
            </div>
        </section>

        <!-- Impact stats: last section before the footer, numbers count up when scrolled into view -->
        <section v-if="stats.length" class="py-16 bg-nyale-blue-light">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-reveal class="text-center mb-10">
                    <p class="text-sm font-bold uppercase tracking-widest text-nyale-blue">Results &amp; Change</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-nyale-navy">Our Impact in Numbers</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div v-for="(stat, index) in stats" :key="stat.id" v-reveal:zoom="index * 120"
                        class="rounded-3xl bg-white p-6 text-center shadow-sm">
                        <p class="text-3xl sm:text-4xl font-extrabold text-nyale-blue">
                            <CountUp :value="stat.value" />
                        </p>
                        <p class="mt-2 text-sm font-semibold text-nyale-navy/70">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Solid brand blue behind the text, fading to transparent so the photo shows through, with a faint green tint on the right */
.slide-overlay {
    background: rgba(31, 154, 214, 0.85);
}

@media (min-width: 1024px) {
    .slide-overlay {
        background: linear-gradient(
            to right,
            #1f9ad6 0%,
            #1f9ad6 32%,
            rgba(31, 154, 214, 0.78) 50%,
            rgba(31, 154, 214, 0.35) 75%,
            rgba(109, 190, 69, 0.35) 100%
        );
    }
}
</style>
