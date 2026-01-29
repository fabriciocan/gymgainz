import type { Role } from '~/types'

export const useRoles = () => {
  const { api } = useApi()

  const roles = ref<Role[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchRoles = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await api<{ roles: Role[] }>('/roles')
      roles.value = response.roles
    } catch (e: any) {
      error.value = e.data?.message || 'Erro ao carregar permissões'
    } finally {
      loading.value = false
    }
  }

  const createRole = async (name: string, description?: string) => {
    const response = await api<{ role: Role; message: string }>('/roles', {
      method: 'POST',
      body: { name, description },
    })
    return response
  }

  const deleteRole = async (roleId: number) => {
    const response = await api<{ message: string }>(`/roles/${roleId}`, {
      method: 'DELETE',
    })
    return response
  }

  return {
    roles,
    loading,
    error,
    fetchRoles,
    createRole,
    deleteRole,
  }
}
