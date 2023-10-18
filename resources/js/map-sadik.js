import { createApp } from 'vue';
export const app = createApp({});
import MapSadik from './components/MapSadik.vue';
app.component('MapSadik', MapSadik);
app.mount("#map-sadik");
