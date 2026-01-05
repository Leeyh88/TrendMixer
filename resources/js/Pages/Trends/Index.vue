<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    charts: Object
});

const currentTab = ref('all');
// [추가] 세부 메뉴 상태 관리
const currentSubMenu = ref('top');

const platforms = [
    { name: 'All Platforms', value: 'all', color: 'bg-gray-900' },
    { name: 'Spotify', value: 'spotify', color: 'bg-[#1DB954]' },
    { name: 'YouTube', value: 'youtube', color: 'bg-[#FF0000]' },
    { name: 'SoundCloud', value: 'soundcloud', color: 'bg-[#FF5500]' },
];

// [추가] 플랫폼별 상세 메뉴 데이터
const subMenus = {
    all: [{ name: 'Global Trending', value: 'top' }],
    spotify: [
        { name: 'Top 50 - Global', value: 'top' },
        { name: 'Viral 50', value: 'viral' },
        { name: 'Top 50 - Korea', value: 'korea' }
    ],
    youtube: [
        { name: 'Trending Music', value: 'top' },
        { name: 'Popular MV', value: 'mv' },
        { name: 'New Releases', value: 'new' }
    ],
    soundcloud: [{ name: 'New & Hot', value: 'top' }]
};

// [추가] 플랫폼 변경 시 세부 메뉴를 첫 번째 항목으로 리셋
watch(currentTab, (newTab) => {
    currentSubMenu.value = subMenus[newTab][0].value;
});

const setTab = (tab) => {
    currentTab.value = tab;
};
</script>

<template>
    <Head title="핫트렌드" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-[#F8FAFC] p-8 rounded-[2.5rem] shadow-sm border border-gray-200">
                    <div class="max-w-7xl mx-auto">
                        <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                            <div>
                                <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Hot Trends</h1>
                                <p class="text-gray-500 font-bold mt-2 uppercase tracking-widest text-xs">DISCOVER YOUR NEXT REMIX MATERIAL</p>
                            </div>
                        </header>

                        <div class="bg-white p-4 rounded-t-[2rem] border-x border-t border-gray-100 flex flex-wrap items-center justify-start gap-4">
                            <div class="flex flex-wrap items-center gap-2 justify-start w-full">
                                <button 
                                    v-for="platform in platforms" 
                                    :key="platform.value"
                                    @click="setTab(platform.value)"
                                    class="px-5 py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase"
                                    :class="[
                                        currentTab === platform.value 
                                            ? `${platform.color} text-white shadow-md` 
                                            : 'bg-gray-50 text-gray-500 hover:bg-gray-100'
                                    ]"
                                >
                                    {{ platform.name }}
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50/50 p-3 rounded-b-[2rem] border-x border-b border-gray-100 mb-10 flex items-center gap-2 overflow-x-auto no-scrollbar">
                            <button 
                                v-for="sub in subMenus[currentTab]" 
                                :key="sub.value"
                                @click="currentSubMenu = sub.value"
                                class="px-4 py-1.5 rounded-full text-[10px] font-bold transition-all whitespace-nowrap border"
                                :class="currentSubMenu === sub.value 
                                    ? 'bg-white border-gray-200 text-gray-900 shadow-sm' 
                                    : 'bg-transparent border-transparent text-gray-400 hover:text-gray-600'"
                            >
                                # {{ sub.name }}
                            </button>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div v-if="currentTab === 'all' || currentTab === 'spotify'" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 transition-all">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[#1DB954] rounded-xl flex items-center justify-center text-white shadow-lg shadow-green-100 font-black">S</div>
                                        <div>
                                            <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-none">Spotify</h2>
                                            <span class="text-[10px] font-bold text-[#1DB954] uppercase tracking-tighter">{{ currentSubMenu }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Daily Update</span>
                                </div>
                                <div class="space-y-4">
                                    <div v-for="song in charts.spotify" :key="song.rank" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all group border border-transparent hover:border-gray-100">
                                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#1DB954] transition-colors w-8 italic">{{ song.rank }}</span>
                                        <div class="flex-grow">
                                            <h4 class="font-black text-gray-900 leading-tight">{{ song.title }}</h4>
                                            <p class="text-sm text-gray-500 font-medium">{{ song.artist }}</p>
                                        </div>
                                        <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-[#1DB954] hover:text-white hover:border-transparent transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="currentTab === 'all' || currentTab === 'youtube'" class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 transition-all">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-[#FF0000] rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100 font-black">Y</div>
                                        <div>
                                            <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-none">YouTube</h2>
                                            <span class="text-[10px] font-bold text-[#FF0000] uppercase tracking-tighter">{{ currentSubMenu }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Trending Now</span>
                                </div>
                                <div class="space-y-4">
                                    <div v-for="song in charts.youtube" :key="song.rank" class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all group border border-transparent hover:border-gray-100">
                                        <span class="text-2xl font-black text-gray-200 group-hover:text-[#FF0000] transition-colors w-8 italic">{{ song.rank }}</span>
                                        <div class="flex-grow">
                                            <h4 class="font-black text-gray-900 leading-tight">{{ song.title }}</h4>
                                            <p class="text-sm text-gray-500 font-medium">{{ song.artist }}</p>
                                        </div>
                                        <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-[#FF0000] hover:text-white hover:border-transparent transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>