<template>
  <div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Welcome Card -->
      <UCard>
        <template #header>
          <h2 class="text-lg font-medium">
            Bem-vindo, {{ authStore.user?.name }}!
          </h2>
        </template>
        <p class="text-gray-600 dark:text-gray-400">
          Você está conectado como {{ authStore.user?.email }}
        </p>
      </UCard>

      <!-- Roles Card -->
      <UCard>
        <template #header>
          <h2 class="text-lg font-medium">
            Suas Permissões
          </h2>
        </template>
        <div class="flex flex-wrap gap-2">
          <UBadge
            v-for="role in authStore.user?.roles"
            :key="role.id"
            color="blue"
            variant="subtle"
          >
            {{ role.name }}
          </UBadge>
          <span
            v-if="!authStore.user?.roles?.length"
            class="text-gray-500 dark:text-gray-400"
          >
            Nenhuma permissão atribuída
          </span>
        </div>
      </UCard>

      <!-- Status Card -->
      <UCard>
        <template #header>
          <h2 class="text-lg font-medium">
            Status da Conta
          </h2>
        </template>
        <div class="flex items-center gap-2">
          <UBadge
            :color="authStore.user?.active ? 'green' : 'red'"
            variant="subtle"
          >
            {{ authStore.user?.active ? 'Conta Ativa' : 'Conta Inativa' }}
          </UBadge>
        </div>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Tipo: {{ authStore.user?.is_externo ? 'Usuário Externo' : 'Usuário LDAP' }}
        </p>
      </UCard>
    </div>

    <!-- Admin Section -->
    <div v-if="authStore.isAdmin" class="mt-8">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
        Administração
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <UCard :ui="{ body: { padding: '' } }">
          <NuxtLink
            to="/admin/users"
            class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
          >
            <div class="flex items-center gap-4">
              <UAvatar
                icon="i-heroicons-users"
                size="lg"
                color="blue"
              />
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                  Gerenciar Usuários
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Gerencie usuários e permissões do sistema
                </p>
              </div>
            </div>
          </NuxtLink>
        </UCard>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth',
})

const authStore = useAuthStore()
</script>
