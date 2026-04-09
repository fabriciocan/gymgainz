<template>
  <div class="space-y-5">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-14 h-14 bg-[#0A0A0A] dark:bg-white rounded-2xl mb-4">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="1.8">
          <path d="M6.5 6.5h11M6.5 12h11M6.5 17.5h11"/>
          <circle cx="3.5" cy="6.5" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
          <circle cx="3.5" cy="12" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
          <circle cx="3.5" cy="17.5" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
        </svg>
      </div>
      <h2 class="text-[24px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">GymTrack</h2>
      <p class="text-[14px] text-[#8A8A8A] mt-1">Acesso completo ao app</p>
    </div>

    <div class="text-center">
      <span class="text-[40px] font-medium font-mono tracking-[-0.03em] text-[#0A0A0A] dark:text-white">R$19,90</span>
      <span class="text-[16px] text-[#8A8A8A]">/mês</span>
    </div>

    <ul class="space-y-2">
      <li v-for="feature in features" :key="feature" class="flex items-center gap-2.5 text-[14px] text-[#3D3D3D] dark:text-[#CCC]">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-[#0A0A0A] dark:text-white shrink-0"><path d="M20 6L9 17l-5-5"/></svg>
        {{ feature }}
      </li>
    </ul>

    <UForm :schema="schema" :state="form" @submit="subscribe" class="space-y-3">
      <div>
        <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Celular (com DDD)</label>
        <UInput v-model="form.cellphone" placeholder="(41) 99999-9999" />
      </div>
      <div>
        <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">CPF</label>
        <UInput v-model="form.taxId" placeholder="000.000.000-00" />
      </div>
      <button
        type="submit"
        class="w-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl py-3.5 text-[15px] font-medium tracking-[-0.01em] transition-all active:scale-[0.98] disabled:opacity-50 mt-2"
        :disabled="loading"
      >
        {{ loading ? '...' : 'Assinar agora via PIX' }}
      </button>
    </UForm>

    <p class="text-[12px] text-[#CCCCCC] dark:text-[#555] text-center">
      Pagamento via PIX · Cancele quando quiser
    </p>
  </div>
</template>

<script setup lang="ts">
import { z } from 'zod'

const emit = defineEmits<{ success: [url: string | null] }>()

const subscriptionStore = useSubscriptionStore()
const loading = ref(false)

const features = [
  'Registro de treinos e séries',
  'Gráficos de progressão de carga',
  'Medidas corporais',
  'Histórico completo',
  'Sugestão automática de treino',
]

const schema = z.object({
  cellphone: z.string().min(10, 'Informe o celular com DDD'),
  taxId: z.string().min(11, 'Informe o CPF'),
})

const form = reactive({ cellphone: '', taxId: '' })

const subscribe = async () => {
  loading.value = true
  try {
    const res = await subscriptionStore.subscribe(form.cellphone, form.taxId)
    emit('success', res.billing_url)
  } catch {
    // erro tratado pelo useApi
  } finally {
    loading.value = false
  }
}
</script>
