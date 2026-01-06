<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // Link 추가
import { ref, computed } from 'vue';

const props = defineProps({
    rankings: Array,
    currentSort: String,
    // [가정] 사이드바용 실시간 데이터가 있다면 추가
    liveMatches: Array 
});

const changeSort = (sortType) => {
    router.get(route('rankings'), { sort: sortType }, {
        preserveState: true, 
        replace: true
    });
};

const selectedRemix = ref(null);
const isModalOpen = ref(false);

const getYoutubeId = (url) => {
    if (!url) return '';
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : '';
}

const openVideo = (data) => {
    selectedRemix.value = data;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedRemix.value = null;
}

const videoUrl = computed(() => {
    if (!selectedRemix.value) return '';
    const videoId = getYoutubeId(selectedRemix.value.youtube_url);
    return `https://www.youtube.com/embed/${videoId}?autoplay=1`;
});
</script>

<template>
    <Head title="실시간 순위" />
    <AuthenticatedLayout>
        <div class="py-12 bg-[#F8FAFC] min-h-screen">
            <div class="max-w-6xl mx-auto px-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2">
                        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                            <header class="mb-10">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-black rounded uppercase">Real-time</span>
                                </div>
                                <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Remix Ranking</h1>
                                <p class="text-gray-400 font-bold mt-1 uppercase tracking-widest text-[10px]">Explore the most liked vibes.</p>
                            </header>

                            <div class="bg-gray-50 p-1.5 rounded-2xl mb-10 flex gap-1">
                                <button @click="changeSort('votes')"
                                    :class="currentSort === 'votes' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="flex-1 py-3 rounded-xl text-xs font-black transition-all uppercase tracking-widest">
                                    Most Liked
                                </button>
                                <button @click="changeSort('views')"
                                    :class="currentSort === 'views' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                                    class="flex-1 py-3 rounded-xl text-xs font-black transition-all uppercase tracking-widest">
                                    Most Viewed
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div v-for="(remix, index) in rankings" :key="remix.id" 
                                    @click="openVideo(remix)"
                                    class="group flex items-center gap-4 p-4 rounded-[1.8rem] border border-transparent hover:border-indigo-100 hover:bg-indigo-50/30 transition-all cursor-pointer">
                                    
                                    <div class="w-12 flex justify-center">
                                        <span v-if="index === 0" class="text-3xl">🥇</span>
                                        <span v-else-if="index === 1" class="text-3xl">🥈</span>
                                        <span v-else-if="index === 2" class="text-3xl">🥉</span>
                                        <span v-else class="font-black text-lg text-gray-200 group-hover:text-gray-400 italic">#{{ index + 1 }}</span>
                                    </div>

                                    <div class="w-14 h-14 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0 shadow-sm border border-gray-50">
                                        <img :src="`https://img.youtube.com/vi/${getYoutubeId(remix.youtube_url)}/default.jpg`" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                    </div>

                                    <div class="flex-grow min-w-0">
                                        <h4 class="font-black text-gray-900 group-hover:text-indigo-600 transition-colors truncate leading-tight">
                                            {{ remix.title }}
                                        </h4>
                                        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-tighter mt-0.5 truncate">
                                            {{ remix.music_track?.artist }} <span class="mx-1 text-gray-200">|</span> {{ remix.genre?.name }}
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <span class="block font-black text-indigo-600 text-lg tabular-nums">
                                            {{ (currentSort === 'views' ? remix.view_count : remix.vote_count).toLocaleString() }}
                                        </span>
                                        <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">
                                            {{ currentSort === 'views' ? 'Views' : 'Likes' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-8">
                        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100 overflow-hidden relative">
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="font-black text-xs uppercase tracking-[0.2em] text-indigo-400">Live Clash 🔥</h3>
                                    <span class="text-[9px] font-bold text-white/50 animate-pulse">LIVE NOW</span>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between gap-3 font-black text-sm italic">
                                        <span class="truncate">Supernova</span>
                                        <span class="text-indigo-500 text-[10px]">VS</span>
                                        <span class="truncate">Armageddon</span>
                                    </div>
                                    <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden flex">
                                        <div class="bg-indigo-500 h-full w-[65%]"></div>
                                    </div>
                                    <p class="text-[9px] text-white/40 font-bold uppercase tracking-widest text-center mt-2">6,241 Mixers Participating</p>
                                </div>
                            </div>
                            <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-600 rounded-full blur-[60px] opacity-30"></div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                            <h3 class="font-black text-gray-900 text-xs uppercase tracking-widest mb-6 border-b pb-4">Hot Feed</h3>
                            <div class="space-y-5">
                                <div v-for="n in 3" :key="n" class="cursor-pointer group">
                                    <p class="text-xs font-bold text-gray-700 group-hover:text-indigo-600 transition-colors line-clamp-1">오늘 신곡 리믹스 미쳤네요 ㄷㄷ</p>
                                    <span class="text-[9px] text-gray-400 font-medium uppercase mt-1 inline-block tracking-tighter">2 mins ago</span>
                                </div>
                            </div>
                            <Link href="/posts" class="mt-8 block w-full py-3 bg-gray-50 hover:bg-indigo-50 text-center rounded-xl text-[10px] font-black text-gray-400 hover:text-indigo-600 transition-all uppercase tracking-widest">
                                Visit Community
                            </Link>
                        </div>
                    </aside>

                </div>

                </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" @click.self="closeModal">
            </div>
    </AuthenticatedLayout>
</template>