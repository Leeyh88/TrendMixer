<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    posts: Object,
    filters: Object,
});

// 검색어 상태 관리
const searchQuery = ref(props.filters.search || '');

// 카테고리 리스트
const categories = [
    { name: '전체', value: ''},
    { name: '자유게시판', value: 'general'},
    { name: '리믹스 추천', value: 'recommend'},
    { name: '질문/답변', value: 'question'},
];

// 통합 필터링 함수
const filterCategory = (value) => {
    router.get(route('posts.index'), 
        { ...props.filters, category: value, page: 1 }, 
        { preserveState: true, replace: true }
    );
};

// 검색 실행 함수
const handleSearch = () => {
    router.get(route('posts.index'), 
        { ...props.filters, search: searchQuery.value, page: 1 }, 
        { preserveState: true, replace: true }
    );
};
</script>

<template>
    <Head title="커뮤니티" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    커뮤니티
                </h2>
                <Link :href="route('posts.create')" 
                      class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                    새 글 쓰기
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2 no-scrollbar pl-2">
                    <button 
                        v-for="cat in categories" 
                        :key="cat.value"
                        @click="filterCategory(cat.value)"
                        class="px-5 py-2.5 rounded-full text-sm font-black transition-all whitespace-nowrap shadow-sm"
                        :class="[
                            (filters.category === cat.value || (!filters.category && cat.value === ''))
                                ? 'bg-indigo-600 text-white shadow-indigo-200 scale-105'
                                : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50 hover:text-indigo-600'
                        ]"
                    >
                        {{ cat.name }}
                    </button>
                </div>
                <div class="flex justify-end w-full mt-2">
                    <div class="text-xs text-gray-400 font-medium">
                        총 <span class="text-indigo-500 font-bold">{{ posts.total }}</span>개의 게시물이 있습니다.
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-[1.5rem] border border-gray-200 mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-20 text-center">No.</th>
                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider">제목</th>
                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-32">작성자</th>
                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-24 text-center">조회수</th>
                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-24 text-center">추천</th>
                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-32 text-center">날짜</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="post in posts.data" :key="post.id" class="hover:bg-indigo-50/30 transition-colors cursor-pointer group">
                                    <td class="px-6 py-5 text-center text-gray-400 font-medium">{{ post.id }}</td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-400 text-[10px] rounded font-bold uppercase">
                                                {{ post.category }}
                                            </span>
                                            <Link :href="route('posts.show', post.id)" class="text-gray-900 font-bold group-hover:text-indigo-600 transition-colors">
                                                {{ post.title }}
                                            </Link>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                                {{ post.user.name.substring(0, 1) }}
                                            </div>
                                            <span class="text-sm text-gray-600 font-medium">{{ post.user.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center text-gray-500 text-sm">{{ post.view_count }}</td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <span :class="post.likes_count > 0 ? 'text-pink-500 font-bold' : 'text-gray-400'" class="text-sm">
                                                {{ post.likes_count || 0 }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center text-gray-400 text-xs">
                                        {{ new Date(post.created_at).toLocaleDateString() }}
                                    </td>
                                </tr>
                                <tr v-if="posts.data.length === 0">
                                    <td colspan="5" class="px-6 py-20 text-center text-gray-400 font-medium">
                                        데이터가 없습니다.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col items-center gap-6 mt-8">
                    <div class="relative w-full max-w-md">
                        <input 
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            type="text" 
                            placeholder="찾으시는 글이 있나요?"
                            class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-[1.5rem] text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm"
                        />
                        <svg @click="handleSearch" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-1">
                        <button v-for="link in posts.links" 
                                :key="link.label"
                                v-html="link.label"
                                @click="link.url ? $inertia.get(link.url) : null"
                                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                                :class="[
                                    link.active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white text-gray-400 border border-gray-200 hover:bg-gray-50',
                                    !link.url ? 'opacity-30 cursor-not-allowed' : ''
                                ]"
                                :disabled="!link.url">
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>