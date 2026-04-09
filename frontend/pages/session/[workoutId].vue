<template>
  <div class="min-h-full bg-white dark:bg-[#0A0A0A]">
    <UNotifications />

    <!-- ── EXERCISE LIST VIEW ── -->
    <div v-if="view === 'list'" class="max-w-lg mx-auto px-5 pt-4 pb-8 flex flex-col min-h-full">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <button class="text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors" @click="cancel">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div class="text-center">
          <p class="text-[15px] font-semibold text-[#0A0A0A] dark:text-white">{{ workout?.name }}</p>
          <p class="font-mono text-[13px] text-[#8A8A8A]">{{ elapsed }}</p>
        </div>
        <button
          class="text-[13px] font-medium text-white bg-[#16A34A] rounded-xl px-3 py-1.5 disabled:opacity-50 transition-opacity"
          :disabled="finishing"
          @click="finish"
        >
          {{ finishing ? '...' : 'Finalizar' }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-3">
        <div class="h-20 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" v-for="i in 3" :key="i" />
      </div>

      <!-- Exercise list -->
      <div v-else class="space-y-2.5">
        <button
          v-for="we in workout?.exercises"
          :key="we.id"
          class="w-full flex items-center justify-between border rounded-2xl px-4 py-3.5 text-left transition-all active:scale-[0.98]"
          :class="exerciseStatus(we.id) === 'done'
            ? 'bg-[#F9F9F9] dark:bg-[#1A1A1A] border-transparent'
            : exerciseStatus(we.id) === 'active'
              ? 'border-[#0A0A0A] dark:border-white bg-white dark:bg-[#0A0A0A]'
              : 'border-[#E5E5E5] dark:border-[#2A2A2A] bg-white dark:bg-[#0A0A0A]'"
          @click="openExercise(we)"
        >
          <div class="flex items-center gap-3">
            <!-- Status indicator -->
            <div
              class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
              :class="exerciseStatus(we.id) === 'done'
                ? 'bg-[#0A0A0A] dark:bg-white'
                : exerciseStatus(we.id) === 'active'
                  ? 'border-2 border-[#0A0A0A] dark:border-white'
                  : 'bg-[#F2F2F2] dark:bg-[#2A2A2A]'"
            >
              <svg v-if="exerciseStatus(we.id) === 'done'" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
              <svg v-else-if="exerciseStatus(we.id) === 'active'" width="10" height="10" viewBox="0 0 24 24" fill="#0A0A0A" class="dark:fill-white"><circle cx="12" cy="12" r="5"/></svg>
            </div>

            <div>
              <p class="text-[15px] font-semibold text-[#0A0A0A] dark:text-white">{{ we.exercise?.name }}</p>
              <p class="text-[12px] text-[#8A8A8A] mt-0.5">
                {{ doneSetCount(we.id) }}/{{ we.sets }} séries · {{ we.reps }} reps
              </p>
            </div>
          </div>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-[#CCCCCC] dark:text-[#444] shrink-0"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
    </div>

    <!-- ── ACTIVE EXERCISE VIEW ── -->
    <div v-else-if="view === 'exercise' && activeExercise" class="max-w-lg mx-auto px-5 pt-4 pb-8 flex flex-col min-h-full">
      <!-- Header -->
      <div class="flex items-center justify-between mb-5">
        <button
          class="flex items-center gap-1.5 text-[13px] text-[#8A8A8A]"
          @click="view = 'list'"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
          {{ workout?.name }}
        </button>
        <div class="font-mono text-[14px] font-medium text-[#0A0A0A] dark:text-white bg-[#F2F2F2] dark:bg-[#1A1A1A] px-2.5 py-1 rounded-full">
          {{ elapsed }}
        </div>
      </div>

      <!-- Exercise name & set progress -->
      <div class="mb-4">
        <h2 class="text-[20px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">{{ activeExercise.exercise?.name }}</h2>
        <p class="text-[13px] text-[#8A8A8A] mt-0.5">Série {{ currentSetNum }} de {{ activeExercise.sets }}</p>
      </div>

      <!-- Previous reference -->
      <div
        v-if="previousRef"
        class="bg-[#F9F9F9] dark:bg-[#1A1A1A] rounded-xl px-3.5 py-2.5 flex items-center gap-2 text-[12px] text-[#8A8A8A] mb-5"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        Anterior: <strong class="text-[#3D3D3D] dark:text-[#BBB] font-medium">{{ previousRef }}</strong>
      </div>

      <!-- Weight × Reps inputs -->
      <div class="flex items-end gap-4 mb-5">
        <div class="flex-1 flex flex-col items-center">
          <input
            v-model.number="inputWeight"
            type="number"
            inputmode="decimal"
            min="0"
            step="0.5"
            class="w-full bg-transparent text-[#0A0A0A] dark:text-white text-[36px] font-medium text-center border-b-2 border-[#E5E5E5] dark:border-[#333] focus:border-[#0A0A0A] dark:focus:border-white outline-none font-mono tracking-[-0.03em] py-1 transition-colors"
          />
          <span class="text-[11px] text-[#8A8A8A] uppercase tracking-[0.1em] mt-1.5">kg</span>
        </div>
        <div class="flex items-center pb-7 text-[24px] font-light text-[#CCCCCC] dark:text-[#444]">×</div>
        <div class="flex-1 flex flex-col items-center">
          <input
            v-model.number="inputReps"
            type="number"
            inputmode="numeric"
            min="0"
            class="w-full bg-transparent text-[#0A0A0A] dark:text-white text-[36px] font-medium text-center border-b-2 border-[#E5E5E5] dark:border-[#333] focus:border-[#0A0A0A] dark:focus:border-white outline-none font-mono tracking-[-0.03em] py-1 transition-colors"
          />
          <span class="text-[11px] text-[#8A8A8A] uppercase tracking-[0.1em] mt-1.5">reps</span>
        </div>
      </div>

      <!-- Confirm button -->
      <button
        class="w-full flex items-center justify-center gap-2 rounded-xl py-3.5 text-[15px] font-medium tracking-[-0.01em] mb-5 transition-all active:scale-[0.98] disabled:opacity-50"
        :class="justConfirmed
          ? 'bg-[#16A34A] text-white'
          : 'bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A]'"
        :disabled="savingSet"
        @click="confirmSet"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        {{ justConfirmed ? 'Confirmado!' : 'Confirmar Série' }}
      </button>

      <!-- Divider -->
      <div class="flex items-center gap-3 text-[10px] font-medium tracking-[0.1em] uppercase text-[#8A8A8A] mb-3">
        <div class="flex-1 h-px bg-[#E5E5E5] dark:bg-[#2A2A2A]" />
        Séries
        <div class="flex-1 h-px bg-[#E5E5E5] dark:bg-[#2A2A2A]" />
      </div>

      <!-- Sets list -->
      <div class="space-y-2">
        <div
          v-for="n in activeExercise.sets"
          :key="n"
          class="flex items-center px-3.5 py-2.5 rounded-xl border transition-all"
          :class="setRowClass(n)"
        >
          <span class="text-[12px] font-medium text-[#8A8A8A] w-7 shrink-0">S{{ n }}</span>

          <div v-if="isSetDone(n)" class="w-5 h-5 rounded-full bg-[#0A0A0A] dark:bg-white flex items-center justify-center mr-2.5 shrink-0">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
          </div>
          <div
            v-else
            class="w-5 h-5 rounded-full border-[1.5px] mr-2.5 shrink-0"
            :class="n === currentSetNum ? 'border-[#0A0A0A] dark:border-white' : 'border-[#CCCCCC] dark:border-[#444]'"
          />

          <span
            v-if="isSetDone(n)"
            class="flex-1 text-[13px] font-medium text-[#3D3D3D] dark:text-[#CCC] font-mono"
          >
            {{ getSetData(n)?.weight_kg }}kg × {{ getSetData(n)?.reps_done }}
          </span>
          <span v-else-if="n === currentSetNum" class="flex-1 text-[13px] font-medium text-[#0A0A0A] dark:text-white">atual</span>
          <span v-else class="flex-1 text-[13px] text-[#CCCCCC] dark:text-[#444] font-mono">pendente</span>

          <!-- PR badge -->
          <PRBadge v-if="isSetDone(n) && isSetPR(n)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth', 'subscription'], layout: 'fullscreen' })

const route = useRoute()
const workoutStore = useWorkoutStore()
const sessionStore = useSessionStore()

const loading = ref(true)
const finishing = ref(false)
const savingSet = ref(false)
const justConfirmed = ref(false)
const workout = ref<any>(null)
const startedAt = ref(Date.now())
const elapsed = ref('00:00:00')
const view = ref<'list' | 'exercise'>('list')
const activeExercise = ref<any>(null)

// Per-exercise current set tracking
const currentSets = ref<Record<number, number>>({})

const currentSetNum = computed(() => {
  if (!activeExercise.value) return 1
  return currentSets.value[activeExercise.value.id] ?? 1
})

// Inputs for current set
const inputWeight = ref(0)
const inputReps = ref(0)

let timer: ReturnType<typeof setInterval>

onMounted(async () => {
  const workoutId = Number(route.params.workoutId)
  try {
    const [w, session] = await Promise.all([
      workoutStore.fetchWorkout(workoutId),
      sessionStore.startSession(workoutId, new Date().toISOString().split('T')[0]),
    ])
    workout.value = w
    await sessionStore.fetchPrevious(session.id)

    // Init currentSets: find next un-done set per exercise
    for (const we of w.exercises ?? []) {
      const done = sessionStore.currentSession?.sets?.filter((s: any) => s.workout_exercise_id === we.id) ?? []
      const doneNums = done.map((s: any) => s.set_number)
      let next = 1
      for (let i = 1; i <= we.sets; i++) {
        if (!doneNums.includes(i)) { next = i; break }
        next = we.sets + 1 // all done
      }
      currentSets.value[we.id] = next
    }

    timer = setInterval(() => {
      const s = Math.floor((Date.now() - startedAt.value) / 1000)
      const h = String(Math.floor(s / 3600)).padStart(2, '0')
      const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0')
      const sec = String(s % 60).padStart(2, '0')
      elapsed.value = `${h}:${m}:${sec}`
    }, 1000)
  } catch {
    // erro já exibido via toast
  } finally {
    loading.value = false
  }
})

onUnmounted(() => clearInterval(timer))

// ── Helpers ──────────────────────────────────────────────────────

const getSetsForExercise = (weId: number) =>
  sessionStore.currentSession?.sets?.filter((s: any) => s.workout_exercise_id === weId) ?? []

const doneSetCount = (weId: number) => getSetsForExercise(weId).length

const exerciseStatus = (weId: number) => {
  const we = workout.value?.exercises?.find((e: any) => e.id === weId)
  if (!we) return 'pending'
  const done = doneSetCount(weId)
  if (done >= we.sets) return 'done'
  if (done > 0) return 'active'
  if (activeExercise.value?.id === weId) return 'active'
  return 'pending'
}

const isSetDone = (setNum: number) => {
  if (!activeExercise.value) return false
  return getSetsForExercise(activeExercise.value.id).some((s: any) => s.set_number === setNum)
}

const getSetData = (setNum: number) => {
  if (!activeExercise.value) return null
  return getSetsForExercise(activeExercise.value.id).find((s: any) => s.set_number === setNum) ?? null
}

const isSetPR = (setNum: number) => {
  const setData = getSetData(setNum)
  const prev = getPreviousWeight(activeExercise.value?.id, setNum)
  if (!setData || prev === null || prev === undefined) return false
  return setData.weight_kg > prev
}

const getPreviousWeight = (weId: number, setNum: number) => {
  return sessionStore.previousSession?.sets?.find(
    (s: any) => s.workout_exercise_id === weId && s.set_number === setNum
  )?.weight_kg ?? null
}

const getPreviousReps = (weId: number, setNum: number) => {
  return sessionStore.previousSession?.sets?.find(
    (s: any) => s.workout_exercise_id === weId && s.set_number === setNum
  )?.reps_done ?? null
}

const previousRef = computed(() => {
  if (!activeExercise.value) return null
  const w = getPreviousWeight(activeExercise.value.id, currentSetNum.value)
  const r = getPreviousReps(activeExercise.value.id, currentSetNum.value)
  if (w === null) return null
  return `${w}kg × ${r} reps`
})

const setRowClass = (n: number) => {
  if (isSetDone(n)) return 'bg-[#F9F9F9] dark:bg-[#1A1A1A] border-transparent'
  if (n === currentSetNum.value) return 'border-[#0A0A0A] dark:border-white bg-white dark:bg-[#0A0A0A]'
  return 'border-[#E5E5E5] dark:border-[#2A2A2A] bg-white dark:bg-[#0A0A0A] opacity-45'
}

// ── Actions ──────────────────────────────────────────────────────

const openExercise = (we: any) => {
  activeExercise.value = we
  const setNum = currentSets.value[we.id] ?? 1

  // Pre-fill inputs from previous session or last saved set
  const prevW = getPreviousWeight(we.id, setNum)
  const prevR = getPreviousReps(we.id, setNum)
  // Check if this set was already saved in current session
  const existing = getSetDataFor(we.id, setNum)
  inputWeight.value = existing?.weight_kg ?? prevW ?? 0
  inputReps.value = existing?.reps_done ?? prevR ?? 0

  view.value = 'exercise'
}

const getSetDataFor = (weId: number, setNum: number) =>
  sessionStore.currentSession?.sets?.find(
    (s: any) => s.workout_exercise_id === weId && s.set_number === setNum
  ) ?? null

const confirmSet = async () => {
  if (!activeExercise.value || savingSet.value) return
  savingSet.value = true
  try {
    const weId = activeExercise.value.id
    const setNum = currentSetNum.value
    const existing = getSetDataFor(weId, setNum)

    if (existing) {
      await sessionStore.updateSet(sessionStore.currentSession!.id, existing.id, {
        reps_done: inputReps.value,
        weight_kg: inputWeight.value,
      })
    } else {
      await sessionStore.addSet(sessionStore.currentSession!.id, {
        workout_exercise_id: weId,
        set_number: setNum,
        reps_done: inputReps.value,
        weight_kg: inputWeight.value,
      })
    }

    // Flash confirmed state
    justConfirmed.value = true
    setTimeout(() => { justConfirmed.value = false }, 1000)

    // Advance set counter
    const nextSet = setNum + 1
    currentSets.value[weId] = nextSet

    // If all sets done → go back to list after brief delay
    if (nextSet > activeExercise.value.sets) {
      setTimeout(() => { view.value = 'list' }, 800)
    } else {
      // Prepare inputs for next set
      const prevW = getPreviousWeight(weId, nextSet)
      const prevR = getPreviousReps(weId, nextSet)
      inputWeight.value = prevW ?? inputWeight.value
      inputReps.value = prevR ?? inputReps.value
    }
  } catch {
    // erro já exibido via toast
  } finally {
    savingSet.value = false
  }
}

const finish = async () => {
  finishing.value = true
  const durationSecs = Math.floor((Date.now() - startedAt.value) / 1000)
  await sessionStore.finishSession(sessionStore.currentSession!.id, {
    duration_minutes: Math.max(1, Math.round(durationSecs / 60)),
  })
  clearInterval(timer)
  await navigateTo('/history')
}

const cancel = () => navigateTo('/workouts')
</script>
