<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, onMounted, computed } from 'vue';
import { router, Link, Head } from '@inertiajs/vue3';

const props = defineProps({
    remixes: Object,      // 페이징 결과
    remixes_all: Array,   // 원곡 선택 모달용 전체 데이터
    genres: Array,
    filters: Object
});

const selectedGenre = ref(props.filters.genre || '');
const selectedSort = ref(props.filters.sort || 'popular');
const selectedTrack = ref(props.filters.track || ''); // 선택된 원곡 필터

// 모달 및 검색 상태
const isVideoModalOpen = ref(false);
const isSearchModalOpen = ref(false);
const currentVideoId = ref('');
const trackSearchQuery = ref('');

// 중복 제거된 원곡 리스트 계산 (모달용)
const uniqueTracks = computed(() => {
    if (!props.remixes_all) return [];
    
    // track_title 기준으로 중복 제거
    const tracks = props.remixes_all.filter((v, i, a) => 
        a.findIndex(t => t.track_title === v.track_title) === i
    );
    
    if (!trackSearchQuery.value) return tracks;
    
    const query = trackSearchQuery.value.toLowerCase();
    return tracks.filter(t => 
        t.track_title.toLowerCase().includes(query) || 
        t.artist_name.toLowerCase().includes(query)
    );
});

const updateFilters = () => {
    router.get(route('remixes.index'), {
        genre: selectedGenre.value,
        sort: selectedSort.value,
        track: selectedTrack.value
    }, { preserveState: true, replace: true, preserveScroll: true });
};

// 선택 처리 함수 수정
const selectTrack = (track) => {
    // 제목과 가수를 구분자(예: |)로 합쳐서 서버에 보냅니다.
    selectedTrack.value = `${track.track_title}|${track.artist_name}`;
    isSearchModalOpen.value = false;
    trackSearchQuery.value = '';
    updateFilters();
};


// 필터 초기화
const clearTrackFilter = () => {
    selectedTrack.value = '';
    updateFilters();
};

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (!params.has('sort')) updateFilters();
});

watch([selectedGenre, selectedSort], () => { updateFilters(); });

