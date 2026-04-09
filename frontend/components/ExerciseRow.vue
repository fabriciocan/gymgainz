<template>
  <div class="flex items-center justify-between px-4 py-3">
    <div>
      <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white">{{ exercise.exercise?.name }}</p>
      <p class="text-[12px] text-[#8A8A8A] mt-0.5">
        {{ exercise.sets }}x{{ exercise.reps }} · {{ exercise.rest_seconds }}s descanso
      </p>
    </div>
    <div class="flex items-center gap-2">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { WorkoutExercise } from '~/stores/workout'

defineProps<{ exercise: WorkoutExercise }>()
</script>
