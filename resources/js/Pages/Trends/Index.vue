<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    charts: {
        type: Object,
        default: () => ({}) // 데이터가 없을 경우 빈 객체로 초기화
    }
});

const currentTab = ref('all');
const currentSubMenu = ref('미국 음악 Top 10');

const platforms = [
    { name: 'All Trends', value: 'all', color: 'bg-gray-900', icon: 'M', brandColor: '#6366f1' },
    // 1순위: 유튜브
    { 
        name: 'YouTube', 
        value: 'youtube', 
        color: 'bg-[#FF0000]', 
        brandColor: '#FF0000',
        icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>`
    },
    // 2순위: 스포티파이
    { 
        name: 'Spotify', 
        value: 'spotify', 
        color: 'bg-[#1DB954]', 
        brandColor: '#1DB954',
        icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.508 17.302c-.223.367-.704.484-1.07.262-2.955-1.805-6.674-2.213-11.056-1.21-.418.096-.837-.168-.933-.586-.096-.418.168-.837.586-.933 4.802-1.097 8.917-.626 12.21 1.387.368.223.484.704.263 1.072zm1.47-3.253c-.28.455-.877.6-1.33.32-3.383-2.08-8.542-2.685-12.544-1.47-.514.156-1.04-.145-1.196-.658-.156-.514.145-1.04.658-1.196 4.57-1.387 10.27-.714 14.09 1.636.455.28.6.877.32 1.33zm.126-3.414C15.035 8.293 8.32 8.07 4.43 9.25c-.62.188-1.267-.172-1.455-.792-.188-.62.172-1.267.792-1.455 4.493-1.363 11.906-1.107 16.6 1.68.558.33.742 1.05.412 1.608-.33.558-1.05.742-1.608.412z"/></svg>`
    },
    // 3순위: 사운드클라우드
    { 
        name: 'SoundCloud', 
        value: 'soundcloud', 
        color: 'bg-[#FF5500]', 
        brandColor: '#FF5500',
        icon: `<svg viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7"><path d="M11.56 16.52c.1 0 .17-.08.17-.18V8.62c0-.1-.07-.18-.17-.18s-.17.08-.17.18v7.72c0 .1.08.18.17.18zm-1.89 0c.1 0 .18-.08.18-.18V8.93c0-.1-.08-.18-.18-.18s-.18.08-.18.18v7.41c0 .1.08.18.18.18zm-1.88 0c.1 0 .18-.08.18-.18v-6.33c0-.1-.08-.18-.18-.18s-.18.08-.18.18v6.33c0 .1.08.18.18.18zm-1.89 0c.1 0 .18-.08.18-.18v-4.13c0-.1-.08-.18-.18-.18s-.18.08-.18.18v4.13c0 .1.08.18.18.18zm-1.89 0c.1 0 .18-.08.18-.18v-2.06c0-.1-.08-.18-.18-.18s-.18.08-.18.18v2.06c0 .1.08.18.18.18zm-1.89-.35c.1 0 .18-.08.18-.18v-1.02c0-.1-.08-.18-.18-.18s-.18.08-.18.18v1.02c0 .1.08.18.18.18zm-1.88-.69c.1 0 .17-.08.17-.18v-.26c0-.1-.07-.17-.17-.17s-.17.08-.17.18v.26c0 .1.08.17.17.17zm18.91-.71c-.02-.02-.05-.03-.08-.03a2.38 2.38 0 0 0-1.2.33c-.27-1.78-1.8-3.15-3.66-3.15-.49 0-.96.1-1.38.29-.44-1.37-1.72-2.38-3.23-2.38-1.01 0-1.91.44-2.52 1.15V16.34c0 .1.08.18.18.18h11.89c1.17 0 2.11-.95 2.11-2.12 0-.61-.26-1.16-.69-1.54z"/></svg>`
    }
];

const subMenus = {
    all: [{ name: '미국 음악 Top 10', value: 'youtube_us_songs' }],
    // 유튜브 서브메뉴를 4개로 세분화
    youtube: [
        { name: '국내 음악 Top 10', value: 'youtube_kr_songs' },
        { name: '국내 영상 Top 10', value: 'youtube_kr_trend' },
        { name: '미국 음악 Top 10', value: 'youtube_us_songs' },
        { name: '미국 음악 Top 10', value: 'youtube_us_trend' }
    ],
    spotify: [{ name: 'Top 50 - Global', value: 'spotify' }],
    soundcloud: [{ name: 'New & Hot', value: 'soundcloud' }]
};

// 탭이 바뀔 때 기본 서브메뉴 설정
watch(currentTab, (newTab) => {
    if (newTab === 'youtube') {
        currentSubMenu.value = 'youtube_kr_songs'; // 유튜브 클릭 시 한국 음악이 기본
    } else {
        currentSubMenu.value = subMenus[newTab][0].name;
    }
});

// 실데이터가 없으면 보여줄 세련된 더미 데이터 생성기
const getSafeCharts = computed(() => {
    const safeData = { ...props.charts };

    // 1. 기본값
    let youtubeTargetKey = 'youtube_us_songs'; 
    
    if (currentTab.value === 'youtube') {
        youtubeTargetKey = currentSubMenu.value; // 유튜브 탭에선 사용자가 선택한 서브메뉴(kr_songs, us_trend 등)
    }

    // 결정된 실데이터를 'youtube' 키에 주입
    if (safeData[youtubeTargetKey] && safeData[youtubeTargetKey].length > 0) {
        safeData['youtube'] = safeData[youtubeTargetKey];
    }

    // 2. 나머지 플랫폼 더미 데이터 처리
    platforms.forEach(p => {
        if (p.value !== 'all' && (!safeData[p.value] || safeData[p.value].length === 0)) {
            safeData[p.value] = Array.from({ length: 5 }, (_, i) => ({
                rank: i + 1,
                title: `${p.name} 데이터 준비 중 #${i + 1}`,
                artist: "Artist",
                album_cover_url: null
            }));
        }
    });

    return safeData;
});
const setTab = (tab) => {
    currentTab.value = tab;
};
</script>

