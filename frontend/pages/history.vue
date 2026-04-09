<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white mb-5">Histórico</h1>

    <div v-if="loading" class="space-y-2.5">
      <div class="h-20 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" v-for="i in 5" :key="i" />
    </div>

    <div v-else-if="!sessionStore.sessions.length" class="text-center py-16">
      <svg class="mx-auto mb-3 text-[#CCCCCC] dark:text-[#444]" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>
      <p class="text-[#8A8A8A] text-[14px]">Nenhuma sessão registrada ainda.</p>
      <NuxtLink to="/workouts" class="inline-block mt-4 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl px-5 py-2.5 text-[14px] font-medium">
        Começar treino
      </NuxtLink>
    </div>

    <div v-else class="space-y-2.5">
      <div
        v-for="session in sessionStore.sessions"
        :key="session.id"
        class="border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-2xl p-4 bg-white dark:bg-[#0A0A0A]"
      >
        <div class="flex items-start justify-between mb-3">
          <div>
            <p class="text-[15px] font-semibold text-[#0A0A0A] dark:text-white">{{ session.workout?.name }}</p>
            <p class="text-[13px] text-[#8A8A8A] mt-0.5">
              {{ formatDate(session.date) }}
              <span v-if="session.duration_minutes"> · {{ session.duration_minutes }}min</span>
            </p>
          </div>
          <button
            class="p-1.5 text-[#CCCCCC] dark:text-[#444] hover:text-red-500 dark:hover:text-red-400 transition-colors"
            @click="confirmDelete(session)"
          >
            <UIcon name="i-heroicons-trash" class="w-4 h-4" />
          </button>
        </div>

        <div v-if="session.workout?.exercises?.length" class="space-y-2">
          <div
            v-for="we in session.workout.exercises"
            :key="we.id"
            class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-xl px-3 py-2"
          >
            <p class="text-[13px] font-medium text-[#0A0A0A] dark:text-white mb-1">{{ we.exercise?.name }}</p>
            <div v-if="getSets(session, we.id).length" class="flex flex-wrap gap-x-3 gap-y-0.5">
              <span
                v-for="s in getSets(session, we.id)"
                :key="s.id"
                class="text-[12px] text-[#8A8A8A] font-mono"
              >
                S{{ s.set_number }}: {{ s.reps_done }}×{{ s.weight_kg }}kg
              </span>
            </div>
            <p v-else class="text-[12px] text-[#CCCCCC] dark:text-[#444]">Nenhuma série registrada</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal confirmar exclusão -->
  <UModal v-model="showDeleteModal">
    <UCard>
      <template #header>
        <p class="text-[#0A0A0A] dark:text-white font-semibold">Excluir sessão</p>
      </template>
      <p class="text-[#8A8A8A]">
        Deseja excluir o treino
        <span class="text-[#0A0A0A] dark:text-white font-medium">{{ deletingSession?.workout?.name }}</span>
        de <span class="text-[#0A0A0A] dark:text-white font-medium">{{ deletingSession ? formatDate(deletingSession.date) : '' }}</span>?
        Esta ação não pode ser desfeita.
      </p>
      <template #footer>
        <div class="flex justify-end gap-2">
          <button
            class="px-4 py-2 rounded-xl text-[13px] text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors"
            @click="showDeleteModal = false"
          >
            Cancelar
          </button>
          <button
            class="px-4 py-2 rounded-xl text-[13px] font-medium bg-red-500 text-white disabled:opacity-50"
            :disabled="deleting"
            @click="doDelete"
          >
            {{ deleting ? '...' : 'Excluir' }}
          </button>
        </div>
      </template>
    </UCard>
  </UModal>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const sessionStore = useSessionStore()
const loading = ref(true)

onMounted(async () => {
  await sessionStore.fetchSessions()
  loading.value = false
})

const formatDate = (date: string) =>
  new Date(date).toLocaleDateString('pt-BR', { weekday: 'short', day: 'numeric', month: 'short' })

const getSets = (session: any, weId: number) =>
  (session.sets ?? []).filter((s: any) => s.workout_exercise_id === weId)

const showDeleteModal = ref(false)
const deletingSession = ref<any>(null)
const deleting = ref(false)

const confirmDelete = (session: any) => {
  deletingSession.value = session
  showDeleteModal.value = true
}

const doDelete = async () => {
  if (!deletingSession.value) return
  deleting.value = true
  try {
    await sessionStore.deleteSession(deletingSession.value.id)
    showDeleteModal.value = false
  } finally {
    deleting.value = false
    deletingSession.value = null
  }
}
</script>
