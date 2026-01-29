export const useApi = () => {
  const config = useRuntimeConfig()
  const baseUrl = config.public.apiBaseUrl

  const getXsrfToken = () => {
    if (typeof document === 'undefined') return null
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/)
    if (match) {
      return decodeURIComponent(match[1])
    }
    return null
  }

  const fetchCsrfToken = async () => {
    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, {
      credentials: 'include',
    })
  }

  const api = async <T>(
    endpoint: string,
    options: {
      method?: 'GET' | 'POST' | 'PUT' | 'DELETE'
      body?: any
      params?: Record<string, any>
    } = {}
  ): Promise<T> => {
    const { method = 'GET', body, params } = options

    // Busca CSRF token para requisições que modificam dados
    if (method !== 'GET') {
      await fetchCsrfToken()
    }

    const url = new URL(`${baseUrl}/api${endpoint}`)
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

    // Adiciona o token XSRF se disponível
    const xsrfToken = getXsrfToken()
    if (xsrfToken) {
      headers['X-XSRF-TOKEN'] = xsrfToken
    }

    return await $fetch<T>(url.toString(), {
      method,
      body,
      credentials: 'include',
      headers,
    })
  }

  return { api, fetchCsrfToken }
}
