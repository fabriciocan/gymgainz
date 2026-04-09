<template>
  <div class="p-4 max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-6">
      <UButton icon="i-heroicons-arrow-left" variant="ghost" to="/history" />
      <div>
        <h1 class="text-white text-xl font-bold">{{ session?.workout?.name }}</h1>
        <p class="text-slate-400 text-sm">{{ session?.date }} · {{ session?.duration_minutes }}min</p>
      </div>
    </div>

    <div v-if="loading" class="space-y-3">
      <USkeleton class="h-24 rounded-xl" v-for="i in 4" :key="i" />
    </div>

    <div v-else-if="session" class="space-y-4">
      <div
        v-for="we in session.workout?.exercises"
        :key="we.id"
        class="bg-slate-800 rounded-xl border border-slate-700 p-4"
      >
        <p class="text-white font-semibold mb-3">{{ we.exercise?.name }}</p>
        <div class="space-y-2">
          <div
            v-for="s in getSets(we.id)"
            :key="s.id"
            class="flex items-center justify-between text-sm"
          >
            <span class="text-slate-400">Série {{ s.set_number }}</span>
            <span class="text-white">{{ s.reps_done }} reps · {{ s.weight_kg }}kg</span>
          </div>
        </div>
      </div>

      <div v-if="session.notes" class="bg-slate-800 rounded-xl border border-slate-700 p-4">
        <p class="text-slate-400 text-xs mb-1">Observações</p>
        <p class="text-white text-sm">{{ session.notes }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const route = useRoute()
const sessionStore = useSessionStore()
const loading = ref(true)
const session = ref<any>(null)

onMounted(async () => {
  try {
    session.value = await sessionStore.fetchSession(Number(route.params.id))
  } finally {
    loading.value = false
  }
})

const getSets = (weId: number) =>
  (session.value?.sets ?? []).filter((s: any) => s.workout_exercise_id === weId)
</script>
