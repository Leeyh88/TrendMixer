<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm,router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    post: Object,
});

// 현재 로그인한 사용자가 작성자인지 확인
const page = usePage();
const isAuthor = computed(() => page.props.auth.user.id === props.post.user_id);
const user = computed(() => page.props.auth.user);

// 댓글 입력을 위한 폼 객체
const commentForm = useForm({
    content: '',
});

// 댓글 저장 함수
const submitComment = () => {
    commentForm.post(route('posts.comments.store', props.post.id), {
        preserveScroll: true, // 댓글 등록 후 스크롤 위치 유지
        onSuccess: () => commentForm.reset(), // 성공시 입력창 비우기
    });
};

// 댓글 삭제 함수 
const deleteComment = (commentId) => {
    if (confirm('댓글을 삭제하시겠습니까?')) {
        commentForm.delete(route('comments.destroy', commentId), {
            preserveScroll: true,
        });
    }
};

// 삭제 함수
const deletePost = () => {
    if (confirm('정말로 이 게시글을 삭제하시겠습니까?')) {
        router.delete(route('posts.destroy', props.post.id));
    }
};

const sharePost = () => {
    // 브라우저 공유 기능이 있는지 확인 
    if (navigator.share) {
        navigator.share({
            title: props.post.title,
            text: props.post.content.substring(0, 100),
            url: window.location.href,
        })
        .then(() => console.log('공유 성공'))
        .catch((error) => console.log('공유 실패', error));
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('주소가 클립보드에 복사되었습니다.');
    }
};

// 답글 입력창 상태
const replyingTo = ref(null); // 현재 답글을 작성중인 부모 댓글의 ID

// 대댓글 전용폼
const replyForm = useForm({
    content: '',
    parent_id: null,
});

const toggleReply = (commentId) => {
    if (replyingTo.value === commentId) {
        replyingTo.value = null;
    } else {
        replyingTo.value = commentId;
        replyForm.parent_id = commentId;
        replyForm.content ='';
    }
};

const submitReply = (parentId) => {
    replyForm.post(route('posts.comments.store', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            replyingTo.value = null;
            replyForm.reset();
        },
    });
};

