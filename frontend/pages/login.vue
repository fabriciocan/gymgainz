<template>
  <div class="flex flex-col lg:flex-row items-center justify-center gap-12 lg:gap-20">
    <!-- Info Section -->
    <div class="flex-1 max-w-lg animate-fade-in">
      <div class="text-center lg:text-left mb-8">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
          Sistema
          <span class="block text-2xl md:text-3xl font-medium text-gray-800 dark:text-gray-200 mt-2">
            Base Nuxt
          </span>
        </h1>
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-4">Sistema de Gerenciamento</p>
      </div>
    </div>

    <!-- Login Form -->
    <div class="w-full max-w-md animate-slide-up">
      <div class="card-clean rounded-3xl p-10 shadow-xl">
        <div class="text-center mb-10">
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">
            Bem-vindo de volta
          </h2>
          <p class="text-gray-600 dark:text-gray-400">
            Acesse sua conta para continuar
          </p>
        </div>

        <!-- Error message -->
        <div v-if="error" class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700">
          <p class="text-sm font-medium text-red-700 dark:text-red-300">{{ error }}</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Email field -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">
              Email
            </label>
            <input
              type="email"
              id="email"
              v-model="email"
              required
              class="input-clean block w-full px-4 py-3 rounded-xl text-gray-900 dark:text-white"
              placeholder="Digite seu email"
            >
          </div>

          <!-- Password field -->
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white">
              Senha
            </label>
            <input
              type="password"
              id="password"
              v-model="password"
              required
              class="input-clean block w-full px-4 py-3 rounded-xl text-gray-900 dark:text-white"
              placeholder="Digite sua senha"
            >
          </div>

          <!-- Remember me -->
          <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center">
              <input
                id="remember"
                type="checkbox"
                v-model="remember"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Lembrar-me</span>
            </label>
          </div>

          <!-- Login button -->
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary w-full py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              Autenticando...
            </span>
            <span v-else>Entrar</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const remember = ref(false)
const loading = ref(false)
const error = ref<string | null>(null)

const handleLogin = async () => {
  loading.value = true
  error.value = null

  try {
    await authStore.login({
      email: email.value,
      password: password.value,
      remember: remember.value,
    })

    navigateTo('/dashboard')
  } catch (e: any) {
    error.value = e.data?.message || 'Credenciais inválidas. Verifique seu email e senha.'
  } finally {
    loading.value = false
  }
}
</script>
