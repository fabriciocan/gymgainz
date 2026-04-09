<template>
  <div class="px-5 pt-3 pb-6 max-w-lg mx-auto">
    <div class="flex items-center justify-between mb-5">
      <h1 class="text-[22px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">Meus Exercícios</h1>
      <button
        class="flex items-center gap-1.5 text-[13px] font-medium text-[#8A8A8A] hover:text-[#0A0A0A] dark:hover:text-white transition-colors"
        @click="openCreate"
      >
        <UIcon name="i-heroicons-plus" class="w-4 h-4" />
        Novo
      </button>
    </div>

    <!-- Filter select -->
    <div class="mb-4">
      <select
        v-model="filterGroup"
        class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white rounded-xl px-4 py-2.5 text-[14px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors appearance-none cursor-pointer"
      >
        <option :value="null">Todos os grupos musculares</option>
        <option v-for="group in filterGroups" :key="group.value" :value="group.value">
          {{ group.label }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="space-y-2.5">
      <div class="h-16 bg-[#F2F2F2] dark:bg-[#1A1A1A] rounded-2xl animate-pulse" v-for="i in 5" :key="i" />
    </div>

    <div v-else-if="!filteredExercises.length" class="text-center py-16">
      <svg class="mx-auto mb-3 text-[#CCCCCC] dark:text-[#444]" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 12h2m14 0h-2M7 7l1.5 1.5M17 7l-1.5 1.5M7 17l1.5-1.5M17 17l-1.5-1.5M12 4v2m0 14v-2"/><circle cx="12" cy="12" r="3"/></svg>
      <p class="text-[14px] text-[#8A8A8A]">
        {{ filterGroup ? 'Nenhum exercício neste grupo.' : 'Você ainda não criou exercícios personalizados.' }}
      </p>
      <button
        class="inline-block mt-4 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl px-5 py-2.5 text-[14px] font-medium"
        @click="openCreate"
      >
        Criar primeiro exercício
      </button>
    </div>

    <div v-else class="space-y-2">
      <div
        v-for="exercise in filteredExercises"
        :key="exercise.id"
        class="border border-[#E5E5E5] dark:border-[#2A2A2A] rounded-2xl px-4 py-3 flex items-center justify-between bg-white dark:bg-[#0A0A0A]"
      >
        <div>
          <p class="text-[14px] font-medium text-[#0A0A0A] dark:text-white">{{ exercise.name }}</p>
          <p class="text-[12px] text-[#8A8A8A] mt-0.5">{{ muscleGroupLabel(exercise.muscle_group) }}</p>
        </div>
        <div class="flex items-center gap-1">
          <button
            class="p-1.5 text-[#CCCCCC] dark:text-[#444] hover:text-[#0A0A0A] dark:hover:text-white transition-colors"
            @click="openEdit(exercise)"
          >
            <UIcon name="i-heroicons-pencil-square" class="w-4 h-4" />
          </button>
          <button
            class="p-1.5 text-[#CCCCCC] dark:text-[#444] hover:text-red-500 dark:hover:text-red-400 transition-colors"
            :class="deletingId === exercise.id ? 'opacity-50' : ''"
            @click="confirmDelete(exercise)"
          >
            <UIcon name="i-heroicons-trash" class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modal criar/editar -->
    <UModal v-model="showModal">
      <UCard>
        <template #header>
          <p class="font-semibold text-[#0A0A0A] dark:text-white">{{ editing ? 'Editar exercício' : 'Novo exercício' }}</p>
        </template>
        <div class="space-y-4">
          <UFormGroup label="Nome" :error="errors.name">
            <UInput v-model="form.name" placeholder="Ex: Supino Inclinado com Halteres" autofocus />
          </UFormGroup>
          <UFormGroup label="Grupo muscular" :error="errors.muscle_group">
            <USelectMenu
              v-model="form.muscleGroup"
              :options="muscleGroupOptions"
              option-attribute="label"
              value-attribute="value"
              placeholder="Selecionar..."
            />
          </UFormGroup>
        </div>
        <template #footer>
          <div class="flex justify-end gap-2">
            <button class="px-4 py-2 rounded-xl text-[13px] text-[#8A8A8A]" @click="showModal = false">Cancelar</button>
            <button
              class="px-4 py-2 rounded-xl text-[13px] font-medium bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] disabled:opacity-50"
              :disabled="saving"
              @click="save"
            >
              {{ saving ? '...' : 'Salvar' }}
            </button>
          </div>
        </template>
      </UCard>
    </UModal>

    <!-- Modal exclusão -->
    <UModal v-model="showDeleteModal">
      <UCard>
        <template #header>
          <p class="font-semibold text-[#0A0A0A] dark:text-white">Excluir exercício</p>
        </template>
        <p class="text-[14px] text-[#8A8A8A]">
          Deseja excluir <span class="text-[#0A0A0A] dark:text-white font-medium">{{ deletingExercise?.name }}</span>?
          Treinos que usam este exercício não serão afetados.
        </p>
        <template #footer>
          <div class="flex justify-end gap-2">
            <button class="px-4 py-2 rounded-xl text-[13px] text-[#8A8A8A]" @click="showDeleteModal = false">Cancelar</button>
            <button
              class="px-4 py-2 rounded-xl text-[13px] font-medium bg-red-500 text-white disabled:opacity-50"
              :disabled="!!deletingId"
              @click="doDelete"
            >
              {{ deletingId ? '...' : 'Excluir' }}
            </button>
          </div>
        </template>
      </UCard>
    </UModal>
  </div>
</template>

<script setup lang="ts">
import { MUSCLE_GROUPS, muscleGroupLabel } from '~/stores/exercise'
import type { Exercise } from '~/stores/exercise'

definePageMeta({ middleware: ['auth', 'subscription'] })

const exerciseStore = useExerciseStore()
const loading = ref(true)

onMounted(async () => {
  await exerciseStore.fetchExercises()
  loading.value = false
})

const filterGroup = ref<string | null>(null)
const filterGroups = MUSCLE_GROUPS

const filteredExercises = computed(() => {
  const list = exerciseStore.customExercises
  if (!filterGroup.value) return list
  return list.filter(e => e.muscle_group === filterGroup.value)
})

const showModal = ref(false)
const editing = ref<Exercise | null>(null)
const saving = ref(false)
const form = reactive({ name: '', muscleGroup: '' })
const errors = reactive({ name: '', muscle_group: '' })
const muscleGroupOptions = MUSCLE_GROUPS

const openCreate = () => {
  editing.value = null
  form.name = ''
  form.muscleGroup = ''
  errors.name = ''
  errors.muscle_group = ''
  showModal.value = true
}

const openEdit = (exercise: Exercise) => {
  editing.value = exercise
  form.name = exercise.name
  form.muscleGroup = exercise.muscle_group
  errors.name = ''
  errors.muscle_group = ''
  showModal.value = true
}

const save = async () => {
  errors.name = form.name.trim() ? '' : 'Nome é obrigatório'
  errors.muscle_group = form.muscleGroup ? '' : 'Selecione um grupo muscular'
  if (errors.name || errors.muscle_group) return

  saving.value = true
  try {
    if (editing.value) {
      await exerciseStore.updateExercise(editing.value.id, {
        name: form.name.trim(),
        muscle_group: form.muscleGroup,
      })
    } else {
      await exerciseStore.createExercise({
        name: form.name.trim(),
        muscle_group: form.muscleGroup,
      })
    }
    showModal.value = false
  } finally {
    saving.value = false
  }
}

const showDeleteModal = ref(false)
const deletingExercise = ref<Exercise | null>(null)
const deletingId = ref<number | null>(null)

const confirmDelete = (exercise: Exercise) => {
  deletingExercise.value = exercise
  showDeleteModal.value = true
}

const doDelete = async () => {
  if (!deletingExercise.value) return
  deletingId.value = deletingExercise.value.id
  try {
    await exerciseStore.deleteExercise(deletingExercise.value.id)
    showDeleteModal.value = false
  } finally {
    deletingId.value = null
    deletingExercise.value = null
  }
}
</script>
