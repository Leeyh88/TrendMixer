<template>
  <div class="container mx-auto p-6">
    <h2 class="text-2xl font-extrabold text-gray-800 mb-8">최신 리믹스 차트</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div 
        v-for="remix in remixes" :key="remix.id" 
        @click="openModal(remix.youtube_video_id)"
        class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl transition-all cursor-pointer border border-gray-100 overflow-hidden"
      >
        <div class="relative aspect-video overflow-hidden">
          <img :src="remix.music_track.thumbnail_url" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
          <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40">
             <div class="bg-white p-3 rounded-full text-indigo-600 shadow-lg">▶️</div>
          </div>
        </div>

        <div class="p-5">
          <h3 class="text-lg font-bold text-gray-900 truncate">{{ remix.title }}</h3>
          <p class="text-gray-500 text-sm mt-2">Original: {{ remix.music_track.artist }}</p>
        </div>
      </div>
    </div>

    <div v-if="selectedVideoId" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @click.self="closeModal">
      <div class="relative w-full max-w-4xl aspect-video bg-black rounded-xl overflow-hidden shadow-2xl">
        <button @click="closeModal" class="absolute -top-10 right-0 text-white text-xl font-bold hover:text-gray-300">닫기 [X]</button>
        
        <iframe 
          class="w-full h-full"
          :src="`https://www.youtube.com/embed/${selectedVideoId}?autoplay=1`" 
          frameborder="0" 
          allow="autoplay; encrypted-media" 
          allowfullscreen
        ></iframe>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const remixes = ref([]);
const selectedVideoId = ref(null); // 현재 선택된 비디오 ID (모달 제어용)

const fetchRemixes = async () => {
    const response = await axios.get('/api/remixes');
    remixes.value = response.data;
};

const openModal = (videoId) => {
    selectedVideoId.value = videoId;
};

const closeModal = () => {
    selectedVideoId.value = null;
};

onMounted(fetchRemixes);
</script>