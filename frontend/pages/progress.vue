<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white mb-5">Evolução</h1>

    <div class="mb-5 space-y-3">
      <USelectMenu
        v-model="selectedExercise"
        :options="exercises"
        option-attribute="name"
        searchable
        placeholder="Selecionar exercício..."
      />
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="p in periods"
          :key="p.value"
          class="px-3 py-1.5 rounded-full text-[12px] font-medium transition-all"
          :class="period === p.value
            ? 'bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A]'
            : 'bg-[#F2F2F2] dark:bg-[#1A1A1A] text-[#8A8A8A]'"
          @click="period = p.value"
        >
          {{ p.label }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="space-y-3">
      <div class="h-56 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" />
    </div>

    <div v-else-if="selectedExercise && chartData.length >= 2" class="space-y-4">
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 border border-[#E5E5E5] dark:border-[#2A2A2A]">
        <p class="text-[11px] font-medium tracking-[0.08em] uppercase text-[#8A8A8A] mb-3">Peso máximo por sessão (kg)</p>
        <ProgressChart
          :data="chartData.map(d => ({ date: d.date, value: d.max_weight }))"
          label="Peso máximo"
          color="#0A0A0A"
        />
      </div>
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 border border-[#E5E5E5] dark:border-[#2A2A2A]">
        <p class="text-[11px] font-medium tracking-[0.08em] uppercase text-[#8A8A8A] mb-3">Repetições médias por sessão</p>
        <ProgressChart
          :data="chartData.map(d => ({ date: d.date, value: d.avg_reps }))"
          label="Reps médias"
          color="#8A8A8A"
        />
      </div>
    </div>

    <div v-else-if="selectedExercise" class="text-center py-12">
      <p class="text-[14px] text-[#8A8A8A]">Dados insuficientes. Registre pelo menos 2 sessões com este exercício.</p>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-[14px] text-[#8A8A8A]">Selecione um exercício para ver a progressão.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const { api } = useApi()
const exercises = ref<any[]>([])
const selectedExercise = ref<any>(null)
const chartData = ref<any[]>([])
const loading = ref(false)
const period = ref('all')

const periods = [
  { label: '30d', value: '30d' },
  { label: '90d', value: '90d' },
  { label: '6m', value: '6m' },
  { label: '1 ano', value: '1y' },
  { label: 'Tudo', value: 'all' },
]

onMounted(async () => {
  const res = await api<{ data: any[] }>('/exercises')
  exercises.value = res.data
})

watch([selectedExercise, period], async () => {
  if (!selectedExercise.value) return
  loading.value = true
  try {
    const res = await api<{ data: any[] }>(`/progress/exercise/${selectedExercise.value.id}`, {
      params: { period: period.value },
    })
    chartData.value = (res as any).data
  } finally {
    loading.value = false
  }
})
</script>
