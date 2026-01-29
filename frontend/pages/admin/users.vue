<template>
  <div class="w-full max-w-none p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Gerenciamento de Permissões</h1>
    </div>

    <UCard>
      <!-- Header com busca e ações -->
      <template #header>
        <div class="flex flex-col lg:flex-row gap-4 justify-between">
          <!-- Search -->
          <UInput
            v-model="searchQuery"
            icon="i-heroicons-magnifying-glass"
            placeholder="Buscar por nome ou email"
            class="flex-1 max-w-md"
            @input="debouncedSearch"
          />

          <!-- Action buttons -->
          <div class="flex flex-wrap gap-2">
            <UButton
              icon="i-heroicons-user-plus"
              color="purple"
              @click="showCreateUserModal = true"
            >
              Criar Usuário
            </UButton>

            <UButton
              icon="i-heroicons-pencil-square"
              color="green"
              :disabled="selectedUsers.length === 0"
              @click="showMassEditModal = true"
            >
              Edição em Massa ({{ selectedUsers.length }})
            </UButton>

            <UButton
              icon="i-heroicons-shield-check"
              color="blue"
              @click="showRoleManagerModal = true"
            >
              Gerenciar Permissões
            </UButton>
          </div>
        </div>
      </template>

      <!-- Table -->
      <UTable
        :rows="users"
        :columns="columns"
        v-model="selectedUsers"
        @select="onSelect"
      >
        <template #name-data="{ row }">
          <div class="flex items-center gap-2">
            <UAvatar :alt="row.name" size="sm" />
            <span>{{ row.name }}</span>
          </div>
        </template>

        <template #active-data="{ row }">
          <UBadge
            :color="row.active ? 'green' : 'red'"
            variant="subtle"
          >
            {{ row.active ? 'Ativo' : 'Inativo' }}
          </UBadge>
        </template>

        <template #roles-data="{ row }">
          <div class="flex flex-wrap gap-1">
            <UBadge
              v-for="role in row.roles"
              :key="role.id"
              color="blue"
              variant="subtle"
              size="xs"
            >
              {{ role.name }}
            </UBadge>
          </div>
        </template>

        <template #actions-data="{ row }">
          <div class="flex justify-center gap-1">
            <UButton
              icon="i-heroicons-pencil"
              color="yellow"
              variant="ghost"
              size="sm"
              @click="openEditRolesModal(row)"
            />

            <UButton
              v-if="!hasRole(row, 'admin')"
              :icon="row.active ? 'i-heroicons-x-circle' : 'i-heroicons-check-circle'"
              :color="row.active ? 'red' : 'green'"
              variant="ghost"
              size="sm"
              @click="handleToggleActive(row)"
            />

            <UButton
              v-if="!hasRole(row, 'admin') && authStore.isAdmin"
              icon="i-heroicons-eye"
              color="purple"
              variant="ghost"
              size="sm"
              @click="handleImpersonate(row)"
            />

            <UButton
              v-if="!hasRole(row, 'admin')"
              icon="i-heroicons-trash"
              color="red"
              variant="ghost"
              size="sm"
              @click="confirmDeleteUser(row)"
            />
          </div>
        </template>
      </UTable>

      <!-- Pagination -->
      <template #footer>
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            Mostrando {{ users.length }} de {{ pagination.total }} usuários
          </span>
          <UPagination
            v-model="pagination.current_page"
            :page-count="10"
            :total="pagination.total"
            @update:model-value="goToPage"
          />
        </div>
      </template>
    </UCard>

    <!-- Create User Modal -->
    <UModal v-model="showCreateUserModal">
      <UCard>
        <template #header>
          <h3 class="text-lg font-semibold">Criar Usuário</h3>
        </template>

        <form @submit.prevent="handleCreateUser" class="space-y-4">
          <UFormGroup label="Nome" required>
            <UInput v-model="newUser.name" />
          </UFormGroup>

          <UFormGroup label="Email" required>
            <UInput v-model="newUser.email" type="email" />
          </UFormGroup>

          <UFormGroup label="Senha" required>
            <UInput v-model="newUser.password" type="password" />
          </UFormGroup>

          <UFormGroup label="Confirmar Senha" required>
            <UInput v-model="newUser.password_confirmation" type="password" />
          </UFormGroup>

          <UFormGroup label="Permissões">
            <div class="space-y-2 max-h-40 overflow-y-auto">
              <UCheckbox
                v-for="role in roles"
                :key="role.id"
                v-model="newUser.roles"
                :value="role.id"
                :label="role.name"
              />
            </div>
          </UFormGroup>

          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="showCreateUserModal = false">
              Cancelar
            </UButton>
            <UButton type="submit" color="blue">
              Criar
            </UButton>
          </div>
        </form>
      </UCard>
    </UModal>

    <!-- Edit Roles Modal -->
    <UModal v-model="showEditRolesModal">
      <UCard>
        <template #header>
          <h3 class="text-lg font-semibold">Editar Permissões - {{ editingUser?.name }}</h3>
        </template>

        <div class="space-y-4">
          <div class="space-y-2 max-h-60 overflow-y-auto">
            <UCheckbox
              v-for="role in roles"
              :key="role.id"
              v-model="editingRoles"
              :value="role.id"
              :label="role.name"
            />
          </div>

          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="showEditRolesModal = false">
              Cancelar
            </UButton>
            <UButton color="blue" @click="handleUpdateRoles">
              Salvar
            </UButton>
          </div>
        </div>
      </UCard>
    </UModal>

    <!-- Mass Edit Modal -->
    <UModal v-model="showMassEditModal">
      <UCard>
        <template #header>
          <h3 class="text-lg font-semibold">Edição em Massa ({{ selectedUsers.length }} usuários)</h3>
        </template>

        <div class="space-y-4">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Selecione as permissões que serão aplicadas a todos os usuários selecionados.
          </p>

          <div class="space-y-2 max-h-60 overflow-y-auto">
            <UCheckbox
              v-for="role in roles"
              :key="role.id"
              v-model="massEditRoles"
              :value="role.id"
              :label="role.name"
            />
          </div>

          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="showMassEditModal = false">
              Cancelar
            </UButton>
            <UButton color="green" @click="handleMassUpdate">
              Aplicar
            </UButton>
          </div>
        </div>
      </UCard>
    </UModal>

    <!-- Role Manager Modal -->
    <UModal v-model="showRoleManagerModal">
      <UCard>
        <template #header>
          <h3 class="text-lg font-semibold">Gerenciar Permissões</h3>
        </template>

        <div class="space-y-4">
          <!-- Create new role -->
          <div class="flex gap-2">
            <UInput
              v-model="newRoleName"
              placeholder="Nome da nova permissão"
              class="flex-1"
            />
            <UButton color="blue" @click="handleCreateRole">
              Criar
            </UButton>
          </div>

          <!-- Roles list -->
          <div class="space-y-2 max-h-60 overflow-y-auto">
            <div
              v-for="role in roles"
              :key="role.id"
              class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg"
            >
              <span class="text-sm">{{ role.name }}</span>
              <UButton
                v-if="role.name !== 'admin'"
                icon="i-heroicons-trash"
                color="red"
                variant="ghost"
                size="xs"
                @click="handleDeleteRole(role)"
              />
            </div>
          </div>

          <div class="flex justify-end">
            <UButton color="gray" variant="ghost" @click="showRoleManagerModal = false">
              Fechar
            </UButton>
          </div>
        </div>
      </UCard>
    </UModal>

    <!-- Delete Confirmation Modal -->
    <UModal v-model="showDeleteModal">
      <UCard>
        <template #header>
          <h3 class="text-lg font-semibold">Confirmar Exclusão</h3>
        </template>

        <div class="space-y-4">
          <p class="text-gray-600 dark:text-gray-400">
            Tem certeza que deseja excluir o usuário <strong>{{ userToDelete?.name }}</strong>? Esta ação não pode ser desfeita.
          </p>

          <div class="flex justify-end gap-2">
            <UButton color="gray" variant="ghost" @click="showDeleteModal = false">
              Cancelar
            </UButton>
            <UButton color="red" @click="handleDeleteUser">
              Excluir
            </UButton>
          </div>
        </div>
      </UCard>
    </UModal>
  </div>
