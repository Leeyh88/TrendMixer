<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// 컨트롤러에서 받아온 데이터 정의
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
    <Head title="리믹스" />

    <AuthenticatedLayout>

        <div class="py-12">
            <div class="max-w-4xl mx-auto px-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-200">

                    <header class="mb-10 flex items-center justify-between"> <div class="flex items-center gap-4">
                        
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 leading-none">Upload Remix</h1>
                            <p class="text-sm text-gray-400 font-bold italic mt-1">Share your vibe with the world.</p>
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
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Original Track</label>
                                <select v-model="form.music_track_id" class="w-full rounded-[1.2rem] border-none bg-gray-50 focus:ring-2 focus:ring-indigo-500 font-bold p-4 text-gray-700 appearance-none">
                                    <option value="" disabled>원곡을 선택하세요</option>
                                    <option v-for="track in musicTracks" :key="track.id" :value="track.id">
                                        {{ track.title }} - {{ track.artist }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Genre</label>
                                <select v-model="form.genre_id" class="w-full rounded-[1.2rem] border-none bg-gray-50 focus:ring-2 focus:ring-indigo-500 font-bold p-4 text-gray-700 appearance-none">
                                    <option value="" disabled>장르 선택</option>
                                    <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                                        {{ genre.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Remix Title</label>
                            <input v-model="form.title" type="text" placeholder="예: Hype Boy (TrendMixer Edition)" class="w-full rounded-[1.2rem] border-none bg-gray-50 focus:ring-2 focus:ring-indigo-500 font-bold p-4 text-gray-700" />
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">YouTube Link</label>
                            <input v-model="form.youtube_url" type="text" placeholder="https://youtube.com/..." class="w-full rounded-[1.2rem] border-none bg-gray-50 focus:ring-2 focus:ring-red-500 font-bold p-4 text-gray-700" />
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">About this Remix</label>
                            <textarea v-model="form.description" rows="3" class="w-full rounded-[1.2rem] border-none bg-gray-50 focus:ring-2 focus:ring-indigo-500 font-medium p-4 text-gray-700"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[1.5rem] font-black text-lg hover:bg-black transition-all shadow-xl shadow-gray-200">
                            PUBLISH REMIX
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>