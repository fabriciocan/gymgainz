<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-8">
      <NuxtLink to="/profile" class="text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      </NuxtLink>
      <h1 class="text-[20px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">Instalar app</h1>
    </div>

    <!-- Já instalado -->
    <div v-if="isInstalled" class="text-center py-12">
      <div class="w-16 h-16 bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-[#0A0A0A] dark:text-white"><path d="M20 6L9 17l-5-5"/></svg>
      </div>
      <p class="text-[18px] font-semibold text-[#0A0A0A] dark:text-white mb-1">App já instalado</p>
      <p class="text-[14px] text-[#8A8A8A]">O GymTrack está na sua tela inicial.</p>
    </div>

    <div v-else class="space-y-6">
      <!-- Ícone do app -->
      <div class="flex items-center gap-4 bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
        <div class="w-14 h-14 bg-[#0A0A0A] dark:bg-white rounded-2xl flex items-center justify-center shrink-0">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="1.8">
            <path d="M6.5 6.5h11M6.5 12h11M6.5 17.5h11"/>
            <circle cx="3.5" cy="6.5" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
            <circle cx="3.5" cy="12" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
            <circle cx="3.5" cy="17.5" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
          </svg>
        </div>
        <div>
          <p class="text-[16px] font-semibold text-[#0A0A0A] dark:text-white">GymTrack</p>
          <p class="text-[13px] text-[#8A8A8A]">Acompanhe sua evolução na academia</p>
        </div>
      </div>

      <!-- Android / Chrome -->
      <div v-if="canInstall">
        <p class="text-[13px] text-[#8A8A8A] mb-3">
          Instale o app para acessar diretamente da tela inicial, sem abrir o navegador.
        </p>
        <button
          class="w-full flex items-center justify-center gap-2.5 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl py-3.5 text-[15px] font-medium tracking-[-0.01em] transition-all active:scale-[0.98] disabled:opacity-50"
          :disabled="installing"
          @click="triggerInstall"
        >
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v13M7 11l5 5 5-5"/><path d="M5 20h14"/></svg>
          {{ installing ? 'Instalando...' : 'Adicionar à tela inicial' }}
        </button>
        <p v-if="result === 'dismissed'" class="text-[12px] text-[#8A8A8A] text-center mt-3">
          Instalação cancelada. Você pode tentar novamente quando quiser.
        </p>
      </div>

      <!-- iOS Safari -->
      <div v-else-if="isIos">
        <p class="text-[13px] text-[#8A8A8A] mb-4">
          No iPhone ou iPad, siga os passos abaixo para adicionar à tela inicial:
        </p>
        <div class="space-y-3">
          <div class="flex items-start gap-3 bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
            <span class="w-6 h-6 rounded-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] text-[12px] font-semibold flex items-center justify-center shrink-0 mt-0.5">1</span>
            <div>
              <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white">Toque em Compartilhar</p>
              <p class="text-[12px] text-[#8A8A8A] mt-0.5">Toque no ícone de compartilhar na barra inferior do Safari</p>
              <div class="mt-2 inline-flex items-center gap-1.5 bg-white dark:bg-[#0A0A0A] border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-lg px-2.5 py-1.5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#0A0A0A] dark:text-white"><path d="M12 3v13M7 8l5-5 5 5"/><path d="M5 20h14"/></svg>
                <span class="text-[12px] text-[#0A0A0A] dark:text-white font-medium">Compartilhar</span>
              </div>
            </div>
          </div>

          <div class="flex items-start gap-3 bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
            <span class="w-6 h-6 rounded-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] text-[12px] font-semibold flex items-center justify-center shrink-0 mt-0.5">2</span>
            <div>
              <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white">Adicionar à Tela de Início</p>
              <p class="text-[12px] text-[#8A8A8A] mt-0.5">Role para baixo no menu e toque em "Adicionar à Tela de Início"</p>
              <div class="mt-2 inline-flex items-center gap-1.5 bg-white dark:bg-[#0A0A0A] border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-lg px-2.5 py-1.5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#0A0A0A] dark:text-white"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 17.5h7M17.5 14v7"/></svg>
                <span class="text-[12px] text-[#0A0A0A] dark:text-white font-medium">Adicionar à Tela de Início</span>
              </div>
            </div>
          </div>

          <div class="flex items-start gap-3 bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
            <span class="w-6 h-6 rounded-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] text-[12px] font-semibold flex items-center justify-center shrink-0 mt-0.5">3</span>
            <div>
              <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white">Toque em Adicionar</p>
              <p class="text-[12px] text-[#8A8A8A] mt-0.5">Confirme tocando em "Adicionar" no canto superior direito</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Outro navegador sem suporte ao prompt -->
      <div v-else>
        <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
          <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white mb-1">Use o Chrome ou Safari</p>
          <p class="text-[13px] text-[#8A8A8A]">
            Para instalar o app, abra esta página no Chrome (Android) ou Safari (iPhone/iPad) e toque em "Adicionar à tela inicial".
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth'] })

const { canInstall, isInstalled, isIos, install } = usePwaInstall()
const installing = ref(false)
const result = ref<string | null>(null)

const triggerInstall = async () => {
  installing.value = true
  result.value = null
  try {
    result.value = await install()
  } finally {
    installing.value = false
  }
}
</script>
