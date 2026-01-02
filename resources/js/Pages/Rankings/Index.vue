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
        replace:true
    });
};

// 모달 관련
const selectedRemix = ref(null);
const isModalOpen = ref(false);

// 유튜브 ID추출 함수
const getYoutubeId = (url) => {
    if (!url) return '';
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : '';
}

// 모달 열기
const openVideo = (data) => { // 인자 이름을 data로 바꿔서 혼동을 방지해 봅시다
    
    selectedRemix.value = data; // .ref (X) -> .value (O)
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
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">실시간 리믹스 순위 Top 10</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="ml-2 mb-6 flex space-x-2">
                    <button
                        @click="changeSort('votes')"
                        :class="currentSort === 'votes' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border'"
                        class="px-4 py-2 rounded-full font-medium transition shadow-sm"
                    >
                        🔥 좋아요순
                    </button>

                    <button
                        @click="changeSort('views')"
                        :class="currentSort === 'votes' ? 'bg-white text-gray-700 border' : 'bg-indigo-600 text-white'"
                        class="px-4 py-2 rounded-full font-medium transition shadow-sm"
                    >
                        👀 조회순
                    </button>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 px-4">순위</th>
                                    <th class="py-2 px-4">곡 정보</th>
                                    <th class="py-2 px-4 text-right">
                                        {{ currentSort === 'views' ? '조회수' : '좋아요 수' }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(remix, index) in rankings" :key="remix.id" class="border-b hover:bg-gray-50 cursor-pointer" @click="openVideo(remix)">
                                    <td class="p-4 font-bold text-xl text-gray-500">{{ index + 1 }}</td>
                                    <td class="p-4">
                                        <div class="font-bold text-lg text-indigo-600 hover:underline">{{ remix.title }}</div>
                                        <div class="text-sm text-gray-500">{{ remix.music_track?.artist }}-{{ remix.music_track?.title }} | {{ remix.genre?.name }}</div>
                                    </td>
                                    <td class="p-4 text-right text-red-500 font-bold">
                                        {{ currentSort === 'views' ? remix.view_count : remix.vote_count }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.self="closeModal">
            <div class="relative w-full max-w-4xl bg-black rounded-lg overflow-hidden shadow-2xl">
                <button @click="closeModal" class="absolute top-2 right-2 text-white text-3xl z-10 hover:text-gray-300">&times;</button>
                
                <div class="aspect-video">
                    <iframe 
                        v-if="selectedRemix"
                        :src="videoUrl"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
                
                <div class="p-4 bg-white">
                    <h3 class="text-xl font-bold">{{ selectedRemix?.title }}</h3>
                    <p class="text-gray-600">{{ selectedRemix?.artist }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>