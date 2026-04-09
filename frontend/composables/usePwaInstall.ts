let deferredPrompt: any = null

export const usePwaInstall = () => {
  const canInstall = ref(false)
  const isInstalled = ref(false)
  const isIos = ref(false)

  const checkInstalled = () => {
    if (typeof window === 'undefined') return
    isInstalled.value = window.matchMedia('(display-mode: standalone)').matches
      || (navigator as any).standalone === true
  }

  const checkIos = () => {
    if (typeof navigator === 'undefined') return
    isIos.value = /iphone|ipad|ipod/i.test(navigator.userAgent)
      && !(navigator as any).standalone
  }

  const listenForPrompt = () => {
    if (typeof window === 'undefined') return
    window.addEventListener('beforeinstallprompt', (e: Event) => {
      e.preventDefault()
      deferredPrompt = e
      canInstall.value = true
    })
    window.addEventListener('appinstalled', () => {
      isInstalled.value = true
      canInstall.value = false
      deferredPrompt = null
    })
  }

  const install = async (): Promise<'accepted' | 'dismissed' | 'unavailable'> => {
    if (!deferredPrompt) return 'unavailable'
    deferredPrompt.prompt()
    const { outcome } = await deferredPrompt.userChoice
    deferredPrompt = null
    canInstall.value = false
    return outcome
  }

  onMounted(() => {
    checkInstalled()
    checkIos()
    if (deferredPrompt) canInstall.value = true
    listenForPrompt()
  })

  return { canInstall, isInstalled, isIos, install }
}
