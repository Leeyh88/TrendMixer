<script setup>
// 1. 반드시 Import문이 있어야 합니다.
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Props 정의 (auth 정보가 넘어오는지 확인)
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    // 컨트롤러에서 넘겨주는 데이터들
    trends: Array,
    rankings: Array,
    posts: Array,
});
</script>

<template>
    <Head title="TrendMixer" />
    <AuthenticatedLayout>
        <div class="py-10 bg-[#FBFBFE] min-h-screen">
            <div class="max-w-6xl mx-auto px-6">
                
                <header class="mb-12 relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-800 to-black p-10 sm:p-16 rounded-[3rem] text-white shadow-2xl">
                    <div class="relative z-10 max-w-2xl">
                        <span class="inline-block px-4 py-1.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6">
                            Start Your Remix Journey
                        </span>
                        <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-[1.1] mb-6">
                            당신의 음악적 감각을 <br/>
                            <span class="text-indigo-400 italic">증명</span> 하세요
                        </h1>
                        <p class="text-indigo-100 text-lg font-medium mb-10 leading-relaxed opacity-80">
                            지금 가입하고 최신 트렌드 곡들을 리믹스하여 <br class="hidden sm:block"/> 순위에 이름을 올리세요.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <Link :href="route('register')" class="px-8 py-4 bg-white text-indigo-900 rounded-2xl font-black text-sm hover:bg-indigo-50 transition-all hover:-translate-y-1 shadow-lg">
                                시작하기 (가입)
                            </Link>
                            <Link :href="route('login')" class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white rounded-2xl font-black text-sm hover:bg-white/20 transition-all">
                                로그인
                            </Link>
                        </div>
                    </div>
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500 rounded-full blur-[100px] opacity-20"></div>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    
                    <div class="lg:col-span-2 space-y-12">
                        <section>
                            <div class="flex items-center justify-between mb-8 px-2">
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic">Hot Trends</h2>
                                    <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1">지금 가장 핫한 리믹스 재료</p>
                                </div>
                                <Link :href="route('trends')" class="text-[10px] font-black text-gray-400 border-b-2 border-gray-100 pb-1 hover:text-indigo-600 hover:border-indigo-600 transition-all uppercase tracking-widest">
                                    Full Trends
                                </Link>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div v-for="n in 4" :key="n" class="group bg-white rounded-[2.5rem] p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer">
                                    <div class="aspect-video bg-gray-100 rounded-[1.8rem] overflow-hidden mb-5">
                                        <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800&q=80" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                    </div>
                                    <div class="px-2">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded uppercase">Remix Choice</span>
                                        </div>
                                        <h3 class="text-lg font-black text-gray-900 truncate tracking-tight">Sample Remix Title {{ n }}</h3>
                                        <p class="text-xs text-gray-400 font-bold uppercase mt-1">Artist Name</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <div class="flex items-center justify-between mb-8 px-2">
                                <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic">Latest Feedback</h2>
                            </div>
                            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm divide-y divide-gray-50 overflow-hidden">
                                <div v-for="n in 5" :key="n" class="p-6 hover:bg-gray-50 transition-colors flex items-center justify-between group cursor-pointer">
                                    <div class="flex items-center gap-5">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-400 font-black text-xs uppercase">R</div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">리믹스 월드컵 결과가 너무 충격적이네요.. {{ n }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium mt-1 uppercase tracking-wider">자유게시판 • 2분 전</p>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside>
                        <div class="sticky top-10">
                            <div class="flex items-center justify-between mb-8 px-2">
                                <h2 class="text-2xl font-black text-gray-900 tracking-tighter uppercase italic">Leaderboard</h2>
                            </div>
                            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                                <div v-for="n in 5" :key="n" class="flex items-center gap-5 mb-8 last:mb-0 group cursor-pointer">
                                    <div class="relative">
                                        <span class="text-2xl font-black italic transition-colors" :class="n < 4 ? 'text-indigo-600' : 'text-gray-100 group-hover:text-gray-200'">{{ n }}</span>
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="text-sm font-black text-gray-900 truncate">Hype Boy Remix</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                                                <div class="bg-indigo-500 h-full" :style="{ width: (100 - (n*15)) + '%' }"></div>
                                            </div>
                                            <span class="text-[9px] font-black text-gray-300 uppercase italic">{{ 1000 - (n*100) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <Link :href="route('rankings')" class="mt-10 block w-full py-4 bg-gray-900 text-center rounded-2xl text-xs font-black text-white uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-lg shadow-gray-200">
                                    Enter The Chart
                                </Link>
                            </div>
                        </div>
                    </aside>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>