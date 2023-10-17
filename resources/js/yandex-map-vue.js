import { createApp } from 'vue';
export const app = createApp({});
import YandexMap from './components/YandexMap.vue';
app.component('YandexMap', YandexMap);
app.mount("#app");
