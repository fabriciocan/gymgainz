<template>
  <div class="min-h-screen bg-white dark:bg-[#0A0A0A] flex items-center justify-center p-5">
    <div class="w-full max-w-sm">
      <!-- Logo -->
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-[#0A0A0A] dark:bg-white rounded-2xl mb-4">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" class="dark:stroke-[#0A0A0A]" stroke-width="1.8">
            <path d="M6.5 6.5h11M6.5 12h11M6.5 17.5h11"/>
            <circle cx="3.5" cy="6.5" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
            <circle cx="3.5" cy="12" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
            <circle cx="3.5" cy="17.5" r="1" fill="white" class="dark:fill-[#0A0A0A]" stroke="none"/>
          </svg>
        </div>
        <h1 class="text-[28px] font-semibold tracking-[-0.02em] text-[#0A0A0A] dark:text-white">GymTrack</h1>
        <p class="text-[14px] text-[#8A8A8A] mt-1">Crie sua conta — 7 dias grátis</p>
      </div>

      <div class="space-y-3">
        <div>
          <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Nome completo</label>
          <input
            v-model="form.name"
            type="text"
            placeholder="Seu nome"
            class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white placeholder-[#CCCCCC] dark:placeholder-[#444] rounded-xl px-4 py-3 text-[15px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors"
          />
        </div>
        <div>
          <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="seu@email.com"
            class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white placeholder-[#CCCCCC] dark:placeholder-[#444] rounded-xl px-4 py-3 text-[15px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors"
          />
        </div>
        <div>
          <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Senha</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="Mínimo 8 caracteres"
            class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white placeholder-[#CCCCCC] dark:placeholder-[#444] rounded-xl px-4 py-3 text-[15px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors"
          />
        </div>
        <div>
          <label class="block text-[11px] font-medium tracking-[0.06em] uppercase text-[#8A8A8A] mb-1.5">Confirmar senha</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="Repita a senha"
            class="w-full bg-[#F9F9F9] dark:bg-[#1A1A1A] text-[#0A0A0A] dark:text-white placeholder-[#CCCCCC] dark:placeholder-[#444] rounded-xl px-4 py-3 text-[15px] border border-[#E5E5E5] dark:border-[#2A2A2A] focus:border-[#0A0A0A] dark:focus:border-white focus:outline-none transition-colors"
          />
        </div>

        <div v-if="error" class="bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-[13px] rounded-xl px-4 py-3">
          {{ error }}
        </div>

        <button
          class="w-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl py-3.5 text-[15px] font-medium tracking-[-0.01em] mt-2 transition-all active:scale-[0.98] disabled:opacity-50"
          :disabled="loading"
          @click="register"
        >
          {{ loading ? '...' : 'Criar conta grátis' }}
        </button>
      </div>

      <p class="text-center text-[13px] text-[#8A8A8A] mt-6">
        Já tem conta?
        <NuxtLink to="/login" class="text-[#0A0A0A] dark:text-white font-medium hover:underline">Entrar</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['guest'], layout: false })

const { api } = useApi()
const authStore = useAuthStore()
const loading = ref(false)
const error = ref('')
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const register = async () => {
  error.value = ''
  loading.value = true
  try {
    const res = await api<any>('/register', { method: 'POST', body: form })
    authStore.user = res.user
    await navigateTo('/dashboard')
  } catch (e: any) {
    error.value = e.data?.message || 'Erro ao criar conta.'
  } finally {
    loading.value = false
  }
}
</script>
