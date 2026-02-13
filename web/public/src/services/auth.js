const TOKEN_KEY = 'nexus_token'

// Connexion : appelle POST /api/auth/login (ou /auth/login si proxy enlève /api)
export async function login(email, motDePasse) {
  const apiBase = import.meta.env.VITE_API_BASE_URL || ''
  const url = `${apiBase}/api/auth/login`

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email,
        mot_de_passe: motDePasse,
      }),
    })

    const contentType = response.headers.get('Content-Type') || ''
    let data = {}
    if (contentType.includes('application/json')) {
      try {
        data = await response.json()
      } catch {
        return { success: false, error: 'Réponse du serveur invalide' }
      }
    } else if (!response.ok) {
      return { success: false, error: 'Erreur du serveur (vérifier que l’API est démarrée)' }
    }

    if (response.ok && data.token) {
      if (data.id) {
        const user = { id: data.id, email: data.email, role: data.role }
        try {
          localStorage.setItem('nexus_user', JSON.stringify(user))
        } catch {}
      }
      localStorage.setItem(TOKEN_KEY, data.token)
      return { success: true }
    }

    return { success: false, error: data.error || 'Identifiants incorrects' }
  } catch (err) {
    console.error('Erreur de connexion:', err)
    const message = err.message || ''
    if (message.includes('Failed to fetch') || message.includes('NetworkError')) {
      return { success: false, error: 'Impossible de joindre le serveur. Vérifiez que l’API est démarrée (ex. port 6080).' }
    }
    return { success: false, error: 'Erreur de connexion au serveur' }
  }
}

// deconnexion
export function logout() {
  localStorage.removeItem(TOKEN_KEY)
}
export function isAuthenticated() {
  const token = getToken()
  if (!token) return false

  // Vérifier si le token n'est pas expiré
  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    const now = Math.floor(Date.now() / 1000)
    return payload.exp > now
  } catch {
    return false
  }
}

export function getToken() {
  return localStorage.getItem(TOKEN_KEY)
}

export function getUser() {
  const token = getToken()
  if (!token) return null

  try {
    const payload = JSON.parse(atob(token.split('.')[1]))
    return {
      id: payload.sub,
      email: payload.data?.email,
      role: payload.data?.role,
    }
  } catch {
    return null
  }
}