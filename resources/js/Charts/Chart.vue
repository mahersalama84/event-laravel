<template>
    <!-- <Chart :type="type" :data="chartData" :options="chartOptions" /> -->
    <Bar :data="chartData" :options="chartOptions" />
</template>

<script setup lang="ts">
const props = defineProps(["type", "chartData", "chartOptions"]);

import {
    Bar,
    Doughnut,
    Line,
    Pie,
    PolarArea,
    Radar,
    Bubble,
    Scatter,
    Chart,
} from "vue-chartjs";

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    LineElement,
    PointElement,
    BarElement,
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    Filler,
    InteractionItem,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    LineElement,
    PointElement,
    BarElement,
    CategoryScale,
    LinearScale,
    RadialLinearScale,
    Filler
);
</script>