<template>
    <Head title="핫트렌드" />
    <AuthenticatedLayout>
        <div class="py-12 bg-[#F8FAFC] min-h-screen">
            <div class="max-w-6xl mx-auto px-6">
                
                <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-black rounded uppercase tracking-wider">Trend Hunter</span>
                        </div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic">Hot Trends</h1>
                        <p class="text-gray-400 font-bold mt-1 uppercase tracking-widest text-[10px]">Discover your next remix material across platforms.</p>
                    </div>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-2 flex items-center gap-1 overflow-x-auto no-scrollbar border-b border-gray-50">
                                <button v-for="platform in platforms" :key="platform.value" @click="setTab(platform.value)"
                                    class="px-5 py-2.5 rounded-2xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase tracking-tighter"
                                    :class="[currentTab === platform.value ? `${platform.color} text-white shadow-lg` : 'text-gray-400 hover:bg-gray-50']">
                                    {{ platform.name }}
                                </button>
                            </div>
                            <div class="px-6 py-4 flex items-center gap-3 overflow-x-auto no-scrollbar">
                                <button v-for="sub in subMenus[currentTab]" :key="sub.value" @click="currentSubMenu = sub.value"
                                    class="text-[11px] font-black uppercase tracking-widest transition-all"
                                    :class="currentSubMenu === sub.value ? 'text-indigo-600' : 'text-gray-300 hover:text-gray-500'">
                                    # {{ sub.name }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <template v-for="platform in platforms.filter(p => p.value !== 'all')" :key="platform.value">
                                <div v-if="currentTab === 'all' || currentTab === platform.value" 
                                     class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 group transition-all hover:shadow-xl hover:shadow-indigo-50/50">
                                    
                                    <div class="flex items-center justify-between mb-8">
                                        <div class="flex items-center gap-4">
                                            <div :class="platform.color" class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-xl">
                                                <div v-html="platform.icon" class="w-6 h-6 flex items-center justify-center"></div>
                                            </div>
                                            <div>
                                                <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-none italic uppercase">{{ platform.name }}</h2>
                                                <span class="text-[10px] font-black uppercase tracking-widest mt-1 block italic opacity-70" :style="{ color: platform.brandColor }">
                                                    {{ currentSubMenu }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-gray-50 rounded-full text-[9px] font-black text-gray-400 uppercase tracking-widest">Trending Now</span>
                                    </div>

                                    <div class="divide-y divide-gray-50">
                                        <div v-for="song in getSafeCharts[platform.value]" :key="song.rank" 
                                            class="flex items-center gap-6 py-4 hover:bg-indigo-50/20 transition-all rounded-2xl px-4 -mx-4 group/item cursor-pointer">
                                            
                                            <span class="text-2xl font-black italic text-gray-100 group-hover/item:transition-colors w-8"
                                                :style="{ '--hover-color': platform.brandColor }" 
                                                :class="`group-hover/item:text-[var(--hover-color)]` ">
                                                {{ String(song.rank).padStart(2, '0') }}
                                            </span>

                                            <div v-if="song.album_cover_url" class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 shadow-sm border border-gray-100">
                                                <img :src="song.album_cover_url" class="w-full h-full object-cover" alt="cover">
                                            </div>
                                            <div v-else class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center text-gray-200">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                                            </div>

                                            <div class="flex-grow min-w-0">
                                                <h4 class="font-black text-gray-900 leading-tight truncate group-hover/item:text-indigo-600 transition-colors">
                                                    {{ song.title }}
                                                </h4>
                                                <p class="text-[11px] text-gray-400 font-bold uppercase mt-0.5">
                                                    {{ song.artist }}
                                                </p>
                                            </div>

                                            <Link :href="route('mixmatch')" class="w-10 h-10 rounded-xl border border-gray-100 flex items-center justify-center text-gray-300 hover:bg-indigo-600 hover:text-white hover:border-transparent transition-all shadow-sm bg-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <aside class="space-y-8">
                        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
                            <div class="relative z-10">
                                <h3 class="font-black text-xs uppercase tracking-[0.2em] text-indigo-400 mb-6 italic">Mixer's Lab</h3>
                                <p class="text-sm font-black leading-snug mb-6">마음에 드는 트렌드 곡을 <br/>찾으셨나요? <br/>지금 바로 리믹스 배틀에 <br/>추가해보세요.</p>
                                <Link :href="route('remixes.index')" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest border-b-2 border-indigo-500 pb-1 hover:text-indigo-400 transition-colors">
                                    Go to Remixes →
                                </Link>
                            </div>
                            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-700">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3v18M8 7v10M4 11v2M16 7v10M20 11v2" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                            <h3 class="font-black text-gray-900 text-[10px] uppercase tracking-widest mb-6 border-b pb-4 italic">Trending Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="tag in ['#K-POP', '#EDM', '#ILLIT', '#NewJeans', '#Remix', '#TWS']" :key="tag" 
                                      class="px-3 py-1.5 bg-gray-50 rounded-lg text-[9px] font-black text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 cursor-pointer transition-all">
                                    {{ tag }}
                                </span>
                            </div>
                        </div>
                    </aside>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>