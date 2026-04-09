<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<style>
/* Comportamento nativo — sem seleção de texto em elementos de UI */
*, *::before, *::after {
  -webkit-tap-highlight-color: transparent;
  box-sizing: border-box;
}

html, body {
  overscroll-behavior: none;
  -webkit-overflow-scrolling: touch;
  height: 100%;
}

body {
  -webkit-user-select: none;
  user-select: none;
  overflow: hidden;
  position: fixed;
  width: 100%;
  height: 100%;
}

#__nuxt {
  height: 100%;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior-y: none;
}

/* Permite selecionar texto em inputs e textareas */
input, textarea, [contenteditable] {
  -webkit-user-select: text;
  user-select: text;
}

/* Safe area para notch/home indicator */
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}

.pt-safe {
  padding-top: env(safe-area-inset-top);
}
</style>
