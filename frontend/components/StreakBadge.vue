<template>
  <div class="inline-flex items-center gap-1.5 bg-[#0A0A0A] dark:bg-white text-white dark:text-[#0A0A0A] rounded-full px-3 py-1 text-[12px] font-medium">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="#FF6B2B"><path d="M12 2C9 8 4 10 4 15a8 8 0 0 0 16 0c0-5-5-7-8-13z"/></svg>
    {{ streak }} {{ streak === 1 ? 'dia' : 'dias' }}
  </div>
</template>

<script setup lang="ts">
defineProps<{ streak: number }>()
</script>
