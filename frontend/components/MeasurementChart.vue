<template>
  <div class="relative h-48">
    <canvas ref="canvas" />
  </div>
</template>

<script setup lang="ts">
import { Chart, LineController, LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Filler } from 'chart.js'

Chart.register(LineController, LineElement, PointElement, LinearScale, CategoryScale, Tooltip, Filler)

const props = defineProps<{
  data: { date: string; value: number | null }[]
  label: string
  unit?: string
  color?: string
}>()

const canvas = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null
const colorMode = useColorMode()

const lineColor = computed(() => {
  if (colorMode.value === 'dark') return '#FFFFFF'
  return props.color ?? '#0A0A0A'
})

const buildChart = () => {
  if (!canvas.value) return
  chart?.destroy()

  const isDark = colorMode.value === 'dark'
  const tickColor = isDark ? '#555555' : '#8A8A8A'
  const c = lineColor.value
  const filtered = props.data.filter(d => d.value !== null)

  chart = new Chart(canvas.value, {
    type: 'line',
    data: {
      labels: filtered.map(d => d.date),
      datasets: [{
        label: `${props.label}${props.unit ? ` (${props.unit})` : ''}`,
        data: filtered.map(d => d.value as number),
        borderColor: c,
        backgroundColor: c + '18',
        fill: true,
        tension: 0.3,
        pointBackgroundColor: c,
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 2,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { tooltip: { mode: 'index', intersect: false } },
      scales: {
        x: {
          ticks: { color: tickColor, font: { size: 11 } },
          grid: { display: false },
          border: { display: false },
        },
        y: {
          ticks: { color: tickColor, font: { size: 11 } },
          grid: { display: false },
          border: { display: false },
        },
      },
    },
  })
}

watch(() => props.data, buildChart, { deep: true })
watch(() => colorMode.value, buildChart)
onMounted(buildChart)
onUnmounted(() => chart?.destroy())
</script>
