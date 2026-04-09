export const useApi = () => {
  const config = useRuntimeConfig()
  const baseUrl = config.public.apiBaseUrl

  const getBase = () => {
    if (baseUrl) return baseUrl
    if (typeof window !== 'undefined') return window.location.origin
    return ''
  }

  const api = async <T>(
    endpoint: string,
    options: {
      method?: 'GET' | 'POST' | 'PUT' | 'DELETE'
      body?: any
      params?: Record<string, any>
      silent?: boolean
    } = {}
  ): Promise<T> => {
    const { method = 'GET', body, params, silent = false } = options
    const toast = useToast()
    const token = useCookie('auth_token')

    const url = new URL(`${getBase()}/api${endpoint}`)
    if (params) {
      Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          url.searchParams.append(key, String(value))
        }
      })
    }

    const headers: Record<string, string> = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    }

    if (token.value) {
      headers['Authorization'] = `Bearer ${token.value}`
    }

    try {
      return await $fetch<T>(url.toString(), {
        method,
        body,
        headers,
      })
    } catch (err: any) {
      const status = err?.status ?? err?.response?.status
      const message = err?.data?.message ?? err?.message ?? 'Erro desconhecido'

      if (status === 403 && err?.data?.error === 'subscription_required') {
        await navigateTo('/subscribe')
        throw err
      }

      if (status === 401) {
        await navigateTo('/login')
        throw err
      }

      if (!silent) {
        toast.add({
          title: 'Erro',
          description: message,
          color: 'red',
        })
      }

      throw err
    }
  }

  return { api }
}
