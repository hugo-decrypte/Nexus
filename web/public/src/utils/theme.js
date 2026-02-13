const SETTINGS_KEY = 'nexus_settings'
const THEME_KEY = 'theme'

export function getTheme() {
  try {
    const saved = localStorage.getItem(SETTINGS_KEY)
    if (saved) {
      const parsed = JSON.parse(saved)
      if (parsed[THEME_KEY] === 'sombre' || parsed[THEME_KEY] === 'clair') {
        return parsed[THEME_KEY]
      }
    }
  } catch {
    // ignore
  }
  return 'clair'
}

export function setTheme(theme) {
  const value = theme === 'sombre' ? 'sombre' : 'clair'
  document.documentElement.setAttribute('data-theme', value)

  try {
    const saved = localStorage.getItem(SETTINGS_KEY)
    const parsed = saved ? JSON.parse(saved) : {}
    parsed[THEME_KEY] = value
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(parsed))
  } catch {
    // ignore
  }
}

export function initTheme() {
  const theme = getTheme()
  document.documentElement.setAttribute('data-theme', theme)
}
