export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  // Se não temos usuário, tenta buscar
  if (!authStore.user) {
    await authStore.fetchUser()
  }

  // Se ainda não tem usuário, redireciona para login
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
