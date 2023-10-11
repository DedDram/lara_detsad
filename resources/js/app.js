import { createApp } from 'vue';  // Импортируем createApp из Vue 3.x
import YandexMap from './components/yandex-map.vue';

const app = createApp(YandexMap);  // Создаем экземпляр приложения на основе вашего компонента

app.mount('#yandex-map');
