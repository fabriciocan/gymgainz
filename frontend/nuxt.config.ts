// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: true },

  modules: [
    '@nuxt/ui',
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@vite-pwa/nuxt',
  ],

  // Proxy: browser fala só com Nuxt (localhost:3000),
  // Nuxt repassa para Laravel (127.0.0.1:8000) — resolve WSL2 e CORS de uma vez
  nitro: {
    devProxy: {
      '/api': {
        target: 'http://127.0.0.1:8000/api',
        changeOrigin: true,
        prependPath: true,
      },
      '/sanctum': {
        target: 'http://127.0.0.1:8000/sanctum',
        changeOrigin: true,
      },
    },
  },

  pwa: {
    manifest: {
      name: 'GymTrack',
      short_name: 'GymTrack',
      description: 'Acompanhe sua evolução na academia',
      theme_color: '#FFFFFF',
      background_color: '#FFFFFF',
      display: 'standalone',
      icons: [
        { src: '/icon-192.png', sizes: '192x192', type: 'image/png' },
        { src: '/icon-512.png', sizes: '512x512', type: 'image/png' },
      ],
    },
    workbox: {
      navigateFallback: '/',
      globPatterns: ['**/*.{js,css,html,png,svg,ico}'],
    },
    devOptions: {
      enabled: true,
      type: 'module',
    },
  },

  // apiBaseUrl vazio = same-origin (usa o proxy do Nuxt)
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE || '',
    },
  },

  colorMode: {
    preference: 'system',
    fallback: 'light',
    classSuffix: '',
  },

  app: {
    head: {
      title: 'GymTrack',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover' },
        { name: 'theme-color', content: '#FFFFFF', media: '(prefers-color-scheme: light)' },
        { name: 'theme-color', content: '#0A0A0A', media: '(prefers-color-scheme: dark)' },
        { name: 'mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-status-bar-style', content: 'default' },
        { name: 'apple-mobile-web-app-title', content: 'GymTrack' },
        { name: 'format-detection', content: 'telephone=no' },
      ],
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap' },
        { rel: 'apple-touch-icon', href: '/icon-192.png' },
      ],
      style: [
        { children: '*, *::before, *::after { font-family: "DM Sans", sans-serif; }' },
      ],
    },
  },
})
