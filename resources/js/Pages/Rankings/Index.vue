<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    rankings: Array,
    currentSort: String
});

const changeSort = (sortType) => {
    router.get(route('rankings'), { sort: sortType }, {
        preserveState: true, 
        replace: true
    });
};

// 모달 관련
const selectedRemix = ref(null);
const isModalOpen = ref(false);

// 유튜브 ID 추출 함수
const getYoutubeId = (url) => {
    if (!url) return '';
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : '';
}

const openVideo = (data) => {
    selectedRemix.value = data;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedRemix.value = null;
}

const videoUrl = computed(() => {
    if (!selectedRemix.value) return '';
    const videoId = getYoutubeId(selectedRemix.value.youtube_url);
    return `https://www.youtube.com/embed/${videoId}?autoplay=1`;
});
</script>

<template>
    <Head title="실시간 순위" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-[#F8FAFC] p-8 rounded-[2.5rem] shadow-sm border border-gray-200">
                    <header class="mb-12">
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase">Remix Ranking</h1>
                        <p class="text-gray-500 font-bold mt-2 uppercase tracking-widest text-xs">Explore the most liked vibes.</p>
                    </header>

                    <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-gray-100 mb-10 flex gap-2">
                        <button @click="changeSort('votes')"
                            :class="currentSort === 'votes' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="flex-1 py-3 rounded-xl text-xs font-black transition-all uppercase">
                            Most Liked
                        </button>
                        <button @click="changeSort('views')"
                            :class="currentSort === 'views' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
                            class="flex-1 py-3 rounded-xl text-xs font-black transition-all uppercase">
                            Most Viewed
                        </button>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-[1.5rem] border border-gray-200">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="py-4 px-6 text-sm font-bold text-gray-400 uppercase">순위</th>
                                    <th class="py-4 px-6 text-sm font-bold text-gray-400 uppercase">곡 정보</th>
                                    <th class="hidden sm:table-cell py-4 px-6 text-right text-sm font-bold text-gray-400 uppercase">
                                        {{ currentSort === 'views' ? '조회수' : '좋아요' }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(remix, index) in rankings" :key="remix.id" 
                                    class="border-b border-gray-50 hover:bg-indigo-50/30 cursor-pointer transition-colors group" 
                                    @click="openVideo(remix)"
                                    :class="[
                                        index === 0 ? 'bg-yellow-50/20' : '',
                                        index === 1 ? 'bg-slate-50/20' : '',
                                        index === 2 ? 'bg-orange-50/20' : ''
                                    ]">
                                    <td class="p-6 text-center w-20">
                                        <span v-if="index === 0" class="text-3xl">🥇</span>
                                        <span v-else-if="index === 1" class="text-3xl">🥈</span>
                                        <span v-else-if="index === 2" class="text-3xl">🥉</span>
                                        <span v-else class="font-black text-xl text-gray-300 italic">#{{ index + 1 }}</span>
                                    </td>
                                    
                                    <td class="p-6">
                                        <div class="font-black text-lg text-gray-900 group-hover:text-indigo-600 transition-colors">
                                            {{ remix.title }}
                                        </div>
                                        <div class="text-sm text-gray-500 font-medium">
                                            {{ remix.music_track?.artist }} | {{ remix.genre?.name }}
                                        </div>
                                    </td>
                                    
                                    <td class="hidden sm:table-cell p-6 text-right font-black text-indigo-600 text-xl tabular-nums">
                                        {{ (currentSort === 'views' ? remix.view_count : remix.vote_count).toLocaleString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" @click.self="closeModal">
                    <div class="relative w-full max-w-4xl bg-white rounded-[2rem] overflow-hidden shadow-2xl">
                        <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-black text-3xl z-10">&times;</button>
                        <div class="aspect-video bg-black">
                            <iframe v-if="selectedRemix" :src="videoUrl" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                        <div class="p-6 bg-white border-t">
                            <h3 class="text-xl font-black text-gray-900">{{ selectedRemix?.title }}</h3>
                            <p class="text-gray-500 font-bold uppercase tracking-widest text-xs mt-1">{{ selectedRemix?.music_track?.artist }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>