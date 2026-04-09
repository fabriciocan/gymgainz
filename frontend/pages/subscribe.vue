<template>
  <div class="min-h-screen bg-white dark:bg-[#0A0A0A] flex items-center justify-center p-5">
    <div class="w-full max-w-sm">
      <div v-if="billingUrl" class="text-center">
        <div class="text-[40px] mb-4">✅</div>
        <h2 class="text-[20px] font-semibold text-[#0A0A0A] dark:text-white mb-2">Assinatura criada!</h2>
        <p class="text-[14px] text-[#8A8A8A] mb-5">Clique para acessar o QR Code PIX e finalizar o pagamento.</p>
        <a
          :href="billingUrl"
          target="_blank"
          class="block w-full bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-xl py-3.5 text-[15px] font-medium text-center mb-2"
        >
          Pagar via PIX
        </a>
        <NuxtLink to="/dashboard" class="block text-center text-[13px] text-[#8A8A8A] py-2">
          Já paguei
        </NuxtLink>
      </div>
      <SubscribeCard v-else @success="onSuccess" />
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ middleware: ['auth'] })

const billingUrl = ref<string | null>(null)

const onSuccess = (url: string | null) => {
  if (url) {
    billingUrl.value = url
  } else {
    navigateTo('/dashboard')
  }
}
</script>