const toggleLike = () => {
    router.post(route('posts.like', props.post.id), {}, {
        preserveScroll: true, // 좋아요 눌렀을 때 화면 튕김 방지
    });
};
</script>
<template>
    <Head :title="post.title" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-200">

                    <header class="mb-10 flex items-center justify-between"> 
                        <div class="flex items-center gap-4">
                            <div>
                                <h1 class="text-2xl font-black text-gray-900 leading-none">READ STORY</h1>
                                <p class="text-sm text-gray-400 font-bold italic mt-1">Read, relate, and reply.</p>
                            </div>
                        </div>

                        <Link 
                            :href="route('remixes.index')" 
                            class="flex items-center gap-2 px-5 py-2.5 bg-gray-50 text-gray-500 rounded-xl font-black text-xs hover:bg-gray-100 hover:text-indigo-600 transition-all border border-gray-100 shadow-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            BACK TO LIST
                        </Link>
                    </header>

                    <!-- <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
                        <div class="max-w-4xl mx-auto px-4 space-y-8"> <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-200 overflow-hidden"> -->
                                <div class="p-8 lg:p-12 border-b border-gray-100 bg-white">
                                    <div class="flex items-center gap-2 mb-6">
                                        <span class="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black uppercase tracking-wider">
                                            {{ post.category }}
                                        </span>
                                        <span class="text-gray-400 text-sm font-medium">
                                            {{ new Date(post.created_at).toLocaleDateString() }}
                                        </span>
                                    </div>

                                    <h1 class="text-3xl lg:text-4xl font-black text-gray-900 mb-8 leading-tight">
                                        {{ post.title }}
                                    </h1>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-100">
                                                {{ post.user.name[0] }}
                                            </div>
                                            <div>
                                                <p class="text-base font-bold text-gray-900">{{ post.user.name }}</p>
                                                <p class="text-xs text-gray-500">{{ post.user.email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-6 text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs uppercase font-bold tracking-tighter">Views</span>
                                                <span class="text-lg font-black text-gray-700">{{ post.view_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-8 lg:p-12 min-h-[300px]">
                                    <div class="prose max-w-none text-gray-700 leading-[1.8] text-lg whitespace-pre-wrap font-medium" v-html="post.content">
                                    </div>
                                </div>

                                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <template v-if="isAuthor">
                                            <Link :href="route('posts.edit', post.id)" class="text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors">수정하기</Link>
                                            <button @click="deletePost" class="text-sm font-bold text-red-400 hover:text-red-600 transition-colors">삭제하기</button>
                                        </template>
                                    </div>
                                    <button @click="sharePost" class="flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-200 rounded-full text-sm font-bold text-gray-700 shadow-sm hover:shadow-md transition-all active:scale-95">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                        공유
                                    </button>
                                </div>

                                <div class="flex flex-col items-center justify-center py-1 mt-1 border-t border-gray-50 bg-[#F8FAFC] rounded">
                                    <button 
                                        @click="toggleLike"
                                        class="group flex flex-col items-center gap-3 transition-all active:scale-90"
                                    >
                                        <div 
                                            :class="post.is_liked 
                                                ? 'bg-pink-500 border-pink-500 shadow-pink-200' 
                                                : 'bg-white border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-400'"
                                            class="w-20 h-20 rounded-[2rem] border-2 flex items-center justify-center shadow-xl transition-all"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                :class="post.is_liked ? 'fill-white' : 'fill-none'"
                                                class="h-10 w-10 stroke-current stroke-2 transition-colors" 
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <span 
                                            :class="post.is_liked ? 'text-red-500' : 'text-gray-500'"
                                            class="text-lg font-black tracking-tight"
                                        >
                                            {{ post.likes_count || 0 }}
                                        </span>
                                    </button>
                                    <p class="text-gray-400 text-xs mt-2 font-medium">이 글이 마음에 드신다면 하트를 눌러주세요!</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-200 overflow-hidden p-8 lg:p-12 mt-3">
                                <h3 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-2">
                                    댓글 <span class="text-indigo-600">{{ post.comments?.length || 0 }}</span>
                                </h3>

                                <form @submit.prevent="submitComment" class="mb-12 relative group">
                                    <textarea 
                                        v-model="commentForm.content"
                                        placeholder="따뜻한 댓글을 남겨주세요!"
                                        class="w-full border-gray-200 rounded-[1.8rem] p-6 pr-24 focus:ring-2 focus:ring-indigo-500 focus:border-transparent min-h-[140px] transition-all bg-gray-50/50 resize-none"
                                    ></textarea>
                                    <button 
                                        type="submit" 
                                        :disabled="commentForm.processing || !commentForm.content"
                                        class="absolute bottom-6 right-6 px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-sm hover:bg-indigo-700 disabled:opacity-30 transition-all shadow-lg shadow-indigo-200"
                                    >
                                        등록
                                    </button>
                                </form>



                                <div class="space-y-10">
                                    <div v-for="comment in post.comments" :key="comment.id" class="flex flex-col gap-4">
                                        <div class="flex gap-4 group/item">
                                            <div class="w-11 h-11 bg-gray-100 rounded-2xl flex-shrink-0 flex items-center justify-center font-bold text-gray-400">
                                                {{ comment.user.name[0] }}
                                            </div>
                                            
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-gray-900 text-sm">{{ comment.user.name }}</span>
                                                        <span class="text-xs text-gray-400 font-medium">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <button @click="toggleReply(comment.id)" class="text-[11px] font-black text-indigo-500 hover:text-indigo-700">
                                                            {{ replyingTo === comment.id ? '취소' : '답글' }}
                                                        </button>
                                                        <button v-if="user && user.id === comment.user_id" @click="deleteComment(comment.id)" class="text-[11px] font-bold text-red-300 opacity-0 group-hover/item:opacity-100 transition-all">
                                                            삭제
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50/80 rounded-[1.3rem] rounded-tl-none p-5 text-gray-700 text-[15px] shadow-sm">
                                                    {{ comment.content }}
                                                </div>

                                                <div v-if="replyingTo === comment.id" class="mt-4 ml-2">
                                                    <form @submit.prevent="submitReply(comment.id)" class="relative">
                                                        <textarea v-model="replyForm.content" placeholder="답글을 남겨주세요..." 
                                                                class="w-full border-gray-100 rounded-[1.5rem] p-4 pr-20 focus:ring-2 focus:ring-indigo-400 bg-white shadow-sm resize-none text-sm min-h-[100px]"></textarea>
                                                        <button type="submit" class="absolute bottom-4 right-4 px-4 py-2 bg-indigo-500 text-white rounded-xl font-bold text-xs shadow-md">
                                                            답글 등록
                                                        </button>
                                                    </form>
                                                </div>

                                                <div v-if="comment.replies?.length" class="mt-6 space-y-6">
                                                    <div v-for="reply in comment.replies" :key="reply.id" class="flex gap-3 group/reply pl-4">                                            
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-6 h-6 bg-indigo-50 rounded-lg flex items-center justify-center font-bold text-indigo-400 text-[10px]">
                                                                        {{ reply.user.name[0] }}
                                                                    </div>
                                                                    <span class="font-bold text-gray-900 text-xs">{{ reply.user.name }}</span>
                                                                    <span class="text-[10px] text-gray-400">{{ new Date(reply.created_at).toLocaleDateString() }}</span>
                                                                </div>
                                                                <button v-if="user && user.id === reply.user_id" @click="deleteComment(reply.id)" 
                                                                        class="opacity-0 group-hover/reply:opacity-100 text-[10px] font-bold text-red-300 hover:text-red-500">
                                                                    삭제
                                                                </button>
                                                            </div>
                                                            <div class="bg-indigo-50/30 rounded-[1.1rem] rounded-tl-none p-4 text-gray-700 text-sm leading-relaxed border border-indigo-100/50">
                                                                {{ reply.content }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* 본문 텍스트가 너무 빽빽하지 않게 조절 */
.prose {
    word-break: break-word;
}
</style>