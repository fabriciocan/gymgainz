export default defineNuxtRouteMiddleware(async () => {
  const subscriptionStore = useSubscriptionStore()
  if (!subscriptionStore.hasAccess && subscriptionStore.subscription === null) {
    await subscriptionStore.fetchStatus()
  }
  if (!subscriptionStore.hasAccess) {
    return navigateTo('/subscribe')
  }
})
