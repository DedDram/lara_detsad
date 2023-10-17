import { createApp } from 'vue';
export const app = createApp({});
import SearchSadikInTable from './components/SearchSadikInTable.vue';
app.component('SearchSadikInTable', SearchSadikInTable);
app.mount("#filter");
