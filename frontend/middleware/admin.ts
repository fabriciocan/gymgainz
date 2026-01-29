export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  // Se não temos usuário, tenta buscar
  if (!authStore.user) {
    await authStore.fetchUser()
  }

  // Se não está autenticado, redireciona para login
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }

  // Se não é admin, redireciona para dashboard
  if (!authStore.isAdmin) {
    return navigateTo('/dashboard')
  }
})
