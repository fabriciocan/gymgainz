import { defineStore } from 'pinia'

export interface Subscription {
  id: number
  status: 'active' | 'cancelled' | 'expired'
  plan: string
  amount_cents: number
  current_period_start: string | null
  current_period_end: string | null
  is_active: boolean
}

export const useSubscriptionStore = defineStore('subscription', () => {
  const { api } = useApi()
  const hasAccess = ref(false)
  const trialEndsAt = ref<string | null>(null)
  const subscription = ref<Subscription | null>(null)
  const loading = ref(false)

  const fetchStatus = async () => {
    loading.value = true
    try {
      const res = await api<{ has_access: boolean; trial_ends_at: string | null; subscription: Subscription | null }>('/subscription/status')
      hasAccess.value = res.has_access
      trialEndsAt.value = res.trial_ends_at
      subscription.value = res.subscription
    } finally {
      loading.value = false
    }
  }

  const subscribe = async (cellphone: string, taxId: string) => {
    const res = await api<{ billing_url: string | null; subscription: Subscription }>('/subscription/create', {
      method: 'POST',
      body: { cellphone, tax_id: taxId },
    })
    subscription.value = res.subscription
    hasAccess.value = true
    return res
  }

  return { hasAccess, trialEndsAt, subscription, loading, fetchStatus, subscribe }
})
