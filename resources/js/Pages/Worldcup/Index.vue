<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onMounted, nextTick, ref, computed } from 'vue';

const props = defineProps({
    candidates: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const displayCandidates = computed(() => props.candidates || page.props.candidates || []);

let players = [null, null];
const isMuted = ref([true, true]);
const confirmingId = ref(null);

const initYouTubePlayers = () => {
    if (displayCandidates.value.length < 2) return;

    displayCandidates.value.forEach((remix, index) => {
        const elementId = `player-${index}`;
        if (document.getElementById(elementId)) {
            players[index] = new window.YT.Player(elementId, {
                videoId: remix.youtube_video_id,
                playerVars: {
                    'autoplay': 1,
                    'mute': 1,
                    'controls': 0,
                    'start': 60,
                    'modestbranding': 1,
                    'playsinline': 1
                },
                events: {
                    'onReady': (event) => {
                        event.target.playVideo();
                    },
                    'onStateChange': (event) => {
                        if (event.data === window.YT.PlayerState.PLAYING) {
                            setTimeout(() => {
                                if (event.target && typeof event.target.pauseVideo === 'function') {
                                    event.target.pauseVideo();
                                }
                            }, 50000); 
                        }
                    }
                }
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
    } else {
        initYouTubePlayers();
    }
});

const askConfirm = (id) => {
    confirmingId.value = id;
};

const selectWinner = (remixId) => {
    router.post(route('worldcup.vote', { id: remixId }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            confirmingId.value = null;
        }
    });
};

// 스킵 기능: 단순히 페이지를 새로고침하여 새로운 랜덤 곡을 가져옴
const skipMatch = () => {
    router.get(route('worldcup'), {}, { preserveScroll: false });
};

const cancelConfirm = () => {
    confirmingId.value = null;
};

const toggleSound = (index) => {
    if (!players[index]) return;
    if (isMuted.value[index]) {
        players.forEach((player, i) => {
            if (player && typeof player.mute === 'function') {
                player.mute();
                isMuted.value[i] = true;
            }
        });
        players[index].unMute();
        players[index].setVolume(50);
        isMuted.value[index] = false;
    } else {
        players[index].mute();
        isMuted.value[index] = true;
    }
};
</script>

<template>
    <Head title="월드컵" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                월드컵
            </h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)] flex flex-col items-center justify-center">
            <div class="max-w-[1200px] mx-auto px-4 w-full relative">
                
                <div class="flex flex-row w-full gap-6 mb-12">
                    <div v-for="(remix, index) in displayCandidates" :key="remix.id" class="flex-1 min-w-0">
                        <div class="bg-white rounded-[2rem] overflow-hidden shadow-md border border-gray-200 flex flex-col h-full transition-all hover:shadow-xl duration-300">
                            
                            <div class="relative aspect-video bg-black overflow-hidden">
                                <div :id="`player-${index}`" class="w-full h-full pointer-events-none scale-[1.1]"></div>
                            </div>

                            <div class="p-8 text-center flex flex-col justify-between flex-grow">
                                <div class="mb-6">
                                    <h3 class="text-2xl font-black text-gray-900 mb-2 truncate">
                                        {{ remix.title }}
                                    </h3>
                                    <p class="text-gray-500 font-bold">
                                        {{ remix.music_track?.artist }} | {{ remix.genre?.name }}
                                    </p>
                                </div>

                                <div class="flex flex-row items-center justify-center gap-3 w-full min-h-[64px]">
                                    <template v-if="confirmingId !== remix.id">
                                        <button @click.stop="toggleSound(index)" 
                                                class="flex-1 h-16 rounded-2xl border-2 flex items-center justify-center transition-all duration-200"
                                                :class="isMuted[index] ? 'border-gray-200 bg-gray-50 text-gray-400' : 'border-indigo-200 bg-indigo-50 text-indigo-600 shadow-inner font-bold'">
                                            <svg v-if="isMuted[index]" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6" />
                                            </svg>
                                        </button>

                                        <button @click="askConfirm(remix.id)" 
                                                class="flex-1 h-16 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center shadow-lg transition-all active:scale-95">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template v-else>
                                        <button @click="cancelConfirm" 
                                                class="flex-1 h-16 rounded-2xl border-2 border-gray-300 bg-white text-gray-700 font-bold hover:bg-gray-50 transition-all">
                                            취소
                                        </button>
                                        <button @click="selectWinner(remix.id)" 
                                                class="flex-1 h-16 rounded-2xl bg-red-600 text-white font-black text-lg shadow-lg animate-pulse transition-all">
                                            확정!
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-6 mt-4">
                    <div class="relative flex items-center justify-center">
                        <div style="position: absolute; width: 80px; height: 80px; background: rgba(239, 68, 68, 0.15); filter: blur(15px); border-radius: 50%;"></div>
                        <div class="vs-circle" 
                             style="position: relative; width: 60px; height: 60px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Arial Black', sans-serif; font-size: 1.4rem; font-weight: 900; border: 4px solid white; box-shadow: 0 4px 15px rgba(185, 28, 28, 0.3); font-style: italic;">
                            VS
                        </div>
                    </div>

                    <button @click="skipMatch" 
                            class="px-8 py-3 rounded-full border border-gray-300 bg-white text-gray-400 font-bold hover:bg-gray-50 hover:text-gray-600 transition-all shadow-sm flex items-center gap-2 group">
                        <span class="text-sm px-1">스킵하기</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
.vs-circle {
    animation: vs-float 3s ease-in-out infinite;
}

@keyframes vs-float {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-8px) scale(1.05); }
}

:deep(iframe) {
    width: 100% !important;
    height: 100% !important;
    position: absolute;
    top: 0;
    left: 0;
}
</style>