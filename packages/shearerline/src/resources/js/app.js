import { createApp } from 'vue';
import DataTable from './components/DataTable.vue';
import StatisticsPanel from './components/StatisticsPanel.vue';
import CourseCard from './components/CourseCard.vue';

const app = createApp({});

app.component('DataTable', DataTable);
app.component('StatisticsPanel', StatisticsPanel);
app.component('CourseCard', CourseCard);

app.mount('#app');

export default app;
