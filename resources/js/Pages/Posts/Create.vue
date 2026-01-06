<script setup>
import Tiptap from '@/Components/Tiptap.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    category: 'general',
    content: '',
});

const submit = () => {
    form.post(route('posts.store'));
};
</script>

<template>
    <Head title="새 글 작성" />

    <AuthenticatedLayout>
        <div class="py-12 bg-[#F8FAFC] min-h-screen">
            <div class="max-w-4xl mx-auto px-6">
                
                <header class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4 px-2">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-black rounded uppercase tracking-widest">Creator Studio</span>
                        </div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic leading-none">Post New Story</h1>
                        <p class="text-gray-400 font-bold mt-2 uppercase tracking-widest text-[10px]">Explore, Discuss, and Mix your insights.</p>
                    </div>

                    <Link 
                        :href="route('posts.index')" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-gray-400 rounded-2xl font-black text-xs hover:text-indigo-600 hover:border-indigo-100 transition-all border border-gray-100 shadow-sm uppercase tracking-widest group"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </Link>
                </header>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <form @submit.prevent="submit" class="p-8 lg:p-12 space-y-8">
                        
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <div class="lg:col-span-1">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Category</label>
                                <select v-model="form.category" 
                                        class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-indigo-600 transition-all cursor-pointer appearance-none">
                                    <option value="general">자유게시판</option>
                                    <option value="recommend">리믹스 추천</option>
                                    <option value="question">질문/답변</option>
                                </select>
                            </div>

                            <div class="lg:col-span-3">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Story Title</label>
                                <input v-model="form.title" type="text" placeholder="트렌디한 제목을 입력해 주세요"
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-900 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 transition-all" />
                                <p v-if="form.errors.title" class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-tight">{{ form.errors.title }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Content</label>
                            <div class="rounded-3xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50/30">
                                <Tiptap v-model="form.content" />
                            </div>
                            <p v-if="form.errors.content" class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-tight">{{ form.errors.content }}</p>
                        </div>

                        <div class="flex items-center justify-end gap-6 pt-6 border-t border-gray-50">
                            <Link :href="route('posts.index')" class="text-xs font-black text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">
                                Cancel
                            </Link>
                            <button type="submit" 
                                    :disabled="form.processing"
                                    class="px-12 py-4 bg-gray-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-gray-100 hover:bg-indigo-600 hover:-translate-y-1 active:scale-95 transition-all disabled:opacity-50 flex items-center gap-3 uppercase tracking-widest">
                                <span v-if="form.processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                {{ form.processing ? 'Publishing...' : 'Publish Story' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-8 px-6 py-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 flex items-center gap-4">
                    <span class="text-lg">💡</span>
                    <p class="text-[11px] font-bold text-indigo-400 leading-relaxed uppercase tracking-tighter">
                        이미지나 리믹스 링크를 포함하면 더 많은 <span class="text-indigo-600">Mixers</span>의 반응을 얻을 수 있습니다.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Tiptap 에디터 내부 스타일 최적화 (필요시) */
:deep(.ProseMirror) {
    min-height: 300px;
    padding: 1.5rem;
    outline: none;
}
</style>