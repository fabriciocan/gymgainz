import { defineStore } from 'pinia'

export interface TrainingSession {
  id: number
  workout_id: number
  workout?: { id: number; name: string; exercises?: WorkoutExerciseSummary[] }
  date: string
  duration_minutes: number | null
  notes: string | null
  sets?: SessionSet[]
  created_at: string
}

export interface WorkoutExerciseSummary {
  id: number
  exercise?: { id: number; name: string }
  sets: number
  reps: number
}

export interface SessionSet {
  id: number
  session_id: number
  workout_exercise_id: number
  set_number: number
  reps_done: number
  weight_kg: number
  created_at: string
}

export const useSessionStore = defineStore('session', () => {
  const { api } = useApi()
  const sessions = ref<TrainingSession[]>([])
  const currentSession = ref<TrainingSession | null>(null)
  const previousSession = ref<TrainingSession | null>(null)
  const loading = ref(false)

  const fetchSessions = async () => {
    loading.value = true
    try {
      const res = await api<{ data: TrainingSession[] }>('/sessions')
      sessions.value = res.data
    } finally {
      loading.value = false
    }
  }

  const startSession = async (workoutId: number, date: string) => {
    const res = await api<{ data: TrainingSession }>('/sessions', {
      method: 'POST',
      body: { workout_id: workoutId, date },
    })
    currentSession.value = res.data
    return res.data
  }

  const fetchSession = async (id: number) => {
    const res = await api<{ data: TrainingSession }>(`/sessions/${id}`)
    currentSession.value = res.data
    return res.data
  }

  const fetchPrevious = async (sessionId: number) => {
    const res = await api<TrainingSession | { data: null }>(`/sessions/${sessionId}/previous`)
    previousSession.value = (res as any).data ?? null
    return previousSession.value
  }

  const finishSession = async (id: number, data: { duration_minutes: number; notes?: string }) => {
    const res = await api<{ data: TrainingSession }>(`/sessions/${id}/finish`, { method: 'PUT', body: data })
    currentSession.value = res.data
    return res.data
  }

  const addSet = async (sessionId: number, data: object) => {
    const res = await api<{ data: SessionSet }>(`/sessions/${sessionId}/sets`, { method: 'POST', body: data })
    if (currentSession.value) {
      currentSession.value.sets = [...(currentSession.value.sets ?? []), res.data]
    }
    return res.data
  }

  const updateSet = async (sessionId: number, setId: number, data: object) => {
    const res = await api<{ data: SessionSet }>(`/sessions/${sessionId}/sets/${setId}`, { method: 'PUT', body: data })
    if (currentSession.value?.sets) {
      const idx = currentSession.value.sets.findIndex(s => s.id === setId)
      if (idx !== -1) currentSession.value.sets[idx] = res.data
    }
    return res.data
  }

  const deleteSet = async (sessionId: number, setId: number) => {
    await api(`/sessions/${sessionId}/sets/${setId}`, { method: 'DELETE' })
    if (currentSession.value?.sets) {
      currentSession.value.sets = currentSession.value.sets.filter(s => s.id !== setId)
    }
  }

  const deleteSession = async (id: number) => {
    await api(`/sessions/${id}`, { method: 'DELETE' })
    sessions.value = sessions.value.filter(s => s.id !== id)
  }

  return {
    sessions, currentSession, previousSession, loading,
    fetchSessions, startSession, fetchSession, fetchPrevious,
    finishSession, addSet, updateSet, deleteSet, deleteSession,
  }
})
