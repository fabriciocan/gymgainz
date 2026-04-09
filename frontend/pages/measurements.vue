<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <div class="flex items-center justify-between mb-5">
      <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">Medidas</h1>
      <button
        class="flex items-center gap-1.5 text-[13px] font-medium text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors"
        @click="showForm = true"
      >
        <UIcon name="i-heroicons-plus" class="w-4 h-4" />
        Nova
      </button>
    </div>

    <!-- Charts -->
    <div v-if="store.measurements.length >= 2" class="space-y-3 mb-5">
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 border border-[#E5E5E5] dark:border-[#2A2A2A]">
        <p class="text-[11px] font-medium tracking-[0.08em] uppercase text-[#8A8A8A] mb-3">Peso (kg)</p>
        <MeasurementChart :data="chartFor('weight_kg')" label="Peso" unit="kg" color="#0A0A0A" />
      </div>
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 border border-[#E5E5E5] dark:border-[#2A2A2A]">
        <p class="text-[11px] font-medium tracking-[0.08em] uppercase text-[#8A8A8A] mb-3">Cintura (cm)</p>
        <MeasurementChart :data="chartFor('waist_cm')" label="Cintura" unit="cm" color="#8A8A8A" />
      </div>
    </div>

    <!-- Form modal -->
    <UModal v-model="showForm">
      <div class="p-5 space-y-4">
        <h2 class="text-[18px] font-semibold text-[#0A0A0A] dark:text-white">Nova Medida</h2>

        <div>
          <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Data</label>
          <UInput v-model="form.date" type="date" />
        </div>

        <div>
          <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Geral</p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Peso (kg)</label>
              <UInput v-model.number="form.weight_kg" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">% Gordura</label>
              <UInput v-model.number="form.body_fat_pct" type="number" step="0.1" placeholder="0.0" />
            </div>
          </div>
        </div>

        <div>
          <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Tronco</p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Peito (cm)</label>
              <UInput v-model.number="form.chest_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Cintura (cm)</label>
              <UInput v-model.number="form.waist_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Abdômen (cm)</label>
              <UInput v-model.number="form.abdomen_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Quadril (cm)</label>
              <UInput v-model.number="form.hip_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
          </div>
        </div>

        <div>
          <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Braços (cm)</p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Bíceps Esq.</label>
              <UInput v-model.number="form.bicep_left_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Bíceps Dir.</label>
              <UInput v-model.number="form.bicep_right_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
          </div>
        </div>

        <div>
          <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Pernas (cm)</p>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Coxa Esq.</label>
              <UInput v-model.number="form.thigh_left_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Coxa Dir.</label>
              <UInput v-model.number="form.thigh_right_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Panturrilha Esq.</label>
              <UInput v-model.number="form.calf_left_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Panturrilha Dir.</label>
              <UInput v-model.number="form.calf_right_cm" type="number" step="0.1" placeholder="0.0" />
            </div>
          </div>
        </div>

        <div>
          <label class="block text-[11px] text-[#8A8A8A] mb-1">Observações</label>
          <UTextarea v-model="form.notes" rows="2" />
        </div>

        <div class="flex gap-2 pt-1">
          <button class="flex-1 py-2.5 rounded-xl text-[14px] text-[#8A8A8A] border border-[#E5E5E5] dark:border-[#2A2A2A]" @click="showForm = false">Cancelar</button>
          <button
            class="flex-1 py-2.5 rounded-xl text-[14px] font-medium bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] disabled:opacity-50"
            :disabled="saving"
            @click="save"
          >
            {{ saving ? '...' : 'Salvar' }}
          </button>
        </div>
      </div>
    </UModal>

    <!-- List -->
    <div v-if="loading" class="space-y-2.5">
      <div class="h-20 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" v-for="i in 3" :key="i" />
    </div>

    <div v-else class="space-y-2.5">
      <div
        v-for="m in store.measurements"
        :key="m.id"
        class="border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-2xl p-4 bg-white dark:bg-[#0A0A0A]"
      >
        <div class="flex items-center justify-between mb-3">
          <span class="text-[13px] font-medium text-[#8A8A8A]">{{ formatDate(m.date) }}</span>
          <button class="p-1 text-[#CCCCCC] dark:text-[#444] hover:text-red-500 transition-colors" @click="remove(m.id)">
            <UIcon name="i-heroicons-trash" class="w-4 h-4" />
          </button>
        </div>

        <div class="grid grid-cols-3 gap-2">
          <div v-if="m.weight_kg != null">
            <p class="text-[10px] text-[#8A8A8A]">Peso</p>
            <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white font-mono">{{ m.weight_kg }}kg</p>
          </div>
          <div v-if="m.body_fat_pct != null">
            <p class="text-[10px] text-[#8A8A8A]">Gordura</p>
            <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white font-mono">{{ m.body_fat_pct }}%</p>
          </div>
          <div v-if="m.chest_cm != null">
            <p class="text-[10px] text-[#8A8A8A]">Peito</p>
            <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white font-mono">{{ m.chest_cm }}cm</p>
          </div>
          <div v-if="m.waist_cm != null">
            <p class="text-[10px] text-[#8A8A8A]">Cintura</p>
            <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white font-mono">{{ m.waist_cm }}cm</p>
          </div>
          <div v-if="m.abdomen_cm != null">
            <p class="text-[10px] text-[#8A8A8A]">Abdômen</p>
            <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white font-mono">{{ m.abdomen_cm }}cm</p>
          </div>
          <div v-if="m.hip_cm != null">
            <p class="text-[10px] text-[#8A8A8A]">Quadril</p>
            <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white font-mono">{{ m.hip_cm }}cm</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const store = useMeasurementStore()
const loading = ref(true)
const showForm = ref(false)
const saving = ref(false)

const emptyForm = () => ({
  date: new Date().toISOString().split('T')[0],
  weight_kg: null as number | null,
  body_fat_pct: null as number | null,
  bicep_left_cm: null as number | null,
  bicep_right_cm: null as number | null,
  chest_cm: null as number | null,
  waist_cm: null as number | null,
  abdomen_cm: null as number | null,
  hip_cm: null as number | null,
  thigh_left_cm: null as number | null,
  thigh_right_cm: null as number | null,
  calf_left_cm: null as number | null,
  calf_right_cm: null as number | null,
  notes: '',
})

const form = reactive(emptyForm())

onMounted(async () => {
  await store.fetchMeasurements()
  loading.value = false
})

const formatDate = (date: string) =>
  new Date(date + 'T00:00:00').toLocaleDateString('pt-BR', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })

const chartFor = (field: string) =>
  store.measurements
    .slice()
    .reverse()
    .map(m => ({ date: m.date, value: (m as any)[field] as number | null }))

const save = async () => {
  saving.value = true
  try {
    await store.create(form as any)
    showForm.value = false
    Object.assign(form, emptyForm())
  } finally {
    saving.value = false
  }
}

const remove = async (id: number) => {
  await store.remove(id)
}
</script>
