import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue'; // 방금 만든 App.vue 불러오기

const app = createApp(App);
app.mount('#app'); // HTML의 id="app"인 곳에 Vue를 끼워넣습니다.