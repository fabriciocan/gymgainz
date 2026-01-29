<template>
  <UContainer class="min-h-screen" :ui="{ constrained: 'max-w-none px-0' }">
    <!-- Impersonate Banner -->
    <UAlert
      v-if="authStore.impersonating"
      color="yellow"
      variant="solid"
      :ui="{ wrapper: 'fixed top-0 left-0 right-0 z-[60]' }"
    >
      <template #title>
        Você está visualizando como outro usuário.
      </template>
      <template #actions>
        <UButton
          color="yellow"
          variant="ghost"
          @click="handleStopImpersonate"
        >
          Voltar para sua conta
        </UButton>
      </template>
    </UAlert>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 dark:bg-gray-950 transform transition-transform duration-300 ease-in-out lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        authStore.impersonating ? 'pt-16' : ''
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center justify-center h-16 bg-gray-900">
        <span class="text-white text-xl font-bold">Sistema LDAP</span>
      </div>

      <!-- Navigation -->
      <nav class="mt-8">
        <UButton
          to="/dashboard"
          variant="ghost"
          color="white"
          block
          :ui="{ padding: { sm: 'px-6 py-3' }, rounded: 'rounded-none' }"
        >
          <template #leading>
            <UIcon name="i-heroicons-home" class="w-5 h-5" />
          </template>
          Dashboard
        </UButton>

        <UButton
          v-if="authStore.isAdmin"
          to="/admin/users"
          variant="ghost"
          color="white"
          block
          :ui="{ padding: { sm: 'px-6 py-3' }, rounded: 'rounded-none' }"
        >
          <template #leading>
            <UIcon name="i-heroicons-users" class="w-5 h-5" />
          </template>
          Usuários
        </UButton>
      </nav>
    </aside>

    <!-- Main Content -->
    <div
      class="lg:pl-64 min-h-screen flex flex-col w-full max-w-none"
      :class="{ 'pt-16': authStore.impersonating }"
    >
      <!-- Top Navigation -->
      <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40" :class="{ 'top-16': authStore.impersonating }">
        <div class="flex items-center justify-between h-16 px-4">
          <!-- Mobile menu button -->
          <UButton
            icon="i-heroicons-bars-3"
            variant="ghost"
            class="lg:hidden"
            @click="sidebarOpen = !sidebarOpen"
          />

          <div class="flex-1"></div>

          <!-- Right side -->
          <div class="flex items-center gap-4">
            <!-- Dark mode toggle -->
            <UButton
              :icon="colorMode.value === 'dark' ? 'i-heroicons-sun' : 'i-heroicons-moon'"
              variant="ghost"
              @click="toggleColorMode"
            />

            <!-- User menu -->
            <UDropdown
              :items="userMenuItems"
              :popper="{ placement: 'bottom-end' }"
            >
              <UButton
                color="white"
                :label="authStore.user?.name || 'Carregando...'"
                trailing-icon="i-heroicons-chevron-down"
                variant="ghost"
              />
            </UDropdown>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 w-full max-w-none">
        <slot />
      </main>
    </div>

    <!-- Mobile sidebar overlay -->
    <Transition
      enter-active-class="transition-opacity ease-linear duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="sidebarOpen"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
      />
    </Transition>

    <UNotifications />
  </UContainer>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const colorMode = useColorMode()

const sidebarOpen = ref(false)

const toggleColorMode = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}

const handleLogout = async () => {
  await authStore.logout()
}

const handleStopImpersonate = async () => {
  await authStore.stopImpersonate()
}

const userMenuItems = [
  [{
    label: 'Sair',
    icon: 'i-heroicons-arrow-left-on-rectangle',
    click: handleLogout
  }]
]

// Verifica status de impersonate ao montar
onMounted(() => {
  authStore.checkImpersonateStatus()
})
</script>
