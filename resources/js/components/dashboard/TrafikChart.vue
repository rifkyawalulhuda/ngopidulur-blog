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
let resizeHandler = null;

const syncTheme = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

const visibleLabelIndexes = () => {
    const total = props.labels.length;

    if (total <= 6) {
        return new Set(props.labels.map((_, index) => index));
    }

    const isCompactViewport = window.innerWidth < 768;
    const step = isCompactViewport ? 6 : 4;
    const indexes = new Set([0, total - 1]);

    for (let index = step; index < total - 1; index += step) {
        indexes.add(index);
    }

    return indexes;
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
                hideOverlappingLabels: true,
                rotate: 0,
                trim: true,
                minHeight: 48,
                maxHeight: 48,
                offsetY: 6,
                formatter: (value, _timestamp, opts) => {
                    const indexes = visibleLabelIndexes();
                    const index = opts?.dataPointIndex ?? opts?.i ?? 0;

                    return indexes.has(index) ? value : '';
                },
                style: {
                    colors: isDark.value ? '#98A2B3' : '#667085',
                    fontSize: '11px',
                    fontWeight: 500,
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
    resizeHandler = async () => {
        await renderChart();
    };
    window.addEventListener('resize', resizeHandler);
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
    if (resizeHandler) {
        window.removeEventListener('resize', resizeHandler);
    }
    chartInstance?.destroy();
});
</script>

<template>
    <div ref="chartElement" class="min-h-[320px]"></div>
</template>
