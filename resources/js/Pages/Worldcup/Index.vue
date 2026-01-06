<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onMounted, nextTick, ref, computed, onBeforeUnmount } from 'vue';

const props = defineProps({
    candidates: { type: Array, default: () => [] }
});

const page = usePage();
const displayCandidates = computed(() => props.candidates || page.props.candidates || []);

const players = ref([null, null]);
const isMuted = ref([true, true]);
const confirmingId = ref(null);

const initYouTubePlayers = () => {
    if (!displayCandidates.value || displayCandidates.value.length < 2) return;
    displayCandidates.value.forEach((remix, index) => {
        const elementId = `player-${index}`;
        if (document.getElementById(elementId)) {
            if (players.value[index]) {
                try { players.value[index].destroy(); } catch (e) {}
            }
            players.value[index] = new window.YT.Player(elementId, {
                videoId: remix.youtube_video_id,
                playerVars: { 'autoplay': 1, 'mute': 1, 'controls': 0, 'start': 60, 'modestbranding': 1, 'playsinline': 1 },
                events: { 'onReady': (event) => event.target.playVideo() }
            });
        }
    });
};

onMounted(async () => {
    await nextTick();
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        window.onYouTubeIframeAPIReady = initYouTubePlayers;
    } else { initYouTubePlayers(); }
});

onBeforeUnmount(() => {
    players.value.forEach(player => { if (player && player.destroy) player.destroy(); });
});

const selectWinner = (remixId) => {
    if (confirmingId.value !== null) return;
    router.post(route('worldcup.vote', { id: remixId }), {}, {
        preserveState: false,
        onStart: () => { confirmingId.value = remixId; },
        onError: () => { confirmingId.value = null; alert('오류가 발생했습니다.'); },
        onFinish: () => { confirmingId.value = null; }
    });
};

const skipMatch = () => { router.get(route('worldcup'), {}, { preserveScroll: false }); };

const toggleSound = (index) => {
    if (!players.value[index]) return;
    if (isMuted.value[index]) {
        players.value.forEach((p, i) => { if (p && p.mute) { p.mute(); isMuted.value[i] = true; } });
        players.value[index].unMute();
        players.value[index].setVolume(50);
        isMuted.value[index] = false;
    } else {
        players.value[index].mute();
        isMuted.value[index] = true;
    }
};
</script>

<template>
    <Head title="CHART CLASH" />

    <AuthenticatedLayout>
        <div class="py-10 bg-[#0F1115] min-h-screen flex items-center">
            <div class="max-w-6xl mx-auto px-6 w-full">
                
                <header class="mb-10 flex items-center justify-between px-2">
                    <div class="flex items-center gap-4">
                        <div class="w-1 h-10 bg-indigo-600 rounded-full"></div>
                        <div>
                            <h1 class="text-3xl font-black text-white tracking-tighter uppercase italic">Chart Clash</h1>
                            <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-[10px]">Round of 32 • Who is the trend?</p>
                        </div>
                    </div>

                    <button @click="skipMatch" class="group flex items-center gap-2 text-gray-500 hover:text-white transition-colors">
                        <span class="text-[10px] font-black uppercase tracking-widest">Skip Match</span>
                        <div class="w-8 h-8 rounded-full border border-gray-800 flex items-center justify-center group-hover:bg-gray-800 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7" /></svg>
                        </div>
                    </button>
                </header>

                <div class="relative">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-50 pointer-events-none">
                        <div class="relative flex items-center justify-center">
                            <div class="absolute w-24 h-24 bg-indigo-600/30 blur-[40px] rounded-full animate-pulse"></div>
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white text-black rounded-full flex items-center justify-center font-black text-xl sm:text-2xl italic shadow-[0_0_30px_rgba(255,255,255,0.2)] border-4 border-[#0F1115]">
                                VS
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div v-for="(remix, index) in displayCandidates" :key="remix.id" 
                             class="relative group overflow-hidden rounded-[2.5rem] bg-[#1A1D23] border border-gray-800 hover:border-indigo-500/50 transition-all duration-500 shadow-2xl">
                            
                            <div class="relative aspect-video bg-black overflow-hidden">
                                <div :id="`player-${index}`" class="w-full h-full pointer-events-none scale-[1.02]"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-[#1A1D23] via-transparent to-black/20"></div>
                                
                                <button @click.stop="toggleSound(index)" 
                                        class="absolute bottom-6 right-6 w-12 h-12 rounded-full backdrop-blur-md flex items-center justify-center transition-all z-20 shadow-xl"
                                        :class="isMuted[index] ? 'bg-white/10 text-white/40 border border-white/10' : 'bg-indigo-600 text-white border border-indigo-400'">
                                    <svg v-if="isMuted[index]" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.5a.75.75 0 01-.75-.75V9.75a.75.75 0 01.75-.75h2.25z" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.5a.75.75 0 01-.75-.75V8.25a.75.75 0 01.75-.75h2.25z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-8 sm:p-10 text-center relative">
                                <div class="mb-8">
                                    <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-2 block">Candidate 0{{ index + 1 }}</span>
                                    <h3 class="text-xl sm:text-3xl font-black text-white mb-2 tracking-tighter truncate leading-tight group-hover:text-indigo-400 transition-colors">
                                        {{ remix.title }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-500 font-bold uppercase tracking-widest">
                                        {{ remix.music_track?.artist }}
                                    </p>
                                </div>

                                <button @click="selectWinner(remix.id)" 
                                        :disabled="confirmingId !== null"
                                        class="w-full py-5 rounded-2xl font-black text-sm sm:text-lg italic tracking-tighter uppercase transition-all flex items-center justify-center gap-3 relative overflow-hidden"
                                        :class="confirmingId === remix.id 
                                            ? 'bg-indigo-600/20 text-indigo-400 cursor-not-allowed' 
                                            : 'bg-white text-black hover:bg-indigo-600 hover:text-white hover:scale-[1.02] active:scale-95 shadow-xl shadow-black/20'">
                                    
                                    <template v-if="confirmingId !== remix.id">
                                        <span>Pick This Vibe</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                    </template>
                                    
                                    <template v-else>
                                        <div class="w-5 h-5 border-3 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 max-w-md mx-auto">
                    <div class="flex items-center justify-between mb-2 px-1">
                        <span class="text-[9px] font-black text-gray-600 uppercase tracking-widest">Progress</span>
                        <span class="text-[9px] font-black text-indigo-500 uppercase">4 / 16</span>
                    </div>
                    <div class="w-full bg-gray-900 h-1 rounded-full overflow-hidden">
                        <div class="bg-indigo-600 h-full w-[25%]"></div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* VS 뱃지 부유 애니메이션 */
.vs-circle {
    animation: vs-float 3s ease-in-out infinite;
}
@keyframes vs-float {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-5px) scale(1.05); }
}

:deep(iframe) {
    width: 100% !important;
    height: 100% !important;
    position: absolute;
    top: 0;
    left: 0;
}
</style>