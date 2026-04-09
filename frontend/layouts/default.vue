<template>
  <div class="min-h-screen bg-white dark:bg-[#0A0A0A] flex flex-col font-sans">
    <!-- Impersonate Banner -->
    <UAlert
      v-if="authStore.impersonating"
      color="yellow"
      variant="solid"
      class="fixed top-0 left-0 right-0 z-[60]"
    >
      <template #title>Você está visualizando como outro usuário.</template>
      <template #actions>
        <UButton color="yellow" variant="ghost" @click="authStore.stopImpersonate()">Voltar</UButton>
      </template>
    </UAlert>

    <!-- Conteúdo principal -->
    <main class="flex-1 pb-20" :class="{ 'pt-12': authStore.impersonating }">
      <slot />
    </main>

    <!-- Bottom navigation -->
    <nav v-if="authStore.isAuthenticated" class="fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-[#0A0A0A]/95 border-t border-[#E5E5E5] dark:border-[#2A2A2A] z-40 backdrop-blur-sm" style="padding-bottom: env(safe-area-inset-bottom)">
      <div class="flex items-center justify-around py-2 max-w-lg mx-auto">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="flex flex-col items-center gap-0.5 py-1 px-3 transition-colors"
        >
          <UIcon
            :name="item.icon"
            class="w-[22px] h-[22px] transition-colors"
            :class="isActive(item.to) ? 'text-[#0A0A0A] dark:text-white' : 'text-[#CCCCCC] dark:text-[#555]'"
          />
          <span
            class="text-[9px] font-medium tracking-[0.08em] uppercase transition-colors"
            :class="isActive(item.to) ? 'text-[#0A0A0A] dark:text-white' : 'text-[#CCCCCC] dark:text-[#555]'"
          >{{ item.label }}</span>
        </NuxtLink>
      </div>
    </nav>

    <UNotifications />
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const route = useRoute()

onMounted(() => authStore.checkImpersonateStatus())

const navItems = [
  { to: '/dashboard', icon: 'i-heroicons-squares-2x2', label: 'Início' },
  { to: '/workouts', icon: 'i-heroicons-list-bullet', label: 'Treinos' },
  { to: '/progress', icon: 'i-heroicons-arrow-trending-up', label: 'Evolução' },
  { to: '/measurements', icon: 'i-heroicons-scale', label: 'Medidas' },
  { to: '/profile', icon: 'i-heroicons-user', label: 'Perfil' },
]

const isActive = (path: string) => route.path.startsWith(path)
</script>
