<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // 레이아웃 임포트 
import RemixList from '@/components/RemixList.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="TrendMixer" />

    <AuthenticatedLayout v-if="$page.props.auth.user">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                최신 리믹스 목록
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <RemixList />
            </div>
        </div>
    </AuthenticatedLayout>

    <div v-else class="min-h-screen bg-gray-100">
        <nav class="p-6 text-right bg-white border-b">
            <Link :href="route('login')" class="text-gray-600 underline">로그인</Link>
            <Link :href="route('register')" class="ml-4 text-gray-600 underline text-indigo-600 font-bold">회원가입</Link>
        </nav>

        <div class="py-12 text-center">
            <h1 class="text-3xl font-bold mb-4">TrendMixer</h1>
            <p class="mb-8 text-gray-500">로그인하고 리믹스 곡에 투표해보세요!</p>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 text-left">
                <RemixList />
            </div>
        </div>
    </div>
</template>