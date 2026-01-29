import { defineStore } from 'pinia'
import type { User, LoginCredentials } from '~/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const impersonating = ref(false)

  const { api } = useApi()

  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.roles?.some(r => r.name === 'admin') ?? false)

  const hasRole = (roleName: string) => {
    return user.value?.roles?.some(r => r.name === roleName) ?? false
  }

  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    error.value = null

    try {
      const response = await api<{ user: User; message: string }>('/login', {
        method: 'POST',
        body: credentials,
      })

      user.value = response.user
      return response
    } catch (e: any) {
      error.value = e.data?.message || 'Erro ao fazer login'
      throw e
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true

    try {
      await api('/logout', { method: 'POST' })
      user.value = null
      impersonating.value = false
      navigateTo('/login')
    } catch (e: any) {
      console.error('Erro ao fazer logout:', e)
    } finally {
      loading.value = false
    }
  }

  const fetchUser = async () => {
    try {
      const response = await api<{ user: User }>('/user')
      user.value = response.user
      return response.user
    } catch (e) {
      user.value = null
      return null
    }
  }

  const checkImpersonateStatus = async () => {
    try {
      const response = await api<{ impersonating: boolean }>('/impersonate/status')
      impersonating.value = response.impersonating
    } catch (e) {
      impersonating.value = false
    }
  }

  const stopImpersonate = async () => {
    try {
      const response = await api<{ user: User; impersonating: boolean }>('/stop-impersonate', {
        method: 'POST',
      })
      user.value = response.user
      impersonating.value = false
      return response
    } catch (e: any) {
      throw e
    }
  }

  return {
    user,
    loading,
    error,
    impersonating,
    isAuthenticated,
    isAdmin,
    hasRole,
    login,
    logout,
    fetchUser,
    checkImpersonateStatus,
    stopImpersonate,
  }
})
