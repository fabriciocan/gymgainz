<template>
  <div class="px-5 pb-6 pt-3 max-w-lg mx-auto">
    <!-- Greeting -->
    <div class="mb-5">
      <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white leading-tight">
        Olá, {{ authStore.user?.name?.split(' ')[0] }}
      </h1>
      <p class="text-[13px] text-[#8A8A8A] mt-0.5">{{ today }}</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-4">
      <div class="h-5 w-32 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-full animate-pulse" />
      <div class="h-28 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" />
      <div class="h-16 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" />
      <div class="h-14 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" />
    </div>

    <div v-else-if="dashboard" class="space-y-5">
      <!-- Streak pill -->
      <div v-if="dashboard.streak?.current" class="inline-flex items-center gap-1.5 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-full px-3 py-1 text-[12px] font-medium">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="#FF6B2B"><path d="M12 2C9 8 4 10 4 15a8 8 0 0 0 16 0c0-5-5-7-8-13z"/></svg>
        {{ dashboard.streak.current }} {{ dashboard.streak.current === 1 ? 'dia consecutivo' : 'dias consecutivos' }}
      </div>

      <!-- Último treino -->
      <div v-if="dashboard.last_session">
        <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Último treino</p>
        <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
          <p class="text-[15px] font-semibold text-[#0A0A0A] dark:text-white mb-1">{{ dashboard.last_session.workout_name }}</p>
          <p class="text-[12px] text-[#8A8A8A]">{{ dashboard.last_session.date }} · {{ dashboard.last_session.duration_minutes }} minutos</p>
          <div class="flex gap-3 mt-3">
            <div class="flex-1 bg-white dark:bg-[#0A0A0A] rounded-xl p-2.5 border border-[#E5E5E5] dark:border-[#2A2A2A]">
              <p class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.08em]">Volume</p>
              <p class="text-[18px] font-medium text-[#0A0A0A] dark:text-white font-mono tracking-[-0.02em]">
                {{ dashboard.last_session.volume_kg ? (dashboard.last_session.volume_kg / 1000).toFixed(1) + 't' : '—' }}
              </p>
            </div>
            <div class="flex-1 bg-white dark:bg-[#0A0A0A] rounded-xl p-2.5 border border-[#E5E5E5] dark:border-[#2A2A2A]">
              <p class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.08em]">Séries</p>
              <p class="text-[18px] font-medium text-[#0A0A0A] dark:text-white font-mono tracking-[-0.02em]">
                {{ dashboard.last_session.sets_count ?? '—' }}
              </p>
            </div>
            <div class="flex-1 bg-white dark:bg-[#0A0A0A] rounded-xl p-2.5 border border-[#E5E5E5] dark:border-[#2A2A2A]">
              <p class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.08em]">PRs</p>
              <p class="text-[18px] font-medium text-[#0A0A0A] dark:text-white font-mono tracking-[-0.02em]">
                {{ dashboard.last_session.pr_count ?? '—' }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Esta semana -->
      <div>
        <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Esta semana</p>
        <div class="flex gap-1.5 items-center">
          <div
            v-for="(day, i) in weekDays"
            :key="i"
            class="w-9 h-9 rounded-full flex items-center justify-center text-[9px] font-medium transition-colors"
            :class="day.done
              ? 'bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A]'
              : 'bg-[#F2F2F2] dark:bg-[#1A1A1A] text-[#8A8A8A]'"
            :title="day.name"
          >
            {{ day.short }}
          </div>
        </div>
        <p class="text-[11px] text-[#8A8A8A] mt-1.5">{{ dashboard.weekly_sessions }} treino{{ dashboard.weekly_sessions !== 1 ? 's' : '' }} esta semana</p>
      </div>

      <!-- Peso atual -->
      <div v-if="dashboard.latest_weight">
        <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">Peso atual</p>
        <div class="flex items-baseline gap-2 mb-1">
          <span class="text-[28px] font-medium text-[#0A0A0A] dark:text-white font-mono tracking-[-0.03em]">{{ dashboard.latest_weight }}</span>
          <span class="text-[14px] text-[#8A8A8A]">kg</span>
          <span
            v-if="dashboard.weight_change_30d !== null"
            class="text-[12px] font-medium rounded-full px-2 py-0.5"
            :class="dashboard.weight_change_30d <= 0
              ? 'text-green-600 bg-green-50 dark:text-green-400 dark:bg-green-950/40'
              : 'text-red-600 bg-red-50 dark:text-red-400 dark:bg-red-950/40'"
          >
            {{ dashboard.weight_change_30d > 0 ? '+' : '' }}{{ dashboard.weight_change_30d }}kg
          </span>
        </div>
      </div>

      <!-- Stats secundários -->
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
          <p class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.08em]">Total de sessões</p>
          <p class="text-[28px] font-medium text-[#0A0A0A] dark:text-white font-mono tracking-[-0.03em] mt-0.5">{{ dashboard.total_sessions }}</p>
        </div>
        <div class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-2xl p-4">
          <p class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.08em]">Melhor sequência</p>
          <p class="text-[28px] font-medium text-[#0A0A0A] dark:text-white font-mono tracking-[-0.03em] mt-0.5">{{ dashboard.streak?.best ?? 0 }}</p>
        </div>
      </div>

      <!-- Sugestão de hoje -->
      <div v-if="dashboard.suggested_workout">
        <p class="text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-2">
          {{ dashboard.suggested_workout.is_scheduled_today ? 'Treino de hoje' : 'Sugestão de hoje' }}
        </p>
        <NuxtLink
          :to="`/session/${dashboard.suggested_workout.id}`"
          class="flex items-center justify-between w-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl px-4 py-3.5 font-medium text-[15px] tracking-[-0.01em] transition-all active:scale-[0.98]"
        >
          <span>&#9654;  Iniciar {{ dashboard.suggested_workout.name }}</span>
        </NuxtLink>
      </div>

      <NuxtLink to="/workouts" class="block text-center text-[13px] text-[#8A8A8A] py-2">
        Ver todos os treinos
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'] })

const authStore = useAuthStore()
const { api } = useApi()
const loading = ref(true)
const dashboard = ref<any>(null)

const today = new Date().toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' })

const weekDays = computed(() => {
  const days = [
    { short: 'S', name: 'Segunda' },
    { short: 'T', name: 'Terça' },
    { short: 'Q', name: 'Quarta' },
    { short: 'Q', name: 'Quinta' },
    { short: 'S', name: 'Sexta' },
    { short: 'S', name: 'Sábado' },
    { short: 'D', name: 'Domingo' },
  ]
  const sessions = dashboard.value?.week_sessions ?? []
  const today = new Date().getDay()
  return days.map((d, i) => {
    const dayIndex = i === 6 ? 0 : i + 1
    return {
      ...d,
      done: sessions.some((s: any) => new Date(s.date).getDay() === dayIndex),
    }
  })
})

onMounted(async () => {
  try {
    dashboard.value = await api('/dashboard')
  } finally {
    loading.value = false
  }
})
</script>
