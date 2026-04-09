<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <div class="flex items-center justify-between mb-5">
      <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">Meus Treinos</h1>
      <NuxtLink
        to="/workouts/new"
        class="flex items-center gap-1.5 text-[13px] font-medium text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors"
      >
        <UIcon name="i-heroicons-plus" class="w-4 h-4" />
        Novo
      </NuxtLink>
    </div>

    <div v-if="loading" class="space-y-2.5">
      <div class="h-24 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" v-for="i in 3" :key="i" />
    </div>

    <div v-else-if="workoutStore.workouts.length === 0" class="text-center py-16">
      <p class="text-[32px] mb-3">🏋️</p>
      <p class="text-[#8A8A8A] text-[14px]">Nenhum treino cadastrado.</p>
      <NuxtLink
        to="/workouts/new"
        class="inline-block mt-4 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl px-5 py-2.5 text-[14px] font-medium"
      >
        Criar primeiro treino
      </NuxtLink>
    </div>

    <div v-else class="space-y-2.5">
      <p class="text-[10px] font-medium tracking-[0.08em] uppercase text-[#8A8A8A] mb-2.5">Ativos</p>
      <WorkoutCard
        v-for="workout in workoutStore.workouts"
        :key="workout.id"
        :workout="workout"
        @click="navigateTo(`/workouts/${workout.id}`)"
      >
        <template #actions>
          <NuxtLink
            :to="`/session/${workout.id}`"
            class="flex items-center justify-center w-8 h-8 rounded-full bg-[#F2F2F2] dark:bg-[#2A2A2A] text-[#8A8A8A] hover:bg-[#0A0A0A] dark:hover:bg-white hover:text-white dark:hover:text-[#0A0A0A] transition-colors"
            @click.stop
          >
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg>
          </NuxtLink>
        </template>
      </WorkoutCard>

      <button
        class="w-full border border-dashed border-[#CCCCCC] dark:border-[#333] rounded-2xl p-4 text-[14px] font-medium text-[#8A8A8A] hover:border-[#8A8A8A] dark:hover:border-[#555] hover:text-[#3D3D3D] dark:hover:text-white hover:bg-[#F9F9F9] dark:hover:bg-[#1A1A1A] transition-all mt-1"
        @click="navigateTo('/workouts/new')"
      >
        + Criar novo treino
      </button>

      <NuxtLink
        to="/history"
        class="flex items-center justify-between w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl px-4 py-3.5 mt-1 text-[14px] font-medium text-[#0A0A0A] dark:text-white"
      >
        <span class="flex items-center gap-2.5">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-[#8A8A8A]"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          Histórico de treinos
        </span>
        <span class="text-[18px] text-[#CCCCCC] dark:text-[#444]">›</span>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const workoutStore = useWorkoutStore()
const loading = ref(true)

onMounted(async () => {
  await workoutStore.fetchWorkouts()
  loading.value = false
})
</script>
