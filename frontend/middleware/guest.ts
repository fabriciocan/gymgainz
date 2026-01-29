export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  // Se não temos usuário, tenta buscar
  if (!authStore.user) {
    await authStore.fetchUser()
  }

  // Se está autenticado, redireciona para dashboard
  if (authStore.isAuthenticated) {
    return navigateTo('/dashboard')
  }
})
