<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-6">
      <NuxtLink to="/workouts" class="text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </NuxtLink>
      <h1 class="text-[20px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">Novo Treino</h1>
    </div>

    <div class="space-y-4">
      <div>
        <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Nome do treino</label>
        <input
          v-model="form.name"
          type="text"
          placeholder="ex: Treino A — Peito e Tríceps"
          class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white placeholder-[#CCCCCC] dark:placeholder-[#444] rounded-xl px-4 py-3 text-[15px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors"
        />
      </div>

      <div>
        <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Grupos musculares (opcional)</label>
        <input
          v-model="form.description"
          type="text"
          placeholder="ex: Peito · Tríceps · Ombro"
          class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white placeholder-[#CCCCCC] dark:placeholder-[#444] rounded-xl px-4 py-3 text-[15px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors"
        />
      </div>

      <div>
        <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-2">Dias da semana</label>
        <div class="flex gap-2">
          <button
            v-for="day in weekDays"
            :key="day.value"
            type="button"
            class="flex-1 py-2 rounded-xl text-[12px] font-medium transition-all"
            :class="form.week_days.includes(day.value)
              ? 'bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A]'
              : 'bg-[#F2F2F2] dark:bg-[#1A1A1A] text-[#8A8A8A]'"
            @click="toggleDay(day.value)"
          >
            {{ day.short }}
          </button>
        </div>
      </div>

      <button
        class="w-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl py-3.5 text-[15px] font-medium tracking-[-0.01em] transition-all active:scale-[0.98] disabled:opacity-50 mt-2"
        :disabled="saving"
        @click="create"
      >
        {{ saving ? '...' : 'Criar treino' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const workoutStore = useWorkoutStore()
const saving = ref(false)
const form = reactive({ name: '', description: '', week_days: [] as number[] })

const weekDays = [
  { value: 1, short: 'Seg' },
  { value: 2, short: 'Ter' },
  { value: 3, short: 'Qua' },
  { value: 4, short: 'Qui' },
  { value: 5, short: 'Sex' },
  { value: 6, short: 'Sáb' },
  { value: 0, short: 'Dom' },
]

const toggleDay = (value: number) => {
  const idx = form.week_days.indexOf(value)
  if (idx === -1) form.week_days.push(value)
  else form.week_days.splice(idx, 1)
}

const create = async () => {
  if (!form.name.trim()) return
  saving.value = true
  try {
    const workout = await workoutStore.createWorkout(form)
    await navigateTo(`/workouts/${workout.id}`)
  } finally {
    saving.value = false
  }
}
</script>
