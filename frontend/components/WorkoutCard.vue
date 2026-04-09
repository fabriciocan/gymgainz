<template>
  <div
    class="border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-2xl p-4 cursor-pointer bg-white dark:bg-[#0A0A0A] hover:border-[#CCCCCC] dark:hover:border-[#444] hover:shadow-sm hover:-translate-y-px transition-all"
    @click="$emit('click')"
  >
    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <h3 class="text-[16px] font-semibold tracking-[-0.01em] text-[#0A0A0A] dark:text-white truncate">{{ workout.name }}</h3>
        <p v-if="muscleGroups" class="text-[10px] font-medium tracking-[0.08em] uppercase text-[#8A8A8A] mt-0.5">{{ muscleGroups }}</p>
        <p v-if="daysLabel" class="text-[11px] text-[#8A8A8A] mt-0.5">{{ daysLabel }}</p>
      </div>
      <slot name="actions" />
    </div>
    <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-[#F2F2F2] dark:border-[#1A1A1A]">
      <span class="text-[12px] text-[#8A8A8A]">
        {{ exerciseCount }} exercício{{ exerciseCount !== 1 ? 's' : '' }}
        <span v-if="lastUsed"> · Último {{ lastUsed }}</span>
      </span>
      <span class="text-[18px] text-[#CCCCCC] dark:text-[#444]">›</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Workout } from '~/stores/workout'

const props = defineProps<{
  workout: Workout
  lastUsed?: string
}>()

defineEmits<{ click: [] }>()

const DAY_LABELS: Record<number, string> = { 0: 'Dom', 1: 'Seg', 2: 'Ter', 3: 'Qua', 4: 'Qui', 5: 'Sex', 6: 'Sáb' }
const DAY_ORDER = [1, 2, 3, 4, 5, 6, 0]

const exerciseCount = computed(() => props.workout.workout_exercises_count ?? props.workout.exercises?.length ?? 0)
const muscleGroups = computed(() => props.workout.description ?? null)
const daysLabel = computed(() => {
  const days = props.workout.week_days ?? []
  if (!days.length) return null
  return DAY_ORDER.filter(d => days.includes(d)).map(d => DAY_LABELS[d]).join(' · ')
})
</script>
