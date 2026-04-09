import { defineStore } from 'pinia'

export interface BodyMeasurement {
  id: number
  date: string
  weight_kg: number | null
  body_fat_pct: number | null
  bicep_left_cm: number | null
  bicep_right_cm: number | null
  chest_cm: number | null
  waist_cm: number | null
  abdomen_cm: number | null
  hip_cm: number | null
  thigh_left_cm: number | null
  thigh_right_cm: number | null
  calf_left_cm: number | null
  calf_right_cm: number | null
  notes: string | null
}

export const useMeasurementStore = defineStore('measurement', () => {
  const { api } = useApi()
  const measurements = ref<BodyMeasurement[]>([])
  const latest = ref<BodyMeasurement | null>(null)
  const loading = ref(false)

  const fetchMeasurements = async () => {
    loading.value = true
    try {
      const res = await api<{ data: BodyMeasurement[] }>('/measurements')
      measurements.value = res.data
    } finally {
      loading.value = false
    }
  }

  const fetchLatest = async () => {
    const res = await api<BodyMeasurement | null>('/measurements/latest')
    latest.value = res
    return res
  }

  const create = async (data: Partial<BodyMeasurement>) => {
    const res = await api<{ data: BodyMeasurement }>('/measurements', { method: 'POST', body: data })
    measurements.value.unshift(res.data)
    latest.value = res.data
    return res.data
  }

  const update = async (id: number, data: Partial<BodyMeasurement>) => {
    const res = await api<{ data: BodyMeasurement }>(`/measurements/${id}`, { method: 'PUT', body: data })
    const idx = measurements.value.findIndex(m => m.id === id)
    if (idx !== -1) measurements.value[idx] = res.data
    return res.data
  }

  const remove = async (id: number) => {
    await api(`/measurements/${id}`, { method: 'DELETE' })
    measurements.value = measurements.value.filter(m => m.id !== id)
  }

  return { measurements, latest, loading, fetchMeasurements, fetchLatest, create, update, remove }
})