const getThumbnail = (videoId) => `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;

const openVideo = (videoId) => {
    currentVideoId.value = videoId;
    isVideoModalOpen.value = true;
};

const closeVideo = () => {
    currentVideoId.value = '';
    isVideoModalOpen.value = false;
};
</script>

<template>
    <Head title="리믹스 탐색" />

    <AuthenticatedLayout>
        <div class="py-12 bg-[#F8FAFC] min-h-screen">
            <div class="max-w-6xl mx-auto px-6">
                
                <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-black rounded uppercase tracking-wider">Sound Explorer</span>
                        </div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Explore Remixes</h1>
                        <p class="text-gray-400 font-bold mt-1 uppercase tracking-widest text-[10px]">Discover the trendiest sounds created by mixers.</p>
                    </div>
                    
                    <Link :href="route('remixes.create')" class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-gray-200 hover:bg-indigo-600 hover:-translate-y-1 transition-all uppercase tracking-tight">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                        Upload Vibe
                    </Link>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 space-y-8">
                        
                        <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col gap-6">
                            
                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-3xl border border-gray-100 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Filter by Original Track</p>
                                        <h4 class="text-sm font-black text-gray-900 truncate">
                                            {{ selectedTrack ? selectedTrack.split('|')[0] + ' - ' + selectedTrack.split('|')[1] : '모든 곡 리믹스 보기' }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button v-if="selectedTrack" @click="clearTrackFilter" class="text-[10px] font-black text-red-400 hover:text-red-600 uppercase tracking-tighter">Clear</button>
                                    <button @click="isSearchModalOpen = true" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-[10px] font-black text-gray-600 hover:bg-indigo-600 hover:text-white transition-all uppercase tracking-widest">
                                        {{ selectedTrack ? '변경' : '곡 선택' }}
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button 
                                    @click="selectedGenre = ''"
                                    class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-tighter"
                                    :class="selectedGenre === '' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50'"
                                >
                                    All Genres
                                </button>
                                <button 
                                    v-for="genre in genres" :key="genre.id"
                                    @click="selectedGenre = genre.slug"
                                    class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-tighter"
                                    :class="selectedGenre === genre.slug ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50'"
                                >
                                    {{ genre.name }}
                                </button>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Sort Options</span>
                                <select v-model="selectedSort" class="bg-transparent border-none text-[11px] font-black text-gray-500 focus:ring-0 cursor-pointer uppercase tracking-widest">
                                    <option value="latest">Latest Updates</option>
                                    <option value="popular">Most Voted</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="remixes.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div v-for="remix in remixes.data" :key="remix.id" 
                                 class="group bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                                
                                <div @click="openVideo(remix.youtube_video_id)" class="relative aspect-video overflow-hidden cursor-pointer">
                                    <img :src="getThumbnail(remix.youtube_video_id)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Thumbnail">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-indigo-600 shadow-xl scale-90 group-hover:scale-100 transition-transform">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <div class="absolute top-4 left-4">
                                        <span class="px-2 py-1 bg-black/80 backdrop-blur-md text-white text-[9px] font-black rounded-lg uppercase tracking-widest">
                                            {{ remix.genre?.name }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></div>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate">{{ remix.artist_name }} - {{ remix.track_title }}</p>
                                    </div>

                                    <h3 class="text-base font-black text-gray-900 line-clamp-1 group-hover:text-indigo-600 transition-colors mb-4">
                                        {{ remix.title }}
                                    </h3>
                                    
                                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 bg-indigo-50 rounded-full flex items-center justify-center text-[10px] font-bold text-indigo-500 border border-indigo-100 uppercase italic">
                                                {{ remix.user?.name.charAt(0) }}
                                            </div>
                                            <span class="text-[11px] font-bold text-gray-500">{{ remix.user?.name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm font-black text-gray-900 tabular-nums leading-none">{{ remix.vote_count }}</span>
                                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-tighter">Votes</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="bg-white rounded-[2.5rem] py-32 text-center border border-dashed border-gray-200">
                            <h3 class="text-gray-300 font-black uppercase tracking-[0.2em]">No Vibes Found</h3>
                        </div>

                        <div v-if="remixes.links.length > 3" class="flex justify-center gap-1.5 pt-6">
                            <template v-for="(link, k) in remixes.links" :key="k">
                                <div v-if="!link.url" v-html="link.label" class="px-4 py-2 rounded-xl font-black text-xs opacity-20 border border-gray-100 bg-white text-gray-400"></div>
                                <Link v-else :href="link.url" v-html="link.label" class="px-4 py-2 rounded-xl font-black text-xs transition-all" :class="[link.active ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50']" />
                            </template>
                        </div>
                    </div>

                    <aside class="space-y-8">
                        <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
                            <div class="relative z-10">
                                <h3 class="font-black text-xs uppercase tracking-[0.2em] text-indigo-200 mb-6 italic">Weekly Pick ⭐</h3>
                                <p class="text-lg font-black leading-tight mb-4 italic">Next Level <br/>(Cyberpunk Remix)</p>
                                <p class="text-[10px] font-bold text-indigo-200 mb-8 uppercase">By @MixMaster_Lee</p>
                                <button class="px-5 py-2.5 bg-white text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg">Listen Now</button>
                            </div>
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-[40px] group-hover:scale-125 transition-transform duration-700"></div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                            <h3 class="font-black text-gray-900 text-[10px] uppercase tracking-widest mb-6 border-b pb-4 italic">Genre Guide</h3>
                            <div class="space-y-4">
                                <div v-for="g in genres.slice(0, 3)" :key="g.id" class="flex items-center justify-between group cursor-pointer">
                                    <span class="text-xs font-bold text-gray-500 group-hover:text-indigo-600 transition-colors">{{ g.name }}</span>
                                    <span class="text-[10px] font-black text-gray-200 tabular-nums">0{{ Math.floor(Math.random() * 9) + 1 }}</span>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <div v-if="isSearchModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @click="isSearchModalOpen = false"></div>
            <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl animate-in zoom-in duration-200">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-xl font-black italic uppercase tracking-tighter mb-4">Find Track Vibe</h3>
                    <input 
                        v-model="trackSearchQuery" 
                        type="text" 
                        placeholder="원곡 제목이나 가수를 검색하세요..." 
                        class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-600" 
                    />
                </div>
                <div class="max-h-[400px] overflow-y-auto p-4 space-y-2 no-scrollbar">
                    <div 
                        v-for="track in uniqueTracks" :key="track.id" 
                        @click="selectTrack(track)"
                        class="flex items-center gap-4 p-4 rounded-3xl hover:bg-indigo-50 cursor-pointer group transition-all"
                    >
                        <img :src="track.album_cover_url" class="w-12 h-12 rounded-xl object-cover shadow-sm border border-gray-100" />
                        <div class="flex-grow">
                            <p class="text-xs font-black text-gray-900 group-hover:text-indigo-600">{{ track.track_title }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ track.artist_name }}</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-200 group-hover:text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" /></svg>
                    </div>
                    <div v-if="uniqueTracks.length === 0" class="py-12 text-center text-gray-300 font-bold uppercase text-xs tracking-widest">
                        No tracks found
                    </div>
                </div>
                <button @click="isSearchModalOpen = false" class="w-full py-5 bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-100 transition-all">Close</button>
            </div>
        </div>

        <div v-if="isVideoModalOpen && currentVideoId" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeVideo"></div>
            <div class="relative w-full max-w-4xl aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl">
                <iframe 
                    class="w-full h-full" 
                    :src="`https://www.youtube.com/embed/${currentVideoId}?autoplay=1`" 
                    frameborder="0" 
                    allow="autoplay; encrypted-media" 
                    allowfullscreen
                ></iframe>
                <button @click="closeVideo" class="absolute -top-12 right-0 text-white flex items-center gap-2 font-black hover:text-gray-300 transition-all uppercase tracking-widest">
                    Close <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>