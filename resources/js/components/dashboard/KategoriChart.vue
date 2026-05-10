<script setup>
import ApexCharts from 'apexcharts';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

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

const palette = ['#465FFF', '#7592FF', '#9CB2FF', '#C2D1FF', '#7A5AF8', '#12B76A'];
const chartElement = ref(null);
const isDark = ref(false);
let chartInstance = null;
let observer = null;

const total = computed(() => props.series.reduce((sum, value) => sum + Number(value || 0), 0));

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
            type: 'donut',
            height: 320,
            toolbar: {
                show: false,
            },
            fontFamily: 'Satoshi, Inter, sans-serif',
            foreColor: isDark.value ? '#98A2B3' : '#667085',
        },
        labels: props.labels,
        series: props.series,
        colors: palette,
        legend: {
            position: 'bottom',
            fontSize: '13px',
            labels: {
                colors: isDark.value ? '#D0D5DD' : '#344054',
            },
            itemMargin: {
                horizontal: 12,
                vertical: 6,
            },
        },
        stroke: {
            colors: [isDark.value ? '#101828' : '#FFFFFF'],
            width: 4,
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            color: isDark.value ? '#98A2B3' : '#667085',
                        },
                        value: {
                            show: true,
                            color: isDark.value ? '#F9FAFB' : '#101828',
                            fontSize: '28px',
                            fontWeight: 700,
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            color: isDark.value ? '#98A2B3' : '#667085',
                            formatter: () => total.value.toString(),
                        },
                    },
                },
            },
        },
        dataLabels: {
            enabled: false,
        },
        tooltip: {
            theme: isDark.value ? 'dark' : 'light',
            y: {
                formatter: (value) => {
                    const percentage = total.value > 0 ? ((value / total.value) * 100).toFixed(1) : '0.0';

                    return `${value} tulisan (${percentage}%)`;
                },
            },
        },
        states: {
            hover: {
                filter: {
                    type: 'none',
                },
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
