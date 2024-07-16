<script setup>
import {ref,computed } from 'vue';
import { Chart, Legend, Title, Tooltip, } from "chart.js";
import { BarChart } from 'vue-chart-3';
import { BarController, CategoryScale, LinearScale, BarElement } from 'chart.js';
Chart.register(Legend, Title, Tooltip, BarController, CategoryScale, LinearScale, BarElement);

const props = defineProps({
    data: Object,
});

/*const dataValues = ref([
        [30, 40, 60, 70, 5,30, 48],
        [30, 40, 60, 70, 5,30, 40],
]);
*/
const data = computed(() => ({
    labels: props.data.labels,
    datasets: [
        {
            label:"leads",
            data: props.data.datasets,
            backgroundColor: '#77CEFF',
                        
        }
    ],
})); 

const options = ref({
    plugins:{
        title:{
            text:"Bar",
        }        
    },
    scales: {
        y: {
            beginAtZero: true
        }
    }
});
</script>
<style scoped>
    .chart-container{
        height: 280px;
    }
</style>
<template>
    <BarChart 
        :chart-data="data" 
        :options="options" 
        css-classes="chart-container" />
</template>