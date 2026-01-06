<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { router, Link, Head } from '@inertiajs/vue3';

const props = defineProps({
    remixes: Object,
    genres: Array,
    filters: Object
});

const selectedGenre = ref(props.filters.genre || '');
const selectedSort = ref(props.filters.sort || 'latest');

const updateFilters = () => {
    router.get(route('remixes.index'), {
        genre: selectedGenre.value,
        sort: selectedSort.value
    }, { preserveState: true, replace: true, preserveScroll: true });
};

const checkAndRedirect = () => {
    const params = new URLSearchParams(window.location.search);
    if (!params.has('sort')) updateFilters();
};

onMounted(() => { checkAndRedirect(); });

watch(() => props.filters, (newFilters) => {
    selectedGenre.value = newFilters.genre || '';
    selectedSort.value = newFilters.sort || 'latest';
}, { deep: true });

watch([selectedGenre, selectedSort], () => { updateFilters(); });

const getThumbnail = (videoId) => `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;

const isModalOpen = ref(false);
const currentVideoId = ref('');

const openVideo = (videoId) => {
    currentVideoId.value = videoId;
    isModalOpen.value = true;
};

const closeVideo = () => {
    currentVideoId.value = '';
    isModalOpen.value = false;
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
                        
                        <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between gap-4 overflow-hidden">
                            <div class="flex items-center gap-1 overflow-x-auto no-scrollbar px-2">
                                <button 
                                    @click="selectedGenre = ''"
                                    class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase tracking-tighter"
                                    :class="selectedGenre === '' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50'"
                                >
                                    All Genres
                                </button>
                                <button 
                                    v-for="genre in genres" :key="genre.id"
                                    @click="selectedGenre = genre.slug"
                                    class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase tracking-tighter"
                                    :class="selectedGenre === genre.slug ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50'"
                                >
                                    {{ genre.name }}
                                </button>
                            </div>
                            
                            <div class="hidden sm:flex items-center px-4 border-l border-gray-50">
                                <select v-model="selectedSort" class="bg-transparent border-none text-[11px] font-black text-gray-500 focus:ring-0 cursor-pointer uppercase tracking-widest">
                                    <option value="latest text-black">Latest</option>
                                    <option value="popular text-black">Most Voted</option>
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
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate">{{ remix.music_track?.title }}</p>
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
                            <Link 
                                v-for="(link, k) in remixes.links" :key="k"
                                :href="link.url" v-html="link.label"
                                class="px-4 py-2 rounded-xl font-black text-xs transition-all"
                                :class="[
                                    link.active ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-400 border border-gray-100 hover:bg-gray-50',
                                    !link.url ? 'opacity-20 pointer-events-none' : ''
                                ]"
                            />
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

        <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeVideo"></div>
            <div class="relative w-full max-w-4xl aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl">
                <iframe class="w-full h-full" :src="`https://www.youtube.com/embed/${currentVideoId}?autoplay=1`" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <button @click="closeVideo" class="absolute -top-12 right-0 text-white flex items-center gap-2 font-black hover:text-gray-300">
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