</template>

<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core'
import type { User, Role } from '~/types'

definePageMeta({
  middleware: 'admin',
  layout: 'default',
  key: route => route.fullPath,
})

const authStore = useAuthStore()
const toast = useToast()
const { users, loading, pagination, fetchUsers, createUser, updateUserRoles, toggleUserActive, deleteUser, massUpdateRoles, impersonateUser } = useUsers()
const { roles, fetchRoles, createRole, deleteRole } = useRoles()

const searchQuery = ref('')
const selectedUsers = ref<number[]>([])

// Modals
const showCreateUserModal = ref(false)
const showEditRolesModal = ref(false)
const showMassEditModal = ref(false)
const showRoleManagerModal = ref(false)
const showDeleteModal = ref(false)

// Modal data
const newUser = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: [] as number[],
})
const editingUser = ref<User | null>(null)
const editingRoles = ref<number[]>([])
const massEditRoles = ref<number[]>([])
const newRoleName = ref('')
const userToDelete = ref<User | null>(null)

// Table columns
const columns = [
  { key: 'name', label: 'Nome' },
  { key: 'email', label: 'Email' },
  { key: 'active', label: 'Status' },
  { key: 'roles', label: 'Permissões' },
  { key: 'actions', label: 'Ações' },
]

// Methods
const debouncedSearch = useDebounceFn(() => {
  selectedUsers.value = []
  fetchUsers({ search: searchQuery.value, page: 1 })
}, 300)

