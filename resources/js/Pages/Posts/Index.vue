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
    { name: 'all genres', value: ''},
    { name: 'general', value: 'general'},
    { name: 'recommend', value: 'recommend'},
    { name: 'question', value: 'question'},
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

const getCategoryName = (category) => {
    const names = {
        general: '자유',
        recommend: '추천',
        question: '질문',
    };
    return names[category] || category;
};
</script>

<template>
    <Head title="커뮤니티" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-[#F8FAFC] p-8 rounded-[2.5rem] shadow-sm border border-gray-200">

                    <div class="max-w-7xl mx-auto">
                        
                        <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
                            <div>
                                <h1 class="text-4xl font-black text-gray-900 tracking-tighter">COMMUNITY</h1>
                                <p class="text-gray-500 font-bold mt-2 uppercase tracking-widest text-xs">Connect with creators, share your insights.</p>
                            </div>
                            
                            <Link :href="route('posts.create')" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-sm shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                                POST NEW STORY
                            </Link>
                        </header>
                            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-gray-100 mb-10 flex flex-col sm:flex-row items-start sm:items-center justify-start gap-4">
                                    <div class="flex flex-wrap items-center gap-2 justify-start w-full">
                                        <button 
                                            v-for="cat in categories" 
                                            :key="cat.value"
                                            @click="filterCategory(cat.value)"
                                            class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-black transition-all whitespace-nowrap uppercase"
                                            :class="[
                                                // 1. 공통 비활성 상태
                                                (props.filters.category !== cat.value && !(!props.filters.category && cat.value === '')) 
                                                    ? 'bg-gray-50 text-gray-500 hover:bg-gray-100' : '',

                                                // 2. 활성 상태일 때 (ALL GENRES는 검은색, 나머지는 인디고)
                                                (props.filters.category === cat.value || (!props.filters.category && cat.value === '')) 
                                                    ? (cat.value === '' ? 'bg-gray-900 text-white shadow-md' : 'bg-indigo-600 text-white shadow-md') 
                                                    : ''
                                            ]"
                                        >
                                            {{ cat.name }}
                                        </button>
                                    </div>
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
                                                    <th class="px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider text-center">제목</th>
                                                    <th class="hidden min-[480px]:table-cell px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-32 text-center">작성자</th>
                                                    <th class="hidden sm:table-cell px-4 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-24 text-center">조회/추천</th>
                                                    <th class="hidden sm:table-cell px-6 py-4 text-sm font-bold text-gray-500 uppercase tracking-wider w-32 text-center">날짜</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                <tr v-for="post in posts.data" :key="post.id" class="hover:bg-indigo-50/30 transition-colors cursor-pointer group">
                                                    <td class="px-6 py-5">
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-400 text-[10px] rounded font-bold uppercase">
                                                                {{ getCategoryName(post.category) }}
                                                            </span>
                                                            <Link :href="route('posts.show', post.id)" class="text-gray-900 font-bold group-hover:text-indigo-600 transition-colors">
                                                                {{ post.title }}
                                                            </Link>
                                                        </div>
                                                    </td>
                                                    <td class="hidden min-[480px]:table-cell px-6 py-5">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                                                {{ post.user.name.substring(0, 1) }}
                                                            </div>
                                                            <span class="text-sm text-gray-600 font-medium">{{ post.user.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="hidden sm:table-cell px-6 py-5 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <span class="text-gray-500 text-sm">{{ post.view_count }}</span>
                                                            /
                                                            <span :class="post.likes_count > 0 ? 'text-pink-500 font-bold' : 'text-gray-400'" class="text-sm">
                                                                {{ post.likes_count || 0 }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="hidden sm:table-cell px-6 py-5 text-center text-gray-400 text-xs">
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
                </div>
            </div>
        </div>                
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>