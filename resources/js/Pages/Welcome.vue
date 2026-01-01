<script setup>
import RemixList from '@/components/RemixList.vue'; // 기존에 만든 컴포넌트 경로 확인
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="TrendMixer" />
    
    <div class="p-6 text-right">
        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-gray-600 underline">대시보드</Link>
        <template v-else>
            <Link :href="route('login')" class="text-gray-600 underline">로그인</Link>
            <Link :href="route('register')" class="ml-4 text-gray-600 underline">회원가입</Link>
        </template>
    </div>

    <RemixList />
</template>