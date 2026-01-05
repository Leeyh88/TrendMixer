<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    myPosts: Array,
    likedPosts: Array,
});

const activeTab = ref('posts'); // posts or likes
</script>
<template>
    <Head title="프로필 설정" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">프로필 설정</h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-[1.5rem] border border-gray-200">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-[1.5rem] border border-gray-200">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-[1.5rem] border border-gray-200">
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                            나의 활동 내역
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 font-medium">작성한 글과 좋아요를 누른 글을 관리하세요.</p>
                    </header>

                    <div class="flex items-center gap-2 mb-6 bg-gray-50 p-1.5 rounded-2xl w-fit">
                        <button 
                            @click="activeTab = 'myPosts'"
                            :class="activeTab === 'myPosts' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-6 py-2 rounded-xl text-sm font-black transition-all"
                        >
                            내가 쓴 글 <span class="ml-1 opacity-50">{{ myPosts?.length || 0 }}</span>
                        </button>
                        <button 
                            @click="activeTab = 'likedPosts'"
                            :class="activeTab === 'likedPosts' ? 'bg-white text-pink-500 shadow-sm' : 'text-gray-400 hover:text-gray-600'"
                            class="px-6 py-2 rounded-xl text-sm font-black transition-all"
                        >
                            좋아요 <span class="ml-1 opacity-50">{{ likedPosts?.length || 0 }}</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template v-if="activeTab === 'myPosts'">
                            <div v-for="post in myPosts" :key="post.id" 
                                class="flex items-center justify-between p-5 bg-white border border-gray-100 rounded-2xl hover:border-indigo-200 hover:shadow-md hover:shadow-indigo-50 transition-all group">
                                <div class="flex flex-col gap-1">
                                    <Link :href="route('posts.show', post.id)" class="text-gray-900 font-bold group-hover:text-indigo-600 transition-colors">
                                        {{ post.title }}
                                    </Link>
                                    <div class="flex items-center gap-3 text-[11px] text-gray-400 font-bold">
                                        <span>조회 {{ post.view_count }}</span>
                                        <span>추천 {{ post.likes_count || 0 }}</span>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400 font-medium">{{ new Date(post.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div v-if="myPosts.length === 0" class="py-12 text-center text-gray-400 font-medium">
                                아직 작성한 글이 없습니다. 커뮤니티에 첫 글을 남겨보세요!
                            </div>
                        </template>

                        <template v-if="activeTab === 'likedPosts'">
                            <div v-for="post in likedPosts" :key="post.id" 
                                class="flex items-center justify-between p-5 bg-white border border-gray-100 rounded-2xl hover:border-pink-200 hover:shadow-md hover:shadow-pink-50 transition-all group">
                                <div class="flex flex-col gap-1">
                                    <Link :href="route('posts.show', post.id)" class="text-gray-900 font-bold group-hover:text-pink-500 transition-colors">
                                        {{ post.title }}
                                    </Link>
                                    <span class="text-[11px] text-gray-400 font-bold">작성자: {{ post.user?.name }}</span>
                                </div>
                                <div class="w-8 h-8 bg-pink-50 rounded-full flex items-center justify-center text-pink-500 text-xs">
                                    ♥
                                </div>
                            </div>
                            <div v-if="likedPosts.length === 0" class="py-12 text-center text-gray-400 font-medium">
                                마음에 드는 글에 좋아요를 눌러보세요!
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-[1.5rem] border border-gray-200">
                    <DeleteUserForm class="max-w-xl" />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>