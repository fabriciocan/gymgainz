<script setup lang="ts">
const show = ref(false)
const { canInstall, isIos, isInstalled, install } = usePwaInstall()

onMounted(() => {
  if (isInstalled.value) return
  if (localStorage.getItem('installDismissed')) return

  if (canInstall.value || isIos.value) {
    setTimeout(() => show.value = true, 3000)
  } else {
    // Android: wait for event to fire (canInstall updates reactively)
    const stop = watch(canInstall, (val) => {
      if (val && !isInstalled.value && !localStorage.getItem('installDismissed')) {
        setTimeout(() => { show.value = true }, 3000)
        stop()
      }
    })
  }
})

async function handleInstall() {
  await install()
  show.value = false
}

function dismiss() {
  show.value = false
  localStorage.setItem('installDismissed', '1')
}
</script>

<template>
  <Transition name="slide-up">
    <div
      v-if="show"
      class="fixed left-4 right-4 z-[9999] bg-[#0A0A0A] text-white rounded-2xl p-4 shadow-2xl"
      :style="{ bottom: 'calc(16px + env(safe-area-inset-bottom))' }"
    >
      <!-- Android / Chrome -->
      <template v-if="!isIos">
        <div class="flex items-center gap-3 mb-3.5">
          <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0A0A0A" stroke-width="1.8">
              <path d="M6.5 6.5h11M6.5 12h11M6.5 17.5h11"/>
              <circle cx="3.5" cy="6.5" r="1" fill="#0A0A0A" stroke="none"/>
              <circle cx="3.5" cy="12" r="1" fill="#0A0A0A" stroke="none"/>
              <circle cx="3.5" cy="17.5" r="1" fill="#0A0A0A" stroke="none"/>
            </svg>
          </div>
          <div>
            <p class="text-[15px] font-semibold">Instalar GymTrack</p>
            <p class="text-[13px] text-white/60 mt-0.5">Adicione à tela inicial</p>
          </div>
        </div>
        <div class="flex gap-2.5">
          <button
            class="flex-1 bg-white/10 text-white rounded-xl py-2.5 text-[14px] font-medium"
            @click="dismiss"
          >
            Agora não
          </button>
          <button
            class="flex-1 bg-white text-[#0A0A0A] rounded-xl py-2.5 text-[14px] font-semibold"
            @click="handleInstall"
          >
            Instalar
          </button>
        </div>
      </template>

      <!-- iOS Safari -->
      <template v-else>
        <div class="text-center">
          <p class="text-[15px] font-semibold mb-3">Instale o GymTrack</p>
          <div class="space-y-1.5 mb-4 text-left">
            <div class="text-[14px] text-white/85 flex items-center gap-2">
              <span class="text-white/40 text-[12px] font-mono">1.</span>
              Toque em <strong>Compartilhar</strong>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline"><path d="M12 3v13M7 8l5-5 5 5"/><path d="M5 20h14"/></svg>
            </div>
            <div class="text-[14px] text-white/85 flex items-start gap-2">
              <span class="text-white/40 text-[12px] font-mono">2.</span>
              Selecione <strong>"Adicionar à Tela de Início"</strong>
            </div>
          </div>
          <button
            class="bg-white/15 text-white rounded-xl px-5 py-2 text-[14px] font-medium"
            @click="dismiss"
          >
            Entendi
          </button>
        </div>
        <!-- Arrow pointing down toward Safari share button -->
        <div class="text-center mt-2 text-[18px] animate-bounce text-white/60">↓</div>
      </template>
    </div>
  </Transition>
</template>

<style scoped>
.slide-up-enter-active {
  transition: all 300ms cubic-bezier(0.34, 1.56, 0.64, 1);
}
.slide-up-leave-active {
  transition: all 200ms ease;
}
.slide-up-enter-from {
  transform: translateY(120px);
  opacity: 0;
}
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
