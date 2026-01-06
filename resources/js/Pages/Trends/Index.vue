<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    charts: Object
});

const currentTab = ref('all');
const currentSubMenu = ref('top');

const platforms = [
    { name: 'All Trends', value: 'all', color: 'bg-gray-900', icon: 'M' },
    { name: 'Spotify', value: 'spotify', color: 'bg-[#1DB954]', icon: 'S' },
    { name: 'YouTube', value: 'youtube', color: 'bg-[#FF0000]', icon: 'Y' },
    { name: 'SoundCloud', value: 'soundcloud', color: 'bg-[#FF5500]', icon: 'SC' },
];

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
                                <button 
                                    v-for="platform in platforms" 
                                    :key="platform.value"
                                    @click="setTab(platform.value)"
                                    class="px-5 py-2.5 rounded-2xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase tracking-tighter"
                                    :class="[
                                        currentTab === platform.value 
                                            ? `${platform.color} text-white shadow-lg` 
                                            : 'text-gray-400 hover:bg-gray-50'
                                    ]"
                                >
                                    {{ platform.name }}
                                </button>
                            </div>
                            
                            <div class="px-6 py-4 flex items-center gap-3 overflow-x-auto no-scrollbar">
                                <button 
                                    v-for="sub in subMenus[currentTab]" 
                                    :key="sub.value"
                                    @click="currentSubMenu = sub.value"
                                    class="text-[11px] font-black uppercase tracking-widest transition-all"
                                    :class="currentSubMenu === sub.value 
                                        ? 'text-indigo-600' 
                                        : 'text-gray-300 hover:text-gray-500'"
                                >
                                    # {{ sub.name }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div v-if="currentTab === 'all' || currentTab === 'spotify'" 
                                 class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 group">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-[#1DB954] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-green-100 font-black text-xl italic">S</div>
                                        <div>
                                            <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-none italic uppercase">Spotify</h2>
                                            <span class="text-[10px] font-black text-[#1DB954] uppercase tracking-widest mt-1 block italic opacity-70">{{ currentSubMenu }}</span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-50 rounded-full text-[9px] font-black text-gray-400 uppercase tracking-widest">Global Top 50</span>
                                </div>

                                <div class="divide-y divide-gray-50">
                                    <div v-for="song in charts.spotify" :key="song.rank" 
                                         class="flex items-center gap-6 py-4 hover:bg-indigo-50/20 transition-all rounded-2xl px-4 -mx-4 group/item cursor-pointer">
                                        <span class="text-2xl font-black italic text-gray-100 group-hover/item:text-[#1DB954] transition-colors w-8">0{{ song.rank }}</span>
                                        <div class="flex-grow min-w-0">
                                            <h4 class="font-black text-gray-900 leading-tight truncate group-hover/item:text-indigo-600 transition-colors">{{ song.title }}</h4>
                                            <p class="text-[11px] text-gray-400 font-bold uppercase mt-0.5">{{ song.artist }}</p>
                                        </div>
                                        <Link :href="route('worldcup.index')" class="w-10 h-10 rounded-xl border border-gray-100 flex items-center justify-center text-gray-300 hover:bg-indigo-600 hover:text-white hover:border-transparent transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div v-if="currentTab === 'all' || currentTab === 'youtube'" 
                                 class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 group">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-[#FF0000] rounded-2xl flex items-center justify-center text-white shadow-xl shadow-red-100 font-black text-xl italic">Y</div>
                                        <div>
                                            <h2 class="text-2xl font-black text-gray-900 tracking-tight leading-none italic uppercase">YouTube</h2>
                                            <span class="text-[10px] font-black text-[#FF0000] uppercase tracking-widest mt-1 block italic opacity-70">{{ currentSubMenu }}</span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-50 rounded-full text-[9px] font-black text-gray-400 uppercase tracking-widest">Trending</span>
                                </div>

                                <div class="divide-y divide-gray-50">
                                    <div v-for="song in charts.youtube" :key="song.rank" 
                                         class="flex items-center gap-6 py-4 hover:bg-indigo-50/20 transition-all rounded-2xl px-4 -mx-4 group/item cursor-pointer">
                                        <span class="text-2xl font-black italic text-gray-100 group-hover/item:text-[#FF0000] transition-colors w-8">0{{ song.rank }}</span>
                                        <div class="flex-grow min-w-0">
                                            <h4 class="font-black text-gray-900 leading-tight truncate group-hover/item:text-indigo-600 transition-colors">{{ song.title }}</h4>
                                            <p class="text-[11px] text-gray-400 font-bold uppercase mt-0.5">{{ song.artist }}</p>
                                        </div>
                                        <Link :href="route('worldcup.index')" class="w-10 h-10 rounded-xl border border-gray-100 flex items-center justify-center text-gray-300 hover:bg-indigo-600 hover:text-white hover:border-transparent transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
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