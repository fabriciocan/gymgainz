<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<style>
*, *::before, *::after {
  -webkit-tap-highlight-color: transparent;
  box-sizing: border-box;
}

html {
  height: 100%;
  overscroll-behavior: none;
}

body {
  height: 100%;
  overflow: hidden;
  position: fixed;
  width: 100%;
  overscroll-behavior: none;
  -webkit-user-select: none;
  user-select: none;
  touch-action: manipulation;
}

#__nuxt {
  height: 100%;
  width: 100%;
  overflow: hidden;
}

/* Permite selecionar texto em inputs e textareas */
input, textarea, [contenteditable] {
  -webkit-user-select: text;
  user-select: text;
}

/* Safe area */
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
