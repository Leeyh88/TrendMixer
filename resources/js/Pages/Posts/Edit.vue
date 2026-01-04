<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        post: Object
    });

    // 기존 post 데이터를 초기값으로 넣기
    const form = useForm({
        title: props.post.title,
        content: props.post.content,
        category: props.post.category,
    });

    const submit = () => {
        form.patch(route('posts.update', props.post.id));
    };
</script>

<template>
    <Head title="글 수정하기" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">게시글 수정</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-200">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">카테고리</label>
                            <select v-model="form.category" class="w-full border-gray-200 rounded-xl focus:ringindigo-500">
                                <option value="general">자유게시판</option>
                                <option value="recommend">리믹스추천</option>
                                <option value="question">질문/답변</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">제목</label>
                            <input v-model="form.title" type="text" class="w-full border-gray-200 rounded-xl focus:ringindigo-500" placeholder="제목을 입력하세요">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">내용</label>
                            <textarea v-model="form.content" rows="10" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500" placeholder="내용을 입력하세요"></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="() => window.history.back()" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition-all">취소</button>
                            <button type="button" :disabled="form.processing" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-70 transition-all shadow-lg shadow-indigo-200">수정 완료</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>