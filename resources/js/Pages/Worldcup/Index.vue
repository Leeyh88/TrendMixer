<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { onMounted, nextTick, ref, computed, onBeforeUnmount } from 'vue';

const props = defineProps({
    candidates: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const displayCandidates = computed(() => props.candidates || page.props.candidates || []);

// 플레이어 상태 관리 (ref로 안전하게 관리)
const players = ref([null, null]);
const isMuted = ref([true, true]);
const confirmingId = ref(null);

const initYouTubePlayers = () => {
    if (!displayCandidates.value || displayCandidates.value.length < 2) return;

    displayCandidates.value.forEach((remix, index) => {
        const elementId = `player-${index}`;
        const el = document.getElementById(elementId);
        
        if (el) {
            // 기존 플레이어 파괴 (재진입 시 충돌 방지)
            if (players.value[index]) {
                try { players.value[index].destroy(); } catch (e) {}
            }

            players.value[index] = new window.YT.Player(elementId, {
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

onBeforeUnmount(() => {
    players.value.forEach(player => {
        if (player && player.destroy) player.destroy();
    });
});

const askConfirm = (id) => { confirmingId.value = id; };
const cancelConfirm = () => { confirmingId.value = null; };

const selectWinner = (remixId) => {
    if (confirmingId.value !== null) return;

    router.post(route('worldcup.vote', { id: remixId }), {}, {
        preserveState: false,
        preserveScroll: false,
        onStart: () => {
            // 클릭 즉시 피드백을 주기 위해 선택된 아이디 저장 (애니메이션용)
            confirmingId.value = remixId;
        },
        onSuccess: () => {
            // 서버에서 응답이 오면 스킵 기능처럼 페이지를 다시 불러와 다음 매칭 유도
            // 만약 서버 컨트롤러에서 Redirect를 해준다면 이 코드는 없어도 되지만,
            // 확실하게 하기 위해 추가할 수 있습니다.
            confirmingId.value = null;
        },
        onError: () => {
            confirmingId.value = null;
            alert('투표 처리 중 오류가 발생했습니다.');
        },
        onFinish: () => {
            confirmingId.value = null;
        }
    });
};

const skipMatch = () => {
    router.get(route('worldcup'), {}, { preserveScroll: false });
};

const toggleSound = (index) => {
    if (!players.value[index]) return;
    
    if (isMuted.value[index]) {
        // 다른 모든 플레이어 음소거 후 현재만 켬
        players.value.forEach((player, i) => {
            if (player && player.mute) {
                player.mute();
                isMuted.value[i] = true;
            }
        });
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
    <Head title="월드컵" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-[#F8FAFC] p-8 rounded-[2.5rem] shadow-sm border border-gray-200">
                    <header class="mb-12 flex items-end justify-between gap-6 px-4">
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tighter uppercase">WORLD CUP</h1>
                            <p class="text-gray-500 font-bold mt-1 sm:mt-2 uppercase tracking-widest text-[10px] sm:text-xs">DEFINE THE TREND</p>
                        </div>

                        <button @click="skipMatch" 
                                class="inline-flex items-center px-4 sm:px-8 py-2.5 bg-indigo-600 text-white rounded-2xl font-black text-[10px] sm:text-sm shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all group">
                            <span class="mr-2 uppercase">SKIP</span>
                            <svg xmlns="http://www.w3.org/2000/vue" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                    </header>

                    <div class="relative w-full">
                        <div class="vs-circle-container absolute left-1/2 top-[35%] sm:top-[40%] -translate-x-1/2 -translate-y-1/2 z-50 pointer-events-none">
                            <div class="relative flex items-center justify-center">
                                <div class="absolute w-16 h-16 sm:w-24 h-24 bg-red-500/20 blur-2xl rounded-full"></div>
                                <div class="vs-circle w-12 h-12 sm:w-20 sm:h-20 bg-gradient-to-br from-red-500 to-red-700 text-white rounded-full flex items-center justify-center font-black text-sm sm:text-2xl italic border-2 sm:border-4 border-white shadow-2xl">
                                    VS
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row gap-2 sm:gap-8 items-stretch overflow-hidden">
                            <div v-for="(remix, index) in displayCandidates" :key="remix.id" class="flex-1 min-w-0">
                                <div class="bg-white rounded-[1.5rem] sm:rounded-[2.5rem] overflow-hidden border border-gray-300 flex flex-col h-full transition-all duration-500 group">
                                    
                                    <div class="relative aspect-video bg-black overflow-hidden">
                                        <div :id="`player-${index}`" class="w-full h-full pointer-events-none scale-[1.05]"></div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                    </div>

                                    <div class="p-3 sm:p-10 text-center flex flex-col justify-between flex-grow">
                                        <div class="mb-4 sm:mb-8">
                                            <h3 class="text-sm sm:text-3xl font-black text-gray-900 mb-1 truncate">
                                                {{ remix.title }}
                                            </h3>
                                            <p class="text-[9px] sm:text-sm text-gray-400 font-bold truncate">
                                                {{ remix.music_track?.artist }}
                                            </p>
                                        </div>

                                      <div class="flex flex-row items-center justify-center gap-2 sm:gap-4 w-full mt-auto">
        
        <button @click.stop="toggleSound(index)" 
                class="flex-1 h-14 sm:h-16 rounded-xl sm:rounded-2xl border-2 transition-all duration-300 flex items-center justify-center group/sound"
                :class="isMuted[index] 
                    ? 'border-gray-100 bg-white text-gray-300 hover:border-orange-200 hover:text-orange-500' 
                    : 'border-orange-500 bg-orange-50 text-orange-600 shadow-inner'">
            <svg v-if="isMuted[index]" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.5a.75.75 0 01-.75-.75V9.75a.75.75 0 01.75-.75h2.25z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-8 sm:h-8 animate-pulse text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.5a.75.75 0 01-.75-.75V8.25a.75.75 0 01.75-.75h2.25z" />
            </svg>
        </button>

        <button @click="selectWinner(remix.id)" 
                :disabled="confirmingId !== null"
                class="flex-1 h-14 sm:h-16 rounded-xl sm:rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 text-white flex items-center justify-center gap-2 shadow-lg shadow-orange-200 hover:scale-105 active:scale-95 transition-all group/win relative overflow-hidden"
                :class="confirmingId === remix.id ? 'brightness-75 cursor-not-allowed' : ''">
            
            <template v-if="confirmingId !== remix.id">
                <span class="font-black text-xs sm:text-xl italic uppercase tracking-tighter">WINNER!</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-7 sm:h-7 group-hover/win:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                </svg>
            </template>
            
            <template v-else>
                <div class="w-6 h-6 border-4 border-white border-t-transparent rounded-full animate-spin"></div>
            </template>
        </button>
    </div>
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
<style scoped>
.vs-circle {
    animation: vs-float 3s ease-in-out infinite;
}

@keyframes vs-float {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-5px) scale(1.1) rotate(5deg); }
}

/* 좁은 화면에서도 VS 아이콘 위치를 안정적으로 고정 */
.vs-circle-container {
    transition: all 0.3s ease;
}

:deep(iframe) {
    width: 100% !important;
    height: 100% !important;
    position: absolute;
    top: 0;
    left: 0;
}
</style>