<template>
  <div />
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth'] })

const authStore = useAuthStore()
if (authStore.isAuthenticated) {
  await navigateTo('/dashboard')
} else {
  await navigateTo('/login')
}
</script>
