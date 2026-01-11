<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    rankings: Array,
    currentSort: String,
    stats: Object // ✅ 컨트롤러에서 보낸 통계 객체
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
    <Head title="순위" />
    <AuthenticatedLayout>
        <div class="py-12 bg-[#F8FAFC] min-h-screen">
            <div class="max-w-6xl mx-auto px-6">
                
                <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-black rounded uppercase">Live Updates</span>
                        </div>
                        
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Live Battle Board</h1>
                                <p class="text-gray-400 font-bold mt-1 uppercase tracking-widest text-[10px]">The Survivors of Mix Match Duel.</p>
                    </div>
                    
                    <!-- <Link :href="route('mixmatch')" class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-gray-200 hover:bg-indigo-600 hover:-translate-y-1 transition-all uppercase tracking-tight">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        Join Mix Match
                    </Link> -->
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-1 overflow-x-auto no-scrollbar">
                            <button @click="changeSort('votes')"
                                class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase tracking-tighter"
                                :class="currentSort === 'votes' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50'">
                                Most Liked
                            </button>
                            <button @click="changeSort('views')"
                                class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase tracking-tighter"
                                :class="currentSort === 'views' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50'">
                                Most Viewed
                            </button>
                        </div> -->

                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="divide-y divide-gray-50">
                                <div v-for="(remix, index) in rankings" :key="remix.id" 
                                    @click="openVideo(remix)"
                                    class="p-6 hover:bg-indigo-50/20 transition-all group cursor-pointer">
                                    <div class="flex items-center gap-6">
                                        <div class="w-10 flex-shrink-0 text-center">
                                            <span v-if="index === 0" class="text-3xl">🥇</span>
                                            <span v-else-if="index === 1" class="text-3xl">🥈</span>
                                            <span v-else-if="index === 2" class="text-3xl">🥉</span>
                                            <span v-else class="font-black text-sm text-gray-200 group-hover:text-indigo-300 transition-colors italic">#{{ index + 1 }}</span>
                                        </div>

                                        <div class="w-16 h-16 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0 shadow-sm border border-gray-50">
                                            <img :src="`https://img.youtube.com/vi/${getYoutubeId(remix.youtube_url)}/default.jpg`" 
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                                        </div>

                                        <div class="flex-grow min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">{{ remix.genre?.name }}</span>
                                                <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ remix.music_track?.artist }}</span>
                                            </div>
                                            <h4 class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors leading-snug truncate">
                                                {{ remix.title }}
                                            </h4>
                                        </div>

                                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                            <span class="text-xl font-black text-gray-900 tabular-nums italic">
                                                {{ (currentSort === 'views' ? remix.view_count : remix.vote_count).toLocaleString() }}
                                            </span>
                                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">
                                                {{ currentSort === 'views' ? 'Views' : 'Wins' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="rankings.length === 0" class="py-24 text-center">
                                    <p class="text-gray-300 font-black uppercase tracking-widest">No Rankings Available</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-8">
                        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                            <div class="relative z-10">
                                <h3 class="font-black text-xs uppercase tracking-[0.2em] text-indigo-400 mb-6">Ranking Stats</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[9px] font-bold text-white/40 uppercase mb-1">Total Votes</p>
                                        <p class="text-2xl font-black italic">{{ stats.total_points }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-bold text-white/40 uppercase mb-1">Total Mixes</p>
                                        <p class="text-2xl font-black italic">{{ stats.total_remixes }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-indigo-600 rounded-full blur-[60px] opacity-20"></div>
                        </div>



                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                            <h3 class="font-black text-gray-900 text-[10px] uppercase tracking-widest mb-6 border-b pb-4 italic">
                                Chart Guidelines
                            </h3>
                            <ul class="space-y-5">
                                <li class="flex gap-3 items-start group">
                                    <div class="w-5 h-5 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 transition-colors">
                                        <svg class="w-3 h-3 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    </div>
                                    <p class="text-[11px] font-bold text-gray-500 leading-relaxed uppercase">
                                        순위는 <span class="text-indigo-600">믹스매치 배틀</span>에서 승리하여 획득한 <span class="text-gray-900">누적 점수</span>로 결정됩니다.
                                    </p>
                                </li>

                                <li class="flex gap-3 items-start group">
                                    <div class="w-5 h-5 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 transition-colors">
                                        <svg class="w-3 h-3 text-indigo-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </div>
                                    <p class="text-[11px] font-bold text-gray-500 leading-relaxed uppercase">
                                        모든 대결은 시스템에 의해 <span class="text-gray-900">랜덤으로 매칭</span>되며, 유저는 두 개의 후보 중 하나를 직접 선택합니다.
                                    </p>
                                </li>

                                <li class="flex gap-3 items-start group">
                                    <div class="w-5 h-5 rounded-lg bg-red-50 flex items-center justify-center shrink-0 group-hover:bg-red-600 transition-colors">
                                        <svg class="w-3 h-3 text-red-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <p class="text-[11px] font-bold text-gray-500 leading-relaxed uppercase">
                                        매크로 사용 등 부정적인 방법으로 점수를 조작할 경우, 해당 리믹스는 <span class="text-red-500">차트에서 즉시 제외</span>될 수 있습니다.
                                    </p>
                                </li>
                            </ul>

                            <Link :href="route('mixmatch')" class="mt-8 block w-full py-3 bg-indigo-50 text-indigo-600 text-center rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                                Go to Mix Match
                            </Link>
                        </div>



                    </aside>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" @click.self="closeModal">
            <div class="relative w-full max-w-4xl aspect-video bg-black rounded-[2rem] overflow-hidden shadow-2xl">
                <iframe class="w-full h-full" :src="videoUrl" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <button @click="closeModal" class="absolute -top-12 right-0 text-white flex items-center gap-2 font-black hover:text-gray-300">
                    CLOSE <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>