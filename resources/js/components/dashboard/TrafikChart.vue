<script setup>
import ApexCharts from 'apexcharts';
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    labels: {
        type: Array,
        default: () => [],
    },
    series: {
        type: Array,
        default: () => [],
    },
});

const chartElement = ref(null);
const isDark = ref(false);
let chartInstance = null;
let observer = null;

const syncTheme = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

const renderChart = async () => {
    await nextTick();

    if (! chartElement.value) {
        return;
    }

    chartInstance?.destroy();

    chartInstance = new ApexCharts(chartElement.value, {
        chart: {
            type: 'line',
            height: 320,
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
            fontFamily: 'Satoshi, Inter, sans-serif',
            foreColor: isDark.value ? '#98A2B3' : '#667085',
        },
        series: [
            {
                name: 'Views',
                data: props.series,
            },
        ],
        stroke: {
            curve: 'smooth',
            width: 3,
        },
        colors: ['#465FFF'],
        markers: {
            size: 4,
            strokeWidth: 0,
            hover: {
                sizeOffset: 2,
            },
        },
        grid: {
            borderColor: isDark.value ? 'rgba(255,255,255,0.08)' : '#F2F4F7',
            strokeDashArray: 4,
        },
        xaxis: {
            categories: props.labels,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            labels: {
                style: {
                    colors: isDark.value ? '#98A2B3' : '#667085',
                    fontSize: '12px',
                },
            },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                formatter: (value) => Math.round(value).toString(),
                style: {
                    colors: isDark.value ? '#98A2B3' : '#667085',
                    fontSize: '12px',
                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        tooltip: {
            theme: isDark.value ? 'dark' : 'light',
            x: {
                show: true,
            },
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.22,
                opacityTo: 0.02,
                stops: [0, 100],
            },
        },
    });

    await chartInstance.render();
};

onMounted(async () => {
    syncTheme();
    observer = new MutationObserver(() => {
        syncTheme();
    });
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });
    await renderChart();
});

watch(
    () => [props.labels, props.series, isDark.value],
    async () => {
        await renderChart();
    },
    { deep: true },
);

onBeforeUnmount(() => {
    observer?.disconnect();
    chartInstance?.destroy();
});
</script>

<template>
    <div ref="chartElement" class="min-h-[320px]"></div>
</template>
