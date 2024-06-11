import './bootstrap';
import Vue from 'vue'
import { createApp } from 'vue';
import VueApexCharts from 'vue-apexcharts'

const app = createApp();
app.mount('#app');

Vue.use(VueApexCharts)

Vue.component('apexchart', VueApexCharts)




