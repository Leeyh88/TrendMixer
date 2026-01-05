<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { router, Link, Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    remixes: Object, // 페이징 처리된 데이터
    genres: Array,
    filters: Object
});

// 검색 및 필터 상태 관리
const selectedGenre = ref(props.filters.genre || '');
const selectedSort = ref(props.filters.sort || 'latest');

// 필터 변경 시 서버에 데이터 요청
const updateFilters = () => {
    router.get(route('remixes.index'), {
        genre: selectedGenre.value,
        sort: selectedSort.value
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
};

// 3. [핵심] 헤더 클릭 등 모든 진입 시점에 파라미터 체크
const checkAndRedirect = () => {
    const params = new URLSearchParams(window.location.search);
    // sort 파라미터가 없으면 무조건 강제 이동
    if (!params.has('sort')) {
        updateFilters();
    }
};

onMounted(() => {
    checkAndRedirect();
});

watch(() => props.filters, (newFilters) => {
    selectedGenre.value = newFilters.genre || '';
    selectedSort.value = newFilters.sort || 'latest';
    
    // 주소창에 sort가 사라졌다면 다시 붙여줌
    const params = new URLSearchParams(window.location.search);
    if (!params.has('sort')) {
        updateFilters();
    }
}, { deep: true });

watch([selectedGenre, selectedSort], () => {
    updateFilters();
});

// 유튜브 썸네일 가져오기 함수
const getThumbnail = (videoId) => {
    return `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;
};

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
    <Head title="리믹스" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-[#F8FAFC] p-8 rounded-[2.5rem] shadow-sm border border-gray-200">
                    <!-- <div class="min-h-screen bg-[#F8FAFC] py-12 px-4 sm:px-6"> -->
                        <div class="max-w-7xl mx-auto">
                            
                            <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                                <div>
                                    <h1 class="text-4xl font-black text-gray-900 tracking-tighter">EXPLORE REMIXES</h1>
                                    <p class="text-gray-500 font-bold mt-2 uppercase tracking-widest text-xs">Discover the trendiest sounds created by our community</p>
                                </div>
                                
                                <Link :href="route('remixes.create')" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                    UPLOAD NEW VIBE
                                </Link>
                            </header>
                            <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-gray-100 mb-10 flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                                    <button 
                                        @click="selectedGenre = ''"
                                        :class="selectedGenre === '' ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                                        class="px-5 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap"
                                    >
                                        ALL GENRES
                                    </button>
                                    <button 
                                        v-for="genre in genres" 
                                        :key="genre.id"
                                        @click="selectedGenre = genre.slug"
                                        :class="selectedGenre === genre.slug ? 'bg-indigo-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                                        class="px-5 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap uppercase"
                                    >
                                        {{ genre.name }}
                                    </button>
                                </div>

                                <div class="flex items-center gap-3">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sort by</label>
                                    <select v-model="selectedSort" class="bg-gray-50 border-none rounded-xl text-xs font-black text-gray-700 focus:ring-2 focus:ring-indigo-500 pr-10">
                                        <option value="latest">LATEST</option>
                                        <option value="popular">MOST VOTED</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="remixes.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                                <div v-for="remix in remixes.data" :key="remix.id" class="group bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-500">
                                    
                                    <div
                                        @click="openVideo(remix.youtube_video_id)"
                                        class="relative aspect-video overflow-hidden cursor-pointer"
                                    >
                                        <img :src="getThumbnail(remix.youtube_video_id)" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Thumbnail">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white scale-90 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 left-4">
                                            <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-indigo-600 text-[10px] font-black rounded-lg uppercase shadow-sm">
                                                {{ remix.genre?.name }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ remix.music_track?.title }}</p>
                                        </div>
                                        <h3 class="text-lg font-black text-gray-900 line-clamp-1 mb-4 group-hover:text-indigo-600 transition-colors">
                                            {{ remix.title }}
                                        </h3>
                                        
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-500 border border-gray-200 uppercase">
                                                    {{ remix.user?.name.charAt(0) }}
                                                </div>
                                                <span class="text-xs font-bold text-gray-600">{{ remix.user?.name }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-500 fill-current" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                                <span class="text-sm font-black text-gray-900">{{ remix.vote_count }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-40">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">결과가 없습니다.</h3>
                                <p class="text-gray-400 mt-2 font-medium">다른 장르나 필터를 선택해 보세요.</p>
                            </div>

                            <div v-if="remixes.links.length > 3" class="mt-16 flex justify-center gap-2">
                                <Link 
                                    v-for="(link, k) in remixes.links" 
                                    :key="k"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-4 py-2 rounded-xl font-black text-xs transition-all"
                                    :class="{ 'bg-indigo-600 text-white': link.active, 'bg-white text-gray-400 hover:bg-gray-50': !link.active, 'opacity-50 pointer-events-none': !link.url }"
                                />
                            </div>

                        </div>
                    <!-- </div> -->
                </div>
            </div>
        </div>
        <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeVideo"></div>
            
            <div class="relative w-full max-w-4xl aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl">
                <iframe 
                    class="w-full h-full"
                    :src="`https://www.youtube.com/embed/${currentVideoId}?autoplay=1`" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                ></iframe>
                
                <button @click="closeVideo" class="absolute -top-12 right-0 text-white flex items-center gap-2 font-black hover:text-gray-300">
                    CLOSE <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>