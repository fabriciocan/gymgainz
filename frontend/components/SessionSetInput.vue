<template>
  <div
    class="flex items-center px-3.5 py-2.5 rounded-xl border transition-all"
    :class="isSaved
      ? 'bg-[#F9F9F9] dark:bg-[#1A1A1A] border-transparent'
      : isActive
        ? 'border-[#0A0A0A] dark:border-white bg-white dark:bg-[#0A0A0A]'
        : 'border-[#E5E5E5] dark:border-[#2A2A2A] bg-white dark:bg-[#0A0A0A] opacity-60'"
  >
    <!-- Set number -->
    <span class="text-[12px] font-medium text-[#8A8A8A] w-7 shrink-0">S{{ setNumber }}</span>

    <!-- Check icon (if saved) -->
    <div v-if="isSaved" class="w-5 h-5 rounded-full bg-[#0A0A0A] dark:bg-white flex items-center justify-center mr-2.5 shrink-0">
      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <!-- Circle (if pending/active) -->
    <div
      v-else
      class="w-5 h-5 rounded-full border-[1.5px] mr-2.5 shrink-0"
      :class="isActive ? 'border-[#0A0A0A] dark:border-white' : 'border-[#CCCCCC] dark:border-[#444]'"
    />

    <!-- Saved state: show data -->
    <div v-if="isSaved" class="flex-1 text-[13px] font-medium text-[#3D3D3D] dark:text-[#CCC] font-mono">
      {{ localWeight }}kg × {{ localReps }}
    </div>

    <!-- Active state: inputs -->
    <div v-else-if="isActive" class="flex-1 flex gap-3 items-center">
      <div class="flex-1 flex flex-col items-center">
        <input
          v-model.number="localWeight"
          type="number"
          inputmode="decimal"
          min="0"
          step="0.5"
          class="w-full bg-transparent text-[#0A0A0A] dark:text-white text-[24px] font-medium text-center border-b-2 border-[#E5E5E5] dark:border-[#333] focus:border-[#0A0A0A] dark:focus:border-white outline-none font-mono tracking-[-0.02em] py-0.5 transition-colors"
        />
        <span class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.1em] mt-1">kg</span>
      </div>
      <span class="text-[20px] text-[#CCCCCC] dark:text-[#444] font-light pb-4">×</span>
      <div class="flex-1 flex flex-col items-center">
        <input
          v-model.number="localReps"
          type="number"
          inputmode="numeric"
          min="0"
          class="w-full bg-transparent text-[#0A0A0A] dark:text-white text-[24px] font-medium text-center border-b-2 border-[#E5E5E5] dark:border-[#333] focus:border-[#0A0A0A] dark:focus:border-white outline-none font-mono tracking-[-0.02em] py-0.5 transition-colors"
        />
        <span class="text-[10px] text-[#8A8A8A] uppercase tracking-[0.1em] mt-1">reps</span>
      </div>
      <button
        class="ml-2 w-9 h-9 rounded-full bg-[#0A0A0A] dark:bg-white flex items-center justify-center shrink-0 active:scale-95 transition-transform"
        :class="saving ? 'opacity-50' : ''"
        :disabled="saving"
        @click="save"
      >
        <svg v-if="saving" class="animate-spin w-4 h-4 text-white dark:text-[#0A0A0A]" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
        <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
      </button>
    </div>

    <!-- Pending state -->
    <div v-else class="flex-1 text-[13px] font-medium text-[#CCCCCC] dark:text-[#444] font-mono">
      pendente
    </div>

    <!-- PR badge -->
    <PRBadge v-if="isSaved && isPR" class="ml-2" />
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  setNumber: number
  previousWeight?: number | null
  previousReps?: number | null
  initialReps?: number
  initialWeight?: number
  saved?: boolean
  saving?: boolean
}>()

const emit = defineEmits<{
  save: [{ reps: number; weight: number }]
}>()

const localReps = ref(props.initialReps ?? props.previousReps ?? 0)
const localWeight = ref(props.initialWeight ?? props.previousWeight ?? 0)

const isSaved = computed(() => props.saved || (props.initialReps !== undefined && props.initialReps !== null))
const isActive = computed(() => !isSaved.value)

const isPR = computed(() =>
  props.previousWeight !== null && props.previousWeight !== undefined
    ? localWeight.value > props.previousWeight
    : false
)

const save = () => {
  emit('save', { reps: localReps.value, weight: localWeight.value })
}
</script>
