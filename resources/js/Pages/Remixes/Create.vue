<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    genres: Array,
    musicTracks: Array
});

const form = useForm({
    title: '',
    youtube_url: '',
    genre_id: '',
    music_track_id: '',
    description: '',
});

const submit = () => {
    form.post(route('remixes.store'));
};
</script>

<template>
    <Head title="리믹스 업로드" />

    <AuthenticatedLayout>
        <div class="py-12 bg-[#F8FAFC] min-h-screen">
            <div class="max-w-4xl mx-auto px-6">
                
                <header class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-6 px-2">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-black rounded uppercase tracking-widest">Artist Studio</span>
                        </div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase italic leading-none">Upload Remix</h1>
                        <p class="text-gray-400 font-bold mt-2 uppercase tracking-widest text-[10px]">Share your vibe and define the new trend.</p>
                    </div>

                    <Link 
                        :href="route('remixes.index')" 
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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Original Track</label>
                                <div class="relative group">
                                    <select v-model="form.music_track_id" 
                                            class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-indigo-600 transition-all cursor-pointer appearance-none">
                                        <option value="" disabled italic>원곡을 선택하세요</option>
                                        <option v-for="track in musicTracks" :key="track.id" :value="track.id">
                                            {{ track.title }} ({{ track.artist }})
                                        </option>
                                    </select>
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Genre</label>
                                <div class="relative group">
                                    <select v-model="form.genre_id" 
                                            class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-700 focus:ring-2 focus:ring-indigo-600 transition-all cursor-pointer appearance-none">
                                        <option value="" disabled italic>장르 선택</option>
                                        <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                                            {{ genre.name }}
                                        </option>
                                    </select>
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Remix Title</label>
                            <input v-model="form.title" type="text" placeholder="예: Hype Boy (TrendMixer Edition)" 
                                   class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 text-sm font-bold text-gray-900 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">YouTube Link</label>
                            <div class="relative">
                                <input v-model="form.youtube_url" type="text" placeholder="https://youtube.com/watch?v=..." 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-12 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-500 transition-all shadow-inner" />
                                <div class="absolute left-5 top-1/2 -translate-y-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">About this Remix</label>
                            <textarea v-model="form.description" rows="4" placeholder="작업 의도나 사용된 툴 등 리믹스에 대한 설명을 들려주세요."
                                      class="w-full bg-gray-50 border-none rounded-[1.8rem] py-5 px-6 text-sm font-medium text-gray-700 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner resize-none"></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    :disabled="form.processing"
                                    class="w-full py-5 bg-gray-900 text-white rounded-2xl font-black text-sm shadow-xl shadow-gray-100 hover:bg-indigo-600 hover:-translate-y-1 active:scale-[0.98] transition-all uppercase tracking-widest flex items-center justify-center gap-3">
                                <span v-if="form.processing" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                {{ form.processing ? 'Publishing...' : 'Publish Remix' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-8 px-8 py-6 bg-white rounded-[2rem] border border-gray-100 flex items-start gap-5 shadow-sm">
                    <div class="w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">📢</span>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.15em] mb-1">Copyright Notice</h4>
                        <p class="text-[11px] font-bold text-gray-400 leading-relaxed uppercase tracking-tighter">
                            리믹스 업로드 시 타인의 저작권을 존중해 주세요. 원곡 정보가 정확해야 <span class="text-indigo-600">CHART CLASH</span> 랭킹에 정상적으로 반영됩니다.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* 입력 필드 내부 그림자로 깊이감 부여 */
input, select, textarea {
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
}
</style>