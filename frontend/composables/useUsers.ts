import type { User, PaginationMeta, CreateUserPayload, MassUpdateRolesPayload } from '~/types'

export const useUsers = () => {
  const { api } = useApi()

  const users = ref<User[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref<PaginationMeta>({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
  })

  const fetchUsers = async (params?: { search?: string; page?: number; per_page?: number }) => {
    loading.value = true
    error.value = null

    try {
      const response = await api<{ users: User[]; meta: PaginationMeta }>('/users', { params })
      users.value = response.users
      pagination.value = response.meta
    } catch (e: any) {
      error.value = e.data?.message || 'Erro ao carregar usuários'
    } finally {
      loading.value = false
    }
  }

  const createUser = async (userData: CreateUserPayload) => {
    loading.value = true
    try {
      const response = await api<{ user: User; message: string }>('/users', {
        method: 'POST',
        body: userData,
      })
      return response
    } finally {
      loading.value = false
    }
  }

  const getUser = async (userId: number) => {
    const response = await api<{ user: User }>(`/users/${userId}`)
    return response.user
  }

  const updateUserRoles = async (userId: number, roles: number[]) => {
    const response = await api<{ user: User; message: string }>(`/users/${userId}/roles`, {
      method: 'PUT',
      body: { roles },
    })
    return response
  }

  const toggleUserActive = async (userId: number) => {
    const response = await api<{ user: User; message: string }>(`/users/${userId}/toggle-active`, {
      method: 'PUT',
    })
    return response
  }

  const deleteUser = async (userId: number) => {
    const response = await api<{ message: string }>(`/users/${userId}`, {
      method: 'DELETE',
    })
    return response
  }

  const massUpdateRoles = async (data: MassUpdateRolesPayload) => {
    const response = await api<{ message: string; updated_count: number }>('/users/mass-update-roles', {
      method: 'POST',
      body: data,
    })
    return response
  }

  const checkEmail = async (email: string) => {
    const response = await api<{ exists: boolean }>('/check-email', {
      params: { email },
    })
    return response.exists
  }

  const impersonateUser = async (userId: number) => {
    const response = await api<{ user: User; message: string; impersonating: boolean }>(
      `/impersonate/${userId}`,
      { method: 'POST' }
    )
    return response
  }

  return {
    users,
    loading,
    error,
    pagination,
    fetchUsers,
    createUser,
    getUser,
    updateUserRoles,
    toggleUserActive,
    deleteUser,
    massUpdateRoles,
    checkEmail,
    impersonateUser,
  }
}
