<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-5">
      <NuxtLink to="/workouts" class="text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </NuxtLink>
      <div class="flex-1">
        <h1
          v-if="!editingName"
          class="text-[20px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white cursor-pointer"
          @click="editingName = true"
        >
          {{ workout?.name }}
        </h1>
        <input
          v-else
          v-model="nameInput"
          class="text-[20px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white bg-transparent border-b-2 border-[#0A0A0A] dark:border-white outline-none w-full"
          autofocus
          @blur="saveName"
          @keyup.enter="saveName"
        />
      </div>
      <NuxtLink
        :to="`/session/${route.params.id}`"
        class="flex items-center gap-1.5 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl px-3 py-2 text-[13px] font-medium"
      >
        <UIcon name="i-heroicons-play" class="w-3.5 h-3.5" />
        Treinar
      </NuxtLink>
    </div>

    <div v-if="loading" class="space-y-2.5">
      <div class="h-14 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" v-for="i in 4" :key="i" />
    </div>

    <div v-else>
      <!-- Days of week -->
      <div class="mb-4">
        <p class="text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-2">Dias da semana</p>
        <div class="flex gap-2">
          <button
            v-for="day in weekDays"
            :key="day.value"
            type="button"
            class="flex-1 py-2 rounded-xl text-[12px] font-medium transition-all"
            :class="selectedDays.includes(day.value)
              ? 'bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A]'
              : 'bg-[#F2F2F2] dark:bg-[#1A1A1A] text-[#8A8A8A]'"
            @click="toggleDay(day.value)"
          >
            {{ day.short }}
          </button>
        </div>
      </div>
      <!-- Exercise list -->
      <div class="border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-2xl mb-4 divide-y divide-[#F2F2F2] dark:divide-[#2A2A2A] overflow-hidden">
        <ExerciseRow
          v-for="we in workout?.exercises"
          :key="we.id"
          :exercise="we"
        >
          <button
            class="p-1.5 text-[#CCCCCC] dark:text-[#444] hover:text-red-500 dark:hover:text-red-400 transition-colors"
            @click="removeExercise(we.id)"
          >
            <UIcon name="i-heroicons-trash" class="w-4 h-4" />
          </button>
        </ExerciseRow>
        <div v-if="!workout?.exercises?.length" class="p-5 text-center text-[13px] text-[#8A8A8A]">
          Nenhum exercício. Adicione abaixo.
        </div>
      </div>

      <!-- Add exercise -->
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
        <h3 class="text-[14px] font-medium text-[#0A0A0A] dark:text-white mb-3">Adicionar exercício</h3>
        <div class="space-y-3">
          <div class="flex gap-2">
            <USelectMenu
              v-model="newEx.exercise"
              :options="exerciseOptions"
              option-attribute="name"
              searchable
              placeholder="Selecionar exercício..."
              class="flex-1"
            />
            <UTooltip text="Criar exercício">
              <NuxtLink
                to="/exercises"
                class="flex items-center justify-center w-10 h-10 rounded-xl border border-[#E5E5E5] dark:border-[#2A2A2A] text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors"
              >
                <UIcon name="i-heroicons-plus" class="w-4 h-4" />
              </NuxtLink>
            </UTooltip>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Séries</label>
              <UInput v-model.number="newEx.sets" type="number" min="1" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Reps</label>
              <UInput v-model.number="newEx.reps" type="number" min="1" />
            </div>
            <div>
              <label class="block text-[11px] text-[#8A8A8A] mb-1">Descanso</label>
              <UInput v-model.number="newEx.rest_seconds" type="number" min="0" />
            </div>
          </div>
          <button
            class="w-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl py-3 text-[14px] font-medium transition-opacity disabled:opacity-50"
            :disabled="addingEx"
            @click="addExercise"
          >
            {{ addingEx ? '...' : 'Adicionar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const route = useRoute()
const workoutStore = useWorkoutStore()
const { api } = useApi()
const loading = ref(true)
const workout = ref<any>(null)
const exercises = ref<any[]>([])
const editingName = ref(false)
const nameInput = ref('')
const addingEx = ref(false)
const newEx = reactive({ exercise: null as any, sets: 3, reps: 10, rest_seconds: 60 })

const weekDays = [
  { value: 1, short: 'Seg' },
  { value: 2, short: 'Ter' },
  { value: 3, short: 'Qua' },
  { value: 4, short: 'Qui' },
  { value: 5, short: 'Sex' },
  { value: 6, short: 'Sáb' },
  { value: 0, short: 'Dom' },
]
const selectedDays = ref<number[]>([])

const exerciseOptions = computed(() => exercises.value.map(e => ({ ...e, label: e.name })))

onMounted(async () => {
  const [w, ex] = await Promise.all([
    workoutStore.fetchWorkout(Number(route.params.id)),
    api<{ data: any[] }>('/exercises'),
  ])
  workout.value = w
  exercises.value = ex.data
  nameInput.value = w.name
  selectedDays.value = w.week_days ?? []
  loading.value = false
})

const toggleDay = async (value: number) => {
  const idx = selectedDays.value.indexOf(value)
  if (idx === -1) selectedDays.value.push(value)
  else selectedDays.value.splice(idx, 1)
  await workoutStore.updateWorkout(workout.value.id, { week_days: selectedDays.value })
}

const saveName = async () => {
  editingName.value = false
  if (nameInput.value.trim() && nameInput.value !== workout.value.name) {
    await workoutStore.updateWorkout(workout.value.id, { name: nameInput.value })
    workout.value.name = nameInput.value
  }
}

const addExercise = async () => {
  if (!newEx.exercise) return
  addingEx.value = true
  try {
    const we = await workoutStore.addExercise(workout.value.id, {
      exercise_id: newEx.exercise.id,
      sets: newEx.sets,
      reps: newEx.reps,
      rest_seconds: newEx.rest_seconds,
    })
    workout.value.exercises = [...(workout.value.exercises ?? []), { ...we, exercise: newEx.exercise }]
    newEx.exercise = null
  } finally {
    addingEx.value = false
  }
}

const removeExercise = async (weId: number) => {
  await workoutStore.removeExercise(workout.value.id, weId)
  workout.value.exercises = workout.value.exercises.filter((e: any) => e.id !== weId)
}
</script>
