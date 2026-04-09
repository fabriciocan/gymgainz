import { defineStore } from 'pinia'

export interface Workout {
  id: number
  name: string
  description: string | null
  week_days: number[]
  exercises?: WorkoutExercise[]
  workout_exercises_count?: number
  created_at: string
  updated_at: string
}

export interface WorkoutExercise {
  id: number
  workout_id: number
  exercise: Exercise
  sets: number
  reps: number
  rest_seconds: number
  order: number
}

export interface Exercise {
  id: number
  name: string
  muscle_group: string
  is_global: boolean
  user_id: number | null
}

export const useWorkoutStore = defineStore('workout', () => {
  const { api } = useApi()
  const workouts = ref<Workout[]>([])
  const loading = ref(false)

  const fetchWorkouts = async () => {
    loading.value = true
    try {
      const res = await api<{ data: Workout[] }>('/workouts')
      workouts.value = res.data
    } finally {
      loading.value = false
    }
  }

  const fetchWorkout = async (id: number) => {
    const res = await api<{ data: Workout }>(`/workouts/${id}`)
    return res.data
  }

  const createWorkout = async (data: { name: string; description?: string }) => {
    const res = await api<{ data: Workout }>('/workouts', { method: 'POST', body: data })
    workouts.value.unshift(res.data)
    return res.data
  }

  const updateWorkout = async (id: number, data: Partial<Workout>) => {
    const res = await api<{ data: Workout }>(`/workouts/${id}`, { method: 'PUT', body: data })
    const idx = workouts.value.findIndex(w => w.id === id)
    if (idx !== -1) workouts.value[idx] = res.data
    return res.data
  }

  const deleteWorkout = async (id: number) => {
    await api(`/workouts/${id}`, { method: 'DELETE' })
    workouts.value = workouts.value.filter(w => w.id !== id)
  }

  const addExercise = async (workoutId: number, data: object) => {
    const res = await api<{ data: WorkoutExercise }>(`/workouts/${workoutId}/exercises`, { method: 'POST', body: data })
    return res.data
  }

  const updateExercise = async (workoutId: number, weId: number, data: object) => {
    const res = await api<{ data: WorkoutExercise }>(`/workouts/${workoutId}/exercises/${weId}`, { method: 'PUT', body: data })
    return res.data
  }

  const removeExercise = async (workoutId: number, weId: number) => {
    await api(`/workouts/${workoutId}/exercises/${weId}`, { method: 'DELETE' })
  }

  const reorderExercises = async (workoutId: number, order: number[]) => {
    await api(`/workouts/${workoutId}/exercises/reorder`, { method: 'POST', body: { order } })
  }

  return {
    workouts, loading,
    fetchWorkouts, fetchWorkout,
    createWorkout, updateWorkout, deleteWorkout,
    addExercise, updateExercise, removeExercise, reorderExercises,
  }
})
