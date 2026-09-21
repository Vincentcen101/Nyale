<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps({
    aboutSections: { type: Object, default: () => ({}) },
    board: { type: Array, default: () => [] },
    team: { type: Array, default: () => [] },
});

const initials = (name) => name.split(' ').filter(Boolean).map((w) => w[0]).join('').slice(0, 2).toUpperCase();
</script>

<template>
    <PublicLayout title="About Us">
        <!-- Who we are: stacked title, vertical divider, text -->
        <section class="bg-nyale-blue">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
                <div class="flex flex-col md:flex-row md:items-stretch gap-8 md:gap-0">
                    <h1 v-reveal:left class="md:w-72 flex-shrink-0 text-center md:text-right text-6xl md:text-7xl font-extrabold leading-[0.95] text-white md:border-r md:border-white/70 md:pr-8">
                        <span v-for="word in (aboutSections.who_we_are?.title || 'Who we are').split(' ')" :key="word" class="block">{{ word }}</span>
                    </h1>
                    <div v-reveal="150" class="flex-1 md:pl-8 space-y-5 text-sm font-bold leading-relaxed text-white text-justify self-center">
                        <p v-for="(paragraph, i) in (aboutSections.who_we_are?.content || '').split(/\n+/).filter(Boolean)" :key="i">
                            {{ paragraph }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story: rounded picture beside a large heading -->
        <section v-if="aboutSections.our_story" class="py-16">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center gap-8 md:gap-12">
                    <div v-reveal:zoom class="mx-auto md:mx-0 h-72 w-56 flex-shrink-0 overflow-hidden rounded-3xl shadow-lg bg-gradient-to-b from-nyale-navy to-nyale-blue-dark flex items-center justify-center">
                        <img :src="aboutSections.our_story.image ? `/storage/${aboutSections.our_story.image}` : '/images/our-story-lantern.webp'"
                            alt="A lit lantern glowing at dusk" class="h-full w-full object-cover" />
                    </div>
                    <div v-reveal="150">
                        <h2 class="text-5xl sm:text-6xl font-extrabold leading-tight text-nyale-blue">
                            {{ aboutSections.our_story.title || 'Our Story' }}
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-nyale-navy/80 max-w-2xl">
                            {{ aboutSections.our_story.content }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision / Mission / Core Values -->
        <section class="pb-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-6">
                    <div v-if="aboutSections.vision" class="rounded-3xl border border-nyale-blue/15 p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-nyale-blue-light text-nyale-blue">
                            <Icon name="globe" />
                        </div>
                        <h3 class="mt-4 text-xl font-extrabold text-nyale-navy">{{ aboutSections.vision.title }}</h3>
                        <p class="mt-2 text-sm text-nyale-navy/70 leading-relaxed">{{ aboutSections.vision.content }}</p>
                    </div>
                    <div v-if="aboutSections.mission" class="rounded-3xl border border-nyale-blue/15 p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-nyale-blue-light text-nyale-blue">
                            <Icon name="sparkles" />
                        </div>
                        <h3 class="mt-4 text-xl font-extrabold text-nyale-navy">{{ aboutSections.mission.title }}</h3>
                        <p class="mt-2 text-sm text-nyale-navy/70 leading-relaxed">{{ aboutSections.mission.content }}</p>
                    </div>
                    <div v-if="aboutSections.core_values" class="rounded-3xl border border-nyale-blue/15 p-8">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-nyale-blue-light text-nyale-blue">
                            <Icon name="heart" />
                        </div>
                        <h3 class="mt-4 text-xl font-extrabold text-nyale-navy">{{ aboutSections.core_values.title }}</h3>
                        <ul class="mt-2 text-sm text-nyale-navy/70 space-y-1">
                            <li v-for="(v, i) in aboutSections.core_values.content.split('\n')" :key="i">&middot; {{ v }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Board Members: circular photos on solid background -->
        <section v-if="board.length" class="py-16 bg-nyale-green">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-white text-center">Our Board Members</h2>
                <div class="mt-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8">
                    <div v-for="member in board" :key="member.id" class="text-center">
                        <div class="mx-auto h-32 w-32 sm:h-40 sm:w-40 lg:h-44 lg:w-44 rounded-full border-[5px] border-white bg-white/20 flex items-center justify-center overflow-hidden shadow-lg">
                            <img v-if="member.photo" :src="`/storage/${member.photo}`" :alt="member.name" class="h-full w-full object-cover object-top" />
                            <span v-else class="text-4xl lg:text-5xl font-extrabold text-white">{{ initials(member.name) }}</span>
                        </div>
                        <p class="mt-4 font-bold text-white text-base">{{ member.name }}</p>
                        <p class="text-sm text-white/80">{{ member.role }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team -->
        <section v-if="team.length" class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-nyale-navy text-center">Our Team</h2>
                <p class="mt-2 text-center text-nyale-navy/60">We are a team of seasoned experts with years of experience.</p>
                <div class="mt-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8">
                    <div v-for="member in team" :key="member.id" class="text-center">
                        <div class="mx-auto h-32 w-32 sm:h-36 sm:w-36 lg:h-40 lg:w-40 rounded-full border-[5px] border-nyale-blue-light bg-nyale-blue-light flex items-center justify-center overflow-hidden shadow-md">
                            <img v-if="member.photo" :src="`/storage/${member.photo}`" :alt="member.name" class="h-full w-full object-cover object-top" />
                            <span v-else class="text-3xl lg:text-4xl font-extrabold text-nyale-blue">{{ initials(member.name) }}</span>
                        </div>
                        <p class="mt-4 font-bold text-nyale-navy text-base">{{ member.name }}</p>
                        <p class="text-sm text-nyale-navy/60">{{ member.role }}</p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
