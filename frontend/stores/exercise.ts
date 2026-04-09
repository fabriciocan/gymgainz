import { defineStore } from 'pinia'

export interface Exercise {
  id: number
  name: string
  muscle_group: string
  is_global: boolean
  user_id: number | null
}

export const MUSCLE_GROUPS = [
  { value: 'chest',       label: 'Peito' },
  { value: 'back',        label: 'Costas' },
  { value: 'shoulders',   label: 'Ombros' },
  { value: 'biceps',      label: 'Bíceps' },
  { value: 'triceps',     label: 'Tríceps' },
  { value: 'forearms',    label: 'Antebraço' },
  { value: 'core',        label: 'Core' },
  { value: 'glutes',      label: 'Glúteos' },
  { value: 'quadriceps',  label: 'Quadríceps' },
  { value: 'hamstrings',  label: 'Posteriores' },
  { value: 'calves',      label: 'Panturrilha' },
  { value: 'full_body',   label: 'Corpo todo' },
]

export const muscleGroupLabel = (value: string) =>
  MUSCLE_GROUPS.find(m => m.value === value)?.label ?? value

export const useExerciseStore = defineStore('exercise', () => {
  const { api } = useApi()
  const exercises = ref<Exercise[]>([])
  const loading = ref(false)

  const fetchExercises = async () => {
    loading.value = true
    try {
      const res = await api<{ data: Exercise[] }>('/exercises')
      exercises.value = res.data
    } finally {
      loading.value = false
    }
  }

  const createExercise = async (data: { name: string; muscle_group: string }) => {
    const res = await api<{ data: Exercise }>('/exercises', { method: 'POST', body: data })
    exercises.value.push(res.data)
    return res.data
  }

  const updateExercise = async (id: number, data: { name: string; muscle_group: string }) => {
    const res = await api<{ data: Exercise }>(`/exercises/${id}`, { method: 'PUT', body: data })
    const idx = exercises.value.findIndex(e => e.id === id)
    if (idx !== -1) exercises.value[idx] = res.data
    return res.data
  }

  const deleteExercise = async (id: number) => {
    await api(`/exercises/${id}`, { method: 'DELETE' })
    exercises.value = exercises.value.filter(e => e.id !== id)
  }

  const customExercises = computed(() => exercises.value.filter(e => !e.is_global))

  return {
    exercises, customExercises, loading,
    fetchExercises, createExercise, updateExercise, deleteExercise,
  }
})
