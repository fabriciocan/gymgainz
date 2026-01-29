export interface Role {
  id: number
  name: string
  description?: string
  users_count?: number
}

export interface User {
  id: number
  name: string
  email: string
  username: string
  department?: string
  manager?: string
  employee_number?: string
  is_externo: boolean
  active: boolean
  roles: Role[]
  created_at: string
  updated_at: string
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

export interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

export interface CreateUserPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
  roles: number[]
}

export interface MassUpdateRolesPayload {
  roles: number[]
  user_ids?: number[]
  select_all?: boolean
  search?: string
}
