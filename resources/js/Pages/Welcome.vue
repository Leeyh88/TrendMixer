<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// 컨트롤러에서 넘겨주는 데이터 정의
const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    hotTrends: Array,
    latestFeedback: Array,
    leaderboard: Array
});

// --- 상태 관리 (State) ---
const isVideoModalOpen = ref(false);
const currentVideoId = ref('');

// --- 계산된 속성 (Computed) ---
// 리더보드 상위 5개의 좋아요 총합 계산
const totalLeaderboardVotes = computed(() => {
    return props.leaderboard.reduce((acc, item) => acc + (item.vote_count || 0), 0);
});

// --- 함수 (Methods) ---
// 1. 카테고리 한글 변환
const getCategoryName = (category) => {
    const names = { general: '자유', recommend: '추천', question: '질문' };
    return names[category] || category;
};

// 2. 날짜 포맷
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};

// 3. 트렌드 클릭 시 리믹스 목록으로 이동 (필터 적용)
const goToRemixList = (title, artist) => {
    router.get(route('remixes.index'), {
        track: `${title}|${artist}`,
        sort: 'popular'
    });
};

// 4. 리더보드 게이지 비중(%) 계산
const getVoteWidth = (count) => {
    if (totalLeaderboardVotes.value === 0) return 0;
    return (count / totalLeaderboardVotes.value) * 100;
};

// 5. 유튜브 모달 제어
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
    <Head title="홈" />
    <AuthenticatedLayout>
        <div class="py-10 bg-[#FBFBFE] min-h-screen">
            <div class="max-w-6xl mx-auto px-6">
                
                <header class="mb-12 relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-800 to-black p-10 sm:p-16 rounded-[3rem] text-white shadow-2xl">
                    <div class="relative z-10 max-w-2xl">
                        <span v-if="!$page.props.auth.user" class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                            Start Your Remix Journey
                        </span>
                        <span v-else class="inline-block px-4 py-1.5 bg-indigo-500/30 backdrop-blur-md border border-indigo-400/30 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                            Welcome Back, {{ $page.props.auth.user.name }}!
                        </span>

                        <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-[1.1] mb-6">
                            당신의 음악적 감각을 <br/>
                            <span class="text-indigo-400 italic">증명</span> 하세요
                        </h1>

                        <div class="flex flex-wrap gap-4 mt-10">
                            <template v-if="!$page.props.auth.user">
                                <Link :href="route('register')" class="px-8 py-4 bg-white text-indigo-900 rounded-2xl font-black text-sm hover:bg-indigo-50 transition-all shadow-lg">시작하기</Link>
                                <Link :href="route('login')" class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white rounded-2xl font-black text-sm hover:bg-white/20 transition-all">로그인</Link>
                            </template>
                            <template v-else>
                                <Link :href="route('remixes.create')" class="px-8 py-4 bg-white text-indigo-900 rounded-2xl font-black text-sm hover:bg-indigo-50 transition-all shadow-lg flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4" /></svg>
                                    리믹스 업로드
                                </Link>
                            </template>
                        </div>
                    </div>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <div class="lg:col-span-2 space-y-12">
                        <section>
                            <div class="flex items-center justify-between mb-8 px-2">
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic">Hot Trends</h2>
                                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1">가장 핫한 리믹스 재료</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div v-for="(trend, index) in hotTrends" :key="index" 
                                     @click="goToRemixList(trend.track_title, trend.artist_name)"
                                     class="group bg-white rounded-[2.5rem] p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer">
                                    <div class="aspect-video bg-gray-100 rounded-[1.8rem] overflow-hidden mb-5">
                                        <img :src="trend.album_cover_url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                    </div>
                                    <div class="px-2">
                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded uppercase mb-2 inline-block">
                                            {{ trend.total }} Remixes
                                        </span>
                                        <h3 class="text-lg font-black text-gray-900 truncate tracking-tight">{{ trend.track_title }}</h3>
                                        <p class="text-xs text-gray-400 font-bold uppercase mt-1">{{ trend.artist_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="flex items-center justify-between mb-8 px-2">
                                <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic">Latest Feedback</h2>
                            </div>
                            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm divide-y divide-gray-50 overflow-hidden">
                                <Link v-for="post in latestFeedback" :key="post.id" :href="route('posts.show', post.id)" 
                                      class="p-6 hover:bg-gray-50 transition-colors flex items-center justify-between group">
                                    <div class="flex items-center gap-5">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-400 font-black text-xs uppercase italic border border-indigo-100">
                                            {{ post.user.name[0] }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ post.title }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium mt-1 uppercase tracking-wider">
                                                {{ getCategoryName(post.category) }} • {{ post.user.name }} • {{ formatDate(post.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M9 5l7 7-7 7" /></svg>
                                </Link>
                            </div>
                        </section>
                    </div>

                    <aside>
                        <div class="sticky top-10">
                            <div class="flex items-center justify-between mb-8 px-2">
                                <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic">Leaderboard</h2>
                            </div>
                            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                                <div v-for="(remix, index) in leaderboard" :key="remix.id" 
                                     @click="openVideo(remix.youtube_video_id)"
                                     class="flex items-center gap-5 mb-8 last:mb-0 group cursor-pointer hover:translate-x-1 transition-all">
                                    <div class="relative">
                                        <span class="text-2xl font-black italic transition-colors w-6 inline-block text-center" 
                                              :class="index < 3 ? 'text-indigo-600' : 'text-gray-100 group-hover:text-gray-200'">
                                            {{ index + 1 }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="text-sm font-black text-gray-900 truncate leading-tight">{{ remix.title }}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-indigo-500 h-full transition-all duration-1000" 
                                                     :style="{ width: getVoteWidth(remix.vote_count) + '%' }"></div>
                                            </div>
                                            <span class="text-[9px] font-black text-gray-300 uppercase italic tabular-nums">
                                                {{ remix.vote_count }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <Link :href="route('remixes.index')" class="mt-10 block w-full py-4 bg-gray-900 text-center rounded-2xl text-xs font-black text-white uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-lg shadow-gray-200">
                                    Enter The Chart
                                </Link>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <div v-if="isVideoModalOpen && currentVideoId" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeVideo"></div>
            <div class="relative w-full max-w-4xl aspect-video bg-black rounded-[2.5rem] overflow-hidden shadow-2xl animate-in zoom-in duration-300">
                <iframe 
                    class="w-full h-full" 
                    :src="`https://www.youtube.com/embed/${currentVideoId}?autoplay=1`" 
                    frameborder="0" allow="autoplay; encrypted-media" allowfullscreen
                ></iframe>
                <button @click="closeVideo" class="absolute -top-12 right-0 text-white flex items-center gap-2 font-black hover:text-indigo-400 transition-all uppercase tracking-widest text-xs">
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