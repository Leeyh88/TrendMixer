<script setup>
import Tiptap from '@/Components/Tiptap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    category: 'general', // 기본값
    content: '',
});

const submit = () => {
    // route('posts.store')는 약속된 POST /posts 경로로 전송합니다.
    form.post(route('posts.store'));
};
</script>

<template>
    <Head title="새 글 쓰기" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">커뮤니티 글쓰기</h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
            <div class="max-w-3xl mx-auto px-4">
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-200 overflow-hidden">
                    <form @submit.prevent="submit" class="p-8 lg:p-12 space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">카테고리</label>
                            <select v-model="form.category" 
                                    class="w-full border-gray-300 rounded-2xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="general">자유게시판</option>
                                <option value="recommend">리믹스 추천</option>
                                <option value="question">질문/답변</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">제목</label>
                            <input v-model="form.title" type="text" placeholder="제목을 입력하세요"
                                   class="w-full border-gray-300 rounded-2xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">내용</label>
                            <Tiptap v-model="form.content" />
                            <!-- <textarea v-model="form.content" rows="12" placeholder="자유롭게 내용을 입력하세요"
                                      class="w-full border-gray-300 rounded-2xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <p v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</p> -->
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4">
                            <Link :href="route('posts.index')" class="text-gray-500 font-bold hover:text-gray-700 transition-colors">
                                취소
                            </Link>
                            <button type="submit" 
                                    :disabled="form.processing"
                                    class="px-10 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-3xl font-black text-lg shadow-lg shadow-indigo-100 transition-all active:scale-95 disabled:opacity-50">
                                {{ form.processing ? '등록 중...' : '등록하기' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>