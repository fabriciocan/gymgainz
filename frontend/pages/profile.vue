<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white mb-5">Perfil</h1>

    <div class="space-y-3">
      <!-- Avatar + Info -->
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-[#0A0A0A] dark:bg-white rounded-full flex items-center justify-center text-white dark:text-[#0A0A0A] font-semibold text-xl">
            {{ authStore.user?.name?.[0]?.toUpperCase() }}
          </div>
          <div>
            <p class="text-[15px] font-semibold text-[#0A0A0A] dark:text-white">{{ authStore.user?.name }}</p>
            <p class="text-[13px] text-[#8A8A8A]">{{ authStore.user?.email }}</p>
          </div>
        </div>
      </div>

      <!-- Assinatura -->
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
        <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2.5">Assinatura</p>
        <div v-if="subscriptionStore.loading">
          <div class="h-6 w-32 bg-[#E5E5E5] dark:bg-[#2A2A2A] rounded-full animate-pulse" />
        </div>
        <div v-else-if="subscriptionStore.hasAccess" class="flex items-center gap-2">
          <span class="inline-flex items-center gap-1.5 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 text-[12px] font-medium rounded-full px-2.5 py-0.5">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block" />
            Ativo
          </span>
          <span class="text-[14px] text-[#0A0A0A] dark:text-white">{{ subscriptionStore.subscription?.plan ?? 'Trial' }}</span>
          <span v-if="subscriptionStore.trialEndsAt" class="text-[12px] text-[#8A8A8A]">
            · até {{ subscriptionStore.trialEndsAt.split('T')[0] }}
          </span>
        </div>
        <div v-else class="flex items-center gap-3">
          <span class="inline-flex items-center gap-1.5 bg-red-50 dark:bg-red-950/40 text-red-500 text-[12px] font-medium rounded-full px-2.5 py-0.5">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block" />
            Inativo
          </span>
          <NuxtLink to="/subscribe" class="text-[13px] font-medium text-[#0A0A0A] dark:text-white underline">Assinar agora</NuxtLink>
        </div>
      </div>

      <!-- Tema -->
      <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 flex items-center justify-between">
        <div>
          <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white">Tema</p>
          <p class="text-[12px] text-[#8A8A8A] mt-0.5">{{ colorMode.preference === 'system' ? 'Sistema' : colorMode.preference === 'dark' ? 'Escuro' : 'Claro' }}</p>
        </div>
        <div class="flex gap-1.5">
          <button
            v-for="mode in themeOptions"
            :key="mode.value"
            class="px-3 py-1.5 rounded-xl text-[12px] font-medium transition-all"
            :class="colorMode.preference === mode.value
              ? 'bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A]'
              : 'bg-[#E5E5E5] dark:bg-[#2A2A2A] text-[#8A8A8A]'"
            @click="colorMode.preference = mode.value"
          >
            {{ mode.label }}
          </button>
        </div>
      </div>

      <!-- Instalar app -->
      <NuxtLink
        v-if="!isInstalled"
        to="/install"
        class="flex items-center justify-between w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 text-[14px] font-medium text-[#0A0A0A] dark:text-white"
      >
        <span class="flex items-center gap-2">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-[#8A8A8A]"><path d="M12 3v13M7 11l5 5 5-5"/><path d="M5 20h14"/></svg>
          Adicionar à tela inicial
        </span>
        <span class="text-[18px] text-[#CCCCCC] dark:text-[#444]">›</span>
      </NuxtLink>

      <!-- Exercícios personalizados -->
      <NuxtLink
        to="/exercises"
        class="flex items-center justify-between w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4 text-[14px] font-medium text-[#0A0A0A] dark:text-white"
      >
        <span class="flex items-center gap-2">
          <UIcon name="i-heroicons-adjustments-horizontal" class="w-5 h-5 text-[#8A8A8A]" />
          Meus Exercícios
        </span>
        <span class="text-[18px] text-[#CCCCCC] dark:text-[#444]">›</span>
      </NuxtLink>

      <!-- Logout -->
      <button
        class="w-full text-[14px] font-medium text-red-500 py-3 rounded-2xl border border-[#E5E5E5] dark:border-[#2A2A2A] hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors"
        :disabled="authStore.loading"
        @click="authStore.logout()"
      >
        {{ authStore.loading ? 'Saindo...' : 'Sair' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth'] })

const authStore = useAuthStore()
const subscriptionStore = useSubscriptionStore()
const colorMode = useColorMode()
const { isInstalled } = usePwaInstall()

const themeOptions = [
  { value: 'light', label: 'Claro' },
  { value: 'system', label: 'Auto' },
  { value: 'dark', label: 'Escuro' },
]

onMounted(() => subscriptionStore.fetchStatus())
</script>