const goToPage = (page: number) => {
  fetchUsers({ search: searchQuery.value, page })
}

const onSelect = (rows: User[]) => {
  selectedUsers.value = rows.map(r => r.id)
}

const hasRole = (user: User, roleName: string) => {
  return user.roles.some(r => r.name === roleName)
}

const showSuccess = (message: string) => {
  toast.add({ title: 'Sucesso', description: message, color: 'green' })
}

const showError = (message: string) => {
  toast.add({ title: 'Erro', description: message, color: 'red' })
}

// Handlers
const handleCreateUser = async () => {
  try {
    const response = await createUser(newUser.value)
    showSuccess(response.message)
    showCreateUserModal.value = false
    newUser.value = { name: '', email: '', password: '', password_confirmation: '', roles: [] }
    await fetchUsers({ search: searchQuery.value, page: pagination.value.current_page })
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao criar usuário')
  }
}

const openEditRolesModal = (user: User) => {
  editingUser.value = user
  editingRoles.value = user.roles.map(r => r.id)
  showEditRolesModal.value = true
}

const handleUpdateRoles = async () => {
  if (!editingUser.value) return
  try {
    const response = await updateUserRoles(editingUser.value.id, editingRoles.value)
    showSuccess(response.message)
    showEditRolesModal.value = false
    await fetchUsers({ search: searchQuery.value, page: pagination.value.current_page })
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao atualizar permissões')
  }
}

const handleToggleActive = async (user: User) => {
  try {
    const response = await toggleUserActive(user.id)
    showSuccess(response.message)
    await fetchUsers({ search: searchQuery.value, page: pagination.value.current_page })
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao alterar status')
  }
}

const handleMassUpdate = async () => {
  try {
    const response = await massUpdateRoles({
      roles: massEditRoles.value,
      user_ids: selectedUsers.value,
    })
    showSuccess(response.message)
    showMassEditModal.value = false
    selectedUsers.value = []
    massEditRoles.value = []
    await fetchUsers({ search: searchQuery.value, page: pagination.value.current_page })
  } catch (e: any) {
    showError(e.data?.message || 'Erro na edição em massa')
  }
}

const handleCreateRole = async () => {
  if (!newRoleName.value.trim()) return
  try {
    const response = await createRole(newRoleName.value)
    showSuccess(response.message)
    newRoleName.value = ''
    await fetchRoles()
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao criar permissão')
  }
}

const handleDeleteRole = async (role: Role) => {
  try {
    const response = await deleteRole(role.id)
    showSuccess(response.message)
    await fetchRoles()
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao excluir permissão')
  }
}

const confirmDeleteUser = (user: User) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const handleDeleteUser = async () => {
  if (!userToDelete.value) return
  try {
    const response = await deleteUser(userToDelete.value.id)
    showSuccess(response.message)
    showDeleteModal.value = false
    userToDelete.value = null
    await fetchUsers({ search: searchQuery.value, page: pagination.value.current_page })
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao excluir usuário')
  }
}

const handleImpersonate = async (user: User) => {
  try {
    const response = await impersonateUser(user.id)
    authStore.user = response.user
    authStore.impersonating = true
    showSuccess(response.message)
    navigateTo('/dashboard')
  } catch (e: any) {
    showError(e.data?.message || 'Erro ao impersonar usuário')
  }
}

// Lifecycle
onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>
