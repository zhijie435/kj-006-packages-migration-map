import CourseCard from './components/CourseCard.vue';
import StatisticsPanel from './components/StatisticsPanel.vue';
import DataTable from './components/DataTable.vue';

export {
    CourseCard,
    StatisticsPanel,
    DataTable
};

export default {
    install(app) {
        app.component('CourseCard', CourseCard);
        app.component('StatisticsPanel', StatisticsPanel);
        app.component('DataTable', DataTable);
    }
};
